<?php
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);

require_once dirname(__DIR__) . '/configuracion/config.php';
app_security_headers(true);

if (!isset($redirect)) {
    $redirect = isset($_GET['redirect']) ? (string) $_GET['redirect'] : '';
}
if (!isset($login_errors) || !is_array($login_errors)) {
    $login_errors = array();
}
$login_session_state = isset($_GET['sesion']) ? (string) $_GET['sesion'] : '';
$login_notice = '';
if ($login_session_state === 'cerrada') {
    $login_notice = 'Has cerrado la sesión correctamente.';
} elseif ($login_session_state === 'expirada') {
    $login_notice = 'La sesión se ha cerrado tras 30 minutos sin actividad.';
} elseif (isset($_GET['token']) && $_GET['token'] === 'invalido') {
    $login_notice = 'El enlace de acceso ya no es válido. Entra con tu usuario y contraseña.';
}

require __DIR__ . '/includes/login-screen.php';
