<?php
require_once __DIR__.'/config.php';
function pool_manager(){return !empty($GLOBALS['permitido'])&&((int)$GLOBALS['nivel_acceso']===4||((int)$GLOBALS['nivel_acceso']===3&&app_can('zona_aulas')));}
function pool_staff_groups(){return pool_manager()||(!empty($GLOBALS['permitido'])&&count(app_rows('SELECT id_monitor_grupo FROM aulas_grupos_monitores WHERE id_usuario=? LIMIT 1','i',array((int)$GLOBALS['idUsuario'])))>0);}
function pool_group($group,$sub){
 $rows=app_rows('SELECT g.*,s.id_grupo_subgrupo,s.capacidad,s.bloqueado,t.nombre_tipo_grupo,c.fecha_inicio AS curso_inicio,c.fecha_fin AS curso_fin FROM aulas_grupos g JOIN aulas_grupos_subgrupos s ON s.id_grupo=g.id_grupo JOIN cursos_academicos c ON c.id_curso=g.id_curso LEFT JOIN aulas_grupos_tipos t ON t.id_tipo_grupo=s.tipo_subgrupo WHERE g.id_grupo=? AND s.id_subgrupo=?','ii',array($group,$sub));
 if(!$rows||!pool_staff_groups()) {http_response_code(403);exit('No tienes acceso a este grupo.');}
 if(!pool_manager()&&!app_rows('SELECT id_monitor_grupo FROM aulas_grupos_monitores WHERE id_grupo=? AND id_subgrupo=? AND id_usuario=?','iii',array($group,$sub,(int)$GLOBALS['idUsuario']))) {http_response_code(403);exit('No tienes acceso a este grupo.');}
 return $rows[0];
}
function pool_valid_date($date){$d=DateTime::createFromFormat('!Y-m-d',$date);return $d&&$d->format('Y-m-d')===$date;}
function pool_period($time){return $time<'12:00:00'?'manana':($time<'16:00:00'?'mediodia':'tarde');}
function pool_history($kind,$record,$before){return db_query_params('INSERT INTO piscina_historial (tipo,registro,autor,contenido,creado) VALUES (?,?,?,?,NOW())','siis',array($kind,$record,(int)$GLOBALS['idUsuario'],json_encode($before)),app_db());}
