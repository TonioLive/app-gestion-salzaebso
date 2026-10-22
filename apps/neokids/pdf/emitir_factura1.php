<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_factura=$_POST["id_factura"];

$consulta_factura=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_factura=?', 's', array($id_factura));

$consulta_datos_factura = db_query_params($consulta_factura[0], $consulta_factura[1], $consulta_factura[2]) or die (db_error());

$numRecords = db_num_rows ($consulta_datos_factura);

$datos_de_factura=db_fetch_array($consulta_datos_factura);

$fecha_factura=$datos_de_factura["fecha_emision"];
$ejercicio_factura=$datos_de_factura["ejercicio_factura"];

// Busca numero de factura
$consulta_facturas=db_query_params('SELECT * FROM facturas_emitidas WHERE ejercicio_factura=? AND facturada=1 ORDER BY num_factura DESC', 's', array((string)($ejercicio_factura)));
if (db_num_rows($consulta_facturas)!=0) {
        $datos_factura=db_fetch_array($consulta_facturas);
        $num_nueva_factura="0000".(intval($datos_factura["num_factura"])+1);
        $num_nueva_factura=substr($num_nueva_factura,strlen($num_nueva_factura)-5,5);
        
        $fecha_ultima_factura=$datos_factura["fecha_emision"];
        
        if ($fecha_factura<$fecha_ultima_factura) {
        	$fecha_nueva_factura=$fecha_ultima_factura;
        	} else {
        	$fecha_nueva_factura=$fecha_factura;
        	}

        } else {
        $num_nueva_factura="00001";
        $fecha_nueva_factura=$fecha_factura;
        }

// Actualiza

// exit("Nueva factura: ".$num_nueva_factura." ".$fecha_nueva_factura);

// mysql_query("UPDATE facturas_emitidas SET facturada=1, num_factura='$num_nueva_factura', fecha_emision='$fecha_nueva_factura', fecha_recibo='$fecha_factura'  WHERE id_factura=".$id_factura);
// No se modifica fecha factura
db_query_params('UPDATE facturas_emitidas SET serie=\'EDU\', facturada=1, num_factura=?, fecha_emision=?, fecha_recibo=?  WHERE id_factura=?', 'ssss', array((string)($num_nueva_factura), (string)($fecha_nueva_factura), (string)($fecha_factura), $id_factura));

header("Location: /pdf/factura.php?id_factura=".$id_factura);

?>