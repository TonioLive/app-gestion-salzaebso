<?php
require_once __DIR__ . '/runtime.php';

app_start_session('accesoprivado');
$expired_scope = app_enforce_idle_timeout();
$scope = app_session_scope();

app_security_headers(true);
header('Content-Type: application/json; charset=UTF-8');

if ($scope === '') {
    http_response_code(401);
    echo json_encode(array(
        'ok' => false,
        'expired' => $expired_scope !== '',
        'redirect' => app_session_login_url(
            $expired_scope !== '' ? $expired_scope : 'gestion',
            $expired_scope !== ''
        ),
    ));
    exit;
}

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(array('ok' => false, 'message' => 'Método no permitido.'));
    exit;
}

$fetch_site = strtolower((string) app_request_value(
    $_SERVER,
    'HTTP_SEC_FETCH_SITE',
    ''
));
if ($fetch_site === 'cross-site') {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'message' => 'Origen no permitido.'));
    exit;
}

if (!app_verify_csrf(app_request_value($_POST, '_csrf', ''))) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'message' => 'La sesión no es válida.'));
    exit;
}

app_touch_user_activity();
echo json_encode(array(
    'ok' => true,
    'remaining' => APP_SESSION_IDLE_TIMEOUT,
));
