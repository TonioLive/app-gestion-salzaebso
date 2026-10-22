<?php
require __DIR__.'/programaciones-common.php';
prog_post();
$id=prog_id($_POST,'id_plantilla',true);$monitor=prog_id($_POST,'monitor',true);
$level=prog_id($_POST,'nivel',true);$period=prog_id($_POST,'periodicidad',true);$count=prog_id($_POST,'num_sesiones');
$name=trim(app_value($_POST,'descripcion'));
if($name===''||mb_strlen($name,'UTF-8')>255)prog_error('Escribe un nombre de hasta 255 caracteres.');
if($level>3||$period>1||$count>($period===1?12:52))prog_error('Revisa el nivel, la repetición y el número de sesiones (hasta 52 semanales o 12 mensuales).');
$old=$id?prog_template($id):null;
if($monitor&&(!$old||$monitor!==(int)$old['monitor'])&&!app_rows('SELECT id_usuario FROM usuarios WHERE id_usuario=? AND tipo_inscripcion=4','i',[$monitor]))prog_error('El monitor seleccionado no está disponible.');
$id=prog_atomic(function()use($id,$monitor,$level,$period,$count,$name){
 if($id){
  prog_template($id,true);
  $outside=app_rows('SELECT MAX(numero_sesion) AS last_session FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=?','i',[$id]);
  if((int)($outside[0]['last_session']??0)>$count)prog_error('Hay actividades preparadas en sesiones posteriores. Conserva ese número de sesiones o revisa primero sus actividades.');
  prog_query('UPDATE agenda_programacion_plantillas SET descripcion=?,nivel=?,num_sesiones=?,monitor=?,periodicidad=?,modif_por=?,fecha_ultima_mod=CURDATE() WHERE id_plantilla=?','siiiiii',[$name,$level,$count,$monitor,$period,(int)$GLOBALS['idUsuario'],$id]);
 }else{
  prog_query('INSERT INTO agenda_programacion_plantillas(descripcion,nivel,num_sesiones,monitor,periodicidad,creada_por,modif_por,fecha_ultima_mod) VALUES(?,?,?,?,?,?,?,CURDATE())','siiiiii',[$name,$level,$count,$monitor,$period,(int)$GLOBALS['idUsuario'],(int)$GLOBALS['idUsuario']]);
  $id=(int)app_db()->insert_id;
 }
 return $id;
});
$return=(int)app_value($_POST,'retorno',1);
if($return===2){$session=prog_id($_POST,'sesion_editada');if($session>$count)prog_error('La sesión no pertenece a esta programación.');prog_redirect('editar_plantillas.php?id_plantilla='.$id.'&sesion_editada='.$session);}
prog_redirect($return===0?'gestion_programaciones.php?done=saved':'gestion_plantillas.php?id_plantilla='.$id.'&done=saved');
