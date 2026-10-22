<?php 
// exit("NO");
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_curso=$_GET["id_curso"];

$busca_datos_alumno=db_query_params('SELECT * FROM gestionguarderias.alumnos LEFT JOIN gestionguarderias.usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE alumnos.id_curso=?;', 's', array($id_curso)) or die(db_error());

while($alumnos=db_fetch_array($busca_datos_alumno)){

if (($alumnos["nivel_maximo_superado"]==0) and ($alumnos["nivel_maximo_trabajado"]==0)) {

// if (1==1) {

if (isset($lista_autoevaluacion)) {

	unset($lista_autoevaluacion);

}

if (isset($lista_evaluacion_alu)) {

	unset($lista_evaluacion_alu);

}

$lista_autoevaluacion=array();
$lista_evaluacion_alu=array();

$id_alumno=$alumnos["id_alumno"];

$id_usuario_alumno=$alumnos["id_usuario"];

$id_aula=$alumnos["aula"];
$id_curso_aula=$alumnos["id_curso"];
 		
 		$busca_objetivos_generales=db_query("SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=0 ORDER BY id_objetivo");
 		
 		while($objetivos_generales=db_fetch_array($busca_objetivos_generales)) {


					$busca_objetivos_especificos=db_query_params('SELECT * FROM agenda_programacion_objetivos_piscina WHERE id_objetivo_g=? ORDER BY id_objetivo', 's', array($objetivos_generales["id_objetivo"]));
 		
			 		while($objetivos_especificos=db_fetch_array($busca_objetivos_especificos)) {
			 		
			 			// Busca envaluacion guardada, si no está la crea                                                                               
                                                        
                    	$busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$id_alumno." AND id_objetivo=".$objetivos_especificos["id_objetivo"].";") or die(db_error());
                                                        
                        if (db_num_rows($busca_evaluacion_guardada)!=0) {
							$datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
                            $valor_calificacion=$datos_evaluacion_guardada["nota"];
                        } else {
                        	$valor_calificacion=0;
                        }
			 		
			 		// Muestra subniveles
			 		$lista_subniveles=explode(",",$objetivos_especificos["autoevaluacion"]);
			 		
			 		$subniveles="";
			 		
			 		foreach ($lista_subniveles as $indice => $subnivel) {
			 		
			 			if ($subnivel=="1") {
			 			
			 				$subniveles.=" <span class='badge badge-".($indice+1)."'>".$nombres_subniveles[($indice+1)]."</span>";	
			 				$lista_autoevaluacion[$indice][]=$objetivos_especificos["id_objetivo"];	
			 				
				 			$lista_evaluacion_alu[$indice][]=$valor_calificacion;		 				
			 			
			 			}
			 		
			 		}
			 		
			 		}

 		}
 		
 		// Comprueba niveles máximos
 		
 		$nivel_maximo_superado=-1;
 		$nivel_maximo_trabajado=-1;
 		
 		for ($niveles=0;$niveles<=8;$niveles++) {
 		
 			$total_objetivos_para_superado=count($lista_autoevaluacion[$niveles]);
 			$total_superados=0;
 			$total_trabajado=0; 			 		
 			
 			if (is_array($lista_evaluacion_alu[$niveles])) {
 			
	 			foreach ($lista_evaluacion_alu[$niveles] as $valor_evauacion) { 			 				 
 			 			
 					if ($valor_evauacion=="1") {
 						$total_superados++;
 					}
	 				if ($valor_evauacion=="2") {
 						$total_trabajado++;
 					}
 			
 				}
 				
 			}
 			
 			if (($total_superados==$total_objetivos_para_superado) and ($total_superados>0)) {
 			
 				$nivel_maximo_superado=$niveles;
 				$nivel_maximo_trabajado=$niveles;	
 			
 			} else {
 			
 				if (($total_trabajado>0) or ($total_superados>0)) {
 				
 					$nivel_maximo_trabajado=$niveles;
 				
 				}
 			
 			}
 			
 		
 		}
 		
 		if (($nivel_maximo_superado==-1) and ($nivel_maximo_trabajado>2)) {
 		
 			if ($nivel_maximo_trabajado<6) {
 				$nivel_maximo_superado=2;
 				} else {
 				$nivel_maximo_superado=5;
 				}
 		
 		} 		 		
 		
 		// Guarda dato
 		$guarda_niveles=db_query_params('UPDATE alumnos SET nivel_maximo_superado=?, nivel_maximo_trabajado=? WHERE id_alumno=?', 'sss', array((string)($nivel_maximo_superado + 1), (string)($nivel_maximo_trabajado + 1), $id_alumno));
 		
 		
 			}
 		}
 		?>        
OK