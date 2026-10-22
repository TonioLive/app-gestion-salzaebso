<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/api/JWT.php';

use \Firebase\JWT\JWT;

app_security_headers(true);

$token = trim((string) app_request_value($_GET, 'token', ''));
$identity = false;
if ($token !== '' && $token !== 'null' && strlen($token) <= 4096) {
    try {
        $decoded = JWT::decode($token, app_jwt_secret(), array('HS256'));
        $expires = isset($decoded->exp)
            ? (int) $decoded->exp
            : (isset($decoded->caducidad) ? strtotime($decoded->caducidad) : 0);
        if ($expires > time()) {
            $active_result = db_query_params(
                'SELECT id_usuario, id_hijo, id_padre '
                . 'FROM tokens_activos WHERE token=? LIMIT 1',
                's',
                array($token)
            );
            $active = $active_result ? db_fetch_assoc($active_result) : false;
            if ($active) {
                $identity = array(
                    'id_usuario' => (int) $active['id_usuario'],
                    'id_hijo' => (int) $active['id_hijo'],
                    'id_padre' => (int) $active['id_padre'],
                );
            }
        }
    } catch (Exception $exception) {
        $identity = false;
    }
}

if ($identity === false || $identity['id_usuario'] <= 0 || $identity['id_padre'] <= 0) {
    header('Location: /app/login.php?token=invalido', true, 303);
    exit;
}

$parent_result = db_query_params(
    'SELECT usuario_webcam FROM padres WHERE id_padre=? AND id_usuario=? LIMIT 1',
    'ii',
    array($identity['id_padre'], $identity['id_usuario'])
);
$parent = $parent_result ? db_fetch_assoc($parent_result) : false;
if (!$parent || trim((string) $parent['usuario_webcam']) === '') {
    header('Location: /app/login.php?token=invalido', true, 303);
    exit;
}

session_regenerate_id(true);
$_SESSION['nombre_padre'] = (string) $parent['usuario_webcam'];
$_SESSION['permitido_padre'] = true;
$_SESSION['id_padre'] = $identity['id_usuario'];
$_SESSION['id_hijo'] = $identity['id_hijo'];
$_SESSION['mostrar_felicitacion'] = 0;
unset($_SESSION['password_padre'], $_SESSION['_csrf_token']);
app_touch_user_activity();

db_query_params(
    'INSERT INTO control_acceso_padres '
    . '(sesion, id_padre, fecha, hora, remote_host, HTTP_ACCEPT_LANGUAGE, '
    . 'HTTP_User_Agent, logacceso) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
    'sissssss',
    array(
        session_id(),
        $identity['id_usuario'],
        date('Y/m/d'),
        date('H:i'),
        (string) app_request_value($_SERVER, 'REMOTE_ADDR', ''),
        (string) app_request_value($_SERVER, 'HTTP_ACCEPT_LANGUAGE', ''),
        (string) app_request_value($_SERVER, 'HTTP_USER_AGENT', ''),
        '',
    )
);

header('Location: /app/index.php', true, 303);
exit;
