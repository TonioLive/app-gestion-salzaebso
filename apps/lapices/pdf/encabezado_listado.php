<?php 
// Aquí el logo
// $x_logo=422;
// $y_logo=715;
// $pdf->addJpegFromFile('logo_factura.jpg',$x_logo,$y_logo,115,115);
$x_logo=412;
$y_logo=715;
$pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);


$pdf->setLineStyle(1,'','','','');
$pdf->setColor('1.00','1.00','1.00','');
$pdf->rectangle(20,656,350,104);

$pdf->setColor('00','00','0.50','');
$pdf->selectFont('./fonts/TrebuchetMS-Bold');
$pdf->addText(25,780,12,"Informe de facturas emitidas");
$pdf->addText(255,780,12,"FECHA:");
$pdf->addText(25,740,10,"PERIODO DEL INFORME:");

$pdf->setLineStyle(0.50,'','','','');
$pdf->line(20,650,572,650);

$pdf->setColor('00','00','0.00','');

// Escribimos datos del cliente
$pdf->selectFont('./fonts/TrebuchetMS');
$pdf->addText(155,740,10,$nombre_mes[intval($mes_seleccionado)]." de ".$ejercicio);

// Escribimos datos de factura
$pdf->addText(305,780,12,date("d/m/Y"));

$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->addText(420,710,8,"Sirera & Garrigós Helados, s.l.");
$pdf->selectFont('./fonts/TrebuchetMS');
$pdf->addText(400,700,7,"C/ El Camí, 69 (Entrada por Músicos Pérez Monllor)");
$pdf->addText(445,690,7,"03801 Alcoy (Alicante)");
$pdf->addText(445,677,7,"Teléfonos: 96 554 15 90");
$pdf->addText(440,667,7,"Email: info@heladosir.com");
$pdf->addText(446,655,8,"C.I.F.: B-53820841");

// Pie de Página

$pdf->setColor('0.00','0.00','0.00','');
$pdf->addText(20,30,6,"Sirera & Garrigos Helados, s.l. Empresa inscrita en el Registro Mercantil de la Provincia de Alicante. Tomo: 2713, Libro: 0, Folio: 133, Hoja: A-79985. C.I.F. B-53820841");

// Fin encabezado y pie de página
?>
