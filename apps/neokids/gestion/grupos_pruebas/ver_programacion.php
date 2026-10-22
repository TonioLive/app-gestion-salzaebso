<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$nombre_mostrar=utf8_decode($_GET["nombre_mostrar"]);


$colores_niveles=array(""," text-success"," text-info"," text-danger");

$busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=? AND id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

$datos_subgrupo=db_fetch_array($busca_datos_subgrupo);

// Busca monitores
$lista_monitores_grupo="";
 $busca_monitores=db_query_params('SELECT * FROM aulas_grupos_monitores,usuarios WHERE aulas_grupos_monitores.id_usuario=usuarios.id_usuario AND aulas_grupos_monitores.id_grupo=? AND aulas_grupos_monitores.id_subgrupo=?', 'ss', array($id_grupo, $id_subgrupo)) or die(db_error());

if (db_num_rows($busca_monitores)!=0) {
	$lista_monitores_grupo="<b>Monitor/a</b>";
	if (db_num_rows($busca_monitores)>1) {
		$lista_monitores_grupo="<b>Monitores/as</b>";
	}
	
	while($lista_monitores=db_fetch_array($busca_monitores)) {
		if ($lista_monitores["id_usuario"]!=0) {
			$lista_monitores_grupo.="<br />".$lista_monitores["Nombre"]." ".$lista_monitores["Apellido1"];
		}
	}
}

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Programación Grupo</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">

</head>
<body>
<div class="container-fluid">
<br />
<div class="panel panel-default">
  <div class="panel-heading"><h2><?=urldecode($nombre_mostrar);?>, Grupo <?=$_GET["id_subgrupo"]+1;?></h2>
	<?=$lista_monitores_grupo?>
  </div>
  <div class="panel-body">
	<?php 
	// Busca si hay programación
	
	$busca_programacion=db_query_params('SELECT * FROM agenda_programacion LEFT JOIN agenda_programacion_actividades_piscina ON agenda_programacion.id_actividad=agenda_programacion_actividades_piscina.id_actividad LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_actividades_piscina.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo  WHERE id_grupo=? AND id_subgrupo=? AND fecha=? ORDER BY agenda_programacion.id_prog', 'sss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha))));
	
	$busca_programacion_plantillas=db_query("SELECT * FROM agenda_programacion_grupos_vinculados LEFT JOIN agenda_programacion_plantillas ON agenda_programacion_grupos_vinculados.id_plantilla=agenda_programacion_plantillas.id_plantilla WHERE id_grupo=$id_grupo AND id_subgrupo=".($id_subgrupo+1)." AND fecha_inicio<='".date("Y-m-d",$fecha)."'");
	

	$hay_plantillas=false;
	$lista_actividades_programadas=array();
	if (db_num_rows($busca_programacion_plantillas)!=0) {
		
		$fecha_actual=$fecha+32401;
	
		while($programacion_plantillas=db_fetch_array($busca_programacion_plantillas)) {
			// Comprueba fechas
			$id_plantilla=$programacion_plantillas["id_plantilla"];
			$dia_prog=strtotime($programacion_plantillas["fecha_inicio"]." 19:00"); 
			
			$num_sesiones=$programacion_plantillas["num_sesiones"];
			
			if ($programacion_plantillas["periodicidad"]==1) {
						
				// Mensual
				$fecha_inicio=$dia_prog;
				$mes_inicio=date("n",$dia_prog);
				$anio_inicio=date("Y",$dia_prog);
				
				$mes_fin=$mes_inicio+($num_sesiones-1);
				$anio_fin=$anio_inicio;
				
				
				if ($mes_fin>12) {
				
					$mes_fin=$mes_fin-12;
					$anio_fin++;
				
				}
				
				$fecha_fin_previo=strtotime($anio_fin."-".$mes_fin."-01");
				$fecha_fin=strtotime($anio_fin."-".$mes_fin."-".date("t",$fecha_fin_previo));				
				
				if (($fecha_actual>=$fecha_inicio) and ($fecha_actual<=$fecha_fin)) {
				
					// Estamos dentro del periodo, buscamos cual es la semana actual
					$mes_actual=date("n",$fecha_actual);
					
					$sesion_actual=$mes_actual-$mes_inicio+1;
					
					if ($sesion_actual<0) {
					
						$sesion_actual=$sesion_actual+12;
					
					}
									
				}
								
			} else {
				// Semanal
				for($c_semanas=1;$c_semanas<=$num_sesiones;$c_semanas++) {
										
					if (date("Y-m-d",$fecha+28800)==date("Y-m-d",$dia_prog)) {
					
						$sesion_actual=$c_semanas;
					
					}
					
					$dia_prog = $dia_prog + (86400*7);					
					
				}												
			}
			
			if ($sesion_actual) {
				
				// Busca programación sesion actual										
				$busca_sesion_actual=db_query("SELECT * FROM agenda_programacion_plantillas_actividades LEFT JOIN agenda_programacion_actividades_piscina ON agenda_programacion_plantillas_actividades.id_actividad=agenda_programacion_actividades_piscina.id_actividad LEFT JOIN agenda_programacion_objetivos_piscina ON agenda_programacion_actividades_piscina.id_objetivo=agenda_programacion_objetivos_piscina.id_objetivo  WHERE agenda_programacion_plantillas_actividades.id_plantilla=$id_plantilla AND agenda_programacion_plantillas_actividades.numero_sesion=$sesion_actual ORDER BY agenda_programacion_plantillas_actividades.orden_actividad,agenda_programacion_plantillas_actividades.id_prog");	
					
				if (db_num_rows($busca_sesion_actual)!=0) {
					
					$hay_plantillas=true;
						
					}
			}
									
		}			
	}

	if ((db_num_rows($busca_programacion)!=0) or ($hay_plantillas==true)) {
	
	?>
	<h4>Programación día <?=date("d/m/Y",$fecha);?></h4>
	
    <table class="table table-condensed table-striped">           	
	<?php 
	
	$objetivo_actual=0;
	
	$contador=0;
	
	while($programacion=db_fetch_array($busca_programacion)){
		
		$contador++;
		
		if ($programacion["id_objetivo"]!=$objetivo_actual) {
				?>
				<tr>
					<td colspan="2" class="text-warning"><?=$programacion["nombre_objetivo"];?></h3>
				</tr>
				<?php 
				$objetivo_actual=$programacion["id_objetivo"];
			}
	
		// Busca imagen
		$target = $web_path."/gestion/grupos_pruebas/imagenes";
		if (file_exists($target."/".$programacion["id_actividad"].".jpg")) {
			?>
			<tr>
								
				<td class="col-md-9">
					<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
				</td>	
				<td class="col-md-3">
					<a href='javascript:mostrar_imagen(<?=$programacion["id_actividad"]?>,"<?=$programacion["descripcion_actividad"];?>");'><img width='100%' src='imagenes/<?=$programacion["id_actividad"];?>.jpg' border=0></a>
				</td>	
						
			</tr>
			<?php 
		} else {		
		?>
		<tr>
			<td class="col-md-9">
				<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
			</td>	
			<td class="col-md-9">
			</td>		
		</tr>
		<?php 		
		}
		
		
		}
	
	// Hay plantillas programadas
	if ($hay_plantillas==true) {
	
	$objetivo_actual=0;
	
	while($programacion=db_fetch_array($busca_sesion_actual)){
		
		$contador++;
		
		if ($programacion["id_objetivo"]!=$objetivo_actual) {
				?>
				<tr>
					<td colspan="2" class="text-warning"><?=$programacion["nombre_objetivo"];?></h3>
				</tr>
				<?php 
				$objetivo_actual=$programacion["id_objetivo"];
			}
	
		// Busca imagen
		$target = $web_path."/gestion/grupos_pruebas/imagenes";
		if (file_exists($target."/".$programacion["id_actividad"].".jpg")) {
			?>
			<tr>
								
				<td class="col-md-9">
					<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
				</td>	
				<td class="col-md-3">
					<a href='javascript:mostrar_imagen(<?=$programacion["id_actividad"]?>,"<?=$programacion["descripcion_actividad"];?>");'><img width='100%' src='imagenes/<?=$programacion["id_actividad"];?>.jpg' border=0></a>
				</td>	
						
			</tr>
			<?php 
		} else {		
		?>
		<tr>
			<td class="col-md-9">
				<h4 class="<?=$colores_niveles[$programacion["nivel"]]?>">- <?=$programacion["descripcion_actividad"];?></h4>
			</td>	
			<td class="col-md-9">
			</td>		
		</tr>
		<?php 		
		}
		
		
		}
	
	}		
	// Final plantillas programadas
		
	?>
	</table>
	<?php 		
	} else {
	?>
	<h3 class="text-danger">No hay programación para el día seleccionado</h3>	
	<?php 
	}
	
	?>	
	<br />
	<button type="button" onclick="window.close();" class="btn btn-danger">Cerrar</button>
  </div>
</div>

</div>

<div class="modal modal-wide fade" id="imagenModal" tabindex="-1" role="dialog" aria-labelledby="imagenModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">  
		<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&nbsp;</span></button>
        <div id="nombreImagen"><h4 class="modal-title" id="grupoModalLabel">Título</h4></div>
      </div>     
      <div class="modal-body" id="imagen_actividad_modal">
        <img src="imagen/nada.jpg" border=0 witdh="100%">
      </div>
    </div>
  </div>
</div>  

<script>

function mostrar_imagen(id_actividad,nombre_imagen) {

	url = 'imagenes/'+id_actividad+".jpg";
            
	$('#nombreImagen').html('<h4 class="modal-title" id="grupoModalLabel">' + nombre_imagen + '</h4>'); 
	$('#imagen_actividad_modal').html("<a href='"+url+"' target='_blank'><img src='"+url+"' border=0 width='100%'></a>");

	$('#imagenModal').modal('show');

}

</script>

</body>
</html>
