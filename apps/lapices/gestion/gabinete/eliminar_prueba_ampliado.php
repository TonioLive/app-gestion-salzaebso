<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_prueba=$_GET["id_prueba"];
$id_alumno=$_GET["id_alumno"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

db_query("DELETE FROM pruebas_brunet_ampliado WHERE id_prueba=$id_prueba");
db_query("DELETE FROM pruebas_brunet_ampliado_respuestas WHERE id_prueba=$id_prueba");

db_close();

header("Location: "."seguimiento.php?id_alumno=$id_alumno");
?>
