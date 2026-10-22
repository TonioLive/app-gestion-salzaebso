<?php
require_once dirname(__DIR__).'/configuracion/runtime.php';

	require '../mailer/PHPMailerAutoload.php';

	function enviar_sms($mensaje,$destino) {
		
		$resultado="";
		
		// Comprueba saldo SMS
		$url = 'http://api.labsmobile.com/balance.php';
	    $username = (getenv('NEOKIDS_SMS_USER') ?: '');
	    $password = (getenv('NEOKIDS_SMS_PASSWORD') ?: '');
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HEADER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	    $result = curl_exec($ch); curl_close($ch);
	
		$inicio_numero=strpos($result, "<messages>");
		$fin_numero=strpos($result, "</messages>");
		
		$num_creditos=substr($result,($inicio_numero+10),($fin_numero-$inicio_numero-10));	
		
		if ($num_creditos>0) {
						
			$url = 'https://api.labsmobile.com/clients/';
			$username = (getenv('NEOKIDS_SMS_USER') ?: '');
			$password = (getenv('NEOKIDS_SMS_PASSWORD') ?: '');
			
			$lista_recepcion.="<msisdn>34".$destino."</msisdn>";
			
			/* TEST
			$sms = '<sms> 
			   		<recipient>  
						'.$lista_recepcion.'
			   		</recipient>  
			   		<tpoa>34630726246</tpoa>
			   		<test>1</test> 
			    	<message>'.$mensaje.'</message> 
			 	</sms>';
			*/
			$sms = '<sms> 
			   		<recipient>  
						'.$lista_recepcion.'
			   		</recipient>
			   		<tpoa>34630726246</tpoa>
			    	<message>'.$mensaje.'</message> 
			 	</sms>';
			 	 	 
			$sms = utf8_encode($sms);
			
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'XmlData='.$sms);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			$result = curl_exec($ch); curl_close($ch);
			
			$inicio_numero=strpos($result, "<code>");
			$fin_numero=strpos($result, "</code>");
				
			$codigo_resultado=substr($result,($inicio_numero+6),($fin_numero-$inicio_numero-6));
			
			$resultados_posibles=array(
			0 => 'Message has been successfully sent',
			10 => 'Missing XML data in request',
			11 => 'Badly formed XML in request',
			20 => 'The message element must be present in the XML',
			21 => 'The message element cannot be empty',
			22 => 'Message too long. There is a limit of 160 7-bit characters',
			23 => 'There are no recipients',
			24 => 'Too many recipients',
			25 => 'TPOA is exceeding max length',
			26 => 'TPOA change is not allowed for this account',
			27 => 'This message contained one or more invalid character(s)',
			28 => 'Subid is exceeding maximum length',
			30 => 'There was an error while sending the message',
			31 => 'AckLevel has been given but missing AckUrl',
			32 => 'AckUrl has been given but missing AckLevel',
			33 => 'An unknown value for AckLevel has been given. Allowed values are gateway, operator or handset.',
			34 => 'Label field too long',
			35 => 'The account has no enough credit for this sending',
			36 => 'Msisdn format [number] is not allowed',
			37 => 'The account has reach the maximum messages per day',
			38 => 'There was an error while sending the message to this MSISDNs',
			39 => 'The value of the Scheduled field is not a valid datetime format',
			40 => 'The username cannot send scheduled messages',
			41 => 'Scheduled messages cannot be send in test mode');
			
			$resultado = $resultados_posibles[$codigo_resultado];			
			
			
		} else {
			
			$resultado = "No hay saldo";
			
		}
		
		return $resultado;
		
	}
		
	function enviar_email($mensaje,$asunto,$destino) {		
	
		// Envia email
		$nombreRemitente="La Piscina de Neokids";
		$mailFrom="lapiscinadeneokids@gmail.com";
		$asuntoMensaje=ucwords($asunto);
		$MSGhtml_type="text/html";
		$MSGCharSet="UTF-8";		

		$mail = new PHPMailer;
	
		// $mail->SMTPDebug = 3;                               // Enable verbose debug output
	
		$mail->isSMTP();									// Set mailer to use SMTP
		// $mail->Host = (getenv('NEOKIDS_SMTP_HOST') ?: '');  // Specify main and backup SMTP servers
		$mail->Host = (getenv('NEOKIDS_SMTP_HOST') ?: '');
		$mail->SMTPAuth = true;                            	// Enable SMTP authentication
		// $mail->Username = (getenv('NEOKIDS_SMTP_USER') ?: '');          	// SMTP username
		$mail->Username = (getenv('NEOKIDS_SMTP_USER') ?: ''); 
		// $mail->Password = (getenv('NEOKIDS_SMTP_PASSWORD') ?: '');          		// SMTP password
		$mail->Password = (getenv('NEOKIDS_SMTP_PASSWORD') ?: '');
		
		$mail->SMTPSecure = 'tls';                			// Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                      			// TCP port to connect to
	
		$mail->From = $mailFrom;
		$mail->FromName = $nombreRemitente;
		$mail->addAddress($destino, '');
		$mail->addReplyTo($mailFrom, $nombreRemitente);
		$mail->addCustomHeader("List-Unsubscribe",'<lapiscinadeneokids@gmail.com>, <http://gestion.neo-kids.es/mailer/unsubscribe1.php?cuenta='.$destino.'>');
		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);                                  // Set email format to HTML
	
		$mail->Subject = $asuntoMensaje;
		$mail->Body    = $mensaje;	
	
		if(!$mail->send()) {
			$resultado="Error: El mensaje para ".$destino." no ha podido ser enviado.";
		} else {
	    	$resultado="OK: Mensaje para ".$destino." enviado correctamente.";
		}
		
		return $resultado;
	}
			
	$sql_host=app_db_settings('sql_host');
	$sql_db=app_db_settings('sql_db');
	$sql_usuario=app_db_settings('sql_usuario');
	$sql_pass=app_db_settings('sql_pass');
	
	$db_validacion=db_connect("$sql_host","$sql_usuario","$sql_pass");
	db_select_db("$sql_db",$db_validacion);		
	
	$contenido_fichero=file_get_contents('http://10.10.1.15/index.php');	
	
	$array=security_unserialize($contenido_fichero);
	
    $hora_toma_datos = $array[3];    
    
    $busca_si_existe=db_query_params('SELECT * FROM registro_temperaturas WHERE hora_toma_datos=?', 's', array((string)($hora_toma_datos)));
    
    if (db_num_rows($busca_si_existe)==0) {
	
		// 0 Temperatura ambiente
		// 1 Humedad relativa
		// 2 Temperatura Piscina	
	    
	    $inserta_registro=db_query_params('INSERT INTO registro_temperaturas (temp, temp_piscina, humedad_rel, hora_toma_datos) VALUES (?,?,?, ?)', 'ssss', array((string)(str_replace(",", ".", $array[0])), (string)(str_replace(",", ".", $array[2])), (string)(str_replace(",", ".", $array[1])), (string)($hora_toma_datos)));	    
	    
    }
    
	// Lee Configuracion de Alarmas
	$busca_configuracion=db_query("SELECT * FROM configuracion WHERE desactivada=0");
	while ($configuracion=db_fetch_array($busca_configuracion)) {
		
		$array_dias=explode(",",$configuracion["dias_semana"]);
		
		if (in_array(date("N"),$array_dias)) {
		
		$nombre_alarma=$configuracion["nombre_temp"];
		$simbolo=$configuracion["simbolo"];
		
		$telefono_notificaciones=$configuracion["telefono_notificaciones"];
		$email_notificaciones=$configuracion["email_notificaciones"];
		
		// Comprueba horario
		$temp_piscina=floatval(str_replace(",",".",$array[2]));
		
			if ((strtotime(date("H:i:s"))>=strtotime($configuracion["horario_inicio"])) and (strtotime(date("H:i:s"))<=strtotime($configuracion["horario_fin"]))) {
			
				// Comprueba si la temperatura está por debajo del limite inferior
			 	if (($temp_piscina>0) and ($temp_piscina<$configuracion["limite_alarma_piscina"]) and ($configuracion["alarma_enviada"]==0)) {
					
					// Envía notificaciónes
					
					// 1. Email
					if ($email_notificaciones) {
	
						$envia_notificacion_email=enviar_email("ALERTA: La $nombre_alarma de la piscina (".$array[2]." $simbolo) se encuentra por debajo del límite recomendado (".$configuracion["limite_alarma_piscina"]." $simbolo)","Nueva alerta de $nombre_alarma piscina",$email_notificaciones);
				
						echo($envia_notificacion_email."<br />");
					
					}
									
					// 2. SMS
					
					if ($telefono_notificaciones) {
					
						$envia_notificacion=enviar_sms("ALERTA: La $nombre_alarma de la piscina (".$array[2]." $simbolo) se encuentra por debajo del límite recomendado (".$configuracion["limite_alarma_piscina"]." $simbolo)",$telefono_notificaciones);	
						
						echo($envia_notificacion."<br />");
						
					}
					
					// Escribe registro y marca la incidencia
					$inserta_registro=db_query_params('INSERT INTO registro_temperaturas (temp, temp_piscina, humedad_rel, hora_toma_datos, anotaciones) VALUES (?,?,?, ?,?)', 'sssss', array((string)(str_replace(",", ".", $array[0])), (string)(str_replace(",", ".", $array[2])), (string)(str_replace(",", ".", $array[1])), (string)($hora_toma_datos), 'ALERTA: La ' . (string)($nombre_alarma) . ' de la piscina (' . (string)($array[2]) . ' ' . (string)($simbolo) . ') se encuentra por debajo del límite recomendado (' . (string)($configuracion["limite_alarma_piscina"]) . ' ' . (string)($simbolo) . ')')) or die(db_error()."-1");	
					
					$marca_incidencia=db_query_params('UPDATE configuracion SET alarma_enviada=1 WHERE id=?', 's', array($configuracion["id"])) or die(db_error()."-2");	
				}
				
				// Comprueba si la temperatura está por encima del limite superior
			 	if (($temp_piscina>0) and ($temp_piscina>$configuracion["limite_sup_alarma_piscina"]) and ($configuracion["alarma_enviada"]==0)) {
					
					// Envía notificaciónes
					
					// 1. Email
					if ($email_notificaciones) {
	
						$envia_notificacion_email=enviar_email("ALERTA: La $nombre_alarma de la piscina (".$array[2]." $simbolo) se encuentra por encima del límite recomendado (".$configuracion["limite_sup_alarma_piscina"]." $simbolo)","Nueva alerta de $nombre_alarma piscina",$email_notificaciones);
				
						echo($envia_notificacion_email."<br />");
					
					}
									
					// 2. SMS
					
					if ($telefono_notificaciones) {
					
						$envia_notificacion=enviar_sms("ALERTA: La $nombre_alarma de la piscina (".$array[2]." $simbolo) se encuentra por encima del límite recomendado (".$configuracion["limite_sup_alarma_piscina"]." $simbolo)",$telefono_notificaciones);	
						
						echo($envia_notificacion."<br />");
						
					}
					
					// Escribe registro y marca la incidencia
					$inserta_registro=db_query_params('INSERT INTO registro_temperaturas (temp, temp_piscina, humedad_rel, hora_toma_datos, anotaciones) VALUES (?,?,?, ?,?)', 'sssss', array((string)(str_replace(",", ".", $array[0])), (string)(str_replace(",", ".", $array[2])), (string)(str_replace(",", ".", $array[1])), (string)($hora_toma_datos), 'ALERTA: La ' . (string)($nombre_alarma) . ' de la piscina (' . (string)($array[2]) . ' ' . (string)($simbolo) . ') se encuentra por encima del límite recomendado (' . (string)($configuracion["limite_sup_alarma_piscina"]) . ' ' . (string)($simbolo) . ')')) or die(db_error()."-1");	
					
					$marca_incidencia=db_query_params('UPDATE configuracion SET alarma_enviada=1 WHERE id=?', 's', array($configuracion["id"])) or die(db_error()."-2");	
				}				
			
			}
		
			// Comprueba si la temperatura se ha recuperado
			if (($temp_piscina>=$configuracion["limite_alarma_piscina"]) and ($temp_piscina<=$configuracion["limite_sup_alarma_piscina"]) and ($configuracion["alarma_enviada"]==1)) {
				
				// La temperatura se ha recuperado 
	
				// Envía notificaciónes
				
				// 1. Email
				if ($email_notificaciones) {
					
					$envia_notificacion_email=enviar_email("FIN DE ALERTA: La $nombre_alarma de la piscina (".$array[2]." $simbolo) se recupera y se encuentra dentro de los límites recomendados (".$configuracion["limite_alarma_piscina"]." $simbolo a ".$configuracion["limite_sup_alarma_piscina"]." $simbolo)","Fin de alerta de $nombre_alarma piscina",$email_notificaciones);
				
					echo($envia_notificacion_email."<br />");
					
				}
				
				// 2. SMS
				if ($telefono_notificaciones) {
					
					$envia_notificacion=enviar_sms("FIN DE ALERTA: La $nombre_alarma de la piscina (".$array[2]." $simbolo) se recupera y se encuentra dentro de los límites recomendados (".$configuracion["limite_alarma_piscina"]." $simbolo a ".$configuracion["limite_sup_alarma_piscina"]." $simbolo)",$telefono_notificaciones);
				
					echo($envia_notificacion."<br />");
					
				}
				
				// Escribe registro y elimina la incidencia
				$inserta_registro=db_query_params('INSERT INTO registro_temperaturas (temp, temp_piscina, humedad_rel, hora_toma_datos, anotaciones) VALUES (?,?,?, ?,?)', 'sssss', array((string)(str_replace(",", ".", $array[0])), (string)(str_replace(",", ".", $array[2])), (string)(str_replace(",", ".", $array[1])), (string)($hora_toma_datos), 'FIN DE ALERTA: La ' . (string)($nombre_alarma) . ' de la piscina (' . (string)($array[2]) . ' ' . (string)($simbolo) . ') se recupera y se encuentra dentro de los límites recomendados (' . (string)($configuracion["limite_alarma_piscina"]) . ' ' . (string)($simbolo) . ' a ' . (string)($configuracion["limite_sup_alarma_piscina"]) . ' ' . (string)($simbolo) . ')')) or die(db_error()."-3");	
				
				$marca_incidencia=db_query_params('UPDATE configuracion SET alarma_enviada=0 WHERE id=?', 's', array($configuracion["id"])) or die(db_error()."-4");	
			}		
		
		} // Fin comprueba día de la semana							
	} // Fin bucle alarmas								    
      	
?>