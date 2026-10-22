<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(dirname(__DIR__)) . '/gestion/panel/bootstrap.php';

if ($permitido !== true) {
    header('Location: /index.php');
    exit;
}
if (!$panel_has_selected_centre) {
    header('Location: /index.php');
    exit;
}
if (!panel_has_access('zona_almacen')) {
    http_response_code(403);
    exit('No tienes acceso al almacén general.');
}

$warehouse_database = isset($panel_database) && $panel_database instanceof mysqli
    ? $panel_database
    : app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($warehouse_database instanceof mysqli)) {
    http_response_code(503);
    exit('No se puede conectar con la base de datos.');
}

if (!defined('ALMACEN_GENERAL_CENTRO_ID')) {
    define('ALMACEN_GENERAL_CENTRO_ID', 6);
}

function warehouse_icons()
{
    return array(
        'almacen' => array('emoji' => '▦', 'label' => 'Almacén'),
        'caja' => array('emoji' => '□', 'label' => 'Caja'),
        'lapiz' => array('emoji' => '✎', 'label' => 'Material escolar'),
        'limpieza' => array('emoji' => '✦', 'label' => 'Limpieza'),
        'informatica' => array('emoji' => '⌘', 'label' => 'Informática'),
        'herramientas' => array('emoji' => '⚒', 'label' => 'Herramientas'),
        'aula' => array('emoji' => '●', 'label' => 'Material de aula'),
        'repuestos' => array('emoji' => '⚙', 'label' => 'Repuestos'),
        'piscina' => array('emoji' => '≈', 'label' => 'Piscina'),
    );
}

function warehouse_icon($key)
{
    $icons = warehouse_icons();
    return isset($icons[$key]) ? $icons[$key]['emoji'] : $icons['caja']['emoji'];
}

function warehouse_safe_icon($key)
{
    $icons = warehouse_icons();
    return isset($icons[$key]) ? $key : 'caja';
}

function warehouse_safe_color($color)
{
    $color = trim((string) $color);
    return preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? strtoupper($color) : '#21675A';
}

function warehouse_decimal($value)
{
    $value = str_replace(',', '.', trim((string) $value));
    return is_numeric($value) ? round((float) $value, 3) : null;
}

function warehouse_units()
{
    return array('unidad', 'caja', 'paquete', 'rollo', 'litro', 'kilo', 'metro', 'juego');
}

function warehouse_safe_unit($unit)
{
    $unit = strtolower(trim((string) $unit));
    return in_array($unit, warehouse_units(), true) ? $unit : 'unidad';
}

function warehouse_query($sql, $types, $parameters)
{
    return db_query_params($sql, $types, $parameters, $GLOBALS['warehouse_database']);
}

function warehouse_rows($result)
{
    $rows = array();
    if (!($result instanceof mysqli_result)) {
        return $rows;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    return $rows;
}

function warehouse_row($result)
{
    if (!($result instanceof mysqli_result)) {
        return false;
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row;
}

function warehouse_table_exists()
{
    $result = warehouse_query(
        "SELECT COUNT(*) AS total
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='almacen_general_articulos'",
        '',
        array()
    );
    $row = warehouse_row($result);
    return $row && (int) $row['total'] === 1;
}

function warehouse_flash($type, $message)
{
    $_SESSION['warehouse_flash'] = array(
        'type' => in_array($type, array('success', 'warning', 'error'), true) ? $type : 'warning',
        'message' => (string) $message,
    );
}

function warehouse_redirect($anchor)
{
    $safeAnchors = array('inventario', 'movimientos', 'organizacion');
    $anchor = in_array($anchor, $safeAnchors, true) ? $anchor : 'inventario';
    header('Location: /almacen/general/#' . $anchor);
    exit;
}

function warehouse_text($source, $key, $maxLength, $required)
{
    $value = trim((string) app_request_value($source, $key, ''));
    if (function_exists('mb_substr')) {
        $value = mb_substr($value, 0, $maxLength, 'UTF-8');
    } else {
        $value = substr($value, 0, $maxLength);
    }
    if ($required && $value === '') {
        return false;
    }
    return $value;
}

function warehouse_article_exists($articleId)
{
    $result = warehouse_query(
        'SELECT id_articulo FROM almacen_general_articulos WHERE id_articulo=? AND id_centro=? LIMIT 1',
        'ii',
        array((int) $articleId, ALMACEN_GENERAL_CENTRO_ID)
    );
    return warehouse_row($result) !== false;
}

function warehouse_record_movement($articleId, $type, $amount, $reason, $notes, $userId)
{
    $database = $GLOBALS['warehouse_database'];
    $type = strtoupper((string) $type);
    if (!in_array($type, array('ENTRADA', 'SALIDA', 'AJUSTE'), true)) {
        return array(false, 'El tipo de movimiento no es válido.');
    }
    if ($amount === null || $amount < 0 || ($type !== 'AJUSTE' && $amount <= 0)) {
        return array(false, 'Introduce una cantidad válida mayor que cero.');
    }

    mysqli_begin_transaction($database);
    $result = warehouse_query(
        'SELECT stock_actual FROM almacen_general_articulos WHERE id_articulo=? AND id_centro=? AND activo=1 FOR UPDATE',
        'ii',
        array((int) $articleId, ALMACEN_GENERAL_CENTRO_ID)
    );
    $article = warehouse_row($result);
    if (!$article) {
        mysqli_rollback($database);
        return array(false, 'El artículo no existe o está desactivado.');
    }

    $previous = round((float) $article['stock_actual'], 3);
    if ($type === 'ENTRADA') {
        $next = $previous + $amount;
        $signedAmount = $amount;
    } elseif ($type === 'SALIDA') {
        $next = $previous - $amount;
        $signedAmount = -$amount;
    } else {
        $next = $amount;
        $signedAmount = $next - $previous;
    }

    if ($next < 0) {
        mysqli_rollback($database);
        return array(false, 'No hay stock suficiente para realizar esta salida.');
    }

    $updated = warehouse_query(
        'UPDATE almacen_general_articulos SET stock_actual=? WHERE id_articulo=? AND id_centro=?',
        'dii',
        array($next, (int) $articleId, ALMACEN_GENERAL_CENTRO_ID)
    );
    $inserted = $updated && warehouse_query(
        'INSERT INTO almacen_general_movimientos
            (id_articulo, tipo, cantidad, stock_anterior, stock_posterior, motivo, observaciones, id_usuario)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        'isdddssi',
        array((int) $articleId, $type, $signedAmount, $previous, $next, $reason, $notes, (int) $userId)
    );

    if (!$inserted) {
        mysqli_rollback($database);
        return array(false, 'No se ha podido guardar el movimiento.');
    }

    mysqli_commit($database);
    return array(true, $next);
}
