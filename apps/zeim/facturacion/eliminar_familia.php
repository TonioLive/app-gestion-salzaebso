<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$familia=$_GET["familia"];


$borrar_articulo = db_query("DELETE FROM tipos_iva_servicios WHERE id_tipo_iva=".urldecode($familia)) or die (db_error());


header("Location: gestion_familias.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

