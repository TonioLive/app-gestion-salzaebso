<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_curso=$_GET["id_curso"];
$fecha=$_GET["fecha"];
$nombre_campo=$_GET["nombre_campo"];

$array_fecha=explode("/",$fecha);

$fecha_mysql=$array_fecha[2]."/".$array_fecha[1]."/".$array_fecha[0];

$cambia_linea=db_query("UPDATE cursos_academicos SET $nombre_campo='$fecha_mysql' WHERE id_curso=$id_curso") or die(db_error());

echo("ok");
?>