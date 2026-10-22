<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_aula=$_GET["ii"];
$id_servicio=$_GET["ids"];

$borrar_articulo = db_query_params('DELETE FROM servicios_contratados_aula WHERE id_servicio_contratado=?', 's', array($id_servicio)) or die (db_error());

header("Location: editar_servicios_aula.php?i=".$id_aula);
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

