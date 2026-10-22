<?php 
// Aquí el logo

$serie_factura=$datos_de_factura["serie"];

$x_logo=10;
$y_logo=-5;
$pdf->Image('logo_factura'.$datos_de_factura["centro_factura"].'.jpg',$x_logo,$y_logo,70);
        
if ($es_albaran==0) {
        $x_logo_iso=35;
        $y_logo_iso=53;
        $pdf->Image('logo_iso.jpg',$x_logo_iso,$y_logo_iso,20);
        }
        
$pdf->setLineStyle(array('width' => 0.3));
$pdf->SetDrawColor(0,0,128);
$pdf->rect(88,25,112,37);

$pdf->SetTextColor(0,0,128);
$pdf->SetFont('helvetica', '', 12);
if ($es_albaran==0) {
        $pdf->Text(90,10,"FACTURA Nº:");
        }

$pdf->Text(160,10,"FECHA:");

$pdf->SetFont('helveticab', '', 10);
$pdf->Text(90,28,"CLIENTE:");
if ($es_albaran==0) {
        $pdf->Text(90,33,"C.I.F.:");
        }
$pdf->Text(90,38,"ALUMNO:");
if ($mostrar_domicilio==1) {
        $pdf->Text(160,38,"DOMICILIO:");
        }
$pdf->Text(90,48,"PROVINCIA:");
$pdf->Text(160,48,"CP:");
$pdf->Text(90,53,"POBLACION:");

$pdf->setLineStyle(array('width' => 0.1));
$pdf->line(10,63,200,63);

$pdf->SetDrawColor(0,0,0);
$pdf->SetTextColor(0,0,128);


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
$ciclo=$datos_aula["ciclo"];
$edad_aula=$datos_aula["edad_maxima"]-1;

$id_curso_contacto=$datos_aula["id_curso"];



// Numero de aulas del 2º ciclo
$busca_aulas2_ciclo=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=? AND ciclo=2', 's', array($id_curso_contacto)) or die(db_error()."-2");
$numero_aulas2_ciclo=db_num_rows($busca_aulas2_ciclo);

}


// Datos subvenciones
if ((strlen($datos_de_factura["centro_factura"])!=0) and (strlen($id_curso_contacto)!=0)) {
        $busca_datos_curso_centro=db_query_params('SELECT * FROM centros_cursos WHERE id_centro=? AND id_curso=?', 'ss', array($datos_de_factura["centro_factura"], $id_curso_contacto));

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
$pdf->SetTextColor(0,0,0);
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
$pdf->SetFont('helvetica', '', 10);
$pdf->Text(114,28,utf8_encode($datos_de_factura["banco_titular"]));
if ($es_albaran==0) {
        $pdf->Text(114,33,utf8_encode($datos_de_factura["banco_dni_titular"]));
        }
$pdf->Text(114,38,utf8_encode($datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"].", ".$datos_de_factura["Nombre"]));
if ($mostrar_domicilio==1) { 
        $pdf->Text(114,43,utf8_encode($datos_de_factura["Direccion"]));
        } else {
        $pdf->Text(114,43,utf8_encode($nombre_aula));
        }
$pdf->Text(114,48,utf8_encode($nombre_provincia));
$pdf->Text(168,48,utf8_encode($datos_de_factura["cp"]));
$pdf->Text(114,53,utf8_encode($datos_de_factura["Poblacion"]));

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

$pdf->SetFont('helvetica', '', 12);
if ($es_albaran==0) {
        $pdf->Text(120,10,$num_factura."/ ".$datos_de_factura["ejercicio_factura"]);
        $pdf->Text(120,10,$es_copia);
        }
$pdf->Text(178,10,date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])));

if ($es_albaran==0) {
		$pdf->SetFont('helvetica','',8);
$texto_html=($domicilio.' - '.$cp.' '.$poblacion.'
Teléfono: '.$telefono);
        $pdf->MultiCell(60,10,$texto_html,0,'C',false,1,15,41);
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
        
		$pdf->setLineStyle(array('width' => 0.3));		
		$pdf->SetDrawColor(0,0,128);
		// x1,y1,x2,y2
		$pdf->line(10,246,200,246);
		// x,y,w,h
		$pdf->rect(10,247,50,35); 
		       
		$pdf->SetFont('helvetica','',10);
		$pdf->SetFillColor(205, 218, 230);
		$pdf->setLineStyle(array('width' => 0.1));
		$pdf->SetTextColor(0,0,128);
        $pdf->MultiCell(50,5,"
Forma de Pago
        ",1,'C',true,1,10,247);     
		
		// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
		  
		$pdf->SetFont('helvetica','',8);
		$pdf->MultiCell(50,5,$forma_de_pago,0,'C',false,1,10,267); 
		       
        }


// Totales

$pdf->SetFont('helveticaB','',10);

if ($es_albaran==0) {
    $pdf->Text(120,248,"TOTAL FACTURA:");
    } else {
    $pdf->Text(120,248,"TOTAL:");
    }
$pdf->SetFont('helvetica','',10);
$pdf->MultiCell(45,5,$total_factura." €",1,'R',false,1,155,248);

/*
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
*/

if ($es_albaran==0) {
	$texto_legal="ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia.
Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123.
De conformidad con lo establecido en el Art. 13 del Nuevo Reglamento general de protección de Datos (UE) 2016/679, le informamos que sus datos personales serán incorporados al sistema de tratamiento titularidad de la empresa ZAEBSAL S.L, con la finalidad de poder emitir adecuadamente la factura de los productos adquiridos y en base a una legitimación basada por cumplimiento de una obligación legal. Los datos serán utilizados únicamente a efectos de gestión y facturación, siendo cedidos a las administraciones publicas correspondientes, conforme a la legislación tributaria vigente. ZAEBSAL S.L le informa que procederá a tratar los datos de manera lícita, leal, transparente, adecuada, pertinente, limitada, exacta y actualizada y por ello nos comprometemos a adoptar todas las medidas técnicas y organizativas para su tratamiento. En cumplimiento con la normativa vigente, la empresa ZAEBSAL S.L le informa que sus datos serán conservados mientras se mantenga la relación comercial o profesional y/o durante los años necesarios para cumplir con las obligaciones legales en materia fiscal y contable, según la Ley General Tributaria.
Así mismo le informamos que si desea ejercer sus derechos de acceso, rectificación, limitación de tratamiento, supresión, portabilidad y oposición, puede hacerlo dirigiéndose a nuestras oficinas sitas AVENIDA GENERAL AVILÉS, 54. 46015 VALENCIA.";

		$pdf->SetFont('helvetica','',5);
		$pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(138,27,$texto_legal,0,'L',false,1,62,256);

        }
        
// Fin encabezado y pie de página
?>
