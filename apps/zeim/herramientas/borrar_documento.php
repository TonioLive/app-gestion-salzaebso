<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$subcarpeta_actual=$_GET["sub"];

$archivo_descarga=$_GET["archivo_descarga"];
$carpeta_descarga=$_GET["carpeta_descarga"];

unlink($carpeta_descarga."/".$archivo_descarga);

header("Location: documentos.php?sub=$subcarpeta_actual");
?>
