<?php  require("../configuracion/config.php"); ?>
<?php
// Datos
require("../configuracion/autentifica.php");

$id_factura=$_GET["id_factura"];

if (strlen($id_factura)!=0) {

$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

// Busca el ultimo albaran facturado en el ejercicio anterior
$consulta_alabran_ultimo="SELECT Last(PEDIDOS_CLIENTES.ID_PEDIDO) AS ultimo FROM PEDIDOS_CLIENTES WHERE (((Year([FECHA_PEDIDO]))=Year(Date())-1));";
$ejecuta_alabran_ultimo = odbc_exec($odbc, $consulta_alabran_ultimo) or die (odbc_errormsg());
$id_alabran_ultimo = odbc_result ($ejecuta_alabran_ultimo, "ultimo");

// Busca datos de la factura
$consulta_factura="SELECT * FROM FACTURAS_EMITIDAS WHERE id_factura=".$id_factura;
$consulta_datos_factura = odbc_exec($odbc, $consulta_factura) or die (odbc_errormsg());

// Mira si el albarán está facturado y mira el nº de factura
$countQueryString = "SELECT count(*) as results FROM (".$consulta_factura.")";
$count = odbc_exec ($odbc, $countQueryString);
$numRecords = odbc_result ($count, "results");

if ($numRecords!=0) {

$datos_de_factura=odbc_fetch_array($consulta_datos_factura);

// Busca datos del cliente
$consulta_datos_cliente = odbc_exec($odbc, "SELECT * FROM TABLA_CIENTES WHERE id_cliente=".$datos_de_factura["id_cliente"]) or die (odbc_errormsg());

$datos_del_cliente=odbc_fetch_array($consulta_datos_cliente);

// Calcula totales

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

while($datos_albaranes = odbc_fetch_array($consulta_datos_albaranes)){
        $consulta_detalle_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_PEDIDO WHERE ID_PEDIDO=".$datos_albaranes["id_pedido"]) or die (odbc_errormsg());
        while($detalle_albaranes = odbc_fetch_array($consulta_detalle_albaranes)){
        $total_linea=$detalle_albaranes["CANTIDAD"]*$detalle_albaranes["PRECIO"];
        $total_dto=$total_linea*$detalle_albaranes["DTO"];
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$detalle_albaranes["TIPO_IVA"];
        $total_abs=$base_linea+$iva_linea;
        
        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($detalle_albaranes["TIPO_IVA"])
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
                }
        }
}
if ($datos_del_cliente["recargo_equivalencia"]==true) {
        $total_recargo=round(($total_base*0.04),2);
        } else {
        $total_recargo=0;
        }
$total_factura=number_format(($total_base+$total_iva+$total_recargo), 2, ',', '.');
$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

$id_cliente=$datos_de_factura["id_cliente"];

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Dibujamos los rectangulos y las tablas

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

$pdf->ezStartPageNumbers(536,30,7,'right','Página {PAGENUM} de {TOTALPAGENUM}',1);

// Fin encabezado y pie de página

// Tabla detalle
$consulta_albaranes_no_pagados="SELECT * FROM PEDIDOS_CLIENTES WHERE (day(FECHA_PEDIDO)<=".date("d",strtotime($datos_de_factura["fecha_emision"]))." AND month(FECHA_PEDIDO)<=".date("m",strtotime($datos_de_factura["fecha_emision"]))." AND year(FECHA_PEDIDO)<=".date("Y",strtotime($datos_de_factura["fecha_emision"])).") AND PAGADO=false AND ID_CLIENTE=".$id_cliente." AND tipo_albaran<>3 ORDER BY FECHA_PEDIDO";

$consulta_albaranes = odbc_exec($odbc, $consulta_albaranes_no_pagados) or die (odbc_errormsg());

$countQueryString = "SELECT count(*) as results FROM (".$consulta_albaranes_no_pagados.")";
$count = odbc_exec ($odbc, $countQueryString);
$numRecords = odbc_result ($count, "results");

if ($numRecords!=0) {

// Informe cuando tiene albaranes pendientes de pago
$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(75,655,9,"Alcoy, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"));
$pdf->ezSetY($posicion_inicial);
$pdf->ezText("Estimado cliente:


Adjunto le remitimos la factura nº ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"]." emitida con fecha ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]))." por un importe de ".$total_factura." €.
",9,array('justification'=>'left','aleft'=>75));
$pdf->ezText("Fecha de vencimiento: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])),9,array('justification'=>'center'));
$pdf->ezText("
A continuación se detallan los albarames pendientes de pago. En la lista se incluyen los albaranes de esta factura y los pendientes hasta la fecha de emisión de la misma.",9,array('justification'=>'left','aleft'=>75));

$posicion_actual=$posicion_inicio_tabla;
$total_pendientes=0;
while($datos_albaranes = odbc_fetch_array($consulta_albaranes)){

        if ($posicion_actual==$posicion_inicio_tabla) {
                $posicion_inicio_tabla=640;
                $pdf->selectFont('./fonts/TrebuchetMS-Bold');
                $pdf->setColor('00','00','0.50','');
                $posicion_actual=$posicion_actual-12;
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Num. Albarán",9,array('justification'=>'left','aleft'=>95));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Fecha pedido",9,array('justification'=>'left','aleft'=>165));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Fecha entrega",9,array('justification'=>'right','aright'=>297));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Total albarán",9,array('justification'=>'right','aright'=>365));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Pagos a cuenta",9,array('justification'=>'right','aright'=>435));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Total pendiente",9,array('justification'=>'right','aright'=>505));
                $posicion_actual=$posicion_actual-12;
                $posicion_actual=$posicion_actual-2;
                $pdf->line(85,$posicion_actual,515,$posicion_actual);
                }

        // Calcula total del albarán
        
        $total_dto_alabran=0;
        $total_base_albaran=0;
        $total_iva_albaran=0;
        $total_base_4=0;
        $total_base_7=0;
        $total_base_16=0;
        $total_iva_4=0;
        $total_iva_7=0;
        $total_iva_16=0;
        $total_recargo=0;
        $total_albaran=0;
        
        $consulta_detalle_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_PEDIDO WHERE ID_PEDIDO=".$datos_albaranes["ID_PEDIDO"]) or die (odbc_errormsg());
        while($detalle_albaranes = odbc_fetch_array($consulta_detalle_albaranes)){

        $consulta_datos_articulo = odbc_exec($odbc, "SELECT * FROM TARIFA WHERE COD='".$detalle_albaranes["ARTICULO"]."'") or die (odbc_errormsg());
        $datos_articulo= odbc_fetch_array($consulta_datos_articulo);

        $consulta_datos_familia = odbc_exec($odbc, "SELECT * FROM TIPOS_IVA WHERE categoria='".$datos_articulo["CATEGORIA"]."'") or die (odbc_errormsg());
        $datos_familia= odbc_fetch_array($consulta_datos_familia);

        $total_linea=$detalle_albaranes["CANTIDAD"]*$detalle_albaranes["PRECIO"];
        $total_dto=$total_linea*$detalle_albaranes["DTO"];
        $total_dto_alabran=$total_dto_alabran+$total_dto;
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$detalle_albaranes["TIPO_IVA"];
        $total_abs=$base_linea+$iva_linea;

        $total_base_albaran=$total_base_albaran+$base_linea;
        $total_iva_albaran=$total_iva_albaran+$iva_linea;
        switch($detalle_albaranes["TIPO_IVA"])
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
                }
        }
        if ($datos_del_cliente["recargo_equivalencia"]==true) {
                $total_recargo=round(($total_base*0.04),2);
                } else {
                $total_recargo=0;
                }

        $total_albaran=($total_base_albaran+$total_iva_albaran+$total_recargo);

        // Comprueba si hay pagos parciales
        $consulta_pagos = odbc_exec($odbc, "SELECT PAGOS_A_CUENTA.id_pedido, Sum(PAGOS_A_CUENTA.importe) AS SumaDeimporte FROM PAGOS_A_CUENTA GROUP BY PAGOS_A_CUENTA.id_pedido HAVING (((PAGOS_A_CUENTA.id_pedido)=".$datos_albaranes["ID_PEDIDO"]."))") or die (odbc_errormsg());
        $datos_pagos= odbc_fetch_array($consulta_pagos);
        $pagos_a_cuenta=$datos_pagos["SumaDeimporte"];
        
        // hasta aquí
        $pdf->setColor('00','00','0.00','');
        $pdf->ezSetY($posicion_actual);
        
                // Busca el ultimo albaran facturado en el ejercicio anterior
                $consulta_alabran_ultimo="SELECT ULTIMOS_ALBARANES.EJERCICIO, [PRIMERO]-1 AS ULTIMO FROM ULTIMOS_ALBARANES WHERE EJERCICIO='".$datos_albaranes["EJERCICIO"]."';";
                $ejecuta_alabran_ultimo = odbc_exec($odbc, $consulta_alabran_ultimo) or die (odbc_errormsg());
                $id_alabran_ultimo = odbc_result ($ejecuta_alabran_ultimo, "ULTIMO");
                $id_albaran_actual=($datos_albaranes["ID_PEDIDO"]-$id_alabran_ultimo)."/".$datos_albaranes["EJERCICIO"];

        $pdf->ezText($id_albaran_actual,8,array('justification'=>'right','aright'=>145));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(date("d/m/Y",strtotime($datos_albaranes["FECHA_PEDIDO"])),8,array('justification'=>'right','aright'=>215));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(date("d/m/Y",strtotime($datos_albaranes["FECHA_ENTREGA"])),8,array('justification'=>'right','aright'=>290));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($total_albaran, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>365));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($pagos_a_cuenta, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>415));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($total_albaran-$pagos_a_cuenta, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
        $posicion_actual=$posicion_actual-12;

        $total_pendientes=$total_pendientes+($total_albaran-$pagos_a_cuenta);
        
        if ($posicion_actual<=$limite_inferior) {
                $posicion_actual=$posicion_inicio_tabla;
                $pdf->ezNewPage();
                include "encabezado_carta.php";
                }
        }
        $pdf->line(85,$posicion_actual,515,$posicion_actual);
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText("Total pagos pendientes:  ".number_format($total_pendientes, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>489));
$posicion_actual=$posicion_actual-60;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Sin otro particular, reciba un cordial saludo:







Fdo. Roger Llacer Vaello",9,array('justification'=>'left','aleft'=>75));
} else {

// Informe cuando no tiene albaranes pendientes de pago
$posicion_actual=638;
include "encabezado_carta.php";

$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(75,655,9,"Alcoy, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"));
$pdf->ezSetY($posicion_inicial);
$pdf->ezText("Estimado cliente:



Adjunto le remitimos la factura nº ".$datos_de_factura["num_factura"]."/".$datos_de_factura["ejercicio_factura"]." emitida con fecha ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"]))." por un importe de ".$total_factura." €.


",9,array('justification'=>'left','aleft'=>75));
$pdf->ezText("Fecha de vencimiento: ".date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])),11,array('justification'=>'center'));
$pdf->ezText("


Sin otro particular, reciba un cordial saludo:






Fdo. Roger Llacer Vaello",9,array('justification'=>'left','aleft'=>75));
}

$pdf->ezStream();

} else {
// El nº de la factura no es correcto
?>
<html>
<head>
</head>
<body>
<br><br><br>
<p align="center"><font size=2 face="Trebuchet MS">No se encuentra la factura con el nº indicado<br><br><a href="javascript:window.close();"></a></font></p>
</body>
</html>
<?php 

}
} else {
// Falta el nº de la factura
?>
<html>
<head>
</head>
<body>
<br><br><br>
<p align="center"><font size=2 face="Trebuchet MS">No se ha indicado el nº de la factura<br><br><a href="javascript:window.close();"></a></font></p>
</body>
</html>
<?php 
}
?>
