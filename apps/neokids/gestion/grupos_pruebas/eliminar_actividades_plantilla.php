<?php
require __DIR__.'/programaciones-common.php';prog_post();
$id=prog_id($_POST,'id_plantilla');$session=prog_id($_POST,'sesion_editada');prog_template($id);
[$objective,$item,$record]=prog_library_target(app_value($_POST,'id_actividad'));
prog_atomic(function()use($objective,$item){
 if($objective){
  prog_query('SELECT id_objetivo FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=? FOR UPDATE','i',[$item]);
  if(app_rows('SELECT id_actividad FROM agenda_programacion_actividades_piscina WHERE id_objetivo=? LIMIT 1','i',[$item])||app_rows('SELECT id_objetivo FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=? LIMIT 1','i',[$item]))prog_error('Este objetivo tiene actividades u objetivos asociados y no se puede eliminar.');
  prog_query('DELETE FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=? AND id_objetivo_g<>0','i',[$item]);
 }else{
  prog_query('SELECT id_actividad FROM agenda_programacion_actividades_piscina WHERE id_actividad=? FOR UPDATE','i',[$item]);
  if(app_rows('SELECT id_prog FROM agenda_programacion_plantillas_actividades WHERE id_actividad=? LIMIT 1','i',[$item])||app_rows('SELECT id_actividad FROM agenda_programacion WHERE id_actividad=? LIMIT 1','i',[$item]))prog_error('Esta actividad ya se utiliza en una programación y no se puede eliminar.');
  prog_query('DELETE FROM agenda_programacion_actividades_piscina WHERE id_actividad=?','i',[$item]);
 }
});
prog_library_return($id,$session);
