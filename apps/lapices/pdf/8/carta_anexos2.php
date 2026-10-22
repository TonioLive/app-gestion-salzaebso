<?php 

require("../../configuracion/config.php");


// Datos
$id_aula=$_GET["id_aula"];


$capacidad_aula=0;
$tope_maximo2=0;
$subvencion2=0;

$anio_actual=date("Y");

$busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula);

$datos_aula=db_fetch_array($busca_datos_aula);
$ciclo=$datos_aula["ciclo"];
$capacidad_aula=$datos_aula["capacidad"];
$nombre_aula=$datos_aula["nombre"];
$id_centro_contacto=$datos_aula["id_centro"];
$id_curso_contacto=$datos_aula["id_curso"];

$edad_aula=$datos_aula["edad_maxima"]-1;

$consulta_centros=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_contacto ORDER BY nombre_centro;");
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
                        $db_nombre_centro="ESCUELA: ".$centros["nombre_centro"];
                        $codigo_centro=$centros["codigo_centro"];
                        } else {
                        $nombre_centro="";
                        }

error_reporting(7);


$tam_fuente=10;
$altura_fila=18.7;
$posicion_inicial=691.65;
$posicion_cruzY=748;



include '../class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(40,0,20,20);
$pdf->setStrokeColor('0.0','0.00','0.50','');
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica');

/*
$x_logo=50;
$y_logo=735;
$pdf->addJpegFromFile('../logo_factura.jpg',$x_logo,$y_logo,150,54);
*/
$x_logo=50;
$y_logo=655;
$pdf->addJpegFromFile('../logo_redondo.jpg',$x_logo,$y_logo,125,125);

// Comprueba si el alumno está inscrito en el curso seleccionado
$busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.sustituye_a=0 AND alumnos.aula=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");

$pdf->ezSetY(20);
$pdf->ezText($nombre_centro_completo.", Aula: ".$nombre_aula,8,array('justification'=>'right','aright'=>575));

$posicion_actual=$posicion_inicial;

$pdf->ezSetY($posicion_actual);
$pdf->ezText("La ".$nombre_centro_completo." con código de centro Nº ".$codigo_centro.", sita en ".$domicilio_centro_completo." de ".$poblacion_centro_completo.".

Comunica las modificaciones de alumnos tanto de altas como de bajas surgidas por aulas.

",12,array('justification'=>'left','aleft'=>40));
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("Aula ".$nombre_aula."

",12,array('justification'=>'left','aleft'=>40));
$pdf->selectFont('./fonts/Helvetica');

while($datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual)) {
                        
                        $id_alumno=$datos_inscripcion_curso_actual["id_alumno"];
                        $id_usuario=$datos_inscripcion_curso_actual["id_usuario"];
                        $orden_en_aula=$datos_inscripcion_curso_actual["orden_en_aula"];
                        $nombre_alu=$datos_inscripcion_curso_actual["Nombre"];
                        $apellido1_alu=$datos_inscripcion_curso_actual["Apellido1"];
                        $apellido2_alu=$datos_inscripcion_curso_actual["Apellido2"];
                        $sustituye_a=$datos_inscripcion_curso_actual["sustituye_a"];
                        $fecha_baja=strtotime($datos_inscripcion_curso_actual["fecha_baja"]);
// --- FIN DATOS ---


// Comprueba si es sustituido

$busca_sustituto_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.sustituye_a=$id_alumno AND alumnos.aula=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");

if (db_num_rows($busca_sustituto_actual)!=0) {
        $datos_sustituto_actual=db_fetch_array($busca_sustituto_actual);

        $nombre_sustituto_actual=$datos_sustituto_actual["Apellido1"]." ".$datos_sustituto_actual["Apellido2"].", ".$datos_sustituto_actual["Nombre"];
        $fecha_alta=strtotime($datos_sustituto_actual["fecha_matricula"]);

$pdf->ezText("BAJA: ".strtoupper($apellido1_alu." ".$apellido2_alu.", ".$nombre_alu)." en fecha de ".$nombre_mes[date("n",$fecha_baja)]." de ".date("Y",$fecha_baja)." sustituido por ALTA: ".strtoupper($nombre_sustituto_actual)." en fecha de alta ".$nombre_mes[date("n",$fecha_alta)]." de ".date("Y",$fecha_alta).".
",$tam_fuente,array('justification'=>'left','aleft'=>40));
        
        } else {
        $nombre_sustituto_actual="";
        }


// FIN PAGINA

}

// $pdf->ezNewPage();

$pdf->ezStream();

?>
