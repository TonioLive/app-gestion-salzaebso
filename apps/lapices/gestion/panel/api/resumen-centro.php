<?php
require_once dirname(dirname(dirname(__DIR__))) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/centre_stats.php';

app_security_headers();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

if ($permitido !== true || (int) $centro_actual <= 0) {
    http_response_code(403);
    echo json_encode(array(
        'ok' => false,
        'message' => 'No hay un centro activo.',
    ));
    exit;
}

$database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($database instanceof mysqli)) {
    http_response_code(503);
    echo json_encode(array(
        'ok' => false,
        'message' => 'No se puede consultar el resumen ahora mismo.',
    ));
    exit;
}

$stats = panel_fetch_centre_stats($database, (int) $centro_actual, date('Y-m-d'));
mysqli_close($database);

echo json_encode(array(
    'ok' => true,
    'stats' => $stats,
));

