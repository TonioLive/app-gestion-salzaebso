<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
require '../../../mailer/PHPMailerAutoload.php';

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha_limite=$_POST["fecha_limite"];
$listado_emails=$_POST["listado_emails"];
$asunto=$_POST["asunto"];
$mensaje=$_POST["mensaje"];

if (strlen($id_subgrupo)!=0) {

        $busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

        $datos_grupo=db_fetch_array($busca_datos_subgrupo);

		$id_grupo_subgrupo=$datos_grupo["id_grupo_subgrupo"];
        $id_curso_actual=$datos_grupo["id_curso"];
        $c_dia=$datos_grupo["dia"];
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        
}

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite)+$tiempo_gracia;

      

$lista_emails_destino=security_unserialize($listado_emails);

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

	$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.<br><br>Si no desea recibir m&aacute;s informaci&oacute;n por correo electr&oacute;nico acceda a esta dirección http://gestion.neo-kids.es/mailer/unsubscribe.php");

	$cuerpo_mensaje=$cuerpo_mensaje."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Enviar SMS a Grupo</title>
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

// Inserta registro de envío
$inserta_registro=db_query_params('INSERT INTO registro_envio_emails (asunto,mensaje,registro_envio,enviado_por) VALUES (?,?,?,?);', 'ssss', array((string)($asuntoMensaje), (string)($mensaje), (string)(serialize($registro_envio)), (string)($idUsuario)));

			?>
  		</div>
	</div>

</div>

</body>
</html>