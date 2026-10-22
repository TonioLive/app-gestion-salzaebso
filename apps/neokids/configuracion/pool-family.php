<?php
// Callers must authenticate the family and authorise the child before using these helpers.
function pool_family_schedule($child,$date=null){
 $date=$date?:date('Y-m-d');
 return app_rows("SELECT DISTINCT a.id_alumno,a.id_usuario,a.id_centro,a.fecha_matricula,a.fecha_baja,g.id_grupo,ag.id_subgrupo,g.dia,g.hora_inicio,g.hora_fin,g.id_curso,c.fecha_inicio AS curso_inicio,c.fecha_fin AS curso_fin,s.bloqueado,t.nombre_tipo_grupo,
 (SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(' ',u.Nombre,u.Apellido1) ORDER BY gm.num_monitor SEPARATOR ', ') FROM aulas_grupos_monitores gm JOIN usuarios u ON u.id_usuario=gm.id_usuario WHERE gm.id_grupo=g.id_grupo AND gm.id_subgrupo=ag.id_subgrupo) AS monitores
 FROM aulas_alumnos_grupos ag JOIN alumnos a ON a.id_alumno=ag.id_alumno AND a.id_curso=ag.id_curso JOIN cursos_academicos c ON c.id_curso=a.id_curso JOIN aulas_grupos g ON g.id_grupo=ag.id_grupo AND g.id_curso=a.id_curso JOIN aulas_grupos_subgrupos s ON s.id_grupo=g.id_grupo AND s.id_subgrupo=ag.id_subgrupo LEFT JOIN aulas_grupos_tipos t ON t.id_tipo_grupo=s.tipo_subgrupo WHERE a.id_usuario=? AND c.fecha_inicio<=? AND c.fecha_fin>=? AND (a.fecha_matricula='0000-00-00' OR a.fecha_matricula IS NULL OR a.fecha_matricula<=?) AND (a.fecha_baja='0000-00-00' OR a.fecha_baja IS NULL OR a.fecha_baja>=?) ORDER BY g.dia,g.hora_inicio",'issss',array($child,$date,$date,$date,$date));
}
function pool_holidays($s){return app_rows('SELECT DISTINCT dia FROM calendario_laboral WHERE estado=1 AND (id_curso=0 OR id_curso=?) AND (id_centro=0 OR id_centro=?) AND dia>=? AND dia<=? ORDER BY dia','iiss',array((int)$s['id_curso'],(int)$s['id_centro'],$s['curso_inicio'],$s['curso_fin']));}
function pool_class_cancelled($s,$date){
 if(!empty($s['bloqueado']))return true;
 if(app_rows('SELECT id_dia FROM calendario_laboral WHERE estado=1 AND dia=? AND (id_curso=0 OR id_curso=?) AND (id_centro=0 OR id_centro=?) LIMIT 1','sii',array($date,(int)$s['id_curso'],(int)$s['id_centro'])))return true;
 return count(app_rows('SELECT id_falta FROM servicios_consumidos_faltas WHERE id_alumno=? AND fecha=? AND id_grupo_falta=? AND id_subgrupo_falta=? LIMIT 1','isii',array((int)$s['id_alumno'],$date,(int)$s['id_grupo'],(int)$s['id_subgrupo'])))>0;
}
function pool_ics_escape($s){return str_replace(array('\\',"\r\n","\n","\r",';',','),array('\\\\','\\n','\\n','\\n','\\;','\\,'),(string)$s);}
function pool_ics($s,$name){
 $tz=new DateTimeZone('Europe/Madrid');$first=new DateTime(max(date('Y-m-d'),$s['curso_inicio'],$s['fecha_matricula']),$tz);$first->modify('+'.(((int)$s['dia']-(int)$first->format('N')+7)%7).' days');
 $end=$s['curso_fin'];if($s['fecha_baja']&&$s['fecha_baja']!=='0000-00-00')$end=min($end,$s['fecha_baja']);if($first->format('Y-m-d')>$end)return '';
 $until=new DateTime($end.' 23:59:59',$tz);$until->setTimezone(new DateTimeZone('UTC'));$dow=array(1=>'MO',2=>'TU',3=>'WE',4=>'TH',5=>'FR',6=>'SA',7=>'SU');
 $lines=array('BEGIN:VCALENDAR','VERSION:2.0','PRODID:-//Neo Kids//Clases//ES','CALSCALE:GREGORIAN','METHOD:PUBLISH','BEGIN:VTIMEZONE','TZID:Europe/Madrid','BEGIN:DAYLIGHT','TZOFFSETFROM:+0100','TZOFFSETTO:+0200','TZNAME:CEST','DTSTART:19700329T020000','RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU','END:DAYLIGHT','BEGIN:STANDARD','TZOFFSETFROM:+0200','TZOFFSETTO:+0100','TZNAME:CET','DTSTART:19701025T030000','RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU','END:STANDARD','END:VTIMEZONE','BEGIN:VEVENT','UID:clase-'.$s['id_alumno'].'-'.$s['id_grupo'].'-'.$s['id_subgrupo'].'@neo-kids.es','DTSTAMP:'.gmdate('Ymd\THis\Z'),'DTSTART;TZID=Europe/Madrid:'.$first->format('Ymd').'T'.str_replace(':','',$s['hora_inicio']),'DTEND;TZID=Europe/Madrid:'.$first->format('Ymd').'T'.str_replace(':','',$s['hora_fin']),'RRULE:FREQ=WEEKLY;BYDAY='.$dow[(int)$s['dia']].';UNTIL='.$until->format('Ymd\THis\Z'),'SUMMARY:'.pool_ics_escape('Natación Neo Kids · '.$name),'DESCRIPTION:'.pool_ics_escape('Monitor/a: '.($s['monitores']?:'Pendiente de asignación')."\nHorario habitual. Consulta la app para cambios y recuperaciones."),'LOCATION:Neo Kids');
 foreach(pool_holidays($s) as $h)$lines[]='EXDATE;TZID=Europe/Madrid:'.str_replace('-','',$h['dia']).'T'.str_replace(':','',$s['hora_inicio']);
 foreach(app_rows('SELECT fecha FROM servicios_consumidos_faltas WHERE id_alumno=? AND id_grupo_falta=? AND id_subgrupo_falta=? AND fecha>=? AND fecha<=?','iiiss',array((int)$s['id_alumno'],(int)$s['id_grupo'],(int)$s['id_subgrupo'],$first->format('Y-m-d'),$end)) as $h)$lines[]='EXDATE;TZID=Europe/Madrid:'.str_replace('-','',$h['fecha']).'T'.str_replace(':','',$s['hora_inicio']);
 $lines=array_merge($lines,array('BEGIN:VALARM','TRIGGER:-PT1H','ACTION:DISPLAY','DESCRIPTION:Tu clase de natación empieza en una hora','END:VALARM','END:VEVENT','END:VCALENDAR'));
 $out=array();foreach($lines as $line){while(strlen($line)>73){$cut=73;while($cut>0&&(ord($line[$cut])&0xc0)===0x80)$cut--; $out[]=substr($line,0,$cut);$line=' '.substr($line,$cut);} $out[]=$line;}return implode("\r\n",$out)."\r\n";
}
