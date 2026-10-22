<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$codigo=$_GET["Cod"];
$submenu=$_GET["Submenu"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

db_query("DELETE FROM NoticiasN WHERE Cod=".$codigo);

db_close();
mt_srand((double)microtime()*1000000);
header("Location: "."modif_noticia.php?Submenu=".$submenu."&id=".(mt_rand(0,10000000)/10000000));
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

