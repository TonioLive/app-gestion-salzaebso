<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Datos
$fecha_selec=$_GET["fecha_selec"];
$edad_selec=$_GET["edad"];

$bajas=$_GET["bajas"];

if (strlen($bajas)==0) {
	
	$bajas=0;
	
}

if ($bajas==1) {
	
	$texto_titulo="bajas";	
	
} else {
	
	$texto_titulo="alumnos";	
	
}

if (!$fecha_selec) {

	$fecha_selec=time();

}

$completo=$_GET["completo"];

$anyo_selec=date("Y",$fecha_selec);
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
	} else {
		$id_curso_actual=0;
	}

$nombre_grupos[0]="HASTA 12 MESES";
$nombre_grupos[1]="DE 13 A 24 MESES";
$nombre_grupos[2]="DE 2 A 3 AÑOS";
$nombre_grupos[3]="DE 3 A 4 AÑOS";
$nombre_grupos[4]="DE 4 A 5 AÑOS";
$nombre_grupos[5]="DE 5 A 6 AÑOS";
$nombre_grupos[6]="DE 7 A 14 AÑOS";
$nombre_grupos[7]="ADULTOS";
	
// Cuenta totales dias de la semana
$totales_dias_semana=array();
if ($completo==1) {
	
	$fecha_inicio=strtotime($datos_curso_actual["fecha_inicio"])+43200;
	$fecha_fin=strtotime($datos_curso_actual["fecha_fin"])+43200;

	} else {
	
	$fecha_inicio=strtotime(date("Y-m-01",$fecha_selec))+43200;
	$fecha_fin=strtotime(date("Y-m-t",$fecha_selec))+43200;
	
	}

if (date("Y-m-d",$fecha_fin)>date("Y-m-d")) {
	$fecha_fin=time();
	}

$fecha_inicio_bucle=$fecha_inicio;
	
while($fecha_inicio_bucle<=$fecha_fin) {

	// Comprueba si es festivo
	$busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE dia=? AND estado=1 LIMIT 0,1', 's', array((string)(date("Y-m-d", $fecha_inicio_bucle))));	
	
	if (db_num_rows($busca_dia_en_calendario)==0) {
		$totales_dias_semana[date("N",$fecha_inicio_bucle)]++;
		}

	$fecha_inicio_bucle+=86400;
}
	
?>
<!doctype html>
<html lang="es">
<head>

<meta charset="WINDOWS-1258">
<title>Listado de alumnos de <?=$nombre_grupos[$edad_selec]?> a fecha <?=date("d/m/Y",$fecha_selec)?></title>

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
</head>
<body>
<?php 
if ($completo==1) {
?>
<h3>Listado de <?=$texto_titulo?> durante el curso del <?=date("d/m/Y",strtotime($datos_curso_actual["fecha_inicio"]));?> al <?=date("d/m/Y",(strtotime($datos_curso_actual["fecha_fin"])-(86400*10)));?></h3>
<?php 
} else {
?>
<h3>Listado de <?=$texto_titulo?> a fecha <?=date("d/m/Y",$fecha_selec)?> (<?=$nombre_mes[date("n",$fecha_selec)];?> de <?=date("Y",$fecha_selec);?>)</h3>
<?php 
}
?>
<table class="table table-striped table-bordered table-hover">
	<tr>
		<td class="col-md-1">Nº</td>
		<td class="col-md-4">Alumno</td>
		<td class="col-md-3">Grupos</td>
		<?php 
		if ($bajas==1) {
		?>
		<td class="col-md-1">Asistencias</td>
		<td class="col-md-1">Faltas</td>		
		<td class="col-md-1">Motivo Baja</td>
		<?php 
		} else {
		?>
		<td class="col-md-2">Asistencias</td>
		<td class="col-md-2">Faltas</td>	
		<?php 	
		}	
		?>		
	</tr>
<?php 
if ($id_curso_actual!=0) {                               

	if ($bajas==1) {
		
		if ($completo==1) {
		
			// Busca bajas mes actual	
			$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_baja>=?) AND (fecha_baja<=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.fecha_nacimiento DESC, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)($datos_curso_actual["fecha_inicio"]), (string)(date("Y-m-d", strtotime($datos_curso_actual["fecha_fin"]) - 86400 * 10)))) or die(db_error()."-2");
		
		} else {
			
			// Busca bajas mes actual	
			$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_baja>=?) AND (fecha_baja<=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.fecha_nacimiento DESC, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)(date("Y-m-01", $fecha_selec)), (string)(date("Y-m-t", $fecha_selec)))) or die(db_error()."-2");
	
		}	
		
	} else {
		
		if ($completo==1) {
		
			$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_matricula<=?) AND (fecha_baja>=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)($datos_curso_actual["fecha_fin"]), (string)($datos_curso_actual["fecha_inicio"]))) or die(db_error()."-2");
		
		} else {
		
			$busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_curso=? AND (fecha_matricula<=?) AND (fecha_baja>=?) GROUP BY usuarios.id_usuario ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre', 'sss', array($id_curso_actual, (string)(date("Y-m-d", $fecha_selec)), (string)(date("Y-m-d", $fecha_selec)))) or die(db_error()."-2");
	
		}	
		
	}

$contador=0;

while($alumnos=db_fetch_array($busca_inscripcion_curso_actual)) {

	// Comprueba si se reengancha
	$mostrar=true;
	if ($bajas==1) {
		
			if ($completo==1) {
		
				$busca_reenganche=db_query_params('SELECT * FROM alumnos WHERE fecha_baja>? AND id_usuario=?', 'ss', array((string)(date("Y-m-d", strtotime($datos_curso_actual["fecha_fin"]) - 86400 * 10)), $alumnos["id_usuario"]));
			
			} else {
			
				$busca_reenganche=db_query_params('SELECT * FROM alumnos WHERE fecha_baja>? AND id_usuario=?', 'ss', array((string)($alumnos["fecha_baja"]), $alumnos["id_usuario"]));
			
			}
				
			if (db_num_rows($busca_reenganche)!=0) {
				$mostrar=false;
			}
			
	}

	if ($mostrar==true) {

		$edad_decimal=date("Y",$fecha_selec)-date("Y",strtotime($alumnos["fecha_nacimiento"]));	
		$mes_d_l=date("m",strtotime($alumnos["fecha_nacimiento"]));
		$mes_d_n=date("m",$fecha_selec);
		$dif_meses=$mes_d_n-$mes_d_l;
	
		$decimales=0;
		
		if ($dif_meses!=0) {
	
			if ($dif_meses>0) {
			
				$decimales=round(($dif_meses/12),2);
			
				} else {
				
				$edad_decimal-=1;
				$decimales=round(((12+$dif_meses)/12),2);
				
				}
	
			}
			
		$edad_decimal+=$decimales;	
	
		$edad_grupo=floor($edad_decimal);
	
		if ((floor($edad_decimal)>=6) and (floor($edad_decimal)<15)) {
				$edad_grupo=6;
			}
			
		if (floor($edad_decimal)>=15) {
				$edad_grupo=7;
			}
	
		$totales_grupos[$edad_grupo]++;
	
		if ($edad_grupo<2) {
		
				$grupo_desglose=ceil($edad_decimal*12);
		
				$totales_grupos_desglose[$grupo_desglose]++;			
		
			}
			
		if ($edad_grupo==$edad_selec) {
			
			$contador++;
			
			// Cuenta asistencias
			if ($completo==1) {
				$busca_registro_entrada=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND (fecha<=?) AND (fecha>=?)', 'sss', array($alumnos["id_usuario"], (string)($datos_curso_actual["fecha_fin"]), (string)($datos_curso_actual["fecha_inicio"]))) or die(db_error());	
				} else {
				$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$alumnos["id_usuario"]." AND month(fecha)=".$mes." AND year(fecha)=".$anyo_selec) or die(db_error());		
				}
			
			// Grupos Alumno
			$lista_grupos_alumno="";
			$filtro_dias_asistencia="";
			
			$busca_alumnos_grupo=db_query_params('SELECT *, alumnos.id_curso AS id_curso_alu  FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND alumnos.id_alumno=?', 's', array($alumnos["id_alumno"])) or die(db_error()."-4");
		
			$dias_debe_asistir=0;
		
		while($grupos_donde=db_fetch_array($busca_alumnos_grupo)) {
			$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"])).".<br />";
			
			$dias_debe_asistir+=$totales_dias_semana[$grupos_donde["dia"]];
			
			}		
			
			$faltas=$dias_debe_asistir-db_num_rows($busca_registro_entrada);
			$color_faltas="";
			$celda_faltas="";
			if ($faltas>0) {
			
				$color_faltas=" class='text-danger'";
				$celda_faltas=" class='danger'";
			
			}
			
		?>
			<tr<?=$celda_faltas?>>
				<td><?=$contador?></td>
				<td><?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?></td>
				<td><?=$lista_grupos_alumno?></td>
				<td><?=db_num_rows($busca_registro_entrada); ?></td>
				<td<?=$color_faltas?>><?=$faltas;?></td>
				<?php 
				if ($bajas==1) {
				?>
				<td><?=$alumnos["motivo_baja"];?></td>
				<?php 
				}	
				?>
			</tr>
		<?php 
			}
		}
	}	
	
}

?>
</table>

</body>
</html>