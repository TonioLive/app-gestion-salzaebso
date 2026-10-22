<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos


$id_actividad=$_POST["id_actividad"];
$id_alumno=$_POST["id_alumno"];

$inicio=$_POST["fecha"];
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
$busca_si_esta=db_query("SELECT * FROM servicios_consumidos_asistencia WHERE id_alumno=$id_alumno AND id_servicio=$id_actividad AND fecha='$fecha'");
if (db_num_rows($busca_si_esta)==0) {
        db_query("INSERT INTO servicios_consumidos_asistencia (id_alumno,id_servicio,fecha,reserva) VALUES ('$id_alumno','$id_actividad','$fecha','0')") or die(db_error());
        } else {
        $datos_registro=db_fetch_array($busca_si_esta);
        $id_registro=$datos_registro["id_servicio_consumido"];
        db_query("UPDATE servicios_consumidos_asistencia SET reserva='0' WHERE id_servicio_consumido=$id_registro") or dei(db_error());
        }
?>
