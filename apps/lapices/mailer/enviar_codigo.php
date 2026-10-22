<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require 'PHPMailerAutoload.php';

$id_contacto=$_GET["id_contacto"];

$busca_datos_contacto=db_query("SELECT * FROM contactos WHERE id_contacto=$id_contacto");

if (db_num_rows($busca_datos_contacto)!=0) {

$datos_contacto=db_fetch_array($busca_datos_contacto);

$to = $datos_contacto["email"];

if (strlen($to)==0) {
	$to = $datos_contacto["email_padre2"];
}

if (strlen($to)==0) {
	$to = $datos_contacto["email_familia"];
}

if (strlen($to)!=0) {

	$fecha_actual=strtotime(date("Y-m-d"));

	$mail_webmaster="info@neo-kids.es";

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


	// Inserta codigo único en ficha usuario

	$inserta_codigo=db_query("UPDATE contactos SET codigo_uso_unico='$mdun' WHERE id_contacto=".$id_contacto);

	// Envia email

	$nombreRemitente="La Piscina de Neokids";
	$mailFrom=$mail_webmaster;
	$asuntoMensaje="Completa el formulario de inscripción en La Piscina de Neokids";
	$MSGhtml_type="text/html";
	$MSGCharSet="UTF-8";

	$cuerpo_mensaje = "Gracias por venir a informarte a La Piscina de Neokids.<br><br>Para completar tu registro haz click en el siguiente enlace:<br><br>http://gestion.neo-kids.es/formulario/index.php?unid=".$mdun."<br><br>Si el desde el correo eléctrónico no funciona, copia y pega el enlace completo en la barra de tu navegador.<br><br>El enlace es de uso único, si accedes, sales de la página por error y necesitas completar los datos, ponte en contacto con nosotros y te haremos llegar un nuevo enlace.<br><br>Un cordial saludo,<br><br>Neokids<br><br>";

	$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje. Si no desea recibir m&aacute;s informaci&oacute;n por correo electr&oacute;nico acceda a esta dirección http://gestion.neo-kids.es/mailer/unsubscribe.php");

	$cuerpo_mensaje=$cuerpo_mensaje."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

	$mail = new PHPMailer;
		               
	// $mail->SMTPDebug = 3;                               // Enable verbose debug output

					$mail->isSMTP();                                      // Set mailer to use SMTP
					$mail->Host = (getenv('LAPICES_SMTP_HOST') ?: '');  // Specify main and backup SMTP servers
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = (getenv('LAPICES_SMTP_USER') ?: '');                 // SMTP username
					$mail->Password = (getenv('LAPICES_SMTP_PASSWORD_02') ?: '');                           // SMTP password
					$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					$mail->Port = 587;                                    // TCP port to connect to

	$mail->From = $mailFrom;
	$mail->FromName = $nombreRemitente;
	$mail->addAddress($to, $datos_contacto["persona_contacto"]);
	$mail->addReplyTo($mailFrom, $nombreRemitente);
	$mail->addCustomHeader("List-Unsubscribe",'<info@neo-kids.es>, <http://gestion.neo-kids.es/mailer/unsubscribe1.php?cuenta='.$to.'>');
	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $asuntoMensaje;
	$mail->Body    = $cuerpo_mensaje;

	if(!$mail->send()) {
		$mensajesFallados++;
	} else {
    	$mensajesEnviados++;
	}

	header("Location: /contactos/listado_contactos.php?curso_seleccionado=".$datos_contacto["id_curso"]);

} else {
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>La piscina de Neokids - Nuevo Contacto</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<style type="text/css">
.myField{
    width:32%;
    display:inline-block;

	}
.myField75{
    width:50%;
    display:inline-block;
	}
</style>	

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" class="pull-right"><br /><br /><br /><h3>La Piscina de Neokids - Contactos</h3></div>
  		<div class="panel-body">
    	No disponemos de ningún email del usuario, no podemos enviarle el código por email.<br><br>
		¿Quieres generar un código QR para que el usuario lo capture con su movil o con la tablet?
		<br><br>
		<button type="button" onclick="window.open('generar_qr.php?id_contacto=<?=$id_contacto?>');window.location.href='/contactos/listado_contactos.php?curso_seleccionado=<?=$datos_contacto["id_curso"];?>';" class="btn btn-primary btn-lg">Si, Generar Código QR</button> <button type="button" onclick="window.location.href='/contactos/listado_contactos.php?curso_seleccionado=<?=$datos_contacto["id_curso"];?>'" class="btn btn-danger btn-lg">No, quiero salir</button>
		</div>
	</div>    
</div>
</body>
</html>
<?php 
}


}

?>