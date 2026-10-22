<?php  require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

// Datos
$id_aula=$_GET["id_aula"];

$modelo=$_GET["m"];
if (strlen($modelo)==0) {
        $modelo=1;
        }
$array_idservicios=$_GET["id_servicios"];

// $tipo_menu_cliente
$nombre_tipo_menu_cliente[0]="Desconocido";
$nombre_tipo_menu_cliente[1]="Pequeños";
$nombre_tipo_menu_cliente[2]="Mayores";
$nombre_tipo_menu_cliente[3]="Colegial";
$nombre_tipo_menu_cliente[4]="Toma";
$nombre_tipo_menu_cliente[5]="Bebe";

$capacidad_aula=0;
$tope_maximo2=0;
$subvencion2=0;

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Comprueba si el alumno está inscrito en el curso seleccionado
$busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos WHERE aula_ayudas=$id_aula");

while($datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual)) {
                        
                        $id_alumno=$datos_inscripcion_curso_actual["id_alumno"];
                        $id_aula_presupuesto=$datos_inscripcion_curso_actual["aula"];
                        $id_usuario=$datos_inscripcion_curso_actual["id_usuario"];

                        $comedor_cliente=$datos_inscripcion_curso_actual["comedor"];
                        $tipo_menu_cliente=$datos_inscripcion_curso_actual["tipo_menu_comedor"];
                        
                        $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_inscripcion_curso_actual["aula"]);
                        $datos_aula=db_fetch_array($busca_datos_aula);
                        $ciclo=$datos_aula["ciclo"];
                        $capacidad_aula=$datos_aula["capacidad"];
                        $nombre_aula="AULA: ".$datos_aula["nombre"];
                        $id_centro_contacto=$datos_aula["id_centro"];
                        $id_curso_contacto=$datos_aula["id_curso"];
                        $edad_aula=$datos_aula["edad_maxima"]-1;

$persona_contacto="";

$busca_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario");
if (db_num_rows($busca_datos_usuario)!=0) {
                        $datos_usuario=db_fetch_array($busca_datos_usuario);

                        $nombre_alu=$datos_usuario["Nombre"];
                        $apellido1_alu=$datos_usuario["Apellido1"];
                        $apellido2_alu=$datos_usuario["Apellido2"];

                        $comentarios=$datos_usuario["comentarios"];
                        $telefono=$datos_usuario["telefono"];
                        $email=$datos_usuario["email"];
                        $poblacion=$datos_usuario["poblacion"];
                        $codigo_postal=$datos_usuario["codigo_postal"];
                        $sexo=$datos_usuario["sexo"];

                        if ($datos_contacto["sexo"]=="H") {
                                $sexo_alumno_numero=1;
                                } else {
                                $sexo_alumno_numero=0;
                                }

                        }
/*
if (strlen($persona_contacto)==0) {
$persona_contacto=$datos_inscripcion_curso_actual["banco_titular"];
}

if (strlen($persona_contacto)==0) {
        if (strlen($persona_contacto)==0) {
                                $persona_contacto=$datos_usuario["contacto1"];
                                }
        if (strlen($persona_contacto)!=0) {
                                $persona_contacto=$persona_contacto."/".$datos_usuario["contacto2"];
                                } else {
                                $persona_contacto=$datos_usuario["contacto2"];
                                }
        }

if (strlen($persona_contacto)==0) {

        if (strlen($id_usuario)!=0) {

        $consulta_relaciones=mysql_query("SELECT * FROM padres,usuarios WHERE padres.id_usuario=usuarios.id_usuario AND padres.parentesco=1 AND padres.padre_de=$id_usuario");
        
        while($relaciones=mysql_num_rows($consulta_relaciones)){
                        $persona_contacto=$relaciones["Nombre"]." ".$relaciones["Apellido1"]." ".$relaciones["Apelldio2"]." ";
                        }
        }
                        
        }

*/

$consulta_centros=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_contacto ORDER BY nombre_centro;");
if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_centro"];
                        $domicilio_centro_completo=$centros["domicilio_centro"];
                        $CP_centro_completo=$centros["CP_centro"];
                        $poblacion_centro_completo=$centros["poblacion_centro"];
                        $telefono_centro_completo=$centros["telefono_centro"];
                        
                        $busca_datos_subvencion=db_query("SELECT * FROM centros_cursos WHERE id_curso=$id_curso_contacto AND id_centro=$id_centro_contacto");
                        $datos_busca_subvencion=db_fetch_array($busca_datos_subvencion);
                        $tope_maximo2=$datos_busca_subvencion["tope_maximo2"];
                        $subvencion2=$datos_busca_subvencion["subvencion2"];
                        $alumnos_ciclo2=$datos_busca_subvencion["alumnos_ciclo2"];
                        
                        $tope_mes[0]=$datos_busca_subvencion["tope_mes_01"];
                        $tope_mes[1]=$datos_busca_subvencion["tope_mes_12"];
                        $tope_mes[2]=$datos_busca_subvencion["tope_mes_23"];

                        $precio_coste[0]=$datos_busca_subvencion["precio_coste_01"];
                        $precio_coste[1]=$datos_busca_subvencion["precio_coste_12"];
                        $precio_coste[2]=$datos_busca_subvencion["precio_coste_23"];
                        
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

// Comprueba matrículas
// Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
$consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_contacto") or die(db_error()."-4");
if (db_num_rows($consulta_cursos)!=0) {
                        $cursos=db_fetch_array($consulta_cursos);
                        $nombre_curso="CURSO: ".$cursos["nombre"];
                        } else {
                        $nombre_curso="";
                        }

// Comprueba ocupacion 2º ciclo
$fecha_seleccionada=time();
$consulta_ocupacion=db_query("SELECT * FROM alumnos,usuarios,centro_aulas WHERE alumnos.id_usuario=usuarios.id_usuario AND centro_aulas.id_aula=alumnos.aula_ayudas AND centro_aulas.id_curso=$id_curso_contacto AND centro_aulas.ciclo=2 AND fecha_matricula<='".date("Y-m-d",$fecha_seleccionada)."' AND fecha_baja>='".date("Y-m-d",$fecha_seleccionada)."'") or die(db_error()."-1");
$ocupacion_actual=db_num_rows($consulta_ocupacion);

$ocupacion_actual=$alumnos_ciclo2;



// Numero de aulas del 2º ciclo
$busca_aulas2_ciclo=db_query("SELECT * FROM centro_aulas WHERE id_curso=$id_curso_contacto AND ciclo=2") or die(db_error()."-2");
$numero_aulas2_ciclo=db_num_rows($busca_aulas2_ciclo);

// --- FIN DATOS ---

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

$altura_fila=28;
$numero_filas=11;
$inicio_filas=135;
$diferencia_1=0;
$limite_inferior=100;
$posicion_inicio_tabla=670;
$posicion_inicial=780;
$posicion_nueva_pagina=780;

// encabezado y pie de página

// Aquí el logo
$x_logo=422;
$y_logo=715;
// $pdf->addJpegFromFile('logo_factura.jpg',$x_logo,$y_logo,115,115);

$pdf->setLineStyle(1,'','','','');

$pdf->setColor('00','00','0.00','');

// Pie de Página

$pdf->setColor('0.00','0.00','0.00','');
$pdf->addText(30,30,6,"ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia. Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123");

// Pie de Página

$nombre_presupuesto="PRESUPUESTO";

$pdf->setColor('0.00','0.00','0.00','');

$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_inicial-14);
$pdf->ezText("$empresa_curso
$domicilio_centro_completo
$CP_centro_completo $poblacion_centro_completo
Teléfono: $telefono_centro_completo",10,array('justification'=>'left','aleft'=>75));
if ($ciclo==1) {
if (strlen($persona_contacto)!=0) {
$pdf->ezText("

Alumno: $nombre_alu $apellido1_alu $apellido2_alu

Sr./Sra. $persona_contacto

Le informo, como padre/madre o tutor de un alumno que ocupará un puesto escolar en Primer Ciclo de Educación Infantil, que para el curso escolar $nombre_curso el citado puesto ha sido subvencionado por la Conselleria de Educación, según los importes que se detallan a continuación:

",8,array('justification'=>'left','aleft'=>75));
} else {
$pdf->ezText("

Alumno: $nombre_alu $apellido1_alu $apellido2_alu

Sr./Sra. _____________________________________________________________

Le informo, como padre/madre o tutor de un alumno que ocupará un puesto escolar en Primer Ciclo de Educación Infantil, que para el curso escolar $nombre_curso el citado puesto ha sido subvencionado por la Conselleria de Educación, según los importes que se detallan a continuación:

",8,array('justification'=>'left','aleft'=>75));
}
} else {
if (strlen($persona_contacto)!=0) {
$pdf->ezText("

Alumno: $nombre_alu $apellido1_alu $apellido2_alu

Sr./Sra. $persona_contacto

Le informo, como padre/madre o tutor de un alumno que ocupará un puesto escolar en Segundo Ciclo de Educación Infantil, que para el curso escolar $nombre_curso el citado puesto ha sido subvencionado por la Conselleria de Educación, según los importes que se detallan a continuación:

",8,array('justification'=>'left','aleft'=>75));
} else {
$pdf->ezText("

Alumno: $nombre_alu $apellido1_alu $apellido2_alu

Sr./Sra. _____________________________________________________________

Le informo, como padre/madre o tutor de un alumno que ocupará un puesto escolar en Segundo Ciclo de Educación Infantil, que para el curso escolar $nombre_curso el citado puesto ha sido subvencionado por la Conselleria de Educación, según los importes que se detallan a continuación:

",8,array('justification'=>'left','aleft'=>75));
}
}

if ($modelo==1) {
                                $posicion_actual=$posicion_inicio_tabla-60;

                                $busca_notas=db_query("SELECT * FROM tarifa, tipos_iva_servicios,tipos_deslose_facturacion WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tarifa.desglosar_como=tipos_deslose_facturacion.id_desglose AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=$id_curso_contacto OR tarifa.id_curso=0) AND tipos_deslose_facturacion.id_desglose=3 ORDER BY tipos_deslose_facturacion.id_desglose DESC,tarifa.ARTICULO") or die(db_error());

                                $familia_actual="";
                                $contador=0;
                                $total_familia=0;
                                $total_total=0;
                                $array_codigos="";

                                $hay_subvenciones=false;

                                while($datos_notas=db_fetch_array($busca_notas)) {

                                $id_servicio=$datos_notas["ID"];
                                
                                // Comprueba si hay que mostrar el servicio
                                
                                $cantidad_actual=0;

                                $busca_linea_presupuesto=db_query("SELECT * FROM servicios_contratados WHERE id_alumno=$id_alumno AND id_servicio=$id_servicio") or die(db_error());
                                if (db_num_rows($busca_linea_presupuesto)!=0) {

                                        $datos_linea_presupuesto=db_fetch_array($busca_linea_presupuesto);
                                        $cantidad_actual=$datos_linea_presupuesto["cantidad"];

                                        }
                                $busca_linea_presupuesto_aula=db_query("SELECT * FROM servicios_contratados_aula WHERE id_aula=$id_aula_presupuesto AND id_servicio=$id_servicio") or die(db_error());
                                if (db_num_rows($busca_linea_presupuesto_aula)!=0) {

                                        $datos_linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);

                                        // Busca si anulado
                                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_linea_presupuesto_aula["id_servicio_contratado"]." AND id_usuario=$id_alumno");
                                        if (db_num_rows($busca_si_activado)==0) {
                                                $cantidad_actual=$datos_linea_presupuesto_aula["cantidad"];
                                                }

                                        }

                                if ($cantidad_actual!=0) {

                                if ($familia_actual!=$datos_notas["nombre_desglose"]) {

                                        $familia_actual=$datos_notas["nombre_desglose"];
                                        $pdf->selectFont('./fonts/Helvetica-Bold');
                                        $pdf->setColor('00','00','0.50','');
                                        $posicion_actual=$posicion_actual-28;
                                        $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText("CONCEPTO",9,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText("IMPORTE",9,array('justification'=>'right','aright'=>485));

                                        $posicion_actual=$posicion_actual-14;
                                        $pdf->line(85,$posicion_actual,515,$posicion_actual);

                                        $total_familia=0;
                                        }

                                $total_actual=round(($cantidad_actual*$datos_notas["PRECIO_VENTA"]),2);
                                $total_total=$total_total+$total_actual;
                                $total_familia=$total_familia+$total_actual;
                                $texto_detalle=$texto_detalle."";
                                $nombre_articulo=$datos_notas["ARTICULO"];

                                // Comprueba si es subvención

                                if ($datos_notas["SUBVENCION"]) {
                                        $hay_subvenciones=true;
                                        }

                                $pdf->selectFont('./fonts/Helvetica');
                                $pdf->setColor('00','00','0.00','');
                                $pdf->ezSetY($posicion_actual);

                                $pdf->ezText($nombre_articulo,8,array('justification'=>'left','aleft'=>95));
                                $pdf->ezSetY($posicion_actual);
                                $pdf->ezText(number_format($total_actual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                $posicion_actual=$posicion_actual-12;

                                }

                                if ($posicion_actual<=$limite_inferior) {
                                        $posicion_actual=$posicion_inicial;
                                        $pdf->ezNewPage();
                                        // include "encabezado_carta.php";
                                        }
                                $contador++;
                                }
                                        if (($familia_actual!="") and ($tipo_presupuesto==0)) {
                                                $pdf->selectFont('./fonts/Helvetica');
                                                $posicion_actual=$posicion_actual-2;
                                                $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                                $pdf->ezSetY($posicion_actual);
                                                 $pdf->ezText("Quota real a pagar per les families (alumne/mes)",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->selectFont('./fonts/Helvetica-Oblique');
                                                 $pdf->ezText("<i>Cuota real a pagar por las familias (alumno/mes)</i>",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->selectFont('./fonts/Helvetica');
                                                 $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($total_familia, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                }
                                        if (($ciclo==1) and ($tope_mes[$edad_aula]>0)) {
                                                $couta_maxima_mensual=$tope_mes[$edad_aula];
                                                $precio_coste_mensual=$precio_coste[$edad_aula];
                                                $posicion_actual=$posicion_actual-20;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Oblique');
                                                $pdf->ezText("<i>Coste del puesto escolar únicamente por enseñanza (alumno/mes)</i>",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($precio_coste_mensual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $pdf->selectFont('./fonts/Helvetica');
                                                $posicion_actual=$posicion_actual-12;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Oblique');
                                                $pdf->ezText("<i>Cuota máxima mensual a pagar por las familias</i>",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($couta_maxima_mensual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $pdf->selectFont('./fonts/Helvetica');
                                                }
                                        if (($ciclo==2) and ($capacidad_aula>0) and ($tope_maximo2>0) and ($subvencion2>0)) {
                                                $CM=($tope_maximo2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                                                $SM=($subvencion2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                                                $couta_maxima_mensual=$CM-$SM;
                                                $posicion_actual=$posicion_actual-28;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText("QUOTA MÀXIMA MENSUAL AUTORITZADA",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->selectFont('./fonts/Helvetica-Oblique');
                                                $pdf->ezText("<i>CUOTA MÁXIMA MENSUAL AUTORIZADA</i>",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->selectFont('./fonts/Helvetica');
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($couta_maxima_mensual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                }

        $posicion_actual=$posicion_actual-60;

        } else {

        // Modelo detallado

                                $posicion_actual=$posicion_inicio_tabla-30;

                                $busca_notas=db_query("SELECT * FROM tarifa, tipos_iva_servicios,tipos_deslose_facturacion WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tarifa.desglosar_como=tipos_deslose_facturacion.id_desglose AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=$id_curso_contacto OR tarifa.id_curso=0) ORDER BY tipos_deslose_facturacion.id_desglose DESC,tarifa.ARTICULO") or die(db_error());

                                $familia_actual="";
                                $contador=0;
                                $total_familia=0;
                                $total_total=0;
                                $array_codigos="";
                                $desglosar_como_actual=-1;
                                $comedor_mostrado=false;
                                $hay_subvenciones=false;

                                while($datos_notas=db_fetch_array($busca_notas)) {

                                $id_servicio=$datos_notas["ID"];
                                
                                // Comprueba si hay que mostrar el servicio
                                $esta_anulado=false;
                                
                                for ($c_array=0;$c_array<=count($array_idservicios);$c_array++) {
                                        if ($array_idservicios[$c_array]==$id_servicio) {
                                                $esta_anulado=true;
                                                }
                                        }
                                
                                if ($esta_anulado==false) {
                                
                                $cantidad_actual=0;

                                $busca_linea_presupuesto=db_query("SELECT * FROM servicios_contratados WHERE id_alumno=$id_alumno AND id_servicio=$id_servicio") or die(db_error());
                                if (db_num_rows($busca_linea_presupuesto)!=0) {

                                        $datos_linea_presupuesto=db_fetch_array($busca_linea_presupuesto);
                                        $cantidad_actual=$datos_linea_presupuesto["cantidad"];

                                        }
                                $busca_linea_presupuesto_aula=db_query("SELECT * FROM servicios_contratados_aula WHERE id_aula=$id_aula_presupuesto AND id_servicio=$id_servicio") or die(db_error());
                                if (db_num_rows($busca_linea_presupuesto_aula)!=0) {

                                        $datos_linea_presupuesto_aula=db_fetch_array($busca_linea_presupuesto_aula);

                                        // Busca si anulado
                                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_linea_presupuesto_aula["id_servicio_contratado"]." AND id_usuario=$id_alumno");
                                        if (db_num_rows($busca_si_activado)==0) {
                                                $cantidad_actual=$datos_linea_presupuesto_aula["cantidad"];
                                                }

                                        }

                                if ($cantidad_actual!=0) {

                                if ($familia_actual!=$datos_notas["nombre_desglose"]) {

                                        if (($desglosar_como_actual==2) and ($comedor_mostrado==false)) {

                                                // Busca comedor

                                                if (($comedor_cliente==3) or ($comedor_cliente==1)) {

                                                $nombre_servicio_comedor="Comedor y custodia";

                                                $precio_servicio_comedor=$precio_mes[$tipo_menu_cliente];

                                                $pdf->selectFont('./fonts/Helvetica');
                                                $pdf->setColor('00','00','0.00','');
                                                $pdf->ezSetY($posicion_actual);

                                                $pdf->ezText($nombre_servicio_comedor,8,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($precio_servicio_comedor, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $posicion_actual=$posicion_actual-12;

                                                }
                                                $comedor_mostrado=true;
                                                // Fin comedor
                                                }

                                        if (($desglosar_como_actual==3) and ($tipo_presupuesto==0)) {
                                                $pdf->selectFont('./fonts/Helvetica');
                                                $posicion_actual=$posicion_actual-2;
                                                $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica');
                                                 $pdf->ezText("<i>Cuota real a pagar por las familias en concepto de escolaridad (alumno/mes)</i>",8,array('justification'=>'left','aleft'=>95));
                                                 $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($total_familia, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                }
                                        if (($desglosar_como_actual==3) and ($ciclo==1) and ($tope_mes[$edad_aula]>0)) {
                                                $couta_maxima_mensual=$tope_mes[$edad_aula];
                                                $precio_coste_mensual=$precio_coste[$edad_aula];
                                                $posicion_actual=$posicion_actual-20;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Oblique');
                                                $pdf->ezText("<i>Coste del puesto escolar únicamente por enseñanza (alumno/mes)</i>",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($precio_coste_mensual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $pdf->selectFont('./fonts/Helvetica');
                                                $posicion_actual=$posicion_actual-12;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Oblique');
                                                $pdf->ezText("<i>Cuota máxima mensual a pagar por las familias</i>",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($couta_maxima_mensual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $pdf->selectFont('./fonts/Helvetica');
                                                }
                                        if (($desglosar_como_actual==3) and ($ciclo==2) and ($capacidad_aula>0) and ($tope_maximo2>0) and ($subvencion2>0)) {
                                                $CM=($tope_maximo2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                                                $SM=($subvencion2*$numero_aulas2_ciclo/10)/$ocupacion_actual;
                                                $couta_maxima_mensual=$CM-$SM;
                                                $posicion_actual=$posicion_actual-28;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica');
                                                $pdf->ezText("CUOTA MAXIMA MENSUAL AUTORIZADA EN CONCEPTO DE ESCOLARIDAD",8,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($couta_maxima_mensual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                }

                                        $desglosar_como_actual=$datos_notas["desglosar_como"];
                                        $familia_actual=$datos_notas["nombre_desglose"];
                                        $pdf->selectFont('./fonts/Helvetica-Bold');
                                        $pdf->setColor('00','00','0.50','');
                                        $posicion_actual=$posicion_actual-28;
                                        $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText($familia_actual,9,array('justification'=>'left','aleft'=>95));
                                        $posicion_actual=$posicion_actual-11;
                                        $pdf->ezText("CONCEPTO",9,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText("IMPORTE",9,array('justification'=>'right','aright'=>485));

                                        $posicion_actual=$posicion_actual-14;
                                        $pdf->line(85,$posicion_actual,515,$posicion_actual);

                                        $total_familia=0;
                                        }

                                $total_actual=round(($cantidad_actual*$datos_notas["PRECIO_VENTA"]),2);
                                $total_total=$total_total+$total_actual;
                                $total_familia=$total_familia+$total_actual;
                                $texto_detalle=$texto_detalle."";
                                $nombre_articulo=$datos_notas["ARTICULO"];

                                // Comprueba si es subvención

                                if ($datos_notas["SUBVENCION"]) {
                                        $hay_subvenciones=true;
                                        }

                                $pdf->selectFont('./fonts/Helvetica');
                                $pdf->setColor('00','00','0.00','');
                                $pdf->ezSetY($posicion_actual);

                                $pdf->ezText($nombre_articulo,8,array('justification'=>'left','aleft'=>95));
                                $pdf->ezSetY($posicion_actual);
                                $pdf->ezText(number_format($total_actual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                $posicion_actual=$posicion_actual-12;

                                }

                                if ($posicion_actual<=$limite_inferior) {
                                        $posicion_actual=$posicion_inicial;
                                        $pdf->ezNewPage();
                                        // include "encabezado_carta.php";
                                        }
                                $contador++;
                                
                                }
                                }

                                        if ($comedor_mostrado==false) {

                                                // Busca comedor

                                                if (($comedor_cliente==3) or ($comedor_cliente==1)) {

                                                $nombre_servicio_comedor="Comedor y custodia";
                                                
                                                $precio_servicio_comedor=$precio_mes[$tipo_menu_cliente];

                                                $busca_familias=db_query("SELECT * FROM tipos_deslose_facturacion WHERE id_desglose=2") or die(db_error());
                                                $datos_familias=db_fetch_array($busca_familias);

                                                $familia_actual=$datos_familias["nombre_desglose"];
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $pdf->setColor('00','00','0.50','');
                                                $posicion_actual=$posicion_actual-28;
                                                $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText($familia_actual,9,array('justification'=>'left','aleft'=>95));
                                                $posicion_actual=$posicion_actual-11;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText("CONCEPTO",9,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText("IMPORTE",9,array('justification'=>'right','aright'=>485));

                                                $posicion_actual=$posicion_actual-14;
                                                $pdf->line(85,$posicion_actual,515,$posicion_actual);

                                                $pdf->selectFont('./fonts/Helvetica');
                                                $pdf->setColor('00','00','0.00','');
                                                $pdf->ezSetY($posicion_actual);

                                                $pdf->ezText($nombre_servicio_comedor,8,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($precio_servicio_comedor, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $posicion_actual=$posicion_actual-12;

                                                }
                                                $comedor_mostrado=true;
                                                // Fin comedor
                                                }

        $posicion_actual=$posicion_actual-30;

        }

        $pdf->ezSetY($posicion_actual);

$pdf->ezText("Valencia, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"),8,array('justification'=>'left','aleft'=>'75'));

$posicion_actual=$posicion_actual-18;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("Titular o representante de la titularidad del centro",8,array('justification'=>'center'));
$posicion_actual=$posicion_actual-60;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("Firma:______________________________________________",8,array('justification'=>'center'));
$posicion_actual=$posicion_actual-18;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("El padre, madre o tutor",8,array('justification'=>'center'));
$posicion_actual=$posicion_actual-60;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("Firma:______________________________________________",8,array('justification'=>'center'));
// FIN PAGINA 2

$pdf->ezNewPage();

}

$pdf->ezStream();

?>
