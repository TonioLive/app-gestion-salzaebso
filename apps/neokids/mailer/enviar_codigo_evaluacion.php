<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require 'PHPMailerAutoload.php'; 

$resultados="";

$lista_alumnos=$_GET["lista_alumnos"]; 

$lista_alumnos_array=explode(",",$lista_alumnos);

$filtro_alumnos="";
foreach ($lista_alumnos_array as $valor) {
        $filtro_alumnos.="alumnos.id_alumno=".$valor." OR ";               
        }
if (strlen($filtro_alumnos)!=0) {
        $filtro_alumnos="(".substr($filtro_alumnos,0,strlen($filtro_alumnos)-4).")";        
        }

$busca_datos_alumno=db_query("SELECT * FROM alumnos LEFT JOIN usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE ".$filtro_alumnos.";") or die(db_error()."-2");

$contador_alumnos=0;
$total_alumno=db_num_rows($busca_datos_alumno);

if (db_num_rows($busca_datos_alumno)!=0) {

        while($listado_alumnos=db_fetch_array($busca_datos_alumno)){

                // Comprueba si el alumno ha sido evaluado en el periodo/trimestre actual
        $busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND periodo=$periodo AND (estado_p1+estado_p2+estado_p3+estado_p4)<>0;");       
        
        $evaluado="<span class='text-danger'>No</span>";
        $uid="";
        if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {       
        
                $datos_evaluacion=db_fetch_array($busca_estado_evaluacion_guardada);
                        
                // Genera UID
                        
                $digitos_id_alumno=strlen($listado_alumnos["id_alumno"]);
                $digitos_id_evaluacion=strlen($datos_evaluacion["id_estado"]);
                        
                $ale1=rand(10000, 99999);
                $ale2=rand(10000, 99999);
                        
                $parte1=substr($ale1,0,(5-$digitos_id_evaluacion)).$datos_evaluacion["id_estado"];
                $parte2=substr($ale2,0,(5-$digitos_id_alumno)).$listado_alumnos["id_alumno"];
                        
                $parte3="00".$datos_evaluacion["id_curso"];
                $parte3=substr($parte3,strlen($parte3)-2,2); 
                        
                $uid=$parte1.$parte2.$parte3.$digitos_id_alumno.$digitos_id_evaluacion; 

                        // Array con todos los emails del alumno

                        $listado_emails=str_replace(" ","",$listado_alumnos["email"]);

                        $array_to = explode(",",$listado_emails);
        
                        if (count($array_to)==0) {
                                $resultados.="- <span class='text-danger'>Error: ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." no tiene ningún email en su ficha de usuario. Debes hacerle llegar la evaluación por otros medios: Impresa, comunicándole el código de acceso ($uid) personalmente cuando venga al centro, etc.</span><br />";        
                        }
        
                        foreach ($array_to as $to) {        
        
                                if (comprobar_email($to)!=0) {
                                
                                        // Comprueba si está en la lista negra
                                        $busca_rechazados=db_query_params('SELECT * FROM emails_rechazados WHERE email=?', 's', array((string)($to)));
                                        if ((db_num_rows($busca_rechazados)!=0) or (1==1)) { // Anulado

                                        // comprueba si no desea recibir email
                                        $busca_no_recibir=db_query_params('SELECT * FROM no_recibir_mail WHERE email=?', 's', array((string)($to)));
                                        if (db_num_rows($busca_no_recibir)!=0) {
                                        
                                                // No desea recibir emails
                                                $resultados.="- <span class='text-danger'>Error: Los padres de ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." no desean recibir emails en su cuenta ($to). Si es su única dirección de email, debes hacerle llegar la evaluación por otros medios: Impresa, comunicándole el código de acceso ($uid) personalmente cuando venga al centro, etc.</span><br />";
                                        
                                        } else {

                                        $fecha_actual=strtotime(date("Y-m-d"));

                                        $mail_webmaster="lapiscinadeneokids@gmail.com";

                                        // Envia email

                                        $genero="o";
                                        if ($listado_alumnos["sexo"]=="H") {
                                                $genero="a";
                                        }

                                        $nombreRemitente="La Piscina de Neokids";
                                        $mailFrom=$mail_webmaster;
                                        $asuntoMensaje="Ya puedes acceder a la evaluación de tu hij".$genero;
                                        $MSGhtml_type="text/html";
                                        $MSGCharSet="UTF-8";

                                        $cuerpo_mensaje="Familia,<br><br>Una vez más os agradecemos la confianza depositada en <b>Neokids</b>. ".$listado_alumnos["Nombre"]." aprende a nadar, semana a semana, a través del juego. Más allá de lo que veis desde la Funny Room, su evolución es seguida y evaluada, de una manera continua por nuestros monitores. <b>La piscina de Neokids</b> quiere haceros partícipe de esa evaluación. Por eso, al finalizar cada período recibiréis por correo electrónico su evaluación, como la que os adjuntamos a continuación. En ella podréis comprobar los objetivos alcanzados  por ".$listado_alumnos["Nombre"]." acordes a su nivel.<br><br>";

                                        $cuerpo_mensaje.="Para descargarte el documento PDF con la evaluación haz click en el siguiente enlace:<br><br><a href='http://gestion.neo-kids.es/pdf/evaluacion_piscina.php?uid=".$uid."'>http://gestion.neo-kids.es/pdf/evaluacion_piscina.php?uid=".$uid."</a><br><br>Si desde tu cliente de correo electrónico no funciona, copia y pega el enlace completo en la barra de tu navegador.<br><br>Si aun así continuas sin poder ver la evaluación puedes acceder a la siguiente página e introducir el código que te indicamos a continuación.<br><br><b>Página de acceso a la evaluación:</b> <a href='http://gestion.neo-kids.es/evaluacion.php'>http://gestion.neo-kids.es/evaluacion.php</a><br><br><b>Código:</b> ".$uid."<br><br>Este código es único y se ha generado de forma aleatoria para la evaluación de ".$listado_alumnos["Nombre"].".<br><br>Gracias por vuestro tiempo y por confiarnos lo más preciado.<br><br>La piscina de Neokids<br><br>";

                                        $texto_legal="De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje. Si no desea recibir más información por correo electrónico acceda a esta dirección <a href='http://gestion.neo-kids.es/mailer/unsubscribe_alumno1.php?cuenta=".$to."'>http://gestion.neo-kids.es/mailer/unsubscribe_alumno.php</a>";

                                        $cuerpo_mensaje=$cuerpo_mensaje."<br><font size=1 face='Helvetica'>".$texto_legal."</font>";

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
                                        $mail->addAddress($to, $datos_contacto["persona_contacto"]);
                                        $mail->addReplyTo($mailFrom, $nombreRemitente);
                                        $mail->addCustomHeader("List-Unsubscribe",'<lapiscinadeneokids@gmail.com>, <http://gestion.neo-kids.es/mailer/unsubscribe_alumno1.php?cuenta='.$to.'>');
                                        $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
                                        $mail->isHTML(true);                                  // Set email format to HTML

                                        $mail->Subject = $asuntoMensaje;
                                        $mail->Body    = $cuerpo_mensaje;

                                        if(!$mail->send()) {
                                                $mensajesFallados++;
                                                $resultados.="- <span class='text-danger'>Error: Ha fallado el envío de la evaluación de ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." a la dirección de email: ".$to."</span><br />";
                                                } else {
                                            $mensajesEnviados++;
                                            $resultados.="- Evaluación de ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." enviada correctamente a la dirección de email: ".$to."<br />";
                                                }

                                        }
                                
                                                                                } else {
                                                                                
                                                                                // El email había sido rechazado anteriormente
                
                                        $resultados.="- <span class='text-danger'>Error: ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." tiene un email incorrecto ($to) en su ficha de usuario. Esta dirección ha sido rechazada anteriormente y no volveremos a enviarle mensajes. Si es su única dirección de email, debes hacerle llegar la evaluación por otros medios: Impresa, comunicándole el código de acceso ($uid) personalmente cuando venga al centro, etc.</span><br />";
                                                                                
                                                                                }
                                                                                
                                        } else {

                                        // Este alumno no tiene email válido
                
                                        $resultados.="- <span class='text-danger'>Error: ".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]." tiene un email incorrecto ($to) en su ficha de usuario. Si es su única dirección de email, debes hacerle llegar la evaluación por otros medios: Impresa, comunicándole el código de acceso ($uid) personalmente cuando venga al centro, etc.</span><br />";

                                }
                        }
        
                }

        }

}
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>La piscina de Neokids</title>

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
                  <div class="panel-heading"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" class="pull-right"><br /><br /><br /><h3>La Piscina de Neokids</h3></div>
                  <div class="panel-body">
            <?=$resultados?>
                </div>
        </div>    
</div>
</body>
</html>
