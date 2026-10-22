<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_incidencia=$_GET["id_incidencia"];
$c=$_GET["c"];

$borrar_incidencia = db_query("DELETE FROM incidencias_2017 WHERE id_incidencia=".$id_incidencia) or die (db_error());
$elimina_seguimiento=db_query("DELETE FROM incidencias_seguimiento WHERE id_incidencia=$id_incidencia");


header("Location: incidencias_general.php?c=$c");
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

