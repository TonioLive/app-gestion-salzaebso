<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");



// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$nombres_niveles=array("Bebés","Nivel 1","Nivel 2","Nivel 3");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Programaciones</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/bootstrap-duallistbox.css" type="text/css">
<link rel="stylesheet" href="/src/chosen.css">

<style>

.objetivo_general {
    font-weight: bold;
}

.objetivo_especifico {
	font-weight: bold;
	font-style: oblique;
}

.actividad {

}

</style>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/src/jquery.bootstrap-duallistbox.js"></script>
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $(".chosen-select").chosen()
});	


function guardar() {
	$('input[name="retorno"]').val(0);
	document.monitores.submit();

}

function recargar() {
	$('input[name="retorno"]').val(1);
	document.monitores.submit();

}

function eliminar(id_plantilla) {

	if (confirm("Vas a eliminar la programación seleccionada, al hacerlo eliminas la programación definitivamente y no tiene vuelta atrás.\n\n¿Estás seguro/a?")) {
	
		window.location.href='borrar_plantilla.php?id_plantilla='+id_plantilla;
	
	}

}

function duplicar(id_plantilla) {

	if (confirm("Se va a crear una copia exacta de esta programación pero se eliminará el Monitor asignado.\n\n¿Estás seguro/a?")) {
	
		window.location.href='duplicar_plantilla.php?id_plantilla='+id_plantilla;
	
	}

}

</script>
</head>
<body>
<div class="container-fluid">
	<div class="panel panel-default">
  		<div class="panel-heading"><h3>Edición de programaciones</h3></div>
  		<div class="panel-body">
 			
 				<table class="table table-hover">
 					<tr>
 						<td>Monitor</td>
 						<td>Nombre programación</td>
 						<td>Nivel</td>
 						<td>Nº de sesiones</td>
 						<td>&nbsp;</td>
 					</tr>
 					<?php 
 					$busca_programaciones=db_query("SELECT * FROM agenda_programacion_plantillas LEFT JOIN usuarios ON agenda_programacion_plantillas.monitor=usuarios.id_usuario ORDER BY nivel, monitor") or die(db_error());
 					
 					while($datos_plantilla=db_fetch_array($busca_programaciones)) { 				 					
 						?>
 					<tr>
 						<td><?=$datos_plantilla["Nombre"]?> <?=$datos_plantilla["Apellido1"]?> <?=$datos_plantilla["Apellido2"]?></td>
 						<td><?=$datos_plantilla["descripcion"]?></td>
 						<td><?=$datos_plantilla["nivel"]?></td>
 						<td><?=$datos_plantilla["num_sesiones"]?></td>
 						<td><a type="button" class="btn btn-success" href="gestion_plantillas.php?id_plantilla=<?=$datos_plantilla["id_plantilla"]?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar</a> <button type="button" class="btn btn-primary" onclick="duplicar(<?=$datos_plantilla["id_plantilla"]?>);"><i class="fa fa-clone" aria-hidden="true"></i> Duplicar</button> <button type="button" class="btn btn-danger" onclick="eliminar(<?=$datos_plantilla["id_plantilla"]?>);"><i class="fa fa-trash" aria-hidden="true"></i> Eliminar</button></td>
 					</tr> 						
 						<?php 
 					}
 					?>
 				</table> 			
				<br /><a type="button" href="/" class="btn btn-danger">Salir</a> <a type="button" class="btn btn-success" href="gestion_plantillas.php">Nueva programación</a>
				<br /><br />
				</div>	    
		</div>
	</div> 
</div>        
</body>
</html>
