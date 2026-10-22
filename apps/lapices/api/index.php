<?php
require_once __DIR__ . '/JWT.php';
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once __DIR__ . '/autenticacion_segura.php';

app_security_headers(true);
header('Content-Type: application/json; charset=UTF-8');
header('Vary: Origin');

$allowed_origins = array(
    'https://escuelasinfantileslapices.es',
    'https://www.escuelasinfantileslapices.es',
    'https://local.escuelasinfantileslapices.es',
    'http://localhost',
    'capacitor://localhost',
    'ionic://localhost',
);
$configured_origins = trim((string) getenv('APP_ALLOWED_ORIGINS'));
if ($configured_origins !== '') {
    $allowed_origins = array_merge(
        $allowed_origins,
        array_map('trim', explode(',', $configured_origins))
    );
}

$origin = (string) app_request_value($_SERVER, 'HTTP_ORIGIN', '');
if ($origin !== '') {
    if (!in_array($origin, $allowed_origins, true)) {
        http_response_code(403);
        echo json_encode(array(
            'status' => '403',
            'message' => 'Forbidden',
            'error' => 'Origen no permitido',
        ));
        exit;
    }
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    header('Access-Control-Max-Age: 600');
}

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!app_is_https()) {
    http_response_code(403);
    echo json_encode(array(
        'status' => '403.4',
        'message' => 'SSL required',
        'error' => 'Acceso limitado a conexiones SSL',
    ));
    exit;
}

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST, OPTIONS');
    echo json_encode(array(
        'status' => '405',
        'message' => 'Method Not Allowed',
        'error' => 'Método no permitido',
    ));
    exit;
}

// Limpieza acotada y ocasional: evita un DELETE completo en cada petición.
if (mt_rand(1, 100) === 1) {
    db_query_params(
        'DELETE FROM tokens_activos WHERE creacion < ? LIMIT 500',
        's',
        array(date('Y-m-d H:i:s', time() - 86400))
    );
}

$response = api_authentication_response();
if ($response['status'] === '401') {
    http_response_code(401);
} elseif ($response['status'] === '403') {
    http_response_code(403);
}
echo json_encode($response);
