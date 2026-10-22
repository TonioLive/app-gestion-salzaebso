<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$nueva_act=$_POST["nueva_act"];
$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha=$_POST["fecha"];
$nombre_mostrar=$_POST["nombre_mostrar"];

	// Introduce nuevo objetivo
	$inserta_actividad=db_query_params('INSERT INTO agenda_programacion_actividades_piscina (id_objetivo, descripcion_actividad) VALUES (?,?)', 'ss', array((string)($_POST["objetivo_nueva"]), (string)($_POST["actividad_nueva"])));
	
	header("Location: editar_objetivos.php?mensaje_error=Actividad añadida&fecha=$fecha&id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");


?>
<script>
	window.close();
</script>