<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

$centro_enviado=$centro_actual;

$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$centro_enviado");
if (db_num_rows($busca_datos_centro)!=0) {
		$datos_centro=db_fetch_array($busca_datos_centro);
		$nombre_centro=$datos_centro["nombre_centro"];
		$poblacion_centro=$datos_centro["poblacion_centro"];
		$tipo_centro=$datos_centro["tipo_centro"];
}

$id_curso_contacto = $_GET["id_curso"];

if (strlen($id_curso_contacto)==0) {
	$id_curso_contacto=33;	
}

?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$nombre_centro;?> - Listado de solicitudes rellenadas en la web</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/src/chosen.css">
<link rel="stylesheet" href="/css/datepicker.css">
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
<script src="/src/chosen.jquery.js" type="text/javascript"></script>
<script src="/js/bootstrap-datepicker.js"></script>
<script src="/js/locales/bootstrap-datepicker.es.js"></script>
<script src="/inc/validacion_fechas.js" type="text/javascript"></script>

</head>
<body>
<div class="container-fluid">
	<div class="panel panel-primary">
  		<div class="panel-heading"><a href="/" class="btn btn-danger pull-right"><i class="fa fa-sign-out" aria-hidden="true"></i></a><h3>Escuela Infantil <?=$nombre_centro;?> - Solicitudes de inscripción rellenadas en la web</h3></div>
  		<div class="panel-body">
			<?php
			$contador=0;
			$busca_contactos=db_query("SELECT * FROM contactos_web,alumnos WHERE contactos_web.id_usuario_alumno=alumnos.id_usuario AND alumnos.id_curso=30 AND contactos_web.id_curso=$id_curso_contacto AND id_centro=$centro_enviado ORDER BY alumnos.aula,Apellido1,Apellido2,Nombre;") or die(db_error());
						
			if (db_num_rows($busca_contactos)!=0) {
				
				?>
				<table class="table table-bordered table-striped table-hover">
					<tr>
						<td>&nbsp;</td>
						<td>Fecha Contacto</td>
						<td>Nombre Alumno</td>
						<td>Aula curso anterior</td>
						<td>Horario</td>
						<td>Mensualidad</td>						
						<td>Comida</td>
						<td>Merienda</td>
						<td>Horas extra</td>
						<td>Nº Horas extra</td>
						<td>&nbsp;</td>	
					</tr>
				<?php
				
				while($datos_alumno=db_fetch_array($busca_contactos)) {
					
					$contador++;
					
					// Busca datos curso actual
					if ($datos_alumno["id_usuario_alumno"]!=0) {
						
						$busca_alumnos = db_query("SELECT *,cursos_academicos.nombre as nombre_curso FROM alumnos,cursos_academicos,centro_aulas WHERE  alumnos.id_curso=cursos_academicos.id_curso AND alumnos.aula=centro_aulas.id_aula AND alumnos.id_alumno=".$datos_alumno["id_alumno"].";") or die(db_error());
						if (db_num_rows($busca_alumnos)!=0) {
							$historico_alumno=db_fetch_array($busca_alumnos);
						}
					?>
					<tr>
						<td><?=$contador;?></td>
						<td><?=date("d/m/Y",strtotime($datos_alumno["fecha_contacto"]))?></td>
						<td><?=$datos_alumno["Nombre"]?> <?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?></td>
						<td><?=$historico_alumno["nombre"]?> - <?=$historico_alumno["nombre_curso"]?></td>
						<td><?=date("d/m/Y",strtotime($datos_alumno["fecha_nacimiento"]))?></td>
						<td><?=str_replace("_"," ",$datos_alumno["mensualidad"]);?></td>						
						<td><?=$datos_alumno["comida"]?></td>
						<td><?=$datos_alumno["merienda"]?></td>
						<td><?=$datos_alumno["horas_extra"]?></td>
						<td><?=$datos_alumno["numero_horas_extra"]?></td>
						<td><a href="/alumnos/nuevo_alumno4.php?u=<?=$datos_alumno["id_usuario_alumno"]?>" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-user" aria-hidden="true"></i></a> <a href="ver_contacto.php?id_contacto=<?=$datos_alumno["id_contacto"]?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>						
					</tr>
					<?php
					}				
					
				}
				
			}
			
			?>
			</table>
		</div>
	</div>    
</div>
</body>
</html>