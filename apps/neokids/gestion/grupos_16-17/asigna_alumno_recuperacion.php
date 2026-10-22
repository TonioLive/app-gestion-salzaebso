<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_alumno=$_GET["id_alumno"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$origen=$_GET["origen"];
$id_falta=$_GET["id_falta"];

$inserta_en_grupo=db_query_params('UPDATE servicios_consumidos_faltas SET id_grupo=?, id_subgrupo=?, fecha_recuperacion=? WHERE id_falta=?', 'ssss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha)), $id_falta));

header("Location: $origen");        
?>
