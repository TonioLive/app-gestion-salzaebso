<?php
require_once __DIR__.'/runtime.php';
if (PHP_SAPI !== 'cli') {
    ob_start('neo_html_utf8_output');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
    set_exception_handler(function ($error) {
        error_log(get_class($error).': '.$error->getMessage().' at '.$error->getFile().':'.$error->getLine());
        if (!headers_sent()) { http_response_code(500); header('Content-Type: text/plain; charset=UTF-8'); }
        echo 'No se ha podido completar la operación. Vuelve a intentarlo o contacta con administración.';
    });
}

if(PHP_SAPI!=='cli') {
    $entry=str_replace('\\','/',(string)app_value($_SERVER,'SCRIPT_NAME'));
    if(preg_match('~/(?:vendor|node_modules|OLD[^/]*|old|test|examples)/~i',$entry)) {http_response_code(404);exit;}
    if(preg_match('~^/(?:herramientas|mailing|mailer)/|^/api/temp(?:_old)?\.php$~i',$entry)) {
        foreach(array('sql_host','sql_usuario','sql_pass','sql_db') as $key) $GLOBALS[$key]=app_db_settings($key);
        app_start_session('neokids_personal');
        require_once __DIR__.'/request-security.php';
        app_refresh_staff_identity(array());
        if(empty($_SESSION['permitido']) || (int)app_value($_SESSION,'nivel_acceso',0)!==4) {http_response_code(403);exit('Acceso reservado a administración.');}
        app_same_origin_guard();
        if(in_array(strtolower($entry),array('/herramientas/enviar_mensaje_edu.php','/herramientas/guardar_mensaje_edu.php','/mailing/enviar_mensaje_todos_edu.php'),true)) {header('Location: /gestion/mensajes/bandeja.php',true,303);exit;}
        if(app_value($_SERVER,'REQUEST_METHOD')==='POST') app_check_csrf();
        elseif(preg_match('~(?:enviar|ejecutar|probar_correo|/api/temp)~i',$entry)){http_response_code(405);header('Allow: POST');exit('Esta operación requiere una solicitud confirmada desde el programa.');}
    }
}

require_once __DIR__.'/security-boundary.php';
security_entry_boundary();
