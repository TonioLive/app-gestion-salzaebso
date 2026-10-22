<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_alimento=$_GET["id_alimento"];


$borrar_articulo = db_query_params('DELETE FROM menu_alimentos WHERE id_alimento=?', 's', array($id_alimento)) or die (db_error());


header("Location: gestion_alimentos.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

