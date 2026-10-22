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
$posicion_inicial=711.65;
$posicion_cruzY=748;



include '../class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(40,0,40,40);
$pdf->setStrokeColor('0.0','0.00','0.50','');
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica');

// Comprueba si el alumno está inscrito en el curso seleccionado
$busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.sustituye_a=0 AND alumnos.aula_ayudas=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");

$busca_sustitutos=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.sustituye_a>0 AND alumnos.aula_ayudas=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");

$total_ninios=db_num_rows($busca_sustitutos);
$contador_ninios=0;

while($datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual)) {

                        // Busca nombre curso
                        $id_curso_carta=$datos_inscripcion_curso_actual["id_curso"];
                        $busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_carta");
                        if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $nombre_curso=$datos_curso["nombre"];
                                } else {
                                if (date("n")>8) {
                                        $nombre_curso=date("Y")."-".(date("Y")+1);
                                        } else {
                                        $nombre_curso=(date("Y")-1)."-".date("Y");
                                        }
                                }
                        
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

$busca_sustituto_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.sustituye_a=$id_alumno AND alumnos.aula_ayudas=$id_aula ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");

if (db_num_rows($busca_sustituto_actual)!=0) {
        $datos_sustituto_actual=db_fetch_array($busca_sustituto_actual);

        $nombre_sustituto_actual=strtoupper($datos_sustituto_actual["Apellido1"]." ".$datos_sustituto_actual["Apellido2"].", ".$datos_sustituto_actual["Nombre"]);
        $fecha_alta=strtotime($datos_sustituto_actual["fecha_matricula"]);

$x_logo=0;
$y_logo=0;
$pdf->addJpegFromFile('fondo_cartas_sustit.jpg',$x_logo,$y_logo,595.28,841.89);

$posicion_actual=$posicion_inicial;

$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("COMUNICACIÓ DE SUBSTITUCIONS
",12,array('justification'=>'left','aleft'=>40));
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("CODI CENTRE: ".$codigo_centro."

NOM DEL CENTRE: ".$nombre_centro_completo."

L'ALUMNE ".strtoupper($apellido1_alu." ".$apellido2_alu.", ".$nombre_alu)." SUBVENCIONAT PER LA CONSELLERIA D'EDUCACIÓ DE LA GENERALITAT VALENCIANA PEL CURS ESCOLAR ".$nombre_curso.", CAUSA BAIXA EN DATA ".date("d/m/Y",$fecha_baja).". ADJUNTE LA QUANTITAT DE __________ BONS PER ANUL·LAR.

EN EL SEU LLOC I AM DATA ".date("d/m/Y",$fecha_alta)." ES DÓNA D'ALTA L'ALUMNE ".$nombre_sustituto_actual.". ADJUNTEM EL LLIBRE DE FAMÍLIA DEGUDAMENT COMPULSAT AM EL SEGELL DEL CENTRE I LA FACTURA INFORMATIVA SIGNADA PEL PARE, MARE O TUTOR DEL XIQUET/A.


SIGNATURA RESPONSABLE CENTRE                                                        SEGELL CENTRE




____________________________________________________________________________
",12,array('justification'=>'left','aleft'=>40));
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("COMUNICACIÓN DE SUSTITUCIONES
",12,array('justification'=>'left','aleft'=>40));
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("CÓDIGO CENTRO: ".$codigo_centro."

NOMBRE CENTRO: ".$nombre_centro_completo."

EL ALUMNO ".strtoupper($apellido1_alu." ".$apellido2_alu.", ".$nombre_alu)." SUBVENCIONADO/A POR LA CONSELLERIA D'EDUCACIÓ DE LA GENERALITAT VALENCIANA, PARA EL CURSO ESCOLAR ".$nombre_curso.", CAUSA BAJA EL DÍA ".date("d/m/Y",$fecha_baja).". ADJUNTO LA CANTIDAD DE __________ BONOS PARA QUE SEAN ANULADOS.

EN SU LUGAR Y CON FECHA ".date("d/m/Y",$fecha_alta)." SE DA DE ALTA EL/LA ALUMNO/A ".$nombre_sustituto_actual.". ADJUNTAMOS EL CORRESPONDIENTE LIBRO DE FAMILIA DEBIDAMENTE COTEJADO CON EL SELLO DEL CENTRO Y LA FACTURA INFORMATIVA FIRMADA POR EL PADRE, MADRE O TUTOR DEL ALUMNO/A.


FIRMA RESPONSABLE DEL CENTRO                                                        SELLO CENTRO
",12,array('justification'=>'left','aleft'=>40));

// FIN PAGINA

$contador_ninios++;

if ($contador_ninios<$total_ninios) {
        $pdf->ezNewPage();
        }

        } else {
        $nombre_sustituto_actual="";
        }

}

$pdf->ezStream();

?>
