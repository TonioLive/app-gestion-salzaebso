<?php
/** Administrator-managed recovery copies. Existing hashes cannot be recovered. */
function gestion_password_key_path()
{
    return app_env_value('LAPICES_PASSWORD_KEY_FILE', dirname(dirname(dirname(__DIR__))) . '/private/lapices/password-consulta.key');
}

function gestion_password_key()
{
    $path = realpath(gestion_password_key_path());
    $root = str_replace('\\', '/', realpath(dirname(dirname(__DIR__))));
    if (!$path || strpos(strtolower(str_replace('\\', '/', $path)), strtolower($root) . '/') === 0) {
        throw new RuntimeException('La consulta de contraseñas no está configurada.');
    }
    $key = @file_get_contents($path);
    if (!is_string($key) || strlen($key) !== 64) {
        throw new RuntimeException('No se puede acceder a la clave de consulta.');
    }
    return $key;
}

function gestion_password_random($length)
{
    $strong = false;
    $bytes = openssl_random_pseudo_bytes($length, $strong);
    if ($bytes === false || !$strong || strlen($bytes) !== $length) {
        throw new RuntimeException('No se ha podido preparar la copia cifrada.');
    }
    return $bytes;
}

function gestion_password_seal($id, $plain, $hash, $key)
{
    if ((int) $id <= 0 || strlen($key) !== 64 || !password_verify($plain, $hash)) {
        throw new InvalidArgumentException('La copia no corresponde a la contraseña de acceso.');
    }
    $iv = gestion_password_random(16);
    $encrypted = openssl_encrypt($plain, 'aes-256-cbc', substr($key, 0, 32), OPENSSL_RAW_DATA, $iv);
    if ($encrypted === false) { throw new RuntimeException('No se ha podido cifrar la contraseña.'); }
    $payload = 'L1' . $iv . $encrypted;
    $context = (int) $id . ':' . hash('sha256', $hash) . ':';
    $mac = hash_hmac('sha256', $context . $payload, substr($key, 32, 32), true);
    return base64_encode($payload . $mac);
}

function gestion_password_open($id, $encoded, $hash, $fingerprint, $key)
{
    if (!$encoded || !$fingerprint || !hash_equals(hash('sha256', $hash), $fingerprint)) { return null; }
    $raw = base64_decode($encoded, true);
    if (strlen($key) !== 64 || $raw === false || strlen($raw) < 66 || substr($raw, 0, 2) !== 'L1') {
        throw new RuntimeException('La copia cifrada no es válida.');
    }
    $payload = substr($raw, 0, -32);
    $context = (int) $id . ':' . hash('sha256', $hash) . ':';
    $mac = hash_hmac('sha256', $context . $payload, substr($key, 32, 32), true);
    if (!hash_equals($mac, substr($raw, -32))) { throw new RuntimeException('La copia cifrada no es válida.'); }
    $plain = openssl_decrypt(substr($payload, 18), 'aes-256-cbc', substr($key, 0, 32), OPENSSL_RAW_DATA, substr($payload, 2, 16));
    if ($plain === false || !preg_match('//u', $plain)) { throw new RuntimeException('La copia cifrada no es válida.'); }
    return $plain;
}

function gestion_password_admin()
{
    if (empty($GLOBALS['permitido']) || empty($GLOBALS['centro_actual']) || empty($_SESSION['idusuario'])
        || !isset($GLOBALS['nivel_acceso']) || (int) $GLOBALS['nivel_acceso'] !== 4) {
        throw new DomainException('Solo un administrador puede consultar las contraseñas.');
    }
    $result = db_query_params('SELECT id_usuario, pass, nivel_acceso FROM usuarios WHERE id_usuario=? AND nivel_acceso=4 AND tipo_inscripcion>=3', 'i', array((int) $_SESSION['idusuario']));
    $admin = $result ? db_fetch_assoc($result) : false;
    if (!$admin) { throw new DomainException('Tu cuenta no tiene permiso de administrador.'); }
    return $admin;
}

function gestion_password_audit($admin, $event, $count)
{
    if (!db_query_params('INSERT INTO usuarios_password_consulta_log (administrador_id,evento,cantidad,fecha) VALUES (?,?,?,NOW())', 'isi', array((int) $admin, $event, (int) $count))) {
        throw new RuntimeException('No se ha podido registrar la consulta.');
    }
}

function gestion_password_reveal($ids, $confirmation)
{
    $admin = gestion_password_admin();
    $result = db_query_params("SELECT COUNT(*) AS total FROM usuarios_password_consulta_log WHERE administrador_id=? AND evento='fallo' AND fecha>DATE_SUB(NOW(),INTERVAL 15 MINUTE)", 'i', array((int) $admin['id_usuario']));
    $failures = $result ? db_fetch_assoc($result) : false;
    if (!$failures) { throw new RuntimeException('No se puede verificar la consulta en este momento.'); }
    if ((int) $failures['total'] >= 5) { throw new OverflowException('Espera 15 minutos antes de volver a confirmar tu contraseña.'); }
    if (!is_string($confirmation) || strlen($confirmation) > 255 || !password_verify($confirmation, $admin['pass'])) {
        gestion_password_audit($admin['id_usuario'], 'fallo', 0);
        throw new UnexpectedValueException('La contraseña de administrador no es correcta.');
    }
    if (!is_array($ids) || !$ids || count($ids) > 200) { throw new InvalidArgumentException('Selecciona los usuarios del listado.'); }
    $valid = array();
    foreach ($ids as $id) {
        if (!ctype_digit((string) $id) || (int) $id <= 0) { throw new InvalidArgumentException('El listado de usuarios no es válido.'); }
        $valid[(int) $id] = (int) $id;
    }
    $valid = array_values($valid);
    $result = db_query_params('SELECT id_usuario,pass,pass_consulta,pass_consulta_hash FROM usuarios WHERE tipo_inscripcion>=3 AND id_usuario IN (' . implode(',', array_fill(0,count($valid),'?')) . ')', str_repeat('i',count($valid)), $valid);
    if (!$result) { throw new RuntimeException('No se ha podido consultar el listado.'); }
    // Log only actor, date and count, never a hash, password, ciphertext or request body.
    gestion_password_audit($admin['id_usuario'], 'consulta', count($valid));
    $key = null;
    $output = array();
    while ($row = db_fetch_assoc($result)) {
        $plain = null;
        if (!empty($row['pass_consulta']) && hash_equals(hash('sha256',$row['pass']), (string) $row['pass_consulta_hash'])) {
            if ($key === null) { $key = gestion_password_key(); }
            $plain = gestion_password_open($row['id_usuario'],$row['pass_consulta'],$row['pass'],$row['pass_consulta_hash'],$key);
        }
        $output[] = array('id'=>(int) $row['id_usuario'], 'password'=>$plain,
            'estado'=>$plain !== null ? 'disponible' : ($row['pass'] === '' ? 'sin_configurar' : 'no_disponible'));
    }
    return $output;
}

function gestion_password_validate_request($method, $csrf)
{
    if ($method !== 'POST') { throw new InvalidArgumentException('Utiliza el botón de consulta del listado.'); }
    if (!app_verify_csrf($csrf)) { throw new DomainException('Recarga la página para actualizar tu sesión.'); }
}
