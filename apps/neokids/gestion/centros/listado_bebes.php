<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Datos
$fecha_selec=$_GET["fecha"];

if (!$fecha_selec) {

	$fecha_selec=time();

}

$completo=$_GET["completo"];

$mes=date("n",$fecha_selec);
$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_selec);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_selec);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }

$timestamp_siguiente=strtotime($ano_siguiente."-".$mes_siguiente."-01");
$timestamp_anterior=strtotime($ano_anterior."-".$mes_anterior."-01");

// Busca curso actual
$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_selec)), (string)(date("Y-m-d", $fecha_selec))));

if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$id_curso_actual=$datos_curso_actual["id_curso"];
		$fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
		$fecha_fin_curso=$datos_curso_actual["fecha_fin"];
		$nombre_curso_actual=$datos_curso_actual["nombre"];
		// Busca curso anterior
		$busca_curso_anterior=db_query("SELECT * FROM cursos_academicos WHERE year(fecha_inicio)<".date("Y",strtotime($fecha_inicio_curso))." ORDER BY fecha_fin DESC LIMIT 0,1");
		
		if (db_num_rows($busca_curso_anterior)!=0) {
			
			$datos_curso_anterior=db_fetch_array($busca_curso_anterior);
			$id_curso_anterior=$datos_curso_anterior["id_curso"];
			$fecha_inicio_curso_anterior=$datos_curso_anterior["fecha_inicio"];
			$fecha_fin_curso_anterior=$datos_curso_anterior["fecha_fin"];
			$nombre_curso_anterior=$datos_curso_anterior["nombre"];
			
		} else {
			// No hay curso anterior, no se puede realizar la comparativa
			$id_curso_actual=0;
			
		}
		
		// Busca curso siguiente
		$busca_curso_siguiente=db_query("SELECT * FROM cursos_academicos WHERE year(fecha_inicio)>=".date("Y",strtotime($fecha_fin_curso))." AND year(fecha_inicio)<>year(fecha_fin) AND id_curso<>1 ORDER BY fecha_fin LIMIT 0,1");		
		
		if (db_num_rows($busca_curso_siguiente)!=0) {
			
			$datos_curso_siguiente=db_fetch_array($busca_curso_siguiente);
			$id_curso_siguiente=$datos_curso_siguiente["id_curso"];
			$fecha_inicio_curso_siguiente=$datos_curso_siguiente["fecha_inicio"];
			$fecha_fin_curso_siguiente=$datos_curso_siguiente["fecha_fin"];
			$nombre_curso_siguiente=$datos_curso_siguiente["nombre"];
			
		} else {
			// No hay curso anterior, no se puede realizar la comparativa
			$id_curso_siguiente=0;
			
		}		
		
	} else {
		$id_curso_actual=0;
	}
	
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Resumen de edades alumnos a fecha <?=date("d/m/Y",$fecha_selec)?></title>

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

<style>

.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 

</style>
<script>

function mostrar(nombreModal) {

	$(nombreModal).modal('show')

}

function mostrarListado(tipo,edad,nombre_edad) {

	$('#listadoAlumnos').modal('show')
	
	$('#nombreListado').html('<h4 class="modal-title" id="grupoModalLabel">Listado alumnos ' + nombre_edad + '</h4>');
	$('#detalles_grupo').attr('src', 'listado_edades_grupo.php?bajas='+tipo+'&fecha_selec=<?=$fecha_selec;?>&completo=<?=$completo?>&edad='+edad);		

	$('#listadoAlumnos').modal('show');		

}

</script>
</head>
<body>
<?php 
if ($id_curso_actual!=0) {                               

	// 1. Busca alumnos del curso anterior que han cumplido 24 meses los 121 días enteriores al inicio de este curso
	
	// Fechas límite 
	$dia_antes=strtotime(date("Y-m-t",strtotime($fecha_inicio_curso))); // El último día del primer mes del curso
	$fecha_fin_limite=date((date("Y",$dia_antes)-2)."-m-d",$dia_antes); // 2 años antes del último día del primer mes del curso
	$fecha_inicio_limite=date("Y-m-d",strtotime($fecha_fin_limite)-(86400*121)); // 121 días antes del fin de la fecha límite
	
	$busca_alumnos_antes="SELECT alumnos.id_usuario AS id_usuario1, alumnos.id_alumno AS id_alumno1, usuarios.fecha_nacimiento AS fecha_nacimiento1, usuarios.Nombre AS Nombre1, usuarios.Apellido1 AS Apellido11, usuarios.Apellido2 AS Apellido21, alumnos.fecha_matricula AS fecha_matricula1, fecha_baja AS fecha_baja1 FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=$id_curso_anterior AND usuarios.fecha_nacimiento>='$fecha_inicio_limite' AND usuarios.fecha_nacimiento<='$fecha_fin_limite'";
	
	$alumnos_antes=db_query($busca_alumnos_antes);
	
	$total_alumnos_antes=db_num_rows($alumnos_antes);
	
	$busca_alumnos_ahora="SELECT alumnos.id_usuario AS id_usuario2, alumnos.id_alumno AS id_alumno2, usuarios.fecha_nacimiento AS fecha_nacimiento2, usuarios.Nombre AS Nombre2, usuarios.Apellido1 AS Apellido12, usuarios.Apellido2 AS Apellido22, alumnos.fecha_matricula AS fecha_matricula2, fecha_baja AS fecha_baja2 FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=$id_curso_actual AND usuarios.fecha_nacimiento>='$fecha_inicio_limite' AND usuarios.fecha_nacimiento<='$fecha_fin_limite'";
	
	// 2. Intersección de ambas consultas

	$busca_alumnos_continuan="SELECT * FROM ($busca_alumnos_ahora) AS Tabla1 LEFT JOIN ($busca_alumnos_antes) AS Tabla2 ON Tabla1.id_usuario2=Tabla2.id_usuario1 HAVING (Tabla2.id_usuario1 is Not Null)";	
	
	$alumnos_continuan=db_query($busca_alumnos_continuan);

	$total_alumnos_continuan=db_num_rows($alumnos_continuan);
	
	// 3. Listado

	$busca_alumnos_completo="SELECT * FROM ($busca_alumnos_antes) AS Tabla1 LEFT JOIN ($busca_alumnos_ahora) AS Tabla2 ON Tabla1.id_usuario1=Tabla2.id_usuario2 ORDER BY Apellido11, Apellido21, Nombre1";
	
	$alumnos_completo=db_query($busca_alumnos_completo);
	
?>
<h3>Alumnos que cumplieron 24 meses al finalizar el curso anterior<span class="text-danger">*</span> y continuaron el curso <?=$nombre_curso_actual?></h3>
<p class="text-danger"><small><small>* 24 meses cumplidos entre los días <?=date("d/m/",strtotime($fecha_inicio_limite)).date("Y",strtotime($fecha_inicio_curso))?> y <?=date("d/m",strtotime($fecha_fin_limite)).date("Y",strtotime($fecha_inicio_curso))?></small></small></p>
<p>
<?php
if ($id_curso_anterior!=0) {	
?>
<a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=strtotime($fecha_fin_curso_anterior);?>"><i class="fa fa-angle-double-left"></i> Curso <?=$nombre_curso_anterior?></a> 
<?php
	}
if ($id_curso_siguiente!=0) {	
?>
 <a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=strtotime($fecha_inicio_curso_siguiente);?>"><i class="fa fa-angle-double-right"></i> Curso <?=$nombre_curso_siguiente?></a> 
<?php
	}	
?>
</p>
<p>
	<b>Alumnos del curso anterior que cumplieron 24 meses al finalizar el curso:</b> <?=$total_alumnos_antes?><br />
	<b>De los cuales continuaron durante este curso:</b> <?=$total_alumnos_continuan?>
</p>
<h3>Listado de alumnos</h3>
<table class="table table-striped table-bordered table-hover">
	<tr>
		<td class="col-md-1">Id Alumno</td>
		<td class="col-md-3">Nombre</td>
		<td class="col-md-1">Fecha nacimiento</td>
		<td class="col-md-1">Inicio curso actual</td>
		<td class="col-md-1">Fin curso actual</td>
		<td class="col-md-5">Situación actual</td>
	</tr>
<?php
	while($listado_alumnos_completo=db_fetch_array($alumnos_completo)) {	
		
		// Busca grupo
		if ($listado_alumnos_completo["fecha_baja2"]) {
				
			if ($listado_alumnos_completo["fecha_baja2"]>date("Y-m-d")) {
				
				$lista_grupos_alumno="";
					
			    if ($listado_alumnos_completo["id_alumno2"]) {
				    $busca_alumnos_grupo=db_query_params('SELECT *, alumnos.id_curso AS id_curso_alu  FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_curso=? AND alumnos.id_usuario=?', 'ss', array($id_curso_actual, $listado_alumnos_completo["id_usuario1"])) or die(db_error()."-4");
			
		            while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
			            $lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))." ";
		            }
			    } 
			        			
				$situacion_actual="<span class='text-success'>Actualmente asistiendo en el grupo ".$lista_grupos_alumno."</span>";	
					
			} else {
				
				// Continuó pero ya ha finalizado
				$situacion_actual="<span class='text-primary'>Inició el curso pero ya ha finalizado</span>";
				
			}
		} else {
			
			// No ha continuado
			$situacion_actual="<span class='text-danger'>No ha continuado el curso ".$nombre_curso_actual."</span>";
		}

		
	?>
	<tr>
		<td><?=$listado_alumnos_completo["id_usuario1"]?></td>
		<td><?=$listado_alumnos_completo["Nombre1"]?> <?=$listado_alumnos_completo["Apellido11"]?> <?=$listado_alumnos_completo["Apellido21"]?></td>
		<td><?=date("d/m/Y",strtotime($listado_alumnos_completo["fecha_nacimiento1"]));?></td>
		<td><?php if ($listado_alumnos_completo["fecha_matricula2"]) { echo date("d/m/Y",strtotime($listado_alumnos_completo["fecha_matricula2"])); } ?></td>
		<td><?php if ($listado_alumnos_completo["fecha_baja2"]) { echo date("d/m/Y",strtotime($listado_alumnos_completo["fecha_baja2"])); } ?></td>
		<td><?=$situacion_actual?></td>
	</tr>	
	<?php	
	}	
?>	
</table>
<?php
} else {
?>
<h1>No hay curso anterior al curso <?=$nombre_curso_actual?>, no se puede hacer la comparación.</h1>
<p>
<?php
if ($id_curso_siguiente!=0) {	
?>
 <a class="btn btn-xs btn-primary" href="<?=$_SERVER["PHP_SELF"]?>?fecha=<?=strtotime($fecha_inicio_curso_siguiente);?>"><i class="fa fa-angle-double-right"></i> Curso <?=$nombre_curso_siguiente?></a> 
<?php
	} else {	
?>	
 <button class="btn btn-xs btn-primary" onclick="history.go(-1);">Volver</button>
<?php 	
	}
}	
?>
</p>
</body>
</html>
