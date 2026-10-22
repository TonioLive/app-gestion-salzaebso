<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
require '../../../mailer/PHPMailerAutoload.php';

$listado_emails=$_POST["listado_emails"];
$asunto=$_POST["asunto"];
$mensaje=$_POST["mensaje"];
    
$lista_emails_destino=app_safe_unserialize($listado_emails);

$num_emails_enviar=count($lista_emails_destino);

$emails_envio=array();
$emails_envio[6]="enviosgeneralaviles@escuelas-infantiles.com";
$emails_envio[7]="enviosvicentebranchat@escuelas-infantiles.com";
$emails_envio[8]="enviosrionervion@escuelas-infantiles.com";
$emails_envio[13]="enviosgranvia@escuelas-infantiles.com";

        $mail_webmaster=$emails_envio[$centro_actual];

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

        $nombreRemitente="Escuelas Infantiles Lápices";
        $mailFrom=$mail_webmaster;
        $asuntoMensaje=ucwords($asunto);
        $MSGhtml_type="text/html";
        $MSGCharSet="UTF-8";

        $cuerpo_mensaje = $mensaje;

        $texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.<br><br>Si no desea recibir m&aacute;s informaci&oacute;n por correo electr&oacute;nico acceda a esta dirección https://www.escuelasinfantileslapices.es/mailer/unsubscribe_alumno.php");

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

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">

<div class="container-fluid">
        <div class="panel panel-default">
                <div class="panel-heading"><h5><i class="fa fa-envelope" aria-hidden="true"></i> Se han enviado <?=$num_emails_enviar?> mensajes de correo electrónico a los alumnos seleccionados.</h5></div>
                  <div class="panel-body">
                          <h5>Resultado:</h5>        
                        <?php 
                        
$registro_envio=array();
                        
foreach ($lista_emails_destino as $to) {

        $mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

        // $mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                    // Set mailer to use SMTP
        $mail->Host = (getenv('LAPICES_SMTP_HOST') ?: '');                                          // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                             // Enable SMTP authentication
        $mail->Username = $mailFrom;                                                   // SMTP username
        $mail->Password = (getenv('LAPICES_SMTP_PASSWORD_01') ?: '');                     // SMTP password
        $mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to

        $mail->From = $mailFrom;
        $mail->FromName = $nombreRemitente;
        $mail->addAddress($to, '');
        $mail->addReplyTo($mailFrom, $nombreRemitente);
        $mail->addCustomHeader("List-Unsubscribe",'<'.$mailFrom.'>, <https://www.escuelasinfantileslapices.es/mailer/unsubscribe_alumno1.php?cuenta='.$to.'>');
        $mail->WordWrap = 50;                               // Set word wrap to 50 characters
        $mail->isHTML(true);                                // Set email format to HTML

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
$inserta_registro=db_query("INSERT INTO registro_envio_emails (asunto,mensaje,registro_envio,enviado_por) VALUES ('".db_real_escape_string($asuntoMensaje)."','".db_real_escape_string($mensaje)."','".serialize($registro_envio)."','".$idUsuario."');");

                        ?>
                  </div>
        </div>

</div>

</body>
</html>
