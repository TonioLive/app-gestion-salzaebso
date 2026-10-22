<?php  require("../../configuracion/config.php"); ?>
<?php
require("../../configuracion/autentifica_padre.php");

$id_alumno=$_GET["id_alumno"];

// Comprueba si el padre es el padre…
$id_alumno_cliente=$id_alumno;

$consulta_datos_del_alumno=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=?', 's', array($id_alumno_cliente));
if (db_num_rows($consulta_datos_del_alumno)!=0) {
        				$datos_del_alumno=db_fetch_array($consulta_datos_del_alumno);
        				$id_usuario_alumno=$datos_del_alumno["id_usuario"];
						}
						
$nombre_usuario_padre=$_SESSION['nombre_padre'];

// Busca usuario
$busca_usuario_padre=db_query_params('SELECT * FROM padres WHERE usuario_webcam=?', 's', array((string)($nombre_usuario))) or die(db_error());
        
if (db_num_rows($busca_usuario_padre)==0) {                
                $id_usuario_padre=0;
                } else {
                $datos_usuario_padre=db_fetch_array($busca_usuario_padre);
                $id_usuario_padre=$datos_usuario_padre["id_usuario"];
                }

$busca_hijo_de_padre=db_query_params('SELECT * FROM padres WHERE id_usuario=? AND padre_de=?', 'ss', array($id_usuario_padre, $id_usuario_alumno)) or die(db_error());

if (db_num_rows($busca_hijo_de_padre)!=0) {

$trimestre=$_GET["trimestre"];
$id_aula=$_GET["id_aula"];

$trimestre_numeral="er";

if ($trimestre==2) {
	$trimestre_numeral="º";
	}

// Busca si el trimestre es acceisble

$busca_evaluaciones=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado WHERE trimestre=$trimestre AND id_aula=".$id_aula." AND estado=1");

if (db_num_rows($busca_evaluaciones)==0) {
	die("<br /><br /><br /><br /><br /><p align=center><font face=Arial size=2>El informe de evaluación solicitado no está disponible</font></p>");
	}

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

$nombre_teacher="";

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        $id_curso_aula=$datos_aula["id_curso"];
        
        $ciclo_aula=$datos_aula["ciclo"];
        if ($ciclo_aula==0) {
        	$ciclo_aula=1;
        	}
        	
        // Busca educadora
        $busca_actual=db_query_params('SELECT * FROM personal_historial WHERE id_aula=? AND fecha_inicio<=? AND fecha_fin>=?;', 'sss', array($id_aula, (string)(date("Y-m-d")), (string)(date("Y-m-d"))));
        
        $id_educadora=0;
        if (db_num_rows($busca_actual)!=0) {	
        	$datos_actual=db_fetch_array($busca_actual);
        	$id_educadora=$datos_actual["id_empleado"];
        	}
        
        
        // Busca nombre educadora
		$busca_nombre_educadora=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_educadora));
		
		if (db_num_rows($busca_nombre_educadora)!=0) {
			$datos_educadora=db_fetch_array($busca_nombre_educadora);
			
			$nombre_teacher=strtoupper($datos_educadora["Nombre"]);
			}
			
        // Busca datos curso
		$busca_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_aula)) or die(db_error());
		
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

                                $busca_objetivos=db_query_params('SELECT * FROM agenda_programacion WHERE id_aula=? AND fecha>=? AND fecha<=?;', 'sss', array($id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());

                                                                while($cuenta_objetivos=db_fetch_array($busca_objetivos)){

                                                                        // Objetivo principal

                                                                        $id_objetivo1=$cuenta_objetivos["objetivo"];

                                                                        // Busca area
                                                                        $busca_datos_objetivo1=db_query_params('SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo1));
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
                                                                                $busca_datos_objetivo2=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_sec[$contador_objetivos_sec]);
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



                                $busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? AND alumnos.id_alumno=? ORDER BY Apellido1, Apellido2, Nombre', 'ssss', array($id_aula, (string)(date("Y-m-t", strtotime($fecha_fin))), (string)(date("Y-m-01", strtotime($fecha_ini))), $id_alumno)) or die(db_error());

                                if (db_num_rows($busca_alumnos)!=0) {

                                while($listado_alumnos=db_fetch_array($busca_alumnos)){

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
$pdf->ezText("EVALUACION ".$trimestre."er. TRIMESTRE",18,array('justification'=>'center'));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText(" ",12,array('justification'=>'center'));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("$nombre_aula",12,array('justification'=>'center'));

$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NOMBRE:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"]." - <b>EDUCADORA:</b>   ".$nombre_teacher,10,array('justification'=>'center'));

                                        // Limpia tabla temp
                                        $limpia_tabla=db_query_params('DELETE FROM agenda_programacion_objetivos_temp WHERE id_alumno=?', 's', array($listado_alumnos["id_alumno"]));

$pagina_actual=1;

                                        for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                        $num_objetivos_trabajados=count($array_objetivos_area[$c_areas]);

                                        for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++){
                                           $id_objetivo=$array_objetivos_area[$c_areas][($contador_objetivos-1)];

                                           $busca_datos_objetivo=db_query_params('SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo));
                                           $datos_objetivos=db_fetch_array($busca_datos_objetivo);

                                           $porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];

                                           $num_actividades_programadas=0;
                                           $num_actividades_trabajadas=0;
                                           $num_actividades_superadas=0;

                                           // Busca actividades trabajadas
                                           $busca_actividades=db_query_params('SELECT * FROM agenda_programacion WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());

                                           while($datos_actividades=db_fetch_array($busca_actividades)) {

                                                   // Busca evaluación
                                                   $busca_evaluacion=db_query_params('SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_prog=? AND id_alumno=?', 'ss', array($datos_actividades["id_prog"], $listado_alumnos["id_alumno"]));

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
                                                        $inserta_fila=db_query_params('INSERT INTO agenda_programacion_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area) VALUES (?,?,?,?,?);', 'sssss', array((string)($id_objetivo), (string)($datos_objetivos["id_evaluacion"]), (string)($listado_alumnos["id_alumno"]), (string)($superado), (string)($c_areas)));
                                                        }
                                                }

                                           // Busca y muestra
                                           $busca_evaluaciones=db_query("SELECT id_evaluacion,count(id_objetivo) AS num_objetivos,sum(superado) AS objetivos_superados FROM agenda_programacion_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND id_area=$c_areas GROUP BY id_evaluacion ORDER BY id_evaluacion ");
                                           while($datos_evaluaciones=db_fetch_array($busca_evaluaciones)) {
                                                if (($datos_evaluaciones["objetivos_superados"]/$datos_evaluaciones["num_objetivos"])>0.5) {
                                                        $calificacion=" S";
                                                        } else {
                                                        $calificacion=" P";
                                                        }
                                                        // Busca datos envaluacion
                                                        $busca_evaluacion=db_query_params('SELECT * FROM agenda_evaluacion_items WHERE id_evaluacion=?', 's', array($datos_evaluaciones["id_evaluacion"])) or die(db_error());
                                                        $datos_evaluacion=db_fetch_array($busca_evaluacion);
// Busca envaluacion guardada, si no está la crea
                                                        
                                                        $busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$listado_alumnos["id_alumno"]." AND id_evaluacion=".$datos_evaluaciones["id_evaluacion"].";");
                                                        
                                                        if (db_num_rows($busca_evaluacion_guardada)!=0) {
                                                        	$datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
                                                        	$id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
                                                        	$valor_calificacion=$datos_evaluacion_guardada["nota"];
                                                        	
															if ($valor_calificacion==1) {
                                                        		$calificacion=" S";
                                                        		} else {
                                                        		$calificacion=" P";
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


if ($posicion_actual<150) {
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
$pdf->ezSetY($posicion_actual);
$pdf->ezText("S = SUPERADO                   P = EN PROGRESO
Página ".$pagina_actual,10,array('justification'=>'center'));

$pagina_actual++;
$pdf->ezSetY($posicion_actual);
$pdf->ezNewPage();

	// encabezado
$posicion_inicial=825;

$pdf->selectFont('./fonts/Helvetica');

$pdf->ezSetY($posicion_inicial);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("EVALUACION ".$trimestre."er. TRIMESTRE",18,array('justification'=>'center'));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText(" ",12,array('justification'=>'center'));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("$nombre_aula",12,array('justification'=>'center'));

$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NOMBRE:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"]." - <b>EDUCADORA:</b>   ".$nombre_teacher,10,array('justification'=>'center'));

	}

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
$pdf->ezSetY($posicion_actual);
$pdf->ezText("S = SUPERADO                   P = EN PROGRESO
Página ".$pagina_actual,10,array('justification'=>'center'));

$pdf->ezSetY($posicion_actual);
$pdf->ezNewPage();
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