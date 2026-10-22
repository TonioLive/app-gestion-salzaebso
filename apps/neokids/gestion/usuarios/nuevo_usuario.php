<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica_admin.php");

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

db_query_params('INSERT INTO usuarios (tipo_inscripcion,Nombre,nivel_acceso,fechaalta) VALUES (\'4\',\'Nuevo usuario\',\'2\',?);', 's', array((string)(date("Y/m/d"))));

db_close();

mt_srand((float)microtime()*1000000);
header("Location: "."gestion_u.php?Submenu=".$submenu_activo."&ran=".(mt_rand(0,10000000)/10000000));
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

