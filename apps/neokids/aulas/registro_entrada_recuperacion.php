<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos

$id_falta=$_POST["id_falta"];
if (strlen($id_falta)==0) {
        $id_falta=$_GET["id_falta"];
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

$hora=time();

$fecha=date("Y/m/d",$fecha_time_stamp);
$hora_entrada=date("H:i:s",$hora);

// Comprueba si ya ha sido introucido
$actualiza=db_query_params('UPDATE servicios_consumidos_faltas SET hora_entrada=? WHERE id_falta=?', 'ss', array((string)($hora_entrada), $id_falta));

?>
