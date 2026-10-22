<?php
require_once __DIR__ . '/bootstrap.php';

$order_id = app_request_int($_GET, 'id', 0);
$order = material_order(
    $material_database,
    $order_id,
    (int) $centro_actual
);
if (!$order) {
    http_response_code(404);
    exit('No se encuentra el pedido.');
}
if ($order['estado'] !== 'stock_actualizado') {
    http_response_code(409);
    exit('El pedido todavía no ha terminado de actualizar el stock.');
}

$lines = material_order_lines($material_database, $order_id);
require_once __DIR__ . '/material_order_pdf.php';
material_order_pdf_output($order, $lines, 'D', '');
exit;
