<?php 
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
// die("Opcion desactivada");


$longitud_mensaje=500;

$listado_emails=$_POST["email"];    
$tipo=$_POST["tipo"];

$lista_emails_destino=array();

if ($listado_emails) {

foreach ($listado_emails as $fila) {

	if ($tipo==0) {
		$busca_datos_alumno=db_query("SELECT * FROM usuarios WHERE id_usuario=".$fila);
	} else {
		$busca_datos_alumno=db_query("SELECT * FROM contactos WHERE id_contacto=".$fila);
	}
	
	$datos_alumno=db_fetch_array($busca_datos_alumno);
		
	// Array con todos los emails del alumno

	$listado_emails=str_replace(" ","",$datos_alumno["email"]);

	$array_to = explode(",",$listado_emails);
	
	$lista_emails="";
        
	foreach ($array_to as $to) {        
        
		if (comprobar_email($to)!=0) {
		
			// Comprueba si está en la lista negra
			$busca_lista=db_query_params('SELECT * FROM no_recibir_mail WHERE email=?', 's', array((string)($to)));
			
			if (db_num_rows($busca_lista)==0) {
				if (!in_array($to,$lista_emails_destino)) {
		
	    	        $lista_emails_destino[]=$to;
					$num_emails_enviar++;
				
					}			
				}
				                    
			}
		} 	

	}
}

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Enviar Email a Grupo</title>
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

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<link rel="stylesheet" href="style.css">

<style>
.fondo_nivelB {
        background: #000;
        }                        
 
.textoBlanco {
                color: #fff;
                } 
                
.panelFijo{
    height: 1000vh;
    overflow-y: no;
    overflow-x: hidden;
}
 
.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 
</style>

</head>
<body>
<form method="POST" name="detalles_grupo" action="enviar_email_todos1.php" target="mensajes_alumnos">
<input type="hidden" name="id_grupo" value="<?=$_POST["id_grupo"];?>">
<input type="hidden" name="id_subgrupo" value="<?=$_POST["id_subgrupo"];?>">
<input type="hidden" name="fecha_limite" value="<?=$_POST["fecha_limite"];?>">
<input type="hidden" name="listado_emails" value='<?=serialize($lista_emails_destino);?>'>

<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-heading"><h5><i class="fa fa-envelope" aria-hidden="true"></i> Se van a enviar <?=$num_emails_enviar?> mensajes de correo electrónico a los alumnos seleccionados.</h5></div>
	  	<div class="panel-body">
			<h6>Asunto</h6>
			<input type="text" name="asunto" id="asunto" class="form-control" required>
			<h6>Mensaje</h6>
			<textarea required class="form-control" rows="2" name="mensaje" id="mensaje" style="height: 245px;"></textarea>
			<button type="submit" class="btn btn-primary" style="margin-top:10px;"><i class="fa fa-envelope" aria-hidden="true"></i> Enviar</button>   	
  		</div>
	</div>

</div>
</form>

</body>
</html>