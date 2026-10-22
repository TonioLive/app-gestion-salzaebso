<?php
require_once __DIR__ . '/../../configuracion/config.php';

function attendance_exit_redirect($message, $type)
{
    $_SESSION['panel_flash'] = array('message' => $message, 'type' => $type);
    header('Location: /index.php');
    exit;
}

if ($permitido !== true) {
    header('Location: /index.php');
    exit;
}

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST'
    || !app_verify_csrf(app_request_value($_POST, '_csrf', ''))
) {
    attendance_exit_redirect('La solicitud de fichaje no es válida.', 'error');
}

$attendance_id = app_request_int($_POST, 'id_registro', 0);
if ($attendance_id <= 0) {
    attendance_exit_redirect('El registro de jornada no es válido.', 'error');
}

$database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($database instanceof mysqli)) {
    attendance_exit_redirect('No se ha podido conectar con el servicio de fichajes.', 'error');
}

$information = 'IP: ' . app_request_value($_SERVER, 'REMOTE_ADDR', '')
    . "\nNavegador: " . app_request_value($_SERVER, 'HTTP_USER_AGENT', '');
$session_id = session_id();
$exit_time = date('H:i:s');
$user_id = (int) $idUsuario;

$statement = mysqli_prepare(
    $database,
    'UPDATE asistencia_docentes '
    . 'SET hora_salida = ?, sesion_s = ?, info_s = ? '
    . 'WHERE id_asistencia = ? AND id_usuario = ? AND hora_salida IS NULL'
);
mysqli_stmt_bind_param(
    $statement,
    'sssii',
    $exit_time,
    $session_id,
    $information,
    $attendance_id,
    $user_id
);
$updated = mysqli_stmt_execute($statement) && mysqli_stmt_affected_rows($statement) === 1;
mysqli_stmt_close($statement);
mysqli_close($database);

attendance_exit_redirect(
    $updated ? 'Salida registrada correctamente.' : 'No se ha encontrado una jornada abierta para cerrar.',
    $updated ? 'success' : 'warning'
);
