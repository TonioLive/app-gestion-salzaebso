<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos


$nombre_campo=$_GET["nombre_campo"];
$valor_campo=$_GET["valor_campo"];

if (!$nombre_campo) {
	
	$nombre_campo=$_POST["nombre_campo"];
	$valor_campo=$_POST["valor_campo"];
	
}

if (!$valor_campo) {
	
	$valor_campo="NULL";
	
} else {
	
	$valor_campo="'$valor_campo'";
}

if ($nombre_campo) {

	$datos_campo=explode("-",$nombre_campo);
	
	$campo=$datos_campo[0];	
	$id_asistencia=$datos_campo[1];	
	
}
	
$cambia_dato=db_query("UPDATE asistencia_docentes SET $campo=$valor_campo WHERE id_asistencia=$id_asistencia") or die(db_error());

echo("ok");

?>