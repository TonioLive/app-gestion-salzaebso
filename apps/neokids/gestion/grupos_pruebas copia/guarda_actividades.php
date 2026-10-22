<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha=$_POST["fecha"];
$programacion=$_POST["duallistbox_ActividadesProgramadas"];
$nombre_mostrar=$_POST["nombre_mostrar"];


$id_actividad=$_POST["id_actividad"];
if ($id_actividad) {

	if (substr($id_actividad,0,1)=="O") {
		// Objetivo
		$modifica_o=db_query("UPDATE agenda_programacion_objetivos_piscina SET nombre_objetivo='".$_POST["texto_editado"]."' WHERE id_objetivo=".substr($id_actividad,1,strlen($id_actividad)-1)) or die(db_error());
		} else {
		// Actividad
		$modifica_a=db_query_params('UPDATE agenda_programacion_actividades_piscina SET descripcion_actividad=? WHERE id_actividad=?', 'ss', array((string)($_POST["texto_editado"]), $id_actividad)) or die(db_error());
		}
	
	}
		
	header("Location: editar_objetivos.php?fecha=$fecha&id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");


?>