<?php
require_once dirname(dirname(dirname(__DIR__))) . '/configuracion/config.php';
require_once dirname(dirname(__DIR__)) . '/panel/bootstrap.php';
require_once dirname(__DIR__) . '/report_engine.php';

reports_require_json_access();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-store, max-age=0');

$database = isset($panel_database) && $panel_database instanceof mysqli
    ? $panel_database
    : false;
if (!($database instanceof mysqli)) {
    http_response_code(503);
    echo json_encode(array(
        'ok' => false,
        'errors' => array('No se puede consultar el esquema ahora mismo.'),
    ));
    exit;
}

$table = app_request_value($_GET, 'tabla', '');
$table_map = reports_table_map($database);
if ($table === '' || !isset($table_map[$table])) {
    http_response_code(400);
    echo json_encode(array(
        'ok' => false,
        'errors' => array('La tabla seleccionada no es válida.'),
    ));
    exit;
}

echo json_encode(array(
    'ok' => true,
    'table' => $table_map[$table],
    'columns' => reports_columns_for_table($database, $table),
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
