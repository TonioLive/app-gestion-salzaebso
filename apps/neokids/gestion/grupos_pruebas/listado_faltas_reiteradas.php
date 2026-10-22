<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Programación recuperación faltas</title>
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

<link rel="stylesheet" href="style.css">
<style>
.fondo_nivel0 {
        background: #fff;
        }
.fondo_nivel1 {
        background: #9ed09c;
        }
.fondo_nivel2 {
        background: #9cbdd0;
        }
.fondo_nivel3 {
        background: #d0af9c;
        } 
.fondo_nivel4 {
        background: #f9a63b;
        } 
.fondo_nivel5 {
        background: #ffa9f0;
        }

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
<?php
	
foreach ($colores_subniveles as $indice => $color) {
?>

.fondo_subnivel<?=$indice?> {
background: <?=$color?>;
}

<?php
}
?>	          
</style> 
</head>
<body>
<div class="container-fluid">
<?php 

$fecha_time_stamp=$_GET["fecha_control"];
$fecha_inicio_curso=$_GET["fecha_inicio_curso"];
$fecha_fin_curso=$_GET["fecha_fin_curso"];

$nombres_escuelas=array();
$nombres_escuelas[0]="Actividades comunes";

$num_dias_ausencias=3;

$mensaje_faltas_reiteradas="";

$busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
while($lista_centros=db_fetch_array($busca_centros)){
        $nombres_escuelas[$lista_centros["id_centro"]]=$lista_centros["nombre_centro"];
        }

// Busca alumnos

$busca_alumnos_hoy=db_query_params('SELECT *, alumnos.id_centro AS id_centro_alu, alumnos.id_curso AS id_curso_alu FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.fecha_matricula<=? AND alumnos.fecha_baja>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

while($lista_alumnos_hoy=db_fetch_array($busca_alumnos_hoy)) {
        
		$busca_dias_asistidos=db_query_params('SELECT fecha FROM servicios_consumidos WHERE fecha>=? AND fecha<=? AND id_alumno=? GROUP BY fecha ORDER BY fecha DESC;', 'sss', array((string)($fecha_inicio_curso), (string)($fecha_fin_curso), $lista_alumnos_hoy["id_usuario"])) or die(db_error());
		
        $num_dias_asistidos=db_num_rows($busca_dias_asistidos);
        if ($num_dias_asistidos!=0) {
        		$datos_dias_asistidos=db_fetch_array($busca_dias_asistidos);
        		
        		$ultimo_dia_asistido_timestamp=strtotime($datos_dias_asistidos["fecha"]);
        		
               	$ultimo_dia_asistido=date("d/m/Y",$ultimo_dia_asistido_timestamp);
               	               	
               	// Comprobamos si ha faltado algún día desde el ultimo asistido
               	
               	$dias_falta=0;
               	
               	$lista_dias_falta="";
               	$lista_dias_semana_asiste=array(0,0,0,0,0,0,0,0);
               	while($ultimo_dia_asistido_timestamp<$fecha_time_stamp) {
               	
               		// comprueba si es festivo
               		$id_curso_alu=$lista_alumnos_hoy["id_curso_alu"];
               		$id_centro_alu=$lista_alumnos_hoy["id_centro_alu"];
               		$busca_calendario_laboral=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=? AND estado=1', 'sss', array($id_curso_alu, $id_centro_alu, (string)(date("Y-m-d", $ultimo_dia_asistido_timestamp)))) or die(db_error());
               	
               		if (db_num_rows($busca_calendario_laboral)==0) {
               	
               		// Comprueba si debería asistir este día
               		$dia_de_la_semana_sel=date("w",$ultimo_dia_asistido_timestamp);
               		
               		$busca_grupo=db_query_params('SELECT * FROM aulas_grupos,aulas_alumnos_grupos WHERE aulas_grupos.id_grupo=aulas_alumnos_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_grupos.dia=?', 'ss', array($lista_alumnos_hoy["id_alumno"], $dia_de_la_semana_sel)) or die(db_error()) ;
               		
               		if (db_num_rows($busca_grupo)!=0) {
               			
               			// Debería asistir hoy... ¿asiste?
               			
               			$comprueba_si_asiste_hoy=db_query_params('SELECT fecha FROM servicios_consumidos WHERE fecha=? AND id_alumno=? GROUP BY fecha ORDER BY fecha DESC;', 'ss', array((string)(date("Y-m-d", $ultimo_dia_asistido_timestamp)), $lista_alumnos_hoy["id_usuario"]));
               		
               			if (db_num_rows($comprueba_si_asiste_hoy)==0) {
               				// No asiste
               				$lista_dias_semana_asiste[date("w",$ultimo_dia_asistido_timestamp)]=1;
               				$dias_falta++;
               				$lista_dias_falta.=$dia_sem_largo[date("w",$ultimo_dia_asistido_timestamp)]." ".date("d/m/Y",$ultimo_dia_asistido_timestamp).", ";
               				}               		               		
               			}
               		}
               		$ultimo_dia_asistido_timestamp=$ultimo_dia_asistido_timestamp+86400;
               		}
               	
               	if ($dias_falta>=$num_dias_ausencias) {
               		if ($lista_dias_falta!="") {
               			$lista_dias_falta=substr($lista_dias_falta,0,strlen($lista_dias_falta)-2);
               			
               			$lista_dias_debe="";
               			
               			for($c_dias_sem_a=0;$c_dias_sem_a<=count($lista_dias_semana_asiste);$c_dias_sem_a++) {
               			
               				if ($lista_dias_semana_asiste[$c_dias_sem_a]==1) {
               					$lista_dias_debe.=$dia_sem_largo[$c_dias_sem_a].", ";
               					}
               			
               				}
               			
               			if ($lista_dias_debe!="") {
               				$lista_dias_debe=substr($lista_dias_debe,0,strlen($lista_dias_debe)-2);
               				}

               			}
               		// Ha faltado más de X días, mostramos alerta
               		$mensaje_faltas_reiteradas.="<font size=2><a href='/alumnos/nuevo_alumno1.php?u=".$lista_alumnos_hoy["id_usuario"]."' target='_blank' data-toggle='tooltip' title='Asiste: $lista_dias_debe. Ha faltado: $lista_dias_falta'>".$lista_alumnos_hoy["Nombre"]." ".$lista_alumnos_hoy["Apellido1"]." ".$lista_alumnos_hoy["Apellido2"]."</a> ha faltado $dias_falta días seguidos desde el día ".$ultimo_dia_asistido.".</font><br>";
               		}
               	
               	
        	} else {
            	$ultimo_dia_asistido="-";
            }                        				

		}
		
		if ($mensaje_faltas_reiteradas!="") {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4>Alerta de faltas reiteradas</h4>
			<?=$mensaje_faltas_reiteradas?>
			</div>
			<?php 
			}
		
?>                         
</div>
</body>
</html>
