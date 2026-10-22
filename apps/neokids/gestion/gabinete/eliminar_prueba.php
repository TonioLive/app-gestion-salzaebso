<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prueba=$_GET["id_prueba"];
$id_alumno=$_GET["id_alumno"];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

db_query_params('DELETE FROM pruebas_brunet WHERE id_prueba=?', 's', array($id_prueba));
db_query_params('DELETE FROM pruebas_brunet_respuestas WHERE id_prueba=?', 's', array($id_prueba));

db_close();

header("Location: "."seguimiento.php?id_alumno=$id_alumno");
?>
