<?php 
// Aquí el logo
$x_logo=422;
$y_logo=715;
// $pdf->addJpegFromFile('logo_factura.jpg',$x_logo,$y_logo,115,115);

$pdf->setLineStyle(1,'','','','');

$pdf->setColor('00','00','0.00','');

// Pie de Página

$pdf->setColor('0.00','0.00','0.00','');
$pdf->addText(30,30,6,"ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia. Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123");

// Fin encabezado y pie de página
?>
