<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

switch ($centro_actual) {
	case 10:
		$mail_webmaster="santpau@escuelas-infantiles.com";
		$nombreRemitente="Escuela Infantil Sant Pau";
		$linkEscuela="santpau.escuelas-infantiles.com";
	break;	
	case 23:
		$mail_webmaster="benicalap@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal Benicalap";
		$linkEscuela="gestion.escuelainfantilbenicalap.com";
	break;
	case 24:
		$mail_webmaster="minipoli@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal Minipoli";
		$linkEscuela="gestion.escuelainfantilminipoli.com";
	break;	
}
$logoEscuela="https://".$linkEscuela."/imagen/logo_".$centro_actual.".jpg";

$id_mensaje=$_GET["id"];

$db_validacion=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_validacion);

$busca_mensaje=db_query_params('SELECT * FROM mensajes_temporal WHERE id_mensaje=?;', 's', array($id_mensaje));

$datos_mensaje=db_fetch_array($busca_mensaje);



$asunto_mensaje=$datos_mensaje["asunto"];
$texto_editor=$datos_mensaje["texto_editor"];

$titulo_web="Escuela infantil lápices";

$mail_webmaster="lapices@escuelas-infantiles.com";

$type="html";
$MSGhtml_type="text/html";
$MSGCharSet="UTF-8";

$texto_legal="De conformidad a la Ley Orgánica 15/1999 de 13 de diciembre, de Protección de Datos de Carácter Personal, Desde ".$nombreRemitente.", le informamos que sus datos han sido obtenidos de un fichero titularidad de esta empresa y que en cumplimiento de lo establecido en la Ley 34/2002 de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, se le comunica que en el supuesto de que no desee seguir recibiendo comunicaciones e informaciones remitidas desde ".$nombreRemitente." mediante comunicaciones electrónicas lo indique a la dirección de correo ".$mailFrom." incluyendo su nombre y dirección de correo electrónico donde haya recibido el presente mensaje.";
$msg="<p align=\"center\"><img src=\"".$logoEscuela."\" border=\"0\"></p><p>".$texto_editor."</p><br><br><font size=1 face='Helvetica'>".$texto_legal."</font>";
        
db_query_params('DELETE FROM mensajes_temporal WHERE id_mensaje=?;', 's', array($id_mensaje));

db_close();
?>
<html>
<head>

 <title>Vista previa del mensaje</title>

 <META http-equiv='Content-Type' content='text/html; charset=utf-8'>
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
<table width=750 border=1 bordercolor="#000000" style="border-collapse:  collapse">
<tr><td>
<?=$msg?></td>
</tr>
</div>
</body>
</html>
