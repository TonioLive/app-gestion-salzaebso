<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_alumno=$_GET["id_usuario"];
$id_padre=$_GET["id_padre"];

// Borra servicios_contratados
db_query("DELETE FROM padres WHERE id_padre=$id_padre") or die(db_error()."-2");

header("Location: nuevo_alumno2.php?u=$id_alumno");
?>
