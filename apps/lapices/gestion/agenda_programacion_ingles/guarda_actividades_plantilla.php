<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_plantilla=$_POST["id_plantilla"];
$sesion_editada=$_POST["sesion_editada"];

$programacion=$_POST["duallistbox_ActividadesProgramadas"];

$id_actividad=$_POST["id_actividad"];
if ($id_actividad) {

	if (substr($id_actividad,0,1)=="O") {
		// Objetivo
		$modifica_o=db_query("UPDATE agenda_programacion_objetivos_piscina SET nombre_objetivo='".$_POST["texto_editado"]."' WHERE id_objetivo=".substr($id_actividad,1,strlen($id_actividad)-1)) or die(db_error());
		} else {
		// Actividad
		$modifica_a=db_query("UPDATE agenda_programacion_actividades_piscina SET descripcion_actividad='".$_POST["texto_editado"]."' WHERE id_actividad=".$id_actividad) or die(db_error());
		}
	
	}
		
	header("Location: editar_objetivos_plantillas.php?id_plantilla=$id_plantilla&sesion_editada=$sesion_editada");


?>