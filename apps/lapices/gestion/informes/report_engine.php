<?php

if (!defined('REPORTS_DEFINITION_VERSION')) {
    define('REPORTS_DEFINITION_VERSION', 1);
    define('REPORTS_MAX_JOINS', 4);
    define('REPORTS_MAX_COLUMNS', 80);
    define('REPORTS_MAX_FILTERS', 20);
    define('REPORTS_MAX_SORTS', 5);
    define('REPORTS_PREVIEW_ROWS', 200);
    define('REPORTS_SCREEN_ROWS', 1000);
    define('REPORTS_EXPORT_ROWS', 50000);
}

function reports_user_can_access()
{
    return !empty($GLOBALS['permitido'])
        && isset($GLOBALS['nivel_acceso'])
        && (int) $GLOBALS['nivel_acceso'] >= 4
        && (int) $GLOBALS['nivel_acceso'] < 10;
}

function reports_require_page_access()
{
    if (!reports_user_can_access()) {
        header('Location: /index.php');
        exit;
    }
}

function reports_require_json_access()
{
    if (!reports_user_can_access()) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array(
            'ok' => false,
            'errors' => array('No tienes permiso para utilizar los informes personalizados.'),
        ));
        exit;
    }
}

function reports_humanize($value)
{
    $value = str_replace(array('_', '-'), ' ', (string) $value);
    $value = preg_replace('/\s+/', ' ', trim($value));
    if ($value === '') {
        return '';
    }

    return function_exists('mb_convert_case')
        ? mb_convert_case($value, MB_CASE_TITLE, 'UTF-8')
        : ucwords($value);
}

function reports_string_limit($value, $length)
{
    $value = trim((string) $value);
    return function_exists('mb_substr')
        ? mb_substr($value, 0, $length, 'UTF-8')
        : substr($value, 0, $length);
}

function reports_quote_identifier($identifier)
{
    return '`' . str_replace('`', '``', (string) $identifier) . '`';
}

function reports_list_tables($database)
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $cache = array();
    if (!($database instanceof mysqli)) {
        return $cache;
    }

    $result = mysqli_query(
        $database,
        "SELECT TABLE_NAME, TABLE_TYPE, TABLE_ROWS "
        . "FROM information_schema.TABLES "
        . "WHERE TABLE_SCHEMA=DATABASE() "
        . "AND TABLE_TYPE IN ('BASE TABLE', 'VIEW') "
        . "ORDER BY TABLE_NAME"
    );

    if (!$result) {
        return $cache;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $cache[] = array(
            'name' => $row['TABLE_NAME'],
            'label' => reports_humanize($row['TABLE_NAME']),
            'type' => $row['TABLE_TYPE'] === 'VIEW' ? 'view' : 'table',
            'rows' => isset($row['TABLE_ROWS']) ? (int) $row['TABLE_ROWS'] : 0,
        );
    }
    mysqli_free_result($result);

    return $cache;
}

function reports_table_map($database)
{
    $map = array();
    foreach (reports_list_tables($database) as $table) {
        $map[$table['name']] = $table;
    }
    return $map;
}

function reports_column_category($data_type)
{
    $data_type = strtolower((string) $data_type);
    if (in_array($data_type, array(
        'tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint',
        'decimal', 'numeric', 'float', 'double', 'real', 'bit',
    ), true)) {
        return 'number';
    }

    if (in_array($data_type, array(
        'date', 'datetime', 'timestamp', 'time', 'year',
    ), true)) {
        return 'date';
    }

    if (in_array($data_type, array(
        'blob', 'tinyblob', 'mediumblob', 'longblob', 'binary', 'varbinary',
    ), true)) {
        return 'binary';
    }

    return 'text';
}

function reports_columns_for_table($database, $table)
{
    static $cache = array();
    $table = (string) $table;
    if (isset($cache[$table])) {
        return $cache[$table];
    }

    $cache[$table] = array();
    $table_map = reports_table_map($database);
    if (!isset($table_map[$table])) {
        return $cache[$table];
    }

    $statement = mysqli_prepare(
        $database,
        "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY "
        . "FROM information_schema.COLUMNS "
        . "WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? "
        . "ORDER BY ORDINAL_POSITION"
    );
    if (!$statement) {
        return $cache[$table];
    }

    mysqli_stmt_bind_param($statement, 's', $table);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    while ($result && ($row = mysqli_fetch_assoc($result))) {
        $cache[$table][] = array(
            'name' => $row['COLUMN_NAME'],
            'label' => reports_humanize($row['COLUMN_NAME']),
            'data_type' => strtolower($row['DATA_TYPE']),
            'column_type' => $row['COLUMN_TYPE'],
            'category' => reports_column_category($row['DATA_TYPE']),
            'nullable' => $row['IS_NULLABLE'] === 'YES',
            'primary' => $row['COLUMN_KEY'] === 'PRI',
        );
    }
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);

    return $cache[$table];
}

function reports_column_map($database, $table)
{
    $map = array();
    foreach (reports_columns_for_table($database, $table) as $column) {
        $map[$column['name']] = $column;
    }
    return $map;
}

function reports_decode_definition($raw)
{
    if (is_array($raw)) {
        return $raw;
    }

    $decoded = json_decode((string) $raw, true);
    return is_array($decoded) ? $decoded : array();
}

function reports_source_field_exists($database, $sources, $source, $column)
{
    return isset($sources[$source])
        && isset(reports_column_map($database, $sources[$source])[$column]);
}

function reports_default_column_label($database, $sources, $source, $column)
{
    $table = isset($sources[$source]) ? $sources[$source] : '';
    $columns = reports_column_map($database, $table);
    $column_label = isset($columns[$column])
        ? $columns[$column]['label']
        : reports_humanize($column);

    return count($sources) > 1
        ? reports_humanize($table) . ' · ' . $column_label
        : $column_label;
}

function reports_normalize_definition($database, $raw, &$errors)
{
    $errors = array();
    $definition = reports_decode_definition($raw);
    $table_map = reports_table_map($database);
    $base_table = isset($definition['base_table'])
        ? (string) $definition['base_table']
        : '';

    if ($base_table === '' || !isset($table_map[$base_table])) {
        $errors[] = 'Selecciona una tabla principal válida.';
        return false;
    }

    $normalized = array(
        'version' => REPORTS_DEFINITION_VERSION,
        'base_table' => $base_table,
        'joins' => array(),
        'columns' => array(),
        'filters' => array(),
        'sort' => array(),
        'limit' => 1000,
    );
    $sources = array('t0' => $base_table);

    $joins = isset($definition['joins']) && is_array($definition['joins'])
        ? array_slice($definition['joins'], 0, REPORTS_MAX_JOINS)
        : array();
    foreach ($joins as $index => $join) {
        $alias = 't' . (count($normalized['joins']) + 1);
        $table = isset($join['table']) ? (string) $join['table'] : '';
        $type = isset($join['type']) && strtolower($join['type']) === 'inner'
            ? 'inner'
            : 'left';
        $left_source = isset($join['left_source'])
            ? (string) $join['left_source']
            : 't0';
        $left_column = isset($join['left_column'])
            ? (string) $join['left_column']
            : '';
        $right_column = isset($join['right_column'])
            ? (string) $join['right_column']
            : '';

        if (!isset($table_map[$table])) {
            $errors[] = 'Una de las tablas relacionadas ya no existe.';
            continue;
        }
        if (!reports_source_field_exists(
            $database,
            $sources,
            $left_source,
            $left_column
        )) {
            $errors[] = 'El campo de origen de una unión no es válido.';
            continue;
        }
        if (!isset(reports_column_map($database, $table)[$right_column])) {
            $errors[] = 'El campo relacionado de una unión no es válido.';
            continue;
        }

        $normalized['joins'][] = array(
            'table' => $table,
            'type' => $type,
            'left_source' => $left_source,
            'left_column' => $left_column,
            'right_column' => $right_column,
        );
        $sources[$alias] = $table;
    }

    $allowed_operations = array(
        'value', 'count', 'count_distinct', 'sum', 'avg', 'min', 'max',
    );
    $columns = isset($definition['columns']) && is_array($definition['columns'])
        ? array_slice($definition['columns'], 0, REPORTS_MAX_COLUMNS)
        : array();
    foreach ($columns as $column) {
        $source = isset($column['source']) ? (string) $column['source'] : '';
        $name = isset($column['column']) ? (string) $column['column'] : '';
        $operation = isset($column['operation'])
            ? strtolower((string) $column['operation'])
            : 'value';

        if (!reports_source_field_exists($database, $sources, $source, $name)) {
            $errors[] = 'Uno de los campos seleccionados ya no es válido.';
            continue;
        }
        if (!in_array($operation, $allowed_operations, true)) {
            $operation = 'value';
        }

        $label = isset($column['label'])
            ? reports_string_limit($column['label'], 120)
            : '';
        if ($label === '') {
            $label = reports_default_column_label(
                $database,
                $sources,
                $source,
                $name
            );
        }

        $normalized['columns'][] = array(
            'source' => $source,
            'column' => $name,
            'operation' => $operation,
            'label' => $label,
        );
    }

    if (count($normalized['columns']) === 0) {
        $errors[] = 'Añade al menos una columna al informe.';
    }

    $allowed_operators = array(
        'eq', 'neq', 'contains', 'starts', 'ends',
        'gt', 'gte', 'lt', 'lte', 'between', 'in',
        'is_null', 'not_null',
    );
    $allowed_modes = array('manual', 'today', 'centre', 'user');
    $filters = isset($definition['filters']) && is_array($definition['filters'])
        ? array_slice($definition['filters'], 0, REPORTS_MAX_FILTERS)
        : array();
    foreach ($filters as $filter) {
        $source = isset($filter['source']) ? (string) $filter['source'] : '';
        $name = isset($filter['column']) ? (string) $filter['column'] : '';
        $operator = isset($filter['operator'])
            ? strtolower((string) $filter['operator'])
            : 'eq';
        $value_mode = isset($filter['value_mode'])
            ? strtolower((string) $filter['value_mode'])
            : 'manual';

        if (!reports_source_field_exists($database, $sources, $source, $name)) {
            $errors[] = 'Uno de los campos usados como filtro ya no es válido.';
            continue;
        }
        if (!in_array($operator, $allowed_operators, true)) {
            $operator = 'eq';
        }
        if (!in_array($value_mode, $allowed_modes, true)) {
            $value_mode = 'manual';
        }

        $normalized['filters'][] = array(
            'source' => $source,
            'column' => $name,
            'operator' => $operator,
            'value_mode' => $value_mode,
            'value' => isset($filter['value'])
                ? reports_string_limit($filter['value'], 500)
                : '',
            'value2' => isset($filter['value2'])
                ? reports_string_limit($filter['value2'], 500)
                : '',
        );
    }

    $sorts = isset($definition['sort']) && is_array($definition['sort'])
        ? array_slice($definition['sort'], 0, REPORTS_MAX_SORTS)
        : array();
    foreach ($sorts as $sort) {
        $column_index = isset($sort['column_index'])
            ? (int) $sort['column_index']
            : -1;
        if (!isset($normalized['columns'][$column_index])) {
            continue;
        }
        $normalized['sort'][] = array(
            'column_index' => $column_index,
            'direction' => isset($sort['direction'])
                && strtolower($sort['direction']) === 'desc'
                    ? 'desc'
                    : 'asc',
        );
    }

    $limit = isset($definition['limit']) ? (int) $definition['limit'] : 1000;
    $normalized['limit'] = max(1, min(REPORTS_EXPORT_ROWS, $limit));

    return count($errors) === 0 ? $normalized : false;
}

function reports_resolve_filter_value($filter)
{
    switch ($filter['value_mode']) {
        case 'today':
            return date('Y-m-d');
        case 'centre':
            return isset($GLOBALS['centro_actual'])
                ? (string) (int) $GLOBALS['centro_actual']
                : '0';
        case 'user':
            return isset($GLOBALS['idUsuario'])
                ? (string) (int) $GLOBALS['idUsuario']
                : '0';
        default:
            return (string) $filter['value'];
    }
}

function reports_compile_definition($database, $raw, $maximum_rows)
{
    $errors = array();
    $definition = reports_normalize_definition($database, $raw, $errors);
    if ($definition === false) {
        return array('ok' => false, 'errors' => $errors);
    }

    $sources = array('t0' => $definition['base_table']);
    foreach ($definition['joins'] as $index => $join) {
        $sources['t' . ($index + 1)] = $join['table'];
    }

    $select = array();
    $headers = array();
    $group_by = array();
    $has_aggregate = false;
    foreach ($definition['columns'] as $index => $column) {
        $field = reports_quote_identifier($column['source'])
            . '.' . reports_quote_identifier($column['column']);
        switch ($column['operation']) {
            case 'count':
                $expression = 'COUNT(' . $field . ')';
                $has_aggregate = true;
                break;
            case 'count_distinct':
                $expression = 'COUNT(DISTINCT ' . $field . ')';
                $has_aggregate = true;
                break;
            case 'sum':
                $expression = 'SUM(' . $field . ')';
                $has_aggregate = true;
                break;
            case 'avg':
                $expression = 'AVG(' . $field . ')';
                $has_aggregate = true;
                break;
            case 'min':
                $expression = 'MIN(' . $field . ')';
                $has_aggregate = true;
                break;
            case 'max':
                $expression = 'MAX(' . $field . ')';
                $has_aggregate = true;
                break;
            default:
                $expression = $field;
                $group_by[] = $field;
                break;
        }

        $select[] = $expression . ' AS ' . reports_quote_identifier('c' . $index);
        $headers[] = $column['label'];
    }

    $sql = 'SELECT ' . implode(', ', $select)
        . ' FROM ' . reports_quote_identifier($definition['base_table'])
        . ' ' . reports_quote_identifier('t0');

    foreach ($definition['joins'] as $index => $join) {
        $alias = 't' . ($index + 1);
        $sql .= ' ' . strtoupper($join['type']) . ' JOIN '
            . reports_quote_identifier($join['table']) . ' '
            . reports_quote_identifier($alias)
            . ' ON '
            . reports_quote_identifier($join['left_source']) . '.'
            . reports_quote_identifier($join['left_column'])
            . ' = '
            . reports_quote_identifier($alias) . '.'
            . reports_quote_identifier($join['right_column']);
    }

    $where = array();
    $params = array();
    foreach ($definition['filters'] as $filter) {
        $field = reports_quote_identifier($filter['source'])
            . '.' . reports_quote_identifier($filter['column']);
        $value = reports_resolve_filter_value($filter);

        switch ($filter['operator']) {
            case 'neq':
                $where[] = $field . ' <> ?';
                $params[] = $value;
                break;
            case 'contains':
                $where[] = $field . ' LIKE ?';
                $params[] = '%' . $value . '%';
                break;
            case 'starts':
                $where[] = $field . ' LIKE ?';
                $params[] = $value . '%';
                break;
            case 'ends':
                $where[] = $field . ' LIKE ?';
                $params[] = '%' . $value;
                break;
            case 'gt':
                $where[] = $field . ' > ?';
                $params[] = $value;
                break;
            case 'gte':
                $where[] = $field . ' >= ?';
                $params[] = $value;
                break;
            case 'lt':
                $where[] = $field . ' < ?';
                $params[] = $value;
                break;
            case 'lte':
                $where[] = $field . ' <= ?';
                $params[] = $value;
                break;
            case 'between':
                $where[] = $field . ' BETWEEN ? AND ?';
                $params[] = $value;
                $params[] = (string) $filter['value2'];
                break;
            case 'in':
                $values = preg_split('/[\r\n,;]+/', $value);
                $values = array_values(array_filter(array_map('trim', $values), 'strlen'));
                $values = array_slice($values, 0, 100);
                if (count($values) === 0) {
                    $where[] = '1=0';
                } else {
                    $where[] = $field . ' IN ('
                        . implode(', ', array_fill(0, count($values), '?'))
                        . ')';
                    foreach ($values as $list_value) {
                        $params[] = $list_value;
                    }
                }
                break;
            case 'is_null':
                $where[] = $field . ' IS NULL';
                break;
            case 'not_null':
                $where[] = $field . ' IS NOT NULL';
                break;
            default:
                $where[] = $field . ' = ?';
                $params[] = $value;
                break;
        }
    }

    if (count($where) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    if ($has_aggregate && count($group_by) > 0) {
        $sql .= ' GROUP BY ' . implode(', ', array_unique($group_by));
    }

    if (count($definition['sort']) > 0) {
        $order = array();
        foreach ($definition['sort'] as $sort) {
            $order[] = reports_quote_identifier('c' . $sort['column_index'])
                . ' ' . strtoupper($sort['direction']);
        }
        $sql .= ' ORDER BY ' . implode(', ', $order);
    }

    $maximum_rows = max(1, min(REPORTS_EXPORT_ROWS, (int) $maximum_rows));
    $display_limit = min($definition['limit'], $maximum_rows);
    $sql .= ' LIMIT ' . (int) ($display_limit + 1);

    return array(
        'ok' => true,
        'definition' => $definition,
        'sql' => $sql,
        'params' => $params,
        'headers' => $headers,
        'limit' => $display_limit,
    );
}

function reports_bind_statement_params($statement, $params)
{
    if (count($params) === 0) {
        return true;
    }

    $types = str_repeat('s', count($params));
    $arguments = array(&$types);
    foreach ($params as $index => $value) {
        $params[$index] = (string) $value;
        $arguments[] = &$params[$index];
    }

    return call_user_func_array(
        array($statement, 'bind_param'),
        $arguments
    );
}

function reports_execute_definition($database, $raw, $maximum_rows)
{
    $compiled = reports_compile_definition($database, $raw, $maximum_rows);
    if (empty($compiled['ok'])) {
        return $compiled;
    }

    $statement = mysqli_prepare($database, $compiled['sql']);
    if (!$statement) {
        return array(
            'ok' => false,
            'errors' => array('No se ha podido preparar el informe. Revisa las uniones seleccionadas.'),
        );
    }

    if (!reports_bind_statement_params($statement, $compiled['params'])) {
        mysqli_stmt_close($statement);
        return array(
            'ok' => false,
            'errors' => array('No se han podido aplicar los filtros del informe.'),
        );
    }

    if (!mysqli_stmt_execute($statement)) {
        mysqli_stmt_close($statement);
        return array(
            'ok' => false,
            'errors' => array('La combinación de tablas y campos no se puede ejecutar.'),
        );
    }

    $result = mysqli_stmt_get_result($statement);
    if (!$result) {
        mysqli_stmt_close($statement);
        return array(
            'ok' => false,
            'errors' => array('No se han podido recuperar los datos del informe.'),
        );
    }

    $rows = array();
    $truncated = false;
    while ($row = mysqli_fetch_assoc($result)) {
        if (count($rows) >= $compiled['limit']) {
            $truncated = true;
            break;
        }

        $values = array();
        foreach ($compiled['headers'] as $index => $header) {
            $values[] = array_key_exists('c' . $index, $row)
                ? $row['c' . $index]
                : null;
        }
        $rows[] = $values;
    }

    mysqli_free_result($result);
    mysqli_stmt_close($statement);

    return array(
        'ok' => true,
        'definition' => $compiled['definition'],
        'headers' => $compiled['headers'],
        'rows' => $rows,
        'row_count' => count($rows),
        'truncated' => $truncated,
        'limit' => $compiled['limit'],
    );
}

function reports_load_saved($database, $report_id, $user_id)
{
    $report_id = (int) $report_id;
    $user_id = (int) $user_id;
    if ($report_id <= 0 || $user_id <= 0) {
        return false;
    }

    $sql = 'SELECT * FROM informes_personalizados WHERE id_informe=?';
    $statement = mysqli_prepare($database, $sql);
    if (!$statement) {
        return false;
    }
    mysqli_stmt_bind_param($statement, 'i', $report_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $report = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);

    if (!$report) {
        return false;
    }
    if (
        (int) $report['id_usuario_creador'] !== $user_id
        && (int) $report['compartido'] !== 1
        && $user_id !== 1
    ) {
        return false;
    }

    $report['definition_array'] = reports_decode_definition($report['definicion']);
    return $report;
}

function reports_can_manage_saved($report, $user_id)
{
    return is_array($report)
        && (
            (int) $report['id_usuario_creador'] === (int) $user_id
            || (int) $user_id === 1
        );
}

function reports_list_saved($database, $user_id, $search)
{
    $user_id = (int) $user_id;
    $search = trim((string) $search);
    $params = array();
    $types = '';

    $sql = 'SELECT * FROM informes_personalizados WHERE ';
    if ($user_id === 1) {
        $sql .= '1=1';
    } else {
        $sql .= '(id_usuario_creador=? OR compartido=1)';
        $params[] = $user_id;
        $types .= 'i';
    }

    if ($search !== '') {
        $sql .= ' AND (nombre LIKE ? OR descripcion LIKE ?)';
        $like = '%' . $search . '%';
        $params[] = $like;
        $params[] = $like;
        $types .= 'ss';
    }
    $sql .= ' ORDER BY fecha_actualizacion DESC, nombre';

    $statement = mysqli_prepare($database, $sql);
    if (!$statement) {
        return array();
    }

    if (count($params) > 0) {
        $arguments = array(&$types);
        foreach ($params as $index => $value) {
            $arguments[] = &$params[$index];
        }
        call_user_func_array(array($statement, 'bind_param'), $arguments);
    }

    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $reports = array();
    while ($result && ($row = mysqli_fetch_assoc($result))) {
        $row['definition_array'] = reports_decode_definition($row['definicion']);
        $reports[] = $row;
    }
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);

    return $reports;
}

function reports_mark_executed($database, $report_id)
{
    $report_id = (int) $report_id;
    if ($report_id <= 0) {
        return;
    }

    $statement = mysqli_prepare(
        $database,
        'UPDATE informes_personalizados SET ultima_ejecucion=NOW() WHERE id_informe=?'
    );
    if ($statement) {
        mysqli_stmt_bind_param($statement, 'i', $report_id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
}

function reports_safe_filename($name)
{
    $name = trim((string) $name);
    if (function_exists('iconv')) {
        $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        if ($converted !== false) {
            $name = $converted;
        }
    }
    $name = preg_replace('/[^A-Za-z0-9_-]+/', '_', $name);
    $name = trim($name, '_-');
    return $name !== '' ? substr($name, 0, 90) : 'informe_personalizado';
}
