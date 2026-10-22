<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

/*
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
        $valor_modificado=mysql_real_escape_string($valor_modificado);
        }

$cambia_linea=mysql_query("UPDATE $tabla SET $campo_modificado='$valor_modificado' WHERE $nombre_id=$fila_modificada") or die(mysql_error());
*/

$recargar=$_POST["recargar"];
if (strlen($recargar)!=0) {
        $texto_largo=$_POST["texto_largo"];
        $fila_modificada=$_POST["fila"];
        $campo_modificado=$_POST["campo"];
        $valor_modificado=$_POST["valor"];
        $tabla=$_POST["tabla"];
        $nombre_id=$_POST["nombre_id"];
        $borrar_actividades=$_POST["borrar_actividades"];
        } else {
        $recargar=$_GET["recargar"];
        $texto_largo=$_GET["texto_largo"];
        $fila_modificada=$_GET["fila"];
        $campo_modificado=$_GET["campo"];
        $valor_modificado=$_GET["valor"];
        $tabla=$_GET["tabla"];
        $nombre_id=$_GET["nombre_id"];
        $borrar_actividades=$_GET["borrar_actividades"];
        }

$texto_extra="";
if ($borrar_actividades==1) {
        $texto_extra=", id_actividad=0, actividad=''";
        }


if (($texto_largo==1) and (strlen($valor_modificado)!=0)) {
        $valor_modificado=db_real_escape_string($valor_modificado);
        }

$array_tablas_fechas=array("agenda_programacion_evaluaciones_comedor_trimestrales","agenda_programacion_evaluaciones_trimestrales","agenda_programacion_ingles_evaluaciones_trimestrales");
$campos_log="";
if (in_array($tabla,$array_tablas_fechas)) {
		$log_modificacion=date("Y-m-d H:i:s").",".$nombre_completo.",".$valor_modificado.";";
        $campos_log=", fecha_modificacion='".date("Y-m-d H:i:s")."', modificado_por='".$nombre_completo."', log=CONCAT(log,'".$log_modificacion."')";
}

$cambia_linea=db_query("UPDATE $tabla SET $campo_modificado='$valor_modificado'".$texto_extra.$campos_log." WHERE $nombre_id=$fila_modificada") or die(db_error());

if ($recargar!=0) {

        $id_aula=$_GET["id_aula"];
        $fecha_inicio=$_GET["fecha_inicio"];

          $pagina_activa=$_GET["pagina_activa"];

        if ($campo_modificado=="area") {
                $focuson="objetivo_".$fila_modificada;
        }

        header("Location: semana_programacion.php?focuson=".$focuson."&pagina_activa=$pagina_activa&id_aula=$id_aula&fecha_inicio=".date("Y-m-d",$fecha_inicio));

        }

?>
