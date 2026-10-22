<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_padre.php");
require("../../configuracion/downloadfile.php");

$archivo_descarga=$_GET["archivo_descarga"];
$carpeta_descarga=$_GET["carpeta_descarga"];

// Marca leido

$marca_leido=db_query("INSERT INTO agenda_documentos_padres (id_usuario,nombre_documento,fecha_lectura,hora_lectura) VALUES ($id_usuario,'$carpeta_descarga/$archivo_descarga','".date("Y-m-d")."','".date("H:i:s")."');");

$dFile = new Download_file($carpeta_descarga , $archivo_descarga);
?>
