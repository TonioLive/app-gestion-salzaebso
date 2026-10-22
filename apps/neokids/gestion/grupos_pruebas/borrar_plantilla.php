<?php
require __DIR__.'/programaciones-common.php';
prog_post();$id=prog_id($_POST,'id_plantilla');
prog_atomic(function()use($id){
 prog_template($id,true);
 if(app_rows('SELECT id_vinculacion FROM agenda_programacion_grupos_vinculados WHERE id_plantilla=? LIMIT 1','i',[$id]))prog_error('Esta programación está asignada a grupos. Desvincúlala antes de eliminarla.');
 prog_query('DELETE FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=?','i',[$id]);
 prog_query('DELETE FROM agenda_programacion_plantillas WHERE id_plantilla=?','i',[$id]);
});
prog_redirect('gestion_programaciones.php?done=deleted');
