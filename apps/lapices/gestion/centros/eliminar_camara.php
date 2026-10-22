<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/autentifica_admin.php");

$id_camara=$_GET["id_webcam"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

db_query("DELETE FROM webcam WHERE id_webcam=".$id_camara) or die(db_error()."-2");

db_close();
mt_srand((double)microtime()*1000000);
header("Location: listado_camaras.php");
?>
