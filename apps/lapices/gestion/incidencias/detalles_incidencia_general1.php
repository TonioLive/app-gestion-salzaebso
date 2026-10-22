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
$medidas_adoptadas1=$_POST["medidas_adoptadas1"];		
if (strlen($medidas_adoptadas1)!=0) {
        $medidas_adoptadas1=urlencode($medidas_adoptadas1);
        }
		
$resuelta=$_POST["resuelta"];

$accion_eficaz=$_POST["accion_eficaz"];

$analisis_causas=db_real_escape_string($_POST["analisis_causas"]);
$medidas_resultado_negativo=db_real_escape_string($_POST["medidas_resultado_negativo"]);
$responsable_implantacion=db_real_escape_string($_POST["responsable_implantacion"]);
$responsable_cierre=db_real_escape_string($_POST["responsable_cierre"]);
$plazo_previsto=db_real_escape_string($_POST["plazo_previsto"]);


$fecha_incidencia=$_POST["fecha_incidencia"];

if (strlen($fecha_incidencia)!=0) {
    
    $partes_fecha=explode( '/', $fecha_incidencia);
    
    $fecha_incidencia = $partes_fecha[2]."-".$partes_fecha[1]."-".$partes_fecha[0];
    
}

$fecha_cierre=$_POST["fecha_cierre"];

if (strlen($fecha_cierre)!=0) {
    
    $partes_fecha=explode( '/', $fecha_cierre);
    
    $fecha_cierre = $partes_fecha[2]."-".$partes_fecha[1]."-".$partes_fecha[0];
    
}

db_query("UPDATE incidencias_2017 SET medidas_adoptadas1='$medidas_adoptadas1',tipo_medida='$tipo_medida', tipo='$tipo', nombre_incidencia='$nombre_incidencia', descripcion='$descripcion', medidas_adoptadas='$medidas_adoptadas', resuelta=$resuelta, seguimiento_por='$seguimiento_por', accion_eficaz='$accion_eficaz', analisis_causas='$analisis_causas', medidas_resultado_negativo='$medidas_resultado_negativo', responsable_implantacion='$responsable_implantacion', responsable_cierre='$responsable_cierre', plazo_previsto='$plazo_previsto', fecha_incidencia='$fecha_incidencia', fecha_cierre='$fecha_cierre' WHERE id_incidencia=$id_incidencia") or die(db_error());

header("Location: incidencias_general.php?c=".$resuelta);
?>
