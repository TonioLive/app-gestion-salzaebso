<?php
require __DIR__.'/programaciones-common.php';
prog_post();$id=prog_id($_POST,'id_plantilla');$session=prog_id($_POST,'sesion_editada');$template=prog_template($id);
if($session>(int)$template['num_sesiones'])prog_error('La sesión no pertenece a esta programación.');
function prog_ids($name){$items=$_POST[$name]??[];if(!is_array($items)||count($items)>2000)prog_error('La lista de actividades no es válida.');$ids=[];foreach($items as $item){if(!is_scalar($item)||!preg_match('/^[1-9]\d{0,8}$/D',(string)$item))prog_error('La selección no es válida.');$ids[(int)$item]=(int)$item;}return array_values($ids);}
$activities=prog_ids('duallistbox_ActividadesProgramadas');$order=prog_ids('orden_actividades');$copies=prog_ids('sesiones_copiar');
if(!isset($_POST['activity_list_submitted'])&&!isset($_POST['duallistbox_ActividadesProgramadas']))prog_error('Vuelve a abrir la sesión antes de guardar.');
foreach($copies as $copy)if($copy>(int)$template['num_sesiones']||$copy===$session)prog_error('Revisa las sesiones de destino.');
$valid=[];foreach(app_rows('SELECT a.id_actividad FROM agenda_programacion_actividades_piscina a JOIN agenda_programacion_objetivos_piscina o ON o.id_objetivo=a.id_objetivo WHERE o.nivel=? AND o.id_objetivo_g<>0 UNION SELECT id_actividad FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=? AND numero_sesion=?','iii',[(int)$template['nivel'],$id,$session]) as $row)$valid[(int)$row['id_actividad']]=true;
foreach($activities as $activity)if(!isset($valid[$activity]))prog_error('Una actividad no corresponde al nivel de la programación.');
$order=array_values(array_intersect($order,$activities));foreach($activities as $activity)if(!in_array($activity,$order,true))$order[]=$activity;
$new=(int)app_value($_POST,'nueva_act',0)===1;$objective=0;$description='';
if($new){$objective=prog_id($_POST,'objetivo_nueva');$description=trim(app_value($_POST,'actividad_nueva'));if($description===''||mb_strlen($description,'UTF-8')>5000)prog_error('Escribe una descripción de la actividad de hasta 5.000 caracteres.');if(!app_rows('SELECT id_objetivo FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=? AND nivel=? AND id_objetivo_g<>0','ii',[$objective,(int)$template['nivel']]))prog_error('El objetivo no pertenece al nivel de esta programación.');}
$replace=(int)app_value($_POST,'borrar',0)===1;
prog_atomic(function()use($id,$session,$order,$copies,$replace,$new,$objective,$description){
 prog_template($id,true);
 if($new){prog_query('INSERT INTO agenda_programacion_actividades_piscina(id_objetivo,descripcion_actividad) VALUES(?,?)','is',[$objective,$description]);$order[]=(int)app_db()->insert_id;}
 foreach(array_merge([$session],$copies) as $target){
  $existing=app_rows('SELECT id_prog,id_actividad,orden_actividad FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=? AND numero_sesion=? ORDER BY orden_actividad,id_prog','ii',[$id,$target]);$byActivity=[];$position=0;
  foreach($existing as $entry){$byActivity[(int)$entry['id_actividad']][]=$entry;$position=max($position,(int)$entry['orden_actividad']+1);}
  $replaceTarget=$target===$session||$replace;
  if($replaceTarget){foreach($existing as $entry)if(!in_array((int)$entry['id_actividad'],$order,true))prog_query('DELETE FROM agenda_programacion_plantillas_actividades WHERE id_prog=? AND id_plantilla=?','ii',[(int)$entry['id_prog'],$id]);$position=0;}
  foreach($order as $activity){
   if(isset($byActivity[$activity])){if($replaceTarget){foreach($byActivity[$activity] as $entry)prog_query('UPDATE agenda_programacion_plantillas_actividades SET orden_actividad=? WHERE id_prog=? AND id_plantilla=?','iii',[$position++,(int)$entry['id_prog'],$id]);}}
   else prog_query('INSERT INTO agenda_programacion_plantillas_actividades(id_plantilla,numero_sesion,id_actividad,orden_actividad) VALUES(?,?,?,?)','iiii',[$id,$target,$activity,$position++]);
  }
 }
 prog_query('UPDATE agenda_programacion_plantillas SET modif_por=?,fecha_ultima_mod=CURDATE() WHERE id_plantilla=?','ii',[(int)$GLOBALS['idUsuario'],$id]);
});
if($new||(int)app_value($_POST,'volver',0)===1)prog_redirect('editar_plantillas.php?id_plantilla='.$id.'&sesion_editada='.$session.'&done=saved');
prog_redirect('gestion_plantillas.php?id_plantilla='.$id.'&done=saved');