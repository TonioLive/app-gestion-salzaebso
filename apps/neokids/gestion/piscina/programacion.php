<?php
require __DIR__.'/common.php';app_db()->set_charset('utf8mb4');
$group=(int)app_value($_GET,'grupo');$sub=(int)app_value($_GET,'sub');$g=pool_group($group,$sub);
$date=app_value($_GET,'fecha',date('Y-m-d',strtotime('+'.(((int)$g['dia']-(int)date('N')+7)%7).' days')));
if(!pool_valid_date($date)||$date<$g['curso_inicio']||$date>$g['curso_fin']||(int)date('N',strtotime($date))!==(int)$g['dia'])pool_error('Selecciona una fecha del curso que coincida con el día de este grupo.');
$base='programacion.php?grupo='.$group.'&sub='.$sub.'&fecha='.$date;
if($_SERVER['REQUEST_METHOD']==='POST'){
 app_check_csrf();$action=app_value($_POST,'action');
 if(!in_array($action,array('add','edit','template'),true)){http_response_code(403);exit('No se permite borrar programaciones.');}
 $lockName='pool-plan-'.$group.'-'.$sub.'-'.$date;$lock=app_rows('SELECT GET_LOCK(?,3) AS ok','s',array($lockName));if(empty($lock[0]['ok']))pool_error('Hay otra edición en curso. Inténtalo de nuevo.');
 try{
  if($action==='template'){
   $template=(int)app_value($_POST,'template');
   if($template){
    $allowed=app_rows('SELECT id_plantilla FROM agenda_programacion_plantillas WHERE id_plantilla=? AND (monitor=? OR ?=1)','iii',array($template,$idUsuario,pool_manager()?1:0));if(!$allowed){http_response_code(403);exit('No tienes acceso a esa plantilla.');}
    $source=app_rows('SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=? AND numero_sesion=? ORDER BY orden_actividad,id_prog','ii',array($template,max(1,(int)app_value($_POST,'session',1))));
   }else $source=app_rows("SELECT * FROM agenda_programacion WHERE id_grupo=? AND id_subgrupo=? AND fecha='0000-00-00' ORDER BY orden_actividad,id_prog",'ii',array($group,$sub));
   if(!$source)pool_error('La programación seleccionada no contiene actividades.');
   foreach($source as $r){
    $found=app_rows('SELECT id_prog FROM agenda_programacion WHERE id_grupo=? AND id_subgrupo=? AND fecha=? AND id_actividad=? AND COALESCE(actividad,\'\')=? LIMIT 1','iisis',array($group,$sub,$date,(int)$r['id_actividad'],(string)$r['actividad']));
    if(!$found&&!db_query_params('INSERT INTO agenda_programacion (id_grupo,id_subgrupo,fecha,id_actividad,actividad,materiales,tiempo,orden_actividad) VALUES (?,?,?,?,?,?,?,?)','iisissii',array($group,$sub,$date,(int)$r['id_actividad'],(string)$r['actividad'],(string)$r['materiales'],(int)$r['tiempo'],(int)$r['orden_actividad']),app_db()))pool_error('No se ha podido incorporar una actividad.');
   }
   pool_history('carga',$group,array('sub'=>$sub,'fecha'=>$date,'plantilla'=>$template));
  }else{
   $text=trim(app_value($_POST,'activity'));$materials=trim(app_value($_POST,'materials'));$minutes=filter_var(app_value($_POST,'minutes',0),FILTER_VALIDATE_INT);
   if($text===''||strlen($text)>8000||strlen($materials)>4000||$minutes===false||$minutes<0||$minutes>180)pool_error('Escribe la actividad y una duración de 0 a 180 minutos.');
   if($action==='edit'){
    $id=(int)app_value($_POST,'id');$old=app_rows('SELECT * FROM agenda_programacion WHERE id_prog=? AND id_grupo=? AND id_subgrupo=? AND fecha=?','iiis',array($id,$group,$sub,$date));
    if(!$old){http_response_code(403);exit('La actividad no pertenece a este grupo y fecha.');}
    if(!hash_equals(md5(json_encode($old[0])),app_value($_POST,'version')))pool_error('Otra persona ha actualizado esta actividad. Recarga para ver sus cambios antes de guardar.');
    if(!pool_history('programacion',$id,$old[0]))pool_error('No se ha podido conservar el historial.');
    $ok=db_query_params('UPDATE agenda_programacion SET actividad=?,materiales=?,tiempo=? WHERE id_prog=?','ssii',array($text,$materials,$minutes,$id),app_db());
   }else{
    $ok=db_query_params('INSERT INTO agenda_programacion (id_grupo,id_subgrupo,fecha,actividad,materiales,tiempo) VALUES (?,?,?,?,?,?)','iisssi',array($group,$sub,$date,$text,$materials,$minutes),app_db());
    if($ok)pool_history('alta_programacion',(int)app_db()->insert_id,array('grupo'=>$group,'sub'=>$sub,'fecha'=>$date));
   }
   if(!$ok)pool_error('No se ha podido guardar la programación.');
  }
 }finally{app_rows('SELECT RELEASE_LOCK(?)','s',array($lockName));}
 header('Location: '.$base.'&guardado=1',true,303);exit;
}
$activities=app_rows('SELECT * FROM agenda_programacion WHERE id_grupo=? AND id_subgrupo=? AND fecha=? ORDER BY orden_actividad,id_prog','iis',array($group,$sub,$date));
$catalog=array();foreach(app_rows('SELECT id_actividad,descripcion_actividad FROM agenda_programacion_actividades_piscina') as $a)$catalog[$a['id_actividad']]=$a['descripcion_actividad'];
$templates=app_rows('SELECT id_plantilla,descripcion,num_sesiones FROM agenda_programacion_plantillas WHERE monitor=? OR ?=1 ORDER BY descripcion','ii',array($idUsuario,pool_manager()?1:0));
$students=app_rows("SELECT DISTINCT a.id_alumno,u.Nombre,u.Apellido1 FROM aulas_alumnos_grupos ag JOIN alumnos a ON a.id_alumno=ag.id_alumno AND a.id_curso=ag.id_curso JOIN usuarios u ON u.id_usuario=a.id_usuario WHERE ag.id_grupo=? AND ag.id_subgrupo=? AND (a.fecha_baja='0000-00-00' OR a.fecha_baja>=?) AND (a.fecha_matricula='0000-00-00' OR a.fecha_matricula<=?) ORDER BY u.Nombre",'iiss',array($group,$sub,$date,$date));
pool_header('Programación de la clase','grupos');
?>
<a class="back" href="grupos.php?curso=<?php echo (int)$g['id_curso']; ?>">← Volver a los grupos</a>
<section class="card"><div class="lesson-heading"><div><span class="eyebrow"><?php echo app_escape($g['nombre_tipo_grupo']?:'Natación'); ?></span><h2><?php echo app_escape(substr($g['hora_inicio'],0,5).' – '.substr($g['hora_fin'],0,5)); ?></h2><p>Grupo <?php echo $group.' / '.$sub; ?> · <?php echo date('d/m/Y',strtotime($date)); ?></p></div><form method="get" class="filters"><input type="hidden" name="grupo" value="<?php echo $group; ?>"><input type="hidden" name="sub" value="<?php echo $sub; ?>"><label>Fecha de la clase<input type="date" name="fecha" value="<?php echo $date; ?>" min="<?php echo $g['curso_inicio']; ?>" max="<?php echo $g['curso_fin']; ?>" required></label><button class="primary">Ver clase</button></form></div><p>Consulta y adapta las actividades. Los cambios conservan su historial.</p></section>
<?php if(isset($_GET['guardado']))echo '<p class="notice" role="status">Programación guardada.</p>'; ?>
<div class="program-grid"><?php foreach($activities as $a){$text=$a['actividad']?:app_value($catalog,$a['id_actividad'],'Actividad'); ?><form class="card stack lesson-form" method="post"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?php echo (int)$a['id_prog']; ?>"><input type="hidden" name="version" value="<?php echo md5(json_encode($a)); ?>"><label>Actividad<textarea name="activity" maxlength="8000" required><?php echo app_escape(strip_tags($text)); ?></textarea></label><div class="fields"><label>Materiales<textarea name="materials" maxlength="4000"><?php echo app_escape(strip_tags($a['materiales'])); ?></textarea></label><label>Duración en minutos<input type="number" name="minutes" min="0" max="180" value="<?php echo (int)$a['tiempo']; ?>"></label></div><button class="primary">Guardar cambios</button></form><?php } if(!$activities)echo '<p class="card empty">Todavía no hay actividades preparadas para esta clase.</p>'; ?></div>
<details class="card advanced"><summary>+ Añadir actividad</summary><form method="post" class="stack lesson-form"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="action" value="add"><label>Qué vamos a trabajar<textarea name="activity" maxlength="8000" required></textarea></label><label>Materiales<textarea name="materials" maxlength="4000"></textarea></label><label>Duración en minutos<input type="number" name="minutes" min="0" max="180" value="10"></label><button class="primary">Añadir a la clase</button></form></details>
<details class="card"><summary>Incorporar una programación guardada</summary><form method="post" class="filters"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="action" value="template"><label>Programación<select name="template"><option value="0">Habitual de este grupo</option><?php foreach($templates as $t)echo '<option value="'.(int)$t['id_plantilla'].'">'.app_escape($t['descripcion']).'</option>'; ?></select></label><label>Número de sesión<input type="number" name="session" min="1" value="1"></label><button class="primary">Incorporar actividades</button></form><p>Se añaden actividades; se conserva lo que ya has preparado.</p></details>
<section class="card"><h2>Evaluación de los alumnos</h2><div class="roster-actions"><?php foreach($students as $s)echo '<a href="evaluacion.php?grupo='.$group.'&sub='.$sub.'&alumno='.(int)$s['id_alumno'].'">'.app_escape($s['Nombre'].' '.$s['Apellido1']).' →</a>'; if(!$students)echo '<p>No hay alumnos asignados a esta fecha.</p>'; ?></div></section>
<?php pool_footer(); ?>
