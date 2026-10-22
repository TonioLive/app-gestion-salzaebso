<?php
require dirname(__DIR__) . '/configuracion/config.php';

if (empty($_SESSION['permitido_padre'])) {
    header('Location: /app/login.php');
    exit;
}

require dirname(__DIR__) . '/configuracion/autentifica_padre.php';

$familias_destinatario = isset($_POST['destinatario'])
    ? trim((string) $_POST['destinatario'])
    : '';
$familias_asunto = isset($_POST['asunto'])
    ? trim((string) $_POST['asunto'])
    : '';
$familias_texto = isset($_POST['texto_mensaje'])
    ? trim((string) $_POST['texto_mensaje'])
    : '';
$familias_csrf = isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : '';
$familias_valido = false;
$familias_tipo_destino = '';
$familias_id_alumno = 0;

if (preg_match('/^([AC])([1-9][0-9]*)$/', $familias_destinatario, $familias_partes)) {
    $familias_tipo_destino = $familias_partes[1];
    $familias_id_alumno = (int) $familias_partes[2];
}

$familias_coordinacion_permitida =
    isset($mostrar_mensajes_administracion) &&
    $mostrar_mensajes_administracion == true;

if (
    isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    app_verify_csrf($familias_csrf) &&
    $familias_id_alumno > 0 &&
    ($familias_tipo_destino === 'A' ||
        ($familias_tipo_destino === 'C' && $familias_coordinacion_permitida)) &&
    $familias_asunto !== '' &&
    strlen($familias_asunto) <= 255 &&
    $familias_texto !== '' &&
    strlen($familias_texto) <= 5000
) {
    $familias_pertenece = db_query(
        'SELECT alumnos.id_usuario ' .
        'FROM alumnos INNER JOIN padres ON padres.padre_de=alumnos.id_usuario ' .
        'WHERE alumnos.id_usuario=' . $familias_id_alumno . ' ' .
        'AND padres.id_usuario=' . (int) $id_usuario . ' ' .
        "AND alumnos.fecha_matricula<='" . date('Y-m-d') . "' " .
        "AND alumnos.fecha_baja>='" . date('Y-m-d') . "' " .
        'AND alumnos.id_curso=' . (int) $id_curso_actual . ' LIMIT 1'
    );

    $familias_valido =
        $familias_pertenece &&
        db_num_rows($familias_pertenece) > 0;
}

if ($familias_valido) {
    $familias_destino = $familias_tipo_destino === 'C' ? 1 : 0;
    $familias_insertado = db_query_params(
        'INSERT INTO agenda_notas_padres ' .
        '(id_padre, id_alumno, id_remitente, tipo_nota, asunto, texto_nota, ' .
        'fecha, hora, fecha_medicacion, hora_medicacion, ' .
        'fecha_fin_medicacion, medicamento, cantidad, destino) ' .
        "VALUES (?, ?, ?, 0, ?, ?, ?, ?, '', '', '', '', '', ?)",
        'iiissssi',
        array(
            (int) $id_usuario,
            $familias_id_alumno,
            (int) $id_usuario,
            $familias_asunto,
            $familias_texto,
            date('Y-m-d'),
            date('H:i:s'),
            $familias_destino,
        )
    );

    if ($familias_insertado) {
        header('Location: /app/mensajeria.php?envio=correcto', true, 303);
        exit;
    }
}

header('Location: /app/nuevo_mensaje.php?estado=error', true, 303);
exit;
