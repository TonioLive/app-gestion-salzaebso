<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

$origen=$_GET["origen"];
if (strlen($origen)==0) {
	$origen=0;
}

$nombreAccion="inscripción";
switch( $origen ) {

	case 0:
		$nombreAccion="inscripción";
	break;
	case 1:
		$nombreAccion="información";
	break;
}

$centro_enviado=$_GET["centro_origen"];
if (strlen($centro_enviado)==0) {
	$centro_enviado=$centro_actual;
}

$bbddContactos = "contactos_web";
/*
if ($centro_enviado==10) {
	$bbddContactos = "contactos_web_sp";
}
*/

$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$centro_enviado");
if (db_num_rows($busca_datos_centro)!=0) {
		$datos_centro=db_fetch_array($busca_datos_centro);
		$nombre_centro=$datos_centro["nombre_centro"];
		$poblacion_centro=$datos_centro["poblacion_centro"];
		$tipo_centro=$datos_centro["tipo_centro"];
}


// Busca el curso actual
$primerCurso=32;
$id_curso_contacto=$_GET["id_curso"];
if (strlen($id_curso_contacto)==0) {
	$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."' AND id_curso>=$primerCurso");

	if (db_num_rows($busca_curso_actual)!=0) {
		$datos_curso_actual=db_fetch_array($busca_curso_actual);
		$id_curso_contacto=$datos_curso_actual["id_curso"];
	} else {
		$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));

		if (db_num_rows($busca_curso_actual)!=0) {
			$datos_curso_actual=db_fetch_array($busca_curso_actual);
			$id_curso_contacto=$datos_curso_actual["id_curso"];
		} else {
			$id_curso_contacto=$primerCurso;
		}
	}
} else {
	$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_contacto));
}

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_contacto));
$datos_curso_actual=db_fetch_array($busca_curso_actual);

$busca_curso_anterior = db_query_params('SELECT * FROM cursos_academicos WHERE id_curso<? AND month(fecha_inicio)=9 ORDER BY fecha_inicio DESC;', 's', array($id_curso_contacto));
$datos_curso_anterior=db_fetch_array($busca_curso_anterior);

$id_curso_anterior=$datos_curso_anterior["id_curso"];


$buscaSemanas = db_query_params('SELECT * FROM cursos_escuela_verano_semanas WHERE id_curso=? ORDER BY orden', 's', array($id_curso_contacto));
$nombresSemanas = array();
while($semanas=db_fetch_array($buscaSemanas)) {
	$nombreSemana = "Semana " . ($semanas["orden"]-1) . ", Del " . date("d/m/Y",strtotime($semanas["inicio_semana"])) . " al " .date("d/m/Y",strtotime($semanas["fin_semana"]));
	if ($semanas["mes_completo"] == 1) {
		$nombreSemana="Mes completo, Del " . date("d/m/Y",strtotime($semanas["inicio_semana"])) . " al " .date("d/m/Y",strtotime($semanas["fin_semana"]));
	}
	$nombresSemanas[] = $nombreSemana;
};

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
  		<div class="panel-heading">
			<a href="/" class="btn btn-danger pull-right"><i class="fa fa-sign-out" aria-hidden="true"></i></a><h3>Solicitudes de <?=$nombreAccion?> rellenadas en la web para el curso <?=$datos_curso_actual["nombre"];?></h3>
			<div>
				<form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
					<input type="hidden" name="origen" value="<?=$origen?>">
					<b>Cambiar Curso:</b> <select name="id_curso" class="form-control" onchange="document.seleccion_curso.submit();">
					<?php 
					$busca_listado_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso>=$primerCurso ORDER BY fecha_inicio DESC");

					while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
					?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_contacto) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
					<?php 
					}
					?>
					</select>
				</form>
			</div>
		</div>
  		<div class="panel-body">
			<?php
			$contador=0;
			// $busca_contactos=mysql_query("SELECT * FROM $bbddContactos LEFT JOIN alumnos ON $bbddContactos.id_usuario_alumno=alumnos.id_usuario WHERE (alumnos.id_curso=$id_curso_anterior OR alumnos.id_curso is Null OR alumnos.id_curso=$id_curso_contacto) AND $bbddContactos.id_curso=$id_curso_contacto AND $bbddContactos.id_centro=$centro_enviado GROUP BY id_contacto ORDER BY alumnos.aula,Apellido1,Apellido2,Nombre;") or die(mysql_error());												
			$busca_contactos=db_query("SELECT *, $bbddContactos.forma_de_pago as forma_de_pago_nueva FROM $bbddContactos LEFT JOIN alumnos ON $bbddContactos.id_usuario_alumno=alumnos.id_usuario WHERE $bbddContactos.id_curso=$id_curso_contacto AND $bbddContactos.id_centro=$centro_enviado AND $bbddContactos.origen=$origen GROUP BY id_contacto ORDER BY $bbddContactos.fecha_contacto,alumnos.aula,Apellido1,Apellido2,Nombre,alumnos.id_curso DESC;") or die(db_error());

			if (db_num_rows($busca_contactos)!=0) {
				
				?>
				<table class="table table-bordered table-striped table-hover">
					<tr>
						<td>&nbsp;</td>						
						<td>Fecha Contacto</td>
						<td>Fecha revisión</td>						
						<td>Nombre Alumno</td>
						<td>Aula curso anterior</td>
						<td>Fecha Nacimiento</td>
						<td>Horario</td>
						<td>Mensualidad</td>						
						<td>Comida</td>
						<td>Matinera</td>
						<td>¿Cómo nos ha conocido?</td>
						<td>&nbsp;</td>	
					</tr>
				<?php
				$totalAlumnos=0;
				$antiguosAlumnos=0;
				$contador=0;
				while($datos_alumno=db_fetch_array($busca_contactos)) {
					
					$contador++;
					
					$aulaCurosAnterior = "Nuevo";
					
					// Busca datos curso actual
					if ($datos_alumno["id_usuario_alumno"]!=0) {

						// Comprueba si inscrito en el curso anterior
						if ($datos_alumno["id_alumno"]) {
						
							$consulta_datos_alumno=array('SELECT *,cursos_academicos.nombre as nombre_curso FROM alumnos,cursos_academicos,centro_aulas WHERE  alumnos.id_curso=cursos_academicos.id_curso AND alumnos.aula=centro_aulas.id_aula AND alumnos.id_curso<>? AND alumnos.id_alumno=?;', 'ss', array($datos_alumno["id_curso"], $datos_alumno["id_alumno"]));
							
							$busca_alumnos = db_query_params($consulta_datos_alumno[0], $consulta_datos_alumno[1], $consulta_datos_alumno[2]) or die(db_error());
							if (db_num_rows($busca_alumnos)!=0) {
								$historico_alumno=db_fetch_array($busca_alumnos);

								$antiguosAlumnos++;
								$aulaCurosAnterior=$historico_alumno["nombre"]." - ".$historico_alumno["nombre_curso"];		
							}				
							
						}
					
						// Introducimos Datos bancarios
						$entidad="";
						$oficina="";
						$dc="";
						$cuenta="";
						$datosAdicionalesLink="";
						$forma_de_pago = $datos_alumno['forma_de_pago_nueva'];

						if (strlen($datos_alumno['IBAN'])!=0) {

							$banco_titular_aa=$datos_alumno["nombre_c1"]." ".$datos_alumno["apellido1_c1"]." ".$datos_alumno["apellido2_c1"];
							$banco_dni_titular_aa=$datos_alumno["dni_padre1"];

							// Quita espacios en blanco
							$iban = str_replace(" ","",$datos_alumno['IBAN']);

							// Quita ES
							$iban = str_replace("ES","",$iban);

							$banco_iban=substr($iban,0,2);			

							$entidad=substr($iban,2,4);
							$oficina=substr($iban,6,4);
							$dc=substr($iban,10,2);
							$cuenta=substr($iban,12,10);

							$datosAdicionalesLink="&banco_titular_aa=$banco_titular_aa&banco_dni_titular_aa=$banco_dni_titular_aa&forma_de_pago=$forma_de_pago&banco_iban=$banco_iban&entidad=$entidad&oficina=$oficina&dc=$dc&cuenta=$cuenta";

						}

						// Comprueba si ya está inscrito en el curso
						$busca_alumno_inscrito=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?;', 'ss', array($datos_alumno["id_usuario_alumno"], $id_curso_contacto)) or die(db_error());
						if (db_num_rows($busca_alumno_inscrito)!=0) {
							$botonFicha='';							
						} else {
							$botonFicha='<a href="/alumnos/nuevo_alumno4.php?u='.$datos_alumno["id_usuario_alumno"].$datosAdicionalesLink.'" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-user" aria-hidden="true"></i> Inscribir</a>';						

						}
						
					} else {
							
						// Intenta buscar si es un alumno antiguo
						$buscaAntiguosAlumnosNombreIgual = db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.Nombre=? AND usuarios.Apellido1=? AND usuarios.Apellido2=? AND alumnos.id_centro=?;', 'ssss', array((string)($datos_alumno["Nombre"]), (string)($datos_alumno["Apellido1"]), (string)($datos_alumno["Apellido2"]), $centro_actual)) or die(db_error());

						if (db_num_rows($buscaAntiguosAlumnosNombreIgual)!=0) {

							// Si es un alumno antiguo, mostramos un botón para asociarlo a un alumno existente
							if (db_num_rows($buscaAntiguosAlumnosNombreIgual)>1) {
								// Si hay más de un alumno con el mismo nombre, mostramos un listado para que elijamos
								$aulaCurosAnterior = "Existe más un alumno con el mismo nombre, por favor, comprueba que si es un alumno de estos antiguos alumnos. Si es así, pulsa el botón correspondiente para asociarlo con esta solicitud.";
																
								while($datos_alumno_antiguo=db_fetch_array($buscaAntiguosAlumnosNombreIgual)) {
									// Busca datos curso y aula del alumno antiguo
									$busca_datos_alumno_antiguo=array('SELECT cursos_academicos.nombre as nombre_curso,centro_aulas.nombre as nombre_aula FROM cursos_academicos,centro_aulas WHERE  centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=?;', 's', array($datos_alumno_antiguo["aula"]));
									$busca_alumnos_antiguos = db_query_params($busca_datos_alumno_antiguo[0], $busca_datos_alumno_antiguo[1], $busca_datos_alumno_antiguo[2]) or die(db_error());
									$datos_aula_antiguo_alumno=db_fetch_array($busca_alumnos_antiguos);
									$aulaCurosAnterior .= "<br /><button class='btn btn-sm btn-success' style='margin-bottom: 5px;margin-right: 5px;' onclick=\"asociarAlumno('".$datos_alumno_antiguo["id_usuario"]."','".$datos_alumno["id_contacto"]."')\">Asociar</button>".$datos_alumno_antiguo["Nombre"]." ".$datos_alumno_antiguo["Apellido1"]." ".$datos_alumno_antiguo["Apellido2"]." - ".$datos_aula_antiguo_alumno["nombre_curso"]." - ".$datos_aula_antiguo_alumno["nombre_aula"];
									if ($datos_alumno_antiguo["id_curso"]==$id_curso_contacto) {
										$aulaCurosAnterior.="<span class='text-danger'> (Puede que ya esté inscrito en el curso)</span>";
									}
								}								
							} else {
								$aulaCurosAnterior = "Existe un alumno con el mismo nombre, por favor, comprueba que si es un antiguo alumno. Si es así, pulsa el botón para asociarlo con esta solicitud.";
								$datos_alumno_antiguo=db_fetch_array($buscaAntiguosAlumnosNombreIgual);
								$aulaCurosAnterior .= "<br /><button class='btn btn-sm btn-success' style='margin-bottom: 5px;margin-right: 5px;' onclick=\"asociarAlumno('".$datos_alumno_antiguo["id_usuario"]."','".$datos_alumno["id_contacto"]."')\">Asociar</button>".$datos_alumno_antiguo["Nombre"]." ".$datos_alumno_antiguo["Apellido1"]." ".$datos_alumno_antiguo["Apellido2"]." - ".$datos_alumno_antiguo["nombre_curso"]." - ".$datos_alumno_antiguo["nombre_aula"]."";
							}	
						}						
						$botonFicha='<a href="/formulario/inscribir.php?id_contacto='.$datos_alumno["id_contacto"].'" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-user" aria-hidden="true"></i> Inscribir</a>';							
						
					}
					
					if ($id_curso_contacto>=34) {
						// $botonFicha='<button type="button" class="btn btn-sm btn-success" disabled=disabled><i class="fa fa-user" aria-hidden="true"></i> Inscribir</button>';
					}
					
					// Semanas
					$ev_semanas=security_unserialize($datos_alumno["ev_semanas"]);
					$semanasAsisteArray=array();
					$semanasAsiste="";
			
					if ($ev_semanas) {

						$buscaSemanas = db_query_params('SELECT * FROM cursos_escuela_verano_semanas WHERE id_curso=? ORDER BY orden', 's', array($id_curso_contacto));
						while($semanas=db_fetch_array($buscaSemanas)) {
							$nombreSemana = "Semana ". ($semanas["orden"]-1) . " del " . date("d/m/Y",strtotime($semanas["inicio_semana"])) . " al " .date("d/m/Y",strtotime($semanas["fin_semana"]));
							if ($semanas["mes_completo"] == 1) {
								$nombreSemana="Mes completo, Del " . date("d/m/Y",strtotime($semanas["inicio_semana"])) . " al " .date("d/m/Y",strtotime($semanas["fin_semana"]));
							}
							if ($ev_semanas[($semanas["orden"]-1)]==1) { $semanasAsisteArray[]=$nombreSemana; }
						}

						$semanasAsiste="<br /><b>Semanas:</b> ".implode(", ",$semanasAsisteArray);

						/*

						foreach($ev_semanas as $key => $value) {
							// $semanasAsiste.=($key+1).", ";
							$semanasAsiste.=$nombresSemanas[$key].", ";
						}
						if (substr($semanasAsiste,strlen($semanasAsiste)-2,2)==", ") {
							$semanasAsiste=substr($semanasAsiste,0,strlen($semanasAsiste)-2);
						}
						$semanasAsiste="<br /><b>Semanas: (".implode(",",$ev_semanas).")</b> ".$semanasAsiste;
						*/
					}
					
					$totalAlumnos++;
					
					$fechaNacimiento='';
					if ($datos_alumno["fecha_nacimiento"]!='0000-00-00') {
						$fechaNacimiento=date("d/m/Y",strtotime($datos_alumno["fecha_nacimiento"]));
					}

					$fechaRevision='<span class="text-danger">Nuevo</span>';
					if ($datos_alumno["revisado"]!='0000-00-00 00:00:00') {
						$fechaRevision=date("d/m/Y H:i",strtotime($datos_alumno["revisado"]));
					}

					?>
					<tr>
						<td><?=$contador;?></td>
						<td><?=date("d/m/Y H:i",strtotime($datos_alumno["fecha_contacto"]))?></td>
						<td><?=$fechaRevision?></td>
						<td><?=$datos_alumno["Nombre"]?> <?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?></td>
						<td><?=$aulaCurosAnterior?></td>
						<td><?=$fechaNacimiento?></td>
						<td><?=$datos_alumno["horario"].$semanasAsiste?></td>
						<td><?=str_replace("_"," ",$datos_alumno["mensualidad"]);?></td>						
						<td><?=$datos_alumno["comida"]?></td>
						<td><?=$datos_alumno["horas_extra"]?><br /><?=$datos_alumno["horario_horas_extra"]?></td>
						<td><?=$datos_alumno["como_conocio_texto"];?></td>
						<td><?=$botonFicha?> <a href="ver_contacto.php?id_contacto=<?=$datos_alumno["id_contacto"]?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>						
					</tr>
					<?php				
					
				}
				
			}
			
			?>
			</table>
			<table class="table table-bordered table-striped">
					<tr>
						<td colspan=2><b>Totales</b></td>
					</tr>
					<tr>
						<td class="text-right col-md-2">Nº total de solicitudes:</td>
						<td><?=$totalAlumnos?></td>
					</tr>
					<tr>
						<td class="text-right col-md-2">Renovaciónes de alumnos actuales:</td>
						<td><?=$antiguosAlumnos?></td>
					</tr>
					<tr>
						<td class="text-right col-md-2">Nuevas incorporaciones</td>
						<td><?=$totalAlumnos-$antiguosAlumnos?></td>
					</tr>
			</table>
		</div>
	</div>    
</div>
<script>
	function asociarAlumno(id_usuario,id_contacto) {
		if (confirm("¿Seguro que quieres asociar este contacto al alumno existente?")) {
			window.location.href="asociar_alumno.php?id_contacto="+id_contacto+"&id_usuario="+id_usuario+"&origen=<?=$origen;?>&centro_origen=<?=$centro_enviado;?>&id_curso=<?=$id_curso_contacto;?>";
		}
	}
</script>
</body>
</html>