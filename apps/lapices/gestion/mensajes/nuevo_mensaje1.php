<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_docente.php");


$id_alumno=$_POST["id_alumno"];
$destinatario=$_POST["destinatario"];
$asunto=$_POST["asunto"];
$texto_mensaje=$_POST["texto_mensaje"];;


	$asunto=db_real_escape_string($asunto);
	$texto_mensaje=db_real_escape_string($texto_mensaje);
	$tipo_mensaje=2;
			
	$inserta_mensaje=db_query("INSERT INTO agenda_notas_padres (id_alumno,id_remitente,tipo_nota,asunto,texto_nota,fecha,hora,id_centro_alumno) VALUES ('$id_alumno','$idUsuario','$tipo_mensaje','$asunto','$texto_mensaje','".date("Y-m-d")."','".date("H:i:s")."','$centro_actual');") or die(db_error());


header("Location: mensajeria.php?id_alumno=$id_alumno");

?>