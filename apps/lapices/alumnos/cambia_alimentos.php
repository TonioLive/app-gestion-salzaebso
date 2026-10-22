<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Recibimos los datos
$id_usuario_seleccionado=$_GET["id_usuario_seleccionado"];
$alimento=$_GET["alimento"];

$consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);
$datos_usuario=db_fetch_array($consulta_datos_usuario);

$nuevo_valor=1;
if ($datos_usuario[$alimento]==1) {
	$nuevo_valor=0;
}

db_query("UPDATE usuarios SET $alimento=$nuevo_valor WHERE id_usuario=$id_usuario_seleccionado") or die(db_error());

header("Location: ficha_alumno.php?u=".$id_usuario_seleccionado);

?>