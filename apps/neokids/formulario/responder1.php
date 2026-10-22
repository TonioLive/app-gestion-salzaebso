<?php

// Ejemplo URL: http://gestion.neo-kids.es/formulario/respuesta.php?ida=5219&idu=9490&emp=luis@doassans.es&opt=1&idc=1

require("../configuracion/config.php"); 

$id_consulta = $_GET["idc"];
$id_alumno = $_GET["ida"];
$id_usuario = $_GET["idu"];


// comprueba coerencia entre id_alumno e id_usuario
$comprueba = db_query_params('SELECT * FROM alumnos WHERE id_alumno=? AND id_usuario=?', 'ss', array($id_alumno, $id_usuario));

if (db_num_rows($comprueba)!=0) {	
	// Inserta respuesta
	// OK 
} else {
	// Vete de aquí, no te queremos 
	exit("Error: Los datos enviados no son coerentes.");
}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>La piscina de Neokids - Consultas</title>

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
		<div class="text-left">
		<br />
<?php

$busca_datos_consulta=db_query_params('SELECT * FROM consultas_email WHERE id_consulta=?', 's', array($id_consulta));
if (db_num_rows($busca_datos_consulta)!=0) {
		$datosConsulta=db_fetch_array($busca_datos_consulta);
		$nombre_consulta=$datosConsulta["nombre_consulta"];
		$pregunta_consulta=$datosConsulta["pregunta_consulta"];
		$opciones_consulta=security_unserialize($datosConsulta["opciones"]);	
}

?><h3><?=utf8_encode($pregunta_consulta)?></h3>
<?php
		foreach($opciones_consulta as $clave => $valor) {
		?>
		<a href="respuesta.php?idu=<?=$id_usuario?>&ida=<?=$id_alumno?>&idc=<?=$id_consulta?>&emp=Telefono&opt=<?=$clave?>"><big>- <?=utf8_encode($valor)?></big></a>
		<br /><br />
		<?php
		}
		?>
		<br />
		</div>    
		</div>
	</div>    
</div>
</body>
</html>