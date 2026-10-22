<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

// Datos
$id_presupuesto=$_GET["id_presupuesto"];

$tipo_presupuesto=$_GET["tipo"];
if (strlen($tipo_presupuesto)==0) {
        $tipo_presupuesto=0;
        }


if (strlen($id_presupuesto)!=0) {

        $busca_presupuesto=db_query("SELECT * FROM alumnos_presupuestos WHERE id_presupuesto=$id_presupuesto") or die(db_error());
        if (db_num_rows($busca_presupuesto)!=0) {
                $datos_presupuesto=db_fetch_array($busca_presupuesto);
                $id_contacto=$datos_presupuesto["id_contacto"];
                $id_aula_presupuesto=$datos_presupuesto["id_aula"];
                $id_curso_presupuesto=$datos_presupuesto["id_curso"];
                $observaciones=$datos_presupuesto["observaciones"];
                }

        $estado_inscripcion="";
        $info_aula_alumno="";
        $sexo_alumno_numero=0;

        if ($id_contacto!=0) {

        $busca_datos_contacto=db_query("SELECT * FROM contactos WHERE id_contacto=$id_contacto");
        if (db_num_rows($busca_datos_contacto)!=0) {
                $datos_contacto=db_fetch_array($busca_datos_contacto);
                $mensaje="Emisión presupuesto";
                $nombre_alu=$datos_contacto["Nombre"];
                $apellido1_alu=$datos_contacto["Apellido1"];
                $apellido2_alu=$datos_contacto["Apellido2"];
                $persona_contacto=$datos_contacto["persona_contacto"];
                $proceso_decision=$datos_contacto["proceso_decision"];
                $comentarios=$datos_contacto["comentarios"];
                $telefono=$datos_contacto["telefono"];
                $email=$datos_contacto["email"];
                $poblacion=$datos_contacto["poblacion"];
                $codigo_postal=$datos_contacto["codigo_postal"];
                $sexo=$datos_contacto["sexo"];
                if ($datos_contacto["sexo"]=="H") {
                        $sexo_alumno_numero=1;
                        } else {
                        $sexo_alumno_numero=0;
                        }
                $id_curso_contacto=$datos_contacto["id_curso"];
                $id_centro_contacto=$datos_contacto["id_centro"];

                        $fecha_nacimiento_dia=date("j",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_mes=date("m",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_ano=date("Y",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_dia1=date("d",strtotime($datos_contacto["fecha_nacimiento"]));

                        if (($datos_contacto["fecha_nacimiento"]=="0000-00-00") or ($datos_contacto["fecha_nacimiento"]=="")) {
                                $fecha_nacimiento_dia="";
                                $fecha_nacimiento_mes="";
                                $fecha_nacimiento_ano="";
                                $edad="";
                                } else {
                                $nacimiento=explode( '-', $datos_contacto["fecha_nacimiento"]);
                                $edad=date("Y")-$nacimiento[0];
                                $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                                $hoy=strtotime(date("Y/m/d"));
                                if (($cumple-$hoy)>0) {
                                        $edad=$edad-1;
                                        }
                                        if ($edad<2) {
                                                // 1. Comprueba la edad restando el año de nacimiento al año actual
                                                $fecha_l=$datos_contacto["fecha_nacimiento"];
                                                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                                // 1.1. Cacula edad con decimales
                                                $mes_d_l=date("m",strtotime($fecha_l));
                                                $mes_d_n=date("m");
                                                $dif_meses=$mes_d_n-$mes_d_l;

                                                $edad_d_l=($edad_a_l*12)+$dif_meses;
                                                $edad=$edad_d_l." meses";
                                                } else {
                                                $edad=$edad." años";
                                                }
                                }
                        $fecha_b1_dia=date("j",strtotime($datos_contacto["fecha_b1"]));
                        $fecha_b1_mes=date("m",strtotime($datos_contacto["fecha_b1"]));
                        $fecha_b1_ano=date("Y",strtotime($datos_contacto["fecha_b1"]));
                        if (($datos_contacto["fecha_b1"]=="0000-00-00") or ($datos_contacto["fecha_b1"]=="")) {
                                $fecha_b1_dia="";
                                $fecha_b1_mes="";
                                $fecha_b1_ano="";
                                }
                        $fecha_b2_dia=date("j",strtotime($datos_contacto["fecha_b2"]));
                        $fecha_b2_mes=date("m",strtotime($datos_contacto["fecha_b2"]));
                        $fecha_b2_ano=date("Y",strtotime($datos_contacto["fecha_b2"]));
                        if (($datos_contacto["fecha_b2"]=="0000-00-00") or ($datos_contacto["fecha_b2"]=="")) {
                                $fecha_b2_dia="";
                                $fecha_b2_mes="";
                                $fecha_b2_ano="";
                                }
                        $fecha_b3_dia=date("j",strtotime($datos_contacto["fecha_b3"]));
                        $fecha_b3_mes=date("m",strtotime($datos_contacto["fecha_b3"]));
                        $fecha_b3_ano=date("Y",strtotime($datos_contacto["fecha_b3"]));
                        if (($datos_contacto["fecha_b3"]=="0000-00-00") or ($datos_contacto["fecha_b3"]=="")) {
                                $fecha_b3_dia="";
                                $fecha_b3_mes="";
                                $fecha_b3_ano="";
                                }
                        $fecha_c_dia=date("j",strtotime($datos_contacto["fecha_c"]));
                        $fecha_c_mes=date("m",strtotime($datos_contacto["fecha_c"]));
                        $fecha_c_ano=date("Y",strtotime($datos_contacto["fecha_c"]));
                        if (($datos_contacto["fecha_c"]=="0000-00-00") or ($datos_contacto["fecha_c"]=="")) {
                                $fecha_c_dia="";
                                $fecha_c_mes="";
                                $fecha_c_ano="";
                                }

                $nuevo_alumno=$datos_contacto["nuevo_alumno"];
                $id_usuario=$datos_contacto["id_usuario"];

                if (intval($id_usuario)!=0) {
                        $busca_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario");
                        if (db_num_rows($busca_datos_usuario)!=0) {
                                $datos_usuario=db_fetch_array($busca_datos_usuario);
                                $texto_id_usuario="($id_usuario) ".$datos_usuario["Nombre"]." ".$datos_usuario["Apellido1"]." ".$datos_usuario["Apellido2"];

                                // Comprueba si el alumno está inscrito en el curso seleccionado
                                $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos WHERE id_curso=$id_curso_contacto AND id_centro=$id_centro_contacto AND id_usuario=$id_usuario");
                                if (db_num_rows($busca_inscripcion_curso_actual)!=0) {
                                        $datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual);
                                        $estado_inscripcion=" disabled";
                                        $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_inscripcion_curso_actual["aula"]);
                                        $datos_aula=db_fetch_array($busca_datos_aula);
                                        $info_aula_alumno="<br>Alumno inscrito en el centro seleccionado en el aula: ".$datos_aula["nombre"];
                                        }
                                } else {
                                $texto_id_usuario="($id_usuario) Usuario Desconocido";
                                }
                        } else {
                        $texto_id_usuario="";
                        }

                } else {
                $id_contacto="";
                $mensaje="Nuevo";
                }

                        $consulta_centros=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_contacto ORDER BY nombre_centro;");
                        if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_centro"];
                        $domicilio_centro_completo=$centros["domicilio_centro"];
                        $CP_centro_completo=$centros["CP_centro"];
                        $poblacion_centro_completo=$centros["poblacion_centro"];
                        $telefono_centro_completo=$centros["telefono_centro"];
                        } else {
                        $nombre_centro="";
                        }

                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_contacto") or die(db_error()."-4");
                        if (db_num_rows($consulta_cursos)!=0) {
                                $cursos=db_fetch_array($consulta_cursos);
                                $nombre_curso=$cursos["nombre"];
                                } else {
                                $nombre_curso="";
                                }
                } else {
                if (strlen($id_curso_presupuesto)!=0) {
                        $db_nombre_centro="";
                        $id_curso_contacto=$id_curso_presupuesto;
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_presupuesto") or die(db_error()."-4");
                        if (db_num_rows($consulta_cursos)!=0) {
                                $cursos=db_fetch_array($consulta_cursos);
                                $nombre_curso="CURSO: ".$cursos["nombre"];
                                } else {
                                $nombre_curso="";
                                }
                        }
                if (strlen($id_aula_presupuesto)!=0) {
                // datos aula

                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula_presupuesto");
                $datos_aula=db_fetch_array($busca_datos_aula);

                $nombre_aula="AULA: ".$datos_aula["nombre"];
                $id_centro_aula=$datos_aula["id_centro"];
                $id_curso_aula=$datos_aula["id_curso"];
                $id_curso_contacto=$id_curso_aula;
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_aula") or die(db_error()."-4");
                        if (db_num_rows($consulta_cursos)!=0) {
                                $cursos=db_fetch_array($consulta_cursos);
                                $nombre_curso="CURSO: ".$cursos["nombre"];
                                } else {
                                $nombre_curso="";
                                }

                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_aula");
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro="ESCUELA: ".$datos_centro["nombre_centro"];
                        
                        $nombre_centro_completo=$datos_centro["nombre_centro"];
                        $domicilio_centro_completo=$datos_centro["domicilio_centro"];
                        $CP_centro_completo=$datos_centro["CP_centro"];
                        $poblacion_centro_completo=$datos_centro["poblacion_centro"];
                        $telefono_centro_completo=$datos_centro["telefono_centro"];
                        
                        } else {
                        $db_nombre_centro="";
                        }
                        }
                }

// --- FIN DATOS ---

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(20,0,20,20);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.50','');

$altura_fila=28;
$numero_filas=11;
$inicio_filas=135;
$diferencia_1=0;
$limite_inferior=100;
$posicion_inicio_tabla=600;
$posicion_inicial=730;
$posicion_nueva_pagina=730;

// encabezado y pie de página

// Aquí el logo
$x_logo=422;
$y_logo=715;
// $pdf->addJpegFromFile('logo_factura.jpg',$x_logo,$y_logo,115,115);

$pdf->setLineStyle(1,'','','','');

$pdf->setColor('00','00','0.00','');

// Pie de Página

//$pdf->setColor('0.00','0.00','0.00','');
//$pdf->addText(30,30,6,"ZAEBSAL S.l. Empresa inscrita en el Registro Mercantil de la Provincia de Valencia. Tomo: 7468, Libro: 4769, Folio: 78, Hoja: V-89580. C.I.F. B-97324123");


$pdf->ezStartPageNumbers(536,30,7,'right','Página {PAGENUM} de {TOTALPAGENUM}',1);

// Pie de Página

if ($tipo_presupuesto==0) {
$nombre_presupuesto="PRESUPUESTO";
} else {
$nombre_presupuesto="LISTA DE PRECIOS";
}

$pdf->setColor('0.00','0.00','0.00','');

$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_inicial);
$pdf->ezSetY($posicion_inicial-14);
$pdf->ezText("Valencia, ".intval(date("d"))." de ".$nombre_mes[intval(date("m"))]." de ".date("Y"),9,array('justification'=>'right'));
$pdf->ezSetY($posicion_inicial-14);
$pdf->ezText("$domicilio_centro_completo
$CP_centro_completo $poblacion_centro_completo
Teléfono: $telefono_centro_completo",10,array('justification'=>'left','aleft'=>75));
if ($id_contacto!=0) {
$pdf->ezText("$nombre_presupuesto

CURSO: $nombre_curso
Alumno: $nombre_alu $apellido1_alu $apellido2_alu
Persona de contacto: $persona_contacto
",12,array('justification'=>'center'));
} else {
$pdf->ezText("$nombre_presupuesto

$nombre_curso
$nombre_aula

",12,array('justification'=>'center'));
}
$posicion_actual=$posicion_inicio_tabla;

                                $busca_notas=db_query("SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=$id_curso_contacto OR tarifa.id_curso=0) ORDER BY tarifa.CATEGORIA,tarifa.ARTICULO") or die(db_error());

                                $familia_actual="";
                                $contador=0;
                                $total_familia=0;
                                $total_total=0;
                                $array_codigos="";
                                while($datos_notas=db_fetch_array($busca_notas)) {
                                        
                                $id_servicio=$datos_notas["ID"];
                                $busca_linea_presupuesto=db_query("SELECT * FROM alumnos_presupuestos_detalles WHERE id_presupuesto=$id_presupuesto AND id_servicio=$id_servicio") or die(db_error());
                                if (db_num_rows($busca_linea_presupuesto)!=0) {
                                
                                                $datos_linea_presupuesto=db_fetch_array($busca_linea_presupuesto);
                                                $id_detalle=$datos_linea_presupuesto["id_detalle"];
                                                $cantidad_actual=$datos_linea_presupuesto["cantidad_servicio"];
                                                
                                if ($familia_actual!=$datos_notas["categoria"]) {
                                

                                        if (($familia_actual!="") and ($tipo_presupuesto==0)) {
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $posicion_actual=$posicion_actual-2;
                                                $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText("Total $familia_actual",8,array('justification'=>'right','aright'=>365));
                                                 $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($total_familia, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $posicion_actual=$posicion_actual-12;
                                                }

                                        $familia_actual=$datos_notas["categoria"];
                                        $pdf->selectFont('./fonts/Helvetica-Bold');
                                        $pdf->setColor('00','00','0.50','');
                                        $posicion_actual=$posicion_actual-12;
                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText("$familia_actual",12,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                        $posicion_actual=$posicion_actual-12;
                                        $posicion_actual=$posicion_actual-4;
                                        $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText("Servicio",9,array('justification'=>'left','aleft'=>95));
                                                $pdf->ezSetY($posicion_actual);
                                        if ($tipo_presupuesto==0) {
                                        $pdf->ezText("Precio",9,array('justification'=>'right','aright'=>365));
                                                $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText("Cantidad",9,array('justification'=>'right','aright'=>435));
                                                $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText("Total Servicio",9,array('justification'=>'right','aright'=>505));
                                        } else {
                                        $pdf->ezText("Precio",9,array('justification'=>'right','aright'=>505));
                                        }
                                        $posicion_actual=$posicion_actual-12;
                                        $posicion_actual=$posicion_actual-2;
                                        $pdf->line(85,$posicion_actual,515,$posicion_actual);

                                        $total_familia=0;
                                        }

                                $total_actual=round(($cantidad_actual*$datos_notas["PRECIO_VENTA"]),2);
                                $total_total=$total_total+$total_actual;
                                $total_familia=$total_familia+$total_actual;
                                $texto_detalle=$texto_detalle."";
                                $nombre_articulo=$datos_notas["ARTICULO"];

                                $pdf->selectFont('./fonts/Helvetica');
                                $pdf->setColor('00','00','0.00','');
                                $pdf->ezSetY($posicion_actual);

                                $pdf->ezText($nombre_articulo,8,array('justification'=>'left','aleft'=>95));
                                $pdf->ezSetY($posicion_actual);
                                if ($tipo_presupuesto==0) {
                                $pdf->ezText(number_format($datos_notas["PRECIO_VENTA"], 2, ',', '.')." €",8,array('justification'=>'right','aright'=>365));
                                $pdf->ezSetY($posicion_actual);
                                $pdf->ezText(number_format($cantidad_actual, 2, ',', '.'),8,array('justification'=>'right','aright'=>415));
                                $pdf->ezSetY($posicion_actual);
                                $pdf->ezText(number_format($total_actual, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                } else {
                                $pdf->ezText(number_format($datos_notas["PRECIO_VENTA"], 2, ',', '.')." €",8,array('justification'=>'right','aright'=>505));
                                }
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
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $posicion_actual=$posicion_actual-2;
                                                $pdf->line(85,$posicion_actual,515,$posicion_actual);
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText("Total $familia_actual",8,array('justification'=>'right','aright'=>365));
                                                 $pdf->ezSetY($posicion_actual);
                                                $pdf->ezText(number_format($total_familia, 2, ',', '.')." €",8,array('justification'=>'right','aright'=>485));
                                                $posicion_actual=$posicion_actual-12;
                                                }
        $pdf->line(85,$posicion_actual,515,$posicion_actual);
        $posicion_actual=$posicion_actual-18;
        $pdf->ezSetY($posicion_actual);
        
        if ($tipo_presupuesto==0) {
                $pdf->selectFont('./fonts/Helvetica-Bold');
                $pdf->setColor('00','00','0.00','');
                $pdf->ezText("Total presupuesto:  ".number_format($total_total, 2, ',', '.')." €",12,array('justification'=>'center'));
                }
if (strlen($observaciones)!=0) {
$observaciones=app_url_text_utf8($observaciones);
// $observaciones=UTF8_encode($observaciones);
$posicion_actual=$posicion_actual-16;
$pdf->ezSetY($posicion_actual);
$pdf->ezText($observaciones,10,array('justification'=>'center'));
}
$pdf->ezStream();
} else {
?>
Error: No se encuentra el presupuesto...
<?php 
}
?>
