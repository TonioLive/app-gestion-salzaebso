<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

// Datos

$id_factura=$_GET["id_factura"];

if (strlen($id_factura)!=0) {

$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

// Busca datos de la factura
$consulta_factura="SELECT * FROM FACTURAS_EMITIDAS WHERE id_factura=".$id_factura;
$consulta_datos_factura = odbc_exec($odbc, $consulta_factura) or die (odbc_errormsg());

// Mira si el albarán está facturado y mira el nº de factura
$countQueryString = "SELECT count(*) as results FROM (".$consulta_factura.")";
$count = odbc_exec ($odbc, $countQueryString);
$numRecords = odbc_result ($count, "results");

if ($numRecords!=0) {

$datos_de_factura=odbc_fetch_array($consulta_datos_factura);

$id_cliente=$datos_de_factura["id_cliente"];

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
$limite_inferior=154;

// encabezado y pie de página

include "encabezado_pie_factura.php";

$pdf->ezStartPageNumbers(536,30,7,'right','Página {PAGENUM} de {TOTALPAGENUM}',1);

// Fin encabezado y pie de página

// Tabla detalle
$consulta_datos_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_FACTURA WHERE id_factura=".$id_factura) or die (odbc_errormsg());

$posicion_actual=638;
$albaran_actual=0;
while($datos_albaranes = odbc_fetch_array($consulta_datos_albaranes)){
        if ($albaran_actual!=$datos_albaranes["id_pedido"]) {
                if ($albaran_actual!=0) {
                        $posicion_actual=$posicion_actual-8;
                        }
                $consulta_datos_albaran = odbc_exec($odbc, "SELECT * FROM PEDIDOS_CLIENTES WHERE ID_PEDIDO=".$datos_albaranes["id_pedido"]) or die (odbc_errormsg());
                $datos_albaran= odbc_fetch_array($consulta_datos_albaran);
                $pdf->selectFont('./fonts/TrebuchetMS-Bold');
                $pdf->setColor('00','00','0.50','');
                $pdf->addText(20,$posicion_actual,8,"Albaran Nº:");
                $pdf->addText(120,$posicion_actual,8,"Fecha:");
                $pdf->selectFont('./fonts/TrebuchetMS');
                $pdf->setColor('00','00','0.00','');

                // Busca el ultimo albaran facturado en el ejercicio anterior
                $consulta_alabran_ultimo="SELECT ULTIMOS_ALBARANES.EJERCICIO, [PRIMERO]-1 AS ULTIMO FROM ULTIMOS_ALBARANES WHERE EJERCICIO='".$datos_albaran["EJERCICIO"]."';";
                $ejecuta_alabran_ultimo = odbc_exec($odbc, $consulta_alabran_ultimo) or die (odbc_errormsg());
                $id_alabran_ultimo = odbc_result ($ejecuta_alabran_ultimo, "ULTIMO");
                $id_albaran_actual=($datos_albaranes["id_pedido"]-$id_alabran_ultimo)."/".$datos_albaran["EJERCICIO"];
                
                $pdf->addText(65,$posicion_actual,8,$id_albaran_actual);
                $pdf->addText(147,$posicion_actual,8,date("d/m/Y",strtotime($datos_albaran["FECHA_PEDIDO"])));
                $pdf->selectFont('./fonts/TrebuchetMS-Bold');
                $pdf->setColor('00','00','0.50','');
                $posicion_actual=$posicion_actual-2;
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("COD.",9,array('justification'=>'left','aleft'=>20));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Concepto",9,array('justification'=>'left','aleft'=>55));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("P.Uni.",9,array('justification'=>'right','aright'=>312));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Uds.",9,array('justification'=>'right','aright'=>332));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Importe",9,array('justification'=>'right','aright'=>392));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Dto %",9,array('justification'=>'right','aright'=>422));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Base Imp.",9,array('justification'=>'right','aright'=>482));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("IVA %",9,array('justification'=>'right','aright'=>512));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("TOTAL",9,array('justification'=>'right','aright'=>572));
                $posicion_actual=$posicion_actual-12;
                $posicion_actual=$posicion_actual-2;
                $pdf->line(20,$posicion_actual,572,$posicion_actual);
                $posicion_actual=$posicion_actual-12;
                }
                
        $consulta_detalle_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_PEDIDO WHERE ID_PEDIDO=".$datos_albaranes["id_pedido"]) or die (odbc_errormsg());
        while($detalle_albaranes = odbc_fetch_array($consulta_detalle_albaranes)){
        $total_linea=$detalle_albaranes["CANTIDAD"]*$detalle_albaranes["PRECIO"];
        $total_dto=$total_linea*$detalle_albaranes["DTO"];
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$detalle_albaranes["TIPO_IVA"];
        $total_abs=$base_linea+$iva_linea;
        
        $consulta_nombre_articulo = odbc_exec($odbc, "SELECT * FROM TARIFA WHERE COD='".$detalle_albaranes["ARTICULO"]."'") or die (odbc_errormsg());
        $nombre_articulo= odbc_fetch_array($consulta_nombre_articulo);
        
        $pdf->selectFont('./fonts/TrebuchetMS');
        $pdf->setColor('00','00','0.00','');
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($detalle_albaranes["ARTICULO"],8,array('justification'=>'left','aleft'=>20));

        $nombre_articulo=$nombre_articulo["ARTÍCULO"];
        if (strlen($nombre_articulo)>40) {
                $nombre_articulo=substr($nombre_articulo,0,45);
                }
        $pdf->ezText($nombre_articulo,8,array('justification'=>'left','aleft'=>55));
        $pdf->ezText(number_format($detalle_albaranes["PRECIO"], 2, ',', '.')." €",8,array('justification'=>'right','aright'=>312));
        $pdf->ezText(number_format($detalle_albaranes["CANTIDAD"], 0, ',', '.'),8,array('justification'=>'right','aright'=>332));
        $pdf->ezText(number_format($total_linea, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>392));
        $pdf->ezText(number_format(($detalle_albaranes["DTO"]*100), 0, ',', '.')." %",8,array('justification'=>'right','aright'=>422));
        $pdf->ezText(number_format($base_linea, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>482));
        $pdf->ezText(number_format(($detalle_albaranes["TIPO_IVA"]*100), 0, ',', '.')." %",8,array('justification'=>'right','aright'=>512));
        $pdf->ezText(number_format($total_abs, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>572));
        $posicion_actual=$posicion_actual-2;
        $posicion_actual=$posicion_actual-10;
        $albaran_actual=$datos_albaranes["id_pedido"];
        if ($posicion_actual<=$limite_inferior) {
                $posicion_actual=638;
                $albaran_actual=0;
                $pdf->ezNewPage();
                include "encabezado_pie_factura.php";
                }
        }
        if (($posicion_actual-28)<=$limite_inferior) {
                $posicion_actual=638;
                $albaran_actual=0;
                $pdf->ezNewPage();
                include "encabezado_pie_factura.php";
                }
}

// Informe consumos cliente
$posicion_actual=638;
$pdf->ezNewPage();

include "encabezado_factura.php";

// Datos Informe


$ejercicio=date("Y",strtotime($datos_de_factura["fecha_emision"]));
$mes_seleccionado=date("m",strtotime($datos_de_factura["fecha_emision"]));

$filtros_fecha="";
$filtros_factura="";
if (strlen($mes_seleccionado)!=0) {
        $filtros_fecha=$filtros_fecha." AND month(PEDIDOS_CLIENTES.FECHA_PEDIDO)=".$mes_seleccionado;
        $filtros_factura=$filtros_factura." AND month(FACTURAS_EMITIDAS.fecha_emision)=".$mes_seleccionado;
        }
if (strlen($ejercicio)!=0) {
        $filtros_fecha=$filtros_fecha." AND year(PEDIDOS_CLIENTES.FECHA_PEDIDO)=".$ejercicio;
        $filtros_factura=$filtros_factura." AND year(FACTURAS_EMITIDAS.fecha_emision)=".$ejercicio;
        }

if ((strlen($ejercicio)!=0) and (strlen($mes_seleccionado)==0)) {
      $coletilla="año ".$ejercicio;
      } else {
      if (strlen($mes_seleccionado)!=0) {
              $coletilla=$nombre_mes[intval($mes_seleccionado)]." de ".$ejercicio;
              }
      }

$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

$consulta_datos="SELECT * FROM TABLA_CIENTES WHERE id_cliente=".$id_cliente;
$datos_cliente = odbc_exec($odbc, $consulta_datos) or die (odbc_errormsg());
$Razon_social = odbc_result ($datos_cliente, "Razon_social");
$CIF = odbc_result ($datos_cliente, "CIF");


$contador=0;

$total_absoluto=0;
$iva_4_absoluto=0;
$iva_7_absoluto=0;
$iva_16_absoluto=0;
$base_iva_4_absoluto=0;
$base_iva_7_absoluto=0;
$base_iva_16_absoluto=0;
$descuento_absoluto=0;
$recargo_absoluto=0;
$total_familias=array();
$total_proveedor=array();


// Busca albaranes
$consulta_datos_albaranes = odbc_exec($odbc, "SELECT * FROM PEDIDOS_CLIENTES WHERE ID_CLIENTE=".$id_cliente.$filtros_fecha) or die (odbc_errormsg());
$total_base=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_dto_fact=0;

// Numero de facturas emitidas
$consulta_facturas = odbc_exec($odbc, "SELECT Count(FACTURAS_EMITIDAS.id_factura) AS CuentaDeid_factura FROM FACTURAS_EMITIDAS WHERE ((FACTURAS_EMITIDAS.id_cliente)=".$id_cliente.$filtros_factura.");") or die (odbc_errormsg());
$num_fact_emitidas = odbc_result ($consulta_facturas, "CuentaDeid_factura");
// Numero de abonos
//$consulta_obonos = odbc_exec($odbc, "SELECT FACTURAS_EMITIDAS.id_factura, Sum([CANTIDAD]*[precio]) AS Total FROM (FACTURAS_EMITIDAS INNER JOIN DETALLES_FACTURA ON FACTURAS_EMITIDAS.id_factura = DETALLES_FACTURA.id_factura) INNER JOIN DETALLES_PEDIDO ON DETALLES_FACTURA.id_pedido = DETALLES_PEDIDO.ID_PEDIDO WHERE ((FACTURAS_EMITIDAS.id_cliente)=".$id_cliente.$filtros_factura.") GROUP BY FACTURAS_EMITIDAS.id_factura HAVING (((Sum([CANTIDAD]*[precio]))<0));") or die (odbc_errormsg());
$consulta_obonos="SELECT FACTURAS_EMITIDAS.id_factura, Sum([CANTIDAD]*[precio]) AS Total FROM (FACTURAS_EMITIDAS INNER JOIN DETALLES_FACTURA ON FACTURAS_EMITIDAS.id_factura = DETALLES_FACTURA.id_factura) INNER JOIN DETALLES_PEDIDO ON DETALLES_FACTURA.id_pedido = DETALLES_PEDIDO.ID_PEDIDO WHERE ((FACTURAS_EMITIDAS.id_cliente)=".$id_cliente.$filtros_factura.") GROUP BY FACTURAS_EMITIDAS.id_factura HAVING (((Sum([CANTIDAD]*[precio]))<0))";
$count_abonos = "SELECT count(*) as cuenta_abonos FROM (".$consulta_obonos.")";
$count = odbc_exec ($odbc, $count_abonos);
$num_abonos_emitidos = odbc_result ($count, "cuenta_abonos");
$num_fact_emitidas=$num_fact_emitidas-$num_abonos_emitidos;

while($datos_albaranes = odbc_fetch_array($consulta_datos_albaranes)){
        $contador=$contador+1;
        $consulta_detalle_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_PEDIDO WHERE ID_PEDIDO=".$datos_albaranes["ID_PEDIDO"]) or die (odbc_errormsg());
        while($detalle_albaranes = odbc_fetch_array($consulta_detalle_albaranes)){

        $consulta_datos_articulo = odbc_exec($odbc, "SELECT * FROM TARIFA WHERE COD='".$detalle_albaranes["ARTICULO"]."'") or die (odbc_errormsg());
        $datos_articulo= odbc_fetch_array($consulta_datos_articulo);

        $consulta_datos_familia = odbc_exec($odbc, "SELECT * FROM TIPOS_IVA WHERE categoria='".$datos_articulo["CATEGORIA"]."'") or die (odbc_errormsg());
        $datos_familia= odbc_fetch_array($consulta_datos_familia);

        $total_linea=$detalle_albaranes["CANTIDAD"]*$detalle_albaranes["PRECIO"];
        $total_dto=$total_linea*$detalle_albaranes["DTO"];
        $total_dto_fact=$total_dto_fact+$total_dto;
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$detalle_albaranes["TIPO_IVA"];
        $total_abs=$base_linea+$iva_linea;

        $total_proveedor[intval($datos_articulo["PROVEEDOR"])]=$total_proveedor[intval($datos_articulo["PROVEEDOR"])]+$total_abs;
        $total_familias[intval($datos_familia["id_tipo_iva"])]=$total_familias[intval($datos_familia["id_tipo_iva"])]+$total_abs;

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
        $total_recargo=nRound(($total_base*0.04),2);
        } else {
        $total_recargo=0;
        }
$total_factura=($total_base+$total_iva+$total_recargo);

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

// Escribe informe

$pdf->selectFont('./fonts/TrebuchetMS');
$pdf->setColor('00','00','0.00','');
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Historial de Consumo del cliente (".$coletilla.")",10,array('justification'=>'center'));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("* Los importes aquí mostrados representan el total de todos los alabranes entregadoss durante el mes.",6,array('justification'=>'center'));
$posicion_actual=$posicion_actual-10;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Si el importe total es inferior al de la factura a la que se adjunta este informe, puede deberse a: 1. Se ha realizado algún abono, 2. En la factura se incluye algún albarán de meses anteriores.",6,array('justification'=>'center'));
$posicion_actual=$posicion_actual-2;
$pdf->setColor('00','00','0.50','');
$pdf->line(20,$posicion_actual,572,$posicion_actual);
$pdf->setColor('00','00','00','');
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Nº de alabaranes emitidos:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText($contador,8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Nº de facturas emitidas (incluida esta):",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText($num_fact_emitidas,8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Nº de abonos realizados:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText($num_abonos_emitidos,8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Base imponible ventas mes:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($base_absoluta, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Importe total ventas mes:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($total_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("I.V.A. total cobrado:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($iva_4_absoluto+$iva_7_absoluto+$iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-20;
// Cuadro desglose IVA
$pdf->setColor('0.80','0.85','0.90','');
$pdf->filledRectangle(18,$posicion_actual-4,155,14);
$pdf->setColor('00','00','0.50','');
$pdf->setLineStyle(1.0,'','','','');
$pdf->rectangle(18,$posicion_actual-46,155,56);
$pdf->setLineStyle(0.50,'','','','');
$pdf->line(18,$posicion_actual-4,173,$posicion_actual-4);

$pdf->ezSetY($posicion_actual);
$pdf->ezText("Tipo",8,array('justification'=>'right','aright'=>50));
$pdf->ezText("Base",8,array('justification'=>'right','aright'=>110));
$pdf->ezText("I.V.A.",8,array('justification'=>'right','aright'=>160));
$pdf->setColor('00','00','00','');
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("4 %",8,array('justification'=>'right','aright'=>60));
$pdf->ezText(number_format($base_iva_4_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>120));
$pdf->ezText(number_format($iva_4_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("7 %",8,array('justification'=>'right','aright'=>60));
$pdf->ezText(number_format($base_iva_7_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>120));
$pdf->ezText(number_format($iva_7_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("16 %",8,array('justification'=>'right','aright'=>60));
$pdf->ezText(number_format($base_iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>120));
$pdf->ezText(number_format($iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>170));
$posicion_actual=$posicion_actual-22;
$pdf->ezSetY($posicion_actual);
$pdf->setColor('00','00','0.50','');
$pdf->ezText("Consumo por proveedor (IVA Inc.)",8,array('justification'=>'left','aleft'=>20));
$pdf->line(18,$posicion_actual-2,173,$posicion_actual-2);
$posicion_actual=$posicion_actual-10;
$pdf->setColor('00','00','00','');

$consulta_proveedores = odbc_exec($odbc, "SELECT * FROM PROVEEDORES ORDER BY RAZON_SOCIAL") or die (odbc_errormsg());
while($proveedores= odbc_fetch_array($consulta_proveedores)){
        if ($total_proveedor[$proveedores["ID_PROVEEDOR"]]!=0) {
                $nombre_proveedor=$proveedores["RAZON_SOCIAL"];
                if (strlen($nombre_proveedor)>30) {
                        $nombre_proveedor=substr($nombre_proveedor,0,30);
                        }
                $ventas_proveedor=$total_proveedor[$proveedores["ID_PROVEEDOR"]];
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($nombre_proveedor,6,array('justification'=>'left','aleft'=>20));
                $pdf->ezText(number_format($ventas_proveedor, 2, ',', '.')." €",6,array('justification'=>'right','aright'=>170));
                $posicion_actual=$posicion_actual-10;
                }
        }
$posicion_actual=$posicion_actual-10;
$pdf->ezSetY($posicion_actual);
$pdf->setColor('00','00','0.50','');
$pdf->ezText("Consumo por familia (IVA Inc.)",8,array('justification'=>'left','aleft'=>20));
$pdf->line(18,$posicion_actual-2,173,$posicion_actual-2);
$posicion_actual=$posicion_actual-10;
$pdf->setColor('00','00','00','');

$consulta_proveedores = odbc_exec($odbc, "SELECT * FROM TIPOS_IVA ORDER BY categoria") or die (odbc_errormsg());
while($proveedores= odbc_fetch_array($consulta_proveedores)){
        if ($total_familias[$proveedores["id_tipo_iva"]]!=0) {
                $nombre_familia=$proveedores["categoria"];
                if (strlen($nombre_familia)>30) {
                        $nombre_familia=substr($nombre_familia,0,30);
                        }
                $ventas_familia=$total_familias[$proveedores["id_tipo_iva"]];
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText($nombre_familia,6,array('justification'=>'left','aleft'=>20));
                $pdf->ezText(number_format($ventas_familia, 2, ',', '.')." €",6,array('justification'=>'right','aright'=>170));
                $posicion_actual=$posicion_actual-10;
                }
        }

// Linea vertical
$pdf->setColor('00','00','0.50','');
$pdf->line(180,45,180,612);
$pdf->setColor('00','00','00','');

// Graficos

// Recopilamos datos

// Ultimos 12 meses
if (strlen($mes_seleccionado)==0) {
        $mes_inicio=12;
        } else {
        $mes_inicio=intval($mes_seleccionado);
        }
if (strlen($ejercicio)==0) {
        $ejercicio_inicio=strftime("%Y");
        } else {
        $ejercicio_inicio=intval($ejercicio);
        }

$ejercicio_actual=$ejercicio_inicio;
$meses=array();
$ventas=array();
$ventas_helados=array();
$ventas_granizados=array();
$ventas_bolleria=array();
$iva=array();
$descuento=array();
$maximo=0;
$hay_helados=false;
$hay_bolleria=false;
$hay_granizados=false;
$total_total=0;
$total_helados=0;
$total_granizados=0;
$total_bolleria=0;
$total_meses_ventas=0;
$total_meses_helados=0;
$total_meses_granizados=0;
$total_meses_bolleria=0;
$total_meses_otros=0;

for ($c_meses=0; $c_meses<=12; $c_meses=$c_meses+1)
        {
        $mes_actual=$mes_inicio-$c_meses;

        if ($mes_actual==0) { $ejercicio_actual=$ejercicio_actual-1; }
        if ($mes_actual<1) { $mes_actual=$mes_actual+12; }

        $mes_buscado=$mes_actual."/".$ejercicio_actual;
        $consulta_meses="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva FROM PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."'));";

        $datos_mes = odbc_exec ($odbc, $consulta_meses);

        // Consumo proveedores
        $cod_helados=9;
        $consulta_helados="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva, TARIFA_COD_COMO_T.PROVEEDOR FROM (PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO) INNER JOIN TARIFA_COD_COMO_T ON DETALLES_PEDIDO.ARTICULO = TARIFA_COD_COMO_T.CODIGO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]), TARIFA_COD_COMO_T.PROVEEDOR HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."') AND ((TARIFA_COD_COMO_T.PROVEEDOR)=".$cod_helados."));";
        $datos_helados = odbc_exec ($odbc, $consulta_helados);

        $cod_granizados=18;
        $consulta_granizados="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva, TARIFA_COD_COMO_T.PROVEEDOR FROM (PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO) INNER JOIN TARIFA_COD_COMO_T ON DETALLES_PEDIDO.ARTICULO = TARIFA_COD_COMO_T.CODIGO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]), TARIFA_COD_COMO_T.PROVEEDOR HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."') AND ((TARIFA_COD_COMO_T.PROVEEDOR)=".$cod_granizados."));";
        $datos_granizados = odbc_exec ($odbc, $consulta_granizados);

        $cod_bolleria=4;
        $consulta_bolleria="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva, TARIFA_COD_COMO_T.PROVEEDOR FROM (PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO) INNER JOIN TARIFA_COD_COMO_T ON DETALLES_PEDIDO.ARTICULO = TARIFA_COD_COMO_T.CODIGO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]), TARIFA_COD_COMO_T.PROVEEDOR HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."') AND ((TARIFA_COD_COMO_T.PROVEEDOR)=".$cod_bolleria."));";
        $datos_bolleria = odbc_exec ($odbc, $consulta_bolleria);

        // Guarda totales

        $meses[$c_meses]=$mes_buscado;
        $ventas[$c_meses]=odbc_result ($datos_mes, "Importe");
        if ($ventas[$c_meses]>0) {
                                 $hay_helados=true;
                                 $total_meses_ventas=$total_meses_ventas+1;
                                 }
        $iva[$c_meses]=odbc_result ($datos_mes, "Iva");
        $descuento[$c_meses]=odbc_result ($datos_mes, "Descuento");
        $ventas_helados[$c_meses]=odbc_result ($datos_helados, "Importe");
        if ($ventas_helados[$c_meses]>0) {
                                         $hay_helados=true;
                                         $total_meses_helados=$total_meses_helados+1;
                                         }
        $ventas_granizados[$c_meses]=odbc_result ($datos_granizados, "Importe");
        if ($ventas_granizados[$c_meses]>0) {
                                            $hay_granizados=true;
                                            $total_meses_granizados=$total_meses_granizados+1;
                                            }
        $ventas_bolleria[$c_meses]=odbc_result ($datos_bolleria, "Importe");
        if ($ventas_bolleria[$c_meses]>0) {
                                          $hay_bolleria=true;
                                          $total_meses_bolleria=$total_meses_bolleria+1;
                                          }
        if ($ventas[$c_meses]-$ventas_helados[$c_meses]-$ventas_granizados[$c_meses]-$ventas_bolleria[$c_meses]>0) {
                                          $hay_otros=true;
                                          $total_meses_otros=$total_meses_otros+1;
                                          }
        if ($ventas[$c_meses]>=$maximo) {
                $maximo=$ventas[$c_meses];
                }
        $total_total=$total_total+$ventas[$c_meses];
        $total_helados=$total_helados+$ventas_helados[$c_meses];
        $total_granizados=$total_granizados+$ventas_granizados[$c_meses];
        $total_bolleria=$total_bolleria+$ventas_bolleria[$c_meses];
        }

// Fin datos
$altura_grafico=175;
$anchura_grafico=183;
$separacion_grafico=6;
$posicion_actual=600;

// Grafico general
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Consumo durante el último año (IVA Inc.)",8,array('justification'=>'left','aleft'=>190));
$posicion_actual=$posicion_actual-2;
$pdf->rectangle(190,$posicion_actual-$altura_grafico,372,$altura_grafico);
$linea_de_base=$posicion_actual-$altura_grafico;

$posicion_x=190;
$anchura_barra_grafico=(372/13);
$pdf->line($posicion_x+2,$linea_de_base+10,$posicion_x+370,$linea_de_base+10);
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $linea1_mes=substr($nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))],0,3).".";
        $linea2_mes=substr($meses[$c_meses],strlen($meses[$c_meses])-4,4);
        $pdf->ezSetY($linea_de_base+2);
        $pdf->ezText($linea1_mes." ".$linea2_mes,5,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        if ($c_meses!=0) {
                $pdf->line($posicion_x,$linea_de_base+2,$posicion_x,$linea_de_base+10);
                }
        }
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
$posicion_x=190;

for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas[$c_meses], 2, ',', '.')." €";
        if ($ventas[$c_meses]>0) {
                $altura_barra=round(($ventas[$c_meses]*$unidad),0);
                $pdf->setColor('0.00','0.00','0.50','');
                $pdf->filledRectangle($posicion_x+2,$linea_de_base+10,$anchura_barra_grafico-4,$altura_barra);
                $pdf->setColor('0.00','0.00','0.00','');
                $pdf->ezSetY($linea_de_base+12+$altura_barra);
                $pdf->ezText($ventas_formateado,4,array('justification'=>'left','aleft'=>($posicion_x+3)));
                } else {
                $altura_barra=0;
                }
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        }

// Linea promedio
if ($total_meses_ventas!=0) {
        $posicion_x=190;
        $promedio_ventas=$total_total/$total_meses_ventas;
        $altura_promedio=round(($promedio_ventas*$unidad),0);
        $pdf->ezSetY($linea_de_base+10+$altura_promedio+1);
        $pdf->ezText("Promedio: ".number_format($promedio_ventas, 2, ',', '.')." €",4,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $pdf->line($posicion_x+2,$linea_de_base+10+$altura_promedio,$posicion_x+370,$linea_de_base+10+$altura_promedio);
        }
$posicion_actual=$posicion_actual-($altura_grafico+12);

// Fin grafico total total

// Helados
$margen_texto=1;
$tama_fuente=3;
$posicion_x=190;

$pdf->ezSetY($posicion_actual);
$pdf->ezText("Consumo de Helados durante el último año (IVA Inc.)",5,array('justification'=>'left','aleft'=>$posicion_x));
$posicion_actual=$posicion_actual-2;
$pdf->rectangle($posicion_x,$posicion_actual-$altura_grafico,$anchura_grafico,$altura_grafico);
$linea_de_base=$posicion_actual-$altura_grafico;

$anchura_barra_grafico=($anchura_grafico/13);
$pdf->line($posicion_x+2,$linea_de_base+10,$posicion_x+$anchura_grafico-2,$linea_de_base+10);
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $linea1_mes=substr($nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))],0,3).".";
        $linea2_mes="";
        $pdf->ezSetY($linea_de_base+2);
        $pdf->ezText($linea1_mes." ".$linea2_mes,5,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        if ($c_meses!=0) {
                $pdf->line($posicion_x,$linea_de_base+2,$posicion_x,$linea_de_base+10);
                }
        }
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
$posicion_x=190;

for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas_helados[$c_meses], 2, ',', '.');
        if ($ventas_helados[$c_meses]>0) {
                $altura_barra=round(($ventas_helados[$c_meses]*$unidad),0);
                $pdf->setColor('0.00','0.00','0.50','');
                $pdf->filledRectangle($posicion_x+2,$linea_de_base+10,$anchura_barra_grafico-4,$altura_barra);
                $pdf->setColor('0.00','0.00','0.00','');
                $pdf->ezSetY($linea_de_base+12+$altura_barra);
                $pdf->ezText($ventas_formateado,$tama_fuente,array('justification'=>'left','aleft'=>($posicion_x+$margen_texto)));
                } else {
                $altura_barra=0;
                }
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        }

// Linea promedio
$posicion_x=190;
if ($total_meses_helados!=0) {
        $promedio_ventas=$total_helados/$total_meses_helados;
        $altura_promedio=round(($promedio_ventas*$unidad),0);
        $pdf->ezSetY($linea_de_base+10+$altura_promedio+1);
        $pdf->ezText("Promedio: ".number_format($promedio_ventas, 2, ',', '.')." €",4,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $pdf->line($posicion_x+2,$linea_de_base+10+$altura_promedio,$posicion_x+$anchura_grafico-2,$linea_de_base+10+$altura_promedio);
        } else {
        $pdf->ezSetY($linea_de_base+10+$altura_total/2);
        $pdf->ezText("No hay consumo",8,array('justification'=>'left','aleft'=>($posicion_x+($anchura_grafico/2)-25)));
        }
//$posicion_actual=$posicion_actual-($altura_grafico+12);
$posicion_actual=$posicion_actual+2;

// Fin grafico Helados

// Granizados
$margen_texto=1;
$tama_fuente=3;
$posicion_x_ini=190+$anchura_grafico+$separacion_grafico;
$posicion_x=$posicion_x_ini;

$pdf->ezSetY($posicion_actual);
$pdf->ezText("Consumo de Granizados durante el último año (IVA Inc.)",5,array('justification'=>'left','aleft'=>$posicion_x));
$posicion_actual=$posicion_actual-2;
$pdf->rectangle($posicion_x,$posicion_actual-$altura_grafico,$anchura_grafico,$altura_grafico);
$linea_de_base=$posicion_actual-$altura_grafico;



$anchura_barra_grafico=($anchura_grafico/13);
$pdf->line($posicion_x+2,$linea_de_base+10,$posicion_x+$anchura_grafico-2,$linea_de_base+10);
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $linea1_mes=substr($nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))],0,3).".";
        $linea2_mes="";
        $pdf->ezSetY($linea_de_base+2);
        $pdf->ezText($linea1_mes." ".$linea2_mes,5,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        if ($c_meses!=0) {
                $pdf->line($posicion_x,$linea_de_base+2,$posicion_x,$linea_de_base+10);
                }
        }
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
$posicion_x=$posicion_x_ini;

for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas_granizados[$c_meses], 2, ',', '.');
        if ($ventas_granizados[$c_meses]>0) {
                $altura_barra=round(($ventas_granizados[$c_meses]*$unidad),0);
                $pdf->setColor('0.00','0.00','0.50','');
                $pdf->filledRectangle($posicion_x+2,$linea_de_base+10,$anchura_barra_grafico-4,$altura_barra);
                $pdf->setColor('0.00','0.00','0.00','');
                $pdf->ezSetY($linea_de_base+12+$altura_barra);
                $pdf->ezText($ventas_formateado,$tama_fuente,array('justification'=>'left','aleft'=>($posicion_x+$margen_texto)));
                } else {
                $altura_barra=0;
                }
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        }

// Linea promedio
$posicion_x=$posicion_x_ini;
if ($total_meses_granizados!=0) {
        $promedio_ventas=$total_granizados/$total_meses_granizados;
        $altura_promedio=round(($promedio_ventas*$unidad),0);
        $pdf->ezSetY($linea_de_base+10+$altura_promedio+1);
        $pdf->ezText("Promedio: ".number_format($promedio_ventas, 2, ',', '.')." €",4,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $pdf->line($posicion_x+2,$linea_de_base+10+$altura_promedio,$posicion_x+$anchura_grafico-2,$linea_de_base+10+$altura_promedio);
        } else {
        $pdf->ezSetY($linea_de_base+10+$altura_total/2);
        $pdf->ezText("No hay consumo",8,array('justification'=>'left','aleft'=>($posicion_x+($anchura_grafico/2)-25)));
        }
$posicion_actual=$posicion_actual-($altura_grafico+12);
//$posicion_actual=$posicion_actual+2;

// Fin grafico Granizados


// Masas
$margen_texto=1;
$tama_fuente=3;
$posicion_x=190;

$pdf->ezSetY($posicion_actual);
$pdf->ezText("Consumo de Masas congeladas durante el último año (IVA Inc.)",5,array('justification'=>'left','aleft'=>$posicion_x));
$posicion_actual=$posicion_actual-2;
$pdf->rectangle($posicion_x,$posicion_actual-$altura_grafico,$anchura_grafico,$altura_grafico);
$linea_de_base=$posicion_actual-$altura_grafico;

$anchura_barra_grafico=($anchura_grafico/13);
$pdf->line($posicion_x+2,$linea_de_base+10,$posicion_x+$anchura_grafico-2,$linea_de_base+10);
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $linea1_mes=substr($nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))],0,3).".";
        $linea2_mes="";
        $pdf->ezSetY($linea_de_base+2);
        $pdf->ezText($linea1_mes." ".$linea2_mes,5,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        if ($c_meses!=0) {
                $pdf->line($posicion_x,$linea_de_base+2,$posicion_x,$linea_de_base+10);
                }
        }
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
$posicion_x=190;

for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas_bolleria[$c_meses], 2, ',', '.');
        if ($ventas_bolleria[$c_meses]>0) {
                $altura_barra=round(($ventas_bolleria[$c_meses]*$unidad),0);
                $pdf->setColor('0.00','0.00','0.50','');
                $pdf->filledRectangle($posicion_x+2,$linea_de_base+10,$anchura_barra_grafico-4,$altura_barra);
                $pdf->setColor('0.00','0.00','0.00','');
                $pdf->ezSetY($linea_de_base+12+$altura_barra);
                $pdf->ezText($ventas_formateado,$tama_fuente,array('justification'=>'left','aleft'=>($posicion_x+$margen_texto)));
                } else {
                $altura_barra=0;
                }
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        }

// Linea promedio
$posicion_x=190;
if ($total_meses_bolleria!=0) {
        $promedio_ventas=$total_bolleria/$total_meses_bolleria;
        $altura_promedio=round(($promedio_ventas*$unidad),0);
        $pdf->ezSetY($linea_de_base+10+$altura_promedio+1);
        $pdf->ezText("Promedio: ".number_format($promedio_ventas, 2, ',', '.')." €",4,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $pdf->line($posicion_x+2,$linea_de_base+10+$altura_promedio,$posicion_x+$anchura_grafico-2,$linea_de_base+10+$altura_promedio);
        } else {
        $pdf->ezSetY($linea_de_base+10+$altura_total/2);
        $pdf->ezText("No hay consumo",8,array('justification'=>'left','aleft'=>($posicion_x+($anchura_grafico/2)-25)));
        }
//$posicion_actual=$posicion_actual-($altura_grafico+12);
$posicion_actual=$posicion_actual+2;

// Fin grafico masas

// Otros
$margen_texto=1;
$tama_fuente=3;
$posicion_x_ini=190+$anchura_grafico+$separacion_grafico;
$posicion_x=$posicion_x_ini;

$pdf->ezSetY($posicion_actual);
$pdf->ezText("Consumo de Otros Productos durante el último año (IVA Inc.)",5,array('justification'=>'left','aleft'=>$posicion_x));
$posicion_actual=$posicion_actual-2;
$pdf->rectangle($posicion_x,$posicion_actual-$altura_grafico,$anchura_grafico,$altura_grafico);
$linea_de_base=$posicion_actual-$altura_grafico;



$anchura_barra_grafico=($anchura_grafico/13);
$pdf->line($posicion_x+2,$linea_de_base+10,$posicion_x+$anchura_grafico-2,$linea_de_base+10);
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $linea1_mes=substr($nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))],0,3).".";
        $linea2_mes="";
        $pdf->ezSetY($linea_de_base+2);
        $pdf->ezText($linea1_mes." ".$linea2_mes,5,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        if ($c_meses!=0) {
                $pdf->line($posicion_x,$linea_de_base+2,$posicion_x,$linea_de_base+10);
                }
        }
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
$posicion_x=$posicion_x_ini;

for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $total_otros=$ventas[$c_meses]-$ventas_helados[$c_meses]-$ventas_granizados[$c_meses]-$ventas_bolleria[$c_meses];
        $ventas_formateado=number_format($total_otros, 2, ',', '.')." €";
        if ($total_otros>0) {
                $altura_barra=round(($total_otros*$unidad),0);
                $pdf->setColor('0.00','0.00','0.50','');
                $pdf->filledRectangle($posicion_x+2,$linea_de_base+10,$anchura_barra_grafico-4,$altura_barra);
                $pdf->setColor('0.00','0.00','0.00','');
                $pdf->ezSetY($linea_de_base+12+$altura_barra);
                $pdf->ezText($ventas_formateado,$tama_fuente,array('justification'=>'left','aleft'=>($posicion_x+$margen_texto)));
                } else {
                $altura_barra=0;
                }
        $posicion_x=$posicion_x+$anchura_barra_grafico;
        }

// Linea promedio
$posicion_x=$posicion_x_ini;
$total_ventas_otros=$total_total-$total_helados-$total_granizados-$total_bolleria;
if ($total_meses_otros!=0) {
        $promedio_ventas=$total_ventas_otros/$total_meses_otros;
        $altura_promedio=round(($promedio_ventas*$unidad),0);
        $pdf->ezSetY($linea_de_base+10+$altura_promedio+1);
        $pdf->ezText("Promedio: ".number_format($promedio_ventas, 2, ',', '.')." €",4,array('justification'=>'left','aleft'=>($posicion_x+2)));
        $pdf->line($posicion_x+2,$linea_de_base+10+$altura_promedio,$posicion_x+$anchura_grafico-2,$linea_de_base+10+$altura_promedio);
        } else {
        $pdf->ezSetY($linea_de_base+10+$altura_total/2);
        $pdf->ezText("No hay consumo",8,array('justification'=>'left','aleft'=>($posicion_x+($anchura_grafico/2)-25)));
        }
$posicion_actual=$posicion_actual-($altura_grafico+12);
//$posicion_actual=$posicion_actual+2;

// Fin grafico otros

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
