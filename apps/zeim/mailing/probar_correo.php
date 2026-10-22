<?php
require_once dirname(__DIR__).'/configuracion/runtime.php';
 
die();
require '../herramientas/PHPMailerAutoload.php';

$id_mensaje=$_GET["id"];

// Comprobar si el referer es correcto...

$sql_host=app_db_settings('sql_host');
$sql_db=app_db_settings('sql_db');
$sql_usuario=app_db_settings('sql_usuario');
$sql_pass=app_db_settings('sql_pass');

$asunto_mensaje=utf8_decode("Mensaje de prueba del correo de Sant Pau");
$texto_editor=utf8_decode("Esto es una prueba, si lo recibes el correo funciona.<br><br>");
$mail_webmaster=$_GET["destinatario"];

if (strlen($mail_webmaster)==0) {
        $mail_webmaster="webmaster@escuelainfantilsantpau.com";
        }

$titulo_web="Escuela infantil municipal Sant Pau";


$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_webmaster." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");

$msg_normal="<img src='http://padres.escuelainfantilsantpau.com/imagen/encabezado_mensaje.gif'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
$msg_alternativo="<br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
//     $msg="<img src='http://padres.escuelainfantilsantpau.com/imagen/encabezado_mensaje.gif'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

$mensajes_enviados=0;


// Metodo nuevo
$to = $mail_webmaster;
$asuntoMensaje=$asunto_mensaje;
$cuerpo_mensaje=$msg_normal;
$mailFrom = "escuelainfantilsantpau@enviosmail.net";
$nombreRemitente="Escuela Infantil Sant Pau";
$nombreDestinatario="Escuela Infantil Sant Pau";


$mail = new PHPMailer;
                               
$mail->SMTPDebug = 3;                               		// Enable verbose debug output

$mail->isSMTP();                                      		// Set mailer to use SMTP
$mail->Host = '94.23.35.93';  								// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               		// Enable SMTP authentication
$mail->Username = (getenv('ZEIM_SMTP_USER_02') ?: '');  // SMTP username
$mail->Password = (getenv('ZEIM_SMTP_PASSWORD_02') ?: '');                           	// SMTP password

$mail->From = $mailFrom;
$mail->FromName = $nombreRemitente;
$mail->addAddress($to, $nombreDestinatario);
$mail->addReplyTo($mailFrom, $nombreRemitente);
$mail->addCustomHeader("List-Unsubscribe",'<info@escuelainfantilsantpau.com>');
$mail->WordWrap = 50;                                 		// Set word wrap to 50 characters
$mail->isHTML(true);                                  		// Set email format to HTML

$mail->Subject = $asuntoMensaje;
$mail->Body    = $cuerpo_mensaje;

if(!$mail->send()) {
        $mensajesFallados++;
} else {
    $mensajesEnviados++;
}


?>
<html>
<head>

 <title>PRUEBA DE MENSAJES</title>

 <META http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
 <META NAME='Keywords' CONTENT=''>
 <META NAME='Description' CONTENT=''>
 <META NAME='Generator' CONTENT='flash 5'>
 <META HTTP-EQUIV='Content-language' CONTENT='es'>
 <META HTTP-EQUIV='Reply-to' CONTENT=''>
 <META NAME='Author' CONTENT=''>
 <META NAME='Distribution' CONTENT='Global'>
 <META NAME='Rating' CONTENT='General'>
 <META NAME='Robots' CONTENT='All'>
 <META NAME='Revisit-After' CONTENT='1 days'>
 <META HTTP-EQUIV='Reply-to' CONTENT=''>
<style type="text/css">
        body {
                scrollbar-arrow-color:#840084;
                scrollbar-face-color:#DEE3D6;
                scrollbar-bace-color:green;
                scrollbar-highlight-color:#B5B6A5;
                scrollbar-shadow-color:black;
                scrollbar-track-color:#B5B6A5;
                font-family: Trebuchet MS, Arial;
                font-size: x-small;
                font-style: normal;
                line-height: normal;
                font-weight: normal;
                font-variant: normal;
                color: #000000;
                text-decoration: none;
}

a {  font-style: normal; font-weight: normal; color: #000000; text-decoration: none}
a:hover {  font-style: normal; font-weight: normal; text-decoration: underline}
</style>

<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="#FEFEFE" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center">
<br><br>
Se ha enviado el mensaje de prueba a la dirección "<?=$mail_webmaster;?>".<br><br><br>
<input type="button" onclick="window.close()" value="Cerrar">
</div>
</body>
</html></html>
