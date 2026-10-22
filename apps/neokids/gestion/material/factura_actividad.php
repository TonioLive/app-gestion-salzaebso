<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos


$id_actividad=$_GET["id_actividad"];
$id_alumno=$_GET["id_alumno"];
$periodo=$_GET["periodo"];

$inicio=$_GET["fecha"];
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

// Comprueba si hay reserva
$busca_reserva_servicios=db_query_params('SELECT * FROM reserva_servicios WHERE id_alumno=? AND id_servicio=?', 'ss', array($id_alumno, $id_actividad));

if (db_num_rows($busca_reserva_servicios)!=0) {
        $reserva_servicios=db_fetch_array($busca_reserva_servicios);
        $unidades_reservadas=$reserva_servicios["cantidad"];
        $id_reserva=$reserva_servicios["id_reserva"];
        $unidades_reservadas=$unidades_reservadas-1;
        if ($unidades_reservadas==0) {
                $borra_reserva=db_query_params('DELETE FROM reserva_servicios WHERE id_reserva=?', 's', array($id_reserva));
                } else {
                $actualiza_reserva=db_query("UPDATE reserva_servicios SET cantidad=$unidades_reservadas WHERE id_reserva=$id_reserva");
                }
        }

// Comprueba si ya ha sido introucido
$busca_si_esta=db_query_params('SELECT * FROM servicios_contratados WHERE id_alumno=? AND id_servicio=? AND fecha_inicio=? AND periodicidad=2', 'sss', array($id_alumno, $id_actividad, (string)($fecha))) or die(db_error());
if (db_num_rows($busca_si_esta)==0) {
        db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,periodo) VALUES (?,?,?,\'2\',\'1\',?)', 'ssss', array((string)($id_alumno), (string)($id_actividad), (string)($fecha), (string)($periodo))) or die(db_error());
        } else {
        // Suma la cantidad a la actual
        $datos_esta=db_fetch_array($busca_si_esta);
        db_query("UPDATE servicios_contratados SET cantidad=".($datos_esta["cantidad"]+1)." WHERE id_servicio_contratado=".$datos_esta["id_servicio_contratado"]);
        }
 
?>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
