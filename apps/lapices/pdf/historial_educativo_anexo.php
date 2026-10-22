<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$valores_sino=array("NO","SI");

$id_alumno=$_GET["id_alumno"];
$id_curso_aula=$_GET["id_curso"];

$id_cuestionario=$_GET["id_cuestionario"];

$tipo_cuestionario=$_GET["tipo_cuestionario"];

$nombre_anexo="Anexo I";
if (($tipo_cuestionario!="_anex1a") and ($tipo_cuestionario!="_anex1")) {
        $nombre_anexo="Anexo II";
        }


        
        // Busca datos curso
                $busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$id_curso_aula) or die(db_error());
                
                if (db_num_rows($busca_curso)!=0){
                $datos_curso=db_fetch_array($busca_curso);

                $fecha_ini=$datos_curso["fecha_inicio"];
                        $fecha_fin=$datos_curso["fecha_fin"];
                
                }


error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(75,75,75,75);

$pdf->ezStartPageNumbers(526,30,7,'right','Página {PAGENUM} de {TOTALPAGENUM}',1);



                                $busca_alumnos=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_alumno") or die(db_error());

                                if (db_num_rows($busca_alumnos)!=0) {

                                $listado_alumnos=db_fetch_array($busca_alumnos);


// Datos historial

$busca_datos_prueba=db_query("SELECT * FROM historial_educativo_cuestionarios$tipo_cuestionario WHERE id_cuestionario=$id_cuestionario");
$datos_prueba=db_fetch_array($busca_datos_prueba);
$fecha_dia=date("j",strtotime($datos_prueba["fecha_historial"]));
$fecha_mes=date("n",strtotime($datos_prueba["fecha_historial"]));
$fecha_ano=date("Y",strtotime($datos_prueba["fecha_historial"]));

// Aquí el logo
$x_logo=15;
$y_logo=705;
$pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');
$pdf->setLineStyle(0.25,'','','','');

$posicion_inicial=800;

$pdf->selectFont('./fonts/Helvetica');

$pdf->ezSetY($posicion_inicial);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("HISTORIAL EDUCATIVO
$nombre_anexo - Cuestionario",18,array('justification'=>'center','aleft'=>'150'));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText(" ",12,array('justification'=>'center'));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NOMBRE:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"],10,array('justification'=>'center'));
$posicion_actual=$posicion_actual-24;


// Busca preguntas cuestionario
$busca_cuestiones=db_query("SELECT * FROM historial_educativo_cuestiones$tipo_cuestionario ORDER BY orden;");
                        
$seccion_actual="0";
while($cuestiones=db_fetch_array($busca_cuestiones)) {


                                if ($cuestiones["seccion"]!=$seccion_actual) {
                                        $seccion_actual=$cuestiones["seccion"];
                                        // Busca datos sección
                                        $nombre_seccion="";
                                        $texto_secion="";
                                        
                                        $busca_seccion=db_query("SELECT * FROM historial_educativo_secciones$tipo_cuestionario WHERE num_seccion='".$seccion_actual."'");
                                        if (db_num_rows($busca_seccion)!=0) {
                                                $datos_seccion=db_fetch_array($busca_seccion);
                                                $nombre_seccion=$datos_seccion["nombre_seccion"];
                                                $texto_secion=$datos_seccion["texto_secion"];
                                                if (strlen($texto_secion)!=0) {
                                                        $texto_secion="<br />".$texto_secion;
                                                        }
                                                }
                        if ($seccion_actual=="1.1") {
                        
$posicion_actual=$posicion_actual-24;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("1. Datos Familiares",10,array('justification'=>'left'));
$pdf->selectFont('./fonts/Helvetica');
$posicion_actual=$posicion_actual-12;
                                }
$posicion_actual=$posicion_actual-12;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText($seccion_actual.". ".$nombre_seccion,10,array('justification'=>'left'));
$pdf->selectFont('./fonts/Helvetica');
$posicion_actual=$posicion_actual-12;
$pdf->ezSetY($posicion_actual);
$pdf->ezText($texto_secion,10,array('justification'=>'left'));
$posicion_actual=$posicion_actual-12;

                }




// Tipos de cuestiones

$xpos1=250;
$suma_posicion=0;
if (strlen($cuestiones["texto_form"])>30) {
        // Posición 1
        $xpos1=350;
        $justificacion=array('justification'=>'left','aleft'=>$xpos1);
        }
if (strlen($cuestiones["texto_form"])>50) {
        $xpos1=400;
        $justificacion=array('justification'=>'left','aleft'=>$xpos1);
        }
 if (strlen($cuestiones["texto_form"])>68) {
                        if (strlen($datos_prueba[$cuestiones["campo_db"]])>10) {
                        $suma_posicion=-12;
                        $justificacion=array('justification'=>'left','aleft'=>250);
                                } else {
                                $justificacion=array('justification'=>'right','aright'=>570);
                                }
                        } else {
                        $justificacion=array('justification'=>'left','aleft'=>$xpos1);
                        }
     
                                                        switch($cuestiones["tipo_campo"]) {
                                    case 0:

$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY($posicion_actual);
$pdf->ezText($cuestiones["texto_form"].":",10,array('justification'=>'left','aleft'=>75));
$pdf->ezSetY($posicion_actual+$suma_posicion);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText($datos_prueba[$cuestiones["campo_db"]],10,$justificacion);
$posicion_actual=$posicion_actual+$suma_posicion-12;

                                                                break;
                                                                case 1:
                                                                
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY($posicion_actual);
$pdf->ezText($cuestiones["texto_form"],10,array('justification'=>'left','aleft'=>75));
$pdf->ezSetY($posicion_actual+$suma_posicion);
$pdf->selectFont('./fonts/Helvetica');
// $pdf->ezText($valores_sino[$datos_prueba[$cuestiones["campo_db"]]],10,array('justification'=>'right','aright'=>530));
$pdf->ezText($valores_sino[$datos_prueba[$cuestiones["campo_db"]]],10,$justificacion);
$posicion_actual=$posicion_actual+$suma_posicion-12;
                                                                
                                                                break;
                                                                case 2:
                                                                
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY($posicion_actual);
$pdf->ezText($cuestiones["texto_form"].":",10,array('justification'=>'left','aleft'=>75));
$pdf->ezSetY($posicion_actual+$suma_posicion);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText($datos_prueba[$cuestiones["campo_db"]],10,$justificacion);
$posicion_actual=$posicion_actual+$suma_posicion-12;

                                                                break;
                                                                case 3:
                                                                
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY($posicion_actual);
$pdf->ezText($cuestiones["texto_form"],10,array('justification'=>'left','aleft'=>75));
$pdf->ezSetY($posicion_actual+$suma_posicion);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText($datos_prueba[$cuestiones["campo_db"]],10,$justificacion);
$posicion_actual=$posicion_actual+$suma_posicion-12;

                                                                break;
                                                                }



if ($posicion_actual<150) {

$pagina_actual++;
$pdf->ezNewPage();

        // encabezado
$posicion_inicial=825;

$x_logo=15;
$y_logo=705;
$pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');
$pdf->setLineStyle(0.25,'','','','');


$posicion_inicial=800;

$pdf->selectFont('./fonts/Helvetica');

$pdf->ezSetY($posicion_inicial);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("HISTORIAL EDUCATIVO
$nombre_anexo - Cuestionario",18,array('justification'=>'center','aleft'=>'150'));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText(" ",12,array('justification'=>'center'));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NOMBRE:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"],10,array('justification'=>'center'));
$posicion_actual=$posicion_actual-48;

        } // Fin encabezado informe

        } // Fin Bucle principal

}


$pdf->ezStream();
?>
