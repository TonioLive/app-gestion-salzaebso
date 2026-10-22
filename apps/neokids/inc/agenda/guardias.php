<div class="alert alert-info alert-dismissible" role="alert">
<h4>Monitores/as de guardia esta semana</h4>
<?php 
// Busca monitor de guardia
	$fecha_control=date("Y-m-d",$fecha_time_stamp);
    	
    // Inicio y fin de semana
	$diasemana=date("w",$fecha_time_stamp);
	$dia_inicio_semana=$fecha_time_stamp-(($diasemana-1)*86400);	
    							
	$busca_guardia=db_query_params('SELECT * FROM personal_guardias WHERE dia>=? AND dia<=?', 'ss', array((string)(date("Y-m-d", $dia_inicio_semana)), (string)(date("Y-m-d", $dia_inicio_semana + 86400 * 6))));
    
	while($datos_guardia=db_fetch_array($busca_guardia)) {
    								
		$id_guardia=$datos_guardia["id_guardia"];
		$monitor_guardia=$datos_guardia["id_usuario"];
    							   							
    							
		$busca_docentes=db_query("SELECT * FROM usuarios WHERE id_usuario=$monitor_guardia;");
                                
		if (db_num_rows($busca_docentes)!=0) {
                                
			$datos_docentes=db_fetch_array($busca_docentes);
          
          
        	echo("<h5>".$dia_sem_largo[date("N",strtotime($datos_guardia["dia"]))]." ".date("d/m/Y",strtotime($datos_guardia["dia"])).": ".mb_convert_case(security_escape($datos_docentes["Nombre"])." ".security_escape($datos_docentes["Apellido1"])." ".security_escape($datos_docentes["Apellido2"]), MB_CASE_TITLE)."</h5>");
                            
			}
		}
				
?>
<button class="btn btn-primary btn-xs pull-right" onclick="muestra_guardias()">Ver calendario de guardias completo</button><br />
</div> 