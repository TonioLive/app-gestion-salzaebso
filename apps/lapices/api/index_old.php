<?php
// die();

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 166400'); 
}

if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
	// Solo admitimos HTTPS
	$json['status'] = '403.4';
	$json['message'] = 'SSL required';
	$json['error'] = "Acceso limitado a conexiones SSL";
} else {

	require("JWT.php");
	require("../configuracion/config.php");
	require("autenticacion.php");

	if ($json['status'] == "202") {

		if (strlen($_GET["id_curso"])!=0) {
	
			$id_curso_actual=$_GET["id_curso"];
		
		} else {

			$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

			if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

		}

		// Procede a mostrar la informacion solicitada
		$solicitud = $_GET["solicitud"];
		if ($solicitud) {
			include ('modulos/'.$solicitud.".php");                                 
		}
	
	}
}
header('Content-Type: application/json');
echo(json_encode($json));
?>