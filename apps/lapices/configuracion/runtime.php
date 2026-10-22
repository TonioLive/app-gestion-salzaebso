<?php
/**
 * Shared runtime helpers used by both the modern and transitional screens.
 * Kept compatible with PHP 5.6 so the existing server can run while the
 * production target is upgraded to PHP 8.5.
 */

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/legacy_php_compat.php';

if (!defined('APP_SESSION_IDLE_TIMEOUT')) {
    define('APP_SESSION_IDLE_TIMEOUT', 30 * 60);
}

if (!defined('APP_SESSION_IDLE_WARNING')) {
    define('APP_SESSION_IDLE_WARNING', 2 * 60);
}

if (defined('MYSQLI_REPORT_OFF')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

function app_debug_enabled()
{
    $value = strtolower((string) getenv('APP_DEBUG'));
    return in_array($value, array('1', 'true', 'yes', 'on'), true);
}

function app_configure_error_handling()
{
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
    ini_set('display_errors', app_debug_enabled() ? '1' : '0');
    ini_set('display_startup_errors', app_debug_enabled() ? '1' : '0');
}

app_configure_error_handling();
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) { mb_internal_encoding('UTF-8'); }

function app_is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    return isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
}

function app_start_session($session_name)
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    @ini_set('session.cookie_samesite', 'Lax');
    session_name($session_name);

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params(array(
            'lifetime' => 0,
            'path' => '/',
            'secure' => app_is_https(),
            'httponly' => true,
            'samesite' => 'Lax',
        ));
    } else {
        session_set_cookie_params(0, '/', '', app_is_https(), true);
    }

    session_start();
}

function app_session_scope()
{
    if (!empty($_SESSION['permitido_padre'])) {
        return 'familias';
    }

    if (isset($_SESSION['nombre_usu']) && isset($_SESSION['usuario_password'])) {
        return 'gestion';
    }

    return '';
}

function app_session_last_activity()
{
    return isset($_SESSION['_app_last_user_activity'])
        ? (int) $_SESSION['_app_last_user_activity']
        : 0;
}

function app_touch_user_activity($timestamp = null)
{
    if (app_session_scope() === '') {
        return false;
    }

    $_SESSION['_app_last_user_activity'] = $timestamp === null
        ? time()
        : (int) $timestamp;
    return true;
}

function app_session_remaining_seconds($timestamp = null)
{
    $last_activity = app_session_last_activity();
    if ($last_activity <= 0) {
        return APP_SESSION_IDLE_TIMEOUT;
    }

    $now = $timestamp === null ? time() : (int) $timestamp;
    return max(0, APP_SESSION_IDLE_TIMEOUT - ($now - $last_activity));
}

function app_destroy_current_session()
{
    $_SESSION = array();

    if (ini_get('session.use_cookies')) {
        $parameters = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $parameters['path'],
            isset($parameters['domain']) ? $parameters['domain'] : '',
            !empty($parameters['secure']),
            !empty($parameters['httponly'])
        );
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Expires an authenticated session after 30 minutes without real activity.
 * Ordinary background requests deliberately do not refresh this timestamp.
 * Returns the expired session scope (gestion/familias), or an empty string.
 */
function app_enforce_idle_timeout($timestamp = null)
{
    $scope = app_session_scope();
    if ($scope === '') {
        return '';
    }

    $now = $timestamp === null ? time() : (int) $timestamp;
    $last_activity = app_session_last_activity();
    if ($last_activity <= 0) {
        app_touch_user_activity($now);
        return '';
    }

    if (($now - $last_activity) > APP_SESSION_IDLE_TIMEOUT) {
        app_destroy_current_session();
        return $scope;
    }

    return '';
}

function app_request_is_navigation()
{
    $fetch_mode = strtolower((string) app_request_value(
        $_SERVER,
        'HTTP_SEC_FETCH_MODE',
        ''
    ));
    if ($fetch_mode !== '') {
        return $fetch_mode === 'navigate';
    }

    if (strtolower((string) app_request_value(
        $_SERVER,
        'HTTP_X_REQUESTED_WITH',
        ''
    )) === 'xmlhttprequest') {
        return false;
    }

    return strpos(
        strtolower((string) app_request_value($_SERVER, 'HTTP_ACCEPT', '')),
        'text/html'
    ) !== false;
}

function app_session_login_url($scope, $expired = false)
{
    $url = $scope === 'familias' ? '/app/login.php' : '/index.php';
    return $expired ? $url . '?sesion=expirada' : $url;
}

function app_session_logout_url($scope)
{
    return $scope === 'familias'
        ? '/app/cerrar_sesion.php?motivo=inactividad'
        : '/configuracion/cerrar_sesion.php?motivo=inactividad';
}

function app_jwt_secret()
{
    $configured = (string) getenv('APP_JWT_SECRET');
    if (strlen($configured) >= 32) {
        return $configured;
    }

    $secretFile = __DIR__ . DIRECTORY_SEPARATOR . '.jwt-secret';
    if (is_readable($secretFile)) {
        $stored = trim((string) @file_get_contents($secretFile));
        if (strlen($stored) >= 64) {
            return $stored;
        }
    }

    if (function_exists('random_bytes')) {
        $randomBytes = random_bytes(48);
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $randomBytes = openssl_random_pseudo_bytes(48);
    } else {
        $randomBytes = hash('sha512', uniqid(mt_rand(), true), true);
    }
    $generated = bin2hex($randomBytes);
    $handle = @fopen($secretFile, 'x');
    if ($handle) {
        fwrite($handle, $generated);
        fclose($handle);
        @chmod($secretFile, 0600);
        return $generated;
    }

    $stored = trim((string) @file_get_contents($secretFile));
    if (strlen($stored) >= 64) {
        return $stored;
    }
    throw new RuntimeException('No se puede cargar la clave de firma de la API.');
}

function app_request_value($source, $key, $default = '')
{
    return is_array($source) && array_key_exists($key, $source)
        ? $source[$key]
        : $default;
}

function app_request_int($source, $key, $default = 0)
{
    $value = app_request_value($source, $key, null);
    if ($value === null || filter_var($value, FILTER_VALIDATE_INT) === false) {
        return $default;
    }

    return (int) $value;
}

function app_env_value($name, $fallback = '')
{
    $value = getenv($name);
    return $value === false || $value === '' ? $fallback : $value;
}

function app_escape($value)
{
    return htmlspecialchars(app_text_utf8($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_csrf_token()
{
    if (empty($_SESSION['_csrf_token'])) {
        if (function_exists('random_bytes')) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $_SESSION['_csrf_token'] = hash('sha256', uniqid(mt_rand(), true));
        }
    }

    return $_SESSION['_csrf_token'];
}

function app_verify_csrf($token)
{
    return is_string($token)
        && !empty($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $token);
}

function app_safe_unserialize($value, $default = array())
{
    if (!is_string($value) || $value === '' || strlen($value) > 1048576) {
        return $default;
    }

    // PHP 5.6 does not yet support allowed_classes. Reject serialized object
    // payloads before decoding so request data cannot instantiate classes.
    if (preg_match('/(?:^|[;{}])(?:O|C):[0-9]+:/', $value)) {
        return $default;
    }

    if (PHP_VERSION_ID >= 70000) {
        $result = @unserialize($value, array('allowed_classes' => false));
    } else {
        $result = @unserialize($value);
    }

    if ($result === false && $value !== 'b:0;') {
        return $default;
    }

    return $result;
}

function app_password_hash_if_needed($password)
{
    $password = (string) $password;
    $info = password_get_info($password);
    if (!empty($info['algo'])) {
        return $password;
    }
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => 10));
}

function app_db_connect($host, $username, $password, $database)
{
    if (!extension_loaded('mysqli')) {
        return false;
    }

    $connection = mysqli_init();
    if (!$connection) {
        return false;
    }

    @mysqli_options($connection, MYSQLI_OPT_CONNECT_TIMEOUT, 8);
    $connection_result = @mysqli_real_connect(
        $connection,
        $host,
        $username,
        $password,
        $database
    );
    if (!$connection_result) {
        return false;
    }

    @mysqli_set_charset($connection, 'utf8mb4');
    return $connection;
}

function app_safe_internal_path($path, $fallback)
{
    if (!is_string($path) || $path === '') {
        return $fallback;
    }

    $parts = parse_url($path);
    if ($parts === false || isset($parts['host']) || isset($parts['scheme'])) {
        return $fallback;
    }

    if (strpos($path, '/') !== 0 || strpos($path, '//') === 0) {
        return $fallback;
    }

    return $path;
}

function app_security_headers($private_response = null)
{
    if (headers_sent()) {
        return;
    }

    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('Cross-Origin-Opener-Policy: same-origin-allow-popups');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('X-Permitted-Cross-Domain-Policies: none');

    if (app_is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    if ($private_response === true) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}

function app_is_management_path($path)
{
    $path = '/' . ltrim((string) $path, '/');
    $protectedPrefixes = array(
        '/agenda/',
        '/agenda_alumnos/',
        '/almacen/',
        '/alumnos/',
        '/aulas/',
        '/aulas_docentes/',
        '/caja/',
        '/cocina/',
        '/contactos/',
        '/facturacion/',
        '/gestion/',
        '/gestion_gastos/',
        '/gestion_gastos_solo_lapices/',
        '/herramientas/',
        '/mailing/',
        '/menu/',
        '/pedidos/',
        '/piscina/',
        '/programacion/',
        '/recibos/',
    );

    foreach ($protectedPrefixes as $prefix) {
        if (strpos($path, $prefix) === 0) {
            return true;
        }
    }
    return false;
}

function app_request_is_same_origin()
{
    $fetchSite = strtolower((string) app_request_value($_SERVER, 'HTTP_SEC_FETCH_SITE', ''));
    if ($fetchSite === 'same-origin') {
        return true;
    }

    $source = (string) app_request_value($_SERVER, 'HTTP_ORIGIN', '');
    if ($source === '') {
        $source = (string) app_request_value($_SERVER, 'HTTP_REFERER', '');
    }
    $targetHost = strtolower((string) app_request_value($_SERVER, 'HTTP_HOST', ''));
    if ($source === '' || $targetHost === '') {
        return false;
    }

    $sourceParts = parse_url($source);
    if ($sourceParts === false || empty($sourceParts['host'])) {
        return false;
    }
    $sourceHost = strtolower($sourceParts['host']);
    if (isset($sourceParts['port'])) {
        $sourceHost .= ':' . (int) $sourceParts['port'];
    }
    return hash_equals($targetHost, $sourceHost);
}

/**
 * Capa de compatibilidad para los módulos privados heredados.
 * Impide abrirlos sin sesión y bloquea escrituras procedentes de otro origen.
 * Los módulos renovados siguen usando tokens CSRF explícitos.
 */
function app_enforce_management_request_security($authenticated)
{
    if (PHP_SAPI === 'cli') {
        return;
    }

    $requestUri = (string) app_request_value($_SERVER, 'REQUEST_URI', '/');
    $requestPath = parse_url($requestUri, PHP_URL_PATH);
    if (!app_is_management_path($requestPath)) {
        return;
    }

    if (!$authenticated) {
        if (app_request_is_navigation() && !headers_sent()) {
            header('Location: /index.php', true, 302);
        } else {
            http_response_code(401);
        }
        exit;
    }

    $method = strtoupper((string) app_request_value($_SERVER, 'REQUEST_METHOD', 'GET'));
    $basename = strtolower((string) basename($requestPath));
    $unsafeMethod = !in_array($method, array('GET', 'HEAD', 'OPTIONS'), true);
    $legacyWriteGet = $method === 'GET' && preg_match(
        '/(?:eliminar|borrar|quitar|anular|descartar|guardar|guarda|actualiza|cambiar_estado|nuevo_.+1|editar_.+1)/',
        $basename
    );
    if (!$unsafeMethod && !$legacyWriteGet) {
        return;
    }

    $token = (string) app_request_value(
        $method === 'GET' ? $_GET : $_POST,
        '_csrf',
        ''
    );
    if (app_verify_csrf($token) || app_request_is_same_origin()) {
        return;
    }

    http_response_code(403);
    exit('Solicitud bloqueada por seguridad. Recarga la pantalla e inténtalo de nuevo.');
}
