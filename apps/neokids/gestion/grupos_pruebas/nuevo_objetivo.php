<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$nueva_act=$_POST["nueva_act"];
$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha=$_POST["fecha"];
$nombre_mostrar=$_POST["nombre_mostrar"];

	// Busca nivel del objetivo padre
	$busca_objetivos=db_query_params('SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo=?','i',array((int)app_value($_POST,'objetivo_nivel')));
                
    $datos_objetivo=db_fetch_array($busca_objetivo);

	// Introduce nuevo objetivo
	$inserta_objetivo=db_query_params('INSERT INTO agenda_programacion_objetivos_piscina (nivel,id_objetivo_g, nombre_objetivo) VALUES (?,?,?)', 'sss', array((string)($datos_objetivo["nivel"]), (string)($_POST["objetivo_nivel"]), (string)($_POST["objetivo_nuevo"])));
	
	header("Location: editar_objetivos.php?mensaje_error=Objetivo añadido&fecha=$fecha&id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");


?>
<script>
	window.close();
</script>