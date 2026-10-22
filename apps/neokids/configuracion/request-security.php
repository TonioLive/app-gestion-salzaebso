<?php
/** Revalidate staff and protect legacy forms, including direct endpoint requests. */
function app_refresh_staff_identity($permissions) {
    if (PHP_SAPI==='cli' || empty($_SESSION['idusuario'])) return;
    $rows=app_rows("SELECT u.* FROM usuarios u WHERE u.id_usuario=? AND u.usuario<>'' AND u.pass<>'' AND u.nivel_acceso IN (2,3,4,10,11) AND EXISTS(SELECT 1 FROM personal_contratos p WHERE p.id_empleado=u.id_usuario AND (p.fecha_fin>=CURDATE() OR p.fecha_fin='0000-00-00' OR p.fecha_fin IS NULL OR p.fecha_inicio>CURDATE())) LIMIT 1",'i',array((int)$_SESSION['idusuario']));
    if (!$rows || !hash_equals((string)app_value($_SESSION,'nombre_usu'),(string)$rows[0]['usuario'])) {
        $_SESSION=array(); session_regenerate_id(true); return;
    }
    $user=$rows[0];
    $stamp=hash('sha256',(string)$user['pass']);
    if(isset($_SESSION['_staff_credential_stamp']) && !hash_equals($_SESSION['_staff_credential_stamp'],$stamp)) {
        $_SESSION=array();session_regenerate_id(true);return;
    }
    $_SESSION['_staff_credential_stamp']=$stamp;
    $_SESSION['nivel_acceso']=(int)$user['nivel_acceso'];
    $_SESSION['id_centro_usuario']=(int)$user['id_centro'];
    $_SESSION['usuario_multicentro']=(int)app_value($user,'multicentro',0);
    foreach($permissions as $permission) $_SESSION[$permission]=(int)app_value($user,$permission,0);
}
function app_security_form_output($html) {
    if (stripos($html,'<html')===false || session_status()!==PHP_SESSION_ACTIVE) return $html;
    $token=app_csrf_token();
    $html=preg_replace_callback('~(<form\b[^>]*>)(\s*<input\b[^>]*name=["\']_csrf["\'][^>]*>)?~i',function($match)use($token){
        if(!empty($match[2]) || !preg_match('~\bmethod\s*=\s*["\']?post\b~i',$match[1]) || preg_match('~\baction\s*=\s*["\'](https?:)?//~i',$match[1]))return $match[0];
        return $match[0].'<input type="hidden" name="_csrf" value="'.$token.'">';
    },$html);
    $script='<script src="/scripts/request-security.js?v=20260929" data-csrf="'.$token.'" defer></script>';
    return preg_replace('~</head>~i',$script.'</head>',$html,1);
}
function app_guard_legacy_request() {
    if(PHP_SAPI==='cli')return;
    $path=str_replace('\\','/',(string)app_value($_SERVER,'SCRIPT_NAME'));
    $section=explode('/',trim($path,'/'))[0];
    $areas=array('alumnos'=>'zona_alumnos','aulas'=>'zona_aulas','aulas_docentes'=>'zona_aulas','programacion'=>'zona_aulas','facturacion'=>'zona_facturacion','facturacion_vista_previa'=>'zona_facturacion','caja'=>'zona_facturacion','almacen'=>'zona_almacen','pedidos'=>'zona_pedidos','gestion_gastos'=>'zona_compras','clientes'=>'zona_facturacion','lista_espera'=>'zona_alumnos','cocina'=>'zona_comedor');
    $admin=preg_match('~^/(?:gestion/usuarios/|herramientas/|mailing/|mailer/)~',$path);
    if(isset($areas[$section])||$admin){
        if(empty($GLOBALS['permitido'])){header('Location: /index.php',true,303);exit;}
        if(($admin && (int)$GLOBALS['nivel_acceso']!==4)||(!$admin && !app_can($areas[$section]))){http_response_code(403);exit('No tienes permiso para acceder a esta sección.');}
    }
    // Shared modern login, family and staff forms all use the same session token.
    if(app_value($_SERVER,'REQUEST_METHOD')==='POST') app_check_csrf();
    static $buffer=false;if(!$buffer){ob_start('app_security_form_output');$buffer=true;}
    if(app_value($_SERVER,'REQUEST_METHOD')==='GET' && !in_array($path,array('/facturacion/modificar_familia.php'),true) && !empty($GLOBALS['permitido']) && (preg_match('~^(?:borrar|eliminar|guardar|guarda_|anular|modificar|modif_|asignar|desasignar|actualizar|cambiar|marcar|desmarcar|agregar)_?~i',basename($path)) || in_array($path,array('/gestion/usuarios/nuevo_usuario.php','/alumnos/generar_password.php','/alumnos/generar_password_todos.php'),true))){
        $query=app_value($_SERVER,'QUERY_STRING');$action=$path.($query!==''?'?'.$query:'');
        $explanation=in_array($path,array('/alumnos/generar_password.php','/alumnos/generar_password_todos.php'),true) ? 'Esta operaci&oacute;n cambia la contrase&ntilde;a. Para consultar o imprimir la clave actual, vuelve a la ficha del alumno. Confirma solo si quieres crear una clave nueva.' : 'Confirma para realizar este cambio.';
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Confirmar operación</title></head><body><main><h1>Confirmar operación</h1><p>'.$explanation.'</p><form method="post" action="'.app_escape($action).'"><button type="submit">Confirmar</button></form><p><a href="javascript:history.back()">Volver</a></p></main></body></html>';exit;
    }
}
