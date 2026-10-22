<?php
require __DIR__.'/programaciones-common.php';
prog_post();$id=prog_id($_POST,'id_plantilla');
prog_atomic(function()use($id){
 $row=prog_template($id,true);$name=mb_substr((string)$row['descripcion'],0,247,'UTF-8').' · Copia';
 prog_query('INSERT INTO agenda_programacion_plantillas(descripcion,nivel,num_sesiones,periodicidad,creada_por,modif_por,fecha_ultima_mod,monitor) VALUES(?,?,?,?,?,?,CURDATE(),0)','siiiii',[$name,(int)$row['nivel'],(int)$row['num_sesiones'],(int)$row['periodicidad'],(int)$GLOBALS['idUsuario'],(int)$GLOBALS['idUsuario']]);
 $copy=(int)app_db()->insert_id;
 $columns='numero_sesion,tiempo_trabajo,area,id_actividad,actividad,metodologia,tiempo,materiales,lugar,evaluacion,orden_actividad,objetivo,edad,objetivos,turno,grupo,realizada,id_grupo,id_subgrupo';
 prog_query('INSERT INTO agenda_programacion_plantillas_actividades(id_plantilla,'.$columns.') SELECT ?,'.$columns.' FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=?','ii',[$copy,$id]);
});
prog_redirect('gestion_programaciones.php?done=copied');
