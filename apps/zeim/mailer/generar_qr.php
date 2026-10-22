<?php
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require 'PHPMailerAutoload.php';

$id_contacto=$_GET["id_contacto"];

$busca_datos_contacto=db_query_params('SELECT * FROM contactos WHERE id_contacto=?', 's', array($id_contacto));

if (db_num_rows($busca_datos_contacto)!=0) {

$datos_contacto=db_fetch_array($busca_datos_contacto);

	$fecha_actual=strtotime(date("Y-m-d"));

	$mail_webmaster="info@neo-kids.es";

	$d=date ("d");
	$m=date ("m");
	$y=date ("Y");
	$t=time();
	$dmt=$d+$m+$y+$t;
	$ran= rand(0,10000000);
	$dmtran= $dmt+$ran;
	$un= uniqid();
	$dmtun = $dmt.$un;
	$mdun = md5($dmtran.$un);


	// Inserta codigo único en ficha usuario

	$inserta_codigo=db_query_params('UPDATE contactos SET codigo_uso_unico=? WHERE id_contacto=?', 'ss', array((string)($mdun), $id_contacto));

	// Inserta registro codigos QR creados
	$nombre_alumno=$datos_contacto["Nombre"]." ".$datos_contacto["Apellido1"]." ".$datos_contacto["Apellido2"];
		
	$enlace_qr="http://gestion.neo-kids.es/mailer/phpqrcode/index.php?nombre_alumno=".$nombre_alumno."&level=H&size=8&data=".urlencode("http://gestion.neo-kids.es/formulario/index.php?&unid=".$mdun);

	$inserta_registro_qr=db_query_params('INSERT INTO codigos_qr (id_contacto,enlace_qr,unid) VALUES (?,?,?);', 'sss', array((string)($id_contacto), (string)($enlace_qr), (string)($mdun)));

	// Muestra codigo QR
	
	header("Location: ".$enlace_qr);

} else {
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>La piscina de Neokids - Nuevo Contacto</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<style type="text/css">
.myField{
    width:32%;
    display:inline-block;

	}
.myField75{
    width:50%;
    display:inline-block;
	}
</style>	

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" class="pull-right"><br /><br /><br /><h3>La Piscina de Neokids - Contactos</h3></div>
  		<div class="panel-body">
    	No se encuentra el contacto solicitado
		<br><br>
		<button type="button" onclick="window.location.href='/contactos/listado_contactos.php'" class="btn btn-danger btn-lg">Salir</button>
		</div>
	</div>    
</div>
</body>
</html>
<?php 
}
?>