<?php
require_once __DIR__.'/../configuracion/config.php';
require_once __DIR__.'/../configuracion/autentifica.php';
if(!app_can('zona_alumnos')||!in_array((int)$nivel_acceso,array(3,4),true))security_stop();
if(app_value($_SERVER,'REQUEST_METHOD')!=='POST')security_stop('Confirma la renovación desde la ficha del alumno.',405);
app_check_csrf();
$child=(int)app_value($_GET,'id_usuario');$relation=(int)app_value($_GET,'id_padre');
$rows=app_rows('SELECT * FROM padres WHERE id_padre=? AND padre_de=?','ii',array($relation,$child));
if(!$rows)security_stop('Relación familiar no disponible.',404);
security_staff_record('usuarios','id_usuario',$child);
$row=$rows[0];$parent=(int)$row['id_usuario'];$user=(string)$row['usuario_webcam'];
if($user==='') {
    $previous=app_rows("SELECT usuario_webcam FROM padres WHERE id_usuario=? AND usuario_webcam<>'' LIMIT 1",'i',array($parent));
    $user=$previous?(string)$previous[0]['usuario_webcam']:'FAM'.strtoupper(base_convert((string)$parent,10,36)).strtoupper(bin2hex(random_bytes(3)));
}
if(app_rows('SELECT id_padre FROM padres WHERE usuario_webcam=? AND id_usuario<>? LIMIT 1','si',array($user,$parent)))security_stop('El nombre de acceso está duplicado. Contacta con administración.',409);
$password=security_generate_password($user);$hash=security_password_hash($password);
if(!db_query_params('UPDATE padres SET usuario_webcam=?,password_webcam=? WHERE id_usuario=? AND (usuario_webcam=? OR id_padre=?)','ssisi',array($user,$hash,$parent,$user,$relation),app_db()))throw new RuntimeException('Family credential update failed');
security_store_family_password(array('id_usuario'=>$parent,'usuario_webcam'=>$user,'password_webcam'=>$hash),$password);
header('Cache-Control: no-store');header('Location: nuevo_alumno2.php?u='.$child,true,303);exit;
