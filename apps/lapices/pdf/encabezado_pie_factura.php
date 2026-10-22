<?php 
// Aquí el logo

$serie_factura=$datos_de_factura["serie"];

$x_logo=75;
$y_logo=715;

$logo_factura = 'logo_redondo.jpg';

if ($datos_de_factura["centro_factura"]==14) {        
        $logo_factura = 'logo_redondo_14.jpg';
}

$pdf->addJpegFromFile($logo_factura,$x_logo,$y_logo,125,125);

if ($es_albaran==0) {
        $x_logo_iso=108;
        $y_logo_iso=655;
        $pdf->addJpegFromFile('logo_iso.jpg',$x_logo_iso,$y_logo_iso,60,28);
        }
        
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('1.00','1.00','1.00','');
$pdf->rectangle(250,656,320,104);

$pdf->setColor('00','00','0.50','');
$pdf->selectFont('./fonts/Helvetica-Bold');
if ($es_albaran==0) {
        $pdf->addText(255,790,12,"FACTURA Nº:");
        }
$pdf->addText(455,790,12,"FECHA:");
$pdf->addText(255,740,10,"CLIENTE:");
if ($es_albaran==0) {
        $pdf->addText(255,726,10,"C.I.F.:");
        }
$pdf->addText(255,712,10,"ALUMNO:");
if ($mostrar_domicilio==1) {
        $pdf->addText(255,700,10,"DOMICILIO:");
        }
$pdf->addText(255,684,10,"PROVINCIA:");
$pdf->addText(455,684,10,"CP:");
$pdf->addText(255,670,10,"POBLACIÓN:");

$pdf->setLineStyle(0.50,'','','','');
$pdf->line(30,650,572,650);

$pdf->setColor('00','00','0.00','');

// Escribimos datos del centro

$busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_de_factura["centro_factura"]);
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
$busca_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_de_factura["aula"]);
if (db_num_rows($busca_aula)!=0) {
$datos_aula=db_fetch_array($busca_aula);
$nombre_aula=$datos_aula["nombre"];
$ciclo=$datos_aula["ciclo"];
$edad_aula=$datos_aula["edad_maxima"]-1;

$id_curso_contacto=$datos_aula["id_curso"];



// Numero de aulas del 2º ciclo
$busca_aulas2_ciclo=db_query("SELECT * FROM centro_aulas WHERE id_curso=$id_curso_contacto AND ciclo=2") or die(db_error()."-2");
$numero_aulas2_ciclo=db_num_rows($busca_aulas2_ciclo);

}


// Datos subvenciones
if ((strlen($datos_de_factura["centro_factura"])!=0) and (strlen($id_curso_contacto)!=0)) {
        $busca_datos_curso_centro=db_query("SELECT * FROM centros_cursos WHERE id_centro=".$datos_de_factura["centro_factura"]." AND id_curso=$id_curso_contacto");

        if (db_num_rows($busca_datos_curso_centro)!=0) {
                $datos_curso_centro=db_fetch_array($busca_datos_curso_centro);

                $tope_mes[0]=$datos_curso_centro["tope_mes_01"];
                $tope_mes[1]=$datos_curso_centro["tope_mes_12"];
                $tope_mes[2]=$datos_curso_centro["tope_mes_23"];

                $precio_coste[0]=$datos_curso_centro["precio_coste_01"];
                $precio_coste[1]=$datos_curso_centro["precio_coste_12"];
                $precio_coste[2]=$datos_curso_centro["precio_coste_23"];

                $tope_maximo2=$datos_curso_centro["tope_maximo2"];
                $subvencion2=$datos_curso_centro["subvencion2"];

                $alumnos_ciclo2=$datos_curso_centro["alumnos_ciclo2"];
                $ocupacion_actual=$alumnos_ciclo2;
                }

        }


// Escribimos datos del cliente
if ($datos_de_factura["Provincia"]!=0) {
$busca_provincia=db_query("SELECT * FROM provincias WHERE id_provincia=".$datos_de_factura["Provincia"]);
if (db_num_rows($busca_provincia)!=0) {
$datos_provincia=db_fetch_array($busca_provincia);
$nombre_provincia=$datos_provincia["provincia"];
} else {
$nombre_provincia="";
}
} else {
$nombre_provincia="";
}
$pdf->selectFont('./fonts/Helvetica');
$pdf->addText(310,740,10,$datos_de_factura["banco_titular"]);
if ($es_albaran==0) {
        $pdf->addText(310,726,10,$datos_de_factura["banco_dni_titular"]);
        }
$pdf->addText(310,712,10,$datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"].", ".$datos_de_factura["Nombre"]);
if ($mostrar_domicilio==1) { 
        $pdf->addText(314,700,10,$datos_de_factura["Direccion"]);
        } else {
        $pdf->addText(310,700,10,$nombre_aula);
        }
$pdf->addText(320,684,10,$nombre_provincia);
$pdf->addText(475,684,10,$datos_de_factura["cp"]);
$pdf->addText(320,670,10,$datos_de_factura["Poblacion"]);

$es_copia="";
if ($copia==1) {
        // $es_copia=" - DUPLICADO -";
        }

// Escribimos datos de factura
if ($datos_de_factura["serie"]!="EDU") {
                                        $num_factura="R".$datos_de_factura["id_factura"];
                                        } else {
                                        $num_factura=$serie_factura.$datos_de_factura["num_factura"];
                                        }

if ($es_albaran==0) {
        $pdf->addText(340,790,12,$num_factura."/ ".$datos_de_factura["ejercicio_factura"]);
        $pdf->addText(340,778,12,$es_copia);
        }
$pdf->addText(505,790,12,date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])));

if ($es_albaran==0) {
        // $pdf->selectFont('./fonts/Helvetica-Bold');
        // $pdf->addText(420,720,8,$razon_social);
        $pdf->selectFont('./fonts/Helvetica');
        $pdf->ezSetY(720);
        $pdf->ezText($domicilio,8,array('justification'=>'center','aleft'=>-300));
        $pdf->ezSetY(710);
        $pdf->ezText("$cp $poblacion",8,array('justification'=>'center','aleft'=>-300));
        $pdf->ezSetY(700);
        $pdf->ezText("Teléfono: $telefono",8,array('justification'=>'center','aleft'=>-300));
        // $pdf->addText(400,710,7,$domicilio);
        // $pdf->addText(445,700,7,"$cp $poblacion");
        /* $pdf->addText(445,687,7,"Teléfono: $telefono");
        if (strlen($fax)!=0) {
                $pdf->addText(440,677,7,"Fax: $fax");
                }
        $pdf->addText(446,665,8,"C.I.F.: $cif");
        */
        }

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

if ($es_albaran==0) {
        // Cuadro Forma de pago
        $x_forma_de_pago=30;
        $pdf->setColor('0.80','0.85','0.90','');
        $pdf->filledRectangle($x_forma_de_pago,100,150,40);
        $pdf->setColor('00','00','0.50','');
        $pdf->setLineStyle(1.0,'','','','');
        $pdf->rectangle($x_forma_de_pago,40,150,100);
        $pdf->setLineStyle(0.50,'','','','');
        $pdf->line($x_forma_de_pago,100,$x_forma_de_pago+150,100);

        $pdf->addText($x_forma_de_pago+40,127,10,"Forma de Pago");

        $pdf->ezSetY(85);
        $pdf->ezText($forma_de_pago,8,array('justification'=>'center','aright'=>$x_forma_de_pago+160));

        // Cuadro observaciones
        $pdf->ezText($observaciones_otras_series,8,array('justification'=>'center'));
        }

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
if ($es_albaran==0) {
        $pdf->addText(385,127,10,"TOTAL FACTURA:");
        } else {
        $pdf->addText(385,127,10,"TOTAL:");
        }
        $pdf->rectangle(475,125,97,14);


$pdf->line(30,145,572,145);

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
if ($es_albaran==0) {
        $pdf->setColor('0.00','0.00','0.00','');
$pdf->ezSetY(100);
$pdf->ezText("ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia.
Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123.
De conformidad con lo establecido en el Art. 13 del Nuevo Reglamento general de protección de Datos (UE) 2016/679, le informamos que sus datos personales serán incorporados al sistema de tratamiento titularidad de la empresa ZAEBSAL S.L, con la finalidad de poder emitir adecuadamente la factura de los productos adquiridos y en base a una legitimación basada por cumplimiento de una obligación legal. Los datos serán utilizados únicamente a efectos de gestión y facturación, siendo cedidos a las administraciones publicas correspondientes, conforme a la legislación tributaria vigente. ZAEBSAL S.L le informa que procederá a tratar los datos de manera lícita, leal, transparente, adecuada, pertinente, limitada, exacta y actualizada y por ello nos comprometemos a adoptar todas las medidas técnicas y organizativas para su tratamiento. En cumplimiento con la normativa vigente, la empresa ZAEBSAL S.L le informa que sus datos serán conservados mientras se mantenga la relación comercial o profesional y/o durante los años necesarios para cumplir con las obligaciones legales en materia fiscal y contable, según la Ley General Tributaria.
Así mismo le informamos que si desea ejercer sus derechos de acceso, rectificación, limitación de tratamiento, supresión, portabilidad y oposición, puede hacerlo dirigiéndose a nuestras oficinas sitas AVENIDA GENERAL AVILÉS, 54. 46015 VALENCIA.",5,array('justification'=>'full','aleft'=>190));
        }

// Fin encabezado y pie de página
?>
