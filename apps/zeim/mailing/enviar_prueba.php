<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require '../herramientas/PHPMailerAutoload.php';

$id_mensaje=$_GET["id"];

$busca_mensaje=db_query_params('SELECT * FROM mensajes_temporal WHERE id_mensaje=?;', 's', array($id_mensaje)) or die(db_error()."-1");

$datos_mensaje=db_fetch_array($busca_mensaje);

$asunto_mensaje=$datos_mensaje["asunto"];
$texto_editor=utf8_decode($datos_mensaje["texto_editor"]);
$retorno=$datos_mensaje["retorno"];

switch ($centro_actual) {
	case 10:
		$mail_webmaster="santpau@escuelas-infantiles.com";
		$nombreRemitente="Escuela Infantil Sant Pau";
		$linkUnsubscribe="santpau.escuelas-infantiles.com";
	break;	
	case 23:
		$mail_webmaster="benicalap@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal Benicalap";
		$linkUnsubscribe="gestion.escuelainfantilbenicalap.com";
	break;
	case 2:
		$mail_webmaster="minipoli@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal Minipoli";
		$linkUnsubscribe="gestion.escuelainfantilminipoli.com";
	break;	
	case 25:
		$mail_webmaster="lacreu@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal La Creu";
		$linkUnsubscribe="gestion.escuelainfantillacreu.com";
	break;	        
}
$logoEscuela="https://".$linkUnsubscribe."/imagen/logo_".$centro_actual.".jpg";


$mailCopia="informatica@escuelas-infantiles.com"; // Para debug


$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$texto_legal="De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.";
$msg_normal="<p align=\"center\"><img src=\"".$logoEscuela."\" border=\"0\"></p><p>".$texto_editor."</p><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
$msg_alternativo="<br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

$mensajes_enviados=0;

// Metodo nuevo
// $to = $mail_webmaster;
$to = $mail_webmaster;
$asuntoMensaje=$asunto_mensaje;
$cuerpo_mensaje=$msg_normal;
$mailFrom = $mail_webmaster;
$nombreDestinatario=$nombreRemitente;


$mail = new PHPMailer;
                               
$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                    // Set mailer to use SMTP

// Mailrelay

$mail->Host = 'smtp1.s.ipzmarketing.com';  	    // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                             // Enable SMTP authentication
$mail->Username = (getenv('ZEIM_SMTP_USER_01') ?: '');   		    // SMTP username
$mail->Password = (getenv('ZEIM_SMTP_PASSWORD_01') ?: '');               // SMTP password
$mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                  // TCP port to connect to

// Enviosmail.net
/*
$mailFromReal = 'escuelainfantilbenicalap@enviosmail.net';
$mail->Host = 'ns3329694.ip-94-23-35.eu';  	    // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                             // Enable SMTP authentication
$mail->Username = (getenv('ZEIM_SMTP_USER_05') ?: '');   		    // SMTP username
$mail->Password = (getenv('ZEIM_SMTP_PASSWORD_05') ?: '');               // SMTP password
$mail->SMTPSecure = 'ssl';                          // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                  // TCP port to connect to   
*/
// $mail->Port = 25;											

$mail->From = $mailFrom;
$mail->FromName = $nombreRemitente;
$mail->addAddress($to, '');
$mail->addReplyTo($mailFrom, $nombreRemitente);
$mail->addCC($mailCopia);
$mail->addCustomHeader("List-Unsubscribe",'<'.$mailFrom.'>, <http://'.$linkUnsubscribe.'/mailer/unsubscribe_alumno1.php?cuenta='.$to.'>');
$mail->WordWrap = 50;                               // Set word wrap to 50 characters
$mail->isHTML(true);                                // Set email format to HTML

$mail->Subject = $asuntoMensaje;
$mail->Body    = $cuerpo_mensaje;

if(!$mail->send()) {
        $mensajesFallados++;
} else {
    $mensajesEnviados++;
}


// Fin metodo nuevo

db_query_params('DELETE FROM mensajes_temporal WHERE id_mensaje=?;', 's', array($id_mensaje)) or die(db_error()."-2");

db_close();
?>
<html>
<head>

 <title>Enviar mensaje de prueba</title>

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
Se ha enviado el mensaje de prueba a la dirección "<?=$to;?>".<br><br><br>
<input type="button" onclick="window.close()" value="Cerrar">
</div>
</body>
</html></html>
