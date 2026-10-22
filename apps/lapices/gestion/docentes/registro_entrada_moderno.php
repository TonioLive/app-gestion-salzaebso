<?php
require_once __DIR__ . '/../../configuracion/config.php';

function attendance_entry_redirect($message, $type)
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
    attendance_entry_redirect('La solicitud de fichaje no es válida.', 'error');
}

$centre_id = app_request_int($_POST, 'id_centro_asistencia', 0);
$shift = app_request_int($_POST, 'turno', 2);
if ($centre_id <= 0 || !in_array($shift, array(1, 2), true)) {
    attendance_entry_redirect('Selecciona un centro válido para registrar la entrada.', 'error');
}

$database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($database instanceof mysqli)) {
    attendance_entry_redirect('No se ha podido conectar con el servicio de fichajes.', 'error');
}

$centre_sql = (int) $idUsuario === 1
    ? 'SELECT 1 FROM centros WHERE id_centro = ? LIMIT 1'
    : 'SELECT 1 FROM centros WHERE id_centro = ? AND mostrar_en_lapices = 1 LIMIT 1';
$centre_statement = mysqli_prepare($database, $centre_sql);
mysqli_stmt_bind_param($centre_statement, 'i', $centre_id);
mysqli_stmt_execute($centre_statement);
$centre_result = mysqli_stmt_get_result($centre_statement);
$centre_is_valid = $centre_result && mysqli_num_rows($centre_result) > 0;
if ($centre_result) {
    mysqli_free_result($centre_result);
}
mysqli_stmt_close($centre_statement);

if (!$centre_is_valid) {
    mysqli_close($database);
    attendance_entry_redirect('No tienes acceso al centro seleccionado.', 'error');
}

$open_statement = mysqli_prepare(
    $database,
    'SELECT 1 FROM asistencia_docentes '
    . 'WHERE id_usuario = ? AND fecha = CURRENT_DATE AND hora_salida IS NULL LIMIT 1'
);
$user_id = (int) $idUsuario;
mysqli_stmt_bind_param($open_statement, 'i', $user_id);
mysqli_stmt_execute($open_statement);
$open_result = mysqli_stmt_get_result($open_statement);
$already_open = $open_result && mysqli_num_rows($open_result) > 0;
if ($open_result) {
    mysqli_free_result($open_result);
}
mysqli_stmt_close($open_statement);

if ($already_open) {
    mysqli_close($database);
    attendance_entry_redirect('Ya tienes una jornada abierta.', 'warning');
}

$information = 'IP: ' . app_request_value($_SERVER, 'REMOTE_ADDR', '')
    . "\nNavegador: " . app_request_value($_SERVER, 'HTTP_USER_AGENT', '');
$session_id = session_id();
$date = date('Y-m-d');
$entry_time = date('H:i:s');

$insert_statement = mysqli_prepare(
    $database,
    'INSERT INTO asistencia_docentes '
    . '(id_usuario, turno, fecha, hora_entrada, id_centro_asistencia, sesion_e, info_e) '
    . 'VALUES (?, ?, ?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param(
    $insert_statement,
    'iississ',
    $user_id,
    $shift,
    $date,
    $entry_time,
    $centre_id,
    $session_id,
    $information
);
$inserted = mysqli_stmt_execute($insert_statement);
mysqli_stmt_close($insert_statement);
mysqli_close($database);

attendance_entry_redirect(
    $inserted ? 'Entrada registrada correctamente.' : 'No se ha podido registrar la entrada.',
    $inserted ? 'success' : 'error'
);
