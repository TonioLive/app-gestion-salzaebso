<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_plantilla=$_GET["id_plantilla"];


// Colores niveles
$colores_niveles=array(""," text-success"," text-info"," text-danger");
$nombres_niveles=array("Bebés","Nivel 1","Nivel 2","Nivel 3");
$colores_niveles_general=array("panel-default"," panel-success"," panel-info"," panel-danger");

$descripcion="";
$num_sesiones=0;
$nivel=0;

if ($id_plantilla) {

	$busca_datos=db_query_params('SELECT * FROM agenda_programacion_plantillas WHERE id_plantilla=?', 's', array($id_plantilla));
	
	if (db_num_rows($busca_datos)!=0) {
	
		$datos_plantilla=db_fetch_array($busca_datos);
		
		$descripcion=$datos_plantilla["descripcion"];
		$nivel=$datos_plantilla["nivel"];
		$num_sesiones=$datos_plantilla["num_sesiones"];
		$monitor=$datos_plantilla["monitor"];
			
	} else {
	
	$id_plantilla=0;
	
	}
	

}


?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Editar Programaciones</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
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

function muestra_sesion(id_sesion) {

	$('input[name="retorno"]').val(2);
	$('input[name="sesion_editada"]').val(id_sesion);
	document.monitores.submit();	

}

</script>
</head>
<body>
<div class="container-fluid">
	<div class="panel panel-default">
  		<div class="panel-heading"><h3>Edición de programaciones</h3></div>
  		<div class="panel-body">
    		<form method="post" action="guarda_plantilla.php" name="monitores">
    			<input type="hidden" value="<?=$id_plantilla?>" name="id_plantilla">
    			<input type="hidden" value="0" name="retorno" id="retorno">
    			<input type="hidden" value="0" name="sesion_editada" id="sesion_editada">   
    			<h4>Monitor</h4> 
    			<select name="monitor" class="form-control" required><option value=""></option>
    				<?php 
					$busca_docentes=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion=4 ORDER BY Apellido1, Apellido2, Nombre;");
					while($datos_docentes=db_fetch_array($busca_docentes)) {
					?>
					<option value="<?=$datos_docentes["id_usuario"];?>"<?php  if ($datos_docentes["id_usuario"]==$monitor) { print " selected"; } ?>><?=$datos_docentes["Nombre"];?> <?=$datos_docentes["Apellido1"];?> <?=$datos_docentes["Apellido2"];?></option>
					<?php 
					}
  					?>
				</select>
    			 			
    			<h4>Nombre programación</h4> 
    			<input type="text" name="descripcion" id="descripcion" required placeholder="Nombre o descripción de la programación" class="form-control" value="<?=$descripcion?>"> 
    			<br />
    			<h4>Nivel</h4>
				<select data-placeholder="Nivel" name="nivel" id="nivel" required tabindex=2 class="form-control">
					<?php 
					for ($niveles=0;$niveles<=3;$niveles++) {
				
						
					$color_fila=$colores_niveles[$niveles];
						
					?>
					<option value="<?=$niveles?>"<?=$color_fila?><?php  if ($niveles==$nivel) { print " selected"; } ?>><?=$nombres_niveles[$niveles];?></option>
					<?php 
        			}
					?>
    			</select>
    			<h4>Número de sesiones</h4>
				<select data-placeholder="Número de sesiones" name="num_sesiones" required id="num_sesiones" tabindex=3 class="form-control" onchange="recargar();">
					<?php 
					for ($nums_sesiones=1;$nums_sesiones<=50;$nums_sesiones++) {						
					?>
					<option value="<?=$nums_sesiones?>"<?php  if ($nums_sesiones==$num_sesiones) { print " selected"; } ?>><?=$nums_sesiones;?></option>
					<?php 
        			}
					?>
    			</select>
    			<?php 
    			if ($num_sesiones>0) {
    			?>
    			<h4>Editar sesiones</h4>
    			<table class="table">
    			<?php     		
    			$columna_actual=0;
    			for ($sesiones=1;$sesiones<=$num_sesiones;$sesiones++) {
    				if ($columna_actual==0) {
    				?>
    				<tr>
    				<?php 
    				}
    				
    				// Comprueba si está programada
    				$busca_programacion=db_query("SELECT * FROM agenda_programacion_plantillas_actividades WHERE id_plantilla=$id_plantilla AND numero_sesion=$sesiones");
    				
    				$color_boton="danger";
    				
    				if (db_num_rows($busca_programacion)!=0) {
    					$color_boton="success";
    				}
    				
    				?>
    				<td class="col-md-2 text-center">
 						<button type="button" class="btn btn-<?=$color_boton?>" onclick="muestra_sesion(<?=$sesiones?>)">Sesión nº <?=$sesiones?></button>
 					</td>
    				<?php 
    				$columna_actual++;
    				if ($columna_actual==6) {
	    				$columna_actual=0;
    					?>
    				</tr>
    					<?php 
    				}
    			}
    			?>
    			</table>  
    			<?php 
    			}
    			?>		
				<div>
				<br /><a type="button" href="gestion_programaciones.php" class="btn btn-danger">Salir</a> <button type="button" class="btn btn-success" onclick="guardar();">Guardar y salir</button> <button type="button" class="btn btn-info" onclick="programar();">Copiar programación a un grupo</button>
				<br /><br />
				</div>	 
    		</form>      
		</div>
	</div> 
</div>        
</body>
</html>
