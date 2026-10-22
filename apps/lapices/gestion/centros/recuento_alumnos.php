<?php require("../../configuracion/parte_superior_privado_bootstrap.php");

$verTodos=$_GET["ver_todos"];
?>
<div class="text-center">
<h3>Resumen número de alumnos por cursos y meses</h3>
<?php
$limite_cursos="";
if ($verTodos!=1) {
	$limite_cursos=" LIMIT 2";
?>
<p>Se muestran el curso actual y el anterior. Si quieres ver todos los cursos haz clic en el siguiente enlace: <a href="<?=$_SERVER['PHP_SELF']?>?ver_todos=1">Ver todos los cursos</a></p>
<?php
}

$tipoCurso=$_GET["tipo_curso"];
if (strlen($tipoCurso)==0) {
	$tipoCurso=0;
}

$filtroTipoCurso=" AND tipo_curso=".$tipoCurso;

$cursoInicial=$_GET["curso_inicial"];
$filtroCurso="";
if (strlen($cursoInicial)!=0) {
	$filtroCurso = " AND id_curso<=".$cursoInicial;	
} else {
	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE month(fecha_inicio)=9 AND year(fecha_inicio)<=".date("Y")."".$filtroTipoCurso." ORDER BY fecha_inicio DESC LIMIT 1");
	$datos_curso_actual=db_fetch_array($busca_curso_actual);
	$cursoInicial=$datos_curso_actual["id_curso"];
}

$listadoCentros = array(6 => 'GA', 7 => 'VB', 8 => 'RN', 13 => 'FC');
$encabezadoCentros = '';
foreach ($listadoCentros as $id_centro => $nombre_centro) {
	$encabezadoCentros.= '<td><small><b>'.$nombre_centro.'</b></small></td>';
}

?>
<form method="get" action="<?=$_SERVER['PHP_SELF']?>">
	<div class="form-group">
		<?php
		/*
		<label for="tipo_curso">Tipo curso:</label>
		<select name="tipo_curso" id="tipo_curso" class="form-control" style="width:200px; display:inline-block;">
			<option value="0" <?php if ($tipoCurso==0) { echo "selected"; } ?>>Curso ordinario</option>
			<option value="1" <?php if ($tipoCurso==1) { echo "selected"; } ?>>Escuela de verano</option>
		</select>
		*/
		?>
		
		<label for="curso_inicial">Curso inicial (opcional):</label>
		<select name="curso_inicial" size=1 class="form-control" style="width:200px; display:inline-block;">
			<?php
			$busca_listado_cursos=db_query("SELECT * FROM cursos_academicos WHERE tipo_curso=".$tipoCurso." ORDER BY fecha_inicio DESC");

			while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
			?>
			<option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$cursoInicial) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
			<?php
			}
			?>
		</select>
		
		<button type="submit" class="btn btn-primary">Filtrar</button>
	</div>
</form>	


<?php

$busca_cursos=db_query("SELECT * FROM cursos_academicos WHERE month(fecha_inicio)=9 AND year(fecha_inicio)<=".date("Y").$filtroTipoCurso.$filtroCurso." ORDER BY fecha_inicio DESC".$limite_cursos);	

$total_alumnos_meses=array();

while($cursos=db_fetch_array($busca_cursos)) {	
	$curso_actual_l=$cursos["id_curso"];
	$total_alumnos_meses=array();
?>

<table class="table table-striped table-bordered table-hover">
        <tr>
        	<td colspan=41 class="text-center"><small><b>Asistencia <?=$cursos["nombre"];?></b></small></td>
        </tr>
        <tr>
        	<td rowspan=2><small><b>Año nacimiento</b></small></td>
        	<?php
	        	$mes_actual=9;
	        	$anio_actual=date("Y",strtotime($cursos["fecha_inicio"]));
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {
	        ?>
			<td colspan="<?=count($listadoCentros);?>"><small><b><?=$nombre_mes[$mes_actual];?></b></small></td>
			<?php
					$mes_actual++;
					if ($mes_actual==13) {
						$mes_actual=1;
						$anio_actual++;
					}
				}
			?>
        </tr>        
        <tr>
	        <?php
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {	
					echo $encabezadoCentros;				
				}
			?>
        </tr>
		<?php
		$anio_desde=date("Y",strtotime($cursos["fecha_fin"]));
		
		for ($anio=$anio_desde;$anio>=$anio_desde-6;$anio--) {
		?>
        <tr>
	        <td><small><?=$anio?></small></td>
	        <?php				
		        $mes_actual=9;
	        	$anio_actual=date("Y",strtotime($cursos["fecha_inicio"]));
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {
		        	foreach ($listadoCentros as $id_centro => $nombre_centro) {
			        	
			        	$ultimo_dia_mes=date("t",strtotime("$anio_actual-$mes_actual-1"));
			        	
			        	$fecha_inicio_mes=date("Y-m-d",strtotime("$anio_actual-$mes_actual-1"));
			        	$fecha_fin_mes=date("Y-m-d",strtotime("$anio_actual-$mes_actual-$ultimo_dia_mes"));
			        	
		        		$busca_alumnos=db_query("SELECT id_alumno FROM alumnos LEFT JOIN usuarios ON alumnos.id_usuario=usuarios.id_usuario WHERE year(usuarios.fecha_nacimiento)=$anio AND alumnos.id_centro=$id_centro AND alumnos.id_curso=$curso_actual_l AND alumnos.fecha_matricula<='$fecha_fin_mes' AND alumnos.fecha_baja>='$fecha_inicio_mes'") or die(db_error());
		        		
		        		$total_alumnos=db_num_rows($busca_alumnos);
		        		$total_alumnos_meses[$id_centro][$anio_actual][$mes_actual]=$total_alumnos_meses[$id_centro][$anio_actual][$mes_actual]+$total_alumnos;
	        ?>
			<td><small><?=$total_alumnos?></small></td>
			<?php
					}
					$mes_actual++;
					if ($mes_actual==13) {
						$mes_actual=1;
						$anio_actual++;
					}
				}
			?>
        </tr>
        <?php	
		}	
	
		?>
        <tr>
	        <td><small>Total</small></td>
	        <?php
			    $mes_actual=9;
	        	$anio_actual=date("Y",strtotime($cursos["fecha_inicio"]));
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {
		        	foreach ($listadoCentros as $id_centro => $nombre_centro) {
						?>
			<td><small><?=$total_alumnos_meses[$id_centro][$anio_actual][$mes_actual]?></small></td>
			<?php				
					}
					$mes_actual++;
					if ($mes_actual==13) {
						$mes_actual=1;
						$anio_actual++;
					}					
				}
			?>
        </tr>		
        </table>
<?php
	// Tabla comedores
	$total_alumnos_meses=array();
	?>
	<table class="table table-striped table-bordered table-hover">
        <tr>
        	<td colspan=41 class="text-center"><small><b>Asistencia Comedor <?=$cursos["nombre"];?></b></small></td>
        </tr>
        <tr>
        	<td rowspan=2><small><b>Año nacimiento</b></small></td>
        	<?php
	        	$mes_actual=9;
	        	$anio_actual=date("Y",strtotime($cursos["fecha_inicio"]));
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {
	        ?>
			<td colspan="<?=count($listadoCentros);?>"><small><b><?=$nombre_mes[$mes_actual];?></b></small></td>
			<?php
					$mes_actual++;
					if ($mes_actual==13) {
						$mes_actual=1;
						$anio_actual++;
					}
				}
			?>
        </tr>        
        <tr>
	        <?php
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {	
					echo $encabezadoCentros;				
				}
			?>
        </tr>
		<?php
		$anio_desde=date("Y",strtotime($cursos["fecha_fin"]));
		
		for ($anio=$anio_desde;$anio>=$anio_desde-6;$anio--) {
		?>
        <tr>
	        <td><small><?=$anio?></small></td>
	        <?php				
		        $mes_actual=9;
	        	$anio_actual=date("Y",strtotime($cursos["fecha_inicio"]));
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {
		        	foreach ($listadoCentros as $id_centro => $nombre_centro) {
			        	
			        	$ultimo_dia_mes=date("t",strtotime("$anio_actual-$mes_actual-1"));
			        	
			        	$fecha_inicio_mes=date("Y-m-d",strtotime("$anio_actual-$mes_actual-1"));
			        	$fecha_fin_mes=date("Y-m-d",strtotime("$anio_actual-$mes_actual-$ultimo_dia_mes"));
			        	

                        // Calcula asistencia al comedor
                        $busca_alumnos=db_query("SELECT * FROM alumnos LEFT JOIN usuarios ON alumnos.id_usuario=usuarios.id_usuario,servicios_contratados,tarifa WHERE year(usuarios.fecha_nacimiento)=$anio AND alumnos.id_alumno=servicios_contratados.id_alumno AND  servicios_contratados.id_servicio=tarifa.ID AND alumnos.id_centro=$id_centro AND alumnos.id_curso=$curso_actual_l AND servicios_contratados.fecha_inicio<='$fecha_fin_mes' AND servicios_contratados.fecha_fin>='$fecha_inicio_mes' AND tarifa.CATEGORIA=7 AND tarifa.subcat=3 GROUP BY alumnos.id_alumno;") or die(db_error()."-1");		        		

		        		$total_alumnos=db_num_rows($busca_alumnos);
		        		$total_alumnos_meses[$id_centro][$anio_actual][$mes_actual]=$total_alumnos_meses[$id_centro][$anio_actual][$mes_actual]+$total_alumnos;
	        ?>
			<td><small><?=$total_alumnos?></small></td>
			<?php
					}
					$mes_actual++;
					if ($mes_actual==13) {
						$mes_actual=1;
						$anio_actual++;
					}
				}
			?>
        </tr>
        <?php	
		}	
	
		?>
        <tr>
	        <td><small>Total</small></td>
	        <?php
			    $mes_actual=9;
	        	$anio_actual=date("Y",strtotime($cursos["fecha_inicio"]));
	        	for ($num_mes=0;$num_mes<10;$num_mes++) {
		        	foreach ($listadoCentros as $id_centro => $nombre_centro) {
						?>
			<td><small><?=$total_alumnos_meses[$id_centro][$anio_actual][$mes_actual]?></small></td>
			<?php				
					}
					$mes_actual++;
					if ($mes_actual==13) {
						$mes_actual=1;
						$anio_actual++;
					}					
				}
			?>
        </tr>		
        </table>
	<?php

	}
?>        
</div>