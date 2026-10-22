<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos


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
        
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];


$hora=time();

$fecha=date("Y/m/d",$fecha_time_stamp);
$hora_entrada=date("H:i:s",$hora);

// Comprueba si ya ha sido introucido
$busca_si_esta=db_query("SELECT * FROM servicios_consumidos_guarderias WHERE id_alumno=$id_alumno AND id_servicio=$id_aula AND turno=$turno AND fecha='$fecha'");
if (db_num_rows($busca_si_esta)==0) {
        db_query_params('INSERT INTO servicios_consumidos_guarderias (id_alumno,id_servicio,turno,fecha,hora_entrada) VALUES (?,?,?,?,?)', 'sssss', array((string)($id_alumno), (string)($id_aula), (string)($turno), (string)($fecha), (string)($hora_entrada))) or die(db_error());
        }


?>
