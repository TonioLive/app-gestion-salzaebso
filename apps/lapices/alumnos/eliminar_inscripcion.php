<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_alumno=$_GET["id_alumno"];

// Datos de la inscripcion

$consulta_inscripcion=db_query("SELECT * FROM alumnos WHERE id_alumno=$id_alumno") or die(db_error()."-1");

$datos_inscripcion=db_fetch_array($consulta_inscripcion);

// Borra servicios


// Borra servicios_contratados
db_query("DELETE FROM servicios_contratados WHERE id_alumno=$id_alumno") or die(db_error()."-2");

// Borra servicios asistencia
db_query("DELETE FROM servicios_consumidos WHERE id_alumno=".$datos_inscripcion["id_usuario"]." AND id_servicio=".$datos_inscripcion["aula"]) or die(db_error()."-3");

// Borra inscripcion
db_query("DELETE FROM alumnos WHERE id_alumno=$id_alumno") or die(db_error()."-4");

?>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
