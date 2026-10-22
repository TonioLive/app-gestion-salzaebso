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
$current_product_id = app_request_int($_POST, 'id_material_actual', 0);
$delivery_id = app_request_int($_POST, 'id_entrega', 0);
$new_product_id = app_request_int($_POST, 'id_material_nuevo', 0);
$new_quantity = (float) app_request_value($_POST, 'cantidad_nueva', 0);
$notes = app_request_value($_POST, 'notas', '');

$result = material_exchange_delivery(
    $material_database,
    $delivery_id,
    (int) $centro_actual,
    $new_product_id,
    $new_quantity,
    (int) $idUsuario,
    $notes
);
material_set_flash(
    !empty($result['ok']) ? 'success' : 'error',
    !empty($result['ok'])
        ? 'Cambio completado: el artículo anterior figura en negativo y el nuevo se ha cobrado automáticamente.'
        : $result['error']
);
header(
    'Location: /gestion/material/control_entregas.php?curso_seleccionado='
    . $course_id . '&id_material=' . $current_product_id
);
exit;
