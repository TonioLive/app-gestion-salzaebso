<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos

$id_alumno=$_POST["id_alumno"];
$id_usuario=$_POST["id_usuario"];
$id_servicio=$_POST["id_servicio"];
$descuento=$_POST["descuento"];
$id_alumno=$_POST["id_alumno"];
$fecha_inicio=$_POST["fecha_inicio"];
$fecha_fin=$_POST["fecha_fin"];

// Inserta servicio
db_query_params('INSERT INTO servicios_contratados (id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad,descuento) VALUES (?,?,?,\'2\',\'1\',?)', 'ssss', array((string)($id_alumno), (string)($id_servicio), (string)(date("Y-m-d", $fecha_inicio)), (string)($descuento))) or die(db_error());

$id_actividad=db_insert_id();

// Factura servicio
$ejercicio_factura=date("Y");
if (date("n")>7) {
        $ejercicio_factura=date("Y")+1;
      }
      
$consulta1=array('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=?', 's', array($id_actividad));
$busca_servicio_contratado=db_query_params($consulta1[0], $consulta1[1], $consulta1[2]);
$datos_albaranes=db_fetch_array($busca_servicio_contratado) or die(db_error()."-3");

// Busca numero de factura
/*
$consulta_facturas=mysql_query("SELECT * FROM facturas_emitidas WHERE ejercicio_factura='".$ejercicio_factura."' ORDER BY num_factura DESC") or die(mysql_error());
if (mysql_num_rows($consulta_facturas)!=0) {
        $datos_factura=mysql_fetch_array($consulta_facturas);
        $num_nueva_factura="0000".(intval($datos_factura["num_factura"])+1);
        $num_nueva_factura=substr($num_nueva_factura,strlen($num_nueva_factura)-5,5);
        } else {
        $num_nueva_factura="00001";
        }
        */
$num_nueva_factura="00001";

$inserta_factura=db_query_params('INSERT INTO facturas_emitidas (num_factura, ejercicio_factura, id_cliente, fecha_emision, id_asistencia) VALUES (?, ?, ?, ?,?);', 'sssss', array((string)($num_nueva_factura), (string)($ejercicio_factura), (string)($id_alumno), (string)(date("Y-m-d")), (string)($id_registro))) or die (db_error());
$id_nueva_factura=db_insert_id();


$concepto_desglose=$datos_albaranes["desglosar_como"];
$nombre_servicio=$datos_albaranes["ARTICULO"];
$inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, \'1\', ?, ?, ?, ?, ?);', 'ssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose))) or die (db_error());


// Cambia fecha fin
db_query_params('UPDATE alumnos SET fecha_baja=? WHERE id_alumno=?', 'ss', array((string)(date("Y-m-d", $fecha_fin)), $id_alumno));


?>
<html>
<head>
<script>
window.opener.history.go(0);
window.close();
</script>
</head>
<body>
</body>
