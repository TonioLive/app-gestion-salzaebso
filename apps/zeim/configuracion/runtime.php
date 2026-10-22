<?php
if(PHP_SAPI!=='cli' && is_file(dirname(__DIR__).'/.private/maintenance.flag')){http_response_code(503);header('Retry-After: 120');exit('Actualización en curso. Vuelve a intentarlo en unos minutos.');}

// Shared by the new panel and existing PHP screens (PHP 5.6+).
require_once __DIR__.'/database.php';
require_once __DIR__.'/credentials.php';
require_once __DIR__.'/legacy_php_compat.php';
date_default_timezone_set('Europe/Madrid');
ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (defined('MYSQLI_REPORT_OFF')) mysqli_report(MYSQLI_REPORT_OFF);
set_include_path('.'.PATH_SEPARATOR.dirname(__DIR__).PATH_SEPARATOR.__DIR__.PATH_SEPARATOR.get_include_path());
function app_value($source, $key, $default = '') {
    return isset($source[$key]) && is_scalar($source[$key]) ? $source[$key] : $default;
}
function app_text($value) {
    $value = (string)$value;
    return preg_match('//u', $value) ? $value : iconv('Windows-1252', 'UTF-8//IGNORE', $value);
}
function app_escape($value) { return htmlspecialchars(app_text($value), ENT_QUOTES, 'UTF-8'); }
function app_csrf_token() {
    if (empty($_SESSION['_csrf'])) {
        $strong = false;
        $bytes = function_exists('random_bytes') ? random_bytes(32) : openssl_random_pseudo_bytes(32, $strong);
        if (!$bytes || (!function_exists('random_bytes') && !$strong)) throw new RuntimeException('Secure random source unavailable');
        $_SESSION['_csrf'] = bin2hex($bytes);
    }
    return $_SESSION['_csrf'];
}
function app_check_csrf() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals(app_csrf_token(), (string)app_value($_POST,'_csrf',app_value($_SERVER,'HTTP_X_CSRF_TOKEN')))) {
        http_response_code(403); exit('La solicitud ha caducado. Vuelve a abrir la pantalla e inténtalo de nuevo.');
    }
}
function app_start_session($name) {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_only_cookies','1'); ini_set('session.use_strict_mode','1');
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name($name);
    if (PHP_VERSION_ID >= 70300) session_set_cookie_params(array('lifetime'=>0,'path'=>'/','secure'=>$https,'httponly'=>true,'samesite'=>'Lax'));
    else session_set_cookie_params(0, '/; SameSite=Lax', '', $https, true);
    session_start();
    if (!empty($_SESSION['idusuario']) && isset($_SESSION['_last_activity']) && time()-$_SESSION['_last_activity'] > 1800) {
        $_SESSION=array(); session_regenerate_id(true); $_SESSION['login_error']='La sesión ha caducado por inactividad.';
    }
    if (!empty($_SESSION['idusuario'])) $_SESSION['_last_activity']=time();
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff'); header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: same-origin'); header('Cache-Control: no-store, private');
    }
}
function app_db() {
    static $db;
    if ($db instanceof mysqli) {
        try { if ($db->thread_id) return $db; } catch (Throwable $error) { $db=null; }
    }
    $db=db_connect($GLOBALS['sql_host'],$GLOBALS['sql_usuario'],$GLOBALS['sql_pass']);
    if (!$db || !db_select_db($GLOBALS['sql_db'],$db)) {
        http_response_code(503); exit('No se puede conectar con la base de datos. Inténtalo de nuevo en unos minutos.');
    }
    return $db;
}
function app_rows($sql, $types = '', $values = array()) {
    $db=app_db();
    $result=$types === '' ? db_query($sql,$db) : db_query_params($sql,$types,$values,$db);
    $rows=array();
    if ($result instanceof mysqli_result) { while ($row=$result->fetch_assoc()) $rows[]=$row; $result->free(); }
    return $rows;
}
function app_can($permission) {
    return !empty($GLOBALS['permitido']) && ((int)$GLOBALS['nivel_acceso']===4 || !empty($GLOBALS[$permission]));
}
function app_can_change_centre() {
    return !empty($_SESSION['idusuario']) && ((int)app_value($_SESSION,'nivel_acceso',0)===4 || !empty($_SESSION['usuario_multicentro']));
}
function app_centres() {
    if (empty($_SESSION['idusuario'])) return array();
    $where=app_can_change_centre() ? '1=1' : 'id_centro='.(int)app_value($_SESSION,'id_centro_usuario',0);
    if (APP_SITE==='santpau' && (int)$_SESSION['idusuario']===6297) $where.=' AND id_centro<>10';
    return app_rows('SELECT * FROM centros WHERE '.$where.' ORDER BY nombre_centro');
}
function app_same_origin_guard() {
    if (PHP_SAPI==='cli' || app_value($_SERVER,'REQUEST_METHOD')!=='POST') return;
    $expected=(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off' ? 'https://' : 'http://').app_value($_SERVER,'HTTP_HOST');
    $origin=app_value($_SERVER,'HTTP_ORIGIN');
    if ($origin!=='' && strtolower(rtrim($origin,'/'))!==strtolower($expected)) {
        http_response_code(403); exit('Origen de solicitud no permitido.');
    }
    if (app_value($_SERVER,'HTTP_SEC_FETCH_SITE')==='cross-site') { http_response_code(403); exit('Solicitud no permitida.'); }
}
function app_login_error($message) { $_SESSION['login_error']=$message; header('Location: /index.php',true,303); exit; }

function app_db_settings($key) {
    static $settings;
    if ($settings===null) $settings=require dirname(__DIR__).'/.private/database.php';
    return isset($settings[$key]) ? $settings[$key] : null;
}
