<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

// Datos

$id_factura=$_GET["id_factura"];
$copia=$_GET["copia"];
$mostrar_domicilio=$_GET["dom"];


if (strlen($id_factura)!=0) {

// Busca datos de la factura
$consulta_factura="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_factura=".$id_factura;
$consulta_datos_factura = db_query($consulta_factura) or die (db_error());

$numRecords = db_num_rows ($consulta_datos_factura);

if ($numRecords!=0) {

$datos_de_factura=db_fetch_array($consulta_datos_factura);

if ($datos_de_factura["forma_de_pago"]==1) {
                $forma_de_pago="Domiciliación bancaria
nº de cuenta: ".$datos_de_factura["banco_cuenta"];
        } else {
                $forma_de_pago="Contado";
        }

// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (odbc_errormsg());

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
/*
if ($datos_del_cliente["recargo_equivalencia"]==true) {
        $total_recargo=round(($total_base*0.04),2);
        } else {
        $total_recargo=0;
        }
*/
$total_factura=number_format(($total_base+$total_iva+$total_recargo), 2, ',', '.');
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

$pdf->ezStartPageNumbers(526,30,7,'right','Página {PAGENUM} de {TOTALPAGENUM}',1);

// Fin encabezado y pie de página

// Tabla detalle
$consulta_datos_albaranes = db_query("SELECT * FROM detalles_factura WHERE id_factura=".$id_factura) or die (db_query());

$posicion_actual=638;
$albaran_actual=0;

                $posicion_actual=$posicion_actual-2;
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Concepto",9,array('justification'=>'left','aleft'=>30));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Precio",9,array('justification'=>'right','aright'=>358));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Cantidad",9,array('justification'=>'right','aright'=>408));
                /*
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Importe",9,array('justification'=>'right','aright'=>388));

                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Dto %",9,array('justification'=>'right','aright'=>478));

                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Base Imp.",9,array('justification'=>'right','aright'=>478));
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("IVA %",9,array('justification'=>'right','aright'=>508));
                */
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("TOTAL",9,array('justification'=>'right','aright'=>568));
                $posicion_actual=$posicion_actual-12;
                $posicion_actual=$posicion_actual-2;
                $pdf->line(30,$posicion_actual,572,$posicion_actual);

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $pdf->selectFont('./fonts/Helvetica');
        $pdf->setColor('00','00','0.00','');

        $nombre_articulo=$datos_albaranes["concepto"];
        if (strlen($nombre_articulo)>40) {
                $nombre_articulo=substr($nombre_articulo,0,100);
                }
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText($nombre_articulo,8,array('justification'=>'left','aleft'=>30));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($datos_albaranes["precio"], 2, ',', '.')." €",8,array('justification'=>'right','aright'=>358));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($datos_albaranes["cantidad"], 2, ',', '.'),8,array('justification'=>'right','aright'=>408));
        /*
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($total_linea, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>388));
        */
        if ($datos_albaranes["dto"]>0) {
                $pdf->ezSetY($posicion_actual);
                $pdf->ezText(number_format($datos_albaranes["dto"], 0, ',', '.')." %",8,array('justification'=>'right','aright'=>508));
                }
        /*
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($base_linea, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>478));
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format(($datos_albaranes["tipo_iva"]*100), 0, ',', '.')." %",8,array('justification'=>'right','aright'=>508));
        */
        $pdf->ezSetY($posicion_actual);
        $pdf->ezText(number_format($total_abs, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>568));
        $posicion_actual=$posicion_actual-2;
        $posicion_actual=$posicion_actual-10;

        if ($posicion_actual<=$limite_inferior) {
                $posicion_actual=638;
                $pdf->ezNewPage();
                include "encabezado_pie_factura.php";
                $posicion_actual=$posicion_actual-2;
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Concepto",9,array('justification'=>'left','aleft'=>30));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("P.Uni.",9,array('justification'=>'right','aright'=>308));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Uds.",9,array('justification'=>'right','aright'=>328));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Importe",9,array('justification'=>'right','aright'=>388));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Dto %",9,array('justification'=>'right','aright'=>418));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("Base Imp.",9,array('justification'=>'right','aright'=>478));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("IVA %",9,array('justification'=>'right','aright'=>508));
                                $pdf->ezSetY($posicion_actual);
                $pdf->ezText("TOTAL",9,array('justification'=>'right','aright'=>568));
                $posicion_actual=$posicion_actual-12;
                $posicion_actual=$posicion_actual-2;
                $pdf->line(30,$posicion_actual,572,$posicion_actual);
                }
        }
        if (($posicion_actual-28)<=$limite_inferior) {
                $posicion_actual=638;
                $pdf->ezNewPage();
                include "encabezado_pie_factura.php";
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
