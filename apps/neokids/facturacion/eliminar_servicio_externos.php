<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_inscripcion=$_GET["ii"];
$id_servicio=$_GET["ids"];

$borrar_articulo = db_query_params('DELETE FROM servicios_contratados_externos WHERE id_servicio_contratado=?', 's', array($id_servicio)) or die (db_error());

header("Location: editar_servicios_externos.php?i=".$id_inscripcion);

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="Javascript">
// window.close();
self.opener.history.go(0);
window.location.href='editar_servicios_externos.php?i=<?=$id_inscripcion?>';
</script>
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

