<?php
require __DIR__.'/programaciones-common.php';prog_post();
$id=prog_id($_POST,'id_plantilla');$session=prog_id($_POST,'sesion_editada');$template=prog_template($id);
$parent=prog_id($_POST,'objetivo_nivel');$level=isset($_POST['nivel'])?prog_id($_POST,'nivel',true):(int)$template['nivel'];$text=trim(app_value($_POST,'objetivo_nuevo'));
if($level>3||$text===''||mb_strlen($text,'UTF-8')>255)prog_error('Revisa el nivel y el nombre del objetivo.');
if(!app_rows('SELECT id_objetivo FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=? AND id_objetivo_g=0','i',[$parent]))prog_error('Selecciona un objetivo general válido.');
prog_query('INSERT INTO agenda_programacion_objetivos_piscina(nivel,id_objetivo_g,nombre_objetivo) VALUES(?,?,?)','iis',[$level,$parent,$text]);prog_library_return($id,$session);
