<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_usuario=$_POST["id_usuario"];
$texto_nota=$_POST["texto_nota"];

if (strlen($texto_nota)!=0) {
	$texto_nota=db_real_escape_string($texto_nota);
}

$inserta_nota=db_query("INSERT INTO alumnos_seguimiento (id_usuario,introducido_por,nombre_introductor,comentarios) VALUES ('$id_usuario','$idUsuario','$nombre_completo','$texto_nota');") or die(db_error());

header("Location: detalles_alumno.php?id_usuario=$id_usuario");

?>