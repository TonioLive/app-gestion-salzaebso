<?php
if(!isset($schoolPoolKind)){http_response_code(404);exit;}
require __DIR__.'/common.php';
require_once __DIR__.'/school-groups-model.php';
if(!pool_manager()){http_response_code(403);exit('No tienes permiso para gestionar grupos de colegios.');}
$kind=school_pool_kind($schoolPoolKind);$base=school_pool_path($kind);
$title=$kind===1?'Grupos de escuelas':'Grupos externos';
$days=array(1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo');
function school_page_url($base,$context,$changes=array()){return $base.'?'.http_build_query(array_merge($context,$changes),'','&');}
function school_page_name($student){return trim(app_text($student['Nombre'].' '.$student['Apellido1'].' '.$student['Apellido2']));}
function school_page_slot($slot){global $days;return ($days[(int)$slot['dia']]??'Día sin definir').' '.substr($slot['hora_inicio'],0,5).'–'.substr($slot['hora_fin'],0,5).' · Grupo '.((int)$slot['id_subgrupo']+1);}
function school_page_fields($action){echo '<input type="hidden" name="_csrf" value="'.app_escape(app_csrf_token()).'"><input type="hidden" name="action" value="'.app_escape($action).'">';}
$error='';$dateAdjusted=false;$data=null;$course=null;
try {
    $courses=school_pool_rows('SELECT id_curso,nombre,fecha_inicio,fecha_fin FROM cursos_academicos ORDER BY fecha_inicio DESC');
    $default=0;foreach($courses as $candidate)if($candidate['fecha_inicio']<=date('Y-m-d')&&$candidate['fecha_fin']>=date('Y-m-d')){$default=(int)$candidate['id_curso'];break;}
    if(!$default&&$courses)$default=(int)$courses[0]['id_curso'];
    $courseId=(int)app_value($_GET,'curso',app_value($_GET,'curso_seleccionado_nk',$default));
    foreach($courses as $candidate)if((int)$candidate['id_curso']===$courseId)$course=$candidate;
    if(!$course)throw new InvalidArgumentException('El curso seleccionado no existe.');
    $date=app_value($_GET,'fecha',date('Y-m-d'));
    if(ctype_digit((string)$date))$date=date('Y-m-d',(int)$date);
    if(!pool_valid_date($date))throw new InvalidArgumentException('Selecciona una fecha válida.');
    if($date<$course['fecha_inicio']||$date>$course['fecha_fin']){$date=$course['fecha_inicio'];$dateAdjusted=true;}
    $centre=(int)app_value($_GET,'centro',0);$classId=(int)app_value($_GET,'aula',0);
    $view=app_value($_GET,'vista','clases')==='horarios'?'horarios':'clases';
    $search=mb_substr(trim(app_value($_GET,'q')),0,150,'UTF-8');
    $context=array('curso'=>$courseId,'fecha'=>$date,'centro'=>$centre,'aula'=>$classId,'vista'=>$view,'q'=>$search);
    $data=school_pool_data($kind,$course,$date);
    $centreIds=array_map(function($row){return (int)$row['id_centro'];},$data['centres']);
    if($centre&&!in_array($centre,$centreIds,true))throw new DomainException('El centro seleccionado no pertenece a este apartado.');
    $selected=null;foreach($data['classes'] as $class)if((int)$class['id_aula']===$classId)$selected=$class;
    if($classId&&(!$selected||($centre&&(int)$selected['id_centro']!==$centre)))throw new DomainException('La clase seleccionada no pertenece a este curso y apartado.');
    if(app_value($_SERVER,'REQUEST_METHOD')==='POST'){
        app_check_csrf();$action=app_value($_POST,'action');
        if($action==='assign'){
            $studentId=(int)app_value($_POST,'student');
            if(!$selected||!isset($data['students'][$studentId])||(int)$data['students'][$studentId]['aula']!==$classId)throw new DomainException('Selecciona un alumno de esta clase.');
            school_pool_assign($kind,$course,$date,$studentId,(int)app_value($_POST,'slot'));
        } elseif($action==='remove') school_pool_remove($kind,$course,$date,(int)app_value($_POST,'assignment'));
        else throw new InvalidArgumentException('La acción no es válida.');
        $_SESSION['school_pool_notice']='Asignación actualizada.';
        header('Location: '.school_page_url($base,$context),true,303);exit;
    }
    $occupancy=school_pool_occupancy($courseId,$date);
    $visibleClasses=array();$classMap=array();
    foreach($data['classes'] as $class){
        $classMap[(int)$class['id_aula']]=$class;
        if($centre&&(int)$class['id_centro']!==$centre)continue;
        $matches=$search===''||mb_stripos(app_text($class['nombre'].' '.$class['nombre_centro']),$search,0,'UTF-8')!==false;
        if(!$matches)foreach($data['class_students'][(int)$class['id_aula']]??array() as $s)if(mb_stripos(school_page_name($s),$search,0,'UTF-8')!==false){$matches=true;break;}
        if($matches)$visibleClasses[(int)$class['id_aula']]=$class;
    }
    $rosters=$unique=array();
    foreach($data['students'] as $id=>$s)if(isset($visibleClasses[(int)$s['aula']])){
        $unique[$id]=$s;
        foreach($data['assignments'][$id]??array() as $link){$key=school_pool_key($link);if(isset($data['slot_map'][$key]))$rosters[$key][$id]=$s;}
    }
    $scheduled=count(array_intersect_key($unique,$data['assignments']));
} catch(DomainException $e){http_response_code(403);$error=$e->getMessage();}
catch(InvalidArgumentException $e){http_response_code(422);$error=$e->getMessage();}
catch(RuntimeException $e){http_response_code(503);$error=$e->getMessage();}
pool_header($title,'grupos');
?>
<link rel="stylesheet" href="/gestion/piscina/school-groups.css?v=20261013.1">
<p class="school-intro"><?= $kind===1?'Clases de mañana de las escuelas infantiles Lápices y sus horarios de piscina.':'Clases de otros colegios, como Peter Pan, Sant Pau y los centros externos configurados.'; ?></p>
<nav class="school-tabs" aria-label="Origen de los grupos">
<?php foreach(array(1=>'Escuelas Lápices',2=>'Colegios externos') as $tab=>$label){ ?><a href="<?= app_escape(school_page_url(school_pool_path($tab),array('curso'=>$course['id_curso']??0,'fecha'=>$date??date('Y-m-d')))); ?>" <?= $kind===$tab?'aria-current="page"':''; ?>><?= $label; ?></a><?php } ?>
</nav>
<?php if($error){ ?><div class="card school-error" role="alert"><h2>No se ha completado la operación</h2><p><?= app_escape($error); ?></p><a class="primary" href="<?= $base; ?>">Volver al listado</a></div><?php pool_footer();return;} ?>
<?php if(!empty($_SESSION['school_pool_notice'])){ ?><p class="notice" role="status"><?= app_escape($_SESSION['school_pool_notice']); ?></p><?php unset($_SESSION['school_pool_notice']);} ?>
<form class="filters school-filters" method="get" action="<?= $base; ?>">
 <label>Curso de piscina<select name="curso"><?php foreach($courses as $c){ ?><option value="<?= (int)$c['id_curso']; ?>" <?= (int)$c['id_curso']===$courseId?'selected':''; ?>><?= app_escape($c['nombre']); ?></option><?php } ?></select></label>
 <label>Fecha de consulta<input name="fecha" type="date" value="<?= $date; ?>" required></label>
 <label><?= $kind===1?'Escuela':'Colegio'; ?><select name="centro"><option value="0">Todos los centros</option><?php foreach($data['centres'] as $c){ ?><option value="<?= (int)$c['id_centro']; ?>" <?= (int)$c['id_centro']===$centre?'selected':''; ?>><?= app_escape($c['nombre_centro']); ?></option><?php } ?></select></label>
 <label>Buscar clase o alumno<input type="search" name="q" value="<?= app_escape($search); ?>" placeholder="Nombre o clase" maxlength="150"></label>
 <input type="hidden" name="vista" value="<?= $view; ?>"><button class="primary" type="submit">Aplicar filtros</button><a class="quiet" href="<?= $base; ?>">Curso actual</a>
</form>
<p class="school-context">Curso de escuelas: <strong><?= $data['school_course']?app_escape(trim($data['school_course']['nombre'])):'Sin curso equivalente'; ?></strong> · Alumnos matriculados a <?= date('d/m/Y',strtotime($date)); ?>.</p>
<?php if($dateAdjusted){ ?><p class="notice">La fecha se ha ajustado al inicio del curso seleccionado.</p><?php } ?>
<?php if($selected){ ?>
 <a class="quiet" href="<?= app_escape(school_page_url($base,$context,array('aula'=>0,'vista'=>'clases'))); ?>">← Volver a las clases</a>
 <section class="card school-detail"><span class="eyebrow"><?= app_escape($selected['nombre_centro']); ?></span><h2><?= app_escape($selected['nombre']); ?></h2>
 <p><?= count($data['class_students'][$classId]??array()); ?> alumnos matriculados en esta fecha.</p>
 <ul class="school-students">
 <?php foreach($data['class_students'][$classId]??array() as $student){$id=(int)$student['id_alumno']; ?>
  <li><strong><?= app_escape(school_page_name($student)); ?></strong><div class="school-student-slots">
   <?php foreach($data['assignments'][$id]??array() as $assignment){$slot=$data['slot_map'][school_pool_key($assignment)]??null;if(!$slot)continue; ?>
    <form method="post" class="school-remove"><?php school_page_fields('remove'); ?><input type="hidden" name="assignment" value="<?= (int)$assignment['id_alumno_grupo']; ?>"><span><?= app_escape(school_page_slot($slot)); ?></span><button class="quiet" type="submit" aria-label="<?= app_escape('Quitar '.school_page_slot($slot).' de '.school_page_name($student)); ?>">Quitar horario</button></form>
   <?php } if(empty($data['assignments'][$id])){ ?><span class="school-muted">Sin horario de piscina asignado</span><?php } ?>
  </div></li>
 <?php } ?>
 </ul>
 <?php if(!empty($data['class_students'][$classId])){ ?>
 <details class="school-assign"><summary>Asignar o cambiar horario</summary>
 <form method="post" class="filters"><?php school_page_fields('assign'); ?>
  <label>Alumno<select name="student" required><option value="">Selecciona un alumno</option><?php foreach($data['class_students'][$classId] as $student){ ?><option value="<?= (int)$student['id_alumno']; ?>"><?= app_escape(school_page_name($student)); ?></option><?php } ?></select></label>
  <label>Horario de piscina<select name="slot" required><option value="">Selecciona un horario</option><?php foreach($data['slots'] as $slot){if(!school_pool_slot_allowed($slot,$kind))continue;$free=max(0,(int)$slot['capacidad']-($occupancy[school_pool_key($slot)]??0)); ?><option value="<?= (int)$slot['id_grupo_subgrupo']; ?>" <?= (int)$slot['bloqueado']?'disabled':''; ?>><?= app_escape(school_page_slot($slot).' · '.$free.' plazas libres'.($slot['bloqueado']?' · Bloqueado':'')); ?></option><?php } ?></select></label>
  <button class="primary" type="submit">Guardar asignación</button>
 </form><p class="school-muted">Cambiar de subgrupo en la misma franja sustituye la asignación anterior. Para cambiar de día, quita primero el horario que ya no corresponda.</p>
 </details><?php } ?>
 </section>
<?php } else { ?>
 <div class="stats"><article class="card"><span>Clases</span><strong><?= count($visibleClasses); ?></strong></article><article class="card"><span>Alumnos matriculados</span><strong><?= count($unique); ?></strong></article><article class="card"><span>Con horario de piscina</span><strong><?= $scheduled; ?></strong></article></div>
 <nav class="school-tabs" aria-label="Vista de grupos"><a href="<?= app_escape(school_page_url($base,$context,array('vista'=>'clases'))); ?>" <?= $view==='clases'?'aria-current="page"':''; ?>>Clases del centro</a><a href="<?= app_escape(school_page_url($base,$context,array('vista'=>'horarios'))); ?>" <?= $view==='horarios'?'aria-current="page"':''; ?>>Horarios asignados</a></nav>
 <?php if($view==='clases'){ ?>
 <div class="groups-grid school-classes">
 <?php foreach($visibleClasses as $class){$students=$data['class_students'][(int)$class['id_aula']]??array();$assigned=0;foreach($students as $s)if(isset($data['assignments'][(int)$s['id_alumno']]))$assigned++; ?>
 <article class="card school-class"><span class="eyebrow"><?= app_escape($class['nombre_centro']); ?></span><h2><?= app_escape($class['nombre']); ?></h2><p><?= count($students); ?> alumnos · <?= $assigned; ?> con horario</p><a class="primary" href="<?= app_escape(school_page_url($base,$context,array('aula'=>(int)$class['id_aula']))); ?>">Ver alumnos y horarios →</a></article>
 <?php } ?>
 </div>
 <?php if(!$visibleClasses){ ?><div class="card school-empty"><h2>No hay clases para esta selección</h2><p><?= $kind===2?'Este curso no tiene clases de colegios externos registradas que coincidan con los filtros. Puedes consultar cursos anteriores con el selector.':'No hay clases de escuelas registradas que coincidan con los filtros.'; ?></p></div><?php } ?>
 <?php } else { ?>
 <div class="groups-grid">
 <?php foreach($data['slots'] as $slot){$key=school_pool_key($slot);if(empty($rosters[$key]))continue; ?>
 <article class="card school-class"><span class="eyebrow"><?= $days[(int)$slot['dia']]??''; ?></span><h2><?= app_escape(substr($slot['hora_inicio'],0,5).'–'.substr($slot['hora_fin'],0,5)); ?></h2><p>Grupo <?= (int)$slot['id_subgrupo']+1; ?> · <?= count($rosters[$key]); ?> alumnos de <?= $kind===1?'escuelas Lápices':'colegios externos'; ?></p><details><summary>Ver alumnos y clases</summary><ul class="school-schedule-roster"><?php foreach($rosters[$key] as $s){$class=$classMap[(int)$s['aula']]; ?><li><strong><?= app_escape(school_page_name($s)); ?></strong><a href="<?= app_escape(school_page_url($base,$context,array('aula'=>(int)$s['aula'],'vista'=>'clases'))); ?>"><?= app_escape($class['nombre_centro'].' · '.$class['nombre']); ?></a></li><?php } ?></ul></details></article>
 <?php } ?>
 </div><?php if(!$rosters){ ?><div class="card school-empty"><h2>Todavía no hay horarios asignados</h2><p>Abre una clase para asignar sus alumnos a un horario de piscina.</p><a class="primary" href="<?= app_escape(school_page_url($base,$context,array('vista'=>'clases'))); ?>">Ver clases</a></div><?php } ?>
 <?php } ?>
<?php } ?>
<?php pool_footer(); ?>
