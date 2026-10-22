<?php 
require("../configuracion/config.php");

$centro_enviado=$_GET["idc"];
if (strlen($centro_enviado)==0) {
	$centro_enviado=14;
}
$busca_datos_centro=db_query("SELECT * FROM gestionguarderias.centros WHERE id_centro=$centro_enviado");
if (db_num_rows($busca_datos_centro)!=0) {
		$datos_centro=db_fetch_array($busca_datos_centro);
		$nombre_centro=$datos_centro["nombre_centro"];
		$poblacion_centro=$datos_centro["poblacion_centro"];
		$tipo_centro=$datos_centro["tipo_centro"];
}

$urlRetorno="/webcam/inscripcion_curso.php";
if ($_SESSION['permitido_padre']!=true) {
	$urlRetorno="https://www.neo-kids.es";	
}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?=$nombre_centro;?> - Inscripción Curso</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/chosen.css">
<link rel="stylesheet" href="/css/datepicker.css">
<style type="text/css">
.myField{
    width:25%;
    display:inline-block;

	}
.myField75{
    width:50%;
    display:inline-block;
	}
	
.btn-xlarge {
    padding: 18px 28px;
    font-size: 22px; //change this to your desired size
    line-height: normal;
    -webkit-border-radius: 8px;
       -moz-border-radius: 8px;
            border-radius: 8px;
}	
</style>	


<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script src="/js/bootstrap-datepicker.js"></script>
<script src="/js/locales/bootstrap-datepicker.es.js"></script>
<script src="/inc/validacion_fechas.js" type="text/javascript"></script>
</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><h3>Escuela Infantil <?=$nombre_centro;?> - Solicitud inscripción</h3></div>
  		<div class="panel-body">
		<div class="text-center">
        <img src="logo-english-club.png" style="max-width:350px">
		<br />
		<h3>La información ha sido enviada correctamente, gracias por su colaboración.</h3>
		<br /><br />
		<button type="button" onclick="window.location.href='<?=$urlRetorno?>'" class="btn btn-primary btn-lg">Salir</button>
		<br /><br />
		<br />
		</div>    
		</div>
	</div>    
</div>
</body>
</html>