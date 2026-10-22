<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

$archivo=$_GET["archivo"];

//esto es para borrarlo, acuerdate
if (file_exists("$web_path\\recibos\\$archivo")) {
          unlink("$web_path\\recibos\\$archivo");
          }

mt_srand((float)microtime()*1000000);
header("Location: "."directorio.php?id=".(mt_rand(0,10000000)/10000000));
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

