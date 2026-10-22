<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica_padre.php");

$nombres_eventos=array("Festivo","Inicio curso","Fin curso","Aistencia","Asiste","Falta","Inicio periodo","Fin periodo","Inicio curso","Fin curso","Inicio asistencia","Fin asistencia");
$colores_areas=array(
	'festivo'=>"#ef6666",
	'inicio_curso'=>"#c8c10a",
	'fin_curso'=>"#e79826",
	'inicio_periodo'=>"#f3af67",
	'fin_periodo'=>"#80b369",
	'inicio_asistencia'=>"#2666e7",
	'fin_asistencia'=>"#e79826",
	'inicio_periodo'=>"#2fb0cc",
	'fin_periodo'=>"#b726e7",	
	'asistencia'=>"#469e2e",
	'cita_agenda'=>"d2d2d2",
	'falta_asistencia'=>'#e71303'
	);

$campos_inicio_dias=array(
	1=>'l',
	2=>'m',
	3=>'x',
	4=>'j',
	5=>'v',
	6=>'s',
	7=>'d',	
);
	
//--------------------------------------------------------------------------------------------------
// This script reads event data from a JSON file and outputs those events which are within the range
// supplied by the "start" and "end" GET parameters.
//
// An optional "timezone" GET parameter will force all ISO8601 date stings to a given timezone.
//
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

// Require our Event class and datetime utilities
require dirname(__FILE__) . '/utils.php';

// Short-circuit if the client did #fa6122 give us a date range.
if (!isset($_GET['start']) || !isset($_GET['end'])) {
	die("Please provide a date range.");
}

// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
// Since no timezone will be present, they will parsed as UTC.
$range_start = parseDateTime($_GET['start']);
$range_end = parseDateTime($_GET['end']);

// Parse the timezone parameter if it is present.
$timezone = null;
if (isset($_GET['timezone'])) {
	$timezone = new DateTimeZone($_GET['timezone']);
}

// Lee los eventos de la agenda para las fechas indicadas
$centros_hijos = "";
$aulas_hijos = "";

$input_arrays = array();

// Busca alumnos a cargo de este usuario
$fechalimite=strtotime(date("Y-m-d"))+(86400*15);	
$busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

$array_alumnos=array();

while($datos_alumnos=db_fetch_array($busca_datos_alumno)) {
	
		$nombre_completo_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"].$datos_alumnos["Apellido2"];
		
		// Inicio y fin del alumno
		if (($datos_alumnos["fecha_matricula"]>=$_GET['start']) and ($datos_alumnos["fecha_matricula"]<=$_GET['end'])) {
			$input_arrays[] = array("id"=>"IA".$datos_alumnos["id_usuario"],"title"=>"Inicio asistencia ".$datos_alumnos["Nombre"],"start"=>$datos_alumnos["fecha_matricula"],"color"=>$colores_areas['inicio_asistencia']);					
		}
		if (($datos_alumnos["fecha_baja"]>=$_GET['start']) and ($datos_alumnos["fecha_baja"]<=$_GET['end'])) {
			$input_arrays[] = array("id"=>"FA".$datos_alumnos["id_usuario"],"title"=>"Fin asistencia ".$datos_alumnos["Nombre"],"start"=>$datos_alumnos["fecha_baja"],"color"=>$colores_areas['fin_asistencia']);				
		}

		$filtro_usuarios.="";
		
		// Datos del grupo del niño
		$busca_grupos_alumno=db_query("SELECT * FROM usuarios,aulas_alumnos_grupos,aulas_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_grupos.id_curso=".$datos_alumnos["id_curso"]." AND alumnos.id_usuario=".$datos_alumnos["padre_de"]) or die(db_error());                
		
		$lista_grupos_alumno="";
		$ids_grupos=array();
		while($grupos_donde=db_fetch_array($busca_grupos_alumno)) {
			$lista_grupos_alumno.=$dia_sem_largo[$grupos_donde["dia"]]." de ".date("H:i",strtotime($grupos_donde["hora_inicio"]))." a ".date("H:i",strtotime($grupos_donde["hora_fin"]))."<br />";
			$ids_grupos[] = $grupos_donde["id_grupo"];
			
			// Asistencias, Inicio y fin de los periodos de los alumnos
			$diasemana=$grupos_donde["dia"];
			
			$busca_trimestres=db_query("SELECT * FROM aulas_grupos_trimestres WHERE fecha_inicio<=".$_GET['end']." OR fecha_fin>=".$_GET['start'].";") or die(db_error());

			while($datos_trimestre=db_fetch_array($busca_trimestres)) {
						
				$fecha_inicio_proximo_trimestre=$datos_trimestre["ini_".$campos_inicio_dias[$diasemana]];
				$fecha_fin_proximo_trimestre=$datos_trimestre["fin_".$campos_inicio_dias[$diasemana]]; 

				// Inicio y fin del periodo
				if (($fecha_inicio_proximo_trimestre>=$_GET['start']) and ($fecha_inicio_proximo_trimestre<=$_GET['end'])) {
					$input_arrays[] = array("id"=>"IT".$datos_trimestre["id_trimestre"],"title"=>"Inicio ".$datos_trimestre["nombre_trimestre"]." grupo ".$dia_sem_largo[$diasemana],"start"=>$fecha_inicio_proximo_trimestre,"color"=>$colores_areas['inicio_periodo']);					
				}
				if (($fecha_fin_proximo_trimestre>=$_GET['start']) and ($fecha_fin_proximo_trimestre<=$_GET['end'])) {
					$input_arrays[] = array("id"=>"FT".$datos_trimestre["id_trimestre"],"title"=>"Fin ".$datos_trimestre["nombre_trimestre"]." grupo ".$dia_sem_largo[$diasemana],"start"=>$fecha_fin_proximo_trimestre,"color"=>$colores_areas['fin_periodo']);				
				}
				
				$periodo=$datos_trimestre["id_trimestre"];

				// buscamos numero de sesiones previstas para el proximo trimestre
				$num_dias_trimestre=0;
				$inicio_bucle=strtotime($fecha_inicio_proximo_trimestre)+43200;
				$fin_bucle=strtotime($fecha_fin_proximo_trimestre)+43200;

						while($inicio_bucle<=$fin_bucle) {
										
								// Comprueba si es festivo
								$busca_cal_laboral=db_query("SELECT * FROM calendario_laboral WHERE dia='".date("Y-m-d",$inicio_bucle)."' AND estado=1");                
								if (db_num_rows($busca_cal_laboral)==0) {
									
									if ((date('Y-m-d',$inicio_bucle)>=$_GET['start']) and (date('Y-m-d',$inicio_bucle)<=$_GET['end']) and (date('Y-m-d',$inicio_bucle)>=$datos_alumnos['fecha_matricula']) and (date('Y-m-d',$inicio_bucle)<=$datos_alumnos['fecha_baja'])) {

										// Comprueba si asiste
										$asiste=true;
										
										if ($inicio_bucle<strtotime(date("Y-m-d"))) {
											
											$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$datos_alumnos["padre_de"]." AND fecha='".date("Y-m-d",$inicio_bucle)."'") or die(db_error());
											
											if (db_num_rows($busca_registro_entrada)==0) {
												$asiste=false;	
											}
											
										}
										
										if ($asiste==true) {
											$input_arrays[] = array("id"=>"AS".$inicio_bucle,"title"=>"Asitencia ".$datos_alumnos["Nombre"],"start"=>date('Y-m-d',$inicio_bucle)." ".$grupos_donde["hora_inicio"],"end"=>date('Y-m-d',$inicio_bucle)." ".$grupos_donde["hora_fin"],"color"=>$colores_areas['asistencia']);
										} else {
											$input_arrays[] = array("id"=>"AS".$inicio_bucle,"title"=>"Falta de Asitencia ".$datos_alumnos["Nombre"],"start"=>date('Y-m-d',$inicio_bucle)." ".$grupos_donde["hora_inicio"],"end"=>date('Y-m-d',$inicio_bucle)." ".$grupos_donde["hora_fin"],"color"=>$colores_areas['falta_asistencia']);												
										}
										
									}
									
								}
								
								$inicio_bucle=$inicio_bucle+(86400*7);
						}
			}
			
			// Fin asistencias			
			
			}
		
		$array_alumnos[] = array(
			'id_usuario' => $datos_alumnos["id_usuario"],
			'id_alumno' => $datos_alumnos["id_alumno"],			
			'nombre_alumno' => $nombre_completo_alumno,
			'inicio_asistencia' => $datos_alumnos["fecha_matricula"],
			'fin_asistencia' => $datos_alumnos["fecha_baja"],
			'grupos' => $lista_grupos_alumno,
			'ids_grupos' => $ids_grupos
		);
		
		$aulas_hijos.=" OR (Agenda.id_aula=".$datos_alumnos["aula"].")";		
		$centros_hijos.=" OR (Agenda.id_centro=".$datos_alumnos["id_centro"]." AND Agenda.id_aula=0)";
}

$filtro_usuarios=" AND (Agenda.id_centro=0$centros_hijos$aulas_hijos)";

// Festivos

$busca_festivos=db_query("SELECT * FROM calendario_laboral WHERE dia>='".$_GET['start']."' AND dia<='".$_GET['end']."' AND estado=1 ORDER BY dia") or die(db_error());

while ($festivos=db_fetch_array($busca_festivos)) {

	if ((int) date('N', strtotime($festivos["dia"])) < 6) {
		$input_arrays[] = array("id"=>"DF".$festivos["dia"],"title"=>"Piscina Cerrada","start"=>$festivos["dia"],"color"=>$colores_areas['festivo']);
	}
	
}

// Inicio de cursos
$busca_cursos=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".$_GET['start']."' AND fecha_inicio<='".$_GET['end']."' ORDER by fecha_inicio") or die(db_error());

while ($cursos=db_fetch_array($busca_cursos)) {

	$input_arrays[] = array("id"=>"IC".$programacion["id_curso"],"title"=>"Inicio del Curso ".$cursos["nombre"],"start"=>$cursos["fecha_inicio"],"color"=>$colores_areas['inicio_curso']);
	
}

// Fin de cursos
$busca_cursos=db_query("SELECT * FROM cursos_academicos WHERE fecha_fin>='".$_GET['start']."' AND fecha_fin<='".$_GET['end']."' ORDER by fecha_fin");

while ($cursos=db_fetch_array($busca_cursos)) {

	$input_arrays[] = array("id"=>"FC".$programacion["id_curso"],"title"=>"Fin del Curso ".$cursos["nombre"],"start"=>$cursos["fecha_fin"],"color"=>$colores_areas['fin_curso']);
	
}

// Citas en la agenda
$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND Agenda.Fecha>='".$_GET['start']."' AND Agenda.Fecha<='".$_GET['end']."' AND agenda_asistentes.nivel=1$filtro_usuarios GROUP BY Agenda.id ORDER BY Agenda.id_centro,Agenda.Hora,Agenda.id_aula");

while($rsNot=db_fetch_array($rsNot_query)) {
	$input_arrays[] = array("id"=>"CI".$rsNot["id_curso"],"title"=>app_text_utf8($rsNot["Titulo"])."\n".app_text_utf8($rsNot["Texto"]),"start"=>$rsNot["Fecha"]." ".$rsNot["Hora"],"color"=>$colores_areas['cita_agenda']);	
}

// Accumulate an output array of event data arrays.
$output_arrays = array();
foreach ($input_arrays as $array) {

	// Convert the input array into a useful Event object
	$event = new Event($array, $timezone);

	// If the event is in-bounds, add it to the output
	if ($event->isWithinDayRange($range_start, $range_end)) {
		$output_arrays[] = $event->toArray();
	}
}

// Send JSON to the client.
echo json_encode($output_arrays);
