<?php
require("../configuracion/config.php"); 
require("../mailer/PHPMailerAutoload.php");

$privatekey = (getenv('LAPICES_RECAPTCHA_SECRET') ?: '');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'secret' => $privatekey,
    'response' => $_POST['g-recaptcha-response'],
    'remoteip' => $_SERVER['REMOTE_ADDR']
]);

$resp = json_decode(curl_exec($ch));
curl_close($ch);

if ($resp->success) {
    // Success
} else {
    // failure
	exit("Error");
}

$id_centro=$_POST["id_centro"];
$id_curso=$_POST["id_curso_contacto"];
$id_contacto=$_POST["id_contacto"];

$id_usuario_alumno=$_POST["id_usuario_alumno"];
$id_usuario_padre1=$_POST["id_usuario_padre1"];
$id_usuario_padre2=$_POST["id_usuario_padre2"];

$como_conocio_texto=$_POST["como_conocio_texto"];

$horario=$_POST["horario"];
$mensualidad=$_POST["mensualidad"];
$comida=$_POST["comida"];
$merienda=$_POST["merienda"];
$horas_extra=$_POST["horas_extra"];
$numero_horas_extra=$_POST["numero_horas_extra"];	

$array_ev_semanas=$_POST["ev_semanas"];


$valores_alergias=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

if ($array_ev_semanas) {
		
		$ev_semanas_1=array();
		
		foreach($array_ev_semanas as $key => $value) {
				$ev_semanas_1[$value]=1;
		}
		
		$ev_semanas=serialize($ev_semanas_1);
		
}

$Nombre=db_real_escape_string($_POST["nombre"]);
$Apellido1=db_real_escape_string($_POST["apellido1"]);
$Apellido2=db_real_escape_string($_POST["apellido2"]);
$sexo=$_POST["sexo"];
$sip=db_real_escape_string($_POST["sip"]);
$fecha_nacimiento=$_POST["fecha_nacimiento"];

if (strlen($fecha_nacimiento)!=0) {

	$fecha_array=explode("/",$fecha_nacimiento);
	
	$fecha_nacimiento_dia=intval($fecha_array[0]);
	$fecha_nacimiento_mes=intval($fecha_array[1]);
	$fecha_nacimiento_ano=$fecha_array[2];
	if (!checkdate(intval($fecha_nacimiento_mes),intval($fecha_nacimiento_dia),intval($fecha_nacimiento_ano))) {
		$fecha_nacimiento_dia="";
		$fecha_nacimiento_mes="";
		$fecha_nacimiento_ano="";
		}
	if ($fecha_nacimiento_dia!="--" and $fecha_nacimiento_mes!="--" and $fecha_nacimiento_ano!="----") {
		$fecha_nacimiento=$fecha_nacimiento_ano."/".$fecha_nacimiento_mes."/".$fecha_nacimiento_dia;
		if (!checkdate(intval($fecha_nacimiento_mes),intval($fecha_nacimiento_dia),intval($fecha_nacimiento_ano))) {
			$fecha_nacimiento="";
		}
	} else {
		$fecha_nacimiento="";
	}
                        
} 
$nonato=$_POST["nonato"];
            
$nombre_c1=db_real_escape_string($_POST["nombre_c1"]);
$apellido1_c1=db_real_escape_string($_POST["apellido1_c1"]);
$apellido2_c1=db_real_escape_string($_POST["apellido2_c1"]);
$dni_padre1=db_real_escape_string($_POST["dni_padre1"]);
$telefono_padre1=db_real_escape_string($_POST["telefono_padre1"]);
$telefono_t1=db_real_escape_string($_POST["telefono_t1"]);
$email_padre1=db_real_escape_string($_POST["email_padre1"]);
$parentesco_padre1=$_POST["parentesco_padre1"];

$nombre_c2=db_real_escape_string($_POST["nombre_c2"]);
$apellido1_c2=db_real_escape_string($_POST["apellido1_c2"]);
$apellido2_c2=db_real_escape_string($_POST["apellido2_c2"]);
$dni_padre2=db_real_escape_string($_POST["dni_padre2"]);
$telefono_padre2=db_real_escape_string($_POST["telefono_padre2"]);
$telefono_t2=db_real_escape_string($_POST["telefono_t2"]);
$email_padre2=db_real_escape_string($_POST["email_padre2"]);
$parentesco_padre2=$_POST["parentesco_padre2"];

$calle=db_real_escape_string($_POST["calle"]);
$codigo_postal=db_real_escape_string($_POST["codigo_postal"]);
$poblacion=db_real_escape_string($_POST["poblacion"]);

$no_publicidad=$_POST["no_publicidad"];
$comentarios=db_real_escape_string($_POST["comentarios"]);

$consentimiento_publicidad=$_POST['consentimiento_publicidad'];
$consentimiento_web=$_POST['consentimiento_web'];
$consentimiento_blog=$_POST['consentimiento_blog'];
$consentimiento_redes=$_POST['consentimiento_redes'];
$informado_tratamiento_datos=$_POST['informado_tratamiento_datos'];

$forma_de_pago=$_POST['forma_de_pago'];
$IBAN=$_POST['IBAN'];

$piscina=$_POST['piscina'];
$grupo_piscina=$_POST['grupo_piscina'];

$queryAlergiasUpdate="";
$queryAlergiasInsert1="";
$queryAlergiasInsert2="";

print_r($POST);

foreach($array_alergias as $key => $alergia) {
		
	$valores_alergias[$alergia]=$_POST[$alergia];
	$queryAlergiasUpdate.=$alergia."='".$_POST[$alergia]."',";	
	$queryAlergiasInsert1.=$alergia.",";
	$queryAlergiasInsert2.="'".$_POST[$alergia]."',";
}

if (strlen($id_contacto)!=0) {

// Modifica contacto
	db_query("UPDATE contactos_web SET 
	como_conocio_texto='$como_conocio_texto',
	id_centro='$id_centro',
	id_curso='$id_curso',
	horario='$horario',
	mensualidad='$mensualidad',
	comida='$comida',
	merienda='$merienda',
	horas_extra='$horas_extra',
	numero_horas_extra='$numero_horas_extra',
	id_usuario_alumno='$id_usuario_alumno',	
	Nombre='$Nombre',
	Apellido1='$Apellido1',
	Apellido2='$Apellido2',
	sexo='$sexo',
	fecha_nacimiento='$fecha_nacimiento',
	SIP='$sip',
	id_usuario_padre1='$id_usuario_padre1',
	nombre_c1='$nombre_c1',
	apellido1_c1='$apellido1_c1',
	apellido2_c1='$apellido2_c1',
	dni_padre1='$dni_padre1',
	telefono_padre1='$telefono_padre1',
	telefono_t1='$telefono_t1',
	email_padre1='$email_padre1',
	parentesco_padre1='$parentesco_padre1',
	id_usuario_padre2='$id_usuario_padre2',
	nombre_c2='$nombre_c2',
	apellido1_c2='$apellido1_c2',
	apellido2_c2='$apellido2_c2',
	dni_padre2='$dni_padre2',
	telefono_padre2='$telefono_padre2',
	telefono_t2='$telefono_t2',
	email_padre2='$email_padre2',
	parentesco_padre2='$parentesco_padre2',
	calle='$calle',
	codigo_postal='$codigo_postal',
	poblacion='$poblacion',
	no_publicidad='$no_publicidad',
	comentarios='$comentarios',
	consentimiento_publicidad='$consentimiento_publicidad',
	consentimiento_web='$consentimiento_web',
	consentimiento_blog='$consentimiento_blog',
	consentimiento_redes='$consentimiento_redes',
	informado_tratamiento_datos='$informado_tratamiento_datos',
	ev_semanas='$ev_semanas',
	$queryAlergiasUpdate
	forma_de_pago='$forma_de_pago',
	IBAN='$IBAN',
	piscina='$piscina',
	grupo_piscina='$grupo_piscina'
	WHERE id_contacto=$id_contacto") or die(db_error());	


} else {
	// Introduce nuevo contacto
	db_query("INSERT INTO contactos_web (
	como_conocio_texto,
	id_centro,
	id_curso,
	horario,
	mensualidad,
	comida,
	merienda,
	horas_extra,
	numero_horas_extra,
	id_usuario_alumno,
	Nombre,
	Apellido1,
	Apellido2,
	sexo,
	fecha_nacimiento,
	SIP,
	id_usuario_padre1,
	nombre_c1,
	apellido1_c1,
	apellido2_c1,
	dni_padre1,
	telefono_padre1,
	telefono_t1,
	email_padre1,
	parentesco_padre1,
	id_usuario_padre2,
	nombre_c2,
	apellido1_c2,
	apellido2_c2,
	dni_padre2,
	telefono_padre2,
	telefono_t2,
	email_padre2,
	parentesco_padre2,
	calle,
	codigo_postal,
	poblacion,
	no_publicidad,
	comentarios,
	fecha_contacto,	
	consentimiento_publicidad,
	consentimiento_web,
	consentimiento_blog,
	consentimiento_redes,
	informado_tratamiento_datos,
	ev_semanas,
	$queryAlergiasInsert1
	IBAN,
	forma_de_pago,
	piscina,
	grupo_piscina
	) VALUES (
	'$como_conocio_texto',
	'$id_centro',
	'$id_curso',
	'$horario',
	'$mensualidad',
	'$comida',
	'$merienda',
	'$horas_extra',
	'$numero_horas_extra',
	'$id_usuario_alumno',	
	'$Nombre',
	'$Apellido1',
	'$Apellido2',
	'$sexo',
	'$fecha_nacimiento',
	'$sip',
	'$id_usuario_padre1',
	'$nombre_c1',
	'$apellido1_c1',
	'$apellido2_c1',
	'$dni_padre1',
	'$telefono_padre1',
	'$telefono_t1',
	'$email_padre1',
	'$parentesco_padre1',
	'$id_usuario_padre2',
	'$nombre_c2',
	'$apellido1_c2',
	'$apellido2_c2',
	'$dni_padre2',
	'$telefono_padre2',
	'$telefono_t2',
	'$email_padre2',
	'$parentesco_padre2',
	'$calle',
	'$codigo_postal',
	'$poblacion',
	'$no_publicidad',
	'$comentarios',
	'".date("Y-m-d H:i:s")."',
	'$consentimiento_publicidad',
	'$consentimiento_web',
	'$consentimiento_blog',
	'$consentimiento_redes',
	'$informado_tratamiento_datos',
	'$ev_semanas',
	$queryAlergiasInsert2
	'$IBAN',
	'$forma_de_pago',
	'$piscina',
	'$grupo_piscina'
	);") or die(db_error());	

	$enviarEmail=false;
	if ((strlen($email_padre1)!=0) or (strlen($email_padre2)!=0)) {
		$enviarEmail=true;
		$to=$email_padre1;
		if (strlen($email_padre1)==0) {
			$to=$email_padre2;
		}
	}

	// Prueba, solo se me envia a mi
	// if ($email_padre1!="luis@doassans.es") {
	// 	$enviarEmail=false;
	// }

	if ($enviarEmail==true) {

		$emails_envio=array();
		$emails_envio[6]="enviosgeneralaviles@escuelas-infantiles.com";
		$emails_envio[7]="enviosvicentebranchat@escuelas-infantiles.com";
		$emails_envio[8]="enviosrionervion@escuelas-infantiles.com";
		$emails_envio[13]="enviosgranvia@escuelas-infantiles.com";

		$emails_respuesta=array();
		$emails_respuesta[6]="generalaviles@escuelas-infantiles.com";
		$emails_respuesta[7]="vicentebranchat@escuelas-infantiles.com";
		$emails_respuesta[8]="rionervion@escuelas-infantiles.com";
		$emails_respuesta[13]="granvia@escuelas-infantiles.com";
		
        $mail_webmaster=$emails_envio[$id_centro];

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

		$asunto="Confirmación de solicitud de inscripción en la Escuela de Verano de Lápices";

        $nombreRemitente="Escuelas Infantiles Lápices";
        $mailFrom=$mail_webmaster;
        $asuntoMensaje=ucwords($asunto);
        $MSGhtml_type="text/html";
        $MSGCharSet="UTF-8";

        $cuerpo_mensaje = "<p>Hola,</p><p>Hemos recibido tu solicitud de inscripción en la Escuela de Verano de Lápices.</p><p>Estamos en contacto.</p><p>Un saludo,</p><p>Escuelas Infantíles Lápices</p><p><font color='#ff0000'>Por favor, no respondas a este mensaje, ha sido enviado automáticamente. Si quieres ponerte en contacto con nosotros puedes enviarnos un email a la dirección <a href='mailto:".$emails_respuesta[$id_centro]."'>".$emails_respuesta[$id_centro].".</font></p>";

        $texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$emails_respuesta[$id_centro]." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.<br><br>Si no desea recibir m&aacute;s informaci&oacute;n por correo electr&oacute;nico acceda a esta dirección https://www.escuelasinfantileslapices.es/mailer/unsubscribe_alumno.php");

        $cuerpo_mensaje=$cuerpo_mensaje."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

		$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

        // $mail->SMTPDebug = 3;                            // Enable verbose debug output

        $mail->isSMTP();                                    // Set mailer to use SMTP
        $mail->Host = (getenv('LAPICES_SMTP_HOST') ?: '');                     // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                             // Enable SMTP authentication
        $mail->Username = $mailFrom;                        // SMTP username
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
			// echo("<span class='text-danger'>Error: El mensaje para ".$to." no ha podido ser enviado.</span><br />");
			$resultado="Error";
        } else {
            // echo("<span class='text-success'>OK: Mensaje para ".$to." enviado correctamente.</span><br />");
            $resultado="Enviado";
        }
	}

}
                       
        

header("Location: /formulario/exito.php?idc=$id_centro");
?>
