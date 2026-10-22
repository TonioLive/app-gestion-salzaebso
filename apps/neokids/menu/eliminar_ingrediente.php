<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_plato=$_GET["id_plato"];
$id_ingrediente=$_GET["id_ingrediente"];


$borrar_articulo = db_query_params('DELETE FROM menu_ingredientes WHERE id_ingrediente=?', 's', array($id_ingrediente)) or die (db_error());


header("Location: composicion_plato.php?id_plato=$id_plato");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

