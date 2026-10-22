<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_curso_actual=$_GET["id_curso_alumno"];
$id_usuario_seleccionado=$_GET["id_alumno"];
$subcarpeta_actual=$_GET["sub"];

$archivo_descarga=$_GET["archivo_descarga"];
$carpeta_descarga=$_GET["carpeta_descarga"];

require_once __DIR__.'/../../configuracion/safe-files.php';
unlink(security_download_path($carpeta_descarga,$archivo_descarga));

header("Location: ver_carpeta_alumno.php?id_alumno=$id_usuario_seleccionado&id_curso_alumno=$id_curso_actual&sub=$subcarpeta_actual");
?>
