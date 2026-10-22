<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_alumno=$_GET["id_alumno"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$id_falta=$_GET["id_falta"];

$inserta_falta=db_query_params('INSERT servicios_consumidos_faltas (id_grupo_falta,id_subgrupo_falta,id_alumno,fecha) VALUES (?,?,?,?)', 'ssss', array($id_grupo, $id_subgrupo, $id_alumno, (string)(date("Y-m-d", $fecha))));

$origen="/aulas/control_asistencia.php?dia=".$fecha;

header("Location: $origen");        
?>
