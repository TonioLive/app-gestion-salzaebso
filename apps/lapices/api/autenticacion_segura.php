<?php

use \Firebase\JWT\JWT;

function api_json_input()
{
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || $raw === '' || strlen($raw) > 32768) {
        return array();
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : array();
}

function api_bearer_token()
{
    $authorization = (string) app_request_value($_SERVER, 'HTTP_AUTHORIZATION', '');
    if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
        return trim($matches[1]);
    }

    return trim((string) app_request_value($_GET, 'token', ''));
}

function api_jwt_secret()
{
    return app_jwt_secret();
}

function api_payload($status, $message, $error)
{
    return array(
        'status' => (string) $status,
        'message' => $message,
        'error' => $error,
    );
}

function api_validate_active_token($token, $secret)
{
    if ($token === '' || $token === 'null' || strlen($token) > 4096) {
        return false;
    }

    try {
        $decoded = JWT::decode($token, $secret, array('HS256'));
    } catch (Exception $exception) {
        return false;
    }

    $expires = isset($decoded->exp)
        ? (int) $decoded->exp
        : (isset($decoded->caducidad) ? strtotime($decoded->caducidad) : 0);
    if ($expires <= time()) {
        return false;
    }

    $active = db_query_params(
        'SELECT id_usuario, id_hijo, id_padre FROM tokens_activos WHERE token=? LIMIT 1',
        's',
        array($token)
    );
    if (!$active || db_num_rows($active) === 0) {
        return false;
    }

    $row = db_fetch_assoc($active);
    return array(
        'id_usuario' => (int) $row['id_usuario'],
        'id_hijo' => (int) $row['id_hijo'],
        'id_padre' => (int) $row['id_padre'],
    );
}

function api_authenticate_credentials($data, $secret)
{
    $username = trim((string) app_request_value($data, 'usuario', ''));
    $password = (string) app_request_value($data, 'contras', '');
    $fcm_token = trim((string) app_request_value($data, 'FCMtoken', ''));

    if (
        $username === '' ||
        $password === '' ||
        strlen($username) > 100 ||
        strlen($password) > 255 ||
        strlen($fcm_token) > 4096 ||
        !preg_match('/^[A-Za-z0-9._@-]+$/', $username)
    ) {
        return api_payload(403, 'Forbidden', 'Credenciales no válidas');
    }

    $result = db_query_params(
        'SELECT id_padre, id_usuario, padre_de, password_webcam '
        . 'FROM padres WHERE usuario_webcam=? LIMIT 1',
        's',
        array($username)
    );
    $parent = $result ? db_fetch_assoc($result) : false;
    if (!$parent) {
        return api_payload(401, 'Unauthorized', 'Credenciales no válidas');
    }

    $stored_password = (string) $parent['password_webcam'];
    $password_info = password_get_info($stored_password);
    $password_ok = !empty($password_info['algo'])
        ? password_verify($password, $stored_password)
        : hash_equals($stored_password, $password);
    if (!$password_ok) {
        return api_payload(401, 'Unauthorized', 'Credenciales no válidas');
    }

    if (empty($password_info['algo'])) {
        $password_hash = password_hash(
            $password,
            PASSWORD_BCRYPT,
            array('cost' => 10)
        );
        if ($password_hash !== false) {
            db_query_params(
                'UPDATE padres SET password_webcam=? WHERE id_padre=?',
                'si',
                array($password_hash, (int) $parent['id_padre'])
            );
        }
    }

    $user_id = (int) $parent['id_usuario'];
    $child_id = (int) $parent['padre_de'];
    $parent_id = (int) $parent['id_padre'];
    $user_result = db_query_params(
        'SELECT Nombre, Apellido1, Apellido2 FROM usuarios WHERE id_usuario=? LIMIT 1',
        'i',
        array($user_id)
    );
    $user = $user_result ? db_fetch_assoc($user_result) : false;
    if (!$user) {
        return api_payload(401, 'Unauthorized', 'Credenciales no válidas');
    }

    $expires_at = time() + 86400;
    $claims = array(
        'usuario' => $username,
        'idUsuario' => $user_id,
        'idHijo' => $child_id,
        'idPadre' => $parent_id,
        'nombre' => $user['Nombre'],
        'apellido1' => $user['Apellido1'],
        'apellido2' => $user['Apellido2'],
        'iat' => time(),
        'exp' => $expires_at,
        'caducidad' => date('Y-m-d H:i:s', $expires_at),
    );
    $jwt = JWT::encode($claims, $secret, 'HS256');

    db_query_params(
        'INSERT INTO tokens_activos (token, id_usuario, id_hijo, id_padre) '
        . 'VALUES (?, ?, ?, ?)',
        'siii',
        array($jwt, $user_id, $child_id, $parent_id)
    );

    if ($fcm_token !== '') {
        $fcm_result = db_query_params(
            'SELECT id_fcm_token FROM tokens_fcm '
            . 'WHERE id_usuario=? AND id_padre=? AND FCMtoken=? LIMIT 1',
            'iis',
            array($user_id, $parent_id, $fcm_token)
        );
        $fcm = $fcm_result ? db_fetch_assoc($fcm_result) : false;
        $access_time = date('Y-m-d H:i:s');
        if ($fcm) {
            db_query_params(
                'UPDATE tokens_fcm SET ultimo_acceso=? WHERE id_fcm_token=?',
                'si',
                array($access_time, (int) $fcm['id_fcm_token'])
            );
        } else {
            db_query_params(
                'INSERT INTO tokens_fcm '
                . '(id_usuario, id_padre, FCMtoken, ultimo_acceso) VALUES (?, ?, ?, ?)',
                'iiss',
                array($user_id, $parent_id, $fcm_token, $access_time)
            );
        }
    }

    $payload = api_payload(202, 'Accepted', '0: No error');
    $payload['id_usuario'] = $user_id;
    $payload['token'] = $jwt;
    return $payload;
}

function api_authentication_response()
{
    $secret = api_jwt_secret();
    $token = api_bearer_token();
    $token_identity = api_validate_active_token($token, $secret);
    if ($token_identity !== false) {
        $payload = api_payload(202, 'Accepted', '0: No error');
        $payload['id_usuario'] = $token_identity['id_usuario'];
        return $payload;
    }

    return api_authenticate_credentials(api_json_input(), $secret);
}
