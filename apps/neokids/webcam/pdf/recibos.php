<?php  require("../../configuracion/config.php"); ?>
<?php
require("../../configuracion/autentifica_padre.php");

require("importes_como_texto.php");

// Datos

$id_factura=$_GET["id_recibo"];

// Datos de la factura

$contador=0;

$total_absoluto=0;
$total_cobrado=0;
$nivel_maximo_morosidad=0;

$morisidades[0]="<font color=green>No tiene pagos atrasados.</font>";
$morisidades[1]="<font color=red>Leve</font>";
$morisidades[2]="<font color=red>Grave</font>";
$morisidades[3]="<font color=red>Muy Grave</font>";
$morisidades[4]="<font color=red>Expulsión</font>";

$consulta1=db_query_params('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND id_factura=? ORDER BY fecha_emision DESC', 's', array($id_factura));
$datos_factura = db_fetch_array($consulta1);

// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
        }

$total_factura=number_format(($total_base+$total_iva+$total_recargo), 2, ',', '.');
$total_absoluto=$total_absoluto+round(($total_base+$total_iva+$total_recargo),2);

$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_0=number_format(Round($total_base_0,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_0=number_format(Round($total_iva_0,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
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

// Total pagado
$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));

$total_a_cuenta=0;
while ($pagos=db_fetch_array($busca_pagos)) {
        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
        }

// Datos pago a cuenta
$id_pago=$_GET["id_pago"];

$busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_pago=?', 's', array($id_pago));

$datos_pago=db_fetch_array($busca_pagos);

$importe_pago=$datos_pago["importe_pago"];
$forma_de_pago=$pagos["forma_pago"];
$fecha_pago=$pagos["fecha_pago"];
if ((strlen($fecha_pago)==0) or ($fecha_pago=="0000-00-00")) {
        $fecha_pago=$datos_factura["fecha_recibo"];
        if (strlen($fecha_pago)==0) {
       		$fecha_pago=$datos_factura["fecha_emision"];
        	}
        
        }

$nombre_cliente_facturas=$datos_factura["banco_titular"];
$dni_cliente_facturas=$datos_factura["banco_dni_titular"];
$nombre_alumno_facturas=$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"].", ".$datos_factura["Nombre"];

$margen_inferior=25;
$desplazamiento_y=25;

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Dibujamos los rectangulos y las tablas

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

include 'include_recibos.php';

$pdf->ezStream();
?>