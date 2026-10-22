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
		// Busca si hay actividades
		$busca_si_act=db_query("SELECT * FROM agenda_programacion_actividades_piscina WHERE id_objetivo=".substr($id_actividad,1,strlen($id_actividad)-1)) or die(db_error());	
		if (db_num_rows($busca_si_act)==0) {
			$elimina_o=db_query("DELETE FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=".substr($id_actividad,1,strlen($id_actividad)-1)) or die(db_error());
			$mensaje_error="Objetivo eliminado.";
		} else {
			$mensaje_error="No se pueden eliminar objetivos que tengan actividades asociadas.";		
		}

		} else {
		// Actividad
		
		// Busca si está programada
		$busca_si_esta=db_query_params('SELECT * FROM agenda_programacion WHERE id_actividad=?', 's', array($id_actividad));	
		
		if (db_num_rows($busca_si_esta)==0) {
			$elimina_a=db_query_params('DELETE FROM agenda_programacion_actividades_piscina WHERE id_actividad=?', 's', array($id_actividad)) or die(db_error());
			$mensaje_error="Actividad eliminada.";
			} else {
			$mensaje_error="No se pueden eliminar actividades que estén programadas.";
			}	
		
		}
	
	}

	header("Location: editar_objetivos.php?mensaje_error=$mensaje_error&fecha=$fecha&id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");


?>