<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_aula=$_GET["ii"];
$id_servicio=$_GET["ids"];

$borrar_articulo = db_query("DELETE FROM servicios_contratados_aula WHERE id_servicio_contratado=".$id_servicio) or die (db_error());

header("Location: editar_servicios_aula.php?i=".$id_aula);
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

