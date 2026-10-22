<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php"); ?>
<?php
require("../../configuracion/autentifica_padre.php");

$nombre_teacher="SARA";

$id_alumno=$_GET["id_alumno"];

// Comprueba si el padre es el padre…
$id_alumno_cliente=$id_alumno;

$consulta_datos_del_alumno=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=".$id_alumno_cliente);
if (db_num_rows($consulta_datos_del_alumno)!=0) {
        				$datos_del_alumno=db_fetch_array($consulta_datos_del_alumno);
        				$id_usuario_alumno=$datos_del_alumno["id_usuario"];
						}
						
$nombre_usuario_padre=$_SESSION['nombre_padre'];

// Busca usuario
$busca_usuario_padre=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'") or die(db_error());
        
if (db_num_rows($busca_usuario_padre)==0) {                
                $id_usuario_padre=0;
                } else {
                $datos_usuario_padre=db_fetch_array($busca_usuario_padre);
                $id_usuario_padre=$datos_usuario_padre["id_usuario"];
                }

$busca_hijo_de_padre=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario_padre AND padre_de=$id_usuario_alumno") or die(db_error());

if (db_num_rows($busca_hijo_de_padre)!=0) {

$trimestre=$_GET["trimestre"];
$id_aula=$_GET["id_aula"];

$trimestre_numeral="er";

if ($trimestre==2) {
	$trimestre_numeral="º";
	}

// Busca si el trimestre es acceisble

$busca_evaluaciones=db_query("SELECT * FROM agenda_programacion_ingles_evaluaciones_estado WHERE trimestre=$trimestre AND id_aula=".$id_aula." AND estado=1") or die(db_error());

if (db_num_rows($busca_evaluaciones)==0) {
	die("<br /><br /><br /><br /><br /><p align=center><font face=Arial size=2>El informe de evaluación solicitado no está disponible</font></p>");
	}

$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula);

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        $id_curso_aula=$datos_aula["id_curso"];

        $edad_ini_aula=$datos_aula["edad_minima_prog"];  
        $edad_fin_aula=$datos_aula["edad_maxima_prog"]; 
        
        $ciclo_aula=$datos_aula["ciclo"];
        if ($ciclo_aula==0) {
        	$ciclo_aula=1;
        	}
        
        // Busca datos curso
		$busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$id_curso_aula) or die(db_error());
		
		if (db_num_rows($busca_curso)!=0){
        	$datos_curso=db_fetch_array($busca_curso);

        	$fecha_ini=$datos_curso["inicio_trim".$trimestre."_".$ciclo_aula];
			$fecha_fin=$datos_curso["fin_trim".$trimestre."_".$ciclo_aula];
        	
        	}
                
        }
        
                                // Busca objetivos trabajados
                                $contador_areas=0;
                                $contador_total=0;
                                $areas=array("ÁREA I. CONOCIMIENTO DE SÍ MISMO Y AUTONOMIA PERSONAL","ÁREA II. CONOCIMIENTO DEL ENTORNO","ÁREA III. LENGUAJES: COMUNICACIÓN Y REPRESENTACION");
                                for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                $array_objetivos_area[$c_areas]=array();
                                $contador_array_obj=0;

                                // $busca_objetivos=mysql_query("SELECT * FROM agenda_programacion_ingles WHERE id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."';") or die(mysql_error());
								if ($id_curso_aula>=28) {
									$busca_objetivos=db_query("SELECT agenda_programacion_ingles_objetivos.* FROM agenda_programacion_ingles_objetivos,agenda_evaluacion_ingles_items WHERE agenda_programacion_ingles_objetivos.id_evaluacion=agenda_evaluacion_ingles_items.id_evaluacion AND agenda_programacion_ingles_objetivos.edad>=$edad_ini_aula AND agenda_programacion_ingles_objetivos.edad<=$edad_fin_aula AND agenda_programacion_ingles_objetivos.version=2 AND (agenda_evaluacion_ingles_items.trimestre=".$trimestre." OR agenda_evaluacion_ingles_items.trimestre=0) ORDER BY agenda_programacion_ingles_objetivos.id_objetivo") or die(db_error());
								} else {	
									$busca_objetivos=db_query("SELECT * FROM agenda_programacion_ingles WHERE id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."';") or die(db_error());
								}
								
								
                                                                while($cuenta_objetivos=db_fetch_array($busca_objetivos)){

                                                                        // Objetivo principal

                                                                        if ($id_curso_aula>=28) {
                                                                                $id_objetivo1=$cuenta_objetivos["id_objetivo"];
                                                                                } else {
                                                                                $id_objetivo1=$cuenta_objetivos["objetivo"];
                                                                                }

                                                                        // Busca area
                                                                        $busca_datos_objetivo1=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=$id_objetivo1");
                                    $datos_objetivos1=db_fetch_array($busca_datos_objetivo1);

                                    if ($datos_objetivos1["id_area"]==($c_areas+1)) {

                                                                                if (in_array($id_objetivo1, $array_objetivos_area[$c_areas])==false) {
                                                                                        $array_objetivos_area[$c_areas][$contador_array_obj]=$id_objetivo1;
                                                                                        $contador_array_obj++;
                                                                                        $contador_total++;
                                                                                        }

                                                                                }

                                                                        // Objetivos secundarios
                                                                        $objetivos_secundarios=$cuenta_objetivos["objetivos"];
                                                                        if (strlen($objetivos_secundarios)!=0) {
                                                                                $objetivos_secundarios=str_replace("[","",$objetivos_secundarios);
                                                                                $objetivos_secundarios=str_replace("]","",$objetivos_secundarios);
                                                                                $array_objetivos_sec=explode(",",$objetivos_secundarios);

                                                                                for ($contador_objetivos_sec=0;$contador_objetivos_sec<count($array_objetivos_sec);$contador_objetivos_sec++){
                                                                                // Busca area
                                                                                $busca_datos_objetivo2=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=".$array_objetivos_sec[$contador_objetivos_sec]);
                                            $datos_objetivos2=db_fetch_array($busca_datos_objetivo2);

                                            if ($datos_objetivos2["id_area"]==($c_areas+1)) {
                                                                                                if (in_array($array_objetivos_sec[$contador_objetivos_sec], $array_objetivos_area[$c_areas])==false) {
                                                                                                        $array_objetivos_area[$c_areas][$contador_array_obj]=$array_objetivos_sec[$contador_objetivos_sec];
                                                                                                        $contador_array_obj++;
                                                                                                        $contador_total++;
                                                                                                        }

                                                                                                }
                                                                                        }

                                                                                }


                                                                        }
                                        }

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(75,75,75,75);

if ($contador_total!=0) {



$busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=$id_aula AND fecha_matricula<='".date("Y-m-t",strtotime($fecha_fin))."' AND fecha_baja>='".date("Y-m-01",strtotime($fecha_ini))."' AND alumnos.id_alumno=$id_alumno ORDER BY Apellido1, Apellido2, Nombre") or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {

	while($listado_alumnos=db_fetch_array($busca_alumnos)){

			$busca_estado_evaluacion_guardada_alumno=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND trimestre=$trimestre;") or die(db_error());		

			$datos_evaluacion_guardada_alumno=db_fetch_array($busca_estado_evaluacion_guardada_alumno);

// Aquí el logo
$x_logo=15;
$y_logo=705;
$pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');
$pdf->setLineStyle(0.25,'','','','');

$posicion_inicial=825;

$pdf->selectFont('./fonts/Helvetica');

$pdf->ezSetY($posicion_inicial);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("EVALUACION ".$trimestre.$trimestre_numeral.". TRIMESTRE",18,array('justification'=>'center'));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Inglés",12,array('justification'=>'center'));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("$nombre_aula",12,array('justification'=>'center'));

$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NAME:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"]." - <b>TEACHER:</b>   ".$nombre_teacher,10,array('justification'=>'center'));

                                        // Limpia tabla temp
                                        $limpia_tabla=db_query("DELETE FROM agenda_programacion_ingles_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]);

                                        for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                        $num_objetivos_trabajados=count($array_objetivos_area[$c_areas]);

                                        for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                           $id_objetivo=$array_objetivos_area[$c_areas][($contador_objetivos-1)];

                                           $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_ingles_objetivos WHERE id_objetivo=$id_objetivo");
                                           $datos_objetivos=db_fetch_array($busca_datos_objetivo);

                                           $porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];

                                           $num_actividades_programadas=0;
                                           $num_actividades_trabajadas=0;
                                           $num_actividades_superadas=0;

                                           // Busca actividades trabajadas
                                           $busca_actividades=db_query("SELECT * FROM agenda_programacion_ingles WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND (id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."') ORDER BY id_actividad ") or die(db_error());

                                           while($datos_actividades=db_fetch_array($busca_actividades)) {

                                                   // Busca evaluación
                                                   $busca_evaluacion=db_query("SELECT * FROM agenda_programacion_ingles_evaluacion_alumnos WHERE id_prog=".$datos_actividades["id_prog"]." AND id_alumno=".$listado_alumnos["id_alumno"]);

                                                   if (db_num_rows($busca_evaluacion)!=0) {

                                                        $datos_evaluacion=db_fetch_array($busca_evaluacion);

                                                        $num_actividades_trabajadas=$num_actividades_trabajadas+$datos_evaluacion["asistencia"];
                                                        $num_actividades_superadas=$num_actividades_superadas+$datos_evaluacion["superada"];

                                                        }
                                                $num_actividades_programadas++;
                                                }
                                                $porcentaje_calificacion=0;
                                                if ($num_actividades_programadas>0) {
                                                        $porcentaje_calificacion=round(($num_actividades_superadas/$num_actividades_programadas)*100,2);
                                                        }

                                                if ($porcentaje_calificacion>=$porcentaje_autoevaluacion) {
                                                        $calificacion="S";
                                                        $superado=1;
                                                        } else {
                                                        $calificacion="P";
                                                        $superado=0;
                                                        }
                                                // Inserta en tabla temp
                                                if ($datos_objetivos["id_evaluacion"]!=0) {
                                                        $inserta_fila=db_query("INSERT INTO agenda_programacion_ingles_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area) VALUES ('$id_objetivo','".$datos_objetivos["id_evaluacion"]."','".$listado_alumnos["id_alumno"]."','$superado','$c_areas');");
                                                        }
                                                }

                                           // Busca y muestra
                                           $busca_evaluaciones=db_query("SELECT id_evaluacion,count(id_objetivo) AS num_objetivos,sum(superado) AS objetivos_superados FROM agenda_programacion_ingles_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND id_area=$c_areas AND id_evaluacion<>0 GROUP BY id_evaluacion ORDER BY id_evaluacion ");
                                           while($datos_evaluaciones=db_fetch_array($busca_evaluaciones)) {
                                                if (($datos_evaluaciones["objetivos_superados"]/$datos_evaluaciones["num_objetivos"])>0.5) {
                                                        $calificacion=" S";
                                                        } else {
                                                        $calificacion=" P";
                                                        }                                                      
                                                        
                                                        // Busca datos envaluacion
                                                        $busca_evaluacion=db_query("SELECT * FROM agenda_evaluacion_ingles_items WHERE id_evaluacion=".$datos_evaluaciones["id_evaluacion"]) or die(db_error());
                                                        $datos_evaluacion=db_fetch_array($busca_evaluacion);

// Busca envaluacion guardada, si no está la crea
                                                        
                                                        $busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_ingles_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$listado_alumnos["id_alumno"]." AND id_evaluacion=".$datos_evaluaciones["id_evaluacion"].";");
                                                        
                                                        if (db_num_rows($busca_evaluacion_guardada)!=0) {
                                                        	$datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
                                                        	$id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
                                                        	$valor_calificacion=$datos_evaluacion_guardada["nota"];
                                                        	
                                                                if ($valor_calificacion==1) {
                                                                        $calificacion=" S";
                                                                        }
                                                                if ($valor_calificacion==0) {
                                                                        $calificacion=" P";
                                                                        }
                                                                if ($valor_calificacion==2) {
                                                                        $calificacion=" NT";
                                                                        }
                                                        	
                                                        	}


$pdf->selectFont('./fonts/Helvetica');
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->ezText($datos_evaluacion["descripcion"],10,array('justification'=>'left','aleft'=>75));
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText($calificacion,10,array('justification'=>'right','aright'=>530));
$pdf->selectFont('./fonts/Helvetica-Oblique');
$posicion_actual=$posicion_actual-14;
$pdf->ezSetY($posicion_actual);
$pdf->ezText($datos_evaluacion["descripcion_esp"]."
",7,array('justification'=>'left','aleft'=>75));
$pdf->line(75,$posicion_actual-17,530,$posicion_actual-17);

// NUEVA PAGINA
if ($posicion_actual<150) {
$posicion_actual=$posicion_actual-16;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica-Bold');
/*
$pdf->ezText("NOTES:",10,array('justification'=>'left','aleft'=>75));
$posicion_actual=$posicion_actual-12;
$pdf->line(115,$posicion_actual,530,$posicion_actual);
$posicion_actual=$posicion_actual-16;
$pdf->line(75,$posicion_actual,530,$posicion_actual);
$posicion_actual=$posicion_actual-16;
$pdf->line(75,$posicion_actual,530,$posicion_actual);
$posicion_actual=$posicion_actual-16;
$pdf->line(75,$posicion_actual,530,$posicion_actual);
*/
$posicion_actual=$posicion_actual-16;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("S = SUPERADO                   P = EN PROGRESO                   NT = NO TRABAJADO
Página ".$pagina_actual,10,array('justification'=>'center'));

$pagina_actual++;
$pdf->ezSetY($posicion_actual);
$pdf->ezNewPage();

// encabezado

// Aquí el logo
$x_logo=15;
$y_logo=705;
$pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);

// Color de las lineas Negro
$pdf->setStrokeColor('0.0','0.00','0.00','');
$pdf->setLineStyle(0.25,'','','','');

$posicion_inicial=825;

$pdf->selectFont('./fonts/Helvetica');

$pdf->ezSetY($posicion_inicial);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("EVALUACION ".$trimestre.$trimestre_numeral.". TRIMESTRE",18,array('justification'=>'center'));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Inglés",12,array('justification'=>'center'));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("$nombre_aula",12,array('justification'=>'center'));

$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NAME:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"]." - <b>TEACHER:</b>   ".$nombre_teacher,10,array('justification'=>'center'));

        }  // FIN NUEVA PAGINA

                                                }
                                                }


$posicion_actual=$posicion_actual-32;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica-Bold');
/*
$pdf->ezText("NOTES:",10,array('justification'=>'left','aleft'=>75));
$posicion_actual=$posicion_actual-12;
$pdf->line(115,$posicion_actual,530,$posicion_actual);
$posicion_actual=$posicion_actual-16;
$pdf->line(75,$posicion_actual,530,$posicion_actual);
$posicion_actual=$posicion_actual-16;
$pdf->line(75,$posicion_actual,530,$posicion_actual);
$posicion_actual=$posicion_actual-16;
$pdf->line(75,$posicion_actual,530,$posicion_actual);
*/

$posicion_actual=$posicion_actual-16;
$pdf->selectFont('./fonts/Helvetica');
if (strlen($datos_evaluacion_guardada_alumno["observaciones_ingles"])!=0) {
	$pdf->ezSetY($posicion_actual);
	$pdf->ezText(app_text_utf8($datos_evaluacion_guardada_alumno["observaciones_ingles"]),10,array('justification'=>'full'));
	$posicion_actual=$posicion_actual-85;
}

$posicion_actual=$posicion_actual-16;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("S = SUPERADO                   P = EN PROGRESO                   NT = NO TRABAJADO",10,array('justification'=>'center'));

$pdf->ezSetY($posicion_actual);
// $pdf->ezNewPage();
                                        }
                                        }
                                }


$pdf->ezStream();

} else {
// No es el padre de este niño
?>
<html>
<head>
</head>
<body>
<br><br><br>
<p align="center"><br><br><font face="Arial" size="2">No tiene autorización para acceder a la información de este alumno</font><br><br></p>
<p align="center"><font size=2 face="Trebuchet MS"><a href="javascript:window.close();"></a></font></p>
</body>
</html>
<?php 
}
?>