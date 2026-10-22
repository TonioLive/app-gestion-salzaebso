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

	$id_nuevo_mensaje = db_insert_id();

	// Inserta notificaciones app movil
	// Busca padres
	$padresAlumno = db_query_params('SELECT * FROM padres WHERE padre_de=?;', 's', array($id_alumno));
	while($datosPadres=db_fetch_array($padresAlumno)) {

		// Busca TOKENS
		$buscaTokens = db_query_params('SELECT * FROM tokens_fcm WHERE id_usuario=? GROUP BY FCMtoken;', 's', array($datosPadres['id_usuario']));
		if (db_num_rows($buscaTokens)!=0) {
			// Inserta Notificación
			$nuevaNotificacion = db_query_params('INSERT INTO notificaciones_app (fecha_envio,destino,tipo,titulo,mensaje,enviada,id_mensaje) VALUES (?,?,\'token\',\'Escuela Infantil\',\'Tienes un nuevo mensaje de la escuela\',0,?);', 'sss', array((string)(date('Y-m-d')), (string)($datosPadres['id_usuario']), (string)($id_nuevo_mensaje)));
		}

	}

header("Location: mensajeria.php?id_alumno=$id_alumno");

?>