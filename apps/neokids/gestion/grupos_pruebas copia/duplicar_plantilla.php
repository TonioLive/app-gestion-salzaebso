<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_plantilla=$_GET["id_plantilla"];

$copia_programaciones=db_query_params('INSERT INTO agenda_programacion_plantillas (descripcion,nivel,num_sesiones,creada_por) SELECT descripcion,nivel,num_sesiones,creada_por FROM agenda_programacion_plantillas WHERE id_plantilla=?', 's', array($id_plantilla));

$nueva_plantilla=db_insert_id();

$cambia_nombre=db_query("UPDATE agenda_programacion_plantillas SET descripcion=CONCAT(descripcion,' -COPIA-'),creada_por=$idUsuario WHERE id_plantilla=$nueva_plantilla");

$copia_actividades=db_query_params('INSERT INTO agenda_programacion_plantillas_actividades (id_plantilla,numero_sesion,id_actividad) SELECT ?,numero_sesion,id_actividad FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=?', 'ss', array((string)($nueva_plantilla), $id_plantilla));	

header("Location: gestion_programaciones.php");

?>