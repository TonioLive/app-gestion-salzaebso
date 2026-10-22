<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_docente.php");


$id_mensaje=$_POST["id_mensaje"];
$id_alumno=$_POST["id_alumno"];
$respuesta=$_POST["respuesta"];

if (strlen($respuesta)!=0) {

	$respuesta=db_real_escape_string($respuesta);
	
	$inserta_respuesta=db_query("INSERT INTO agenda_notas_padres_respuestas (id_nota,respondido_por,fecha_respuesta,hora_respuesta,texto_respuesta,direccion) VALUES ('$id_mensaje','$idUsuario','".date("Y-m-d")."','".date("H:i:s")."','$respuesta',1);") or die(db_error());

	}

header("Location: ver_mensaje.php?id_alumno=$id_alumno&id_mensaje=$id_mensaje");

?>