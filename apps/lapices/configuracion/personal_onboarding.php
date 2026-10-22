<?php
function personal_onboarding_required($id){
    $r=db_query_params('SELECT pendiente FROM personal_altas WHERE id_usuario=?','i',array((int)$id));
    if(!$r)throw new RuntimeException('No se ha podido comprobar el estado de tu acceso.');
    $row=db_fetch_assoc($r);return $row&&(int)$row['pendiente']===1;
}
function personal_onboarding_allowed_path($path){
    return in_array($path,array('/gestion/usuarios/completar_perfil.php','/configuracion/cerrar_sesion.php','/configuracion/cerrar_sesion_moderno.php','/configuracion/estado_sesion.php','/configuracion/actividad_sesion.php'),true);
}
function personal_onboarding_guard($authenticated){
    if(PHP_SAPI==='cli'||!$authenticated||empty($GLOBALS['idUsuario']))return;
    try{$required=personal_onboarding_required((int)$GLOBALS['idUsuario']);}
    catch(Exception $e){http_response_code(503);exit('No se ha podido comprobar tu acceso. Inténtalo de nuevo.');}
    if(!$required)return;
    $path=parse_url(app_request_value($_SERVER,'REQUEST_URI','/'),PHP_URL_PATH);
    if(personal_onboarding_allowed_path($path))return;
    if(app_request_is_navigation()||app_request_value($_SERVER,'REQUEST_METHOD','GET')==='GET')header('Location: /gestion/usuarios/completar_perfil.php',true,303);
    else http_response_code(403);
    exit('Completa tus datos personales antes de acceder al programa.');
}
function personal_onboarding_validate($input){
    $data=array();foreach(array('telefono1','Direccion','email','telefono_contacto1') as $key){$data[$key]=isset($input[$key])&&is_string($input[$key])?trim($input[$key]):'';if($data[$key]==='')throw new InvalidArgumentException('Completa los cuatro datos obligatorios.');}
    foreach(array('telefono1','telefono_contacto1') as $key){if(strlen($data[$key])>50||!preg_match('/^[+0-9().\s-]+$/',$data[$key])||strlen(preg_replace('/\D/','',$data[$key]))<7||strlen(preg_replace('/\D/','',$data[$key]))>15)throw new InvalidArgumentException('Revisa el teléfono y el teléfono de emergencia.');}
    if(mb_strlen($data['Direccion'],'UTF-8')<8||mb_strlen($data['Direccion'],'UTF-8')>1000)throw new InvalidArgumentException('Escribe tu dirección completa, incluyendo localidad.');
    if(strlen($data['email'])>255||!filter_var($data['email'],FILTER_VALIDATE_EMAIL))throw new InvalidArgumentException('Introduce un correo electrónico válido.');return $data;
}
function personal_onboarding_complete($id,$input){
    $data=personal_onboarding_validate($input);
    if(!personal_onboarding_required($id))return;
    if(!db_query_params('UPDATE usuarios SET telefono1=?,Direccion=?,email=?,telefono_contacto1=? WHERE id_usuario=? AND tipo_inscripcion>=3','ssssi',array($data['telefono1'],$data['Direccion'],$data['email'],$data['telefono_contacto1'],(int)$id)))throw new RuntimeException('No se han podido guardar tus datos.');
    // Read back before opening the gate, including on nontransactional legacy tables.
    $r=db_query_params('SELECT telefono1,Direccion,email,telefono_contacto1 FROM usuarios WHERE id_usuario=? AND tipo_inscripcion>=3','i',array((int)$id));$saved=$r?db_fetch_assoc($r):null;
    if(!$saved)throw new RuntimeException('No se ha podido comprobar tu ficha.');personal_onboarding_validate($saved);
    if(!db_query_params('UPDATE personal_altas SET pendiente=0,completado=NOW() WHERE id_usuario=? AND pendiente=1','i',array((int)$id)))throw new RuntimeException('No se ha podido completar el acceso. Inténtalo de nuevo.');
    $_SESSION['email_usu']=$data['email'];
}
