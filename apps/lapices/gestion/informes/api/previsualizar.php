<?php
require_once dirname(dirname(dirname(__DIR__))) . '/configuracion/config.php';
require_once dirname(dirname(__DIR__)) . '/panel/bootstrap.php';
require_once dirname(__DIR__) . '/report_engine.php';

reports_require_json_access();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-store, max-age=0');

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        'ok' => false,
        'errors' => array('Método no permitido.'),
    ));
    exit;
}

if (!app_verify_csrf(app_request_value($_POST, '_csrf', ''))) {
    http_response_code(403);
    echo json_encode(array(
        'ok' => false,
        'errors' => array('La sesión del formulario ha caducado.'),
    ));
    exit;
}

$database = isset($panel_database) && $panel_database instanceof mysqli
    ? $panel_database
    : false;
if (!($database instanceof mysqli)) {
    http_response_code(503);
    echo json_encode(array(
        'ok' => false,
        'errors' => array('No se puede consultar la base de datos ahora mismo.'),
    ));
    exit;
}

$result = reports_execute_definition(
    $database,
    app_request_value($_POST, 'definicion', ''),
    REPORTS_PREVIEW_ROWS
);

if (empty($result['ok'])) {
    http_response_code(422);
}
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
