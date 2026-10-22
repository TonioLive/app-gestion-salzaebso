<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error()."-1");

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
$id_alu=$_GET["id_alu"];
$no_fecha_hoy=$_GET["no_fecha_hoy"];

$cantidad_cobrada=$_GET["cantidad_cobrada"];

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

// Antiguo alumnos
$descuento_antiguo_alumno=$_GET["antiguo_alumno"];

if (strlen($descuento_antiguo_alumno)!=0) {
        
        // Añade descuento del 50% al antiguo alumno

        $agrega_descuento=db_query_params('UPDATE servicios_contratados SET descuento=10 WHERE id_servicio_contratado=?', 's', array($id_actividad));

        }
        
// Hermanos
$descuento_hermano=$_GET["hermano"];

if (strlen($descuento_hermano)!=0) {
        
        // Añade descuento del 50% al antiguo alumno

        $agrega_descuento=db_query_params('UPDATE servicios_contratados SET descuento=5 WHERE id_servicio_contratado=?', 's', array($id_actividad));

        }


// Busca servicio contratado
$consulta1="SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=$id_actividad";
$busca_servicio_contratado=db_query($consulta1);
$datos_albaranes=db_fetch_array($busca_servicio_contratado) or die(db_error()."-3");

$introduce_servicio_devuelto=false;
$id_linea_servicio_devuelto=0;

if ($datos_albaranes["id_tipo_iva"]==1) {

        // Busca fianza de servicios educativos
		$periodo_devolucion=false; 
        if (time()>=strtotime(date('Y-09-01'))) {
			$periodo_devolucion=true;
		}
        
        if ($cantidad_cobrada<=0) {
        	$periodo_devolucion=false;
        }
        
        if (($periodo_devolucion==true) and ($datos_albaranes["id_servicio"]!=318) and ($datos_albaranes["id_servicio"]!=329) and ($datos_albaranes["id_servicio"]!=330)) {
        
        $servicio_fianza="(servicios_contratados.id_servicio=318 OR servicios_contratados.id_servicio=329 OR servicios_contratados.id_servicio=330 OR servicios_contratados.id_servicio=338 OR servicios_contratados.id_servicio=356 OR servicios_contratados.id_servicio=365 OR servicios_contratados.id_servicio=376)";
        $busca_servicios_fianzas=db_query("SELECT * FROM servicios_contratados,tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND $servicio_fianza AND servicios_contratados.id_alumno=".$id_alu." AND cantidad>0 ORDER BY servicios_contratados.fecha_inicio") or die(db_error());
        	
        if (db_num_rows($busca_servicios_fianzas)!=0) {
        
        		$cantidad_a_devolver=0;
        
                $busca_servicios_contratados_devueltos=db_query("SELECT * FROM servicios_contratados WHERE $servicio_fianza AND servicios_contratados.id_alumno=".$id_alu." AND servicios_contratados.cantidad=-1 ORDER BY servicios_contratados.fecha_inicio") or die(db_error());
                		
                if (db_num_rows($busca_servicios_contratados_devueltos)==0) {
                	$cantidad_a_devolver=-1;
                }                        		       	
		
                if ($cantidad_a_devolver!=0) {

				// No se le ha devuelto la fianza
                        $introduce_servicio_devuelto=true;
                                                        
                        $datos_servicios_devolver=db_fetch_array($busca_servicios_fianzas);        
                        
                        $id_servicio_devuelto=$datos_servicios_devolver["id_servicio"];                                                
                        $precio_servicio_devuelto=$datos_servicios_devolver["PRECIO_VENTA"];
                        $iva_servicio_devuelto=$datos_servicios_devolver["porcentaje_iva"];
                        $concepto_desglose_devuelto=$datos_servicios_devolver["desglosar_como"];
                        $nombre_servicio_devuelto=$datos_servicios_devolver["ARTICULO"];
                        $subvencion_devuelto=$datos_servicios_devolver["SUBVENCION"];
                        $periodo_devuelto=$datos_servicios_devolver["periodo"];                                               
                                        
                        db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,periodo) VALUES (?,?,?,\'2\',?,?)', 'sssss', array((string)($id_alu), (string)($id_servicio_devuelto), (string)(date("Y/m/d")), (string)($cantidad_a_devolver), (string)($periodo_devuelto))) or die(db_error());                                                                                        
                        
                        $id_linea_servicio_devuelto=db_insert_id();
                        
                }
                
        }
	} // Fin devolución
}

// Inserta importe en movimientos caja

if (strlen($cantidad_cobrada)==0) {
        $cantidad_cobrada=$datos_albaranes["cantidad"];
        }

$importe_cobrado=(round(($datos_albaranes["PRECIO_VENTA"]*(1+$datos_albaranes["porcentaje_iva"])*$cantidad_cobrada),4))*round((1-($datos_albaranes["descuento"]/100)),4);
       
if ($_GET["porcentaje"]!=0) {
                $importe_cobrado=$importe_cobrado*0.5;
                }     


// Datos del alumno
$consulta_datos_alumno=db_query_params('SELECT alumnos.*, usuarios.*, alumnos.id_centro AS centro_alu FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=? AND alumnos.id_alumno=? ORDER BY id_curso DESC;', 'ss', array($id_alumno, $id_alu)) or die(db_error()."-4");
$datos_alumno=db_fetch_array($consulta_datos_alumno);

if (strlen($_GET["solo_emitir"])==0) {

if ($introduce_servicio_devuelto==true) {
        $importe_cobrado=$importe_cobrado+round(($precio_servicio_devuelto*-1*(1+$iva_servicio_devuelto)),2);
        }

$nuevo_pago = db_query("INSERT INTO pagos_a_cuenta (id_factura, id_centro, id_centro_cobro, fecha_pago, importe_pago, forma_pago, observaciones_pago, comentario, hora_pago, signo, anotado_por, id_asistencia) VALUES ('0', '".$datos_alumno["centro_alu"]."', '".$datos_alumno["centro_alu"]."', '".date("Y-m-d")."', '".$importe_cobrado."', '0', 'Cobrado desde entregas de material', '".$cantidad_cobrada." Ud. de ".$datos_albaranes["ARTICULO"].", cobrado el día  ".date("d/m/Y",strtotime($fecha))."', '".date("H:i:s")."', 1, $idUsuario, '$id_registro')") or die (db_error()."-5");

$id_nuevo_pago=db_insert_id();

} else {

$id_nuevo_pago=0;

}

// Introduce factura
$ejercicio_factura=date("Y");
if (date("n")>8) {
        $ejercicio_factura=date("Y")+1;
      }
      
// Busca numero de factura
$consulta_facturas=db_query_params('SELECT * FROM facturas_emitidas WHERE ejercicio_factura=? AND serie_factura<>\'EDU\' ORDER BY num_factura DESC', 's', array((string)($ejercicio_factura)));
if (db_num_rows($consulta_facturas)!=0) {
        $datos_factura=db_fetch_array($consulta_facturas);
        $num_nueva_factura="0000".(intval($datos_factura["num_factura"])+1);
        $num_nueva_factura=substr($num_nueva_factura,strlen($num_nueva_factura)-5,5);
        } else {
        $num_nueva_factura="00001";
        }

$fecha_emision_factura=date("Y-m-d");
if ($no_fecha_hoy==1) {
	$fecha_emision_factura=$datos_albaranes["fecha_inicio"];
}

$inserta_factura=db_query_params('INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision, id_asistencia) VALUES (?, ?, ?, ?,?);', 'sssss', array((string)($num_nueva_factura), (string)($ejercicio_factura), (string)($datos_alumno["id_alumno"]), (string)($fecha_emision_factura), (string)($id_registro))) or die (db_error());
$id_nueva_factura=db_insert_id();


$concepto_desglose=$datos_albaranes["desglosar_como"];
$nombre_servicio=$datos_albaranes["ARTICULO"];

$inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar,periodo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?);', 'ssssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($cantidad_cobrada), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose), (string)($datos_albaranes["periodo"]))) or die (db_error());

// Introduce devolución fianza si fuera necesario
if ($introduce_servicio_devuelto==true) {

        $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar,periodo) VALUES (?, ?, ?, \'-1\', \'0\', ?, ?, ?, ?,?);', 'ssssssss', array((string)($id_nueva_factura), (string)($id_linea_servicio_devuelto), (string)($precio_servicio_devuelto), (string)($iva_servicio_devuelto), 'Devolución ' . (string)($nombre_servicio_devuelto), (string)($subvencion_devuelto), (string)($concepto_desglose_devuelto), (string)($periodo_devuelto))) or die (db_error());

}

// Relaciona pago con factura
if ($id_nuevo_pago!=0) {

$relaciona_pago=db_query_params('UPDATE pagos_a_cuenta SET id_factura=? WHERE id_pago=?', 'ss', array($id_nueva_factura, $id_nuevo_pago)) or die(db_error());

?>
<script language="Javascript">
// window.close();
self.opener.history.go(0);
window.location.href='/pdf/recibos_material.php?id_pago=<?=$id_nuevo_pago?>&id_material=<?=$id_actividad?>';
</script>
<?php 

} else {

if ($_GET["solo_emitir"]==1) {

	header("Location: /facturacion/editar_servicios.php?i=".$datos_alumno["id_alumno"]);

}

?>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
<?php 

}
 
?>
