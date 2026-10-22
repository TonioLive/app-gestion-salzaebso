<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_profesion=$_GET["id_profesion"];


$borrar_articulo = db_query_params('DELETE FROM padres_profesiones WHERE id_profesion=?', 's', array($id_profesion)) or die (db_error());


header("Location: listado_profesiones.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

