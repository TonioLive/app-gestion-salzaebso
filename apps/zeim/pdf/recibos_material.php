<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

require("importes_como_texto.php");

// Datos

$id_material=$_GET["id_material"];
$id_pago=$_GET["id_pago"];

if (strlen($id_pago)==0) {
        $id_factura=$_GET["id_factura"];
        $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));
        if (db_num_rows($busca_pagos)!=0) {
                $datos_pagos=db_fetch_array($busca_pagos);
                $id_pago=$datos_pagos["id_pago"];
                } else {
                $id_pago=0;
                }
        }

// Datos pago a cuenta
$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_pago=?', 's', array($id_pago));

$datos_pago=db_fetch_array($busca_pagos);

$id_factura_pago=$datos_pago["id_factura"];

// Busca datos de la factura
$consulta_factura=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_factura=?', 's', array($id_factura_pago));
$consulta_datos_factura = db_query_params($consulta_factura[0], $consulta_factura[1], $consulta_factura[2]) or die (db_error());

$datos_de_factura=db_fetch_array($consulta_datos_factura);

$id_alumno_recibo=$datos_de_factura["id_cliente"];

// Busca servicio contratado
$consulta1=array('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_servicio_contratado=?', 's', array($id_material));
$busca_servicio_contratado=db_query_params($consulta1[0], $consulta1[1], $consulta1[2]);
$datos_albaranes=db_fetch_array($busca_servicio_contratado);

$busca_datos_asistencia=db_query_params('SELECT alumnos.*, usuarios.*, alumnos.id_centro AS centro_alu FROM servicios_consumidos_asistencia, alumnos, usuarios WHERE servicios_consumidos_asistencia.id_alumno=alumnos.id_usuario AND alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_alumno_recibo)) or die(db_error()."-C");

$datos_asistencia=db_fetch_array($busca_datos_asistencia);

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_de_factura["centro_factura"]));
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$nombre_centro=$datos_centro["nombre_centro"];
$prefijo_centro=$datos_centro["prefijo_centro"];
$razon_social=$datos_centro["razon_social"];
$domicilio=$datos_centro["domicilio_centro"];
$cp=$datos_centro["CP_centro"];
$poblacion=$datos_centro["poblacion_centro"];
$telefono=$datos_centro["telefono_centro"];
$fax=$datos_centro["fax_centro"];
$cif=$datos_centro["CIF_centro"];
}

$id_pago=$datos_pago["id_pago"];

$importe_pago=$datos_pago["importe_pago"];
$forma_de_pago=$pagos["forma_pago"];
$fecha_pago=$pagos["fecha_pago"];
if ((strlen($fecha_pago)==0) or ($fecha_pago=="0000-00-00")) {
        $fecha_pago=$datos_de_factura["fecha_emision"];
        }
$nombre_cliente_facturas=$datos_de_factura["banco_titular"];

if (strlen($nombre_cliente_facturas)==0) {
        $nombre_cliente_facturas=$datos_de_factura["contacto1"];
        }

$dni_cliente_facturas=$datos_de_factura["banco_dni_titular"];
$nombre_alumno_facturas=$datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"].", ".$datos_de_factura["Nombre"];

$margen_inferior=25;
$desplazamiento_y=25;

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Dibujamos los rectangulos y las tablas

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

include 'include_recibos_asistencia.php';

$pdf->ezStream();
?>
