<?php
    require("../configuracion/config.php");
    require("../configuracion/autentifica.php");
    require("../mailer/PHPMailerAutoload.php");


    $id_factura=123456;

    // Envía email confirmación
    $resultadoFirma="";
    $contadorMensajes=0;
    $direccionesEnviado = array();

    // $busca_padres=mysql_query("SELECT * FROM padres,usuarios WHERE padres.id_usuario=usuarios.id_usuario AND padres.padre_de=$id_usuario_cliente");
    // while($padres=mysql_fetch_array($busca_padres)){

        $direccionesEnvio = array('luis@doassans.es');

        foreach($direccionesEnvio as $to) {

        // $to = $padres["email"];
        // $usuarioPadre = $padres["usuario_webcam"];
        // $contraPadre = security_family_password($padres);
        $usuarioPadre = "XXXXXXXX";
        $contraPadre = "YYYYYYYY";

        if (strlen($to)!=0) {

            if (comprobar_email($to)) {

                $direccionesEnviado[] = $to;

                $titulo_web="La Piscina de Neokids";

                $mail_webmaster="noreply@neo-kids.es";
                $mail_contacto ="info@neo-kids.es";

                $type="html";
                $MSGhtml_type="text/html";
                $MSGCharSet="UTF-8";
                
                $asunto = "La piscina de Neokids - Recibo firmado ".date("d/m/Y");

                $cuerpo_mensaje = "<p>Hola,<br><br>Para acceder y descargar el recibo que acaba de firmar se le requerirá el siguiente nombre de usuario y contraseña:</p>";
                $cuerpo_mensaje.= "<p align='center'><b>Usuario:</b> ".$usuarioPadre."<br><b>Contraseña</b> ".$contraPadre."</p>";
                $cuerpo_mensaje.= "<p align='center'>Puede descargar la copia del recibo firmado haciendo clic en el siguiente enlace: <a href='https://gestion.neo-kids.es/clientes/login.php?redirect=/pdf/recibos_padres.php?id_factura=".$id_factura."'><b>Abrir Recibo</b></a></p>";
                $cuerpo_mensaje.= "<p>Estas credenciales servirán tambien para que puedas acceder al <a href='https://gestion.neo-kids.es/clientes'><b>Área Privada Padres</b></a> de nuestra web, donde podrás consultar toda la información sobre la actividad de la piscina: calendarios, recibos, evaluaciones, mensajes, etc.</p><p>Un saludo,<br><br>La Piscina de Neokids</p><p>ATENCIÓN: No responda a este mensaje. Es un mensaje automático enviado por el sistema. Para contactar con nosotros envíenos un mensaje a la dirección de email <a href='mailto:".$mail_contacto."'>".$mail_contacto."</a>";
        
                $texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_contacto." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");                
                
                $cuerpo_mensaje="<img src='https://gestion.neo-kids.es/pdf/logo_redondo.jpg' width='150'><br>".$cuerpo_mensaje."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
                

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
                
                $nombreRemitente=$titulo_web;                
                $mailFrom=$mail_webmaster;
                $asuntoMensaje=ucwords($asunto);
                $MSGhtml_type="text/html";
                $MSGCharSet="UTF-8";
        
                $mail = new PHPMailer;
        
                // $mail->SMTPDebug = 3;                            // Enable verbose debug output
        
                $mail->isSMTP();                                    // Set mailer to use SMTP
                $mail->Host = (getenv('NEOKIDS_SMTP_HOST') ?: '');                      // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                             // Enable SMTP authentication
                $mail->Username = $mailFrom;                        // SMTP username
                $mail->Password = (getenv('NEOKIDS_SMTP_PASSWORD') ?: '');                       // SMTP password
                $mail->SMTPSecure = 'ssl';                          // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 465;                                  // TCP port to connect to
        

                $mail->addReplyTo($mail_contacto, $titulo_web);  
                $mail->SetFrom($mail_webmaster, $titulo_web);                    
                // $mail->From = $mailFrom;
                // $mail->FromName = $nombreRemitente;
                $mail->addAddress($to, '');
                $mail->addReplyTo($mailFrom, $nombreRemitente);
                $mail->addCustomHeader("List-Unsubscribe",'<'.$mailFrom.'>, <https://gestion.neo-kids.es/mailer/unsubscribe_alumno1.php?cuenta='.$to.'>');
                $mail->WordWrap = 50;                               // Set word wrap to 50 characters
                $mail->isHTML(true);                                // Set email format to HTML
        
                $mail->Subject = $asuntoMensaje;
                $mail->Body    = $cuerpo_mensaje;        
        
                if(!$mail->send()) {
                    // echo("<span class='text-danger'>Error: El mensaje para ".$to." no ha podido ser enviado.</span><br />");
                    $resultado="Error";
                } else {
                    // echo("<span class='text-success'>OK: Mensaje para ".$to." enviado correctamente.</span><br />");
                    $resultado="Enviado";
                    $contadorMensajes++;
                }
                
            }

        }
    } 

    if ($contadorMensajes==0) {
        $resultadoFirma=" Error al enviar el recibo por correo electrónico, no tenemos ninguna dirección de email.";    
    } else {
        $resultadoFirma=" Recibo enviado por correo electrónico a las direcciones: ".implode(',',$direccionesEnviado);    
    }

    echo($resultadoFirma);
?>