<?php 

require("../../configuracion/config.php");


// Datos
$id_centro=$_GET["id_centro"];

        // Busca el curso actual
        $id_curso_actual=$_GET["curso_seleccionado"];
        if (strlen($id_curso_actual)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $anio_inicio_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));
                } else {
                $id_curso_actual=0;
                }
        } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $anio_inicio_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));
        }

$consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=? ORDER BY nombre_centro;', 's', array($id_centro));
if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_completo"];
                        $domicilio_centro_completo=$centros["domicilio_centro"];
                        $CP_centro_completo=$centros["CP_centro"];
                        $poblacion_centro_completo=$centros["poblacion_centro"];
                        $telefono_centro_completo=$centros["telefono_centro"];

                        $busca_datos_subvencion=db_query_params('SELECT * FROM centros_cursos WHERE id_curso=? AND id_centro=?', 'ss', array($id_curso_actual, $id_centro));
                        $datos_busca_subvencion=db_fetch_array($busca_datos_subvencion);
                        $tope_maximo2=$datos_busca_subvencion["tope_maximo2"];
                        $subvencion2=$datos_busca_subvencion["subvencion2"];
                        $alumnos_ciclo2=$datos_busca_subvencion["alumnos_ciclo2"];

                        $precio_mes[0]=$centros["precio_mes_3"];
                        $precio_mes[1]=$centros["precio_mes_1"];
                        $precio_mes[2]=$centros["precio_mes_2"];
                        $precio_mes[3]=$centros["precio_mes_3"];
                        $precio_mes[4]=$centros["precio_mes_4"];
                        $precio_mes[5]=$centros["precio_mes_5"];
                        
                        $titular=$centros["titular"];
                        $codigo_centro=$centros["codigo_centro"];
                        
                        if ($alumnos_ciclo2>0) {
                        
                        // Alumnos 3
                        $edad_aula=$anio_inicio_curso-3;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_3=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_3=0;
                        $ratio_3=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>=? AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array((string)(date("Y-m-d")), $id_aula));
                                $alumnos_3=$alumnos_3+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_3=$ratio_3+$datos_aula_actual["capacidad"];
                                }
                        if ($aulas_3>0) {
                                $ratio_3=round(($ratio_3/$aulas_3),2);
                                } else {
                                $ratio_3=0;
                                }

                        // Alumnos 4
                        $edad_aula=$anio_inicio_curso-4;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_4=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_4=0;
                        $ratio_4=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>=? AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array((string)(date("Y-m-d")), $id_aula));
                                $alumnos_4=$alumnos_4+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_4=$ratio_4+$datos_aula_actual["capacidad"];
                                }
                        $ratio_4=round(($ratio_4/$aulas_4),2);
                        
                        // Alumnos 5
                        $edad_aula=$anio_inicio_curso-5;
                        $busca_aulas_edad_actual=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro AND id_curso=$id_curso_actual AND ciclo=2 AND anio_maximo=$edad_aula");
                        $aulas_5=db_num_rows($busca_aulas_edad_actual);
                        $alumnos_5=0;
                        $ratio_5=0;
                        while($datos_aula_actual=db_fetch_array($busca_aulas_edad_actual)) {
                                $id_aula=$datos_aula_actual["id_aula"];
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.fecha_baja>=? AND alumnos.aula_ayudas=? ORDER BY alumnos.orden_en_aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'ss', array((string)(date("Y-m-d")), $id_aula));
                                $alumnos_5=$alumnos_5+db_num_rows($busca_inscripcion_curso_actual);
                                $ratio_5=$ratio_5+$datos_aula_actual["capacidad"];
                                }
                        $ratio_5=round(($ratio_5/$aulas_5),2);
                        
                        // Busca cuota aula
                        
                        $busca_linea_presupuesto_aula=db_query_params('SELECT * FROM tarifa,servicios_contratados_aula WHERE tarifa.ID=servicios_contratados_aula.id_servicio AND servicios_contratados_aula.id_aula=? AND tarifa.desglosar_como=3 AND tarifa.SUBVENCION=0', 's', array($id_aula)) or die(db_error());
                        if (db_num_rows($busca_linea_presupuesto_aula)!=0) {

                                        $datos_linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);

                                        $precio_edu=$datos_linea_presupuesto_aula["PRECIO_VENTA"];

                                        }
                        
                        // Busca aulas ciclo 2
                        $ocupacion_actual=$alumnos_3+$alumnos_4+$alumnos_5;

                        // Numero de aulas del 2º ciclo
                        $busca_aulas2_ciclo=db_query_params('SELECT * FROM centro_aulas WHERE id_curso=? AND ciclo=2', 's', array($id_curso_actual)) or die(db_error()."-2");
                        $numero_aulas2_ciclo=db_num_rows($busca_aulas2_ciclo);

                        if ($ocupacion_actual!=0) {

                                $CM=($tope_maximo2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                                $SM=($subvencion2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                        
                                } else {
                                
                                $CM=($tope_maximo2*$numero_aulas2_ciclo/10)/$alumnos_ciclo2;
                                $SM=($subvencion2*$numero_aulas2_ciclo/10)/$alumnos_ciclo2;
                                
                                }
                        
                        $couta_maxima_mensual=$CM-$SM;
                        
                        }
                        
                        } else {
                        $nombre_centro="";
                        }

error_reporting(7);

include '../class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);
$pdf->setStrokeColor('0.0','0.00','0.50','');
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica');

$x_logo=0;
$y_logo=0;
$pdf->addJpegFromFile('C2anexoIV.jpg',$x_logo,$y_logo,595.28,841.89);

$pdf->ezSetY(727);
$pdf->ezText($titular,10,array('justification'=>'left','aleft'=>70));
$pdf->ezSetY(708);
$pdf->ezText($nombre_centro_completo,10,array('justification'=>'left','aleft'=>145));
$pdf->ezSetY(685);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>60));
$pdf->ezSetY(685);
$pdf->ezText($codigo_centro,10,array('justification'=>'left','aleft'=>410));


$pdf->ezSetY(518);
$pdf->ezText($aulas_3,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(518);
$pdf->ezText($alumnos_3,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(518);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));
$pdf->ezSetY(520);
$pdf->ezText($ratio_3,10,array('justification'=>'right','aright'=>450));

$pdf->ezSetY(501);
$pdf->ezText($aulas_4,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(501);
$pdf->ezText($alumnos_4,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(501);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));
$pdf->ezSetY(503);
$pdf->ezText($ratio_4,10,array('justification'=>'right','aright'=>450));

$pdf->ezSetY(483);
$pdf->ezText($aulas_5,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(483);
$pdf->ezText($alumnos_5,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(483);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));
$pdf->ezSetY(485);
$pdf->ezText($ratio_5,10,array('justification'=>'right','aright'=>450));

$pdf->ezSetY(462);
$pdf->ezText($aulas_3+$aulas_4+$aulas_5,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(462);
$pdf->ezText($alumnos_3+$alumnos_4+$alumnos_5,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(462);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));

$pdf->ezSetY(407);
$pdf->ezText(number_format($CM, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(382);
$pdf->ezText(number_format($SM, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(359);
$pdf->ezText(number_format($couta_maxima_mensual, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(338);
$pdf->ezText(number_format($precio_edu-$SM, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(228);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>120));
$pdf->ezSetY(228);
$pdf->ezText(date("j"),10,array('justification'=>'left','aleft'=>265));
$pdf->ezSetY(228);
$pdf->ezText($nombre_mes[date("n")],10,array('justification'=>'left','aleft'=>330));
$pdf->ezSetY(228);
$pdf->ezText(date("Y"),10,array('justification'=>'left','aleft'=>465));

$pdf->ezNewPage();

$x_logo=0;
$y_logo=0;
$pdf->addJpegFromFile('C2anexoIV2.jpg',$x_logo,$y_logo,595.28,841.89);

$pdf->ezSetY(727);
$pdf->ezText($titular,10,array('justification'=>'left','aleft'=>70));
$pdf->ezSetY(708);
$pdf->ezText($nombre_centro_completo,10,array('justification'=>'left','aleft'=>145));
$pdf->ezSetY(685);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>60));
$pdf->ezSetY(685);
$pdf->ezText($codigo_centro,10,array('justification'=>'left','aleft'=>410));


$pdf->ezSetY(518);
$pdf->ezText($aulas_3,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(518);
$pdf->ezText($alumnos_3,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(518);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));
$pdf->ezSetY(520);
$pdf->ezText($ratio_3,10,array('justification'=>'right','aright'=>450));

$pdf->ezSetY(501);
$pdf->ezText($aulas_4,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(501);
$pdf->ezText($alumnos_4,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(501);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));
$pdf->ezSetY(503);
$pdf->ezText($ratio_4,10,array('justification'=>'right','aright'=>450));

$pdf->ezSetY(483);
$pdf->ezText($aulas_5,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(483);
$pdf->ezText($alumnos_5,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(483);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));
$pdf->ezSetY(485);
$pdf->ezText($ratio_5,10,array('justification'=>'right','aright'=>450));

$pdf->ezSetY(462);
$pdf->ezText($aulas_3+$aulas_4+$aulas_5,10,array('justification'=>'right','aright'=>250));
$pdf->ezSetY(462);
$pdf->ezText($alumnos_3+$alumnos_4+$alumnos_5,10,array('justification'=>'right','aright'=>310));
$pdf->ezSetY(462);
$pdf->ezText(0,10,array('justification'=>'right','aright'=>385));

$pdf->ezSetY(407);
$pdf->ezText(number_format($CM, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(382);
$pdf->ezText(number_format($SM, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(359);
$pdf->ezText(number_format($couta_maxima_mensual, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(338);
$pdf->ezText(number_format($precio_edu-$SM, 2, ',', '.')." €",10,array('justification'=>'right','aright'=>495));

$pdf->ezSetY(228);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>120));
$pdf->ezSetY(228);
$pdf->ezText(date("j"),10,array('justification'=>'left','aleft'=>265));
$pdf->ezSetY(228);
$pdf->ezText($nombre_mes[date("n")],10,array('justification'=>'left','aleft'=>330));
$pdf->ezSetY(228);
$pdf->ezText(date("Y"),10,array('justification'=>'left','aleft'=>465));


$pdf->ezStream();

?>
