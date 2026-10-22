<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_docente.php");


$id_mensaje=$_POST["id_mensaje"];
$id_alumno=$_POST["id_alumno"];
$respuesta=$_POST["respuesta"];

if (strlen($respuesta)!=0) {

	$respuesta=db_real_escape_string($respuesta);
	
	$inserta_respuesta=db_query("INSERT INTO agenda_notas_padres_respuestas (id_nota,respondido_por,fecha_respuesta,hora_respuesta,texto_respuesta,direccion) VALUES ('$id_mensaje','$idUsuario','".date("Y-m-d")."','".date("H:i:s")."','$respuesta',1);") or die(db_error());

	// Inserta notificaciones app movil
	// Busca padres
	$padresAlumno = db_query_params('SELECT * FROM padres WHERE padre_de=?;', 's', array($id_alumno));
	while($datosPadres=db_fetch_array($padresAlumno)) {

		// Busca TOKENS
		$buscaTokens = db_query_params('SELECT * FROM tokens_fcm WHERE id_usuario=? GROUP BY FCMtoken;', 's', array($datosPadres['id_usuario']));
		if (db_num_rows($buscaTokens)!=0) {
			// Inserta Notificación
			$nuevaNotificacion = db_query_params('INSERT INTO notificaciones_app (fecha_envio,destino,tipo,titulo,mensaje,enviada,id_mensaje) VALUES (?,?,\'token\',\'Escuela Infantil\',\'Tienes una nueva respuesta a un mensaje de la escuela\',0,?);', 'sss', array((string)(date('Y-m-d')), (string)($datosPadres['id_usuario']), (string)($id_mensaje)));
		}

	}

	}

header("Location: ver_mensaje.php?id_alumno=$id_alumno&id_mensaje=$id_mensaje");

?>