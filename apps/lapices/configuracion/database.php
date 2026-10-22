<?php
require_once __DIR__ . '/encoding.php';
/**
 * Native mysqli database adapter for the application.
 *
 * The db_* surface keeps legacy call signatures stable while removing the
 * dependency on ext/mysql. New or security-sensitive code should prefer
 * db_prepare()/db_execute() and pass values separately from SQL.
 */

if (!defined('MYSQL_ASSOC')) {
    define('MYSQL_ASSOC', 1);
}
if (!defined('MYSQL_NUM')) {
    define('MYSQL_NUM', 2);
}
if (!defined('MYSQL_BOTH')) {
    define('MYSQL_BOTH', 3);
}

$GLOBALS['app_db_default_link'] = null;
$GLOBALS['app_db_last_error'] = '';
$GLOBALS['app_db_last_errno'] = 0;

function db_link($link_identifier = null)
{
    if ($link_identifier instanceof mysqli) {
        return $link_identifier;
    }

    return isset($GLOBALS['app_db_default_link'])
        ? $GLOBALS['app_db_default_link']
        : null;
}

function db_set_default_link($link_identifier)
{
    if (!($link_identifier instanceof mysqli)) {
        return false;
    }

    $GLOBALS['app_db_default_link'] = $link_identifier;
    return true;
}

function db_remember_error($link_identifier = null)
{
    $link = db_link($link_identifier);
    if ($link instanceof mysqli) {
        $GLOBALS['app_db_last_error'] = mysqli_error($link);
        $GLOBALS['app_db_last_errno'] = mysqli_errno($link);
        return;
    }

    $GLOBALS['app_db_last_error'] = mysqli_connect_error();
    $GLOBALS['app_db_last_errno'] = mysqli_connect_errno();
}

function db_parse_server($server)
{
    $host = $server;
    $port = 0;
    $socket = null;

    if (is_string($server) && preg_match('/^(.+):([0-9]+)$/', $server, $matches)) {
        $host = $matches[1];
        $port = (int) $matches[2];
    }

    return array($host, $port, $socket);
}

function db_connect(
    $server = null,
    $username = null,
    $password = null,
    $new_link = false,
    $client_flags = 0
) {
    if (!extension_loaded('mysqli')) {
        trigger_error('La extensión mysqli es obligatoria.', E_USER_WARNING);
        return false;
    }

    if (defined('MYSQLI_REPORT_OFF')) {
        mysqli_report(MYSQLI_REPORT_OFF);
    }

    list($host, $port, $socket) = db_parse_server($server);
    $link = mysqli_init();
    if (!$link) {
        return false;
    }

    @mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 8);

    $connected = @mysqli_real_connect(
        $link,
        $host,
        $username,
        $password,
        null,
        $port,
        $socket,
        $client_flags
    );

    if (!$connected) {
        db_remember_error();
        return false;
    }

    // MySQL converts existing latin1 columns at this boundary. All application
    // queries, form values and results now use UTF-8.
    @mysqli_set_charset($link, 'utf8mb4');
    db_set_default_link($link);
    $GLOBALS['app_db_last_error'] = '';
    $GLOBALS['app_db_last_errno'] = 0;

    return $link;
}

function db_pconnect(
    $server = null,
    $username = null,
    $password = null,
    $client_flags = 0
) {
    if (is_string($server) && strpos($server, 'p:') !== 0) {
        $server = 'p:' . $server;
    }

    return db_connect($server, $username, $password, true, $client_flags);
}

function db_select_db($database_name, $link_identifier = null)
{
    $link = db_link($link_identifier);
    if (!$link) {
        return false;
    }

    $selected = @mysqli_select_db($link, $database_name);
    if (!$selected) {
        db_remember_error($link);
    }

    return $selected;
}

function db_query($query, $link_identifier = null)
{
    $link = db_link($link_identifier);
    if (!$link) {
        return false;
    }

    $result = @mysqli_query($link, $query);
    if ($result === false) {
        db_remember_error($link);
    }

    return $result;
}

function db_unbuffered_query($query, $link_identifier = null)
{
    $link = db_link($link_identifier);
    if (!$link) {
        return false;
    }

    $result = @mysqli_query($link, $query, MYSQLI_USE_RESULT);
    if ($result === false) {
        db_remember_error($link);
    }

    return $result;
}

function db_prepare($query, $link_identifier = null)
{
    $link = db_link($link_identifier);
    if (!$link) {
        return false;
    }

    $statement = @mysqli_prepare($link, $query);
    if ($statement === false) {
        db_remember_error($link);
    }

    return $statement;
}

function db_execute($statement)
{
    return $statement instanceof mysqli_stmt
        ? mysqli_stmt_execute($statement)
        : false;
}

function db_query_params($query, $types, $parameters = array(), $link_identifier = null)
{
    $link = db_link($link_identifier);
    $statement = db_prepare($query, $link);
    if (!$statement) {
        return false;
    }

    if ($types !== '') {
        $bindArguments = array($statement, $types);
        foreach ($parameters as $index => $value) {
            $parameters[$index] = $value;
            $bindArguments[] = &$parameters[$index];
        }

        if (!call_user_func_array('mysqli_stmt_bind_param', $bindArguments)) {
            db_remember_error($link);
            mysqli_stmt_close($statement);
            return false;
        }
    }

    if (!mysqli_stmt_execute($statement)) {
        db_remember_error($link);
        mysqli_stmt_close($statement);
        return false;
    }

    if (mysqli_stmt_field_count($statement) > 0) {
        $result = mysqli_stmt_get_result($statement);
        if ($result === false) {
            db_remember_error($link);
        }
        mysqli_stmt_close($statement);
        return $result;
    }

    mysqli_stmt_close($statement);
    return true;
}

function db_text_row($row, $result)
{
    if (!is_array($row)) { return $row; }
    $fields = null;
    foreach ($row as $key => $value) {
        if (!is_string($value) || !preg_match('/[\x80-\xFF]/', $value)
            || strpos($value, "\0") !== false || preg_match('/^(?:a|O|C|s):[0-9]+:/', $value)) { continue; }
        if ($fields === null) {
            $fields = array();
            foreach (mysqli_fetch_fields($result) as $index => $field) {
                // Newly created communication tables already store native UTF-8.
                // Preserve intentional literal text in these native UTF-8 tables.
                $charset = strpos($field->orgtable, 'comunicacion_') === 0 ? 63 : $field->charsetnr;
                $fields[$index] = $charset;
                $fields[$field->name] = $charset;
            }
        }
        // Never reinterpret BLOBs, binary fields or serialized application data.
        if (isset($fields[$key]) && (int) $fields[$key] !== 63) { $row[$key] = app_text_utf8($value); }
    }
    return $row;
}

function db_fetch_array($result, $result_type = MYSQL_BOTH)
{
    if (!($result instanceof mysqli_result)) {
        return false;
    }

    $mode = MYSQLI_BOTH;
    if ($result_type === MYSQL_ASSOC) {
        $mode = MYSQLI_ASSOC;
    } elseif ($result_type === MYSQL_NUM) {
        $mode = MYSQLI_NUM;
    }

    return db_text_row(mysqli_fetch_array($result, $mode), $result);
}

function db_fetch_assoc($result)
{
    return $result instanceof mysqli_result ? db_text_row(mysqli_fetch_assoc($result), $result) : false;
}

function db_fetch_row($result)
{
    return $result instanceof mysqli_result ? db_text_row(mysqli_fetch_row($result), $result) : false;
}

function db_fetch_field($result, $field_offset = null)
{
    if (!($result instanceof mysqli_result)) {
        return false;
    }

    if ($field_offset !== null && !mysqli_field_seek($result, (int) $field_offset)) {
        return false;
    }

    return mysqli_fetch_field($result);
}

function db_num_rows($result)
{
    return $result instanceof mysqli_result ? mysqli_num_rows($result) : false;
}

function db_num_fields($result)
{
    return $result instanceof mysqli_result ? mysqli_num_fields($result) : false;
}

function db_data_seek($result, $row_number)
{
    return $result instanceof mysqli_result
        ? mysqli_data_seek($result, (int) $row_number)
        : false;
}

function db_free_result($result)
{
    if (!($result instanceof mysqli_result)) {
        return false;
    }

    mysqli_free_result($result);
    return true;
}

function db_result($result, $row, $field = 0)
{
    if (!($result instanceof mysqli_result) || !mysqli_data_seek($result, (int) $row)) {
        return false;
    }

    $data = db_text_row(mysqli_fetch_array($result, MYSQLI_BOTH), $result);
    return is_array($data) && array_key_exists($field, $data) ? $data[$field] : false;
}

function db_insert_id($link_identifier = null)
{
    $link = db_link($link_identifier);
    return $link ? mysqli_insert_id($link) : false;
}

function db_affected_rows($link_identifier = null)
{
    $link = db_link($link_identifier);
    return $link ? mysqli_affected_rows($link) : false;
}

function db_real_escape_string($unescaped_string, $link_identifier = null)
{
    $link = db_link($link_identifier);
    return $link ? mysqli_real_escape_string($link, $unescaped_string) : false;
}

function db_escape_string($unescaped_string)
{
    $link = db_link();
    return $link ? mysqli_real_escape_string($link, $unescaped_string) : addslashes($unescaped_string);
}

function db_error($link_identifier = null)
{
    $link = db_link($link_identifier);
    if ($link) {
        $real_error = mysqli_error($link);
    } else {
        $real_error = isset($GLOBALS['app_db_last_error'])
            ? $GLOBALS['app_db_last_error']
            : mysqli_connect_error();
    }

    if ($real_error !== '' && getenv('APP_DEBUG') !== '1') {
        error_log('[Lapices DB] ' . $real_error);
        return 'No se ha podido completar la operación solicitada.';
    }

    return $real_error;
}

function db_errno($link_identifier = null)
{
    $link = db_link($link_identifier);
    if ($link) {
        return mysqli_errno($link);
    }

    return isset($GLOBALS['app_db_last_errno'])
        ? $GLOBALS['app_db_last_errno']
        : mysqli_connect_errno();
}

function db_close($link_identifier = null)
{
    $link = db_link($link_identifier);
    if (!$link) {
        return false;
    }

    $closed = mysqli_close($link);
    if ($link === $GLOBALS['app_db_default_link']) {
        $GLOBALS['app_db_default_link'] = null;
    }

    return $closed;
}
