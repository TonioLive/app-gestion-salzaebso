<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_factura=$_GET["id_factura"];
$id_cliente=$_GET["id_cliente"];
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];

db_query_params('UPDATE facturas_emitidas SET estado=1 WHERE id_factura=?', 's', array($id_factura));

header("Location: listado_facturas.php?mes=$mes&centro_factura=$centro_factura&id_cliente=$id_cliente&ejercicio=$ejercicio");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

