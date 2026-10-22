<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_ip_local.php");

$busca_qr=db_query("SELECT * FROM codigos_qr,contactos WHERE codigos_qr.unid=contactos.codigo_uso_unico ORDER BY codigos_qr.fecha DESC");

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

<script>
setInterval("location.reload()",5000);
</script>

</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><img src="/webcam/agenda/imagenes/la-pisicna-de-neo-kids.png" class="pull-right"><br /><br /><br /><h3>La Piscina de Neokids - Contactos</h3></div>
  		<div class="panel-body">
  		<?php 
  		if (db_num_rows($busca_qr)==0) {
  		?>
  		<div align="center"><h1>No hay códigos pendientes de mostrar</h1></div> 
  		<?php 
  		} else {
  		$datos_qr=db_fetch_array($busca_qr);
  		?>
		<iframe src="<?=$datos_qr["enlace_qr"];?>" name="frame1" scrolling="auto" frameborder="no" align="center" height = "600px" width = "600px"></frame>
		<?php 
		}
		?>
</iframe>
		</div>
	</div>    
</div>
</body>
</html>