<?php
// Datos

include "/s30/".$_GET["file"].".txt";

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Dibujamos los rectangulos y las tablas
$all = $pdf->openObject();

$pdf->setLineStyle(1,'','','','');
$pdf->setColor('0.90','0.90','0.90','');
$pdf->filledRectangle(20,712,558,20);
$pdf->filledRectangle(20,586,558,116);

$pdf->setLineStyle(2,'','','','');
$pdf->setStrokeColor('00','00','00','');
$pdf->setColor('00','00','00','');
$pdf->rectangle(20,712,558,20);
$pdf->rectangle(20,586,558,116);
$pdf->rectangle(20,40,558,80);

$altura_fila=26;
$numero_filas=16;
$inicio_filas=135;

for ($filas=1; $filas<=$numero_filas; $filas=$filas+1)
        {
        $pdf->rectangle(20,($inicio_filas-$altura_fila+($filas*$altura_fila)),558,$altura_fila);
        }
        
$pdf->setColor('0.90','0.90','0.90','');
$pdf->filledRectangle(20,($inicio_filas-$altura_fila+($numero_filas*$altura_fila)),558,$altura_fila*2);
$pdf->setColor('00','00','00','');
$pdf->rectangle(20,($inicio_filas-$altura_fila+($numero_filas*$altura_fila)),558,$altura_fila*2);

//Lineas verticales gruesas
$pdf->line(378,$inicio_filas,378,($inicio_filas+(($numero_filas+1)*$altura_fila)));

//Lineas verticales finas
$pdf->setLineStyle(1,'','','','');
$pdf->line(178,$inicio_filas,178,($inicio_filas+($numero_filas*$altura_fila)));
$pdf->line(278,$inicio_filas,278,($inicio_filas+($numero_filas*$altura_fila)));
$pdf->line(478,$inicio_filas,478,($inicio_filas+(($numero_filas+1)*$altura_fila)));

//Lineas horizontales finas
$pdf->line(20,($inicio_filas+($numero_filas*$altura_fila)),378,($inicio_filas+($numero_filas*$altura_fila)));

// Texto de la tabla
$pdf->selectFont('./fonts/Times-Bold');
$pdf->addText(128,(($altura_fila/2)+$inicio_filas+($numero_filas*$altura_fila)-4),12,"DATOS DE LOS ASISTENTES");
$pdf->addText(65,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"APELLIDOS");
$pdf->addText(205,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"NOMBRE");
$pdf->addText(312,(($altura_fila/2)+$inicio_filas+(($numero_filas-1)*$altura_fila)-4),10,"N.I.F.");
$pdf->addText(412,(($altura_fila/2)+$inicio_filas+(($numero_filas-0.5)*$altura_fila)-4),10,"FIRMA");
$pdf->addText(485,(($altura_fila/2)+$inicio_filas+(($numero_filas-0.5)*$altura_fila)-4),10,"OBSERVACIONES");
$pdf->addText(22,108,10,"OBSERVACIONES GENERALES:");

// Ponemos el texto del encabezado
$pdf->ezSetY(40);
$pdf->ezText("CJS-109",8,array('justification'=>'left'));
$pdf->ezSetY(807);
$pdf->ezText("FUNDACIÓN TRIPARTITA PARA LA FORMACIÓN EN EL EMPLEO",14,array('justification'=>'centre'));
$pdf->ezSetY(787);
$pdf->ezText("CONVOCATORIA PLANES ".$anio_convocatoria,14,array('justification'=>'centre'));
$pdf->ezSetY(755);
$pdf->ezText("S-30  PARTE DE ASISTENCIA",16,array('justification'=>'left'));

$pdf->addText(30,718,10,"Nº DE EXPDTE: ".$num_expediente."    ENTIDAD SOLICITANTE: ".$entidad_solicitante."  C.I.F.: ".$cif_entidad);
$pdf->addText(30,688,10,"DENOMINACIÓN DE LA ACCIÓN FORMATIVA: ".$nombre_accion);
$pdf->addText(30,668,10,"Nº: ".$num_accion."    GRUPO: ".$grupo_accion);
$pdf->addText(259,668,10,"FECHA DE INICIO: ".$curso_inicio."    FECHA FIN: ".$curso_fin);
$pdf->addText(30,648,10,"FORMADOR/RESPONSABLE DE FORMACIÓN: ".$formador_accion);
$pdf->addText(30,628,10,"SESIÓN Nº: ".$numero_sesion."    FECHA: ".$fecha_sesion);
$pdf->addText(259,628,10,"MAÑANA/TARDE: ".$sesion_turno."    HORARIO: ".$sesion_horario);
$pdf->addText(30,604,10,"FIRMADO:");
$pdf->addText(30,594,10,"(Formador/Resp. Formación)");

$pdf->closeObject();

$pdf->addObject($all,'all');
// select a font
$pdf->selectFont('./fonts/Times-Roman');

$pdf->ezStartPageNumbers(530,30,8,'right','',1);

$size=12;
$height = $pdf->getFontHeight($size);

// Escribimos los nombres de los alumnos, si hay más de 15 insertamos paginas nuevas
$inicio=(10+$inicio_filas+(($numero_filas-2)*$altura_fila));
$contador=1;
$contador_total=1;
while (list($dni) = each($alumnos_curso)) {

if ($contador==16) {
        $pdf->ezNewPage();
        $inicio=(10+$inicio_filas+(($numero_filas-2)*$altura_fila));
        $contador=1;
        $pdf->addObject($all,'all');
        }

$pdf->addText(25,$inicio,10,$contador_total.". ".$alumnos_curso[$dni]["apellidos"]);
$pdf->addText(183,$inicio,10,$alumnos_curso[$dni]["nombre"]);
$pdf->addText(288,$inicio,10,$dni);

$inicio=$inicio-$altura_fila;
$contador=$contador+1;
$contador_total=$contador_total+1;

}

$pdf->ezStream();

?>
