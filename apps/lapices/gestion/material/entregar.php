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
$product_id = app_request_int($_POST, 'id_material', 0);
$student_id = app_request_int($_POST, 'id_alumno', 0);
$quantity = (float) app_request_value($_POST, 'cantidad', 0);
$notes = app_request_value($_POST, 'notas', '');

$result = material_create_delivery(
    $material_database,
    (int) $centro_actual,
    $course_id,
    $student_id,
    $product_id,
    $quantity,
    (int) $idUsuario,
    $notes
);
material_set_flash(
    !empty($result['ok']) ? 'success' : 'error',
    !empty($result['ok'])
        ? 'Entrega registrada y descontada del stock. Queda pendiente de cobro.'
        : $result['error']
);
header(
    'Location: /gestion/material/control_entregas.php?curso_seleccionado='
    . $course_id . '&id_material=' . $product_id
);
exit;
