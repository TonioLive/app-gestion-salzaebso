<?php  require("../configuracion/config.php"); 
require("../configuracion/autentifica_padre.php");

$id_encuesta=$_GET["id_encuesta"];

$introduce_resgistro_encuesta=db_query("INSERT INTO respuestas_encuestas_padres (id_encuesta, id_padre) VALUES ('$id_encuesta','$id_usuario');");        
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<title>Encuesta Summer School</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</head>
<body>
<div align="center">
        <br>
        <h3>Gracias por responder a nuestra encuesta</h3>
        <br /><br />
        <button type="button" class="btn btn-default" onclick="parent.$('#myModal').modal('hide');">Cerrar Encuesta</button>
</div>
</body>
</html>        
