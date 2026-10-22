<?php
// Datos

include "/s30/".$_GET["file"].".txt";

// --- FIN DATOS ---

// error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// select a font
$pdf->selectFont('./fonts/Times-Roman');

//$pdf->ezStartPageNumbers(530,30,8,'right','',0);

$size=12;
$height = $pdf->getFontHeight($size);

$altura_pagina=841.89;


if ($alumnos_curso>10) {
                       $numero_de_paginas=1+(ceil((count($alumnos_curso)-10)/15));
                       } else {
                       $numero_de_paginas=1;
                       }

for ($dias_lectivos=1; $dias_lectivos<=$total_sesiones; $dias_lectivos=$dias_lectivos+1) {

        $contador_paginas=1;
        // Dibujamos los rectangulos y las tablas

        // Color de las lineas
        $pdf->setStrokeColor('0.75','0.75','0.75','');

        $altura_fila=28;
        $numero_filas=11;
        $inicio_filas=135;
        $diferencia_1=86;

        $pdf->setLineStyle(1,'','','','');
        $pdf->setColor('1.00','1.00','1.00','');
        $pdf->filledRectangle(20,652,558,75);
        $pdf->filledRectangle(20,574-$diferencia_1,558,128);

        $pdf->selectFont('./fonts/Verdana-Italic');
        $pdf->setColor('0.75','0.75','0.75','');
        $pdf->addText(45,637,9,"(Se seleccionará uno de los apartados anteriores dependiendo del perfil con el que se haya accedido al Sistema telemático de");
        $pdf->addText(45,627,9,"Gestión de Acciones Formativas en las Empresas)");

        $pdf->setLineStyle(2,'','','','');
        $pdf->setColor('00','00','0.50','');
        $pdf->rectangle(20,652,558,75);
        $pdf->rectangle(20,574-$diferencia_1,558,128);
        $pdf->rectangle(20,40,558,80);

        for ($filas=1; $filas<=$numero_filas; $filas=$filas+1)
                {
                $pdf->rectangle(20,($inicio_filas-$altura_fila+($filas*$altura_fila)),558,$altura_fila);
                }
        $pdf->setColor('1.00','1.00','1.00','');
        $pdf->filledRectangle(20,($inicio_filas-$altura_fila+($numero_filas*$altura_fila)),558,$altura_fila*2);
        $pdf->setColor('00','00','0.50','');
        $pdf->rectangle(20,($inicio_filas-$altura_fila+($numero_filas*$altura_fila)),558,$altura_fila*2);

        //Lineas verticales gruesas
        $pdf->line(378,$inicio_filas,378,($inicio_filas+(($numero_filas+1)*$altura_fila)));

        //Lineas verticales finas
        $pdf->setLineStyle(1,'','','','');
        $pdf->line(40,$inicio_filas,40,($inicio_filas+(($numero_filas-1)*$altura_fila)));
        $pdf->line(178,$inicio_filas,178,($inicio_filas+($numero_filas*$altura_fila)));
        $pdf->line(278,$inicio_filas,278,($inicio_filas+($numero_filas*$altura_fila)));
        $pdf->line(478,$inicio_filas,478,($inicio_filas+(($numero_filas+1)*$altura_fila)));

        //Lineas horizontales finas
        $pdf->line(20,($inicio_filas+($numero_filas*$altura_fila)),378,($inicio_filas+($numero_filas*$altura_fila)));

        // Texto de la tabla
        $pdf->selectFont('./fonts/Verdana-Bold');
        $pdf->addText(128,(($altura_fila/2)+$inicio_filas+($numero_filas*$altura_fila)-4),12,"DATOS DE LOS ASISTENTES");
        $pdf->addText(65,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"APELLIDOS");
        $pdf->addText(205,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"NOMBRE");
        $pdf->addText(312,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"N.I.F.");
        $pdf->addText(412,(($altura_fila/2)+$inicio_filas+(($numero_filas-0.5)*$altura_fila)-4),10,"FIRMAS");
        $pdf->addText(485,(($altura_fila/2)+$inicio_filas+(($numero_filas-0.5)*$altura_fila)-4),10,"OBSERVACIONES");
        $pdf->addText(22,108,10,"OBSERVACIONES GENERALES:");


        // Aquí los logos
        $pdf->ezSetY(807);
        $pdf->ezImage("logos.jpg",0,558,array('resize'=>'width'),array('justification'=>'left'));
        //$pdf->ezSetY(807);
        //$pdf->ezImage("fso.jpg",0,60,array('resize'=>'width'),array('justification'=>'right'));

        // Ponemos el texto del encabezado
        $pdf->ezSetY(757);
        $pdf->ezText("CONTROL DE ASISTENCIA",16,array('justification'=>'center'));

        if ($opcion_fc_nueva="Falso") {
        $pdf->addText(30,710,10,"OPCIÓN A         EMPRESA BONIFICADA:");
        $pdf->addText(480,710,10,"C.I.F.:");
        $pdf->addText(30,680,10,"OPCIÓN B  X    ENTIDAD ORGANIZADORA: ".$entidad_solicitante);
        $pdf->addText(480,680,10,"C.I.F.: ".$cif_entidad);
        } else {
        $pdf->addText(30,710,10,"OPCIÓN A  X    EMPRESA BONIFICADA: ".$entidad_solicitante);
        $pdf->addText(480,710,10,"C.I.F.: ".$cif_entidad);
        $pdf->addText(30,680,10,"OPCIÓN B         ENTIDAD ORGANIZADORA:");
        $pdf->addText(480,680,10,"C.I.F.:");
        }

        $pdf->addText(104,660,10,"CÓDIGO DE AGRUPACIÓN:___________________________________________");
        $pdf->addText(30,688-$diferencia_1,10,"DENOMINACIÓN DE LA ACCIÓN FORMATIVA: ".$nombre_accion);
        $pdf->addText(30,668-$diferencia_1,10,"Nº: ".$num_accion."    GRUPO: ".$grupo_accion);
        $pdf->addText(259,668-$diferencia_1,10,"FECHA DE INICIO: ".$curso_inicio."    FECHA FIN: ".$curso_fin);
        $pdf->addText(30,648-$diferencia_1,10,"FORMADOR/RESPONSABLE DE FORMACIÓN: ".$formador_accion);
        $pdf->addText(30,628-$diferencia_1,10,"SESIÓN Nº: ".$dias_lectivos."    FECHA: ".$fecha_sesion[$dias_lectivos-1]);
        $pdf->addText(259,628-$diferencia_1,10,"MAÑANA/TARDE: ".$sesion_turno."    HORARIO: ".$sesion_horario);
        $pdf->addText(30,604-$diferencia_1,10,"FIRMADO:");
        $pdf->addText(30,594-$diferencia_1,10,"(Formador/Resp. Formación)");
        
        $pdf->selectFont('./fonts/Verdana-BoldItalic');
        $pdf->addText(530,30,8,"Hoja ".$contador_paginas." de ".$numero_de_paginas);

        // Escribimos los nombres de los alumnos, si hay más de 15 insertamos paginas nuevas
        reset($alumnos_curso);
        $inicio=(10+$inicio_filas+(($numero_filas-2)*$altura_fila));
        $contador=1;
        $contador_total=1;
        while (list($dni) = each($alumnos_curso)) {

        if ($contador==$numero_filas) {
                        $pdf->ezNewPage();
                        $contador_paginas=$contador_paginas+1;

                        $altura_fila=32;
                        $numero_filas=16;
                        $inicio_filas=235;

                        // Dibuja lineas y todo eso
                        $pdf->rectangle(20,40,558,180);

                        for ($filas=1; $filas<=$numero_filas; $filas=$filas+1)
                                {
                                $pdf->rectangle(20,($inicio_filas-$altura_fila+($filas*$altura_fila)),558,$altura_fila);
                                }
                        $pdf->setColor('1.00','1.00','1.00','');
                        $pdf->filledRectangle(20,($inicio_filas-$altura_fila+($numero_filas*$altura_fila)),558,$altura_fila*2);
                        $pdf->setColor('00','00','0.50','');
                        $pdf->rectangle(20,($inicio_filas-$altura_fila+($numero_filas*$altura_fila)),558,$altura_fila*2);

                        //Lineas verticales gruesas
                        $pdf->line(378,$inicio_filas,378,($inicio_filas+(($numero_filas+1)*$altura_fila)));

                        //Lineas verticales finas
                        $pdf->setLineStyle(1,'','','','');
                        $pdf->line(40,$inicio_filas,40,($inicio_filas+(($numero_filas-1)*$altura_fila)));
                        $pdf->line(178,$inicio_filas,178,($inicio_filas+($numero_filas*$altura_fila)));
                        $pdf->line(278,$inicio_filas,278,($inicio_filas+($numero_filas*$altura_fila)));
                        $pdf->line(478,$inicio_filas,478,($inicio_filas+(($numero_filas+1)*$altura_fila)));

                        //Lineas horizontales finas
                        $pdf->line(20,($inicio_filas+($numero_filas*$altura_fila)),378,($inicio_filas+($numero_filas*$altura_fila)));

                        // Texto de la tabla
                        $pdf->selectFont('./fonts/Verdana-Bold');
                        $pdf->addText(128,(($altura_fila/2)+$inicio_filas+($numero_filas*$altura_fila)-4),12,"DATOS DE LOS ASISTENTES");
                        $pdf->addText(65,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"APELLIDOS");
                        $pdf->addText(205,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"NOMBRE");
                        $pdf->addText(312,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"N.I.F.");
                        $pdf->addText(412,(($altura_fila/2)+$inicio_filas+(($numero_filas-0.5)*$altura_fila)-4),10,"FIRMAS");
                        $pdf->addText(485,(($altura_fila/2)+$inicio_filas+(($numero_filas-0.5)*$altura_fila)-4),10,"OBSERVACIONES");
                        $pdf->addText(22,208,10,"OBSERVACIONES GENERALES:");
        
                        $pdf->selectFont('./fonts/Verdana-BoldItalic');
                        $pdf->addText(530,30,8,"Hoja ".$contador_paginas." de ".$numero_de_paginas);

                        $inicio=(10+$inicio_filas+(($numero_filas-2)*$altura_fila));
                        $contador=1;
                        }
                        
                $pdf->selectFont('./fonts/Verdana');
                $pdf->setColor('00','00','0.00','');
                $pdf->addText(25,$inicio,10,$contador_total.". ".$alumnos_curso[$dni]["apellidos"]);
                $pdf->addText(183,$inicio,10,$alumnos_curso[$dni]["nombre"]);
                $pdf->addText(288,$inicio,10,$dni);

                $inicio=$inicio-$altura_fila;
                $contador=$contador+1;
                $contador_total=$contador_total+1;

                }

        if ($dias_lectivos<>$total_sesiones) { $pdf->ezNewPage(); }
}

$pdf->ezStream();
?>
