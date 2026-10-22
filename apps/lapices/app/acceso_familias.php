<?php
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);

require_once dirname(__DIR__) . '/configuracion/config.php';
app_security_headers(true);

function familias_login_redirect($redirect)
{
    $redirect = trim((string) $redirect);
    if (
        $redirect === '' ||
        strpos($redirect, "\r") !== false ||
        strpos($redirect, "\n") !== false ||
        strpos($redirect, '..') !== false ||
        preg_match('#^(?:https?:)?//#i', $redirect)
    ) {
        return 'index.php';
    }

    if (strpos($redirect, '/') === 0 && strpos($redirect, '/app/') !== 0) {
        return 'index.php';
    }

    return $redirect;
}

$redirect = isset($_POST['redirect']) ? (string) $_POST['redirect'] : '';
$login_errors = array();

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

if (!app_verify_csrf(app_request_value($_POST, '_csrf', ''))) {
    $login_errors[] = 'La sesión del formulario ha caducado. Vuelve a intentarlo.';
}

$nombre_usuario = isset($_POST['user'])
    ? trim((string) $_POST['user'])
    : '';
$pass_usuario = isset($_POST['pass'])
    ? (string) $_POST['pass']
    : '';

$attempt_window = 300;
$attempt_limit = 8;
$now = time();

if (
    !isset($_SESSION['familias_login_window']) ||
    ($now - (int) $_SESSION['familias_login_window']) > $attempt_window
) {
    $_SESSION['familias_login_window'] = $now;
    $_SESSION['familias_login_attempts'] = 0;
}

if (count($login_errors) > 0) {
    // El formulario se vuelve a mostrar con un token nuevo y sin consultar la base de datos.
} elseif ((int) $_SESSION['familias_login_attempts'] >= $attempt_limit) {
    $login_errors[] = 'Has realizado demasiados intentos. Espera unos minutos antes de volver a probar.';
} elseif (
    $nombre_usuario === '' ||
    $pass_usuario === '' ||
    !preg_match('/^[A-Za-z0-9._@-]+$/', $nombre_usuario) ||
    strlen($nombre_usuario) > 100 ||
    strlen($pass_usuario) > 255
) {
    $login_errors[] = 'Revisa el usuario y la contraseña e inténtalo de nuevo.';
} else {
    $busca_usuario = db_query_params(
        'SELECT * FROM padres WHERE usuario_webcam=? LIMIT 1',
        's',
        array($nombre_usuario)
    );

    if ($busca_usuario && db_num_rows($busca_usuario) !== 0) {
        $datos_usuario = db_fetch_array($busca_usuario);
        $password_guardada = isset($datos_usuario['password_webcam'])
            ? (string) $datos_usuario['password_webcam']
            : '';
        $password_info = password_get_info($password_guardada);
        $password_cifrada = !empty($password_info['algo']);
        $password_correcta = $password_cifrada
            ? password_verify($pass_usuario, $password_guardada)
            : hash_equals($password_guardada, $pass_usuario);

        if ($password_correcta) {
            if (!$password_cifrada) {
                $password_hash = password_hash(
                    $pass_usuario,
                    PASSWORD_BCRYPT,
                    array('cost' => 10)
                );
                if ($password_hash !== false) {
                    db_query_params(
                        'UPDATE padres SET password_webcam=? WHERE id_padre=?',
                        'si',
                        array($password_hash, (int) $datos_usuario['id_padre'])
                    );
                }
            }
            $id_usuario = (int) $datos_usuario['id_usuario'];
            $id_hijo = (int) $datos_usuario['padre_de'];
            $busca_datos_padre = db_query_params(
                'SELECT * FROM usuarios WHERE id_usuario=? LIMIT 1',
                'i',
                array($id_usuario)
            );

            if ($busca_datos_padre && db_num_rows($busca_datos_padre) !== 0) {
                session_regenerate_id(true);
                $_SESSION['nombre_padre'] = $nombre_usuario;
                $_SESSION['permitido_padre'] = true;
                $_SESSION['id_padre'] = $id_usuario;
                $_SESSION['id_hijo'] = $id_hijo;
                $_SESSION['mostrar_felicitacion'] = 0;
                unset($_SESSION['password_padre'], $_SESSION['_csrf_token']);
                app_touch_user_activity();
                unset(
                    $_SESSION['familias_login_attempts'],
                    $_SESSION['familias_login_window']
                );

                $remote_host = (string) app_request_value($_SERVER, 'REMOTE_ADDR', '');
                db_query_params(
                    'INSERT INTO control_acceso_padres '
                    . '(sesion, id_padre, fecha, hora, remote_host) '
                    . 'VALUES (?, ?, ?, ?, ?)',
                    'sisss',
                    array(
                        session_id(),
                        $id_usuario,
                        date('Y/m/d'),
                        date('H:i'),
                        $remote_host,
                    )
                );

                header('Location: ' . familias_login_redirect($redirect));
                exit;
            }
        }
    }

    $_SESSION['familias_login_attempts'] =
        (int) $_SESSION['familias_login_attempts'] + 1;
    $login_errors[] = 'El usuario o la contraseña no son correctos.';
}

require __DIR__ . '/login_familias.php';
