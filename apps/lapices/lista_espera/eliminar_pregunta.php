<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

$id_tipo_punto=$_GET["pregunta"];


$borrar_articulo = db_query("DELETE FROM lista_espera_cuestionarios WHERE id_tipo_punto=".$id_tipo_punto) or die (db_error());


header("Location: edicion.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

