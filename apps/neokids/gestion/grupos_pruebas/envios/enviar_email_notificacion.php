<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
require '../../../mailer/PHPMailerAutoload.php';

$id_cita=$_GET["id_cita"];
$reintentar_errores=$_GET["reintentar_errores"];
$dias_semana=$_GET["dias_semana"];

if ((strlen($dias_semana)!=0) or ($reintentar_errores==1)) {	

$array_dias_semana = json_decode($dias_semana);
	
$filtro_dias="";

foreach ($array_dias_semana as $dia) {

	$filtro_dias.="aulas_grupos.dia=".$dia." OR ";		
	
}	

if ($filtro_dias!="") {
	$filtro_dias=substr($filtro_dias,0,strlen($filtro_dias)-4);
}

$filtro_dias="(".$filtro_dias.")";	

// Busca datos nota solo padres
$busca_nota=db_query_params('SELECT * FROM Agenda LEFT JOIN agenda_asistentes ON Agenda.id=agenda_asistentes.id_cita WHERE agenda_asistentes.nivel=1 AND Agenda.id=?', 's', array($id_cita));

if (db_num_rows($busca_nota)!=0) {

	$datos_nota=db_fetch_array($busca_nota);

	$asunto=$datos_nota["Titulo"];
	$mensaje=$datos_nota["Texto"];
	
	$lista_emails_destino=array();
	
	if ($reintentar_errores==1) {
	
		// Busca ultimo registro de error
		$busca_registro_error=db_query_params('SELECT * FROM registro_envio_emails WHERE asunto=? AND registro_envio like \'%Error%\' ORDER BY id_registro DESC LIMIT 0,1', 's', array((string)($asunto)));
		
		while($registro_error=db_fetch_array($busca_registro_error)) {
		
			$lista_errores=security_unserialize($registro_error["registro_envio"]);
			
			foreach($lista_errores as $registro) {
				
				if ($registro["resultado"]=="Error") {
				
					$lista_emails_destino[]=$registro["destinatario"];	
					
				}				
				
			}				
			
		}
		
		
	} else {
	
	// Busca destinatarios: Todos los alumnos actuales o Futuros.
	$busca_alumnos_actuales=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_baja>=? AND alumnos.fecha_matricula<=?', 'ss', array((string)($datos_nota["Fecha"]), (string)($datos_nota["Fecha"])));
	
	while($alumnos_actuales=db_fetch_array($busca_alumnos_actuales)) {
		
		// Busca dias semana asiste
		$busca_alumnos_grupo=db_query("SELECT *, alumnos.id_curso AS id_curso_alu  FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_usuario=".$alumnos_actuales["id_usuario"]." AND ".$filtro_dias) or die(db_error()."-4");
		
		if (db_num_rows($busca_alumnos_grupo)!=0) {		
		
			// Array con todos los emails del alumno
		
			$listado_emails_alumno=str_replace(" ","",$alumnos_actuales["email"]);
		
			$array_to = explode(",",$listado_emails_alumno);		
		        
			foreach ($array_to as $to) {        
		        
				if (comprobar_email($to)!=0) {
				
					if (!in_array($to,$lista_emails_destino)) {
				
			            $lista_emails_destino[]=$to;
						$num_emails_enviar++;
						
					}						                    
				}
			} 
		}		
	}				      			
	
	}
	
	$num_emails_enviar=count($lista_emails_destino);	
		
		$fecha_actual=strtotime(date("Y-m-d"));
	
		$mail_webmaster="lapiscinadeneokids@gmail.com";
	
		$d=date ("d");
		$m=date ("m");
		$y=date ("Y");
		$t=time();
		$dmt=$d+$m+$y+$t;
		$ran= rand(0,10000000);
		$dmtran= $dmt+$ran;
		$un= uniqid();
		$dmtun = $dmt.$un;
		$mdun = md5($dmtran.$un);
	
		// Envia email
	
		$nombreRemitente="La Piscina de Neokids";
		$mailFrom=$mail_webmaster;
		$asuntoMensaje=ucwords($asunto);
		$MSGhtml_type="text/html";
		$MSGCharSet="UTF-8";
	
		$cuerpo_mensaje = $mensaje;
	
		$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje. Si no desea recibir m&aacute;s informaci&oacute;n por correo electr&oacute;nico acceda a esta dirección http://gestion.neo-kids.es/mailer/unsubscribe.php");
	
		$cuerpo_mensaje=$cuerpo_mensaje."<br><br>Puedes ampliar la información, leer esta y otras informaciones en tu Área Privada de Neokids.<br><br>Haz clic en el siguiente enlace para acceder a tu Área Privada: http://gestion.neo-kids.es/clientes/<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
	
	?>
	<!doctype html>
	<html lang="en">
	<head>
	
	<meta charset="WINDOWS-1258">
	<title>Enviar Notificación por Email</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
	
	<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
	<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>
	
	<link rel="stylesheet" href="style.css">
	
	<style>
	.fondo_nivelB {
	        background: #000;
	        }                        
	 
	.textoBlanco {
	                color: #fff;
	                } 
	                
	.panelFijo{
	    height: 1000vh;
	    overflow-y: no;
	    overflow-x: hidden;
	}
	 
	.modal.modal-wide .modal-dialog {
	  width: 95%;
	}
	.modal-wide .modal-body {
	  overflow-y: auto;
	} 
	</style>
	
	</head>
	<body>
	
	<div class="container-fluid">
		<div class="panel panel-default">
			<div class="panel-heading"><h5><i class="fa fa-envelope" aria-hidden="true"></i> Se han enviado <?=$num_emails_enviar?> mensajes de correo electrónico a los alumnos seleccionados.</h5></div>
		  	<div class="panel-body">
		  		<h5>Resultado:</h5>	
				<?php 
		
	$registro_envio=array();
	
	$mensajesEnviados=0;
	$mensajesFallados=0;
				
	foreach ($lista_emails_destino as $to) {
	
		$mail = new PHPMailer;
	
		// $mail->SMTPDebug = 3;                               // Enable verbose debug output
	
		$mail->isSMTP();                                      // Set mailer to use SMTP
		// $mail->Host = (getenv('NEOKIDS_SMTP_HOST') ?: '');  // Specify main and backup SMTP servers
		$mail->Host = (getenv('NEOKIDS_SMTP_HOST') ?: '');
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		// $mail->Username = (getenv('NEOKIDS_SMTP_USER') ?: '');                 // SMTP username
		$mail->Username = (getenv('NEOKIDS_SMTP_USER') ?: ''); 
		// $mail->Password = (getenv('NEOKIDS_SMTP_PASSWORD') ?: '');                           // SMTP password
		$mail->Password = (getenv('NEOKIDS_SMTP_PASSWORD') ?: '');
		
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to
	
		$mail->From = $mailFrom;
		$mail->FromName = $nombreRemitente;
		$mail->addAddress($to, '');
		$mail->addReplyTo($mailFrom, $nombreRemitente);
		$mail->addCustomHeader("List-Unsubscribe",'<lapiscinadeneokids@gmail.com>, <http://gestion.neo-kids.es/mailer/unsubscribe1.php?cuenta='.$to.'>');
		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);                                  // Set email format to HTML
	
		$mail->Subject = $asuntoMensaje;
		$mail->Body    = $cuerpo_mensaje;	
	
		if(!$mail->send()) {
			echo("<span class='text-danger'>Error: El mensaje para ".$to." no ha podido ser enviado.</span><br />");
			$resultado="Error";
			$mensajesFallados++;
		} else {
			echo("<span class='text-success'>OK: Mensaje para ".$to." enviado correctamente.</span><br />");
	    	$mensajesEnviados++;
	    	$resultado="Enviado";
		}
		
		$registro_envio[]=array('destinatario' => $to, 'resultado' => $resultado);
		
	}
	
	// Inserta info envio
	$inserta_info=db_query("UPDATE Agenda SET num_veces_enviado=num_veces_enviado+1,num_envios_ok=num_envios_ok+$mensajesEnviados,num_envios_error=$mensajesFallados,fechas_envio=concat(fechas_envio, '<br>".date("d/m/Y H:i:s")."') WHERE id=$id_cita") or die(db_error());
	
	// Inserta registro de envío
	$inserta_registro=db_query_params('INSERT INTO registro_envio_emails (asunto,mensaje,registro_envio,enviado_por) VALUES (?,?,?,?);', 'ssss', array((string)($asuntoMensaje), (string)($mensaje), (string)(serialize($registro_envio)), (string)($idUsuario)));
	
				?>
				<div class="text-center">
					<?php
					
					if ($mensajesFallados>0) {
					?>	
					Se ha producido algún error al enviar los mensajes, espera unos minutos y pulsar el botón "Reintentar errores" si quieres volver a enviar los mensajes que no han podido enviarse.
			  		<br />
			  		<button class="btn btn-danger" onclick="window.location.href='enviar_email_notificacion.php?reintentar_errores=1&id_cita=<?= $id_cita; ?>'"><i class="fa fa-envelope" aria-hidden="true"></i> Cerrar</button>					
					<?php 	
					}	
						
					?>
			  		<br />
			  		<button class="btn btn-danger" onclick="window.close()"><i class="fa fa-close" aria-hidden="true"></i> Cerrar</button>
		  		</div>
	  		</div>
		</div>
	
	</div>
	
	</body>
	</html>
<?php
	}
} else {
?>
No se ha enviado ningún mensaje, no has seleccionado ningún día de la semana.<br /><br /><br />
<input type="button" onclick="history.go(-1)" value="Volver">	
<?php 	
}	
?>