<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_plato=$_GET["id_plato"];
$id_ingrediente=$_GET["id_ingrediente"];


$borrar_articulo = db_query("DELETE FROM menu_ingredientes WHERE id_ingrediente=$id_ingrediente") or die (db_error());


header("Location: composicion_plato.php?id_plato=$id_plato");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

