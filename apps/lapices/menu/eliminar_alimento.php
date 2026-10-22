<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_alimento=$_GET["id_alimento"];


$borrar_articulo = db_query("DELETE FROM menu_alimentos WHERE id_alimento=$id_alimento") or die (db_error());


header("Location: gestion_alimentos.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

