<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos


$id_actividad=$_GET["id_actividad"];
$id_alumno=$_GET["id_alumno"];

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

// Comprueba si ya ha sido introucido
$busca_si_esta=db_query("SELECT * FROM servicios_consumidos_asistencia WHERE id_alumno=$id_alumno AND id_servicio=$id_actividad AND fecha='$fecha'");
if (db_num_rows($busca_si_esta)==0) {
        db_query("INSERT INTO servicios_consumidos_asistencia (id_alumno,id_servicio,fecha,pagado) VALUES ('$id_alumno','$id_actividad','$fecha','0')") or die(db_error());
        } else {
        $datos_registro=db_fetch_array($busca_si_esta);
        $id_registro=$datos_registro["id_servicio_consumido"];
        db_query("UPDATE servicios_consumidos_asistencia SET pagado='0' WHERE id_servicio_consumido=$id_registro") or dei(db_error());
        }
        
// Borra el recibo
$borra_recibo=db_query("DELETE FROM pagos_a_cuenta WHERE id_asistencia=$id_registro");

// BUsca la factura
$busca_factura_emitida=db_query("SELECT * FROM facturas_emitidas WHERE id_asistencia=$id_registro");
if (db_num_rows($busca_factura_emitida)!=0) {
        $datos_factura_emitida=db_fetch_array($busca_factura_emitida);
        
        $id_factura_emitida=$datos_factura_emitida["id_factura"];
        
        // Borra detalles
        $borra_detalles=db_query("DELETE FROM detalles_factura WHERE id_factura=$id_factura_emitida");
        
        // Borra factura
        $borra_factura=db_query("DELETE FROM facturas_emitidas WHERE id_factura=$id_factura_emitida");
        }
        
?>
<script language="Javascript">
window.close();
self.opener.history.go(0);
</script>
