<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$codigo=$_GET["codigo"];
$familia=$_GET["familia"];

$borrar_articulo = db_query_params('DELETE FROM tarifa WHERE ID=?', 's', array($codigo)) or die (db_error());

header("Location: ver_familia.php?familia=".$familia);
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

