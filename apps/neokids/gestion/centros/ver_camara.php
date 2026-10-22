<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_admin.php");

$id_webcam=$_GET["id_webcam"];
$ip_local=$_GET["local"];

if ($id_webcam!=0) {

$busca_datos_camara=db_query_params('SELECT * FROM webcam WHERE id_webcam=?', 's', array($id_webcam));

$datos_camara=db_fetch_array($busca_datos_camara);

$usuario_camara=$datos_camara["usuario_visor"];
$password_camara=$datos_camara["password_visor"];
$url_camara=$datos_camara["url_webcam"];
if ($ip_local==1) {
        $url_camara=$datos_camara["ip_local"];
        }
$puerto_camara=$datos_camara["puerto_webcam"];
$nombre_camara=$datos_camara["nombre_webcam"];
$hora_inicio_camara=strtotime($datos_camara["hora_conexion"]);
$hora_fin_camara=strtotime($datos_camara["hora_desconexion"]);
$modelo_camara=$datos_camara["modelo"];
}

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
                                                                <table width="336" height="15" border="0" cellpadding="0" cellspacing="0" align="center">
                                                                <tr>
                                                                <td rowspan="4">&nbsp;</td>
                                                                <td valign="bottom" height="21" colspan="3" width="360"><img src="../../webcam/imag/arriba.jpg"></td>
                                                                <td rowspan="4">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                                <td  align="right" width="8"><img src="../../webcam/imag/izquierda.jpg" width="8"></td>
                                                                <td width="320" height="240" valign="middle" align="center">
<?php 
if ($modelo_camara==2) {

if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {

?>
<script language="javascript" src="enet.js"></script>
<SCRIPT language="javascript">
<!--
var ipcam_width = "320";
var ipcam_height = "240";
var ipaddr = "<?=$url_camara?>";
var port = "<?=$puerto_camara?>";
var login = <?php echo json_encode(getenv('NEOKIDS_CAMERA_LOGIN') ?: '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var password = <?php echo json_encode(getenv('NEOKIDS_CAMERA_PASSWORD') ?: '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var code = <?php echo json_encode(getenv('NEOKIDS_CAMERA_CODE') ?: '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
//-->
</SCRIPT>
<div id="ipcam_div" align="left">
<script language="JScript" src="ipcam.js"></script>
</div>
<?php 
} else {
?>
<SCRIPT LANGUAGE="JavaScript">
  // Set the BaseURL to the url of your camera

  var BaseURLMozilla = "http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=($puerto_camara+1)?>/";

  // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
  // You may change these numbers, the effect will be a stretch or a shrink of the image
  var DisplayWidth = "320";
  var DisplayHeight = "240";

  // This is the path to the image generating file inside the camera itself

  var File = "mjpg/video.mjpg";

  // No changes required below this point

var output = "";

  theDate = new Date();
  output = '<IMG SRC="';
  output += BaseURLMozilla;
  output += File;
  output += '" HEIGHT="';
  output += DisplayHeight;
  output += '" WIDTH="';
  output += DisplayWidth;
  output += '" ALT="Camera Image">';



document.write(output);

</SCRIPT>
<?php 
}
} else {

if ($modelo_camara==3) {

if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {

?>
<iframe name="webcam" src="camara_ie.php?id_webcam=<?=$id_webcam;?>" frameborder="0" scrolling="no" width="320" height="240" marginwidth="0" marginheight="0" seamless="seamless"></iframe>
<?php 
} else {
?>
<SCRIPT LANGUAGE="JavaScript">
  // Set the BaseURL to the url of your camera

  var BaseURLMozilla = "http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=$puerto_camara?>/";

  // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
  // You may change these numbers, the effect will be a stretch or a shrink of the image
  var DisplayWidth = "320";
  var DisplayHeight = "240";

  // This is the path to the image generating file inside the camera itself

  var File = "snapshot.cgi";

  // No changes required below this point

var output = "";

  theDate = new Date();
  output = '<IMG SRC="';
  output += BaseURLMozilla;
  output += File;
  output += '" HEIGHT="';
  output += DisplayHeight;
  output += '" WIDTH="';
  output += DisplayWidth;
  output += '" ALT="Camera Image">';



document.write(output);

</SCRIPT>
<?php 
}

} else{

?>
<SCRIPT LANGUAGE="JavaScript">
  // Set the BaseURL to the url of your camera
  var BaseURL = "http://<?=$url_camara?>:<?=$puerto_camara?>/";
  var BaseURLMozilla = "http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=$puerto_camara?>/";

  // DisplayWidth & DisplayHeight specifies the displayed width & Height of the image.
  // You may change these numbers, the effect will be a stretch or a shrink of the image
  var DisplayWidth = "320";
  var DisplayHeight = "240";

  // This is the path to the image generating file inside the camera itself



  var File = "axis-cgi/mjpg/video.cgi?resolution=320x240";

  // No changes required below this point

var output = "";

if ((navigator.appName == "Microsoft Internet Explorer") &&
   (navigator.platform != "MacPPC") && (navigator.platform != "Mac68k"))
{
  // If Internet Explorer under Windows then use ActiveX

  output  = '<OBJECT ID="Player" width='
  output += DisplayWidth;
  output += ' height=';
  output += DisplayHeight;
  output += ' CLASSID="CLSID:DE625294-70E6-45ED-B895-CFFA13AEB044" ';
  output += 'CODEBASE="';
  output += BaseURL;
  output += 'activex/AMC.cab#version=3,32,19,0">';
  output += '<PARAM NAME="MediaURL" VALUE="';
  output += BaseURL;
  output += File + '">';
  output += '<param name="MediaType" value="mjpeg-unicast">';
  output += '<param name="MediaUsername" value="<?=$usuario_camara?>">';
  output += '<param name="MediaPassword" value="<?=$password_camara?>">';
  output += '<param name="ShowStatusBar" value="0">';
  output += '<param name="ShowToolbar" value="0">';
  output += '<param name="AutoStart" value="1">';
  output += '<param name="StretchToFit" value="1">';
  output += '<BR><B>Axis Media Control</B><BR>';
  output += 'The AXIS Media Control, which enables you ';
  output += 'to view live image streams in Microsoft Internet';
  output += ' Explorer, could not be registered on your computer.';
  output += '<BR></OBJECT>';
/*
  output =  '<OBJECT ID="Player" width='
  output += DisplayWidth;
  output += ' height=';
  output += DisplayHeight;
  output += ' CLASSID="CLSID:745395C8-D0E1-4227-8586-624CA9A10A8D" ';
  output += 'CODEBASE="';
  output += BaseURL;
  output += '/activex/AMC.cab#version=2,0,22,0">';
  output += '<PARAM NAME="MediaURL" VALUE="';
  output += BaseURL;
  output += File + '">';
  output += '<param name="MediaType" value="mjpeg-unicast">';
  output += '<param name="MediaUsername" value="<?=$usuario_camara?>">';
  output += '<param name="MediaPassword" value="<?=$password_camara?>">';
  output += '<param name="ShowStatusBar" value="0">';
  output += '<param name="ShowToolbar" value="0">';
  output += '<param name="AutoStart" value="1">';
  output += '<param name="StretchToFit" value="1">';
  output += '<BR><B>Axis Media Control</B><BR>';
  output += 'The AXIS Media Control, which enables you ';
  output += 'to view live image streams in Microsoft Internet';
  output += ' Explorer, could not be registered on your computer.';
  output += '<BR></OBJECT>';
*/

} else {
  // If not IE for Windows use the browser itself to display
  theDate = new Date();
  output = '<IMG SRC="';
  output += BaseURLMozilla;
  output += File;
  output += '&dummy=' + theDate.getTime().toString(10);
  output += '" HEIGHT="';
  output += DisplayHeight;
  output += '" WIDTH="';
  output += DisplayWidth;
  output += '" ALT="Camera Image">';

}

document.write(output);
document.Player.ToolbarConfiguration = "play,+snapshot,+fullscreen"


</SCRIPT>
<?php 
}
}
?>
                                                                </td>
                                                                <td  align="left" width="32"><img src="../../webcam/imag/derecha.jpg" width="32"></td>
                                                        </tr>
                                                        <tr>
                                                                <td valign="top" width="360" height="39" colspan="3"><img src="../../webcam/imag/abajo.jpg" width="360"></td>
                                                        </tr>
                                                      </table>
                                                      <br>
                                                      <input type="button" onclick="window.close()" value="Cerrar ventana" style="font-size: 10px"><br><br>
</center></div>
</body>
</html>
