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
$planned = max(0, (float) app_request_value($_POST, 'cantidad_planificada', 0));
$course = material_course($material_database, $course_id);
$product = material_product($material_database, $product_id);

if (!$course || !$product) {
    material_set_flash('error', 'No se encuentra el curso o el artículo.');
} else {
    $pupils = material_pupils(
        $material_database,
        (int) $centro_actual,
        $course_id
    );
    $estimate = material_estimate($product, $pupils, $course);
    $saved = material_save_forecast(
        $material_database,
        (int) $centro_actual,
        $course_id,
        $product_id,
        $estimate['recommended'],
        $planned,
        (int) $idUsuario
    );
    material_set_flash(
        $saved ? 'success' : 'error',
        $saved
            ? 'Previsión guardada. Puedes cambiarla en cualquier momento.'
            : 'No se ha podido guardar la previsión.'
    );
}

header(
    'Location: /gestion/material/gestion_material.php?curso_seleccionado='
    . $course_id
);
exit;
