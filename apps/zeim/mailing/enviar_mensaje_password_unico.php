<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<html>
<head>

 <title>MENSAJE ENVIADO</title>

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
<?php
require '../herramientas/PHPMailerAutoload.php';

function enviarMensajeAlumnos ($to,$asunto_mensaje,$msg_normal,$nombre_padre_act,$id_centro_alumno) {

    /* Para debugging
    
    echo("Mensaje enviado a: ".$to."<br><br>Mensaje enviado: <br><br>".$msg_normal."<br><br>");    
    return true;
    
    */

        $email_centro[10]="santpau@escuelas-infantiles.com";
        $email_centro[23]="benicalap@escuelas-infantiles.com";
        $email_centro[24]="minipoli@escuelas-infantiles.com";
        $email_centro[25]="lacreu@escuelas-infantiles.com";
        $email_centro[26]="albalat@escuelas-infantiles.com";


        $nombres_centro[10]="Escuela Infantil Sant Pau";
        $nombres_centro[23]="Escuela Infantil Municipal Benicalap";
        $nombres_centro[24]="Escuela Infantil Municipal Minipoli";
        $nombres_centro[25]="Escuela Infantil Municipal La Creu";
        $nombres_centro[26]="Escuela Infantil Municipal Albalat dels Sorells";

        $links_centro[10]="santpau.escuelas-infantiles.com";
        $links_centro[23]="gestion.escuelainfantilbenicalap.com";
        $links_centro[24]="gestion.escuelainfantilminipoli.com";
        $links_centro[25]="gestion.escuelainfantillacreu.com";
        $links_centro[26]="gestion.escuelainfantilalbalat.com";

        $asuntoMensaje = $asunto_mensaje;
        $cuerpo_mensaje = $msg_normal;
        $mailFrom = $email_centro[$id_centro_alumno];
        $nombreRemitente = $nombres_centro[$id_centro_alumno];
        $nombreDestinatario = $nombre_padre_act;

        $mail = new PHPMailer;

        // $mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                    // Set mailer to use SMTP
        $mail->Host = 'smtp1.s.ipzmarketing.com';  	    // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                             // Enable SMTP authentication
        $mail->Username = (getenv('ZEIM_SMTP_USER_01') ?: '');   		    // SMTP username
        $mail->Password = (getenv('ZEIM_SMTP_PASSWORD_01') ?: '');               // SMTP password
        $mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to
        // $mail->Port = 25;

        $mail->From = $mailFrom;
        $mail->FromName = $nombreRemitente;
        $mail->addAddress($to, $nombreDestinatario);
        $mail->addReplyTo($mailFrom, $nombreRemitente);
        $mail->addCustomHeader("List-Unsubscribe",'<'.$mailFrom.'>');
        $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $asuntoMensaje;
        $mail->Body    = $cuerpo_mensaje;

        if(!$mail->send()) {                
                return true;
        } else {
                return false;
        }
}

$centro_seleccionado_email=$_GET["id_centro"];
$solo_activos=$_GET["solo_activos"];
if (strlen($solo_activos)==0) {
    $solo_activos=0;
}    

$email_centro[10]="santpau@escuelas-infantiles.com";
$email_centro[23]="benicalap@escuelas-infantiles.com";
$email_centro[24]="minipoli@escuelas-infantiles.com";
$email_centro[25]="lacreu@escuelas-infantiles.com";
$email_centro[26]="albalat@escuelas-infantiles.com";

$nombres_centro[10]="Escuela Infantil Sant Pau";
$nombres_centro[23]="Escuela Infantil Municipal Benicalap";
$nombres_centro[24]="Escuela Infantil Municipal Minipoli";
$nombres_centro[25]="Escuela Infantil Municipal La Creu";
$nombres_centro[26]="Escuela Infantil Municipal Albalat dels Sorells";

$links_centro[10]="santpau.escuelas-infantiles.com";
$links_centro[23]="gestion.escuelainfantilbenicalap.com";
$links_centro[24]="gestion.escuelainfantilminipoli.com";
$links_centro[25]="gestion.escuelainfantillacreu.com";
$links_centro[26]="gestion.escuelainfantilalbalat.com";

$busca_listado_centros=db_query("SELECT * FROM centros WHERE enviar_mensajes_padres=1 ORDER BY centro_propio DESC, nombre_centro");
$nombres_centros=array();
while ($listado_centros=db_fetch_array($busca_listado_centros)){
                          $nombres_centros[$listado_centros["id_centro"]]=$listado_centros["nombre_centro"];
                          }

$asunto_mensaje="Recordatorio de contraseña de acceso a la web de la ".$nombres_centro[$centro_seleccionado_email];
$texto_editor=utf8_decode($datos_mensaje["texto_editor"]);

$titulo_web=$nombres_centro[$centro_seleccionado_email];

$mail_webmaster=$email_centro[$centro_seleccionado_email];

$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_webmaster." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");

$msg_alternativo="<br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

$mensajes_enviados=0;
$mensajesFallados=0;
$direcciones_enviadas=array();
$direccionesNoEnviadas=array();
$direcciones_duplicadas=0;

echo "<font size=3><b>Listado de direcciones a las que se les ha enviado el mensaje:</b></font><br>";

// Inicio Mensaje
$centro_seleccionado_listado=$_GET["id_centro"];
$id_padre=$_GET["id_padre"];
$id_alumno=$_GET["id_alumno"];

    $busca_padre=db_query_params('SELECT padres.*,usuarios.*  FROM padres,usuarios WHERE padres.id_usuario=usuarios.id_usuario AND padres.id_padre=? AND padres.padre_de=?;', 'ss', array($id_padre, $id_alumno)) or die(db_error()."-75");

    while($datos_padre=db_fetch_array($busca_padre)) {

                            $nombre_pila_padre=$datos_padre["Nombre"]; 
                            $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                            $email_padre_act=$datos_padre["email"];
                            $usuario_padre_act=$datos_padre["usuario_webcam"];
                            $password_padre_act=security_family_password($datos_padre);                        

                            $to = $email_padre_act;

                            if (strlen($to)!=0) {
                            
                                // Comprueba si ya ha sido enviada antes
                            
                                if (array_search($to, $direcciones_enviadas)) {

                                    $direcciones_duplicadas++;

                                } else {                        

                                    $texto_editor="Hola $nombre_pila_padre,<br><br>Te enviamos este mensaje para recordarte que tu usuario y contraseña de acceso al área privada de padres de la web de la ".$nombres_centro[$centro_seleccionado_listado].", para acceder la información de tu hijo/a son:<br><br><b>Usuario: </b>".$usuario_padre_act."<br><b>Contraseña: </b>".$password_padre_act."<br><br>Para acceder al área privada de padres o descargar la APP Móvil, haz clic en el siguiente enlace: <a href='https://".$links_centro[$centro_seleccionado_email]."/app' target='_blank'>https://".$links_centro[$centro_seleccionado_email]."/app</a><br><br>Un saludo,<br><br>".$nombres_centro[$centro_seleccionado_listado]."<br><br>";

                                    $msg_normal="<img src='https://".$links_centro[$centro_seleccionado_email]."/imagen/logo_".$centro_seleccionado_email.".jpg'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

                                    if (enviarMensajeAlumnos ($to,$asunto_mensaje,$msg_normal,$nombre_padre_act,$centro_seleccionado_listado)) {
                                        $direcciones_enviadas[$mensajes_enviados]=$to;
                                        echo($to."<br>");
                                        $mensajes_enviados++;
                                    } else {
                                        $direccionesNoEnviadas[$mensajesFallados]=$to;
                                        $mensajesFallados++;        
                                    }
                            
                                } // Fin si ya ha sido enviada antes
                            } // Fin if email
                            $contador_padres++;
                        } // Fin if datos_padre                        

if ($mensajesFallados!=0) {
        foreach ($direccionesNoEnviadas as $noEnviado) {
                echo($noEnviado."<br>");
        }                
}

db_close();
?>
<input type="button" onclick="window.close()" value="Cerrar">
</div>
</body>
</html>
