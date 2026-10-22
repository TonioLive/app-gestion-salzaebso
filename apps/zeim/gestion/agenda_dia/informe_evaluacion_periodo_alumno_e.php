<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");


$id_alumno=$_POST["id_alumno"];
$trimestre=$_POST["trimestre"];
$id_aula=$_POST["id_aula"];

$trimestre_numeral=$trimestre."er";

if ($trimestre==2) {
        $trimestre_numeral=$trimestre."º";
        }

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

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
        
        // Busca datos curso
                $busca_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_aula)) or die(db_error());
                
                if (db_num_rows($busca_curso)!=0){
                $datos_curso=db_fetch_array($busca_curso);

                $fecha_inicio_curso=$datos_curso["fecha_inicio"];

                $fecha_ini=$datos_curso["inicio_trim".$trimestre."_".$ciclo_aula];
                $fecha_fin=$datos_curso["fin_trim".$trimestre."_".$ciclo_aula];
                
                }
                
        $evaluacion_por_edades=$datos_aula["evaluacion_por_edades"];          
        $edad_ini_aula=$datos_aula["edad_minima_prog"];  
        $edad_fin_aula=$datos_aula["edad_maxima_prog"];
        
        $mes_inicio_trimestre=date("n",strtotime($fecha_ini));  
        $mes_fin_trimestre=date("n",strtotime($fecha_fin));
                
        }
        
// Busca envaluacion guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre;");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
        $datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    } else {
    $guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_estado (id_curso,id_aula,trimestre) VALUES (?,?,?)', 'sss', array((string)($id_curso_aula), (string)($id_aula), (string)($trimestre)));
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    }        
     
?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<script language="javascript">
<!--
self.focus();

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda){ celda.style.backgroundColor="#ffffff" }
    
var pagina_destino="guarda_dato_evaluacion.php";

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);
    }
}
    
//-->
</script>


</head>

<body>
                                <table class="table table-striped">
                                        <tr>
                                        <td><h3>Aula: <?=$nombre_aula?> - Evaluación del <?=$trimestre_numeral?> trimestre. Del  <?=date("d/m/Y",strtotime($fecha_ini))?> al <?=date("d/m/Y",strtotime($fecha_fin))?></h3></td>
                                        </tr>
<tr>
                                        <td><font size=2><b>Visibilidad del informe por parte de los padres: <select name="visibilidad" id="visibilidad" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado'},{desc:'campo',dato:'estado'},{desc:'valor',dato:document.getElementById('visibilidad').value});">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada==0) { print " selected"; } ?>>NO VISIBLE</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada==1) { print " selected"; } ?>>VISIBLE</option>
                                        </select></b></font></td>
                                        </tr>
                                </table><br />
                                <?php 
                                // Busca objetivos trabajados
                                $contador_areas=0;
                                $contador_total=0;
                                $areas=array("ÁREA I. CONOCIMIENTO DE SÍ MISMO Y AUTONOMIA PERSONAL","ÁREA II. CONOCIMIENTO DEL ENTORNO","ÁREA III. LENGUAJES: COMUNICACIÓN Y REPRESENTACION");
                                for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                $array_objetivos_area[$c_areas]=array();
                                $contador_array_obj=0;
                                
                                if ($edad_ini_aula==0) {
                                                // Bebes, solo lo trabajado                                               
                                                $busca_objetivos=db_query_params('SELECT * FROM agenda_programacion LEFT JOIN agenda_programacion_objetivos ON agenda_programacion.objetivo=agenda_programacion_objetivos.id_objetivo WHERE agenda_programacion.id_aula=? AND agenda_programacion.fecha>=? AND agenda_programacion.fecha<=? ORDER BY agenda_programacion_objetivos.mes, agenda_programacion.objetivo;', 'sss', array($id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());
                                        } else {
                                                // Resto, lo programado en el trimestre
                                                // $busca_objetivos=mysql_query("SELECT * FROM agenda_programacion_objetivos WHERE edad>='".$edad_ini_aula."' AND edad<='".$edad_fin_aula."' AND mes>=".$mes_inicio_trimestre." AND mes<=".$mes_fin_trimestre." ORDER BY id_objetivo;") or die(mysql_error());
                                                                                                                                                                                                                                                                												
                                                $consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE edad>='".$edad_ini_aula."' AND edad<='".$edad_fin_aula."' AND ((mes>=".$mes_inicio_trimestre." AND mes<=".$mes_fin_trimestre.") OR (mes=0)) AND editorial=0 ORDER BY id_objetivo;";                                                                                                                                                                                                                												
                                                // Comprueba si la programación debe ser EDEBE
												$nueva_programacion_activa=true;
												if (($fecha_inicio_curso<=strtotime("2019-08-31")) and ($fecha_inicio_curso>=strtotime("2017-09-01")) and (($edad_ini_aula==1) or ($edad_ini_aula==2)) and ($nueva_programacion_activa==true)) {
													
													$editorialProgramacion=1;
													if ($fecha_inicio_curso>=strtotime("2018-09-01")) {
														$editorialProgramacion=2;
														
														// 1 AÑO
														
														
														// Trimestre 1
														if ($trimestre==1) {
															$filtro_meses_programacion="mes=1 OR mes=2 OR mes=3";	
														}
														
														// Trimestre 2
														if ($trimestre==2) {
															$filtro_meses_programacion="mes=4 OR mes=5 OR mes=6";	
														}
														
														// Trimestre 3													
														if ($trimestre==3) {
															$filtro_meses_programacion="mes=7 OR mes=8 OR mes=9";
														}
																				
														if ($edad_ini_aula==2) {
															
															// 2 AÑOS
															
															if ($trimestre==1) {
																$filtro_meses_programacion="mes=1 OR mes=2";
															}
															if ($trimestre==2) {
																$filtro_meses_programacion="mes=3 OR mes=4";
															}
															if ($trimestre==3) {
																$filtro_meses_programacion="mes=5 OR mes=6";
															}								
															
														}
														
													} else {
														
														// 1 AÑO
														
														
														// Trimestre 1
														if ($trimestre==1) {
															$filtro_meses_programacion="mes=1";	
														}
														
														// Trimestre 2
														if ($trimestre==2) {
															$filtro_meses_programacion="mes=2 OR mes=3";	
														}
														
														// Trimestre 3													
														if ($trimestre==3) {
															$filtro_meses_programacion="mes=4 OR mes=5";
														}
																				
														if ($edad_ini_aula==2) {
															
															// 2 AÑOS
															
															if ($trimestre==1) {
																$filtro_meses_programacion="mes=1 OR mes=2";
															}
															if ($trimestre==2) {
																$filtro_meses_programacion="mes=3 OR mes=4";
															}
															if ($trimestre==3) {
																$filtro_meses_programacion="mes=5 OR mes=6";
															}								
															
														}	
														
													}
											 														
													if ($editorialProgramacion==2) {
														
														if ($trimestre==3) {
															// 3er trimestre, todos los objetivos delos trimestres 2º y 3º
															$consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.edad>=$edad_ini_aula AND agenda_programacion_objetivos.edad<=$edad_fin_aula AND agenda_programacion_objetivos.editorial=$editorialProgramacion AND agenda_evaluacion_items.trimestre>1 ORDER BY agenda_programacion_objetivos.id_objetivo";
														} else {
															$consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.edad>=$edad_ini_aula AND agenda_programacion_objetivos.edad<=$edad_fin_aula AND agenda_programacion_objetivos.editorial=$editorialProgramacion AND agenda_evaluacion_items.trimestre=".$trimestre." ORDER BY agenda_programacion_objetivos.id_objetivo";	
														}
														
														
													} else {
														$consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE edad>=$edad_ini_aula AND edad<=$edad_fin_aula AND (".$filtro_meses_programacion." OR mes=0) AND editorial=$editorialProgramacion ORDER BY id_objetivo";
													}											
												}
																								
												$busca_objetivos=db_query($consulta_busca_objetivos) or die(db_error());                                                                                                                                                                                                                												
                                                                                                                                                                                                                                                                												
                                    }

									while($cuenta_objetivos=db_fetch_array($busca_objetivos)){
                                                                
										// Objetivo principal
										if ($edad_ini_aula==0) {
											// Bebes
											$id_objetivo1=$cuenta_objetivos["objetivo"];
										} else {
											// Resto
											$id_objetivo1=$cuenta_objetivos["id_objetivo"];
										}

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
                                 								
                                if ($contador_total==0) {
                                ?>
                                <b>No se han trabajado objetivos durante el periodo seleccionado.</b></font>
                                <?php 
                                } else {
                                
                                $busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($id_aula, (string)(date("Y-m-t", strtotime($fecha_fin))), (string)(date("Y-m-01", strtotime($fecha_ini))))) or die(db_error());

                                if (db_num_rows($busca_alumnos)!=0) {
                                
                                while($listado_alumnos=db_fetch_array($busca_alumnos)){

// Busca envaluacion alumno guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_curso=$id_curso_aula AND id_alumno=".$listado_alumnos["id_alumno"]." AND trimestre=$trimestre;");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
        $datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    $observaciones_evaluacion_guardada=$datos_estado_evaluacion_guardada["observaciones"];
    } else {
    $guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_estado_alumnos (id_curso,id_alumno,trimestre) VALUES (?,?,?)', 'sss', array((string)($id_curso_aula), (string)($listado_alumnos["id_alumno"]), (string)($trimestre)));
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    }
	
	$contador_items=1;

	switch ($trimestre) {
    case 1:
        $grupo_actual=$listado_alumnos["agenda_grupo"];
        break;
    case 2:
        $grupo_actual=$listado_alumnos["agenda_2_grupo"];
        break;
    case 3:
        $grupo_actual=$listado_alumnos["agenda_3_grupo"];
        break;
	}

	if ($grupo_actual==0) {
			$nombre_grupo=" Grupo: Sin asignar a un grupo";        
		} else {
         	$nombre_grupo=" Grupo: ".$grupo_actual;
		}
                                                                
                                ?>
                                <table class="table table-striped table-hover">
                                        <tr class="info">
                                        <td colspan=3>
											<h4><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?>.<?=$nombre_grupo?></h4>
											<button 
											type="button" 
											onclick="marcarTodoSuperado(<?=$listado_alumnos['id_alumno']?>, '<?=addslashes($listado_alumnos['Nombre'])?> <?=addslashes($listado_alumnos['Apellido1'])?> <?=addslashes($listado_alumnos['Apellido2'])?>')">
											Marcar todos los objetivos de <?=$listado_alumnos["Nombre"]?> como superados
											</button>
										</td>
                                        </tr>
                                        <tr>
                                        <td colspan=3><h5>Observaciones</h5>
                 <textarea class="form-control" rows="3" name="observaciones<?=$id_estado_evaluacion_guardada?>" id="observaciones<?=$id_estado_evaluacion_guardada?>" onblur="sendAndLoad({desc:'texto_largo',dato:1},{desc:'fila',dato:<?=$id_estado_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_estado'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_estado_alumnos'},{desc:'campo',dato:'observaciones'},{desc:'valor',dato:document.getElementById('observaciones<?=$id_estado_evaluacion_guardada?>').value});"><?=utf8_decode($observaciones_evaluacion_guardada)?></textarea></td>
                                        </tr>
                                        <?php 
                                        
// Limpia tabla temp
$limpia_tabla=db_query_params('DELETE FROM agenda_programacion_objetivos_temp WHERE id_alumno=?', 's', array($listado_alumnos["id_alumno"]));
                                        
for ($c_areas=0;$c_areas<count($areas);$c_areas++) {        

	$num_objetivos_trabajados=count($array_objetivos_area[$c_areas]);
                                        
	if ($edad_ini_aula==0) {
		?>
		<tr>
			<td colspan="3"><h5 class="text-primary"><?=$areas[$c_areas]?></h5></td>
		</tr>
		<?php 
	}
                                       
	$mes_actual=-1;
                                       
	for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++) {

		$id_objetivo=$array_objetivos_area[$c_areas][($contador_objetivos-1)];
	
		$busca_datos_objetivo=db_query_params('SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo));
		$datos_objetivos=db_fetch_array($busca_datos_objetivo);                                                                                                                               
                                           
		// Comprueba tipo de evaluación y procede según
		$mostrar_objetivo=true;
		if ($evaluacion_por_edades==1) {
              
        	$edad_alumno_anyos=date("Y",strtotime($fecha_inicio_curso)) - date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));                            
               
            if ($edad_alumno_anyos==$datos_objetivos["edad"]) {
            	$mostrar_objetivo=true;
            } else {
            	$mostrar_objetivo=false;
            }
                                                                                              
		}

		if ($mostrar_objetivo==true) {
                                           
			$porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];
                                        
			$num_actividades_programadas=0;
			$num_actividades_trabajadas=0;
			$num_actividades_superadas=0;

			// Busca actividades trabajadas por este niño  
		
			if ($edad_ini_aula==0) {
		
				// Bebes, solo las de su grupo
			
				$busca_actividades=db_query("SELECT * FROM agenda_programacion WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND (id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."') AND edad=".($grupo_actual-1)." ORDER BY id_actividad ") or die(db_error());
		
			} else {
		
				// Resto de edades, Todas
			
				$busca_actividades=db_query_params('SELECT * FROM agenda_programacion WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());
			
				// Si no está relacionado con un mes, busca si programado desde el inicio del curso
			
				$busca_actividades_curso=db_query_params('SELECT * FROM agenda_programacion WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_inicio_curso), (string)($fecha_fin))) or die(db_error());  
			              
			}                                                                                                                                                                          

			$objetivo_no_programado = "";
			$objetivo_no_programado_curso = false;
		
			if (db_num_rows($busca_actividades)==0) {
				$objetivo_no_programado = " <span class=\"text-danger\">No programado</span>";
			}
			if ($edad_ini_aula!=0) {
				if (db_num_rows($busca_actividades_curso)==0) {
  					$objetivo_no_programado_curso = true;
				}
			}

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
				$calificacion="<font color='#00cc00'>S</font>";
				$superado=1;
			} else {
				$calificacion="<font color='#cc0000'>P</font>";
 				$superado=0;
			}
		
			// Inserta en tabla temp
			// $inserta_fila=mysql_query("INSERT INTO agenda_programacion_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area, texto) VALUES ('$id_objetivo','".$datos_objetivos["id_evaluacion"]."','".$listado_alumnos["id_alumno"]."','$superado','$c_areas', '$objetivo_no_programado');");
                                                
			// Inserta en tabla temp
			if ($datos_objetivos["id_evaluacion"]!=0) {
                                                            
				// Si son bebes, solo lo programado
				$insertar=true;
				if (($edad_ini_aula==0) and ($objetivo_no_programado!="") and ($num_actividades_trabajadas==0)) {
					$insertar=false;
				}
                                                             
				// Si no son bebes, es un objetivo independiente de edad, solo si programado 
				if (($datos_objetivos["mes"]==0) and ($objetivo_no_programado_curso==true)) {
					$insertar=false;
				}
                                                             
				if ($insertar==true) {
					$inserta_fila=db_query_params('INSERT INTO agenda_programacion_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area, texto) VALUES (?,?,?,?,?, ?);', 'ssssss', array((string)($id_objetivo), (string)($datos_objetivos["id_evaluacion"]), (string)($listado_alumnos["id_alumno"]), (string)($superado), (string)($c_areas), (string)($objetivo_no_programado))) or die(db_error()); 
				}                                                                                                                     
			}                                            
		} // Fin Mostrar Objetivo
	} // Fin Bucle objetivos
                                                
	// Busca y muestra
	$busca_evaluaciones=db_query("SELECT id_evaluacion,count(id_objetivo) AS num_objetivos,sum(superado) AS objetivos_superados, texto FROM agenda_programacion_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND id_area=$c_areas GROUP BY id_evaluacion ORDER BY id_evaluacion;");
                                             
	while($datos_evaluaciones=db_fetch_array($busca_evaluaciones)) {
		
		if (($datos_evaluaciones["objetivos_superados"]/$datos_evaluaciones["num_objetivos"])>0.5) {
			$calificacion=" <font color='#00cc00'>S</font>";
			$valor_calificacion=1;
		} else {
			$calificacion=" <font color='#cc0000'>P</font>";
			$valor_calificacion=0;
		}
        $valor_calificacion_evaluada = $valor_calificacion;

		// Busca datos envaluacion
		$busca_evaluacion=db_query_params('SELECT * FROM agenda_evaluacion_items WHERE id_evaluacion=?', 's', array($datos_evaluaciones["id_evaluacion"])) or die(db_error());
		$datos_evaluacion=db_fetch_array($busca_evaluacion);
                                                        
		// Busca envaluacion guardada, si no está la crea
                                                        
		$busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$listado_alumnos["id_alumno"]." AND id_evaluacion=".$datos_evaluaciones["id_evaluacion"].";");                           

		if (db_num_rows($busca_evaluacion_guardada)!=0) {
			$datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
			$id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
			$valor_calificacion=$datos_evaluacion_guardada["nota"];

			/*
			// Marca toda un aula como superada
			if (($id_aula==1272) and ($trimestre=2)) {
				$actualizaEvaluacion = mysql_query("UPDATE agenda_programacion_evaluaciones_trimestrales SET nota=1 WHERE id_eval=".$id_evaluacion_guardada.";");
			}
			*/

			// Comprueba si se ha superado desde la última vez que se revisó
			if (($valor_calificacion_evaluada==1) and ($valor_calificacion==0)) {
				$valor_calificacion=1;	
				// Actualiza BBDD
				$actualizaEvaluacion = db_query_params('UPDATE agenda_programacion_evaluaciones_trimestrales SET nota=1 WHERE id_eval=?;', 's', array($id_evaluacion_guardada));
			}

		} else {
			$guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_trimestrales (id_curso,id_aula,trimestre,id_alumno,id_evaluacion,nota) VALUES (?,?,?,?,?,?)', 'ssssss', array((string)($id_curso_aula), (string)($id_aula), (string)($trimestre), (string)($listado_alumnos["id_alumno"]), (string)($datos_evaluaciones["id_evaluacion"]), (string)($valor_calificacion)));
			$id_evaluacion_guardada=db_insert_id();
		}

	?>
	<tr>
		<td><font size=2 face="<?= $fuente_general; ?>"><?=$contador_items.". ".$datos_evaluacion["descripcion"].$datos_evaluaciones['texto'];?></font></td>
		<td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$calificacion?></font></td>
  		<td align="center">
  			<select name="evaluacion<?=$id_evaluacion_guardada?>" class="objetivo-alumno alumno-<?=$listado_alumnos["id_alumno"]?>" id="evaluacion<?=$id_evaluacion_guardada?>" style="font-size: 10px" onchange="sendAndLoad({desc:'texto_largo',dato:0},{desc:'fila',dato:<?=$id_evaluacion_guardada?>},{desc:'nombre_id',dato:'id_eval'},{desc:'tabla',dato:'agenda_programacion_evaluaciones_trimestrales'},{desc:'campo',dato:'nota'},{desc:'valor',dato:document.getElementById('evaluacion<?=$id_evaluacion_guardada?>').value});">
				<option style="color: #00cc00" value="1"<?php  if ($valor_calificacion==1) { print " selected"; } ?>>S</option>
				<option style="color: #cc0000" value="0"<?php  if ($valor_calificacion==0) { print " selected"; } ?>>P</option>
				<option style="color: #0000cc" value="2"<?php  if ($valor_calificacion==2) { print " selected"; } ?>>NT</option>
			</select>
		</td>
	</tr>
	<?php 
		$contador_items++;
	}
} // Fin Bucle areas
?>
</table><br class="saltopagina" />
<?php 
                                        }
                                        }
                                }
                                ?>
                                </font>
<script>
function marcarTodoSuperado(idAlumno, nombreAlumno) {

    const mensaje = 
        "¿Marcar los objetivos del alumno " + nombreAlumno + " como SUPERADOS?\n\n" +
        "ATENCIÓN: Esta acción NO se puede deshacer.\n" +
        "Se borrarán los cambios realizados anteriormente.";

    if (!confirm(mensaje)) {
        return;
    }

    const selects = document.querySelectorAll('.alumno-' + idAlumno);

    selects.forEach(function(select) {
        select.value = "1";     
        select.dispatchEvent(new Event('change'));
    });
}
</script>
</body>
</html>
<?php 
db_close();
?>
