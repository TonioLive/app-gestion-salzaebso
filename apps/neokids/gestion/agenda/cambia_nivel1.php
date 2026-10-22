<?php 
require("../../configuracion/config.php");

$id_alumno=$_POST["id_alumno"];
if (strlen($id_alumno)==0) {
	$id_alumno=$_GET["id_alumno"];
	}
$nuevo_nivel=$_POST["nuevo_nivel"];
if (strlen($id_alumno)==0) {
	$nuevo_nivel=$_GET["nuevo_nivel"];
	}
$externo=$_POST["externo"];
if (strlen($id_alumno)==0) {
	$externo=$_GET["externo"];
	}
	
$bbdd_usuarios="";
if ($externo==1) {
        $bbdd_usuarios="gestionguarderias.";
        }


$actualiza_nivel=db_query("UPDATE ".$bbdd_usuarios."alumnos SET nivel_proximo1=$nuevo_nivel WHERE id_alumno=$id_alumno");   
?>
