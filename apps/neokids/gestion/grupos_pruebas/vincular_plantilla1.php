<?php
require __DIR__.'/programaciones-common.php';
prog_post();$id=prog_id($_POST,'id_plantilla');$template=prog_template($id);
if(!preg_match('/^([1-9]\d{0,8})_([1-9]\d{0,2})$/D',app_value($_POST,'grupo'),$parts))prog_error('Selecciona un grupo válido.');
$group=(int)$parts[1];$sub=(int)$parts[2];$date=app_value($_POST,'fecha_inicio');$parsed=DateTimeImmutable::createFromFormat('!Y-m-d',$date);
if(!$parsed||$parsed->format('Y-m-d')!==$date)prog_error('Selecciona una fecha válida.');
$rows=app_rows('SELECT g.*,c.fecha_inicio AS curso_inicio,c.fecha_fin AS curso_fin,s.bloqueado FROM aulas_grupos g JOIN aulas_grupos_subgrupos s ON s.id_grupo=g.id_grupo AND s.id_subgrupo=? JOIN cursos_academicos c ON c.id_curso=g.id_curso WHERE g.id_grupo=? AND s.id_subgrupo<g.num_subgrupos','ii',[$sub-1,$group]);
if(!$rows||!empty($rows[0]['bloqueado']))prog_error('El grupo no está disponible.');$g=$rows[0];
if($date<$g['curso_inicio']||$date>$g['curso_fin']||(int)$parsed->format('N')!==(int)$g['dia'])prog_error('La fecha debe estar dentro del curso y coincidir con el día de clase de ese grupo.');
if((int)$template['num_sesiones']<1)prog_error('Prepara el número de sesiones antes de asignar la programación.');
prog_atomic(function()use($id,$group,$sub,$date){
 $template=prog_template($id,true);
 $end=function($start,$sessions,$period){$d=new DateTimeImmutable($start);return $period===1?$d->modify('first day of this month')->modify('+'.max(0,$sessions-1).' months')->modify('last day of this month')->format('Y-m-d'):$d->modify('+'.max(0,$sessions*7-1).' days')->format('Y-m-d');};
 $finish=$end($date,(int)$template['num_sesiones'],(int)$template['periodicidad']);
 $existing=prog_query('SELECT v.fecha_inicio,p.num_sesiones,p.periodicidad FROM agenda_programacion_grupos_vinculados v JOIN agenda_programacion_plantillas p ON p.id_plantilla=v.id_plantilla WHERE v.id_grupo=? AND v.id_subgrupo=? FOR UPDATE','ii',[$group,$sub]);
 while($row=$existing->fetch_assoc()){if($row['fecha_inicio']&&$row['fecha_inicio']!=='0000-00-00'&&$row['fecha_inicio']<=$finish&&$end($row['fecha_inicio'],(int)$row['num_sesiones'],(int)$row['periodicidad'])>=$date)prog_error('El grupo ya tiene una programación en parte de ese periodo. Revisa sus asignaciones o elige otra fecha.');}
 prog_query('INSERT INTO agenda_programacion_grupos_vinculados(id_plantilla,id_grupo,id_subgrupo,fecha_inicio) VALUES(?,?,?,?)','iiis',[$id,$group,$sub,$date]);
});
prog_redirect('gestion_plantillas.php?id_plantilla='.$id.'&done=saved');
