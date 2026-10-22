<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php"); ?>
<?php
require("../configuracion/autentifica.php");

$id_alumno=$_GET["id_alumno"];
$trimestre=$_GET["trimestre"];

// Comprueba si el padre es el padre…
$id_alumno_cliente=$id_alumno;

$consulta_datos_del_alumno=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_alumno=".$id_alumno_cliente) or die(db_error()."-1");
if (db_num_rows($consulta_datos_del_alumno)!=0) {
                                        $datos_del_alumno=db_fetch_array($consulta_datos_del_alumno);
                                        $id_usuario_alumno=$datos_del_alumno["id_usuario"];
                                        $id_aula=$datos_del_alumno["aula"];
                                        // busca datos aula
                                        
                                        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_del_alumno["aula"]);

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
                                                $busca_actual=db_query("SELECT * FROM personal_historial WHERE id_aula=$id_aula AND fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."';") or die(db_error()."-2");

                                                $id_educadora=0;
                                                if (db_num_rows($busca_actual)!=0) {
                                                        $datos_actual=db_fetch_array($busca_actual);
                                                        $id_educadora=$datos_actual["id_empleado"];
                                                        }


                                                // Busca nombre educadora
                                                $busca_nombre_educadora=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_educadora") or die(db_error()."-3");

                                                if (db_num_rows($busca_nombre_educadora)!=0) {
                                                        $datos_educadora=db_fetch_array($busca_nombre_educadora);
                                                        $nombre_teacher=strtoupper($datos_educadora["Nombre"]);
                                                        }

                                                // Busca datos curso
                                                $busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$id_curso_aula) or die(db_error()."-4");

                                                if (db_num_rows($busca_curso)!=0){
                                                        $datos_curso=db_fetch_array($busca_curso);

                                                        $fecha_ini=$datos_curso["inicio_trim".$trimestre."_".$ciclo_aula];
                                                        $fecha_fin=$datos_curso["fin_trim".$trimestre."_".$ciclo_aula];
                                                        }

                                        }
                                        
                                        
                                }

$lista_alumnos=$id_alumno;
$mostrar_logo=$_GET["logo"];
if (strlen($mostrar_logo)==0) {
        $mostrar_logo=1;
}
        
$lista_alumnos_array=explode(",",$lista_alumnos);
if (count($lista_alumnos_array)==1) {
        $id_alumno=$lista_alumnos_array[0];
}
        
if (strlen($id_alumno)==0) {
        $id_alumno=$_GET["id_alumno"];
}

$base_datos_alumnos="";
$campo_periodo="periodo";
$valor_campo_periodo=$trimestre;

// Busca objetivos generales
$nombre_objetivos_generales=array();
$busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_comedor WHERE id_objetivo_g=0 ORDER BY id_objetivo") or die(db_error()."-7");
                 
while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {
        $nombre_objetivos_generales[$objetivos_generales["id_objetivo"]]=$objetivos_generales["nombre_objetivo"];
}

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(00,0,70,70);

if ($id_alumno) {
        // Uno solo
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=".$id_alumno.";") or die(db_error()."-8");
} else {             
        // Muchos   
        $filtro_alumnos="";
        foreach ($lista_alumnos_array as $valor) {
            $filtro_alumnos.="alumnos.id_alumno=".$valor." OR ";                
        }
        if (strlen($filtro_alumnos)!=0) {
                $filtro_alumnos="(".substr($filtro_alumnos,0,strlen($filtro_alumnos)-4).")";        
        }
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE ".$filtro_alumnos.";") or die(db_error()."-9");
}

$contador_alumnos=0;
$total_alumno=db_num_rows($busca_datos_alumno);

if (db_num_rows($busca_datos_alumno)!=0) {

        while($listado_alumnos=db_fetch_array($busca_datos_alumno)){

                $id_alumno=$listado_alumnos["id_alumno"];
                                               
                // Busca envaluacion guardada
                                                                
                $busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_estado_alumnos WHERE id_alumno=$id_alumno AND $campo_periodo=$valor_campo_periodo;") or die(db_error()."-10");
                               
				if (db_num_rows($busca_estado_evaluacion_guardada)==0) {
					// Si no encontramos nada mostramos la evaluación anterior
					$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_estado_alumnos WHERE id_alumno=$id_alumno;") or die(db_error()."-10B");
				}
				
				$datos_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
				
                if ($datos_evaluacion_guardada["estado"]==1) {                
        
                        if ($contador_alumnos>0) {
                                $pdf->ezNewPage();
                        }                
                        
                        $contador_alumnos++;                        
                        
                        $id_aula=$listado_alumnos["aula"];
                        $id_curso_aula=$listado_alumnos["id_curso"];
                        $nivel_actual=$listado_alumnos["nivel_actual"];


                        // Fecha de incorporación
                        $fecha_incorporacion=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));
                        if (strtotime($listado_alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                                $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]))."</span>";
            }
                        $fecha_baja=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));
                        if (strtotime($listado_alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                    $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]))."</span>";
                }
                        if ($listado_alumnos["nonato"]==1) {
                    $edad_alumno="";
                                $fecha_nac_alumno="No ha nacido";
                } else {
                                $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                        $edad_alumno=$edad_alumno-1;
                }
                                if ($edad_alumno<2) {
                        // 1. Comprueba la edad restando el año de nacimiento al año actual
                                        $fecha_l=$listado_alumnos["fecha_nacimiento"];
                                        $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                        // 1.1. Cacula edad con decimales
                                        $mes_d_l=date("m",strtotime($fecha_l));
                                        $mes_d_n=date("m");
                                        $dif_meses=$mes_d_n-$mes_d_l;

                                        $edad_d_l=($edad_a_l*12)+$dif_meses;
                                        $edad_alumno=$edad_d_l." meses";
                    } else {
                        $edad_alumno=$edad_alumno." años";
                    }
                                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                }
                
                        $lista_grupos_alumno="";
                                       

                        $fechas_inicio_fin=$nombre_mes[date("n",strtotime($listado_alumnos["fecha_matricula"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_matricula"]))." a ".$nombre_mes[date("n",strtotime($listado_alumnos["fecha_baja"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_baja"]));

                        // Busca objetivos trabajados
                        $consulta_objetivos_trabajados=db_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_trimestrales LEFT JOIN agenda_programacion_objetivos_comedor ON agenda_programacion_evaluaciones_comedor_trimestrales.id_objetivo=agenda_programacion_objetivos_comedor.id_objetivo WHERE agenda_programacion_evaluaciones_comedor_trimestrales.$campo_periodo=$valor_campo_periodo AND agenda_programacion_evaluaciones_comedor_trimestrales.id_alumno=".$id_alumno." ORDER BY agenda_programacion_objetivos_comedor.id_objetivo_g, agenda_programacion_evaluaciones_comedor_trimestrales.id_objetivo;") or die(db_error()."-11");
						
						if (db_num_rows($consulta_objetivos_trabajados)==0) {
								// Si no encontramos nada mostramos la evaluación anterior
								$consulta_objetivos_trabajados=db_query("SELECT * FROM agenda_programacion_evaluaciones_comedor_trimestrales LEFT JOIN agenda_programacion_objetivos_comedor ON agenda_programacion_evaluaciones_comedor_trimestrales.id_objetivo=agenda_programacion_objetivos_comedor.id_objetivo WHERE agenda_programacion_evaluaciones_comedor_trimestrales.id_alumno=".$id_alumno." ORDER BY agenda_programacion_objetivos_comedor.id_objetivo_g, agenda_programacion_evaluaciones_comedor_trimestrales.id_objetivo;") or die(db_error()."-11B");
						}						


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
$pdf->ezText("EVALUACION COMEDOR",18,array('justification'=>'center','aleft'=>150));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText(" ",12,array('justification'=>'center','aleft'=>150));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Aula: $nombre_aula",12,array('justification'=>'center','aleft'=>150));

$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NOMBRE:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"]." - <b>EDUCADORA:</b>   ".$nombre_teacher,10,array('justification'=>'center','aleft'=>150));
$posicion_actual=$posicion_actual-28;
                                        
                        $pagina_actual=1;
                        $objetivo_general_actual=0;

                        while($busca_objetivos_trabajados=db_fetch_array($consulta_objetivos_trabajados)) {

                                if ($busca_objetivos_trabajados["nota"]!=0) { // Solo los trabajados
                
                                        if ($busca_objetivos_trabajados["nota"]==1) {
                                                $calificacion=" S";
                                       } else {
                                                   $calificacion=" P";
                                      } 
                                                                                               
                                          $pdf->selectFont('./fonts/Helvetica');

                                          if ($objetivo_general_actual!=$busca_objetivos_trabajados["id_objetivo_g"]) {
                                                  $objetivo_general_actual=$busca_objetivos_trabajados["id_objetivo_g"];
                                                  $pdf->ezSetY($posicion_actual);
                                                  $pdf->ezText("<b>".$nombre_objetivos_generales[$objetivo_general_actual]."</b>",12,array('justification'=>'left','aleft'=>75));
                                                  $posicion_actual=$posicion_actual-14;
                                        }

                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText($busca_objetivos_trabajados["nombre_objetivo"],10,array('justification'=>'left','aleft'=>105));
                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->selectFont('./fonts/Helvetica-Bold');
                                        $pdf->ezText($calificacion,10,array('justification'=>'right','aright'=>530));
                                        $posicion_actual=$posicion_actual-14;

                                        if ($posicion_actual<80) { // Salto de página
                                        
$pdf->ezSetY(54);
$pdf->selectFont('./fonts/Helvetica-Bold');
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
$pdf->ezText("EVALUACION COMEDOR",18,array('justification'=>'center','aleft'=>150));
$posicion_actual=$posicion_inicial-32;
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezSetY($posicion_actual);
$pdf->ezText(" ",12,array('justification'=>'center','aleft'=>150));
$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->ezText("Aula: $nombre_aula",12,array('justification'=>'center','aleft'=>150));

$posicion_actual=$posicion_actual-28;
$pdf->ezSetY($posicion_actual);
$pdf->selectFont('./fonts/Helvetica');
$pdf->ezText("<b>NOMBRE:</b>   ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." ".$listado_alumnos["Nombre"]." - <b>EDUCADORA:</b>   ".$nombre_teacher,10,array('justification'=>'center','aleft'=>150));
$posicion_actual=$posicion_actual-28;
                                        } // Fin salto página

                                } // Fin if nota!=0
                        } // Fin bucle

                        $posicion_actual=$posicion_actual-16;
                        $pdf->selectFont('./fonts/Helvetica');
                        if ((strlen($datos_evaluacion_guardada["observaciones"])!=0) and ($trimestre==2)) {
                                $pdf->ezSetY($posicion_actual);
                                $pdf->ezText(app_text_utf8($datos_evaluacion_guardada["observaciones"]),10,array('justification'=>'full'));
                                $posicion_actual=$posicion_actual-85;
                        }
                        if ((strlen($datos_evaluacion_guardada["observaciones1"])!=0) and ($trimestre==1)) {
                                $pdf->ezSetY($posicion_actual);
                                $pdf->ezText(app_text_utf8($datos_evaluacion_guardada["observaciones1"]),10,array('justification'=>'full'));
                                $posicion_actual=$posicion_actual-85;
                        } 
                        if ((strlen($datos_evaluacion_guardada["observaciones2"])!=0) and ($trimestre==3)) {
                                $pdf->ezSetY($posicion_actual);
                                $pdf->ezText(app_text_utf8($datos_evaluacion_guardada["observaciones2"]),10,array('justification'=>'full'));
                                $posicion_actual=$posicion_actual-85;
                        }                         



                        $pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezSetY(54);
$pdf->selectFont('./fonts/Helvetica-Bold');
$pdf->ezText("S = SUPERADO                   P = EN PROGRESO
Página ".$pagina_actual,10,array('justification'=>'center'));


                } // Fin if evaluacion guardada estado=1
        }        // Fin bucle alumnos
}        // Fin if hay alumnos

$pdf->ezStream();
?>
