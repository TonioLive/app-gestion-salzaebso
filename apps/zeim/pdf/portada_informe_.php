<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$id_alumno=$_GET["id_alumno"];

$busca_alumnos_escuela=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($id_alumno)) or die(db_error());

$alumnos_escuela=db_fetch_array($busca_alumnos_escuela);

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($alumnos_escuela["id_curso"])) or die(db_error());

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso=$datos_curso_actual["id_curso"];
                $nombre_curso=$datos_curso_actual["nombre"];
                } else {
                $id_curso=0;
                $nombre_curso="";
                }
                
        $id_centro_alumno=$alumnos_escuela["id_centro"];
        $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($alumnos_escuela["id_centro"]));
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro=$datos_centro["nombre_centro"];
                        }
                        
// Nombre foto
$nombre_foto="foto_usu".$alumnos_escuela["id_usuario"].".jpg";

if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="nada.jpg";
      }
                        
// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(75,75,75,75);

// Aquí el logo
$x_logo=0;
$y_logo=0;
$pdf->addJpegFromFile('portada_informe'.$id_centro_alumno.'.jpg',$x_logo,$y_logo,595.28,841.89);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');

$posicion_texto1=346;
$posicion_texto2=180;

$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY($posicion_texto1);
$pdf->ezText("CURSO $nombre_curso",24,array('justification'=>'left','aleft'=>266));
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY($posicion_texto2);
$pdf->ezText("Nombre alumno:",12,array('justification'=>'left','aleft'=>175));
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_texto2);
$pdf->ezText($alumnos_escuela["Nombre"]." ".$alumnos_escuela["Apellido1"]." ".$alumnos_escuela["Apellido2"],12,array('justification'=>'left','aleft'=>275));
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY($posicion_texto2-22);
$pdf->ezText("Fecha de nacimiento:",12,array('justification'=>'left','aleft'=>175));
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_texto2-22);
$pdf->ezText(date("d/m/Y",strtotime($alumnos_escuela["fecha_nacimiento"])),12,array('justification'=>'left','aleft'=>300));

$pdf->addJpegFromFile('../fotos_usuarios/'.$nombre_foto,80,110,75,100);

$pdf->ezStream();
?>

