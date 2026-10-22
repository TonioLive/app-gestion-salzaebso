<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$id_centro=$_GET["id_centro"];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

db_query_params('DELETE FROM centro_aulas WHERE id_aula=?', 's', array($id_aula)) or die(db_error()."-1");
db_query_params('DELETE FROM servicios_contratados_aula WHERE id_aula=?', 's', array($id_aula)) or die(db_error()."-2");

db_close();
mt_srand((float)microtime()*1000000);
header("Location: editar_centro.php?id_centro=$id_centro");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

