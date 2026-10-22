<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_plantilla=$_GET["id_plantilla"];

$borra_programaciones=db_query("DELETE FROM agenda_programacion_plantillas WHERE id_plantilla=$id_plantilla");
$borra_actividades=db_query("DELETE FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla");	

header("Location: gestion_programaciones.php");

?>