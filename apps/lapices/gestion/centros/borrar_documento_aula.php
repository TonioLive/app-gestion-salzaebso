<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$archivo_descarga=$_GET["archivo_descarga"];
$carpeta_descarga=$_GET["carpeta_descarga"];

unlink($carpeta_descarga."/".$archivo_descarga);

header("Location: ver_carpeta_aula.php?id_aula=$id_aula");
?>