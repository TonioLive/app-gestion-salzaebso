<?php 
require("../../configuracion/config.php");
?>
<html>
<head>
<title>Recordatorio de contraseña</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php  require("../../configuracion/estilos/estilo1.php"); ?>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $color_lat; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background=<?php  echo $back_image; ?> bgproperties="fixed" >
<img src="<?php  echo $url_web; ?>/imagen/logo1.gif">
<div align="center"><center>
<?php  
$id_pass=$_GET["id"];

if (strlen($id_pass)==0)
{
  $dni=$_POST["dni"];
  $consulta_password="SELECT * FROM usuarios WHERE DNI='".$dni."'";
}
  else
{
  $consulta_password="SELECT * FROM usuarios WHERE ID=".$id_pass;
} 

$db_m1=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta_u=db_query($consulta_password);

if (db_num_rows($consulta_u) != 0) {

  $rsbusca_usuario=db_fetch_array($consulta_u);

  $id_usuario=$rsbusca_usuario["Codigo"];
  $nombre_completo_alumno=$rsbusca_usuario["Nombre"]." ".$rsbusca_usuario["Apellido1"]." ".$rsbusca_usuario["Apellido2"];
  $nombre_usuario=$rsbusca_usuario["usuario"];
  $password_usu=$rsbusca_usuario["pass"];
?>
</div><div align="center">
<p align="center">El mensaje con la contraseña del usuario <b><?php    echo $nombre_completo_alumno; ?></b>,
<?php  
  $adrressfrom=$mail_contacto_general;
  $namefrom="Centro de formación virtual";
  $destino=$rsbusca_usuario["Email"];
  $subject="Recordatorio Contraseña";
  $txtmsg="<font face='Arial' size=2>".
    "En respuesta a su petición, le remitimos sus datos de acceso al centro de formación virtual.<br><br>".
    "Su nombre de usuario y contraseña son los siguientes:".
    "<p align='center'>Nombre de usuario: <b>".$nombre_usuario."</b><br>".
    "Contraseña: <b>".$password_usu."</b></p><br>".
    " Gracias.";

  $headers .= "From: ".$namefrom."<".$adrressfrom.">\n";
  $headers .= "X-Sender: <".$adrressfrom.">\n";
  $headers .= "X-Mailer: PHP\n"; //mailer
  $headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
  $headers .= "Return-Path: <".$adrressfrom.">\n";
  //Quitar comentarios para enviar en formato HTML
  $headers .= "Content-Type: text/html; charset=UTF-8\n";
  //$headers .= "cc: usuario@dominio.com\n"; // CC
  //$headers .= "bcc: usuario@dominio.com"; // BCC to, separar multiples con comas mail@mail.com, mail2@mail.com

$envia_mail=mail($destino,$subject,$txtmsg,$headers);

if (!$envia_mail) {
        echo (" no ha podido enviarse debido a un error al enviar el mensaje de correo.");
        } else {
        echo " se ha enviado correctamente.";
        }
} else {
?>
<br><br>
<big>El usuario solicitado no existe.</big>
<?php 
}
?>
<br><br>
Pulse el botón <b>Volver</b> para salir.
<br><br>
<input type="button" value="<< Volver" style="font-size: 10px" onclick="history.go(-1)" id=button2 name=button2>
<br><br>
</body>
</html>

