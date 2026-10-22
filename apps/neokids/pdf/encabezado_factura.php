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
$pdf->addText(25,780,12,"FACTURA Nº:");
$pdf->addText(255,780,12,"FECHA:");
$pdf->addText(25,740,10,"CLIENTE:");
$pdf->addText(25,726,10,"C.I.F.:");
$pdf->addText(25,712,10,"DOMICILIO:");
$pdf->addText(25,684,10,"PROVINCIA:");
$pdf->addText(255,684,10,"CP:");
$pdf->addText(25,670,10,"POBLACIÓN:");

$pdf->setLineStyle(0.50,'','','','');
$pdf->line(20,650,572,650);

$pdf->setColor('00','00','0.00','');

// Escribimos datos del cliente
$pdf->selectFont('./fonts/TrebuchetMS');
$pdf->addText(75,740,10,$datos_del_cliente["Razon_social"]);
$pdf->addText(75,726,10,$datos_del_cliente["CIF"]);
$pdf->addText(85,712,10,$datos_del_cliente["Direccion"]);
$pdf->addText(90,684,10,$datos_del_cliente["Provincia"]);
$pdf->addText(285,684,10,$datos_del_cliente["Cod_Postal"]);
$pdf->addText(90,670,10,$datos_del_cliente["Poblacion"]);

// Escribimos datos de factura
$pdf->addText(105,780,12,$datos_de_factura["num_factura"]."/ ".$datos_de_factura["ejercicio_factura"]);
$pdf->addText(305,780,12,date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])));

$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->addText(420,710,8,"Sirera & Garrigós Helados, s.l.");
$pdf->selectFont('./fonts/TrebuchetMS');
$pdf->addText(400,700,7,"C/ El Camí, 69 (Entrada por Músicos Pérez Monllor)");
$pdf->addText(445,690,7,"03801 Alcoy (Alicante)");
$pdf->addText(445,677,7,"Teléfono: 96 554 15 90");
$pdf->addText(440,667,7,"Email: info@heladosir.com");
$pdf->addText(446,655,8,"C.I.F.: B-53820841");

// Pie de Página

$pdf->setColor('0.00','0.00','0.00','');
$pdf->addText(20,30,6,"Sirera & Garrigos Helados, s.l. Empresa inscrita en el Registro Mercantil de la Provincia de Alicante. Tomo: 2713, Libro: 0, Folio: 133, Hoja: A-79985. C.I.F. B-53820841");

// Fin encabezado y pie de página
?>
