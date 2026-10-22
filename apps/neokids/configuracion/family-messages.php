<?php
/** One scope and unread definition for the staff inbox, counters and actions. */
function fm_can_access() { return app_can('zona_mensajes') || ((int)($GLOBALS['nivel_acceso']??0)===2 && app_can('zona_aulas')); }
function fm_manager() { return in_array((int)($GLOBALS['nivel_acceso']??0),[3,4],true); }
function fm_child_scope($column, &$types, &$values) {
    $where=[];
    if ((int)($GLOBALS['nivel_acceso']??0)!==4 && empty($_SESSION['usuario_multicentro'])) {
        $where[]='EXISTS(SELECT 1 FROM alumnos fc WHERE fc.id_usuario='.$column.' AND fc.id_centro=?)';
        $types.='i';$values[]=(int)($_SESSION['id_centro_usuario']??0);
    }
    if (!fm_manager()) {
        $where[]="EXISTS(SELECT 1 FROM alumnos fa JOIN aulas_alumnos_grupos fg ON fg.id_alumno=fa.id_alumno JOIN aulas_grupos_monitores fm ON fm.id_grupo=fg.id_grupo AND fm.id_subgrupo=fg.id_subgrupo JOIN cursos_academicos fc ON fc.id_curso=fa.id_curso WHERE fa.id_usuario=$column AND fm.id_usuario=? AND fc.fecha_inicio<=CURDATE() AND fc.fecha_fin>=CURDATE() AND (fa.fecha_baja='0000-00-00' OR fa.fecha_baja IS NULL OR fa.fecha_baja>=CURDATE()))";
        $types.='i';$values[]=(int)($GLOBALS['idUsuario']??0);
    }
    return $where?implode(' AND ',$where):'1=1';
}
function fm_scope(&$types,&$values) {
    return fm_child_scope('n.id_alumno',$types,$values).(fm_manager()?'':' AND COALESCE(n.destino,0)=0');
}
function fm_unread_sql() {
    return "((COALESCE(n.tipo_nota,0)<>2 AND COALESCE(n.estado,0)=0) OR EXISTS(SELECT 1 FROM agenda_notas_padres_respuestas fr WHERE fr.id_nota=n.id_nota_padre AND COALESCE(fr.direccion,0)=0 AND COALESCE(fr.leida,0)=0))";
}
function fm_unread_count() {
    if(!fm_can_access())return 0;
    $types='';$values=[];$scope=fm_scope($types,$values);
    $rows=app_rows('SELECT COUNT(*) AS total FROM agenda_notas_padres n WHERE '.$scope.' AND '.fm_unread_sql(),$types,$values);
    return (int)($rows[0]['total']??0);
}
function fm_message($id) {
    $types='i';$values=[(int)$id];$scope=fm_scope($types,$values);
    $rows=app_rows('SELECT n.*,CONCAT_WS(" ",u.Nombre,u.Apellido1,u.Apellido2) AS alumno,CONCAT_WS(" ",s.Nombre,s.Apellido1,s.Apellido2) AS remitente,'.fm_unread_sql().' AS pendiente FROM agenda_notas_padres n LEFT JOIN usuarios u ON u.id_usuario=n.id_alumno LEFT JOIN usuarios s ON s.id_usuario=n.id_remitente WHERE n.id_nota_padre=? AND '.$scope,$types,$values);
    if(!$rows){http_response_code(403);exit('No tienes acceso a esta conversación.');}
    return $rows[0];
}
function fm_child($id) {
    $types='i';$values=[(int)$id];$scope=fm_child_scope('u.id_usuario',$types,$values);
    $rows=app_rows('SELECT u.id_usuario,CONCAT_WS(" ",u.Nombre,u.Apellido1,u.Apellido2) AS alumno FROM usuarios u WHERE u.id_usuario=? AND EXISTS(SELECT 1 FROM padres fp WHERE fp.padre_de=u.id_usuario) AND '.$scope,$types,$values);
    if(!$rows){http_response_code(403);exit('No tienes acceso a esta familia.');}return $rows[0];
}
function fm_plain($text) {
    // Historic messages may contain rich HTML; new entries are escaped on write.
    $text=app_text($text);
    $text=preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is','',$text);
    $text=preg_replace('~<br\s*/?>|</(?:p|div|li)>~i',"\n",$text);
    return trim(html_entity_decode(strip_tags($text),ENT_QUOTES|ENT_HTML5,'UTF-8'));
}
function fm_date($date,$time='') {return $date && $date!=='0000-00-00'?date('d/m/Y',strtotime($date)).($time?' · '.substr($time,0,5):''):'';}
function fm_mark_read($id) {
    // Reading a reply from the family must never mark a staff reply as read by them.
    $a=db_query_params('UPDATE agenda_notas_padres SET estado=1 WHERE id_nota_padre=? AND COALESCE(tipo_nota,0)<>2 AND COALESCE(estado,0)=0','i',[(int)$id],app_db());
    $b=db_query_params('UPDATE agenda_notas_padres_respuestas SET leida=1 WHERE id_nota=? AND COALESCE(direccion,0)=0','i',[(int)$id],app_db());
    return $a&&$b;
}
