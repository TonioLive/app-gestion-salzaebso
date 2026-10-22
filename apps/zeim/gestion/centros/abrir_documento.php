<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/downloadfile.php");

$archivo_descarga=$_GET["archivo_descarga"];
$carpeta_descarga=$_GET["carpeta_descarga"];

$dFile = new Download_file($carpeta_descarga , $archivo_descarga);
?>
