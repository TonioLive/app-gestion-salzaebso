<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");

$a=$_GET["a"];
$na=$_GET["na"];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

db_query("UPDATE alumnos SET aula=$na, aula_ayudas=$na WHERE id_alumno=$a");

db_close();

mt_srand((float)microtime()*1000000);
header("Location: "."listado_alumnos.php?id_aula=".$na."&ran=".(mt_rand(0,10000000)/10000000));
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>

