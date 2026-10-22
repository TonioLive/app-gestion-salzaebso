<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

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

// Busca servicios aula
$busca_linea_presupuesto_aula=db_query_params('SELECT * FROM servicios_contratados_aula,tarifa,centro_aulas WHERE servicios_contratados_aula.id_aula=centro_aulas.id_aula AND servicios_contratados_aula.id_servicio=tarifa.ID AND centro_aulas.edad_maxima=1 AND tarifa.CATEGORIA=4 AND tarifa.SUBVENCION=1 AND centro_aulas.id_centro=? AND centro_aulas.id_curso=?', 'ss', array($id_centro_contacto, $id_curso_contacto)) or die(db_error());
if (db_num_rows($busca_linea_presupuesto_aula)!=0) {
        $linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);
        $subvencion_conselleria_01=$linea_presupuesto_aula["PRECIO_VENTA"];
        }
$busca_linea_presupuesto_aula=db_query_params('SELECT * FROM servicios_contratados_aula,tarifa,centro_aulas WHERE servicios_contratados_aula.id_aula=centro_aulas.id_aula AND servicios_contratados_aula.id_servicio=tarifa.ID AND centro_aulas.edad_maxima=1 AND tarifa.CATEGORIA=1 AND tarifa.subcat=2 AND centro_aulas.id_centro=? AND centro_aulas.id_curso=?', 'ss', array($id_centro_contacto, $id_curso_contacto)) or die(db_error());
if (db_num_rows($busca_linea_presupuesto_aula)!=0) {
        $linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);
        $cuota_edu_01=$linea_presupuesto_aula["PRECIO_VENTA"];
        }
$busca_linea_presupuesto_aula=db_query_params('SELECT * FROM servicios_contratados_aula,tarifa,centro_aulas WHERE servicios_contratados_aula.id_aula=centro_aulas.id_aula AND servicios_contratados_aula.id_servicio=tarifa.ID AND centro_aulas.edad_maxima=2 AND tarifa.CATEGORIA=4 AND tarifa.SUBVENCION=1 AND centro_aulas.id_centro=? AND centro_aulas.id_curso=?', 'ss', array($id_centro_contacto, $id_curso_contacto)) or die(db_error());
if (db_num_rows($busca_linea_presupuesto_aula)!=0) {
        $linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);
        $subvencion_conselleria_12=$linea_presupuesto_aula["PRECIO_VENTA"];
        }
$busca_linea_presupuesto_aula=db_query_params('SELECT * FROM servicios_contratados_aula,tarifa,centro_aulas WHERE servicios_contratados_aula.id_aula=centro_aulas.id_aula AND servicios_contratados_aula.id_servicio=tarifa.ID AND centro_aulas.edad_maxima=2 AND tarifa.CATEGORIA=1 AND tarifa.subcat=2 AND centro_aulas.id_centro=? AND centro_aulas.id_curso=?', 'ss', array($id_centro_contacto, $id_curso_contacto)) or die(db_error());
if (db_num_rows($busca_linea_presupuesto_aula)!=0) {
        $linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);
        $cuota_edu_12=$linea_presupuesto_aula["PRECIO_VENTA"];
        }
$busca_linea_presupuesto_aula=db_query_params('SELECT * FROM servicios_contratados_aula,tarifa,centro_aulas WHERE servicios_contratados_aula.id_aula=centro_aulas.id_aula AND servicios_contratados_aula.id_servicio=tarifa.ID AND centro_aulas.edad_maxima=3 AND tarifa.CATEGORIA=4 AND tarifa.SUBVENCION=1 AND centro_aulas.id_centro=? AND centro_aulas.id_curso=?', 'ss', array($id_centro_contacto, $id_curso_contacto)) or die(db_error());
if (db_num_rows($busca_linea_presupuesto_aula)!=0) {
        $linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);
        $subvencion_conselleria_23=$linea_presupuesto_aula["PRECIO_VENTA"];
        }
$busca_linea_presupuesto_aula=db_query_params('SELECT * FROM servicios_contratados_aula,tarifa,centro_aulas WHERE servicios_contratados_aula.id_aula=centro_aulas.id_aula AND servicios_contratados_aula.id_servicio=tarifa.ID AND centro_aulas.edad_maxima=3 AND tarifa.CATEGORIA=1 AND tarifa.subcat=2 AND centro_aulas.id_centro=? AND centro_aulas.id_curso=?', 'ss', array($id_centro_contacto, $id_curso_contacto)) or die(db_error());
if (db_num_rows($busca_linea_presupuesto_aula)!=0) {
        $linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);
        $cuota_edu_23=$linea_presupuesto_aula["PRECIO_VENTA"];
        }

$consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=? ORDER BY nombre_centro;', 's', array($id_centro_contacto));
if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_completo"];
                        $domicilio_centro_completo=$centros["domicilio_centro"];
                        $CP_centro_completo=$centros["CP_centro"];
                        $poblacion_centro_completo=$centros["poblacion_centro"];
                        $telefono_centro_completo=$centros["telefono_centro"];
                        
                        $busca_datos_subvencion=db_query_params('SELECT * FROM centros_cursos WHERE id_curso=? AND id_centro=?', 'ss', array($id_curso_contacto, $id_centro_contacto));
                        $datos_busca_subvencion=db_fetch_array($busca_datos_subvencion);
                        $tope_maximo2=$datos_busca_subvencion["tope_maximo2"];
                        $subvencion2=$datos_busca_subvencion["subvencion2"];
                        $alumnos_ciclo2=$datos_busca_subvencion["alumnos_ciclo2"];
                        
                        $tope_mes_01=$datos_busca_subvencion["tope_mes_01"];
                        $tope_mes_12=$datos_busca_subvencion["tope_mes_12"];
                        $tope_mes_23=$datos_busca_subvencion["tope_mes_23"];

                        $precio_coste_01=$datos_busca_subvencion["precio_coste_01"];
                        $precio_coste_12=$datos_busca_subvencion["precio_coste_12"];
                        $precio_coste_23=$datos_busca_subvencion["precio_coste_23"];
                        
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

// error_reporting(7);

// --- FIN DATOS ---

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

$pdf -> ezSetMargins(20,0,20,20);
$pdf->setStrokeColor('0.0','0.00','0.50','');
$pdf->setLineStyle(1,'','','','');
$pdf->setColor('00','00','0.00','');
$pdf->selectFont('./fonts/Helvetica');

$x_logo=0;
$y_logo=0;
$pdf->addJpegFromFile('ANEXOIVa09-10.jpg',$x_logo,$y_logo,595.28,841.89);


$pdf->ezSetY(746);
$pdf->ezText($nombre_centro_completo,10,array('justification'=>'left','aleft'=>145));
$pdf->ezSetY(732);
$pdf->ezText($domicilio_centro_completo,10,array('justification'=>'left','aleft'=>145));
$pdf->ezSetY(716);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>145));

$pdf->ezSetY(526);
$pdf->ezText(number_format($precio_coste_01,2,",",".")." €",12,array('justification'=>'left','aleft'=>345));
$pdf->ezSetY(526);
$pdf->ezText(number_format($precio_coste_12,2,",",".")." €",12,array('justification'=>'left','aleft'=>422));
$pdf->ezSetY(526);
$pdf->ezText(number_format($precio_coste_23,2,",",".")." €",12,array('justification'=>'left','aleft'=>498));

$pdf->ezSetY(505);
$pdf->ezText(number_format($subvencion_conselleria_01*-1,2,",",".")." €",12,array('justification'=>'left','aleft'=>345));
$pdf->ezSetY(505);
$pdf->ezText(number_format($subvencion_conselleria_12*-1,2,",",".")." €",12,array('justification'=>'left','aleft'=>422));
$pdf->ezSetY(505);
$pdf->ezText(number_format($subvencion_conselleria_23*-1,2,",",".")." €",12,array('justification'=>'left','aleft'=>498));

$pdf->ezSetY(484);
$pdf->ezText(number_format($tope_mes_01,2,",",".")." €",12,array('justification'=>'left','aleft'=>345));
$pdf->ezSetY(484);
$pdf->ezText(number_format($tope_mes_12,2,",",".")." €",12,array('justification'=>'left','aleft'=>422));
$pdf->ezSetY(484);
$pdf->ezText(number_format($tope_mes_23,2,",",".")." €",12,array('justification'=>'left','aleft'=>498));

$pdf->ezSetY(463);
$pdf->ezText(number_format($cuota_edu_01+$subvencion_conselleria_01,2,",",".")." €",12,array('justification'=>'left','aleft'=>345));
$pdf->ezSetY(463);
$pdf->ezText(number_format($cuota_edu_12+$subvencion_conselleria_12,2,",",".")." €",12,array('justification'=>'left','aleft'=>422));
$pdf->ezSetY(463);
$pdf->ezText(number_format($cuota_edu_23+$subvencion_conselleria_23,2,",",".")." €",12,array('justification'=>'left','aleft'=>498));

/*
// Fecha
$pdf->ezSetY(340);
$pdf->ezText($poblacion_centro_completo,10,array('justification'=>'left','aleft'=>166));
$pdf->ezSetY(340);
$pdf->ezText(date("j"),10,array('justification'=>'left','aleft'=>258));
$pdf->ezSetY(340);
$pdf->ezText($nombre_mes[date("n")],10,array('justification'=>'left','aleft'=>310));
$pdf->ezSetY(340);
$pdf->ezText(date("Y"),10,array('justification'=>'left','aleft'=>412));
*/
$pdf->ezStream();

?>
