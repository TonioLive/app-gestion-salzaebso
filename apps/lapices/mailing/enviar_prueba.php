<?php 
require '../herramientas/PHPMailerAutoload.php';

$id_mensaje=$_GET["id"];

// Comprobar si el referer es correcto...

$sql_host = (getenv('APP_DB_HOST') ?: 'localhost');
$sql_db = (getenv('APP_DB_NAME') ?: '');
$sql_usuario = (getenv('APP_DB_USER') ?: '');
$sql_pass = (getenv('APP_DB_PASSWORD') ?: '');

$db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_validacion);

$busca_mensaje=db_query("SELECT * FROM mensajes_temporal WHERE id_mensaje=$id_mensaje;") or die(db_error()."-1");

$datos_mensaje=db_fetch_array($busca_mensaje);

$asunto_mensaje=$datos_mensaje["asunto"];
$texto_editor=$datos_mensaje["texto_editor"];
$retorno=$datos_mensaje["retorno"];

$titulo_web="Escuela infantil lápices";

$mail_webmaster="circulares@escuelas-infantiles.com";

$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_webmaster." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");

$msg_normal="<img src='https://www.escuelasinfantileslapices.es/imagen/encabezado_mensaje.gif'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
$msg_alternativo="<br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
//     $msg="<img src='https://www.escuelasinfantileslapices.es/imagen/encabezado_mensaje.gif'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

$mensajes_enviados=0;

// Metodo antiguo
/*
                        $to = $mail_webmaster;
                        
                        $msg=$msg_normal;
                        if (substr($to,strlen($to)-23,23)=="escuelas-infantiles.com") {
                                $msg=$msg_alternativo;
                                }

                        $from = ucfirst($titulo_web)."<".$mail_webmaster.">";

                                $headers = "From: " . $titulo_web . " <" . $mail_webmaster . ">\n";
                                $headers .= "Return-Path: <" . $mail_webmaster . ">\n";
                                $headers .= "Reply-To: " . $mail_webmaster . "\n";
                                $headers .= "X-Mailer:PHP" . phpversion() . "\n";
                                $headers .= "Precedence: list\nList-Id: " . $titulo_web . "\n";

                                if ('html' == $type) {
                                        $sub = UTF8_encode($asunto_mensaje);

                                        $headers .= "MIME-Version: 1.0\n";
                                        $headers .= "Content-Type: " . $MSGhtml_type . "; charset=\"". $MSGCharSet . "\"\n";
                                        $mailtext = "<html><head><title>" . $sub . "</title></head><body>" . $msg . "</body></html>";
                                } else {
                                        $sub = UTF8_encode($asunto_mensaje);

                                        $msg=UTF8_encode($msg);

                                        $headers .= "MIME-Version: 1.0\n";
                                        $headers .= "Content-Type: text/plain; charset=\"". $MSGCharSet . "\"\n";
                                        $message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $msg);
                                        $message = preg_replace('|&amp;|', '&', $msg);
                                        $mailtext = wordwrap(strip_tags($msg), 80, "\n");
                                }

                        $hdrs = "From: ".ucfirst($titulo_web)."<".$mail_webmaster.">\n";

                        mail($to, $sub, $msg,$headers);
                        
*/
// Fin metodo antiguo

// Metodo nuevo
$to = $mail_webmaster;
$asuntoMensaje=$asunto_mensaje;
$cuerpo_mensaje=$msg_normal;
$mailFrom = "circulares@escuelas-infantiles.com";
$nombreRemitente="Escuelas Infantiles Lápices";
$nombreDestinatario="Escuelas Infantiles Lápices";


$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

// $mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = (getenv('LAPICES_SMTP_HOST') ?: '');  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = (getenv('LAPICES_SMTP_USER') ?: '');                 // SMTP username
$mail->Password = (getenv('LAPICES_SMTP_PASSWORD_04') ?: '');                           // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                     // TCP port to connect to

$mail->From = $mailFrom;
$mail->FromName = $nombreRemitente;
$mail->addAddress($to, $nombreDestinatario);
$mail->addReplyTo($mailFrom, $nombreRemitente);
$mail->addCustomHeader("List-Unsubscribe",'<circulares@escuelas-infantiles.com>');
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $asuntoMensaje;
$mail->Body    = $cuerpo_mensaje;

if(!$mail->send()) {
        $mensajesFallados++;
} else {
    $mensajesEnviados++;
}


// Fin metodo nuevo

db_query("DELETE FROM mensajes_temporal WHERE id_mensaje=$id_mensaje;") or die(db_error()."-2");

db_close();
?>
<html>
<head>

 <title>SELECCIÓN FECHAS</title>

 <META http-equiv='Content-Type' content='text/html; charset=UTF-8'>
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
