<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
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

// Comprueba si ya ha sido introucido
$busca_si_esta=db_query_params('SELECT * FROM servicios_consumidos_asistencia WHERE id_alumno=? AND id_servicio=? AND fecha=?', 'sss', array($id_alumno, $id_actividad, (string)($fecha)));
if (db_num_rows($busca_si_esta)==0) {
        db_query_params('INSERT INTO servicios_consumidos_asistencia (id_alumno,id_servicio,fecha,pagado) VALUES (?,?,?,\'1\')', 'sss', array((string)($id_alumno), (string)($id_actividad), (string)($fecha))) or die(db_error());
        $busca_si_esta=db_query_params('SELECT * FROM servicios_consumidos_asistencia WHERE id_alumno=? AND id_servicio=? AND fecha=?', 'sss', array($id_alumno, $id_actividad, (string)($fecha)));
        $datos_registro=db_fetch_array($busca_si_esta);
        $id_registro=$datos_registro["id_servicio_consumido"];
        } else {
        $datos_registro=db_fetch_array($busca_si_esta);
        $id_registro=$datos_registro["id_servicio_consumido"];
        db_query_params('UPDATE servicios_consumidos_asistencia SET pagado=\'1\' WHERE id_servicio_consumido=?', 's', array($id_registro)) or dei(db_error());
        }

// Inserta importe en movimientos caja

// Datos del servicio
$consulta_actividades=db_query_params('SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND ID=?;', 's', array($id_actividad)) or die(db_error());
$actividades=db_fetch_array($consulta_actividades);


// Datos del alumno
$consulta_datos_alumno=db_query("SELECT alumnos.*, usuarios.*, alumnos.id_centro AS centro_alu FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario$filtro_curso_actual AND usuarios.id_usuario=".$id_alumno." ORDER BY id_curso DESC;") or die(db_error());
$datos_alumno=db_fetch_array($consulta_datos_alumno);

$importe_cobrado=round(($actividades["PRECIO_VENTA"]*(1+$actividades["porcentaje_iva"])),4);

$nuevo_pago = db_query("INSERT INTO pagos_a_cuenta (id_factura, id_centro, id_centro_cobro, fecha_pago, importe_pago, forma_pago, observaciones_pago, comentario, hora_pago, signo, anotado_por, id_asistencia) VALUES ('0', '".$datos_alumno["centro_alu"]."', '".$datos_alumno["centro_alu"]."', '".date("Y-m-d")."', '".$importe_cobrado."', '0', 'Cobrado desde control de asistencia', 'Asistencia de ".$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"]." a ".$actividades["ARTICULO"].", el día  ".date("d/m/Y",strtotime($fecha))."', '".date("H:i:s")."', 1, $idUsuario, '$id_registro')") or die (db_error());

$id_nuevo_pago=db_insert_id();

// Introduce factura
$ejercicio_factura=date("Y");
if (date("n")>7) {
        $ejercicio_factura=date("Y")+1;
      }
      
// Busca numero de factura
$consulta_facturas=db_query_params('SELECT * FROM facturas_emitidas WHERE ejercicio_factura=? AND serie=\'EDU\' ORDER BY num_factura DESC', 's', array((string)($ejercicio_factura)));
if (db_num_rows($consulta_facturas)!=0) {
        $datos_factura=db_fetch_array($consulta_facturas);
        $num_nueva_factura="0000".(intval($datos_factura["num_factura"])+1);
        $num_nueva_factura=substr($num_nueva_factura,strlen($num_nueva_factura)-5,5);
        } else {
        $num_nueva_factura="00001";
        }

$inserta_factura=db_query_params('INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision, id_asistencia) VALUES (?, ?, ?, ?,?);', 'sssss', array((string)($num_nueva_factura), (string)($ejercicio_factura), (string)($datos_alumno["id_alumno"]), (string)(date("Y-m-d")), (string)($id_registro))) or die (db_error());
$id_nueva_factura=db_insert_id();

$inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, cod_facturacion, subvencion) VALUES (?, ?, ?, \'1\', \'0\', ?, ?, \'\', \'0\');', 'sssss', array((string)($id_nueva_factura), (string)($id_actividad), (string)($actividades["PRECIO_VENTA"]), (string)($actividades["porcentaje_iva"]), 'Asistencia de ' . (string)($datos_alumno["Nombre"]) . ' ' . (string)($datos_alumno["Apellido1"]) . ' ' . (string)($datos_alumno["Apellido2"]) . ' a ' . (string)($actividades["ARTICULO"]) . ', el día  ' . (string)(date("d/m/Y", strtotime($fecha))))) or die (db_error());

// Relaciona pago con factura
$relaciona_pago=db_query_params('UPDATE pagos_a_cuenta SET id_factura=? WHERE id_pago=?', 'ss', array($id_nueva_factura, $id_nuevo_pago)) or die(db_error());
 
?>
<script language="Javascript">
// window.close();
self.opener.history.go(0);
window.location.href='/pdf/recibos_asistencia.php?id_asistencia=<?=$id_registro?>';
</script>
