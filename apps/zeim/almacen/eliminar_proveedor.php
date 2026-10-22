<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_proveedor=$_GET["id_proveedor"];


$borrar_articulo = db_query_params('DELETE FROM proveedores WHERE ID_PROVEEDOR=?', 's', array($id_proveedor)) or die (db_error());


header("Location: listado_proveedores.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

