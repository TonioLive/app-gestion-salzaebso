<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require '../herramientas/PHPMailerAutoload.php';

$id_mensaje=$_GET["id"];
$mensajesFallados=0;

$busca_mensaje=db_query_params('SELECT * FROM mensajes_temporal WHERE id_mensaje=?;', 's', array($id_mensaje));

$datos_mensaje=db_fetch_array($busca_mensaje);

$centro_seleccionado_email=$datos_mensaje["id_centro"];

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

$busca_listado_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro");
$nombres_centros=array();
while ($listado_centros=db_fetch_array($busca_listado_centros)){
                          $nombres_centros[$listado_centros["id_centro"]]=$listado_centros["nombre_centro"];
                          }

$asunto_mensaje=$datos_mensaje["asunto"];
$texto_editor=utf8_decode($datos_mensaje["texto_editor"]);
$retorno=$datos_mensaje["retorno"];
$id_curso=$datos_mensaje["id_curso"];

if ($id_curso) {
	
	// Busca datos curso
	$busca_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso));

	$datos_cursos=db_fetch_array($busca_cursos);
	
	$fecha_fin_curso=$datos_cursos["fecha_fin"];	
	
}

$cen=$datos_mensaje["cen"];
$aula_seleccionada_listado=$datos_mensaje["aul"];

if (strlen($aula_seleccionada_listado)!=0) {
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$aula_seleccionada_listado;") or die(db_error());
        $datos_aula=db_fetch_array($consulta_datos_aula);
        $array_centros=array($datos_aula["id_centro"]);
        } else {
        $array_centros=explode(",", $cen);
        }
  
/* Antiguos alumnos NO ¡¡¡PELIGRO!!!
        
$ant=$datos_mensaje["ant"];
$array_antiguos=explode(",", $ant);
*/

$contactos = $datos_mensaje["contactos"];

if ($contactos!="0") {


        $filtroCurso="";
        if (strlen($id_curso)!=0) {
                $filtroCurso=" AND contactos.id_curso=$id_curso";
        }
        
        $filtroCiclo="";
                        
        $filtroCentro = "";

        if (strlen($id_centro)==0) {
                $id_centro=$centro_actual;
                $filtroCentro = 'contactos.id_centro='.$id_centro; 
        } else {                
                $filtroCentro = 'contactos.id_centro='.$id_centro;                
        }
        
        $filtroedad="";
        

        $consultaAlumnos="SELECT *, contactos.id_contacto AS id_usuario_alumno, year(contactos.fecha_nacimiento) AS anio_nacimiento, contactos.id_centro AS centro_alumno FROM contactos WHERE id_contacto>0".$filtroCiclo." AND proceso_decision='".$contactos."' AND $filtroCentro".$filtroCurso.$filtroedad." ORDER BY Apellido1, Apellido2, Nombre";		

        $busca_alumnos=db_query($consultaAlumnos) or die(db_error());
        
        $lista_emails_destino=array();
        
        $contador_alumnos=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
                $fila=$listado_alumnos["id_contacto"];
                        
                // Array con todos los emails del alumno
        
                $listado_emails=str_replace(" ","",$listado_alumnos["email"]);
        
                $array_to = explode(",",$listado_emails);
                
                $lista_emails="";
                                                        
                foreach ($array_to as $to) {        
                        
                        if (comprobar_email($to)!=0) {
                        
                                // Comprueba si está en la lista negra
                                $busca_lista=db_query_params('SELECT * FROM no_recibir_mail WHERE email=?', 's', array((string)($to)));
                                
                                if (db_num_rows($busca_lista)==0) {

                                        if (!in_array($to,$direcciones_enviadas)) {
                        

                                                // Metodo nuevo
                                                
                                                $mail_webmaster=$email_centro[$alumnos_inscritos["id_centro_ins"]];
                                                $nombreRemitente=$nombres_centro[$alumnos_inscritos["id_centro_ins"]];
                                                $linkUnsubscribe=$links_centro[$alumnos_inscritos["id_centro_ins"]];
                                                $logoEscuela="https://".$linkUnsubscribe."/imagen/logo_".$alumnos_inscritos["id_centro_ins"].".jpg";
        
                                                $texto_legal="De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.";
                                                $msg_normal="<p align=\"center\"><img src=\"".$logoEscuela."\" border=\"0\"></p><p>".$texto_editor."</p><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
                                                $msg_alternativo="<br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
        
                                                $asuntoMensaje=$asunto_mensaje;
                                                $cuerpo_mensaje=$msg_normal;
                                                $mailFrom = $mail_webmaster;
                                                $nombreDestinatario=$nombre_padre_act;


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
                                                $mail->addAddress($to, '');
                                                $mail->addReplyTo($mailFrom, $nombreRemitente);
                                                $mail->addCustomHeader("List-Unsubscribe",'<'.$mailFrom.'>, <http://'.$linkUnsubscribe.'/mailer/unsubscribe_alumno1.php?cuenta='.$to.'>');
                                                $mail->WordWrap = 50;                               // Set word wrap to 50 characters
                                                $mail->isHTML(true);                                // Set email format to HTML

                                                $mail->Subject = $asuntoMensaje;
                                                $mail->Body    = $cuerpo_mensaje;

                                                if(!$mail->send()) {
                                                        $direcciones_falladas[]=$to;    
                                                        $mensajesFallados++;
                                                } else {
                                                        $direcciones_enviadas[]=$to;
                                                        $mensajes_enviados++;
                                                }                                               
                                                                                                
                                                // Fin metodo nuevo
                                        } else {
                                                $direcciones_duplicadas++;
                                        }		
                                }
                                                                        
                        }
                } 	
        
        }

} else {

        $type="html";
        $MSGhtml_type="text/html";
        $MSGCharSet="UTF-8";

        $mensajes_enviados=0;
        $direcciones_falladas=array();
        $direcciones_enviadas=array();
        $direcciones_duplicadas=0;

        echo "<font size=3><b>Listado de direcciones a las que se les ha enviado el mensaje:</b></font><br>";

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

                if (strtotime($fecha_fin_curso)<time()) {
                        
                        $filtro_mensajes.=" AND alumnos.fecha_baja>='".date("Y-m-d",strtotime($fecha_fin_curso))."'";
                        
                } else {
                        
                        $filtro_mensajes.=" AND alumnos.fecha_baja>='".date("Y-m-d")."'";	

                }



                $busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario$filtro_mensajes ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");

                while($alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos)) {

                                // Busca provincia
                                $id_provincia_sel=$alumnos_inscritos["Provincia"];
                                if (strlen($id_provincia_sel)==0) {
                                        $id_provincia_sel=0;
                                        }
                                $busca_provincia=db_query_params('SELECT * FROM provincias WHERE id_provincia=?', 's', array($id_provincia_sel));
                                if (db_num_rows($busca_provincia)!=0) {
                                        $datos_provincia=db_fetch_array($busca_provincia);
                                        $nombre_provincia=$datos_provincia["provincia"];
                                        } else {
                                        $nombre_provincia="";
                                        }

                                $consulta_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($alumnos_inscritos["id_centro_ins"])) or die(db_error()."-2");
                                $datos_centro=db_fetch_array($consulta_nombre_centro);

                                // Busca datos del aula
                                $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($alumnos_inscritos["aula"]));
                                $datos_aula=db_fetch_array($consulta_datos_aula);

                                $consulta_relaciones=db_query_params('SELECT * FROM padres WHERE padre_de=? ORDER BY parentesco', 's', array($alumnos_inscritos["id_usuario"])) or die(db_error());

                                $contador_padres=0;
                                while($relaciones=db_fetch_array($consulta_relaciones)) {
                                        $consulta_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($relaciones["id_usuario"]));

                                        $nombre_padre_act="";
                                        $email_padre_act="";
                                        if (db_num_rows($consulta_datos_padre)!=0) {
                                                        $datos_padre=db_fetch_array($consulta_datos_padre);
                                                        $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                                                        $email_padre_act=$datos_padre["email"];
                                                        }


                                        $to = $email_padre_act;

                                        if (strlen($to)!=0) {
                                        
                                                // Comprueba si ya ha sido enviada antes
                                                
                                                if (array_search($to, $direcciones_enviadas)) {

                                                        $direcciones_duplicadas++;

                                                } else {                                                                                                
                                                
                                                        // Metodo nuevo
                                                        
                                                        $mail_webmaster=$email_centro[$alumnos_inscritos["id_centro_ins"]];
                                                        $nombreRemitente=$nombres_centro[$alumnos_inscritos["id_centro_ins"]];
                                                        $linkUnsubscribe=$links_centro[$alumnos_inscritos["id_centro_ins"]];
                                                        $logoEscuela="https://".$linkUnsubscribe."/imagen/logo_".$alumnos_inscritos["id_centro_ins"].".jpg";

                                                        $texto_legal="De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.";
                                                        $msg_normal="<p align=\"center\"><img src=\"".$logoEscuela."\" border=\"0\"></p><p>".$texto_editor."</p><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
                                                        $msg_alternativo="<br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

                                                        $asuntoMensaje=$asunto_mensaje;
                                                        $cuerpo_mensaje=$msg_normal;
                                                        $mailFrom = $mail_webmaster;
                                                        $nombreDestinatario=$nombre_padre_act;


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
                                                        $mail->addAddress($to, '');
                                                        $mail->addReplyTo($mailFrom, $nombreRemitente);
                                                        $mail->addCustomHeader("List-Unsubscribe",'<'.$mailFrom.'>, <http://'.$linkUnsubscribe.'/mailer/unsubscribe_alumno1.php?cuenta='.$to.'>');
                                                        $mail->WordWrap = 50;                               // Set word wrap to 50 characters
                                                        $mail->isHTML(true);                                // Set email format to HTML

                                                        $mail->Subject = $asuntoMensaje;
                                                        $mail->Body    = $cuerpo_mensaje;

                                                        if(!$mail->send()) {
                                                                $direcciones_falladas[]=$to;    
                                                                $mensajesFallados++;
                                                        } else {
                                                                $direcciones_enviadas[]=$to;
                                                                $mensajes_enviados++;
                                                        }                                               

                                                        // Fin metodo nuevo
                                                
                                                }
                                        }

                                        $contador_padres++;
                                        }

                }

        }

}

/*
// Antiguos alumnos
for ($lista_centros=0;$lista_centros<count($array_antiguos);$lista_centros++) {

$filtro_mensajes="";

$centro_seleccionado_listado=$array_antiguos[$lista_centros];

echo "<br><b>Antiguos alumnos de ".$nombres_centros[$centro_seleccionado_listado]."</b><br><br>";

if (strlen($centro_seleccionado_listado)!=0) {
		$filtro_mensajes=" AND alumnos.fecha_baja<'".date("Y-m-d")."' AND alumnos.id_centro=$centro_seleccionado_listado";
		
		if (strtotime($fecha_fin_curso)<time()) {
			
			$filtro_mensajes=" AND alumnos.fecha_baja<'".date("Y-m-d",strtotime($fecha_fin_curso))."' AND alumnos.id_centro=$centro_seleccionado_listado";
			
			}      
        }

$busca_alumnos_inscritos=mysql_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario$filtro_mensajes ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");

while($alumnos_inscritos=mysql_fetch_array($busca_alumnos_inscritos)) {

                // Busca provincia
                $id_provincia_sel=$alumnos_inscritos["Provincia"];
                if (strlen($id_provincia_sel)==0) {
                        $id_provincia_sel=0;
                        }
                $busca_provincia=mysql_query("SELECT * FROM provincias WHERE id_provincia=".$id_provincia_sel);
                if (mysql_num_rows($busca_provincia)!=0) {
                        $datos_provincia=mysql_fetch_array($busca_provincia);
                        $nombre_provincia=$datos_provincia["provincia"];
                        } else {
                        $nombre_provincia="";
                        }

                $consulta_nombre_centro=mysql_query("SELECT * FROM centros WHERE id_centro=".$alumnos_inscritos["id_centro_ins"]) or die(mysql_error()."-2");
                $datos_centro=mysql_fetch_array($consulta_nombre_centro);

                // Busca datos del aula
                $consulta_datos_aula=mysql_query("SELECT * FROM centro_aulas WHERE id_aula=".$alumnos_inscritos["aula"]);
                $datos_aula=mysql_fetch_array($consulta_datos_aula);

                $consulta_relaciones=mysql_query("SELECT * FROM padres WHERE padre_de=".$alumnos_inscritos["id_usuario"]." ORDER BY parentesco") or die(mysql_error());

                $contador_padres=0;
                while($relaciones=mysql_fetch_array($consulta_relaciones)) {
                        $consulta_datos_padre=mysql_query("SELECT * FROM usuarios WHERE id_usuario=".$relaciones["id_usuario"]);

                        $nombre_padre_act="";
                        $email_padre_act="";
                        if (mysql_num_rows($consulta_datos_padre)!=0) {
                                        $datos_padre=mysql_fetch_array($consulta_datos_padre);
                                        $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                                        $email_padre_act=$datos_padre["email"];
                                        }


                        $to = $email_padre_act;

                        if (strlen($to)!=0) {

                        // Comprueba si ya ha sido enviada antes

                        if (array_search($to, $direcciones_enviadas)) {
                        
                        $direcciones_duplicadas++;
                        
                        } else {
                        

                                                
                        // Metodo nuevo
                                                $asuntoMensaje=$asunto_mensaje;
                                                $cuerpo_mensaje=$msg_normal;
                                                $mailFrom = "escuelainfantilsantpau@enviosmail.net";
                                                $nombreRemitente="Escuela Infantil Sant Pau";
                                                $nombreDestinatario=$nombre_padre_act;


                                                $mail = new PHPMailer;
                               
                                                //$mail->SMTPDebug = 3;                               // Enable verbose debug output

                                                $mail->isSMTP();                                      // Set mailer to use SMTP
                                                $mail->Host = '94.23.35.93';  // Specify main and backup SMTP servers
                                                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                                                $mail->Username = (getenv('ZEIM_SMTP_USER_02') ?: '');                 // SMTP username
                                                $mail->Password = (getenv('ZEIM_SMTP_PASSWORD_02') ?: '');                           // SMTP password

                                                $mail->From = $mailFrom;
                                                $mail->FromName = $nombreRemitente;
                                                $mail->addAddress($to, $nombreDestinatario);
                                                $mail->addReplyTo($mailFrom, $nombreRemitente);
                                                $mail->addCustomHeader("List-Unsubscribe",'<info@escuelainfantilsantpau.com>');
                                                $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
                                                $mail->isHTML(true);                                  // Set email format to HTML

                                                $mail->Subject = $asuntoMensaje;
                                                $mail->Body    = $cuerpo_mensaje;

                                                if(!$mail->send()) {
                                                        $mensajesFallados++;
                                                } else {
                                                    $direcciones_enviadas[$mensajes_enviados]=$to;
                                $mensajes_enviados++;
                                                }


                                                // Fin metodo nuevo
                                                
                        }
                        }

                        $contador_padres++;
                        }

}

}
*/
// mysql_query("DELETE FROM mensajes_temporal WHERE id_mensaje=$id_mensaje;");

db_close();
?>
<html>
<head>

<title>Enviar mensaje</title>

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
Se han enviado <?=$mensajes_enviados?> mensajes.<br><br>
Se han omitido <?=$direcciones_duplicadas?> direcciones por estar duplicadas.<br><br>
<?=$mensajesFallados?> direcciones han dado error.<br><br>
<?php 
if ($mensajesFallados>0) {
?>
        Listado de direcciones a las que no se les ha podido enviar el mensaje:<br /><br />
<?php
        echo implode("<br />", $direcciones_falladas);
}
?>
<br />
<input type="button" onclick="window.close()" value="Cerrar">
</div>
</body>
</html>