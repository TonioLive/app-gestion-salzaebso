<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$id_centro=$_GET["id_centro"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

db_query("DELETE FROM centro_aulas WHERE id_aula=".$id_aula) or die(db_error()."-1");
db_query("DELETE FROM servicios_contratados_aula WHERE id_aula=".$id_aula) or die(db_error()."-2");

db_close();
mt_srand((double)microtime()*1000000);
header("Location: editar_centro.php?id_centro=$id_centro");
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

