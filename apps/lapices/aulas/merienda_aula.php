<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/configuracion/autentifica.php';

if (
    app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST'
    || !app_verify_csrf(app_request_value($_POST, '_csrf', ''))
) {
    http_response_code(403);
    exit('La sesión del formulario ha caducado.');
}

$classroom_id = app_request_int($_POST, 'id_aula', 0);
$course_id = app_request_int($_POST, 'id_curso', 0);
$date = app_request_value($_POST, 'fecha', '');
$active = app_request_int($_POST, 'activa', 0) === 1 ? 1 : 0;
if (
    $classroom_id <= 0
    || $course_id <= 0
    || strtotime($date) === false
) {
    http_response_code(422);
    exit('Los datos del aula no son válidos.');
}
$date = date('Y-m-d', strtotime($date));

$database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($database instanceof mysqli)) {
    http_response_code(503);
    exit('No se puede conectar con la base de datos.');
}

$classroom_statement = mysqli_prepare(
    $database,
    'SELECT id_aula FROM centro_aulas '
    . 'WHERE id_aula=? AND id_centro=? AND id_curso=? LIMIT 1'
);
mysqli_stmt_bind_param(
    $classroom_statement,
    'iii',
    $classroom_id,
    $centro_actual,
    $course_id
);
mysqli_stmt_execute($classroom_statement);
mysqli_stmt_store_result($classroom_statement);
$valid_classroom = mysqli_stmt_num_rows($classroom_statement) > 0;
mysqli_stmt_close($classroom_statement);
if (!$valid_classroom) {
    http_response_code(403);
    exit('El aula no pertenece al centro y curso seleccionados.');
}

$reason = $active ? 'Evento del aula: cocina no debe preparar meriendas' : '';
$updated = date('Y-m-d H:i:s');
$statement = mysqli_prepare(
    $database,
    'INSERT INTO aulas_exclusiones_merienda '
    . '(id_centro,id_curso,id_aula,fecha,motivo,activa,creado_por,'
    . 'fecha_actualizacion) VALUES (?,?,?,?,?,?,?,?) '
    . 'ON DUPLICATE KEY UPDATE motivo=VALUES(motivo),activa=VALUES(activa),'
    . 'creado_por=VALUES(creado_por),fecha_actualizacion=VALUES(fecha_actualizacion)'
);
mysqli_stmt_bind_param(
    $statement,
    'iiissiis',
    $centro_actual,
    $course_id,
    $classroom_id,
    $date,
    $reason,
    $active,
    $idUsuario,
    $updated
);
$saved = mysqli_stmt_execute($statement);
mysqli_stmt_close($statement);

$_SESSION['comedor_flash'] = array(
    'type' => $saved ? 'success' : 'error',
    'message' => $saved
        ? (
            $active
                ? 'Meriendas retiradas para toda el aula. La cocina ya no las contará.'
                : 'Meriendas restauradas para el aula.'
        )
        : 'No se ha podido actualizar la merienda del aula.',
);

header(
    'Location: /aulas/control_asistencia_comedor.php?dia='
    . strtotime($date)
    . '&curso_seleccionado=' . $course_id
);
exit;
