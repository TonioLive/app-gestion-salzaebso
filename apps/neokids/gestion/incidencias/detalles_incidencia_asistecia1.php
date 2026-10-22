<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];
$observaciones=$_POST["observaciones"];
$medidas_adoptadas=$_POST["medidas_adoptadas"];
$resuelta=$_POST["resuelta"];

$hora_e=$_POST["hora_e"];
$min_e=$_POST["min_e"];
$seg_e=$_POST["seg_e"];
$hora_s=$_POST["hora_s"];
$min_s=$_POST["min_s"];
$seg_s=$_POST["seg_s"];

db_query("UPDATE servicios_consumidos SET hora_entrada='".$hora_e.":".$min_e.":".$seg_e."',hora_salida='".$hora_s.":".$min_s.":".$seg_s."',observaciones='$observaciones', medidas_adoptadas='$medidas_adoptadas', resuelta=$resuelta WHERE id_servicio_consumido=$id_registro") or die(db_error());

header("Location: gestion_incidencias.php?c=".$resuelta);
?>
