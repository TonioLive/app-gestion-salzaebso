<?php
require_once __DIR__.'/common.php';
app_check_csrf();
$action=(string)app_value($_POST,'action');
$id=(int)app_value($_POST,'id_mensaje');
if(!in_array($action,['new','reply','read'],true)){http_response_code(422);exit('Acción no válida.');}
$message=$action!=='new'?fm_message($id):null;
$child=$message?(int)$message['id_alumno']:(int)app_value($_POST,'id_alumno');
if($action==='new')fm_child($child);
if($message && (int)app_value($_POST,'id_alumno',$child)!==$child){http_response_code(403);exit('La conversación no corresponde a esta familia.');}
if($action==='read'){
 $ok=fm_mark_read($id);
}else{
 $text=trim((string)app_value($_POST,'text'));$subject=trim((string)app_value($_POST,'subject'));
 if($text===''||mb_strlen($text)>12000||!preg_match('//u',$text)||($action==='new'&&($subject===''||mb_strlen($subject)>150||!preg_match('//u',$subject)))){
  http_response_code(422);fm_header('Revisa el mensaje');echo '<p class="fm-error">Escribe un mensaje de hasta 12.000 caracteres y un asunto de hasta 150.</p><p><a class="quiet" href="'.($message?'ver_mensaje.php?id_mensaje='.$id:'nuevo_mensaje.php?id_alumno='.$child).'">Volver al mensaje</a></p>';fm_footer();exit;
 }
 $stored=htmlspecialchars($text,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
 if($action==='new'){
  $ok=db_query_params('INSERT INTO agenda_notas_padres (id_alumno,id_remitente,tipo_nota,asunto,texto_nota,fecha,hora,destino,estado) VALUES (?,?,2,?,?,CURDATE(),CURTIME(),0,0)','iiss',[$child,(int)$idUsuario,$subject,$stored],app_db());
  $id=(int)app_db()->insert_id;
 }else{
  $ok=db_query_params('INSERT INTO agenda_notas_padres_respuestas (id_nota,respondido_por,fecha_respuesta,hora_respuesta,texto_respuesta,leida,direccion) VALUES (?,?,CURDATE(),CURTIME(),?,0,1)','iis',[$id,(int)$idUsuario,$stored],app_db());
  if($ok)fm_mark_read($id);
 }
}
if(!$ok){http_response_code(503);fm_header('No se ha guardado el cambio');echo '<p class="fm-error">No se ha podido guardar. Vuelve a abrir la conversación e inténtalo de nuevo.</p>';fm_footer();exit;}
$_SESSION['fm_notice']=$action==='read'?'Conversación marcada como leída.':'Mensaje guardado y disponible para la familia.';
fm_redirect('ver_mensaje.php?id_mensaje='.$id);
