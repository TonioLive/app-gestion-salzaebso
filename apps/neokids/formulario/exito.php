<?php 
require("../configuracion/config.php");

$formulario_name="contactos";
$formulario_accion="contacto1.php";

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>La piscina de Neokids - Contactos</title>

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
  		<div class="panel-heading"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" class="pull-right"><br /><br /><br /><h3>La Piscina de Neokids</h3></div>
  		<div class="panel-body">
		<div class="text-center">
		<br />
		<h3>La información ha sido enviada correctamente, gracias por su colaboración.</h3>
		<br /><br />
		<button type="button" onclick="window.location.href='index.php'" class="btn btn-primary btn-lg">Salir</button>
		<br /><br />
		<br />
		</div>    
		</div>
	</div>    
</div>
</body>
</html>