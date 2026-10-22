<?php 

$id_mensaje=$_GET["id"];

// Comprobar si el referer es correcto...

$sql_host = (getenv('APP_DB_HOST') ?: 'localhost');
$sql_db = (getenv('APP_DB_NAME') ?: '');
$sql_usuario = (getenv('APP_DB_USER') ?: '');
$sql_pass = (getenv('APP_DB_PASSWORD') ?: '');

$db_validacion=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_validacion);

$busca_mensaje=db_query("SELECT * FROM mensajes_temporal WHERE id_mensaje=$id_mensaje;");

$datos_mensaje=db_fetch_array($busca_mensaje);

$centro_seleccionado_email=$datos_mensaje["id_centro"];

$email_centro[8]="lapices@escuelas-infantiles.com";
$email_centro[6]="lapices@escuelas-infantiles.com";
$email_centro[7]="lapices@escuelas-infantiles.com";

$asunto_mensaje=$datos_mensaje["asunto"];
$texto_editor=$datos_mensaje["texto_editor"];
$retorno=$datos_mensaje["retorno"];
$solo_activos=$datos_mensaje["solo_activos"];
$filtro_mensajes="";

$centro_seleccionado_listado=$datos_mensaje["cen"];

if (strlen($centro_seleccionado_listado)!=0) {
        $filtro_mensajes=" AND alumnos.id_centro=$centro_seleccionado_listado";
        }

$aula_seleccionada_listado=$datos_mensaje["aul"];

if (strlen($aula_seleccionada_listado)!=0) {
        $filtro_mensajes=" AND alumnos.aula=$aula_seleccionada_listado";
        }
if (strlen($solo_activos)!=0) {
        $filtro_mensajes.=" AND alumnos.fecha_baja>='".date("Y-m-d")."' AND alumnos.fecha_matricula<='".date("Y-m-d")."'";
        }

$titulo_web="Escuela infantil lápices";

$mail_webmaster=$email_centro[$centro_seleccionado_email];

$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_webmaster." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");


$msg="<img src='https://www.escuelasinfantileslapices.es/imagen/encabezado_mensaje.gif'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";


$busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario$filtro_mensajes ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");

$mensajes_enviados=0;

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

                        $nombre_padre_act="";
                        $email_padre_act="";
                        if (db_num_rows($consulta_datos_padre)!=0) {
                                        $datos_padre=db_fetch_array($consulta_datos_padre);
                                        $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                                        $email_padre_act=$datos_padre["email"];
                                        }


                        $to = $email_padre_act;

                        if (strlen($to)!=0) {

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

                        $mensajes_enviados++;
                        }

                        $contador_padres++;
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
Se han enviado <?=$mensajes_enviados?> mensajes.<br><br><br>
<input type="button" onclick="window.close()" value="Cerrar">
</div>
</body>
</html>
