<?php
ob_start();
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once __DIR__ . '/catalogo.php';
ob_end_clean();

app_security_headers();
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, private, max-age=0');

if ($permitido !== true) {
    http_response_code(401);
    echo json_encode(array('ok' => false, 'message' => 'La sesión no es válida.'));
    exit;
}

$path = (string) app_request_value($_GET, 'ruta', '/');
if (strlen($path) > 500) {
    $path = substr($path, 0, 500);
}

$context = lapices_help_for_path($path);
unset($context['patterns'], $context['keywords']);

echo json_encode(
    array('ok' => true, 'context' => $context),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

