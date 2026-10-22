<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_inscripcion=$_GET["ii"];
$id_servicio=$_GET["ids"];

$borrar_articulo = db_query_params('DELETE FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($id_servicio, $id_inscripcion)) or die (db_error());

header("Location: editar_servicios.php?i=".$id_inscripcion);
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

