<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
		
	// Lee Configuracion de Alarmas
	
	$busca_configuracion=db_query("SELECT * FROM registro_temperaturas.configuracion WHERE desactivada=0 ORDER BY nombre_temp");
	
	while ($configuracion=db_fetch_array($busca_configuracion)) {	
	
		// Recibimos los datos		
		 
		$dias_semana=$_POST["dias_semana".$configuracion["id"]];		        
		$limite_alarma_piscina=$_POST["limite_alarma_piscina".$configuracion["id"]];	        
		$limite_sup_alarma_piscina=$_POST["limite_sup_alarma_piscina".$configuracion["id"]]; 		        		        
		$email_notificaciones=$_POST["email_notificaciones".$configuracion["id"]];
		$telefono_notificaciones=$_POST["telefono_notificaciones".$configuracion["id"]];	       		
		$hora_inicio=$_POST["hora_i".$configuracion["id"]].":".$_POST["min_i".$configuracion["id"]].":00";
		$hora_fin=$_POST["hora_f".$configuracion["id"]].":".$_POST["min_f".$configuracion["id"]].":00";
		
		
		$actualiza=db_query_params('UPDATE registro_temperaturas.configuracion SET dias_semana=?,limite_alarma_piscina=?,limite_sup_alarma_piscina=?,email_notificaciones=?,telefono_notificaciones=?,horario_inicio=?, horario_fin=? WHERE id=?', 'ssssssss', array((string)($dias_semana), (string)($limite_alarma_piscina), (string)($limite_sup_alarma_piscina), (string)($email_notificaciones), (string)($telefono_notificaciones), (string)($hora_inicio), (string)($hora_fin), $configuracion["id"])) or die(db_error());	
		
	}
	
	header("Location: registro_temperaturas.php");
		
?>