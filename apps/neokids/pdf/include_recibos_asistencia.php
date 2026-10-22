<?php 

$mostrar_solo_fecha_de_hoy=$_GET["hoy"];
$copia=$_GET["copia"];
$original=$_GET["original"];

if (($datos_factura["num_impresiones_recibo"]!=0) and ($original!=1)) {
	$copia=1;
}

// Busca contraseñas padres

$busca_padres_alumnos_escuela=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.padre_de=? LIMIT 0,1', 's', array($datos_alumno["id_usuario"])) or die(db_error());

// Busca padres

if (time()>=strtotime('2017-10-01 00:00:00')) {
	
	$texto_cambia_fecha="Existe";
	
} else {
	
	$texto_cambia_fecha="A partir del día 1 de octubre de 2017 estará disponible";	
	
}

$texto_acceso_web="";

while($padres_alumnos_escuela=db_fetch_array($busca_padres_alumnos_escuela)){
	
	$texto_acceso_web=$texto_cambia_fecha." una zona privada en la web de La Piscina de Neokids donde los usuarios podrán acceder a distintos servicios.
	El acceso a la zona privada se realiza desde la URL: http://gestion.neo-kids.es/clientes introduciendo el siguiente nombre de usuario y contraseña.
	
	USUARIO: ".$padres_alumnos_escuela["usuario_webcam"]." - CONTRASEÑA: ".security_family_password($padres_alumnos_escuela)."
	";

}

// Actualiza numero de impresiones
$contador_impresiones=db_query_params('UPDATE facturas_emitidas SET num_impresiones_recibo=num_impresiones_recibo+1 WHERE id_factura=?', 's', array($datos_factura["id_factura"]));

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
$media_hoja=round(($altura_hoja/2),2);
$anchura_recibo=595.28;
$margen_horizontal=25;
$margen_vertical=25;
$altura_logo=round(($altura_recibo/2),2);

$texto_concepto=$datos_pago["comentario"];

$posicion_actual=$altura_hoja;

$x_logo=$margen_horizontal+50;
$y_logo=$posicion_actual-$altura_logo;
$pdf->addJpegFromFile('logo_recibo.jpg',$x_logo,$y_logo,$altura_logo,$altura_logo);

$xRectanguloFirma1=($margen_horizontal+365);
$yRectanguloFirma1=($posicion_actual-$altura_recibo+$margen_vertical+5);
$wImagenFirma=170;
$hImagenFirma=102;
$pdf->addJpegFromFile('firma_recibos.jpg',$xRectanguloFirma1,$yRectanguloFirma1-35,$wImagenFirma,$hImagenFirma);

$pdf->setLineStyle(1,'','','','');
$pdf->setColor('1.00','1.00','1.00','');
$pdf->rectangle($margen_horizontal,($posicion_actual-$altura_recibo+$margen_vertical),($anchura_recibo-($margen_horizontal*2)),($altura_recibo-($margen_vertical*2)-47));

$pdf->setColor('0.80','0.85','0.90','');
$pdf->setLineStyle(0.25,'','','','');
$pdf->rectangle(($margen_horizontal+72),($posicion_actual-$margen_vertical-111),464,58.5);
$pdf->filledRectangle(($margen_horizontal+10),$posicion_actual-176,526,28);
$pdf->filledRectangle(($margen_horizontal+10),$posicion_actual-191,526,13);
$pdf->rectangle(($margen_horizontal+365),($posicion_actual-$altura_recibo+$margen_vertical+5),170,55);
$pdf->setColor('00','00','0.50','');

$posicion_texto=$posicion_actual-$margen_vertical-20;

$pdf->setColor('00','00','0.50','');
$pdf->selectFont('./fonts/Helvetica-Bold');
if ($num_vencimientos>1) {
	if (strlen($datos_de_factura["serie"])!=0) {
        $pdf->addText(304,$posicion_texto,16,"Pago ".$nv." de ".$num_vencimientos." del RECIBO Nº: ".$prefijo_centro.$id_asistencia.".$id_pago/ ".date("Y"));
        } else {
        $pdf->addText(304,$posicion_texto,16,"Pago ".$nv." de ".$num_vencimientos." del RECIBO Nº: ".$datos_factura["id_factura"]);
        }
} else {
	if (strlen($datos_de_factura["serie"])!=0) {
        $pdf->addText(354,$posicion_texto,16,"RECIBO Nº: ".$prefijo_centro.$id_asistencia.".$id_pago/ ".date("Y"));
        } else {
        $pdf->addText(354,$posicion_texto,16,"RECIBO Nº: ".$datos_factura["id_factura"]);
        }
}      
if ($copia==1) {
        // $posicion_texto=$posicion_texto-20;
        $pdf->setColor('1','00','0.00','');
        $pdf->addText(200,$posicion_texto,16,"***COPIA***");
        // $posicion_texto=$posicion_texto+20;
        }
$pdf->setColor('00','00','0.00','');
$posicion_texto=$posicion_texto-20;

if ((strlen($datos_factura["fecha_recibo"])!=0) and ($datos_factura["fecha_recibo"]!='0000-00-00')) {
        $fecha_recibo=date("d/m/Y",strtotime($datos_factura["fecha_recibo"]));
} else {
        $fecha_recibo=date("d/m/Y",strtotime($datos_factura["fecha_emision"]));
}

$pdf->addText(300,$posicion_texto,10,"Fecha emisión: ".$fecha_recibo."    Fecha cobro: ".$fecha_pago);

$pdf->selectFont('./fonts/Helvetica');

$posicion_texto=$posicion_texto-9;
$pdf->ezSetY($posicion_texto);
$pdf->ezText("Recibí de: ",10,array('justification'=>'right','aleft'=>$margen_horizontal+10,'aright'=>($margen_horizontal+70)));
$pdf->selectFont('./fonts/Helvetica');
$parte2_nombre_recibo=$nombre_alumno_facturas;
if (strlen($parte2_nombre_recibo)!=0) {
        $parte2_nombre_recibo=" - Alumno: ".$parte2_nombre_recibo.", Grupos: ".$lista_grupos_alumno;
        }
$pdf->ezSetY($posicion_texto-1);
$pdf->ezText($nombre_cliente_facturas.$parte2_nombre_recibo."
Por el servicio: ".$servicios_contratados,10,array('justification'=>'left','aleft'=>($margen_horizontal+75),'aright'=>($anchura_recibo-$margen_horizontal)));

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

$posicion_texto=$posicion_texto-65;

$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("<b>Normas de la Piscina de Neokids</b>",10,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($anchura_recibo-$margen_horizontal)));
$posicion_texto=$posicion_texto-14;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_texto);
$pdf->ezText("1. Es obligatorio el uso de gorro, bañador y calcetines antideslizantes para los niños mayores de veinticuatro (24) meses, y pañal de agua y bañador para los niños menores de veinticuatro (24) meses.
2. Los padres que accedan a los vestuarios y a la zona del vaso de la piscina deben utilizar calzado adecuado (chanclas de goma). Si no disponen de él se les facilitarán bolsas cubre pies en la recepción.
3. Es obligatorio ducharse antes de entrar en la piscina.
4. Si el usuario de la piscina padece algún tipo de patología, su padre o tutor debe informar a los responsables de Neokids. Además, no se debe realizar actividad acuática si el alumno presenta fiebre o febrícula, es decir, si su temperatura corporal supera los treinta y siete grados y medio (37,5ºC). El incumplimiento de esta norma es responsabilidad exclusiva del familiar, tutor legal o responsable que acompaña al alumno. Además, para garantizar la salubridad e higiene de las instalaciones, los usuarios deben informar si padecen alguna enfermedad infecciosa o tienen alguna herida abierta.
5. Las clases de los niños pueden recuperarse cuando la ausencia a la misma sea por causa de fuerza mayor o prescripción médica, debidamente justificada, entre los que se destaca, a modo enunciativo, pero no limitativo, los siguientes supuestos: ingreso hospitalario, fracturas, esguinces, gastroenteritis, otitis, conjuntivitis, bronquiolitis, neumonía, varicela y/o enfermedades contagiosas que así se especifiquen en el informe médico. Las clases no podrán recuperarse si el motivo es un viaje, festival, graduación o evento similar. La recuperación se realizará en el período contratado y en los días marcados por la dirección de Neokids. No está contemplado la prolongación de ningún período.
6. Una vez abonado el importe del curso de natación y/o actividad educativa o recreativa por la duración seleccionada, si las familias/usuarios deciden voluntariamente dar de baja al alumno/a, no se reembolsará ningún importe de la cantidad abonada, salvo cuando se ejercite el derecho de desistimiento contemplado en la norma 16. 
7. La reserva de plaza de un curso a otro se realizará en los meses de junio y julio previa entrega de una cantidad fijada. Importe que se descontará del recibo del primer período y no será reembolsable. 
8. Se prohíbe el uso de teléfonos móviles en el interior del perímetro de la piscina y en los vestuarios. Queda totalmente prohibido realizar fotografías o grabación de imágenes en los vestuarios. La realización de fotografías o grabación de imágenes descritas en este punto podrá ser sancionada bajo multa económica de cien euros (100,00 €) y/o expulsión definitiva de las instalaciones de la piscina, sin perjuicio de las acciones legales y de responsabilidad civil que puedan derivarse de dicho acto. 
9. Los únicos elementos flotantes permitidos dentro del vaso de la piscina son aquellos proporcionados y homologados por los monitores de Neokids.
10. No está permitida la entrada de animales, a excepción de aquellos cuyo uso este prescrito médica o judicialmente y además presenten condiciones de higiene excelentes, como por ejemplo, el uso de perros guía
11. Queda prohibido introducir alimentos o material de vidrio en los vestuarios y en el perímetro de la piscina.
12. No está permitido correr en las instalaciones.
13. Únicamente puede acceder al vestuario un adulto por niño, salvo en casos concretos en los que, por motivo médico debidamente justificado, el cuidado del menor haga necesaria la presencia de dos adultos simultáneamente.
14. Los grupos dentro de los diferentes cursos que se imparten en las instalaciones se formarán a partir de tres niños en infantil y cinco en bebes. Los grupos de nivel bajo (verde) podrán tener un máximo de cinco alumnos, y los niveles (azul) y alto (rojo) podrán tener un máximo de seis alumnos por calle. El nivel de los alumnos será evaluado por los monitores y profesionales de Neokids.
15. El pago de los períodos de los cursos se realiza en los quince (15) días previos a su inicio. De no ser así, la empresa entiende que el alumno no desea continuar y se reserva el derecho de proceder a su baja. 
16. El alumno tiene derecho a desistir de cualquier contrato de servicios que celebre con Neokids en el plazo de catorce (14) días naturales sin necesidad de justificación salvo en aquellos supuestos en los que la Ley establezca un plazo diferente. El plazo de desistimiento expirará a los catorce (14) días naturales a contar desde el día de la celebración del contrato. Para ejercer el derecho de desistimiento previsto en el presente punto, el alumno deberá notificar a Neokids su decisión de desistir del contrato a través de una declaración inequívoca.
17. Los horarios, los grupos y los monitores de Neokids están sujetos a variaciones de un curso a otro en beneficio de la mejor organización de todos los alumnos.
18. Una vez alcanzada la edad de dos (2) años y previa evaluación del monitor, los niños que pasen del grupo de bebé al de infantil se les ofrecerá una plaza exclusivamente en el horario de las 19:25h. Aquellos que, al cumplir dos (2) años, no quieran pasar al grupo infantil, tendrán la opción de permanecer en el grupo de bebés solo un trimestre más. En ambos casos, se les ofrecerá la opción de cambiar de horario al terminar cada trimestre, siempre y cuando quedasen plazas disponibles.

El cumplimiento de esta normativa persigue garantizar  una correcta convivencia, el respeto a los demás y el buen manejo de las instalaciones, con el fin de conseguir su máximo aprovechamiento en condiciones de seguridad y comodidad.
",4,array('justification'=>'full','aleft'=>$margen_horizontal+4,'aright'=>($anchura_recibo-$margen_horizontal)));

$pdf->ezText($texto_acceso_web,7,array('justification'=>'center','aleft'=>$margen_horizontal+4,'aright'=>($anchura_recibo-$margen_horizontal)));

$pdf->ezText("De acuerdo a lo establecido en la Ley Orgánica 15/1999, de 13 de diciembre, de Protección de Datos de Carácter Personal, en lo que concierne al tratamiento de los datos personales, las libertades públicas y los derechos fundamentales de las personas físicas, y especialmente de su honor, intimidad y privacidad personal y familiar. Está ley se desarrolla fundamentándose en el artículo 18 de la constitución española de 1978, sobre el derecho a la intimidad familiar y personal y el secreto de las comunicaciones.
Le  informamos de que los datos personales que nos facilite, serán incorporados en un fichero titularidad de NEOKIDS S.L , con domicilio en Avda. General Avilés, 54 - 46015 - VALENCIA que cumple con las medidas de seguridad exigidas en el Real Decreto 1720/2007 de 21 de diciembre, y cuya finalidad es la inscripción , control y gestión de nuestros alumnos matriculados en el centro, y así como el envío  de publicidad de los contenidos comerciales.
Asimismo le informamos que durante la actividad podrían ser realizadas fotografías de forma Particular por parte de los PADRES/TUTORES de los alumnos,  siendo responsabilidad de estos  y no del CENTRO el mal uso de las mismas o el poder  incluirlas en REDES SOCIALES a las que pertenezcan los mismos.
Como padre/ Tutor podrá ejercitar los derechos de acceso, rectificación, cancelación y oposición, comunicándolo por escrito a ( marketing@neo-kids.es).",5,array('justification'=>'full','aleft'=>$margen_horizontal+4,'aright'=>($anchura_recibo-$margen_horizontal)));

$posicion_actual=$posicion_actual-$media_hoja-80;


$xRectangulo=($posicion_actual-($altura_recibo/3))-50;
$yRectangulo=($anchura_recibo-($margen_horizontal*2));

$posicion_texto=$posicion_actual-20;

$pdf->setColor('00','00','0.50','');
$pdf->selectFont('./fonts/Helvetica-Bold');
if (strlen($datos_de_factura["serie"])!=0) {
        $pdf->addText($margen_horizontal+4,$posicion_texto,16,"RECIBO Nº: ".$datos_de_factura["serie"].$datos_factura["num_factura"].".$id_pago/ ".$datos_factura["ejercicio_factura"]);
        } else {
        $pdf->addText($margen_horizontal+4,$posicion_texto,16,"RECIBO Nº: ".$datos_factura["id_factura"]);
        }

$xImagenFirma=($margen_horizontal+365);
$yImagenFirma=($posicion_actual-($altura_recibo/3)+55)-100;
$wImagenFirma=170;
$hImagenFirma=102;

// Inserta firma si existe
if (strlen($datos_factura["firma"])!=0) {

        $nombreImagen = "firma-".$id_factura."-".date("ymdHis");

        $nombreArchivoImagen = dirname(__DIR__) . '/pdf/tmp/firmas/'.$nombreImagen;

        // Crea archivo temporal con imagen de la firma

        $img = $datos_factura["firma"]; // Your data 'data:image/png;base64,AAAFBfj42Pj4';
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        file_put_contents($nombreArchivoImagen.".png", $data);

        // Convierte a JPG
        $image = imagecreatefrompng($nombreArchivoImagen.".png");
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file 
        imagejpeg($bg, $nombreArchivoImagen . ".jpg", $quality);
        imagedestroy($bg);        

        $pdf->addJpegFromFile('./tmp/firmas/'.$nombreImagen.'.jpg',$xImagenFirma,$yImagenFirma-25,$wImagenFirma,$hImagenFirma);

        // Borra archivos temporales
        unlink('./tmp/firmas/'.$nombreImagen.'.jpg');
        unlink('./tmp/firmas/'.$nombreImagen.'.png');

}

$pdf->setColor('00','00','0.00','');

if ((strlen($datos_factura["fecha_recibo"])!=0) and ($datos_factura["fecha_recibo"]!='0000-00-00')) {
        $fecha_recibo=date("d/m/Y",strtotime($datos_factura["fecha_recibo"]));
} else {
        $fecha_recibo=date("d/m/Y",strtotime($datos_factura["fecha_emision"]));
}

$pdf->addText(300,$posicion_texto,10,"Fecha emisión: ".$fecha_recibo."    Fecha cobro: ".$fecha_pago);
$posicion_texto=$posicion_texto-7;
$pdf->ezSetY($posicion_texto);
$pdf->ezText("<b>Justificante de entrega</b>",10,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($margen_horizontal+362)));
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("Se me ha entregado el recibo por el servicio:".$servicios_contratados."
".substr($parte2_nombre_recibo,3,strlen($parte2_nombre_recibo)-3),9,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($margen_horizontal+362)));
$pdf->ezText("<b>Importe abonado:</b> ".number_format($importe_pago, 2, ',', '.')." €",9,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($margen_horizontal+362)));
$posicion_texto=$posicion_texto-57;
$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("Firmado, ".$nombre_cliente_facturas,6,array('justification'=>'left','aleft'=>($margen_horizontal+367),'aright'=>($anchura_recibo-$margen_horizontal)));

// Rectangulos
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('1.00','1.00','1.00','');
$pdf->rectangle($margen_horizontal,$xRectangulo,$yRectangulo,($altura_recibo/3)+50);
$pdf->setColor('0.80','0.85','0.90','');
$pdf->setLineStyle(0.25,'','','','');
$pdf->rectangle($xImagenFirma,$yImagenFirma,170,55);  
$pdf->setColor('00','00','0.00','');

$posicion_texto=$posicion_texto-60;
$pdf->ezSetY($posicion_texto);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("<b>Normas de la Piscina de Neokids</b>",10,array('justification'=>'left','aleft'=>$margen_horizontal+4,'aright'=>($anchura_recibo-$margen_horizontal)));
$posicion_texto=$posicion_texto-13;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_texto);
$pdf->ezText("1. Es obligatorio el uso de gorro, bañador y calcetines antideslizantes para los niños mayores de veinticuatro (24) meses, y pañal de agua y bañador para los niños menores de veinticuatro (24) meses.
2. Los padres que accedan a los vestuarios y a la zona del vaso de la piscina deben utilizar calzado adecuado (chanclas de goma). Si no disponen de él se les facilitarán bolsas cubre pies en la recepción.
3. Es obligatorio ducharse antes de entrar en la piscina.
4. Si el usuario de la piscina padece algún tipo de patología, su padre o tutor debe informar a los responsables de Neokids. Además, no se debe realizar actividad acuática si el alumno presenta fiebre o febrícula, es decir, si su temperatura corporal supera los treinta y siete grados y medio (37,5ºC). El incumplimiento de esta norma es responsabilidad exclusiva del familiar, tutor legal o responsable que acompaña al alumno. Además, para garantizar la salubridad e higiene de las instalaciones, los usuarios deben informar si padecen alguna enfermedad infecciosa o tienen alguna herida abierta.
5. Las clases de los niños pueden recuperarse cuando la ausencia a la misma sea por causa de fuerza mayor o prescripción médica, debidamente justificada, entre los que se destaca, a modo enunciativo, pero no limitativo, los siguientes supuestos: ingreso hospitalario, fracturas, esguinces, gastroenteritis, otitis, conjuntivitis, bronquiolitis, neumonía, varicela y/o enfermedades contagiosas que así se especifiquen en el informe médico. Las clases no podrán recuperarse si el motivo es un viaje, festival, graduación o evento similar. La recuperación se realizará en el período contratado y en los días marcados por la dirección de Neokids. No está contemplado la prolongación de ningún período.
6. Una vez abonado el importe del curso de natación y/o actividad educativa o recreativa por la duración seleccionada, si las familias/usuarios deciden voluntariamente dar de baja al alumno/a, no se reembolsará ningún importe de la cantidad abonada, salvo cuando se ejercite el derecho de desistimiento contemplado en la norma 16. 
7. La reserva de plaza de un curso a otro se realizará en los meses de junio y julio previa entrega de una cantidad fijada. Importe que se descontará del recibo del primer período y no será reembolsable. 
8. Se prohíbe el uso de teléfonos móviles en el interior del perímetro de la piscina y en los vestuarios. Queda totalmente prohibido realizar fotografías o grabación de imágenes en los vestuarios. La realización de fotografías o grabación de imágenes descritas en este punto podrá ser sancionada bajo multa económica de cien euros (100,00 €) y/o expulsión definitiva de las instalaciones de la piscina, sin perjuicio de las acciones legales y de responsabilidad civil que puedan derivarse de dicho acto. 
9. Los únicos elementos flotantes permitidos dentro del vaso de la piscina son aquellos proporcionados y homologados por los monitores de Neokids.
10. No está permitida la entrada de animales, a excepción de aquellos cuyo uso este prescrito médica o judicialmente y además presenten condiciones de higiene excelentes, como por ejemplo, el uso de perros guía
11. Queda prohibido introducir alimentos o material de vidrio en los vestuarios y en el perímetro de la piscina.
12. No está permitido correr en las instalaciones.
13. Únicamente puede acceder al vestuario un adulto por niño, salvo en casos concretos en los que, por motivo médico debidamente justificado, el cuidado del menor haga necesaria la presencia de dos adultos simultáneamente.
14. Los grupos dentro de los diferentes cursos que se imparten en las instalaciones se formarán a partir de tres niños en infantil y cinco en bebes. Los grupos de nivel bajo (verde) podrán tener un máximo de cinco alumnos, y los niveles (azul) y alto (rojo) podrán tener un máximo de seis alumnos por calle. El nivel de los alumnos será evaluado por los monitores y profesionales de Neokids.
15. El pago de los períodos de los cursos se realiza en los quince (15) días previos a su inicio. De no ser así, la empresa entiende que el alumno no desea continuar y se reserva el derecho de proceder a su baja. 
16. El alumno tiene derecho a desistir de cualquier contrato de servicios que celebre con Neokids en el plazo de catorce (14) días naturales sin necesidad de justificación salvo en aquellos supuestos en los que la Ley establezca un plazo diferente. El plazo de desistimiento expirará a los catorce (14) días naturales a contar desde el día de la celebración del contrato. Para ejercer el derecho de desistimiento previsto en el presente punto, el alumno deberá notificar a Neokids su decisión de desistir del contrato a través de una declaración inequívoca.
17. Los horarios, los grupos y los monitores de Neokids están sujetos a variaciones de un curso a otro en beneficio de la mejor organización de todos los alumnos.
18. Una vez alcanzada la edad de dos (2) años y previa evaluación del monitor, los niños que pasen del grupo de bebé al de infantil se les ofrecerá una plaza exclusivamente en el horario de las 19:25h. Aquellos que, al cumplir dos (2) años, no quieran pasar al grupo infantil, tendrán la opción de permanecer en el grupo de bebés solo un trimestre más. En ambos casos, se les ofrecerá la opción de cambiar de horario al terminar cada trimestre, siempre y cuando quedasen plazas disponibles.

El cumplimiento de esta normativa persigue garantizar  una correcta convivencia, el respeto a los demás y el buen manejo de las instalaciones, con el fin de conseguir su máximo aprovechamiento en condiciones de seguridad y comodidad.
",4,array('justification'=>'full','aleft'=>$margen_horizontal+4,'aright'=>($anchura_recibo-$margen_horizontal)));

$pdf->ezText("De acuerdo a lo establecido en la Ley Orgánica 15/1999, de 13 de diciembre, de Protección de Datos de Carácter Personal, en lo que concierne al tratamiento de los datos personales, las libertades públicas y los derechos fundamentales de las personas físicas, y especialmente de suhonor, intimidad y privacidad personal y familiar. Está ley se desarrolla fundamentándose en el artículo 18 de laconstitución española de 1978, sobre el derecho a la intimidad familiar y personal y el secreto de las comunicaciones.
Le  informamos de que los datos personales que nos facilite, serán incorporados en un fichero titularidad de NEOKIDS S.L , con domicilio en Avda. General Avilés, 54 - 46015 - VALENCIA que cumple con las medidas de seguridad exigidas en el Real Decreto 1720/2007 de 21 de diciembre, y cuya finalidad es la inscripción , control y gestión de nuestros alumnos matriculados en el centro, y así como el envío  de publicidad de los contenidos comerciales.
Asimismo le informamos que durante la actividad podrían ser realizadas fotografías de forma Particular por parte de los PADRES/TUTORES de los alumnos,  siendo responsabilidad de estos  y no del CENTRO el mal uso de las mismas o el poder  incluirlas en REDES SOCIALES a las que pertenezcan los mismos.
Como padre/ Tutor podrá ejercitar los derechos de acceso, rectificación, cancelación y oposición, comunicándolo por escrito a ( marketing@neo-kids.es).",4,array('justification'=>'full','aleft'=>$margen_horizontal+4,'aright'=>($anchura_recibo-$margen_horizontal)));


?>
