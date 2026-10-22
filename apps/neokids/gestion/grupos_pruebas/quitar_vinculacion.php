<?php
require __DIR__.'/programaciones-common.php';
prog_post();$id=prog_id($_POST,'id_plantilla');$link=prog_id($_POST,'id_vinculacion');
prog_atomic(function()use($id,$link){prog_template($id,true);prog_query('DELETE FROM agenda_programacion_grupos_vinculados WHERE id_vinculacion=? AND id_plantilla=?','ii',[$link,$id]);});
prog_redirect('gestion_plantillas.php?id_plantilla='.$id.'&done=saved');
