<?php
require_once __DIR__ . '/../../../../configuracion/encoding.php';
require("../../../../configuracion/config.php");
require("../../../../configuracion/autentifica.php");

$colores_areas=array("#678ef3","#f3af67","#80b369");

$id_aula=$_GET["id_aula"];


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

// Short-circuit if the client did not give us a date range.
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

// Lee las citas de la agenda para las fechas indicadas

$busca_programacion=db_query("SELECT * FROM agenda_programacion_ingles_calendario WHERE dia>='".$_GET['start']."' AND  dia<='".$_GET['end']."' AND estado=1 AND id_aula=$id_aula ORDER BY dia");

$input_arrays = array();
$contadores_actividades = array();

while($programacion=db_fetch_array($busca_programacion)) {

	$id_plantilla=$programacion["id_programacion"];
	$dia_programacion=$programacion["dia"];
	
	// Busca número de sesión
	$busca_programaciones_ingles=db_query("SELECT * FROM agenda_programacion_ingles_calendario WHERE dia<='".$dia_programacion."' AND estado=1 AND id_programacion=$id_plantilla AND id_aula=$id_aula") or die(db_error());	
	$num_sesion_hoy=db_num_rows($busca_programaciones_ingles);
	
	// Busca activiades hoy
	$busca_actividades=db_query("SELECT * FROM agenda_programacion_plantillas_actividades LEFT JOIN agenda_programacion_ingles_actividades ON agenda_programacion_plantillas_actividades.id_actividad=agenda_programacion_ingles_actividades.id_actividad, agenda_programacion_ingles_objetivos WHERE agenda_programacion_ingles_actividades.id_objetivo=agenda_programacion_ingles_objetivos.id_objetivo AND id_plantilla=$id_plantilla AND numero_sesion=$num_sesion_hoy ORDER BY orden_actividad,id_prog");
	
	$num_prog_ingles=db_num_rows($busca_actividades);
		
	while($actividades=db_fetch_array($busca_actividades)) {
		
		$contadores_actividades[$programacion["dia"]]++;
		
		$descripcion_actividad_actual=app_text_utf8(ucfirst($actividades["descripcion_actividad"]));		
		$objetivos_dia="";
		if (strlen($actividades["nombre_objetivo"])!=0) {
				$objetivos_dia=app_text_utf8(mb_convert_case($actividades["nombre_objetivo"], MB_CASE_UPPER).": ");
		}		
		
		$input_arrays[] = array("id"=>$programacion["id_dia"],"title"=>$objetivos_dia.$descripcion_actividad_actual,"start"=>$programacion["dia"]." ".($contadores_actividades[$programacion["dia"]]+8).":00:00","end"=>$programacion["dia"]." ".($contadores_actividades[$programacion["dia"]]+9).":00:00","color"=>$colores_areas[$actividades["area"]],"url"=>"/gestion/agenda_programacion_ingles/gestion_plantillas.php?id_plantilla=".$id_plantilla);
			
	}	

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