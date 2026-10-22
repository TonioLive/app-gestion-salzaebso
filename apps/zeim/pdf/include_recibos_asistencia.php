<?php 

$mostrar_solo_fecha_de_hoy=$_GET["hoy"];

// Recibos

if (($forma_de_pago<7) and ($forma_de_pago>1)) {
        if (strlen($datos_del_cliente["Cuenta_cobros"]!=0)) {
                $texto_forma_de_pago_recibo="Nº de cuenta donde se realizará el cargo\n".$datos_del_cliente["Cuenta_cobros"]."\n";
                } else {
                $texto_forma_de_pago_recibo="";
                }
        }

$altura_hoja=841.89;
$altura_recibo=round(($altura_hoja/3),2);
$anchura_recibo=595.28;
$margen_horizontal=25;
$margen_vertical=25;
$altura_logo=round(($altura_recibo/2),2);

$posicion_actual=$altura_hoja;

$x_logo=$margen_horizontal+50;
$y_logo=$posicion_actual-$altura_logo;
$pdf->addJpegFromFile('logo_recibo.jpg',$x_logo,$y_logo,$altura_logo,$altura_logo);

$pdf->setLineStyle(1,'','','','');
$pdf->setColor('1.00','1.00','1.00','');
$pdf->rectangle($margen_horizontal,($posicion_actual-$altura_recibo+$margen_vertical),($anchura_recibo-($margen_horizontal*2)),($altura_recibo-($margen_vertical*2)-47));

$pdf->setColor('0.80','0.85','0.90','');
$pdf->setLineStyle(0.25,'','','','');
$pdf->rectangle(($margen_horizontal+72),($posicion_actual-$margen_vertical-81),464,28.5);
$pdf->filledRectangle(($margen_horizontal+10),$posicion_actual-176,526,28);
$pdf->filledRectangle(($margen_horizontal+10),$posicion_actual-191,526,13);
$pdf->rectangle(($margen_horizontal+365),($posicion_actual-$altura_recibo+$margen_vertical+5),170,55);
$pdf->setColor('00','00','0.50','');

$posicion_texto=$posicion_actual-$margen_vertical-20;

$pdf->setColor('00','00','0.50','');
$pdf->selectFont('./fonts/Helvetica-Bold');
if (strlen($datos_de_factura["serie"])!=0) {
        $pdf->addText(354,$posicion_texto,16,"RECIBO Nº: ".$prefijo_centro.$id_asistencia.".$id_pago/ ".date("Y"));
        } else {
        $pdf->addText(354,$posicion_texto,16,"RECIBO");
        }

$pdf->setColor('00','00','0.00','');
$posicion_texto=$posicion_texto-20;

$pdf->addText(484,$posicion_texto,10,"Fecha: ".date("d/m/Y",strtotime($fecha_pago)));

$pdf->selectFont('./fonts/Helvetica');
// $pdf->addText($margen_horizontal,$posicion_texto,6,"Neus Corporación Distribuciones Cosméticas, s.l - C.I.F.: B-53835062");

$posicion_texto=$posicion_texto-9;
$pdf->ezSetY($posicion_texto);
$pdf->ezText("Recibí de: ",10,array('justification'=>'right','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+70)));
$pdf->selectFont('./fonts/Helvetica');
$parte2_nombre_recibo=$nombre_alumno_facturas;
if (strlen($parte2_nombre_recibo)!=0) {
        $parte2_nombre_recibo=" - Alumno: ".$parte2_nombre_recibo;
        }
$pdf->ezSetY($posicion_texto);
$pdf->ezText($nombre_cliente_facturas.$parte2_nombre_recibo,10,array('justification'=>'left','aleft'=>($margen_horizontal+75),'aright'=>($anchura_recibo-$margen_horizontal)));

$posicion_texto=$posicion_texto-32;

if (strlen($datos_de_factura["serie"])!=0) {
        $pdf->ezSetY($posicion_texto);
        $pdf->selectFont('./fonts/Helvetica-Bold');
        $pdf->ezText("N.I.F.: ",10,array('justification'=>'right','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+72)));
        $pdf->selectFont('./fonts/Helvetica');
        $pdf->ezSetY($posicion_texto);
        $pdf->ezText($dni_cliente_facturas,10,array('justification'=>'left','aleft'=>($margen_horizontal+75),'aright'=>($anchura_recibo-125)));
        }

$posicion_texto=$posicion_texto-30;

$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("La cantidad de: ",9,array('justification'=>'left','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+115)));

// Importe en texto

// Separamos la parte entera de la parte decimal
$importe_mostrado=a_texto($importe_pago);

$posicion_texto=$posicion_texto-15;
$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText($importe_mostrado,10,array('justification'=>'center','aleft'=>$margen_horizontal+10,'aright'=>($anchura_recibo-$margen_horizontal)));

$posicion_texto=$posicion_texto-25;
$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("------".number_format($importe_pago, 2, ',', '.')." €------",12,array('justification'=>'center','aleft'=>$margen_horizontal+10,'aright'=>($anchura_recibo-$margen_horizontal)));

$posicion_texto=$posicion_texto-20;
$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("Firma y sello",6,array('justification'=>'left','aleft'=>($margen_horizontal+367),'aright'=>($anchura_recibo-$margen_horizontal)));


$texto_concepto=$datos_pago["comentario"];
$posicion_texto=$posicion_texto+5;
$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>Por:</b> ".$texto_concepto,12,array('justification'=>'left','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+362)));

if ($forma_de_pago==1) {
$pdf->ezText("<b>Forma de pago:</b> Domiciliación bancaria",10,array('justification'=>'left','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+362)));
$pdf->ezText("<b>Nº de cuenta:</b> ".$datos_asistencia["banco_cuenta"],10,array('justification'=>'left','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+362)));
}
if ($forma_de_pago==0) {
$pdf->ezText("<b>Forma de pago:</b> Contado",10,array('justification'=>'left','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+362)));
}

if ($forma_de_pago==2) {
$pdf->ezText("<b>Forma de pago:</b> Transferencia",10,array('justification'=>'left','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+362)));
}

$posicion_actual=$posicion_actual-$altura_recibo;

$pdf->setLineStyle(1,'','','','');
$pdf->setColor('1.00','1.00','1.00','');
$pdf->rectangle($margen_horizontal,($posicion_actual-($altura_recibo/3)),($anchura_recibo-($margen_horizontal*2)),($altura_recibo/3));

$posicion_texto=$posicion_actual-20;

$pdf->setColor('00','00','0.50','');
$pdf->selectFont('./fonts/Helvetica-Bold');
if (strlen($datos_de_factura["serie"])!=0) {
        $pdf->addText($margen_horizontal+4,$posicion_texto,16,"RECIBO Nº: ".$datos_de_factura["serie"].$datos_factura["num_factura"].".$id_pago/ ".$datos_factura["ejercicio_factura"]);
        } else {
        $pdf->addText($margen_horizontal+4,$posicion_texto,16,"RECIBO");
        }


$pdf->setColor('0.80','0.85','0.90','');
$pdf->setLineStyle(0.25,'','','','');
$pdf->rectangle(($margen_horizontal+365),($posicion_actual-($altura_recibo/3)+5),170,55);

$pdf->setColor('00','00','0.00','');
$pdf->addText(475,$posicion_texto,10,"Fecha: ".date("d/m/Y",strtotime($fecha_pago)));
$posicion_texto=$posicion_texto-7;
$pdf->ezSetY($posicion_texto);
$pdf->ezText("<b>Justificante de entrega</b>",10,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($margen_horizontal+362)));
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("Se me ha entregado el recibo por el ".$texto_concepto,9,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($margen_horizontal+362)));
$pdf->ezText("<b>Importe abonado:</b> ".number_format($importe_pago, 2, ',', '.')." €",9,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($margen_horizontal+362)));
$posicion_texto=$posicion_texto-7;
$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("Firmado, ".$nombre_cliente_facturas,6,array('justification'=>'left','aleft'=>($margen_horizontal+367),'aright'=>($anchura_recibo-$margen_horizontal)));

?>
