<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_alumno=$_GET["id_alumno"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$origen=$_GET["origen"];
$id_falta=$_GET["id_falta"];

$actualiza_falta=db_query_params('UPDATE servicios_consumidos_faltas SET id_grupo=0, id_subgrupo=0, fecha_recuperacion=null WHERE id_falta=?', 's', array($id_falta));

header("Location: $origen");        
?>
