<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_motivo=$_GET["id_motivo"];


$borrar_articulo = db_query_params('DELETE FROM contactos_motivos_cancelacion WHERE id_motivo=?', 's', array($id_motivo)) or die (db_error());


header("Location: listado_motivos.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

