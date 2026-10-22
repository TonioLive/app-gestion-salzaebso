<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$archivo_descarga=$_GET["archivo_descarga"];
$carpeta_descarga=$_GET["carpeta_descarga"];

require_once __DIR__.'/../../configuracion/safe-files.php';
unlink(security_download_path($carpeta_descarga,$archivo_descarga));

header("Location: ver_carpeta_aula.php?id_aula=$id_aula");
?>