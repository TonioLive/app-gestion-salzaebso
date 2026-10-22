<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$codigo=$_GET["Cod"];
$submenu=$_GET["Submenu"];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

db_query_params('DELETE FROM NoticiasN WHERE Cod=?', 's', array($codigo));

db_close();
mt_srand((float)microtime()*1000000);
header("Location: "."modif_noticia.php?Submenu=".$submenu."&id=".(mt_rand(0,10000000)/10000000));
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

