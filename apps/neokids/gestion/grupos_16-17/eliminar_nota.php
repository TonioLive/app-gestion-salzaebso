<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_usuario=$_GET["id_usuario"];
$id_nota=$_GET["id_nota"];


$borrar_nota=db_query_params('DELETE FROM alumnos_seguimiento WHERE id_nota=?;', 's', array($id_nota)) or die(db_error());

header("Location: detalles_alumno.php?id_usuario=$id_usuario");

?>