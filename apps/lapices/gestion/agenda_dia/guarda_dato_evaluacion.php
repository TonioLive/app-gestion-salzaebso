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
		$log_modificacion=date("Y-m-d H:i:s").",".$nombre_completo.",".$valor_modificado.";";
        $campos_log=", fecha_modificacion='".date("Y-m-d H:i:s")."', modificado_por='".$nombre_completo."', log=CONCAT(log,'".$log_modificacion."')";
}

/* NO TESTEADO. DESACTIVADO PARA EVITAR POSIBLES PROBLEMAS, ACTIVAR CUANDO ESTÉ PROBADO
if (($campo_modificado=='observaciones') and ($tabla='agenda_programacion_evaluaciones_estado_alumnos')) {

	// Busca valor actual
	$busca_valor=mysql_query("SELECT $campo_modificado FROM $tabla WHERE $nombre_id=$fila_modificada");
	
	if (mysql_num_rows($busca_valor)!=0) {
		
		$datos_registro=mysql_fetch_array($busca_valor);
		
		$valor_actual=$datos_registro[$campo_modificado];

		if ($valor_actual!=$valor_modificado) {
			$log_modificacion=date("Y-m-d H:i:s").",".$nombre_completo.",".$valor_actual.";";
	        $campos_log=", fecha_modificacion='".date("Y-m-d H:i:s")."', modificado_por='".$nombre_completo."', log=CONCAT(log,'".$log_modificacion."')";
	    }		
		
	}	
}
*/

$cambia_linea=db_query("UPDATE $tabla SET $campo_modificado='$valor_modificado'".$campos_log." WHERE $nombre_id=$fila_modificada") or die(db_error());

?>
