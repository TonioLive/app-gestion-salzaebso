<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
require '../../../mailer/PHPMailerAutoload.php';

$id_cita=$_GET["id_cita"];

$asunto=$_POST["asunto"];
$mensaje=$_POST["mensaje"];

// Busca datos nota solo padres
$busca_nota=db_query_params('SELECT * FROM Agenda LEFT JOIN agenda_asistentes ON Agenda.id=agenda_asistentes.id_cita WHERE agenda_asistentes.nivel=1 AND Agenda.id=?', 's', array($id_cita));

if (db_num_rows($busca_nota)!=0) {

	$datos_nota=db_fetch_array($busca_nota);

	$asunto=$datos_nota["Titulo"];
	$mensaje=$datos_nota["Texto"];
	
	$otra_vez="";
	if ($datos_nota["num_veces_enviado"]!=0) {
		$otra_vez=" otra vez";		
	}
	
	// Busca destinatarios: Todos los alumnos actuales o Futuros.
	$busca_alumnos_actuales=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_baja>=? AND alumnos.fecha_matricula<=?', 'ss', array((string)($datos_nota["Fecha"]), (string)($datos_nota["Fecha"])));
	
	$lista_emails_destino=array();
	$num_alumnos_enviar_semana[]=array();
	
	while($alumnos_actuales=db_fetch_array($busca_alumnos_actuales)) {
		
		
		// Busca dias semana asiste
		$busca_alumnos_grupo=db_query_params('SELECT *, alumnos.id_curso AS id_curso_alu  FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_usuario=?', 's', array($alumnos_actuales["id_usuario"])) or die(db_error()."-4");

        while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
               	$num_alumnos_enviar_semana[$grupos_donde["dia"]]++;
                }		
		
		// Array con todos los emails del alumno
	
		$listado_emails_alumno=str_replace(" ","",$alumnos_actuales["email"]);
	
		$array_to = explode(",",$listado_emails_alumno);		
	        
		foreach ($array_to as $to) {        
	        
			if (comprobar_email($to)!=0) {
			
				if (!in_array($to,$lista_emails_destino)) {			
			
		            $lista_emails_destino[]=$to; // Pruebas
					$num_emails_enviar++;
					
				}
					                    
			}
		} 	
	
	}				      		
	
	$num_emails_enviar=count($lista_emails_destino);	
	
	$fecha_actual=strtotime(date("Y-m-d"));		
	
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
			<div class="panel-heading"><h5>Registro de envíos de la nota "<?=$asunto?>".</h5></div>
		  	<div class="panel-body">
		  		<h5>Nº de veces enviada: <?=$datos_nota["num_veces_enviado"]?></h5>
		  		<h5>Fechas en las que se ha enviado: <?=$datos_nota["fechas_envio"]?></h5>			  			
		  		<h5>Nº de emails enviados: <?=$datos_nota["num_envios_ok"]?></h5>	
		  		<h5>Nº de errores al enviar: <?=$datos_nota["num_envios_error"]?> <?php if ($datos_nota["num_envios_error"]>0) { ?><button class="btn btn-primary btn-sm" onclick="if (confirm('¿Estás segura/o?')) { enviar_notificaciones(1); }"><i class="fa fa-envelope" aria-hidden="true"></i> Reintentar enviar los errores</button><?php } ?></h5>
		  		Pulsa el botón "Enviar mensaje" para enviar la notificación<?=$otra_vez?> (se enviará a <?=$num_emails_enviar?> direcciones de email<?php  if ($num_emails_enviar>50) { ?>. Paciencia, puede tardar un poco<?php } ?>).
		  		<div class="text-center">
			  		<br />
			  		Enviar solo a los alumnos de estos días:<br />
			  		<input type="checkbox" value="1" id="dia_semana" name="dia_semana[]" checked="checked"> Lunes (<?=$num_alumnos_enviar_semana[1]?>)
			  		<input type="checkbox" value="2" id="dia_semana" name="dia_semana[]" checked="checked"> Martes (<?=$num_alumnos_enviar_semana[2]?>)
			  		<input type="checkbox" value="3" id="dia_semana" name="dia_semana[]" checked="checked"> Miercoles  (<?=$num_alumnos_enviar_semana[3]?>)
			  		<input type="checkbox" value="4" id="dia_semana" name="dia_semana[]" checked="checked"> Jueves  (<?=$num_alumnos_enviar_semana[4]?>)
			  		<input type="checkbox" value="5" id="dia_semana" name="dia_semana[]" checked="checked"> Viernes  (<?=$num_alumnos_enviar_semana[5]?>)
			  		<input type="checkbox" value="6" id="dia_semana" name="dia_semana[]" checked="checked"> Sábado  (<?=$num_alumnos_enviar_semana[6]?>)
			  		<input type="checkbox" value="7" id="dia_semana" name="dia_semana[]" checked="checked"> Domingo  (<?=$num_alumnos_enviar_semana[7]?>)
			  		<br /><br />
			  		<button class="btn btn-danger" onclick="window.close()"><i class="fa fa-close" aria-hidden="true"></i> Cerrar</button> <button class="btn btn-primary" onclick="if (confirm('¿Estás segura/o?')) { enviar_notificaciones(0); }"><i class="fa fa-envelope" aria-hidden="true"></i> Enviar mensajes</button>
		  		</div>
	  		</div>
		</div>
	
	</div>
	<script>
	
		function enviar_notificaciones(reintentar) {
						
			var checked = []
			$("input[name='dia_semana[]']:checked").each(function ()
			{
			    checked.push(parseInt($(this).val()));
			});
			
			var data = JSON.stringify(checked);			
			
			window.location.href='enviar_email_notificacion.php?reintentar_errores='+reintentar+'&dias_semana='+data+'&id_cita=<?= $datos_nota["id"]; ?>';
			
			
		}
		
	</script>
	</body>
	</html>
<?php
}	
?>