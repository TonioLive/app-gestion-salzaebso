<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_incidencia=$_GET["id_incidencia"];
$c=$_GET["c"];

$borrar_incidencia = db_query_params('DELETE FROM incidencias WHERE id_incidencia=?', 's', array($id_incidencia)) or die (db_error());


header("Location: incidencias_general.php?c=$c");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

