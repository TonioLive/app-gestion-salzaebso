<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

// DATOS

$ejercicio=$_GET["ejercicio"];
$mes_seleccionado=$_GET["mes"];

$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

$consulta1="SELECT * FROM FACTURAS_EMITIDAS, TABLA_CIENTES WHERE FACTURAS_EMITIDAS.id_cliente=TABLA_CIENTES.id_cliente AND year(fecha_emision)=".$ejercicio." AND month(fecha_emision)=".$mes_seleccionado." ORDER BY num_factura";
$consulta_albaranes_cliente = odbc_exec($odbc, $consulta1) or die (odbc_errormsg());

$countQueryString = "SELECT count(*) as results FROM (".$consulta1.")";
$count = odbc_exec ($odbc, $countQueryString);
$numRecords = odbc_result ($count, "results");

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Dibujamos los rectangulos y las tablas

// Color de las lineas
$pdf->setStrokeColor('0.0','0.00','0.50','');

$altura_fila=28;
$numero_filas=11;
$inicio_filas=135;
$diferencia_1=0;
$limite_inferior=55;
$posicion_inicial=638;

$pdf->ezStartPageNumbers(536,30,7,'right','Página {PAGENUM} de {TOTALPAGENUM}',1);

// Fin encabezado y pie de página

// Tabla detalle
$pagina_nueva=1;

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
$total_meses=array(0,0,0,0,0,0,0,0,0,0,0,0);
$total_familias=array();
$total_proveedor=array();
$num_abonos_emitidos=0;

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
                $num_albaranes=$num_albaranes+1;
                if ($detalle_albaranes["PAGADO"]==true) {
                      $num_pagados=$num_pagados+1;
                      }
                }
        }
        if ($num_pagados==0) {
                $estado_factura="No Cobrada";
                } else {
                if ($num_pagados==$num_albaranes) {
                        $estado_factura="Cobrada";
                        } else {
                        $estado_factura="Parcialmente";
                        }
                }

        if ($datos_del_cliente["recargo_equivalencia"]==true) {
                $total_recargo=round(($total_base*0.04),2);
                } else {
                $total_recargo=0;
                }

        $total_factura=($total_base+$total_iva+$total_recargo);
        if ($total_factura<0) { $num_abonos_emitidos=$num_abonos_emitidos+1; }
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

        $total_factura=number_format($total_factura, 2, ',', '.');
        $total_recargo=number_format($total_recargo, 2, ',', '.');
        $total_base=number_format(Round($total_base,2), 2, ',', '.');
        $total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
        $total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
        $total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
        $total_iva=number_format(Round($total_iva,2), 2, ',', '.');
        $total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
        $total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
        $total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

        // Aquí representación de datos
        
        // Comprueba si es página nueva e introduce el encabezado
        if ($pagina_nueva==1) {
                $pagina_nueva=0;
                // encabezado y pie de página
                include "encabezado_listado.php";
                $posicion_actual=$posicion_inicial;
                $pdf->selectFont('./fonts/TrebuchetMS-Bold');
                $pdf->setColor('00','00','0.50','');
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Nº factura",9,array('justification'=>'left','aleft'=>20));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Fecha",9,array('justification'=>'left','aleft'=>65));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Cliente",9,array('justification'=>'left','aleft'=>106));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("CIF/NIF",9,array('justification'=>'right','aright'=>320));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Base imp.",9,array('justification'=>'right','aright'=>380));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("IVA",9,array('justification'=>'right','aright'=>420));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("RE",9,array('justification'=>'right','aright'=>460));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Importe",9,array('justification'=>'right','aright'=>520));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Estado",9,array('justification'=>'center','aleft'=>524));
                $posicion_actual=$posicion_actual-12;
                $posicion_actual=$posicion_actual-2;
                $pdf->line(20,$posicion_actual,572,$posicion_actual);
                $posicion_actual=$posicion_actual-12;
                }

        $pdf->selectFont('./fonts/TrebuchetMS');
        $pdf->setColor('00','00','0.00','');
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($datos_factura["num_factura"]."/".$datos_factura["ejercicio_factura"],8,array('justification'=>'left','aleft'=>20));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(date("j/n/Y",strtotime($datos_factura["fecha_emision"])),8,array('justification'=>'right','aright'=>103));
        $pdf->ezSetY($posicion_actual);
        $razon_social_cliente=$datos_factura["Razon_social"];
        if (strlen($razon_social_cliente)>32) {
                $razon_social_cliente=substr($razon_social_cliente,0,32);
                }
        $pdf->ezText($razon_social_cliente,8,array('justification'=>'left','aleft'=>106));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($datos_factura["CIF"],8,array('justification'=>'right','aright'=>320));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($total_base." €",8,array('justification'=>'right','aright'=>380));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($total_iva." €",8,array('justification'=>'right','aright'=>420));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($total_recargo." €",8,array('justification'=>'right','aright'=>460));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($total_factura." €",8,array('justification'=>'right','aright'=>520));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($estado_factura,7,array('justification'=>'center','aleft'=>524));
        $posicion_actual=$posicion_actual-2;
        $posicion_actual=$posicion_actual-10;
        
        if ($posicion_actual<=$limite_inferior) {
                $posicion_actual=$posicion_inicial;
                $pagina_nueva=1;
                $pdf->ezNewPage();
                }

        // Fin representacion
        
        $contador=$contador+1;
}
// Linea que marca el final
$pdf->line(20,$posicion_actual,572,$posicion_actual);

// Resumen
$posicion_actual=$posicion_actual-12;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Resumen en la siguiente página >>",8,array('justification'=>'right','aright'=>572));
$pdf->ezNewPage();
$posicion_actual=$posicion_inicial;
include "encabezado_listado.php";

$pdf->selectFont('./fonts/TrebuchetMS');
$pdf->setColor('00','00','0.00','');
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Resumen de facturación",10,array('justification'=>'center'));
$posicion_actual=$posicion_actual-2;
$pdf->setColor('00','00','0.50','');
$pdf->line(20,$posicion_actual,572,$posicion_actual);
$pdf->setColor('00','00','00','');
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Nº de facuras emitidas (inc. abonos):",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText($contador,8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Nº de abonos realizados:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText($num_abonos_emitidos,8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Base imponible mes:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($base_absoluta, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Importe total mes:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($total_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("I.V.A. total cobrado:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($iva_4_absoluto+$iva_7_absoluto+$iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-20;

// Cuadro desglose IVA
$pdf->setColor('0.80','0.85','0.90','');
$pdf->filledRectangle(18,$posicion_actual-4,165,14);
$pdf->setColor('00','00','0.50','');
$pdf->setLineStyle(1.0,'','','','');
$pdf->rectangle(18,$posicion_actual-46,165,56);
$pdf->setLineStyle(0.50,'','','','');
$pdf->line(18,$posicion_actual-4,183,$posicion_actual-4);

$pdf->ezSetY($posicion_actual);
$pdf->ezText("Tipo",8,array('justification'=>'right','aright'=>50));
$pdf->ezText("Base",8,array('justification'=>'right','aright'=>110));
$pdf->ezText("I.V.A.",8,array('justification'=>'right','aright'=>170));
$pdf->setColor('00','00','00','');
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("4 %",8,array('justification'=>'right','aright'=>60));
$pdf->ezText(number_format($base_iva_4_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>120));
$pdf->ezText(number_format($iva_4_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("7 %",8,array('justification'=>'right','aright'=>60));
$pdf->ezText(number_format($base_iva_7_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>120));
$pdf->ezText(number_format($iva_7_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("16 %",8,array('justification'=>'right','aright'=>60));
$pdf->ezText(number_format($base_iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>120));
$pdf->ezText(number_format($iva_16_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
// RE y descuentos
$posicion_actual=$posicion_actual-16;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Importe R.E. cobrado:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($recargo_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Importe descuentos:",8,array('justification'=>'left','aleft'=>20));
$pdf->ezText(number_format($descuento_absoluto, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>180));
$posicion_actual=$posicion_actual-14;
// Desgloses

$posicion_actual=$posicion_inicial-14;
$posicion_horizontal=200;

// Linea vertical
$pdf->setColor('00','00','0.50','');
$pdf->line($posicion_horizontal+5,$limite_inferior,$posicion_horizontal+5,$posicion_inicial-2);
$pdf->setColor('00','00','00','');

$pdf->ezSetY($posicion_actual);
$pdf->setColor('00','00','0.50','');
$pdf->ezText("Facturación por proveedor (IVA Inc.)",8,array('justification'=>'left','aleft'=>$posicion_horizontal+20));
$pdf->line($posicion_horizontal+18,$posicion_actual-2,$posicion_horizontal+173,$posicion_actual-2);
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
                $pdf->ezText($nombre_proveedor,6,array('justification'=>'left','aleft'=>$posicion_horizontal+20));
                $pdf->ezText(number_format($ventas_proveedor, 2, ',', '.')." €",6,array('justification'=>'right','aright'=>$posicion_horizontal+170));
                $posicion_actual=$posicion_actual-10;
                }
        }
$posicion_actual=$posicion_inicial-14;
$posicion_horizontal=390;

// Linea vertical
$pdf->setColor('00','00','0.50','');
$pdf->line($posicion_horizontal+5,$limite_inferior,$posicion_horizontal+5,$posicion_inicial-2);
$pdf->setColor('00','00','00','');

$pdf->ezSetY($posicion_actual);
$pdf->setColor('00','00','0.50','');
$pdf->ezText("Facturación por familia (IVA Inc.)",8,array('justification'=>'left','aleft'=>$posicion_horizontal+20));
$pdf->line($posicion_horizontal+18,$posicion_actual-2,$posicion_horizontal+173,$posicion_actual-2);
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
                $pdf->ezText($nombre_familia,6,array('justification'=>'left','aleft'=>$posicion_horizontal+20));
                $pdf->ezText(number_format($ventas_familia, 2, ',', '.')." €",6,array('justification'=>'right','aright'=>$posicion_horizontal+170));
                $posicion_actual=$posicion_actual-10;
                }
        }

// FIN NUEVO

$pdf->ezStream();

?>
