<?php
require __DIR__.'/programaciones-common.php';
prog_post();
// Old sortable clients must not be able to reorder another template by supplying arbitrary record IDs.
$id=prog_id($_POST,'id_plantilla');$session=prog_id($_POST,'sesion_editada');$template=prog_template($id);
if($session>(int)$template['num_sesiones'])prog_error('La sesión no pertenece a la programación.');
$items=$_POST['actividad']??[];if(!is_array($items)||count($items)>2000)prog_error('El orden no es válido.');
$available=array_map('intval',array_column(app_rows('SELECT id_prog FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=? AND numero_sesion=?','ii',[$id,$session]),'id_prog'));
$order=[];foreach($items as $item){if(!is_scalar($item)||!preg_match('/^[1-9]\d{0,8}$/D',(string)$item)||!in_array((int)$item,$available,true))prog_error('Una actividad no pertenece a esta sesión.');$order[]=(int)$item;}
if(count(array_unique($order))!==count($available)||count($order)!==count($available))prog_error('Vuelve a abrir la sesión antes de ordenarla.');
prog_atomic(function()use($id,$order){prog_template($id,true);foreach($order as $position=>$item)prog_query('UPDATE agenda_programacion_plantillas_actividades SET orden_actividad=? WHERE id_prog=? AND id_plantilla=?','iii',[$position,$item,$id]);});
header('Content-Type: application/json; charset=UTF-8');echo json_encode(['ok'=>true]);