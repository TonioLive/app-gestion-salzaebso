<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_POST["id_registro"];

$hora_salida=date("H:i:s");

if (strlen($id_registro)!=0) { 	
	db_query_params('UPDATE servicios_consumidos_guarderias SET hora_salida=? WHERE id_servicio_consumido=?', 'ss', array((string)($hora_salida), $id_registro)) or dei(db_error());
	} else {
	
	$id_alumno=$_POST["id_alumno"];
	
	$id_aula=$_POST["id_aula"];
	if (strlen($id_aula)==0) {
        $id_aula=$_GET["id_aula"];
        }
	$turno=$_POST["turno"];
	if (strlen($turno)==0) {
        $turno=$_GET["turno"];
        }
	$id_alumno=$_POST["id_alumno"];
	if (strlen($id_alumno)==0) {
        $id_alumno=$_GET["id_alumno"];
        }

	$inicio=$_POST["fecha"];
	if (strlen($inicio)==0) {
        $inicio=$_GET["fecha"];
        }
        
	if (strlen($inicio)==0) {	
		$fecha_time_stamp=time(); 
		} else {
	    $fecha_time_stamp=$inicio;
		}

	$mes=date("n",$fecha_time_stamp);
	$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
	$inicio_mes_time_stamp=strtotime($inicio_mes);
	$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
	$diasemana=date("w",$fecha_time_stamp);
	$dianombre=$dia_sem_largo[$diasemana];
	$mesnombre=$nombre_mes[$mes];

	$fecha=date("Y/m/d",$fecha_time_stamp);
	
	db_query("UPDATE servicios_consumidos_guarderias SET hora_salida='$hora_salida' WHERE id_alumno=$id_alumno AND id_servicio=$id_aula AND turno=$turno AND fecha='$fecha'") or dei(db_error());
	
	}

?>