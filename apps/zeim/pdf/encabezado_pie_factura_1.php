<?php 
// Aquí el logo
$x_logo=400;
$y_logo=735;
$pdf->addJpegFromFile('logo_factura.jpg',$x_logo,$y_logo,150,54);

$pdf->setLineStyle(1,'','','','');
$pdf->setColor('1.00','1.00','1.00','');
$pdf->rectangle(20,656,350,104);

$pdf->setColor('00','00','0.50','');
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->addText(25,780,12,"FACTURA Nº:");
$pdf->addText(255,780,12,"FECHA:");
$pdf->addText(25,740,10,"CLIENTE:");
$pdf->addText(25,726,10,"C.I.F.:");
$pdf->addText(25,712,10,"ALUMNO:");
$pdf->addText(25,684,10,"PROVINCIA:");
$pdf->addText(255,684,10,"CP:");
$pdf->addText(25,670,10,"POBLACIÓN:");

$pdf->setLineStyle(0.50,'','','','');
$pdf->line(20,650,572,650);

$pdf->setColor('00','00','0.00','');
// Escribimos datos del centro

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_de_factura["centro_factura"]));
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$prefijo_centro=$datos_centro["prefijo_centro"];
$razon_social=$datos_centro["razon_social"];
$domicilio=$datos_centro["domicilio_centro"];
$cp=$datos_centro["CP_centro"];
$poblacion=$datos_centro["poblacion_centro"];
$telefono=$datos_centro["telefono_centro"];
$fax=$datos_centro["fax_centro"];
$cif=$datos_centro["CIF_centro"];
}

$nombre_aula="";
$busca_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_de_factura["aula"]));
if (db_num_rows($busca_aula)!=0) {
$datos_aula=db_fetch_array($busca_aula);
$nombre_aula=$datos_aula["nombre"];
}

// Escribimos datos del cliente

$busca_provincia=db_query("SELECT * FROM provincias WHERE id_provincia=".$datos_de_factura["Provincia"]);
if (db_num_rows($busca_provincia)!=0) {
$datos_provincia=db_fetch_array($busca_provincia);
$nombre_provincia=$datos_provincia["provincia"];
} else {
$nombre_provincia="";
}

$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(85,740,10,$datos_de_factura["banco_titular"]);
$pdf->addText(85,726,10,$datos_de_factura["banco_dni_titular"]);
$pdf->addText(85,712,10,$datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"].", ".$datos_de_factura["Nombre"]);
$pdf->addText(310,700,10,$nombre_aula);
$pdf->addText(90,684,10,$nombre_provincia);
$pdf->addText(285,684,10,$datos_de_factura["cp"]);
$pdf->addText(90,670,10,$datos_de_factura["Poblacion"]);

// Escribimos datos del centro

$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_de_factura["centro_factura"]));
if (db_num_rows($busca_centro)!=0) {
$datos_centro=db_fetch_array($busca_centro);
$prefijo_centro=$datos_centro["prefijo_centro"];
$razon_social=$datos_centro["razon_social"];
$domicilio=$datos_centro["domicilio_centro"];
$cp=$datos_centro["CP_centro"];
$poblacion=$datos_centro["poblacion_centro"];
$telefono=$datos_centro["telefono_centro"];
$fax=$datos_centro["fax_centro"];
$cif=$datos_centro["CIF_centro"];
}

$es_copia="";
if ($copia==1) {
        $es_copia=" - DUPLICADO -";
        }

// Escribimos datos de factura
$pdf->addText(105,780,12,$datos_de_factura["serie"].$datos_de_factura["num_factura"]."/ ".$datos_de_factura["ejercicio_factura"]);
$pdf->addText(105,768,12,$es_copia);
$pdf->addText(305,780,12,date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])));

// $pdf->selectFont('./fonts/Helvetica-Bold');
// $pdf->addText(420,720,8,$razon_social);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY(730);
$pdf->ezText($domicilio,8,array('justification'=>'center','aleft'=>375));
$pdf->ezSetY(720);
$pdf->ezText("$cp $poblacion",8,array('justification'=>'center','aleft'=>375));
$pdf->ezSetY(700);
$pdf->ezText("Teléfono: $telefono",8,array('justification'=>'center','aleft'=>375));
// $pdf->addText(400,710,7,$domicilio);
// $pdf->addText(445,700,7,"$cp $poblacion");
/* $pdf->addText(445,687,7,"Teléfono: $telefono");
if (strlen($fax)!=0) {
        $pdf->addText(440,677,7,"Fax: $fax");
        }
$pdf->addText(446,665,8,"C.I.F.: $cif");
*/

// Pie de página
/*
// Cuadro desglose IVA
$pdf->setColor('0.80','0.85','0.90','');
$pdf->filledRectangle(20,100,200,40);
$pdf->setColor('00','00','0.50','');
$pdf->setLineStyle(1.0,'','','','');
$pdf->rectangle(20,40,200,100);
$pdf->setLineStyle(0.50,'','','','');
$pdf->line(20,100,220,100);
$pdf->line(20,80,220,80);
$pdf->line(20,60,220,60);
$pdf->line(20,40,220,40);
$pdf->line(50,40,50,100);
$pdf->line(135,40,135,100);

$pdf->addText(30,87,10,"4 %");
$pdf->addText(30,67,10,"7 %");
$pdf->addText(25,47,10,"16 %");

$pdf->addText(80,127,10,"I.V.A. Desglosado");
$pdf->addText(27,107,8,"Tipo");
$pdf->addText(67,107,8,"Base Imponible");
$pdf->addText(167,107,8,"I.V.A.");
*/
// Cuadro Forma de pago
$x_forma_de_pago=20;
$pdf->setColor('0.80','0.85','0.90','');
$pdf->filledRectangle($x_forma_de_pago,100,150,40);
$pdf->setColor('00','00','0.50','');
$pdf->setLineStyle(1.0,'','','','');
$pdf->rectangle($x_forma_de_pago,40,150,100);
$pdf->setLineStyle(0.50,'','','','');
$pdf->line($x_forma_de_pago,100,$x_forma_de_pago+150,100);

$pdf->addText($x_forma_de_pago+40,127,10,"Forma de Pago");

$pdf->ezSetY(85);
$pdf->ezText($forma_de_pago,8,array('justification'=>'center','aright'=>$x_forma_de_pago+150));

// Totales
$pdf->selectFont('./fonts/Helvetica-Bold');
if ($total_iva!=0) {
$pdf->addText(385,127,10,"BASE IMPONIBLE:");
$pdf->rectangle(475,125,97,14);
$pdf->addText(444,107,10,"I.V.A.:");
$pdf->rectangle(475,105,97,14);
}
if ($total_recargo!=0) {
        $pdf->addText(449,87,10,"R.E.:");
        $pdf->rectangle(475,85,97,14);
        }
$pdf->addText(385,127,10,"TOTAL FACTURA:");
$pdf->rectangle(475,125,97,14);


$pdf->line(20,145,572,145);

// INSERTA TOTALES
$pdf->selectFont('./fonts/Helvetica');
$pdf->setColor('00','00','0.00','');
if ($total_iva!=0) {
$pdf->ezSetY(139);
$pdf->ezText($total_base." €    ",10,array('justification'=>'right'));
$pdf->ezSetY(119);
$pdf->ezText($total_iva." €    ",10,array('justification'=>'right'));
}
if ($total_recargo!=0) {
        $pdf->ezSetY(98);
        $pdf->ezText(($total_recargo)." €    ",10,array('justification'=>'right'));
        }
$pdf->ezSetY(139);
$pdf->ezText($total_factura." €    ",10,array('justification'=>'right'));

/*
// Desglose IVA
// $pdf->addText(60,87,10,$total_base_4." €.");
$pdf->ezSetY(98);
$pdf->ezText($total_base_4." €",10,array('justification'=>'right','aright'=>125));
$pdf->ezSetY(98);
$pdf->ezText($total_iva_4." €",10,array('justification'=>'right','aright'=>210));
$pdf->ezSetY(78);
$pdf->ezText($total_base_7." €",10,array('justification'=>'right','aright'=>125));
$pdf->ezSetY(78);
$pdf->ezText($total_iva_7." €",10,array('justification'=>'right','aright'=>210));
$pdf->ezSetY(58);
$pdf->ezText($total_base_16." €",10,array('justification'=>'right','aright'=>125));
$pdf->ezSetY(58);
$pdf->ezText($total_iva_16." €",10,array('justification'=>'right','aright'=>210));
*/

$pdf->setColor('0.00','0.00','0.00','');
$pdf->addText(20,30,6,"ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia. Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123");

// Fin encabezado y pie de página
?>
