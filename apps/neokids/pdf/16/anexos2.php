<?php 

require("../../configuracion/config.php");

// Datos
$id_aula=$_GET["id_aula"];

$trimestre=$_GET["trimestre"];

if (strlen($trimestre)==0) {
        $trimestre=1;
        }

$capacidad_aula=0;
$tope_maximo2=0;
$subvencion2=0;

$anio_actual=date("Y");

$busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

$datos_aula=db_fetch_array($busca_datos_aula);
$ciclo=$datos_aula["ciclo"];
$capacidad_aula=$datos_aula["capacidad"];
$nombre_aula=$datos_aula["nombre"];
$id_centro_contacto=$datos_aula["id_centro"];
$id_curso_contacto=$datos_aula["id_curso"];

$edad_aula=$datos_aula["edad_maxima"]-1;

$consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=? ORDER BY nombre_centro;', 's', array($id_centro_contacto));
if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_completo"];
                        $domicilio_centro_completo=$centros["domicilio_centro"];
                        $CP_centro_completo=$centros["CP_centro"];
                        $poblacion_centro_completo=$centros["poblacion_centro"];
                        $telefono_centro_completo=$centros["telefono_centro"];
                        $tope_maximo2=$centros["tope_maximo2"];
                        $subvencion2=$centros["subvencion2"];
                        $alumnos_ciclo2=$centros["alumnos_ciclo2"];
                        $precio_mes[0]=$centros["precio_mes_3"];
                        $precio_mes[1]=$centros["precio_mes_1"];
                        $precio_mes[2]=$centros["precio_mes_2"];
                        $precio_mes[3]=$centros["precio_mes_3"];
                        $precio_mes[4]=$centros["precio_mes_4"];
                        $precio_mes[5]=$centros["precio_mes_5"];
                        $db_nombre_centro="ESCUELA: ".$datos_centro["nombre_centro"];
                        } else {
                        $nombre_centro="";
                        }

error_reporting(7);

$posicion_cruz[0]=104;
$posicion_cruz[1]=210;
$posicion_cruz[2]=310.5;
$posicion_cruz[3]=117;
$posicion_cruz[4]=188;
$posicion_cruz[5]=261;

if ($ciclo==1) {
        $tam_fuente=10;
        $altura_fila=18.7;
        $posicion_inicial=691.65;
        $posicion_cruzY=748;
        }
if ($ciclo==2) {
        $tam_fuente=10;
        $altura_fila=18.7;
        $posicion_inicial=691.65;
        $posicion_cruzY=744;
        }


include '../class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);
$pdf->setStrokeColor('0.0','0.00','0.50','');
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica');

if ($ciclo==1) {
        $x_logo=0;
        $y_logo=0;
        $pdf->addJpegFromFile('C1AnexoIV1.jpg',$x_logo,$y_logo,595.28,841.89);
        }

// Comprueba si el alumno está inscrito en el curso seleccionado
switch($trimestre)
        {

        case 1:
                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.sustituye_a=0 AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 's', array($id_aula));
        break;
        
        case 2:
                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.sustituye_a=0 AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 's', array($id_aula));
        break;
        }


$pdf->ezSetY(20);
$pdf->ezText($nombre_centro_completo.", Aula: ".$nombre_aula,8,array('justification'=>'right','aright'=>575));

$posicion_actual=$posicion_inicial;

$pdf->ezSetY($posicion_cruzY);
$pdf->ezText("X",8,array('justification'=>'left','aleft'=>$posicion_cruz[$edad_aula]));


while($datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual)) {
                        
                        $id_alumno=$datos_inscripcion_curso_actual["id_alumno"];
                        $id_usuario=$datos_inscripcion_curso_actual["id_usuario"];
                        $orden_en_aula=$datos_inscripcion_curso_actual["orden_en_aula"];
                        $nombre_alu=$datos_inscripcion_curso_actual["Nombre"];
                        $apellido1_alu=$datos_inscripcion_curso_actual["Apellido1"];
                        $apellido2_alu=$datos_inscripcion_curso_actual["Apellido2"];
                        $sustituye_a=$datos_inscripcion_curso_actual["sustituye_a"];

// Comprueba si es sustituido

$busca_sustituto_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.sustituye_a=? AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array($id_alumno, $id_aula));

if (db_num_rows($busca_sustituto_actual)!=0) {
        $datos_sustituto_actual=db_fetch_array($busca_sustituto_actual);

        $nombre_sustituto_actual=$datos_sustituto_actual["Apellido1"]." ".$datos_sustituto_actual["Apellido2"].", ".$datos_sustituto_actual["Nombre"];
        } else {
        $nombre_sustituto_actual="";
        }

// --- FIN DATOS ---

$pdf->ezSetY($posicion_actual);
$pdf->ezText("",$tam_fuente,array('justification'=>'right','aright'=>50));
$pdf->ezSetY($posicion_actual);
$pdf->ezText(strtoupper($apellido1_alu." ".$apellido2_alu.", ".$nombre_alu),$tam_fuente,array('justification'=>'left','aleft'=>95));
$pdf->ezSetY($posicion_actual);
$pdf->ezText($nombre_sustituto_actual,$tam_fuente,array('justification'=>'left','aleft'=>358));
$posicion_actual=$posicion_actual-$altura_fila;

// FIN PAGINA

}

$pdf->ezNewPage();

if ($ciclo==1) {
        $x_logo=0;
        $y_logo=0;
        $pdf->addJpegFromFile('C1AnexoIV2.jpg',$x_logo,$y_logo,595.28,841.89);
        }

// Comprueba si el alumno está inscrito en el curso seleccionado
$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.sustituye_a=0 AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 's', array($id_aula));

$pdf->ezSetY(20);
$pdf->ezText($nombre_centro_completo.", Aula: ".$nombre_aula,8,array('justification'=>'right','aright'=>575));

$posicion_actual=$posicion_inicial;

$pdf->ezSetY($posicion_cruzY);
$pdf->ezText("X",8,array('justification'=>'left','aleft'=>$posicion_cruz[$edad_aula]));


while($datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual)) {

                        $id_alumno=$datos_inscripcion_curso_actual["id_alumno"];
                        $id_usuario=$datos_inscripcion_curso_actual["id_usuario"];
                        $orden_en_aula=$datos_inscripcion_curso_actual["orden_en_aula"];
                        $nombre_alu=$datos_inscripcion_curso_actual["Nombre"];
                        $apellido1_alu=$datos_inscripcion_curso_actual["Apellido1"];
                        $apellido2_alu=$datos_inscripcion_curso_actual["Apellido2"];
                        $sustituye_a=$datos_inscripcion_curso_actual["sustituye_a"];

// Comprueba si es sustituido

$busca_sustituto_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.sustituye_a=? AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array($id_alumno, $id_aula));

if (db_num_rows($busca_sustituto_actual)!=0) {
        $datos_sustituto_actual=db_fetch_array($busca_sustituto_actual);

        $nombre_sustituto_actual=$datos_sustituto_actual["Apellido1"]." ".$datos_sustituto_actual["Apellido2"].", ".$datos_sustituto_actual["Nombre"];
        } else {
        $nombre_sustituto_actual="";
        }

// --- FIN DATOS ---

$pdf->ezSetY($posicion_actual);
$pdf->ezText("",$tam_fuente,array('justification'=>'right','aright'=>50));
$pdf->ezSetY($posicion_actual);
$pdf->ezText(strtoupper($apellido1_alu." ".$apellido2_alu.", ".$nombre_alu),$tam_fuente,array('justification'=>'left','aleft'=>95));
$pdf->ezSetY($posicion_actual);
$pdf->ezText($nombre_sustituto_actual,$tam_fuente,array('justification'=>'left','aleft'=>358));
$posicion_actual=$posicion_actual-$altura_fila;

// FIN PAGINA

}

$pdf->ezStream();

?>
