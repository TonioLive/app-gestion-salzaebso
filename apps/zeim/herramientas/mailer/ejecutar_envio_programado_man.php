<?php 
require("../configuracion/config_principal.php");
require 'PHPMailerAutoload.php';

$mail_webmaster="webmaster@cursos-online.info";

$fecha_actual=strtotime(date("Y-m-d"));

$busca_cursos=db_query("SELECT * FROM Config_cursos;") or die(db_error());

while($datos_cursos=db_fetch_array($busca_cursos)) {

$id_web_curso=$datos_cursos["id_web"];

$id_web_curso=str_replace("][",",",$id_web_curso);
$id_web_curso=str_replace("[","",$id_web_curso);
$id_web_curso=str_replace("]","",$id_web_curso);
$id_web_cursos=explode(",",$id_web_curso);

$consulta_web=db_query("SELECT * FROM plataforma_webs WHERE id_web=".$id_web_cursos[0]) or die(db_error());

if (db_num_rows($consulta_web)!=0) {
        $rsweb=db_fetch_array($consulta_web);

        $url_root_array=explode(" ",$rsweb["url"]);

        $url_root="http://".$url_root_array[0];
        
        } else {
        $url_root="http://www.cursos-online.info";
        }
        

$fecha_inicio_curso=strtotime($datos_cursos["Fecha_inicio_curso"]);

$dias_desde_inicio=ceil(($fecha_actual-$fecha_inicio_curso-3660)/86400);


// Borrar al terminar
// $dias_desde_inicio=1;

$id_curso=$datos_cursos["id_curso"];

// if ($dias_desde_inicio>=0) {

$busca_programacion=db_query("SELECT * FROM mensajes_programados WHERE enviado=0 AND hora_envio=0 AND dias_inicio<=$dias_desde_inicio AND id_curso=$id_curso;") or die(db_error());

while($programacion_actual=db_fetch_array($busca_programacion)) {

        $id_actual=$programacion_actual["id_mensaje"];

        $mensajesEnviados=0;
        $mensajesFallados=0;
        $cuentasDuplicadas=0;
        $nombreRemitente="Cursos Online Teleformación";
        $mailFrom=$mail_webmaster;
        $asuntoMensaje=$programacion_actual["asunto_mensaje"];
        $MSGhtml_type="text/html";
        $MSGCharSet="UTF-8";

        $cuerpo_mensaje = $programacion_actual["cuerpo_mensaje"];

$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.<br><br>Si no desea recibir m&aacute;s informaci&oacute;n por correo electr&oacute;nico acceda a esta dirección http://www.cursos-online.info/mailer/unsubscribe.php");

$cuerpo_mensaje=$cuerpo_mensaje."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

        // Registra fecha y hora de inicio de envío
        if ((strlen($programacion_actual["fecha_inicio_envio"])==0) or (is_null($programacion_actual["fecha_inicio_envio"])) or ($programacion_actual["fecha_inicio_envio"]=='0000-00-00')) {
                $intro_fecha_envio=db_query_params('UPDATE mensajes_programados SET fecha_inicio_envio=?, hora_inicio_envio=? WHERE id_mensaje=?', 'sss', array((string)(date("Y-m-d")), (string)(date("H:i:s")), $id_actual)) or die(db_error());
                }
                
        // Borra el registro de envío si es requerido

        $contador_intentos=0;

		if ($dias_desde_inicio>=0) {
        	$busca_cuentas=db_query_params('SELECT * FROM Usuarios, Inscripciones WHERE Usuarios.ID=Inscripciones.id_alumno AND Inscripciones.Tipo=\'1\' AND Inscripciones.Matriculado=true AND Inscripciones.Borrado=0 AND Inscripciones.id_curso=? AND no_newsletter=0 ORDER BY Inscripciones.Matriculado,Usuarios.Apellido1', 's', array($id_curso)) or die(db_error());

			} else {
			$busca_cuentas=db_query_params('SELECT * FROM Usuarios, Inscripciones WHERE Usuarios.ID=Inscripciones.id_alumno AND Inscripciones.Tipo=\'1\' AND Inscripciones.Borrado=0 AND Inscripciones.id_curso=? AND no_newsletter=0 ORDER BY Inscripciones.Matriculado,Usuarios.Apellido1', 's', array($id_curso)) or die(db_error());	
			}

        while($datos_cuentas=db_fetch_array($busca_cuentas)){
        
        $buscar=$datos_cuentas["Email"];
        
        // Comprueba si se le ha enviado
        $busca_registro_envios=db_query_params('SELECT * FROM registro_envio_mensajes WHERE id_mensaje=? AND cuenta=? AND resultado=1', 'ss', array($id_actual, (string)($buscar))) or die(db_error());

        if (db_num_rows($busca_registro_envios)==0) {
        
        $to = urldecode($datos_cuentas["Email"]);
        
/*        
        // Borrar al terminar
        // $to="luis@doassans.es";

        $from = "'".ucfirst($nombreRemitente)."' <".$mailFrom.">";

                $headers = "From: '" . $nombreRemitente . "' <" . $mailFrom . ">\n";
                $headers .= "Return-Path: '" . $nombreRemitente . "' <" . $mailFrom . ">\n";
                $headers .= "Reply-To: '" . $nombreRemitente . "' <" . $mail_webmaster . "\n";
                $headers .= "X-Mailer:PHP" . phpversion() . "\n";
                $headers .= "Precedence: list\nList-Id: " . $nombreRemitente . "\n";

                                $sub = UTF8_encode($asuntoMensaje);

                                $msg = UTF8_encode($cuerpo_mensaje);

                                $headers .= "MIME-Version: 1.0\n";
                                $headers .= "Content-Type: " . $MSGhtml_type . "; charset=\"". $MSGCharSet . "\"\n";
                                $mailtext = "<html><head><title>" . $sub . "</title></head><body>" . $msg . "</body></html>";
                                 
                if(mail($to, $asuntoMensaje, $msg,$headers)) {
                        $mensajesEnviados++;
                        // Introduce en registro de envios
                        $intro_registro=mysql_query("INSERT INTO registro_envio_mensajes (cuenta,fecha,hora,id_mensaje,resultado) VALUES ('$buscar','".date("Y-m-d")."','".date("H:i:s")."','$id_actual','1');") or die(mysql_error());

                } else {
                        $mensajesFallados++;
                        // Introduce en registro de errores
                        $intro_registro=mysql_query("INSERT INTO registro_envio_mensajes (cuenta,fecha,hora,id_mensaje,resultado) VALUES ('$buscar','".date("Y-m-d")."','".date("H:i:s")."','$id_actual','2');") or die(mysql_error());
                }
*/

// Nuevo metodo               

$mail = new PHPMailer;
		               
// $mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mail.cursos-online.info';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = (getenv('ZEIM_SMTP_USER_03') ?: '');                 // SMTP username
$mail->Password = (getenv('ZEIM_SMTP_PASSWORD_03') ?: '');                           // SMTP password
// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 25;                                    // TCP port to connect to

$mail->From = $mailFrom;
$mail->FromName = $nombreRemitente;
$mail->addAddress($to, $datos_cuentas["Nombre"]." ".$datos_cuentas["Apellido1"]." ".$datos_cuentas["Apellido2"]);
$mail->addReplyTo($mailFrom, $nombreRemitente);
$mail->addCustomHeader("List-Unsubscribe",'<info@cursos-online.info>, <http://www.cursos-online.info/mailer/unsubscribe1.php?cuenta='.$to.'>');
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $asuntoMensaje;
$mail->Body    = $cuerpo_mensaje;

if(!$mail->send()) {
	$mensajesFallados++;
    // Introduce en registro de errores
    $intro_registro=db_query_params('INSERT INTO registro_envio_mensajes (cuenta,fecha,hora,id_mensaje,resultado) VALUES (?,?,?,?,\'2\');', 'ssss', array((string)($buscar), (string)(date("Y-m-d")), (string)(date("H:i:s")), (string)($id_actual))) or die(db_error());
} else {
    $mensajesEnviados++;
    // Introduce en registro de envios
    $intro_registro=db_query_params('INSERT INTO registro_envio_mensajes (cuenta,fecha,hora,id_mensaje,resultado) VALUES (?,?,?,?,\'1\');', 'ssss', array((string)($buscar), (string)(date("Y-m-d")), (string)(date("H:i:s")), (string)($id_actual))) or die(db_error());
}

// Nuevo metodo
                
                
        $contador_intentos++;
        sleep(1);
        }

        }

        // Registra mensajes enviados y fallados
        $total_enviados=$mensajesEnviados;
        $total_fallados=$mensajesFallados;

        $intro_registro_envio=db_query_params('UPDATE mensajes_programados SET fecha_fin_envio=?, hora_fin_envio=?, enviado=1, enviados=?, fallados=? WHERE id_mensaje=?', 'sssss', array((string)(date("Y-m-d")), (string)(date("H:i:s")), (string)($total_enviados), (string)($total_fallados), $id_actual)) or die(db_error());


                }


//        }
}

?>
