<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

$busca_orden_en_aula=db_query_params('SELECT * FROM alumnos WHERE alumnos.aula_ayudas=? AND orden_en_aula<>0', 's', array($id_aula));

if (db_num_rows($busca_orden_en_aula)!=0) {

        $actualiza_alumno=db_query_params('UPDATE alumnos SET orden_en_aula=0 WHERE aula_ayudas=?', 's', array($id_aula));
                
        }

db_close();

mt_srand((float)microtime()*1000000);
header("Location: "."listado_alumnos_ayudas.php?id_aula=".$id_aula."&ran=".(mt_rand(0,10000000)/10000000));
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>
