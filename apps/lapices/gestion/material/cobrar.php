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
$delivery_id = app_request_int($_POST, 'id_entrega', 0);
$result = material_charge_delivery(
    $material_database,
    $delivery_id,
    (int) $centro_actual,
    (int) $idUsuario
);
material_set_flash(
    !empty($result['ok']) ? 'success' : 'error',
    !empty($result['ok'])
        ? 'Artículo cobrado y factura generada correctamente.'
        : $result['error']
);
header(
    'Location: /gestion/material/control_entregas.php?curso_seleccionado='
    . $course_id . '&id_material=' . $product_id
);
exit;
