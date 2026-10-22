<?php  require("../configuracion/config.php"); ?>
<?php

$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

// Datos
$id_cliente==$_GET["ejercicio"];
$ejercicio=$_GET["ejercicio"];

$filtros_fecha="";

// Comienza el bucle
$consulta_datos="SELECT * FROM TABLA_CIENTES WHERE id_cliente=$id_cliente;";
$consulta_cliente = odbc_exec($odbc, $consulta_datos) or die (odbc_errormsg());

$datos_del_cliente = odbc_fetch_array($consulta_cliente);

$consulta1="SELECT * FROM FACTURAS_EMITIDAS WHERE id_cliente=".$id_cliente." AND ejercicio_factura='".$ejercicio."' ORDER BY fecha_emision DESC";
$consulta_albaranes_cliente = odbc_exec($odbc, $consulta1) or die (odbc_errormsg());

$countQueryString = "SELECT count(*) as results FROM (".$consulta1.")";
$count = odbc_exec ($odbc, $countQueryString);
$numRecords = odbc_result ($count, "results");

$contador=0;

$base_absoluta=0;
$total_absoluto=0;
$iva_4_absoluto=0;
$iva_7_absoluto=0;
$iva_16_absoluto=0;
$base_iva_4_absoluto=0;
$base_iva_7_absoluto=0;
$base_iva_16_absoluto=0;
$descuento_absoluto=0;
$recargo_absoluto=0;
$total_meses=array(0,0,0,0,0,0,0,0,0,0,0,0);
$total_familias=array();
$total_proveedor=array();

while($datos_factura = odbc_fetch_array($consulta_albaranes_cliente)){

// Mira si los albaranes de la factura y calcula los totales
$id_factura=$datos_factura["id_factura"];

// Busca albaranes
$consulta_datos_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_FACTURA WHERE id_factura=".$id_factura) or die (odbc_errormsg());
$total_base=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_dto_fact=0;
$total_base_sin_IVA=0;
$num_albaranes=0;
$num_pagados=0;

while($datos_albaranes = odbc_fetch_array($consulta_datos_albaranes)){
        $consulta_detalle_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_PEDIDO, PEDIDOS_CLIENTES WHERE DETALLES_PEDIDO.ID_PEDIDO=PEDIDOS_CLIENTES.ID_PEDIDO AND DETALLES_PEDIDO.ID_PEDIDO=".$datos_albaranes["id_pedido"]) or die (odbc_errormsg());
        while($detalle_albaranes = odbc_fetch_array($consulta_detalle_albaranes)){

        $consulta_datos_articulo = odbc_exec($odbc, "SELECT * FROM TARIFA WHERE COD='".$detalle_albaranes["ARTICULO"]."'") or die (odbc_errormsg());
        $datos_articulo= odbc_fetch_array($consulta_datos_articulo);

        $consulta_datos_familia = odbc_exec($odbc, "SELECT * FROM TIPOS_IVA WHERE categoria='".$datos_articulo["CATEGORIA"]."'") or die (odbc_errormsg());
        $datos_familia= odbc_fetch_array($consulta_datos_familia);

        $total_linea=$detalle_albaranes["CANTIDAD"]*$detalle_albaranes["PRECIO"];
        $total_dto=$total_linea*$detalle_albaranes["DTO"];
        $total_dto_fact=$total_dto_fact+$total_dto;
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_familia["porcentaje_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_proveedor[intval($datos_articulo["PROVEEDOR"])]=$total_proveedor[intval($datos_articulo["PROVEEDOR"])]+$total_abs;
        $total_familias[intval($datos_familia["id_tipo_iva"])]=$total_familias[intval($datos_familia["id_tipo_iva"])]+$total_abs;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_familia["porcentaje_iva"])
                {
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
                default:
                        $total_base_sin_IVA=$total_base_sin_IVA+$base_linea;
                }
        $num_albaranes=$num_albaranes+1;
        if ($detalle_albaranes["PAGADO"]==true) {
              $num_pagados=$num_pagados+1;
              }
        }
}
if ($num_pagados==0) {
        $estado_factura="<small>Pendiente cobro</small>";
        } else {
        if ($num_pagados==$num_albaranes) {
                $estado_factura="<small>Cobrada</small>";
                } else {
                $estado_factura="<small>Cobrada parcialmente</small>";
                }
        }

if ($datos_del_cliente["recargo_equivalencia"]==true) {
        $total_recargo=round(($total_base*0.04),2);
        } else {
        $total_recargo=0;
        }

$total_factura=($total_base+$total_iva+$total_recargo);

$total_meses[date("n",strtotime($datos_factura["fecha_emision"]))-1]=$total_meses[date("n",strtotime($datos_factura["fecha_emision"]))-1]+$total_factura;
$total_absoluto=$total_absoluto+$total_factura;
$base_absoluta=$base_absoluta+$total_base;
$iva_4_absoluto=$iva_4_absoluto+$total_iva_4;
$iva_7_absoluto=$iva_7_absoluto+$total_iva_7;
$iva_16_absoluto=$iva_16_absoluto+$total_iva_16;
$base_iva_4_absoluto=$base_iva_4_absoluto+$total_base_4;
$base_iva_7_absoluto=$base_iva_7_absoluto+$total_base_7;
$base_iva_16_absoluto=$base_iva_16_absoluto+$total_base_16;
$descuento_absoluto=$descuento_absoluto+$total_dto_fact;
$recargo_absoluto=$recargo_absoluto+$total_recargo;

}

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

$altura_fila=28;
$numero_filas=11;
$inicio_filas=135;
$diferencia_1=0;
$limite_inferior=100;
$posicion_inicio_tabla=525;
$posicion_inicial=625;
// encabezado y pie de página

include "encabezado_carta.php";

// Fin encabezado y pie de página


// Informe cuando paga por domiciliacion
$posicion_actual=638;

$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(75,655,9,"Alcoy, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"));
$pdf->ezSetY($posicion_inicial);
$pdf->ezText("Estimado cliente:



De conformidad con el Real Decreto 2027/95, que establece la obligatoriedad de presentar la relación de clientes y proveedores cuya cifra de negocio haya superado los 3005,06 €; queremos informarles que según nuestros datos, su cifra de negocio durante el ejercicio ".$ejercicio." ha sido de:
",9,array('justification'=>'left','aleft'=>75));
$pdf->ezText("Base imponible facturada:  ".number_format($base_absoluta, 2, ',', '.')." €",9,array('justification'=>'center'));
$pdf->ezText("Total I.V.A.:  ".number_format($iva_4_absoluto+$iva_7_absoluto+$iva_16_absoluto, 2, ',', '.'),9,array('justification'=>'center'));
if ($recargo_absoluto>0) {
        $pdf->ezText("Total R.E.:  ".number_format($recargo_absoluto, 2, ',', '.'),9,array('justification'=>'center'));
        }
$pdf->ezText("Total Facturado:  ".number_format($total_absoluto, 2, ',', '.'),9,array('justification'=>'center'));

        $posicion_actual=$posicion_actual-150;
        $pdf->ezSetY($posicion_actual);
        $pdf->selectFont('./fonts/Helvetica-Bold');
        $pdf->setColor('00','00','0.50','');
        $pdf->ezText("I.V.A. Desglosado",9,array('justification'=>'center'));
        $posicion_actual=$posicion_actual-12;
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText("Tipo I.V.A.",9,array('justification'=>'left','aleft'=>195));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText("Base",9,array('justification'=>'left','aleft'=>265));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText("Importe",9,array('justification'=>'right','aright'=>397));
        $posicion_actual=$posicion_actual-12;
        $posicion_actual=$posicion_actual-2;
        $pdf->line(85,$posicion_actual,515,$posicion_actual);

        $pdf->selectFont('./fonts/Helvetica');
        $pdf->setColor('00','00','0.00','');
        $posicion_actual=$posicion_actual-2;
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText("4%",8,array('justification'=>'right','aright'=>245));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($base_iva_4_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>315));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($iva_4_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>390));
        $posicion_actual=$posicion_actual-12;
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText("7%",8,array('justification'=>'right','aright'=>245));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($base_iva_7_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>315));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($iva_7_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>390));
        $posicion_actual=$posicion_actual-12;
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText("16%",8,array('justification'=>'right','aright'=>245));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($base_iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>315));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>390));
        $posicion_actual=$posicion_actual-12;

$pdf->ezText("


Rogamos nos confirmen la exactitud de este importe antes del próximo día 15/03/".date("Y")."

Sin otro particular, reciba un cordial saludo:






Fdo. Roger Llacer Vaello",9,array('justification'=>'left','aleft'=>75));

$pdf->ezStream();
?>
