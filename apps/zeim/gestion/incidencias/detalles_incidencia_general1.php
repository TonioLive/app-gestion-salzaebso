<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$id_incidencia=$_POST["id_incidencia"];
$tipo=$_POST["tipo"];
$nombre_incidencia=$_POST["nombre_incidencia"];
if (strlen($nombre_incidencia)!=0) {
        $nombre_incidencia=strtoupper($nombre_incidencia);
        $nombre_incidencia=urlencode($nombre_incidencia);
        }
$tipo_medida=$_POST["tipo_medida"];
if (strlen($tipo_medida)!=0) {
        $tipo_medida=strtoupper($tipo_medida);
        $tipo_medida=urlencode($tipo_medida);
        }
$descripcion=urlencode($_POST["descripcion"]);
$medidas_adoptadas=$_POST["medidas_adoptadas"];
$seguimiento_por=$_POST["seguimiento_por"];
if (strlen($medidas_adoptadas)!=0) {
        $medidas_adoptadas=urlencode($medidas_adoptadas);
        }
$resuelta=$_POST["resuelta"];

db_query("UPDATE incidencias SET tipo_medida='$tipo_medida', tipo='$tipo', nombre_incidencia='$nombre_incidencia', descripcion='$descripcion', medidas_adoptadas='$medidas_adoptadas', resuelta=$resuelta, seguimiento_por='$seguimiento_por' WHERE id_incidencia=$id_incidencia") or die(db_error());

header("Location: incidencias_general.php?c=".$resuelta);
?>
