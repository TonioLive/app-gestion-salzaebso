<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'") or die(db_error()."-1");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado") or die(db_error()."-2");
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro_curso_actual=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro_curso_actual="";
                                }


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

// Busca servicio contratado
$consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=$id_actividad";
$busca_servicio_contratado=db_query($consulta1);
$datos_albaranes=db_fetch_array($busca_servicio_contratado) or die(db_error()."-3");

// Inserta importe en movimientos caja

$cantidad_cobrada=$_GET["cantidad_cobrada"];
if (strlen($cantidad_cobrada)==0) {
        $cantidad_cobrada=$datos_albaranes["cantidad"];
        }

// Datos del alumno
$consulta_datos_alumno=db_query("SELECT alumnos.*, usuarios.*, alumnos.id_centro AS centro_alu FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario$filtro_curso_actual AND usuarios.id_usuario=".$id_alumno." ORDER BY id_curso DESC;") or die(db_error()."-4");
$datos_alumno=db_fetch_array($consulta_datos_alumno);

$nuevo_pago = db_query("INSERT INTO pagos_a_cuenta (id_factura, id_centro, id_centro_cobro, fecha_pago, importe_pago, forma_pago, observaciones_pago, comentario, hora_pago, signo, anotado_por, id_asistencia) VALUES ('0', '".$datos_alumno["centro_alu"]."', '".$datos_alumno["centro_alu"]."', '".date("Y-m-d")."', '".(round(($datos_albaranes["PRECIO_VENTA"]*$cantidad_cobrada),2))*round((1-($datos_albaranes["descuento"]/100)),2)."', '0', 'Cobrado desde entregas de material', '".$cantidad_cobrada." Ud. de ".$datos_albaranes["ARTICULO"].", cobrado el día  ".date("d/m/Y",strtotime($fecha))."', '".date("H:i:s")."', 1, $idUsuario, '$id_registro')") or die (db_error()."-5");

$id_nuevo_pago=db_insert_id();

// Introduce factura
$ejercicio_factura=date("Y");

// Busca numero de factura
$consulta_facturas=db_query("SELECT * FROM facturas_emitidas WHERE ejercicio_factura='".$ejercicio_factura."' AND serie_factura<>'EDU' ORDER BY num_factura DESC");
if (db_num_rows($consulta_facturas)!=0) {
        $datos_factura=db_fetch_array($consulta_facturas);
        $num_nueva_factura="0000".(intval($datos_factura["num_factura"])+1);
        $num_nueva_factura=substr($num_nueva_factura,strlen($num_nueva_factura)-5,5);
        } else {
        $num_nueva_factura="00001";
        }

$inserta_factura=db_query("INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision, id_asistencia) VALUES ('".$num_nueva_factura."', '".$ejercicio_factura."', '".$datos_alumno["id_alumno"]."', '".date("Y-m-d")."','$id_registro');") or die (db_error());
$id_nueva_factura=db_insert_id();


$concepto_desglose=$datos_albaranes["desglosar_como"];
$nombre_servicio=$datos_albaranes["ARTICULO"];
$inserta_detalle=db_query("INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES ('".$id_nueva_factura."', '".$datos_albaranes["id_servicio_contratado"]."', '".$datos_albaranes["PRECIO_VENTA"]."', '".$cantidad_cobrada."', '".$datos_albaranes["descuento"]."', '".$datos_albaranes["porcentaje_iva"]."', '".$nombre_servicio."', '".$datos_albaranes["SUBVENCION"]."', '$concepto_desglose');") or die (db_error());

// Relaciona pago con factura
$relaciona_pago=db_query("UPDATE pagos_a_cuenta SET id_factura=$id_nueva_factura WHERE id_pago=$id_nuevo_pago") or die(db_error());
 
?>
<script language="Javascript">
// window.close();
self.opener.history.go(0);
window.location.href='/pdf/recibos_material.php?id_pago=<?=$id_nuevo_pago?>&id_material=<?=$id_actividad?>';
</script>