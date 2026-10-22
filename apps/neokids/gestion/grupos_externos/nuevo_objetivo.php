<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$nueva_act=$_POST["nueva_act"];
$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha=$_POST["fecha"];
$nombre_mostrar=$_POST["nombre_mostrar"];

	// Introduce nuevo objetivo
	$inserta_objetivo=db_query_params('INSERT INTO agenda_programacion_objetivos_piscina (nivel, nombre_objetivo) VALUES (?,?)', 'ss', array((string)($_POST["objetivo_nivel"]), (string)($_POST["objetivo_nuevo"])));
	
	header("Location: editar_objetivos.php?mensaje_error=Objetivo añadido&fecha=$fecha&id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");


?>
<script>
	window.close();
</script>