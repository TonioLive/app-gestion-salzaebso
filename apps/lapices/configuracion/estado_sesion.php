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
        'authenticated' => false,
        'expired' => $expired_scope !== '',
        'redirect' => app_session_login_url(
            $expired_scope !== '' ? $expired_scope : 'gestion',
            $expired_scope !== ''
        ),
    ));
    exit;
}

echo json_encode(array(
    'authenticated' => true,
    'scope' => $scope,
    'timeout' => APP_SESSION_IDLE_TIMEOUT,
    'warning' => APP_SESSION_IDLE_WARNING,
    'remaining' => app_session_remaining_seconds(),
    'csrf' => app_csrf_token(),
    'activityUrl' => '/configuracion/actividad_sesion.php',
    'logoutUrl' => app_session_logout_url($scope),
    'loginUrl' => app_session_login_url($scope, true),
));
