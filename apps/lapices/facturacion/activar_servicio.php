<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_inscripcion=$_GET["ii"];
$id_servicio=$_GET["ids"];

$borrar_articulo = db_query("DELETE FROM servicios_no_contratados WHERE id_servicio_aula=".$id_servicio." AND id_usuario=$id_inscripcion") or die (db_error());

header("Location: editar_servicios.php?i=".$id_inscripcion);
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

