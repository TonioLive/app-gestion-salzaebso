<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_admin.php");
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$nombre_centro;?> - Listado de consultas a los padres</title>

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
			<h3><a href="/" class="btn btn-default btn-md pull-right">Salir</a>Listado de consultas realizadas a los padres</h3>
		</div>
  		<div class="panel-body">
			<?php

            $id_centro=$centro_actual;

			$contador=0;

            $busca_consultas=db_query_params('SELECT * FROM consultas_padres WHERE id_centro=? ORDER BY fecha_inicio;', 's', array($id_centro)) or die(db_error());

			if (db_num_rows($busca_consultas)!=0) {
				
				?>
				<table class="table table-bordered table-striped table-hover">
					<tr>
						<td>&nbsp;</td>						
						<td>Nombre</td>
						<td>Fecha inicio</td>						
						<td>Fecha fin</td>
						<td>Nº respuestas recibidas</td>
                        <td>Nº respuestas opción 1 (No)</td>
                        <td>Nº respuestas opción 2 (Si)</td>
                        <td>Nº respuestas opción 3 (Algunos días. En caso de que se haya consultado)</td>
						<td>&nbsp;</td>	
					</tr>
				<?php
				$contador=0;
				while($datos_consulta=db_fetch_array($busca_consultas)) {
					
					$contador++;
					
                    $totalRespuestas=0;
                    $totalesOpciones=array(0,0,0,0);

                    // Busca respuestas
                    $busca_respuestas = db_query_params('SELECT * FROM consultas_padres_respuestas WHERE id_consulta=? AND id_alumno<>0 ORDER BY asistira;', 's', array($datos_consulta["id_consulta"]));

                    while($respuesta=db_fetch_array($busca_respuestas)) {
                        $totalRespuestas++;
                        $totalesOpciones[$respuesta['asistira']]++;   
                    }

					?>
					<tr>
						<td><?=$contador;?></td>				
						<td><?=$datos_consulta["nombre"]?></td>
						<td><?=date("d/m/Y",strtotime($datos_consulta["fecha_inicio"]));?></td>
						<td><?=date("d/m/Y",strtotime($datos_consulta["fecha_fin"]));?></td>
						<td class="text-right"><?=$totalRespuestas?></td>
						<td class="text-right"><?=$totalesOpciones[1];?></td>
						<td class="text-right"><?=$totalesOpciones[2];?></td>
						<td class="text-right"><?=$totalesOpciones[3];?></td>
						<td><a href="listado_respuestas.php?id_consulta=<?=$datos_consulta["id_consulta"]?>" class="btn btn-sm btn-primary">Ver listado de respuestas</a></td>						
					</tr>
					<?php				
					
				}
				
			}
			
			?>
			</table>
		</div>
	</div>    
</div>
</body>
</html>