<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require '../herramientas/PHPMailerAutoload.php';

function enviarMensajeAlumnos ($to,$asunto_mensaje,$msg_normal,$nombre_padre_act) {

    /* Para debugging
    echo("Mensaje enviado a: ".$to."<br><br>Mensaje enviado: <br><br>".$msg_normal."<br><br>");

    return true;
    */

        $asuntoMensaje=$asunto_mensaje;
        $cuerpo_mensaje=$msg_normal;
        $mailFrom = "circulares@escuelas-infantiles.com";
        $nombreRemitente="Escuelas Infantiles Lápices";
        $nombreDestinatario=$nombre_padre_act;

        $mail = new PHPMailer;
$mail->CharSet = 'UTF-8';

        // $mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                    // Set mailer to use SMTP
        $mail->Host = (getenv('LAPICES_SMTP_HOST') ?: '');  	    // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                             // Enable SMTP authentication
        $mail->Username = (getenv('LAPICES_SMTP_USER') ?: '');   		    // SMTP username
        $mail->Password = (getenv('LAPICES_SMTP_PASSWORD_03') ?: '');               // SMTP password
        $mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to
        // $mail->Port = 25;

        $mail->From = $mailFrom;
        $mail->FromName = $nombreRemitente;
        $mail->addAddress($to, $nombreDestinatario);
        $mail->addReplyTo($mailFrom, $nombreRemitente);
        $mail->addCustomHeader("List-Unsubscribe",'<lapices@escuelas-infantiles.com>');
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

$email_centro[8]="boletines@escuelas-infantiles.com";
$email_centro[6]="boletines@escuelas-infantiles.com";
$email_centro[7]="boletines@escuelas-infantiles.com";
$email_centro[13]="boletines@escuelas-infantiles.com";

$nombres_centro[8]="Escuela Infantil Lápices Río Nervión";
$nombres_centro[6]="Escuela Infantil Lápices General Avilés";
$nombres_centro[7]="Escuela Infantil Lápices Vicente Branchat";
$nombres_centro[13]="Escuela Infantil Lápices Gran Vía";

$links_centro[8]="www.escuelasinfantileslapices.es";
$links_centro[6]="www.escuelasinfantileslapices.es";
$links_centro[7]="www.escuelasinfantileslapices.es";
$links_centro[13]="www.escuelasinfantileslapices.es";

$busca_listado_centros=db_query("SELECT * FROM centros WHERE enviar_mensajes_padres=1 ORDER BY centro_propio DESC, nombre_centro");
$nombres_centros=array();
while ($listado_centros=db_fetch_array($busca_listado_centros)){
                          $nombres_centros[$listado_centros["id_centro"]]=$listado_centros["nombre_centro"];
                          }

$asunto_mensaje="Recordatorio de contraseña de acceso a la web de la ".$nombres_centros[$centro_seleccionado_email];
$texto_editor=app_text_utf8($datos_mensaje["texto_editor"]);

$id_curso=$_GET["id_curso"];

if ($id_curso) {
	
	// Busca datos curso
	$busca_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso");

	$datos_cursos=db_fetch_array($busca_cursos);
	
	$fecha_fin_curso=$datos_cursos["fecha_fin"];	
	
}

$aula_seleccionada_listado=$_GET["id_aula"];

if (strlen($aula_seleccionada_listado)!=0) {
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$aula_seleccionada_listado;") or die(db_error());
        $datos_aula=db_fetch_array($consulta_datos_aula);
        $array_centros=array($datos_aula["id_centro"]);
        } else {
        $array_centros=array($centro_seleccionado_email);
        }

$titulo_web="Escuela infantil lápices";

$mail_webmaster="bolestines@escuelas-infantiles.com";

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

// Inicio alumnos

for ($lista_centros=0;$lista_centros<count($array_centros);$lista_centros++) {

    $filtro_mensajes="";

    $centro_seleccionado_listado=$array_centros[$lista_centros];

    echo "<br><b>Alumnos de ".$nombres_centros[$centro_seleccionado_listado]."</b><br><br>";

    if (strlen($centro_seleccionado_listado)!=0) {
            $filtro_mensajes=" AND alumnos.id_centro=$centro_seleccionado_listado AND alumnos.id_curso=$id_curso";
            }

    if (strlen($aula_seleccionada_listado)!=0) {
            $filtro_mensajes=" AND alumnos.aula=$aula_seleccionada_listado AND alumnos.id_curso=$id_curso";
            }
    if ($solo_activos!=0) {
            $filtro_mensajes.=" AND alumnos.fecha_baja>='".date("Y-m-d")."' AND alumnos.fecha_matricula<='".date("Y-m-d")."'";
            }

    $busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario$filtro_mensajes ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");

    while($alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos)) {

                    // Busca provincia
                    $id_provincia_sel=$alumnos_inscritos["Provincia"];
                    if (strlen($id_provincia_sel)==0) {
                            $id_provincia_sel=0;
                            }
                    $busca_provincia=db_query("SELECT * FROM provincias WHERE id_provincia=".$id_provincia_sel);
                    if (db_num_rows($busca_provincia)!=0) {
                            $datos_provincia=db_fetch_array($busca_provincia);
                            $nombre_provincia=$datos_provincia["provincia"];
                            } else {
                            $nombre_provincia="";
                            }

                    $consulta_nombre_centro=db_query("SELECT * FROM centros WHERE id_centro=".$alumnos_inscritos["id_centro_ins"]) or die(db_error()."-2");
                    $datos_centro=db_fetch_array($consulta_nombre_centro);

                    // Busca datos del aula
                    $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$alumnos_inscritos["aula"]);
                    $datos_aula=db_fetch_array($consulta_datos_aula);

                    $consulta_relaciones=db_query("SELECT * FROM padres WHERE padre_de=".$alumnos_inscritos["id_usuario"]." ORDER BY parentesco") or die(db_error());

                    $contador_padres=0;
                    while($relaciones=db_fetch_array($consulta_relaciones)) {
                        $consulta_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=".$relaciones["id_usuario"]);

                        // Borra variables
                        $nombre_padre_act="";
                        $email_padre_act="";
                        $msg_normal="";
                        $usuario_padre_act="";
                        $password_padre_act="";

                        if (db_num_rows($consulta_datos_padre)!=0) {
                            $datos_padre=db_fetch_array($consulta_datos_padre);
                            $nombre_pila_padre=$datos_padre["Nombre"]; 
                            $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                            $email_padre_act=$datos_padre["email"];
                            $usuario_padre_act=$relaciones["usuario_webcam"];
                            $password_padre_act=$relaciones["password_webcam"];                        

                            $to = $email_padre_act;

                            if (strlen($to)!=0) {
                            
                                // Comprueba si ya ha sido enviada antes
                            
                                if (array_search($to, $direcciones_enviadas)) {

                                    $direcciones_duplicadas++;

                                } else {
                            
                                    $hijo_hija="hijo ";
                                    if ($alumnos_inscritos["sexo"]=="H") {
                                        $hijo_hija="hija ";
                                    }
                                    $texto_editor="Hola $nombre_pila_padre,<br><br>Te enviamos este mensaje para recordarte que tu usuario y contraseña de acceso al área privada de padres de la web de la ".$nombres_centros[$centro_seleccionado_listado].", para acceder la información de tu ".$hijo_hija.$alumnos_inscritos['Nombre'].",son:<br><br><b>Usuario: </b>".$usuario_padre_act."<br><b>Contraseña: </b>".$password_padre_act."<br><br>Un saludo,<br><br>".$nombres_centros[$centro_seleccionado_listado]."<br><br>";

                                    $msg_normal="<img src='https://www.escuelasinfantileslapices.es/imagen/encabezado_mensaje.gif'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

                                    if (enviarMensajeAlumnos ($to,$asunto_mensaje,$msg_normal,$nombre_padre_act)) {
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
                    } // Fin while relaciones

    }

}

if ($mensajesFallados!=0) {
        foreach ($direccionesNoEnviadas as $noEnviado) {
                echo($noEnviado."<br>");
        }                
}

// mysql_query("DELETE FROM mensajes_temporal WHERE id_mensaje=$id_mensaje;");

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
<?php
/*
Se han enviado <?=$mensajes_enviados?> mensajes.<br><br>
Se han omitido <?=$direcciones_duplicadas?> direcciones por estar duplicadas.<br><br>
*/
?>
<input type="button" onclick="window.close()" value="Cerrar">
</div>
</body>
</html>
