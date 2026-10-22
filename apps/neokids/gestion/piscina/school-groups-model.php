<?php
// The school registry and Neo Kids now run on separate database servers.
function school_pool_kind($kind) {
    if (!in_array((int)$kind,array(1,2),true)) throw new InvalidArgumentException('Selecciona escuelas o colegios externos.');
    return (int)$kind;
}
function school_pool_path($kind) {return school_pool_kind($kind)===1?'/gestion/grupos_lapices_nuevo/':'/gestion/grupos_externos/';}
function school_pool_db() {
    if (isset($GLOBALS['school_pool_connection'])) return $GLOBALS['school_pool_connection'];
    $path=dirname(dirname(__DIR__)).'/.private/schools-database.php';
    if (!is_file($path)) throw new RuntimeException('No está configurada la conexión con las escuelas.');
    $settings=require $path;
    list($host,$port,$socket)=db_parse_server($settings['sql_host']);
    $db=mysqli_init();$db->options(MYSQLI_OPT_CONNECT_TIMEOUT,5);
    if (!@$db->real_connect($host,$settings['sql_usuario'],$settings['sql_pass'],$settings['sql_db'],$port?:3306,$socket)
        || !$db->set_charset('utf8mb4')) throw new RuntimeException('No se puede consultar el registro de escuelas en este momento.');
    $GLOBALS['school_pool_connection']=$db;
    return $db;
}
function school_pool_rows($sql,$types='',$values=array(),$schools=false) {
    $db=$schools?school_pool_db():app_db();
    $result=$types===''?db_query($sql,$db):db_query_params($sql,$types,$values,$db);
    if (!($result instanceof mysqli_result)) throw new RuntimeException('No se han podido consultar los grupos. Inténtalo de nuevo.');
    $rows=array();while($row=$result->fetch_assoc())$rows[]=$row;$result->free();return $rows;
}
function school_pool_match_course($course,$schoolCourses) {
    $best=null;$bestOverlap=0;
    foreach($schoolCourses as $candidate) {
        $overlap=min(strtotime($course['fecha_fin']),strtotime($candidate['fecha_fin']))-max(strtotime($course['fecha_inicio']),strtotime($candidate['fecha_inicio']))+86400;
        if($overlap>$bestOverlap){$best=$candidate;$bestOverlap=$overlap;}
    }
    return $best;
}
function school_pool_active_sql($alias) {
    return "($alias.fecha_matricula IS NULL OR $alias.fecha_matricula='0000-00-00' OR $alias.fecha_matricula<=?) AND ($alias.fecha_baja IS NULL OR $alias.fecha_baja='0000-00-00' OR $alias.fecha_baja>=?)";
}
function school_pool_slots($course) {
    return school_pool_rows('SELECT g.id_grupo,g.id_curso,g.dia,g.hora_inicio,g.hora_fin,s.id_grupo_subgrupo,s.id_subgrupo,s.capacidad,s.tipo_subgrupo,s.bloqueado FROM aulas_grupos g JOIN aulas_grupos_subgrupos s ON s.id_grupo=g.id_grupo WHERE g.id_curso=? ORDER BY g.dia,g.hora_inicio,s.id_subgrupo','i',array($course));
}
function school_pool_key($row) {return (int)$row['id_grupo'].'-'.(int)$row['id_subgrupo'];}
function school_pool_slot_allowed($slot,$kind) {
    return in_array((int)$slot['tipo_subgrupo'],array(0,1),true)&&((int)$kind!==1||$slot['hora_inicio']<'14:00:00');
}
function school_pool_data($kind,$course,$date) {
    school_pool_kind($kind);
    $schoolCourses=school_pool_rows('SELECT id_curso,nombre,fecha_inicio,fecha_fin FROM cursos_academicos ORDER BY fecha_inicio DESC','',array(),true);
    $schoolCourse=school_pool_match_course($course,$schoolCourses);
    $centres=school_pool_rows('SELECT id_centro,nombre_centro FROM centros WHERE piscina=? ORDER BY nombre_centro','i',array($kind),true);
    $classes=$students=array();
    if($schoolCourse) {
        $classes=school_pool_rows('SELECT ca.id_aula,ca.id_centro,ca.nombre,c.nombre_centro FROM centro_aulas ca JOIN centros c ON c.id_centro=ca.id_centro WHERE ca.id_curso=? AND ca.tipo=0 AND c.piscina=? ORDER BY c.nombre_centro,ca.edad_minima,ca.nombre','ii',array($schoolCourse['id_curso'],$kind),true);
        $students=school_pool_rows('SELECT a.id_alumno,a.aula,a.id_centro,u.Nombre,u.Apellido1,u.Apellido2 FROM alumnos a JOIN usuarios u ON u.id_usuario=a.id_usuario JOIN centro_aulas ca ON ca.id_aula=a.aula AND ca.id_centro=a.id_centro JOIN centros c ON c.id_centro=ca.id_centro WHERE a.id_curso=? AND ca.id_curso=? AND ca.tipo=0 AND c.piscina=? AND '.school_pool_active_sql('a').' ORDER BY u.Apellido1,u.Apellido2,u.Nombre','iiiss',array($schoolCourse['id_curso'],$schoolCourse['id_curso'],$kind,$date,$date),true);
    }
    $assignments=school_pool_rows('SELECT ag.id_alumno_grupo,ag.id_alumno,ag.id_grupo,ag.id_subgrupo FROM aulas_alumnos_lapices_grupos ag JOIN aulas_grupos g ON g.id_grupo=ag.id_grupo AND g.id_curso=ag.id_curso WHERE ag.id_curso=?','i',array($course['id_curso']));
    $studentMap=$classStudents=$links=array();
    foreach($students as $student){$studentMap[(int)$student['id_alumno']]=$student;$classStudents[(int)$student['aula']][]=$student;}
    foreach($assignments as $assignment)if(isset($studentMap[(int)$assignment['id_alumno']]))$links[(int)$assignment['id_alumno']][]=$assignment;
    $slots=school_pool_slots((int)$course['id_curso']);$slotMap=array();foreach($slots as $slot)$slotMap[school_pool_key($slot)]=$slot;
    return array('school_course'=>$schoolCourse,'centres'=>$centres,'classes'=>$classes,'students'=>$studentMap,'class_students'=>$classStudents,'assignments'=>$links,'slots'=>$slots,'slot_map'=>$slotMap);
}
function school_pool_occupancy($course,$date) {
    $regular=school_pool_rows('SELECT ag.id_grupo,ag.id_subgrupo,COUNT(DISTINCT ag.id_alumno) AS total FROM aulas_alumnos_grupos ag JOIN alumnos a ON a.id_alumno=ag.id_alumno AND a.id_curso=ag.id_curso WHERE ag.id_curso=? AND '.school_pool_active_sql('a').' GROUP BY ag.id_grupo,ag.id_subgrupo','iss',array($course,$date,$date));
    $counts=array();foreach($regular as $row)$counts[school_pool_key($row)]=(int)$row['total'];
    $linked=school_pool_rows('SELECT id_alumno,id_grupo,id_subgrupo FROM aulas_alumnos_lapices_grupos WHERE id_curso=?','i',array($course));
    $ids=array();foreach($linked as $row)$ids[(int)$row['id_alumno']]=(int)$row['id_alumno'];
    $active=array();
    foreach(array_chunk(array_values($ids),500) as $batch) {
        $rows=school_pool_rows('SELECT a.id_alumno FROM alumnos a WHERE a.id_alumno IN ('.implode(',',array_fill(0,count($batch),'?')).') AND '.school_pool_active_sql('a'),str_repeat('i',count($batch)).'ss',array_merge($batch,array($date,$date)),true);
        foreach($rows as $row)$active[(int)$row['id_alumno']]=true;
    }
    $seen=array();foreach($linked as $row){$key=school_pool_key($row);$identity=$key.':'.$row['id_alumno'];if(isset($active[(int)$row['id_alumno']])&&!isset($seen[$identity])){$counts[$key]=($counts[$key]??0)+1;$seen[$identity]=true;}}
    return $counts;
}
function school_pool_assign($kind,$course,$date,$studentId,$slotId) {
    if(!pool_manager())throw new DomainException('No tienes permiso para gestionar estos grupos.');
    $data=school_pool_data($kind,$course,$date);
    if(!isset($data['students'][$studentId]))throw new DomainException('El alumno no pertenece a este apartado o no está matriculado en la fecha seleccionada.');
    $slot=null;foreach($data['slots'] as $candidate)if((int)$candidate['id_grupo_subgrupo']===$slotId)$slot=$candidate;
    if(!$slot||!school_pool_slot_allowed($slot,$kind))throw new DomainException('Ese horario no corresponde a este curso o al tipo de clase.');
    if((int)$slot['bloqueado'])throw new InvalidArgumentException('El grupo está bloqueado. Elige otro horario.');
    $lockName='neokids-school-course-'.(int)$course['id_curso'];
    $lock=school_pool_rows('SELECT GET_LOCK(?,3) AS acquired','s',array($lockName));
    if(empty($lock[0]['acquired']))throw new RuntimeException('Hay otra asignación en curso. Inténtalo de nuevo.');
    try {
        $existing=school_pool_rows('SELECT ag.id_alumno_grupo,ag.id_grupo,ag.id_subgrupo,g.dia,g.hora_inicio,g.hora_fin FROM aulas_alumnos_lapices_grupos ag JOIN aulas_grupos g ON g.id_grupo=ag.id_grupo WHERE ag.id_curso=? AND ag.id_alumno=?','ii',array($course['id_curso'],$studentId));
        $same=array();foreach($existing as $row) {
            if((int)$row['id_grupo']===(int)$slot['id_grupo']){$same[]=$row;continue;}
            if((int)$row['dia']===(int)$slot['dia']&&$row['hora_inicio']<$slot['hora_fin']&&$row['hora_fin']>$slot['hora_inicio'])throw new InvalidArgumentException('El alumno ya tiene otro grupo a esa hora.');
        }
        foreach($same as $row)if((int)$row['id_subgrupo']===(int)$slot['id_subgrupo'])return;
        $counts=school_pool_occupancy((int)$course['id_curso'],$date);
        if(($counts[school_pool_key($slot)]??0)>=(int)$slot['capacidad'])throw new InvalidArgumentException('El grupo no tiene plazas libres.');
        if($same)$ok=db_query_params('UPDATE aulas_alumnos_lapices_grupos SET id_subgrupo=? WHERE id_curso=? AND id_alumno=? AND id_grupo=?','iiii',array($slot['id_subgrupo'],$course['id_curso'],$studentId,$slot['id_grupo']),app_db());
        else $ok=db_query_params('INSERT INTO aulas_alumnos_lapices_grupos (id_curso,id_alumno,id_grupo,id_subgrupo) VALUES (?,?,?,?)','iiii',array($course['id_curso'],$studentId,$slot['id_grupo'],$slot['id_subgrupo']),app_db());
        if(!$ok)throw new RuntimeException('No se ha podido guardar la asignación.');
    } finally {school_pool_rows('SELECT RELEASE_LOCK(?)','s',array($lockName));}
}
function school_pool_remove($kind,$course,$date,$assignmentId) {
    if(!pool_manager())throw new DomainException('No tienes permiso para gestionar estos grupos.');
    $data=school_pool_data($kind,$course,$date);$found=false;
    foreach($data['assignments'] as $links)foreach($links as $link)if((int)$link['id_alumno_grupo']===$assignmentId)$found=true;
    if(!$found)throw new DomainException('La asignación no pertenece a este apartado, curso y fecha.');
    if(!db_query_params('DELETE FROM aulas_alumnos_lapices_grupos WHERE id_alumno_grupo=? AND id_curso=?','ii',array($assignmentId,$course['id_curso']),app_db()))throw new RuntimeException('No se ha podido quitar la asignación.');
}
