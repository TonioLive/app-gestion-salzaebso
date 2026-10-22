<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_admin.php");

$id_centro=$centro_actual;

$listaOpciones = array(
	"1" => "No asistirá",
	"2" => "Asistirá todos los días",
	"3" => "Asistirá algunos días",
);

$id_consulta=$_GET["id_consulta"];

$busca_consultas=db_query_params('SELECT * FROM consultas_padres WHERE id_consulta=?;', 's', array($id_consulta)) or die(db_error());

if (db_num_rows($busca_consultas)!=0) {

	$datos_consulta=db_fetch_array($busca_consultas);

	if ($datos_consulta["numero_respuestas_posibles"]==2) {
		$listaOpciones[2]="Asistirá";
	}

	$id_curso_contacto=$datos_consulta["id_curso"];

	// Busca el curso actual
	if (strlen($id_curso_contacto)!=0) {
		$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_contacto));

		if (db_num_rows($busca_curso_actual)!=0) {
			$datos_curso_actual=db_fetch_array($busca_curso_actual);
			$id_curso_contacto=$datos_curso_actual["id_curso"];
		} else {
			exit("No se encuentra el curso relacionado con la consulta buscada");
		}
	} else {
		exit("No se ha indicado el curso relacionado con la consulta buscada");
	}

} else {
	exit("No se encuentra la consulta buscada");
}
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Listado de respuestas recibidas a la consulta <?=$datos_consulta["nombre"]?></title>

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
  		<div class="panel-heading">
			<h3><a href="/gestion/respuestas_consultas" class="btn btn-default btn-md pull-right">Salir</a>Listado de respuestas recibidas a la consulta <?=$datos_consulta["nombre"]?> - Curso: <?=$datos_curso_actual["nombre"];?></h3>			
		</div>
  		<div class="panel-body">			
			<b>Consulta realizada entre los días:</b> <?=date("d/m/Y",strtotime($datos_consulta["fecha_inicio"]));?> y <?=date("d/m/Y",strtotime($datos_consulta["fecha_fin"]));?><br />
			<b>Pregunta realizada:</b> <?=$datos_consulta["texto_presentacion"]?><br /><br />
			<?php
			$totalRespuestas=0;
			$totalesOpciones=array(0,0,0,0);

			// Busca respuestas
			$busca_respuestas = db_query_params('SELECT * FROM consultas_padres_respuestas WHERE id_consulta=? AND id_alumno<>0 ORDER BY asistira;', 's', array($datos_consulta["id_consulta"]));

			while($respuesta=db_fetch_array($busca_respuestas)) {
				$totalRespuestas++;
				$totalesOpciones[$respuesta['asistira']]++;   
			}
			?>
			<b>Nº respuestas recibidas: </b><?=$totalRespuestas?><br />
			<b>Nº respuestas opción 1 (<?=$listaOpciones[1]?>): </b><?=$totalesOpciones[1]?><br />
			<b>Nº respuestas opción 2 (<?=$listaOpciones[2]?>): </b><?=$totalesOpciones[2]?><br />
			<?php
			if ($datos_consulta["numero_respuestas_posibles"]==3) {
			?>
			<b>Nº respuestas opción 3 (<?=$listaOpciones[3]?>): </b><?=$totalesOpciones[3]?><br />		
			<?php
			}

			// Busca aulas curso
			$consulta_aulas=db_query_params('SELECT * FROM centro_aulas WHERE tipo<3 AND id_curso=? AND id_centro=? ORDER BY nombre;', 'ss', array($id_curso_contacto, $id_centro)) or die(db_error());
			while($aulas=db_fetch_array($consulta_aulas)) {

				// Busca respuestas
				$busca_respuestas = db_query_params('SELECT * FROM consultas_padres_respuestas, usuarios, alumnos WHERE consultas_padres_respuestas.id_alumno=usuarios.id_usuario AND usuarios.id_usuario=alumnos.id_usuario AND consultas_padres_respuestas.id_alumno<>0 AND alumnos.aula=? AND consultas_padres_respuestas.id_consulta=? ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre;', 'ss', array($aulas["id_aula"], $datos_consulta["id_consulta"]));

				if (db_num_rows($busca_respuestas)!=0) {				

					$totalRespuestasAula=0;
                    $totalesOpcionesAula=array(0,0,0,0);

					while($respuesta=db_fetch_array($busca_respuestas)) {
                        $totalRespuestasAula++;
                        $totalesOpcionesAula[$respuesta['asistira']]++;  						
                    }

					?>
					<table class="table table-bordered table-striped table-hover">
						<tr>
							<td colspan=2 class="col-md-3"><h3>Aula: <?=$aulas["nombre"]?><h3></td>
							<td class="col-md-2"><b>Nº respuestas recibidas: </b><?=$totalRespuestasAula?></td>
							<td class="col-md-2"><b>Nº respuestas opción 1 "<?=$listaOpciones[1]?>": </b><?=$totalesOpcionesAula[1]?></td>
							<td class="col-md-2"><b>Nº respuestas opción 2 "<?=$listaOpciones[2]?>": </b><?=$totalesOpcionesAula[2]?></td>
							<td class="col-md-3"><?php if ($datos_consulta["numero_respuestas_posibles"]==3) { ?><b>Nº respuestas opción 3 "<?=$listaOpciones[3]?>": </b><?=$totalesOpcionesAula[3]?><?php } ?></td>
						</tr>
						<tr>
							<td class="col-md-1">&nbsp;</td>						
							<td class="col-md-2"><b>Alumno</b></td>
							<td class="col-md-2"><b>Fecha respuesta</b></td>						
							<td class="col-md-2"><b>Respondido por</b></td>
							<td class="col-md-2"><b>Respuesta</b></td>
							<td class="col-md-3"><b><?php
							if (strlen($datos_consulta["texto_pregunta_horario"])!=0) {
								echo($datos_consulta["texto_pregunta_horario"]);
							} else {
								if ($datos_consulta["numero_respuestas_posibles"]==3) {
									?>Días que asistirá y horario que realizará<?php } else { ?>Horario que realizará<?php } 
								}
							?></b></td>
						</tr>
					<?php
					// Busca respuestas
					$busca_respuestas = db_query_params('SELECT * FROM consultas_padres_respuestas, usuarios, alumnos WHERE consultas_padres_respuestas.id_alumno=usuarios.id_usuario AND usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=? AND consultas_padres_respuestas.id_consulta=? ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre;', 'ss', array($aulas["id_aula"], $datos_consulta["id_consulta"]));

					$contador=0;
					while($respuesta=db_fetch_array($busca_respuestas)) {
						
						// Busca datos padre
						$nombrePadre="Desconocido";

						if ($respuesta["id_usuario"]!=0) {

							$buscaPadre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?;', 's', array($respuesta["id_usuario"]));
							if (db_num_rows($buscaPadre)!=0) {
								$datos_padre=db_fetch_array($buscaPadre);
								$nombrePadre= $respuesta["Nombre"]." ".$respuesta["Apellido1"]." ".$respuesta["Apellido2"];
							}

						}

						$contador++;					
						?>
						<tr>
							<td><?=$contador;?></td>
							<td><?=$respuesta["Nombre"]?> <?=$respuesta["Apellido1"]?> <?=$respuesta["Apellido2"]?></td>
							<td><?=date("d/m/Y H:i",strtotime($respuesta["fecha_registro"]))?></td>
							<td><?=$nombrePadre?></td>
							<td><?=$listaOpciones[$respuesta["asistira"]]?></td>					
							<td><?=$respuesta["horario_deseado"]?></td>					
						</tr>
						<?php				
						
					} // Bucle respuestas
				} // If respuestas				
			} // Bucle aulas		
			?>
			</table>
		</div>
	</div>    
</div>
</body>
</html>