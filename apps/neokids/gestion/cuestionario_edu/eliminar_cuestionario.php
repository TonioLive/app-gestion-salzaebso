<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_cuestionario=$_GET["id_cuestionario"];
$id_alumno=$_GET["id_alumno"];
$tipo_cuestionario=$_GET["tipo_cuestionario"];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

db_query("DELETE FROM historial_educativo_cuestionarios$tipo_cuestionario WHERE id_cuestionario=$id_cuestionario");

db_close();

header("Location: /gestion/gabinete/seguimiento.php?id_alumno=$id_alumno");
?>