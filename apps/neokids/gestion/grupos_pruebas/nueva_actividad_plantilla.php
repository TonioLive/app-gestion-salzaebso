<?php
require __DIR__.'/programaciones-common.php';prog_post();
$id=prog_id($_POST,'id_plantilla');$session=prog_id($_POST,'sesion_editada');prog_template($id);
$objective=prog_id($_POST,'objetivo_nueva');$text=trim(app_value($_POST,'actividad_nueva'));
if($text===''||mb_strlen($text,'UTF-8')>5000)prog_error('Escribe una descripción de hasta 5.000 caracteres.');
if(!app_rows('SELECT id_objetivo FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=? AND id_objetivo_g<>0','i',[$objective]))prog_error('Selecciona un objetivo válido.');
prog_query('INSERT INTO agenda_programacion_actividades_piscina(id_objetivo,descripcion_actividad) VALUES(?,?)','is',[$objective,$text]);prog_library_return($id,$session);
