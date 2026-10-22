<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$texto_largo=$_POST["texto_largo"];
$fila_modificada=$_POST["fila"];
$campo_modificado=$_POST["campo"];
$valor_modificado=$_POST["valor"];

if ($valor_modificado=="true") {
        $valor_modificado=1;
        }
if ($valor_modificado=="false") {
        $valor_modificado=0;
        }

$tabla=$_POST["tabla"];
$nombre_id=$_POST["nombre_id"];

if (($texto_largo==1) and (strlen($valor_modificado)!=0)) {
        $valor_modificado=db_real_escape_string($valor_modificado);
        }
        
$array_tablas_fechas=array("agenda_programacion_evaluaciones_comedor_trimestrales","agenda_programacion_evaluaciones_trimestrales","agenda_programacion_ingles_evaluaciones_trimestrales");
$campos_log="";
if (in_array($tabla,$array_tablas_fechas)) {
        $campos_log=", fecha_modificacion='".date("Y-m-d H:i:s")."', modificado_por='".$nombre_completo."'";
}

$cambia_linea=db_query("UPDATE $tabla SET $campo_modificado='$valor_modificado'".$campos_log." WHERE $nombre_id=$fila_modificada") or die(db_error());


?>
