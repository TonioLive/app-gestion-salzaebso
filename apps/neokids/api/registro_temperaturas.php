<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Registro de temperaturas</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
         
</style> 
  
</head>
<body>
<div class="container-fluid">
	<a href="editar_consignas.php" class="pull-right btn btn-primary"><i class="fa fa-cog" aria-hidden="true"></i></a>
	<table class="table table-condensed table-striped table-bordered">
		<tr>
			<td>Fecha y Hora toma de datos</td>
			<td>Temperatura piscina</td>
			<td>Temperatura sala</td>
			<td>Humedad relativa</td>
			<td>Anotaciones</td>
		</tr>
		<?php 
		$busca_temperaturas=db_query("SELECT * FROM registro_temperaturas.registro_temperaturas ORDER BY fecha DESC LIMIT 0,200");
		while($datos_temperaturas=db_fetch_array($busca_temperaturas)) {
		
		$color_fila="";
		
		if (substr($datos_temperaturas["anotaciones"],0,6)=="ALERTA") {
		
			$color_fila=" class='text-danger'";	
			
		}
		
		if (substr($datos_temperaturas["anotaciones"],0,3)=="FIN") {
		
			$color_fila=" class='text-success'";	
			
		}					
			
	    ?>
		<tr<?=$color_fila?>>
    		<td><?=$datos_temperaturas["hora_toma_datos"];?></td>
    		<td class="text-right"><?=number_format($datos_temperaturas["temp_piscina"],2,",",".")?> ºC</td>
    		<td class="text-right"><?=number_format($datos_temperaturas["temp"],2,",",".")?> ºC</td>
    		<td class="text-right"><?=number_format($datos_temperaturas["humedad_rel"],2,",",".")?> %</td>
    		<td><?=$datos_temperaturas["anotaciones"]?></td>
		</tr>    			
	    <?php 
		}
		?>
	</table>                       
</div>

</body>
</html>