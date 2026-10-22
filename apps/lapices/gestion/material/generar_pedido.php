<?php
require_once __DIR__ . '/bootstrap.php';

if (
    app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST'
    || !app_verify_csrf(app_request_value($_POST, '_csrf', ''))
) {
    http_response_code(403);
    exit('La sesión del formulario ha caducado.');
}

$course_id = app_request_int($_POST, 'curso_seleccionado', 0);
$token = (string) app_request_value($_POST, 'token_peticion', '');
$course = material_course($material_database, $course_id);
if (!$course) {
    material_set_flash('error', 'No se encuentra el curso seleccionado.');
    header('Location: /gestion/material/gestion_material.php');
    exit;
}

$manager_name = isset($nombre_completo) && trim((string) $nombre_completo) !== ''
    ? trim((string) $nombre_completo)
    : (isset($nombre) ? trim((string) $nombre) : 'Usuario de gestión');
$result = material_create_order(
    $material_database,
    (int) $centro_actual,
    $course_id,
    (int) $idUsuario,
    $manager_name,
    $token
);
if (!$result['ok']) {
    material_set_flash('error', $result['error']);
    header(
        'Location: /gestion/material/gestion_material.php?curso_seleccionado='
        . $course_id
    );
    exit;
}

$order = $result['order'];
$lines = material_order_lines(
    $material_database,
    (int) $order['id_pedido']
);
material_clear_order_request_token((int) $centro_actual, $course_id);
require_once __DIR__ . '/material_order_pdf.php';
material_order_pdf_output($order, $lines, 'D', '');
exit;
