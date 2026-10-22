<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$nueva_act=$_POST["nueva_act"];
$id_plantilla=$_POST["id_plantilla"];
$sesion_editada=$_POST["sesion_editada"];	

	// Introduce nuevo objetivo
	$inserta_objetivo=db_query("INSERT INTO agenda_programacion_objetivos_piscina (edad, nombre_objetivo, version) VALUES ('".$datos_objetivo["nivel"]."','".$_POST["objetivo_nuevo"]."','".$version_actual_prog_ingles."')");
	
	header("Location: editar_objetivos_plantillas.php?mensaje_error=Objetivo añadido&id_plantilla=$id_plantilla&sesion_editada=$sesion_editada");


?>
<script>
	window.close();
</script>