<?php
require_once __DIR__ . '/bootstrap.php';

if (
    app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST'
    || !app_verify_csrf(app_request_value($_POST, '_csrf', ''))
) {
    http_response_code(403);
    header('Location: /gestion/informes/guardados.php?error=sesion');
    exit;
}

$report_id = app_request_int($_POST, 'id_informe', 0);
$report = $reports_database instanceof mysqli
    ? reports_load_saved($reports_database, $report_id, (int) $idUsuario)
    : false;
if (!$report || !reports_can_manage_saved($report, $idUsuario)) {
    http_response_code(403);
    header('Location: /gestion/informes/guardados.php?error=permiso');
    exit;
}

$statement = mysqli_prepare(
    $reports_database,
    'DELETE FROM informes_personalizados WHERE id_informe=?'
);
$deleted = false;
if ($statement) {
    mysqli_stmt_bind_param($statement, 'i', $report_id);
    $deleted = mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

header(
    'Location: /gestion/informes/guardados.php?'
    . ($deleted ? 'eliminado=1' : 'error=eliminar')
);
