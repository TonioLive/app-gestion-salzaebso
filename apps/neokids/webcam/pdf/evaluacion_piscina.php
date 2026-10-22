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


$lista_alumnos="L".$id_alumno;
$mostrar_logo=$_GET["logo"];
if (strlen($mostrar_logo)==0) {
        $mostrar_logo=1;
}

$origen_alumnos=0;
if (substr($lista_alumnos,0,1)=="L") {
        $origen_alumnos=1;
        $lista_alumnos=str_replace("L","",$lista_alumnos);                        
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
$valor_campo_periodo=$periodo;
$tabla_grupos_alumnos="aulas_alumnos_grupos";
if ($origen_alumnos==1) {
        $base_datos_alumnos="gestionguarderias.";
        $campo_periodo="trimestre";
        $valor_campo_periodo=$trimestre;
        $tabla_grupos_alumnos="aulas_alumnos_lapices_grupos";
}

// Busca objetivos generales
$nombre_objetivos_generales=array();
$busca_objetivos_generales=db_query("SELECT * FROM gestionpiscina.agenda_programacion_objetivos_piscina WHERE id_objetivo_g=0 ORDER BY id_objetivo");
                 
while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {
        $nombre_objetivos_generales[$objetivos_generales["id_objetivo"]]=$objetivos_generales["nombre_objetivo"];
}

error_reporting(7);

include 'class.ezpdf.php';

$pdf = new Cezpdf('a4','portrait');

$pdf -> ezSetMargins(00,0,70,70);

if ($id_alumno) {
        // Uno solo
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_alumno=".$id_alumno.";") or die(db_error()."-1");
} else {             
        // Muchos   
        $filtro_alumnos="";
        foreach ($lista_alumnos_array as $valor) {
            $filtro_alumnos.="alumnos.id_alumno=".$valor." OR ";                
        }
        if (strlen($filtro_alumnos)!=0) {
                $filtro_alumnos="(".substr($filtro_alumnos,0,strlen($filtro_alumnos)-4).")";        
        }
        $busca_datos_alumno=db_query("SELECT * FROM ".$base_datos_alumnos."alumnos LEFT JOIN ".$base_datos_alumnos."usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE ".$filtro_alumnos.";") or die(db_error()."-2");                           
}

$contador_alumnos=0;
$total_alumno=db_num_rows($busca_datos_alumno);

if (db_num_rows($busca_datos_alumno)!=0) {

        while($listado_alumnos=db_fetch_array($busca_datos_alumno)){

                $nombre_alumno=$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"];                

                if ($origen_alumnos==0) {
                        $anotacion_nivel=" en el nivel ".$nivel_actual;
                }                
                
                $id_alumno=$listado_alumnos["id_alumno"];
                                
                // Busca envaluacion guardada
                                                                
                $busca_estado_evaluacion_guardada=db_query_params('SELECT * FROM gestionpiscina.agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=?;', 's', array($id_alumno)) or die(db_error());

                $datos_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
                
                if (($datos_evaluacion_guardada["estado_t1"]+$datos_evaluacion_guardada["estado_t2"]+$datos_evaluacion_guardada["estado_t3"])>0) {
        
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
        
                        $busca_alumnos_grupo=db_query("SELECT * FROM ".$base_datos_alumnos."usuarios,gestionpiscina.$tabla_grupos_alumnos,gestionpiscina.aulas_grupos,".$base_datos_alumnos."alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND $tabla_grupos_alumnos.id_alumno=alumnos.id_alumno AND $tabla_grupos_alumnos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=".$id_alumno) or die(db_error());
                        $total_grupos=db_num_rows($busca_alumnos_grupo);
                        $contador_grupos=0;
                        while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
                                $lista_grupos_alumno.=" los ".$dia_sem_largo_plural[$grupos_donde["dia"]];
                                $contador_grupos++;
                                if ($contador_grupos<$total_grupos) {
                                        if ($contador_grupos<($total_grupos-1)) {
                                                $lista_grupos_alumno.=", ";                                        
                                        } else {
                                                $lista_grupos_alumno.=" y ";
                                        }
                                }
                        }
                                       

                        $fechas_inicio_fin=$nombre_mes[date("n",strtotime($listado_alumnos["fecha_matricula"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_matricula"]))." a ".$nombre_mes[date("n",strtotime($listado_alumnos["fecha_baja"]))]." de ".date("Y",strtotime($listado_alumnos["fecha_baja"]));

                        // Busca objetivos trabajados
                        $consulta_objetivos_trabajados=db_query_params('SELECT * FROM gestionpiscina.agenda_programacion_evaluaciones_trimestrales LEFT JOIN gestionpiscina.agenda_programacion_objetivos_piscina ON gestionpiscina.agenda_programacion_evaluaciones_trimestrales.id_objetivo=gestionpiscina.agenda_programacion_objetivos_piscina.id_objetivo WHERE agenda_programacion_evaluaciones_trimestrales.id_alumno=? ORDER BY agenda_programacion_objetivos_piscina.id_objetivo_g, agenda_programacion_evaluaciones_trimestrales.id_objetivo;', 's', array($id_alumno)) or die(db_error());


						// Aquí el logo
                        if ($mostrar_logo==1) {
                                $x_logo=15;
                                $y_logo=705;
                                $pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);                                
                        }

                        // Color de las lineas Negro
                        $pdf->setStrokeColor('0.0','0.00','0.00','');
                        $pdf->setLineStyle(0.25,'','','','');

                        $posicion_inicial=730;

                        $pdf->selectFont('./fonts/Helvetica');

                        $pdf->ezSetY($posicion_inicial);
                        $pdf->selectFont('./fonts/Helvetica-Bold');
                        $pdf->ezText("VALORACIÓN CURSO NATACIÓN EDUCATIVA",16,array('justification'=>'center','aleft'=>50));
                        $posicion_actual=$posicion_inicial-32;
                        $pdf->selectFont('./fonts/Helvetica');
                        $pdf->ezSetY($posicion_actual);
                        $pdf->ezText("<b>".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]."</b> ha asistido al curso de natación educativa en La piscina de Neokids, ".$lista_grupos_alumno.". A lo largo de este período ha demostrado una gran capacidad de trabajo. A fecha de hoy (".date("d/m/Y").") su evolución ha sido positiva,  acorde a su edad y a su nivel, hasta superar los objetivos establecidos y que le destacamos a continuación con la letra S. Además,  durante este tiempo ha avanzado en otros muchos y que le señalamos con la letra P.",10,array('justification'=>'full'));

                        $posicion_actual=$posicion_actual-68;
                                        
                        $pagina_actual=1;
                        $objetivo_general_actual=0;

						$contador_mostrados=0;

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
			
												$nun_lineas_objetivo=ceil(strlen($nombre_objetivos_generales[$objetivo_general_actual])/75);
                                                  
                                                  $posicion_actual=$posicion_actual-14*$nun_lineas_objetivo;
                                        }

                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->ezText($busca_objetivos_trabajados["nombre_objetivo"],10,array('justification'=>'left','aleft'=>105));
                                        $pdf->ezSetY($posicion_actual);
                                        $pdf->selectFont('./fonts/Helvetica-Bold');
                                        $pdf->ezText($calificacion,10,array('justification'=>'right','aright'=>530));
                                        $posicion_actual=$posicion_actual-14;
										$contador_mostrados++;
										
                                        if ($posicion_actual<80) { // Salto de página
	                                        
	                                        	$contador_mostrados=0;
	                                        
                                                $posicion_actual=$posicion_actual-16;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $pdf->ezSetY(40);
                                                $pdf->ezText($nombre_alumno.", Página ".$pagina_actual,10,array('justification'=>'center'));

                                                $pagina_actual++;
                                                $pdf->ezNewPage();

                                                // encabezado
                                                if ($mostrar_logo==1) {
                                                        $x_logo=15;
                                                        $y_logo=705;
                                                        $pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);
                                                        }

                                                // Color de las lineas Negro
                                                $pdf->setStrokeColor('0.0','0.00','0.00','');
                                                $pdf->setLineStyle(0.25,'','','','');

                                                $posicion_inicial=730;
                                                $posicion_actual=$posicion_inicial;

                                                $pdf->selectFont('./fonts/Helvetica');

                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $pdf->ezText("VALORACIÓN CURSO NATACIÓN EDUCATIVA",16,array('justification'=>'center','aleft'=>50));
                                                $posicion_actual=$posicion_actual-32;
                                        } // Fin salto página

                                } // Fin if nota!=0
                        } // Fin bucle
                        
                        // if ($posicion_actual<=151) {
                        if ($contador_mostrados>0) {
                                                $posicion_actual=$posicion_actual-16;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $pdf->ezSetY(40);
                                                $pdf->ezText($nombre_alumno.", Página ".$pagina_actual,10,array('justification'=>'center'));
												
                                $pagina_actual++;
                                $pdf->ezNewPage();
                                // encabezado
                                                if ($mostrar_logo==1) {
                                                        $x_logo=15;
                                                        $y_logo=705;
                                                        $pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);
                                                        }

                                                // Color de las lineas Negro
                                                $pdf->setStrokeColor('0.0','0.00','0.00','');
                                                $pdf->setLineStyle(0.25,'','','','');

                                                $posicion_inicial=730;
                                                $posicion_actual=$posicion_inicial;

                                                $pdf->selectFont('./fonts/Helvetica');

                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $pdf->ezText("VALORACIÓN CURSO NATACIÓN EDUCATIVA",16,array('justification'=>'center','aleft'=>50));
                                                $posicion_actual=$posicion_actual-32;
                        }

                        $pdf->ezSetY($posicion_actual);
						$pdf->selectFont('./fonts/Helvetica-Bold');
                        $pdf->ezText("COMENTARIOS DE LA MONITORA", 16,array('justification'=>'center','aleft'=>50));                       
                        $posicion_actual=$posicion_actual-32;
                        $pdf->ezSetY($posicion_actual);
                        
                        $pdf->selectFont('./fonts/Helvetica');
                        if (strlen($datos_evaluacion_guardada["observaciones"])!=0) {
                                $pdf->ezSetY($posicion_actual);
                                $lineas_salto=18*ceil(strlen($datos_evaluacion_guardada["observaciones"])/80);
                                
                                $pdf->ezText(utf8_decode($datos_evaluacion_guardada["observaciones"]),10,array('justification'=>'full'));
                                                                                                
                                $posicion_actual=$posicion_actual-$lineas_salto;
                        }

						if ($posicion_actual<220) {												
                                                $posicion_actual=$posicion_actual-16;
                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $pdf->ezSetY(40);
                                                $pdf->ezText($nombre_alumno.", Página ".$pagina_actual,10,array('justification'=>'center'));

                                $pagina_actual++;
                                $pdf->ezNewPage();
                                // encabezado
                                                if ($mostrar_logo==1) {
                                                        $x_logo=15;
                                                        $y_logo=705;
                                                        $pdf->addJpegFromFile('logo_redondo.jpg',$x_logo,$y_logo,125,125);
                                                        }

                                                // Color de las lineas Negro
                                                $pdf->setStrokeColor('0.0','0.00','0.00','');
                                                $pdf->setLineStyle(0.25,'','','','');

                                                $posicion_inicial=730;
                                                $posicion_actual=$posicion_inicial;

                                                $pdf->selectFont('./fonts/Helvetica');

                                                $pdf->ezSetY($posicion_actual);
                                                $pdf->selectFont('./fonts/Helvetica-Bold');
                                                $pdf->ezText("VALORACIÓN CURSO NATACIÓN EDUCATIVA",16,array('justification'=>'center','aleft'=>50));
                                                $posicion_actual=$posicion_actual-60;
                                                $pdf->selectFont('./fonts/Helvetica');
						}

                        $x_logo=15;
                        $y_logo=$posicion_actual-120;
                        $pdf->addJpegFromFile('FIRMA-DANI.jpg',$x_logo,$y_logo,258.67,40);
                        $x_logo=365;
                        $y_logo=$posicion_actual-140;
                        $pdf->addJpegFromFile('FIRMA-JAVI.jpg',$x_logo,$y_logo,116.63,100);

                        $pdf->ezSetY($posicion_actual);
                        $pdf->ezText(" Desde Neokids le animamos a seguir practicando la natación educativa y le agradecemos la confianza depositada en nosotros todo este tiempo.


Valencia, ".date("j")." de ".$nombre_mes[date("n")]." de ".date("Y"),10,array('justification'=>'full'));
                        $posicion_actual=$posicion_actual-120;
                        $pdf->ezSetY($posicion_actual);
                        $pdf->ezText($nombreDirectora."
Directora deportiva",10,array('justification'=>'full'));
                        $pdf->ezSetY($posicion_actual);
                        $pdf->ezText("Juan I. Sotos
Gerente  
La Piscina de Neokids",10,array('justification'=>'full','aleft'=>350));
                        $pdf->selectFont('./fonts/Helvetica-Bold');
                        $pdf->ezSetY(40);
                        $pdf->ezText($nombre_alumno.", Página ".$pagina_actual,10,array('justification'=>'center'));

                } // Fin if evaluacion guardada estado=1
        }        // Fin bucle alumnos
}        // Fin if hay alumnos

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
