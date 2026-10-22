<?php
require __DIR__ . '/responder_mensaje_familias.php';
exit;
?>
<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");


$id_mensaje=$_POST["id_mensaje"];
$id_remitente=$_POST["id_remitente"];
$respuesta=$_POST["respuesta"];

if ((strlen($respuesta)!=0) and ($id_remitente==$id_usuario)) {

	$respuesta=db_real_escape_string($respuesta);
	
	$inserta_respuesta=db_query("INSERT INTO agenda_notas_padres_respuestas (id_nota,respondido_por,fecha_respuesta,hora_respuesta,texto_respuesta) VALUES ('$id_mensaje','$id_usuario','".date("Y-m-d")."','".date("H:i:s")."','$respuesta');") or die(db_error());

	}

header("Location: ver_mensaje.php?id_mensaje=$id_mensaje");

?>