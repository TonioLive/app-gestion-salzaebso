<?php
require_once dirname(__DIR__).'/configuracion/runtime.php';
 

$id_mensaje=$_GET["id"];

// Comprobar si el referer es correcto...

$sql_host=app_db_settings('sql_host');
$sql_db=app_db_settings('sql_db');
$sql_usuario=app_db_settings('sql_usuario');
$sql_pass=app_db_settings('sql_pass');

$db_validacion=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_validacion);

$busca_mensaje=db_query_params('SELECT * FROM mensajes_temporal WHERE id_mensaje=?;', 's', array($id_mensaje));

$datos_mensaje=db_fetch_array($busca_mensaje);

$centro_seleccionado_email=$datos_mensaje["id_centro"];

$email_centro[8]="lapices@escuelas-infantiles.com";
$email_centro[6]="lapices@escuelas-infantiles.com";
$email_centro[7]="lapices@escuelas-infantiles.com";


$busca_listado_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro");
$nombres_centros=array();
while ($listado_centros=db_fetch_array($busca_listado_centros)){
                          $nombres_centros[$listado_centros["id_centro"]]=$listado_centros["nombre_centro"];
                          }

$asunto_mensaje=$datos_mensaje["asunto"];
$texto_editor=$datos_mensaje["texto_editor"];
$retorno=$datos_mensaje["retorno"];
$id_curso=$datos_mensaje["id_curso"];

$cen=$datos_mensaje["cen"];
$aula_seleccionada_listado=$datos_mensaje["aul"];

if (strlen($aula_seleccionada_listado)!=0) {
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$aula_seleccionada_listado;") or die(db_error());
        $datos_aula=db_fetch_array($consulta_datos_aula);
        $array_centros=array($datos_aula["id_centro"]);
        } else {
        $array_centros=explode(",", $cen);
        }
        
$ant=$datos_mensaje["ant"];
$array_antiguos=explode(",", $ant);

$titulo_web="Escuela infantil lápices";

$mail_webmaster="lapices@escuelas-infantiles.com";

$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$texto_legal=htmlentities("De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde la ".$titulo_web.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde la ".$titulo_web." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mail_webmaster." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.");

$msg_normal="<img src='http://gestion.neo-kids.es/imagen/encabezado_mensaje.gif'><br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
$msg_alternativo="<br>".$texto_editor."<br><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";

$mensajes_enviados=0;
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
                        echo $to."<br>";

                        $direcciones_enviadas[$mensajes_enviados]=$to;
                        $mensajes_enviados++;
                        
                        }
                        }

                        $contador_padres++;
                        }

}

}

for ($lista_centros=0;$lista_centros<count($array_antiguos);$lista_centros++) {

$filtro_mensajes="";

$centro_seleccionado_listado=$array_antiguos[$lista_centros];

echo "<br><b>Antiguos alumnos de ".$nombres_centros[$centro_seleccionado_listado]."</b><br><br>";

if (strlen($centro_seleccionado_listado)!=0) {
        $filtro_mensajes=" AND alumnos.fecha_baja<'".date("Y-m-d")."' AND alumnos.id_centro=$centro_seleccionado_listado";
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
                        echo $to."<br>";

                        $direcciones_enviadas[$mensajes_enviados]=$to;
                        $mensajes_enviados++;

                        }
                        }

                        $contador_padres++;
                        }

}

}

db_query_params('DELETE FROM mensajes_temporal WHERE id_mensaje=?;', 's', array($id_mensaje));

db_close();
?>
<html>
<head>

 <title>SELECCIÓN FECHAS</title>

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
<input type="button" onclick="window.close()" value="Cerrar">
</div>
</body>
</html>
