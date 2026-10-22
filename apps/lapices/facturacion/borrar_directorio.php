<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

$archivo=$_GET["archivo"];

//esto es para borrarlo, acuerdate
if (file_exists("$web_path\\recibos\\$archivo")) {
          unlink("$web_path\\recibos\\$archivo");
          }

mt_srand((double)microtime()*1000000);
header("Location: "."directorio.php?id=".(mt_rand(0,10000000)/10000000));
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

