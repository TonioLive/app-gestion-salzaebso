<?php 

$usuario_camara=(getenv('ZEIM_CAMERA_USER_02') ?: '');
$password_camara=(getenv('ZEIM_CAMERA_PASSWORD_02') ?: '');
$url_camara="generalaviles.homeip.net";
$puerto_camara="9052";

?>
<html>
<body>
<SCRIPT LANGUAGE="JavaScript">
  // Set the BaseURL to the url of your camera

  var BaseURLMozilla = "http://<?=$usuario_camara?>:<?=$password_camara?>@<?=$url_camara?>:<?=$puerto_camara?>/";

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
  // output += '&dummy=' + theDate.getTime().toString(10);
  output += '" HEIGHT="';
  output += DisplayHeight;
  output += '" WIDTH="';
  output += DisplayWidth;
  output += '" ALT="Camera Image">';



document.write(output);


</SCRIPT>
</body>
</html>
