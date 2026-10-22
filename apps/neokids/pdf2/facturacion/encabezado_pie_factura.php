<?php 
// Aquí el logo

$serie_factura=$datos_de_factura["serie"];

$x_logo=10;
$y_logo=10;
$pdf->Image('logo_factura.jpg',$x_logo,$y_logo,55);
        
if (strlen($serie_factura)!=0) {
        $x_logo_iso=30;
        $y_logo_iso=53;
        $pdf->Image('logo_iso.jpg',$x_logo_iso,$y_logo_iso,20);
        }
        
$pdf->setLineStyle(1);
$pdf->SetDrawColor(0,0,128);
$pdf->rect(88,25,112,37);

$pdf->SetTextColor(0,0,128);
$pdf->SetFont('helveticab', '', 12);
if (strlen($serie_factura)!=0) {
        $pdf->Text(90,10,"FACTURA Nº:");
        }

$pdf->Text(160,10,"FECHA:");

$pdf->SetFont('helveticab', '', 10);
$pdf->Text(90,28,"CLIENTE:");
if (strlen($serie_factura)!=0) {
        $pdf->Text(90,33,"C.I.F.:");
        }
$pdf->Text(90,38,"ALUMNO:");
if ($mostrar_domicilio==1) {
        $pdf->Text(160,38,"DOMICILIO:");
        }
$pdf->Text(90,48,"PROVINCIA:");
$pdf->Text(160,48,"CP:");
$pdf->Text(90,53,"POBLACION:");

$pdf->setLineStyle(0.50);
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
$pdf->Text(114,28,$datos_de_factura["banco_titular"]);
if (strlen($serie_factura)!=0) {
        $pdf->Text(114,33,$datos_de_factura["banco_dni_titular"]);
        }
$pdf->Text(114,38,$datos_de_factura["Apellido1"]." ".$datos_de_factura["Apellido2"].", ".$datos_de_factura["Nombre"]);
if ($mostrar_domicilio==1) { 
        $pdf->Text(114,43,$datos_de_factura["Direccion"]);
        } else {
        $pdf->Text(114,43,$nombre_aula);
        }
$pdf->Text(114,48,$nombre_provincia);
$pdf->Text(168,48,$datos_de_factura["cp"]);
$pdf->Text(114,53,$datos_de_factura["Poblacion"]);

$es_copia="";
if ($copia==1) {
        // $es_copia=" - DUPLICADO -";
        }

// Escribimos datos de factura
$pdf->SetFont('helvetica', '', 12);
if (strlen($serie_factura)!=0) {
        $pdf->Text(120,10,$serie_factura.$datos_de_factura["num_factura"]."/ ".$datos_de_factura["ejercicio_factura"]);
        $pdf->Text(120,10,$es_copia);
        }
$pdf->Text(178,10,date("d/m/Y",strtotime($datos_de_factura["fecha_emision"])));

if (strlen($serie_factura)!=0) {
		$pdf->SetFont('helvetica','',8);
$texto_html=$domicilio.'
'.$cp.' '.$poblacion.'

Teléfono: '.$telefono;
        $pdf->MultiCell(60,10,$texto_html,0,'C',false,1,10,33);
        }

/*
if (strlen($serie_factura)!=0) {
        // Cuadro Forma de pago
        $x_forma_de_pago=30;
        $pdf->setColor('0.80','0.85','0.90','');
        $pdf->filledrect($x_forma_de_pago,100,150,40);
        $pdf->setColor('00','00','0.50','');
        $pdf->setLineStyle(1.0,'','','','');
        $pdf->rect($x_forma_de_pago,40,150,100);
        $pdf->setLineStyle(0.50,'','','','');
        $pdf->line($x_forma_de_pago,100,$x_forma_de_pago+150,100);

        $pdf->Text($x_forma_de_pago+40,127,10,"Forma de Pago");

        $pdf->ezSetY(85);
        $pdf->ezText($forma_de_pago,8,array('justification'=>'center','aright'=>$x_forma_de_pago+160));

        // Cuadro observaciones
        $pdf->ezText($observaciones_otras_series,8,array('justification'=>'center'));
        }

// Totales
$pdf->SetFont('helvetica');
if ($total_iva!=0) {
$pdf->Text(385,127,10,"BASE IMPONIBLE:");
$pdf->rect(475,125,97,14);
$pdf->Text(444,107,10,"I.V.A.:");
$pdf->rect(475,105,97,14);
}
if ($total_recargo!=0) {
        $pdf->Text(449,87,10,"R.E.:");
        $pdf->rect(475,85,97,14);
        }
if (strlen($serie_factura)!=0) {
        $pdf->Text(385,127,10,"TOTAL FACTURA:");
        } else {
        $pdf->Text(385,127,10,"TOTAL:");
        }
        $pdf->rect(475,125,97,14);


$pdf->line(30,145,572,145);

// INSERTA TOTALES
$pdf->SetFont('helvetica');
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
// $pdf->Text(60,87,10,$total_base_4." €.");
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

/*
if (strlen($serie_factura)!=0) {
        $pdf->setColor('0.00','0.00','0.00','');
        $pdf->Text(30,30,6,"ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia. Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123");
        }
*/
// Fin encabezado y pie de página
?>
