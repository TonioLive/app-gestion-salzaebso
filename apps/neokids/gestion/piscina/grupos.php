<?php
require __DIR__.'/common.php';require __DIR__.'/calendar-model.php';require_once __DIR__.'/student-payments.php';if(!pool_staff_groups()){http_response_code(403);exit('No tienes grupos asignados.');}
app_db()->set_charset('utf8mb4');
$courses=app_rows('SELECT id_curso,nombre,fecha_inicio,fecha_fin FROM cursos_academicos ORDER BY fecha_inicio DESC');
$default=0;foreach($courses as $c)if($c['fecha_inicio']<=date('Y-m-d')&&$c['fecha_fin']>=date('Y-m-d')){$default=(int)$c['id_curso'];break;}
if(!$default && $courses)$default=(int)$courses[0]['id_curso'];
$course=(int)app_value($_GET,'curso',$default);$validCourse=false;foreach($courses as $c)if((int)$c['id_curso']===$course)$validCourse=true;
if(!$validCourse)pool_error('El curso seleccionado no existe.');
$day=max(0,min(7,(int)app_value($_GET,'dia',0)));
$searchQuery=mb_substr(trim(app_value($_GET,'q')),0,200,'UTF-8');
$period=app_value($_GET,'franja','todo');if(!in_array($period,array('todo','manana','mediodia','tarde'),true))$period='todo';
if($period==='mediodia')$period='todo';
$types=app_rows('SELECT id_tipo_grupo,nombre_tipo_grupo FROM aulas_grupos_tipos ORDER BY nombre_tipo_grupo');
if ($_SERVER['REQUEST_METHOD']==='POST') {
 app_check_csrf();
 // Planning changes are reserved to the management roles.
 if(!pool_manager()){http_response_code(403);exit;}
 $subgroup=(int)app_value($_POST,'subgroup');$capacity=filter_var(app_value($_POST,'capacity'),FILTER_VALIDATE_INT);
 if($capacity===false||$capacity<1||$capacity>100)pool_error('La capacidad debe estar entre 1 y 100 plazas.');
 if(app_value($_POST,'action')==='capacity') {
  $group=app_rows('SELECT s.id_grupo_subgrupo FROM aulas_grupos_subgrupos s JOIN aulas_grupos g ON g.id_grupo=s.id_grupo WHERE s.id_grupo_subgrupo=? AND g.id_curso=?','ii',array($subgroup,$course));
  if(!$group)pool_error('No se encuentra el grupo.');
  $ok=db_query_params('UPDATE aulas_grupos_subgrupos SET capacidad=? WHERE id_grupo_subgrupo=?','ii',array($capacity,$subgroup),app_db());
 } else {
  $newDay=filter_var(app_value($_POST,'day'),FILTER_VALIDATE_INT);$start=app_value($_POST,'start');$end=app_value($_POST,'end');$type=(int)app_value($_POST,'type');
  if($newDay<1||$newDay>7||!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',$start)||!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',$end)||$end<=$start)pool_error('Revisa el día y la hora de inicio y final.');
  $validType=false;foreach($types as $t)if((int)$t['id_tipo_grupo']===$type)$validType=true;if(!$validType)pool_error('Selecciona un tipo de grupo válido.');
  $db=app_db();
  // Serialize planning creates for the course, including tables still using MyISAM.
  $lock=app_rows('SELECT GET_LOCK(?,3) AS acquired','s',array('neokids-planning-'.$course));
  if(!$lock||!(int)$lock[0]['acquired'])pool_error('Hay otra edición en curso. Inténtalo de nuevo.');
  $same=app_rows('SELECT id_grupo FROM aulas_grupos WHERE id_curso=? AND dia=? AND hora_inicio=? AND hora_fin=? LIMIT 1','iiss',array($course,$newDay,$start,$end));
  $created=false;
  if($same)$groupId=(int)$same[0]['id_grupo'];
  else { $created=db_query_params('INSERT INTO aulas_grupos (id_curso,dia,hora_inicio,hora_fin,orden,num_subgrupos) VALUES (?,?,?,?,0,0)','iiss',array($course,$newDay,$start,$end),$db);$groupId=(int)$db->insert_id; }
  $next=app_rows('SELECT COALESCE(MAX(id_subgrupo)+1,0) AS next_id FROM aulas_grupos_subgrupos WHERE id_grupo=?','i',array($groupId));
  $nextId=(int)$next[0]['next_id'];
  $ok=$groupId?db_query_params('INSERT INTO aulas_grupos_subgrupos (id_grupo,id_subgrupo,capacidad,tipo_subgrupo,bloqueado) VALUES (?,?,?,?,0)','iiii',array($groupId,$nextId,$capacity,$type),$db):false;
  if($ok)db_query_params('UPDATE aulas_grupos SET num_subgrupos=(SELECT COALESCE(MAX(id_subgrupo)+1,0) FROM aulas_grupos_subgrupos WHERE id_grupo=?) WHERE id_grupo=?','ii',array($groupId,$groupId),$db);
  elseif($created)db_query_params('DELETE FROM aulas_grupos WHERE id_grupo=?','i',array($groupId),$db);
  app_rows('SELECT RELEASE_LOCK(?)','s',array('neokids-planning-'.$course));
 }
 if(!$ok)pool_error('No se ha podido guardar el grupo. Revisa los datos e inténtalo de nuevo.');
 $_SESSION['pool_notice']='Grupo actualizado.';header('Location: grupos.php?curso='.$course.'&dia='.$day.'&franja='.$period,true,303);exit;
}
// Four batched reads replace nested per-slot queries in the previous timetable.
$groups=app_rows('SELECT g.*,s.id_grupo_subgrupo,s.id_subgrupo,s.capacidad,s.tipo_subgrupo,s.bloqueado,t.nombre_tipo_grupo FROM aulas_grupos g JOIN aulas_grupos_subgrupos s ON s.id_grupo=g.id_grupo LEFT JOIN aulas_grupos_tipos t ON t.id_tipo_grupo=s.tipo_subgrupo WHERE g.id_curso=? ORDER BY g.dia,g.hora_inicio,s.id_subgrupo','i',array($course));
$students=app_rows("SELECT DISTINCT ag.id_grupo,ag.id_subgrupo,a.id_alumno,a.id_usuario,a.id_centro,u.Nombre,u.Apellido1,u.Apellido2 FROM aulas_alumnos_grupos ag JOIN alumnos a ON a.id_alumno=ag.id_alumno AND a.id_curso=ag.id_curso JOIN usuarios u ON u.id_usuario=a.id_usuario WHERE ag.id_curso=? AND (a.fecha_baja='0000-00-00' OR a.fecha_baja IS NULL OR a.fecha_baja>=CURDATE()) AND (a.fecha_matricula IS NULL OR a.fecha_matricula='0000-00-00' OR a.fecha_matricula<=CURDATE())",'i',array($course));
$monitors=app_rows('SELECT gm.id_grupo,gm.id_subgrupo,gm.id_usuario,u.Nombre,u.Apellido1 FROM aulas_grupos_monitores gm JOIN aulas_grupos g ON g.id_grupo=gm.id_grupo JOIN usuarios u ON u.id_usuario=gm.id_usuario WHERE g.id_curso=?','i',array($course));
$mine=array();foreach($monitors as $m)if((int)$m['id_usuario']===(int)$idUsuario)$mine[$m['id_grupo'].'-'.$m['id_subgrupo']]=true;if(!pool_manager())$groups=array_values(array_filter($groups,function($g)use($mine){return isset($mine[$g['id_grupo'].'-'.$g['id_subgrupo']]);}));
$rosters=array();foreach($students as $s)$rosters[$s['id_grupo'].'-'.$s['id_subgrupo']][]=$s;
$team=array();foreach($monitors as $m)$team[$m['id_grupo'].'-'.$m['id_subgrupo']][]=trim($m['Nombre'].' '.$m['Apellido1']);
$days=array(1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo');
$visible=array();$occupied=0;$capacity=0;$free=0;foreach($groups as $g)if((!$day||(int)$g['dia']===$day)&&($period==='todo'||pool_calendar_period($g['hora_inicio'])===$period)){$visible[]=$g;$k=$g['id_grupo'].'-'.$g['id_subgrupo'];$used=isset($rosters[$k])?count($rosters[$k]):0;$occupied+=$used;$capacity+=(int)$g['capacidad'];if(!$g['bloqueado'])$free+=max(0,(int)$g['capacidad']-$used);}
// Read billing once for the enrolments rendered in this permitted timetable.
$paymentEnrolments=array();foreach($visible as $g){$key=$g['id_grupo'].'-'.$g['id_subgrupo'];foreach($rosters[$key]??array() as $student)$paymentEnrolments[(int)$student['id_alumno']]=true;}
$studentPayments=array();$paymentLookupFailed=false;
try{$studentPayments=pool_student_payment_status(array_keys($paymentEnrolments));}
catch(RuntimeException $error){$paymentLookupFailed=true;error_log('NeoKids calendar: payment status lookup failed.');}
$canOpenStudentFile=app_can('zona_alumnos')&&in_array((int)$nivel_acceso,array(3,4),true);
pool_header('Grupos y horarios','grupos');
if(!empty($_SESSION['pool_notice'])){echo '<p class="notice" role="status">'.app_escape($_SESSION['pool_notice']).'</p>';unset($_SESSION['pool_notice']);}
if($paymentLookupFailed)echo '<p class="notice" role="status">No se ha podido consultar el estado de los cobros. Puedes seguir viendo los grupos; los estados de pago no están disponibles en este momento.</p>';
?>
<form class="filters" method="get"><label>Curso<select name="curso"><?php foreach($courses as $c){ ?><option value="<?php echo (int)$c['id_curso']; ?>" <?php if((int)$c['id_curso']===$course)echo 'selected'; ?>><?php echo app_escape($c['nombre']); ?></option><?php } ?></select></label><label>Día<select name="dia"><option value="0">Toda la semana</option><?php foreach($days as $d=>$name){ ?><option value="<?php echo $d; ?>" <?php if($day===$d)echo 'selected'; ?>><?php echo $name; ?></option><?php } ?></select></label><input type="hidden" name="franja" value="<?php echo $period; ?>"><button class="primary">Ver horario</button><label class="group-search">Buscar grupo o alumno<input id="search-groups" name="q" type="search" value="<?php echo app_escape($searchQuery); ?>" maxlength="200" placeholder="Nombre, apellidos, hora o tipo…"></label></form>
<div class="stats"><article class="card"><span>Grupos en esta franja</span><strong><?php echo count($visible); ?></strong></article><article class="card"><span>Plazas ocupadas</span><strong><?php echo $occupied; ?><small> / <?php echo $capacity; ?></small></strong></article><article class="card"><span>Plazas libres</span><strong><?php echo $free; ?></strong></article></div>
<?php if(pool_manager()){ ?><details class="card"><summary>+ Crear grupo</summary><form class="filters" method="post"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="action" value="create"><label>Día<select name="day"><?php foreach($days as $d=>$name)echo '<option value="'.$d.'"'.($day===$d?' selected':'').'>'.$name.'</option>'; ?></select></label><label>Inicio<input type="time" name="start" required></label><label>Final<input type="time" name="end" required></label><label>Tipo<select name="type"><?php foreach($types as $t)echo '<option value="'.(int)$t['id_tipo_grupo'].'">'.app_escape($t['nombre_tipo_grupo']).'</option>'; ?></select></label><label>Plazas<input type="number" name="capacity" min="1" max="100" value="6" required></label><button class="primary">Crear grupo</button></form><p>Si esa franja ya existe, se añade un subgrupo a la misma hora.</p></details><?php } ?>
<?php define('POOL_CALENDAR_VIEW',true);require __DIR__.'/calendar-view.php'; ?>
<p id="no-groups" class="card empty" <?php if($visible)echo 'hidden'; ?>>No hay grupos que coincidan con esta selección.</p><?php if(pool_manager()){ ?><details class="card advanced"><summary>Más opciones de planificación</summary><div class="advanced-links"><a href="/gestion/grupos_pruebas/?curso_seleccionado=<?php echo $course; ?>">Planificación completa</a><a href="/gestion/grupos_lapices_nuevo/">Grupos de escuelas</a><a href="/gestion/grupos_externos/">Grupos externos</a><a href="/gestion/grupos_pruebas/gestion_programaciones.php">Programaciones</a></div></details><?php } ?>
<script src="/gestion/piscina/calendar.js?v=20260930-4" defer></script>
<?php pool_footer(); ?>
