<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_alumno=$_GET["id_alumno"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$origen=$_GET["origen"];
$id_falta=$_GET["id_falta"];

$inserta_en_grupo=db_query_params('DELETE FROM servicios_consumidos_faltas WHERE id_falta=?', 's', array($id_falta)) or die(db_error());

header("Location: $origen");        
?>
