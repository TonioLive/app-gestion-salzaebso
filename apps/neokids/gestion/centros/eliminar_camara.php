<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/autentifica_admin.php");

$id_camara=$_GET["id_webcam"];

$db=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db);

db_query_params('DELETE FROM webcam WHERE id_webcam=?', 's', array($id_camara)) or die(db_error()."-2");

db_close();
mt_srand((float)microtime()*1000000);
header("Location: listado_camaras.php");
?>
