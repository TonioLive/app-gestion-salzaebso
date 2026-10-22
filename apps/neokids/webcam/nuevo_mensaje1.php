<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");


$id_remitente=$_POST["id_remitente"];
$destinatario=$_POST["destinatario"];
$asunto=$_POST["asunto"];
$texto_mensaje=$_POST["texto_mensaje"];
$medicamento=$_POST["medicamento"];
$cantidad=$_POST["cantidad"];

$tipo_destino=substr($destinatario,0,1);
$id_alumno=substr($destinatario,1,(strlen($destinatario)-1));

if ($tipo_destino=="C") {
	$destino=1;
	} else {
	$destino=0;
	}

if ($id_remitente==$id_usuario) {

	$asunto=db_real_escape_string($asunto);
	$texto_mensaje=db_real_escape_string($texto_mensaje);
	if (strlen($medicamento)!=0) {
		$tipo_mensaje=1;
		$medicamento=db_real_escape_string($medicamento);
		$cantidad=db_real_escape_string($cantidad);
		
		$fecha_inicio_dia=$_POST["fecha_inicio_dia"];
                        $fecha_inicio_mes=$_POST["fecha_inicio_mes"];
                        $fecha_inicio_ano=$_POST["fecha_inicio_ano"];
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                }
                        $fecha_fin_dia=$_POST["fecha_fin_dia"];
                        $fecha_fin_mes=$_POST["fecha_fin_mes"];
                        $fecha_fin_ano=$_POST["fecha_fin_ano"];
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                }

                if ($fecha_inicio_dia!="--" and $fecha_inicio_mes!="--" and $fecha_inicio_ano!="----") {
                        $fecha_medicacion=$fecha_inicio_ano."-".$fecha_inicio_mes."-".$fecha_inicio_dia;
                        if (!checkdate(intval($fecha_inicio_mes),intval($fecha_inicio_dia),intval($fecha_inicio_ano))) {
                                $fecha_medicacion="";
                                }
                        } else {
                        $fecha_medicacion="";
                        }
                if ($fecha_fin_dia!="--" and $fecha_fin_mes!="--" and $fecha_fin_ano!="----") {
                        $fecha_fin_medicacion=$fecha_fin_ano."-".$fecha_fin_mes."-".$fecha_fin_dia;
                        if (!checkdate(intval($fecha_fin_mes),intval($fecha_fin_dia),intval($fecha_fin_ano))) {
                                $fecha_fin_medicacion="";
                                }
                        } else {
                        $fecha_fin_medicacion="";
                        }
		
		$hora=$_POST["hora"];
		$min=$_POST["min"];
	
		$hora_medicacion=$hora.":".$min.":00";
		
		} else {
		$tipo_mensaje=0;
		}
			
	$inserta_mensaje=db_query("INSERT INTO agenda_notas_padres (id_padre,id_alumno,id_remitente,tipo_nota,asunto,texto_nota,fecha,hora,fecha_medicacion,hora_medicacion,fecha_fin_medicacion,medicamento,cantidad,destino) VALUES ('$id_remitente','$id_alumno','$id_remitente','$tipo_mensaje','$asunto','$texto_mensaje','".date("Y-m-d")."','".date("H:i:s")."','$fecha_medicacion','$hora_medicacion','$fecha_fin_medicacion','$medicamento','$cantidad','$destino');") or die(db_error());

	}

header("Location: mensajeria.php");

?>