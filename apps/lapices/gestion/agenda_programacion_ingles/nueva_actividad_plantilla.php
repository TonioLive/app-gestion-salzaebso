<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$nueva_act=$_POST["nueva_act"];
$id_plantilla=$_POST["id_plantilla"];
$sesion_editada=$_POST["sesion_editada"];

	// Introduce nuevo objetivo
	$inserta_actividad=db_query("INSERT INTO agenda_programacion_actividades_piscina (id_objetivo, descripcion_actividad) VALUES ('".$_POST["objetivo_nueva"]."','".$_POST["actividad_nueva"]."', '".$version_actual_prog_ingles."')");

	header("Location: editar_objetivos_plantillas.php?mensaje_error=Actividad añadida&id_plantilla=$id_plantilla&sesion_editada=$sesion_editada");

?>
<script>
	window.close();
</script>