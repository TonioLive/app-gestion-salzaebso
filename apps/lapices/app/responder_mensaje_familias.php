<?php
require dirname(__DIR__) . '/configuracion/config.php';

if (empty($_SESSION['permitido_padre'])) {
    header('Location: /app/login.php');
    exit;
}

require dirname(__DIR__) . '/configuracion/autentifica_padre.php';

$familias_id_mensaje = app_request_int($_POST, 'id_mensaje', 0);
$familias_respuesta = isset($_POST['respuesta'])
    ? trim((string) $_POST['respuesta'])
    : '';
$familias_csrf = isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : '';
$familias_valido = false;

if (
    isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $familias_id_mensaje > 0 &&
    $familias_respuesta !== '' &&
    strlen($familias_respuesta) <= 5000 &&
    app_verify_csrf($familias_csrf)
) {
    $familias_pertenece = db_query(
        'SELECT agenda_notas_padres.id_nota_padre ' .
        'FROM agenda_notas_padres INNER JOIN padres ' .
        'ON padres.padre_de=agenda_notas_padres.id_alumno ' .
        'WHERE agenda_notas_padres.id_nota_padre=' .
        $familias_id_mensaje . ' AND padres.id_usuario=' .
        (int) $id_usuario . ' LIMIT 1'
    );

    $familias_valido =
        $familias_pertenece &&
        db_num_rows($familias_pertenece) > 0;
}

if ($familias_valido) {
    db_query_params(
        'INSERT INTO agenda_notas_padres_respuestas ' .
        '(id_nota, respondido_por, fecha_respuesta, hora_respuesta, texto_respuesta) ' .
        'VALUES (?, ?, ?, ?, ?)',
        'iisss',
        array(
            $familias_id_mensaje,
            (int) $id_usuario,
            date('Y-m-d'),
            date('H:i:s'),
            $familias_respuesta,
        )
    );

    header(
        'Location: /app/ver_mensaje.php?id_mensaje=' .
        $familias_id_mensaje . '#responder',
        true,
        303
    );
    exit;
}

header(
    'Location: /app/ver_mensaje.php?id_mensaje=' .
    $familias_id_mensaje . '&respuesta=error#responder',
    true,
    303
);
exit;

