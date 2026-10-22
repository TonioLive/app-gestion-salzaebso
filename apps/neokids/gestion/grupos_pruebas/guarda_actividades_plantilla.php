<?php
require __DIR__.'/programaciones-common.php';prog_post();
$id=prog_id($_POST,'id_plantilla');$session=prog_id($_POST,'sesion_editada');prog_template($id);
[$objective,$item,$record]=prog_library_target(app_value($_POST,'id_actividad'));$text=trim(app_value($_POST,'texto_editado'));
if($text===''||mb_strlen($text,'UTF-8')>($objective?255:5000))prog_error('Escribe una descripción válida.');
prog_query($objective?'UPDATE agenda_programacion_objetivos_piscina SET nombre_objetivo=? WHERE id_objetivo=?':'UPDATE agenda_programacion_actividades_piscina SET descripcion_actividad=? WHERE id_actividad=?','si',[$text,$item]);
prog_library_return($id,$session);
