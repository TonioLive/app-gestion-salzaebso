<?php
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

$busca_programacion=db_query_params('SELECT * FROM agenda_programacion WHERE fecha>=? AND  fecha<=? AND id_aula=? ORDER BY fecha,tiempo_trabajo,orden_actividad', 'sss', array((string)($_GET['start']), (string)($_GET['end']), $id_aula));

$input_arrays = array();

while($programacion=db_fetch_array($busca_programacion)) {

	if (strlen($programacion["actividad"])!=0) {
	
		if ($programacion["id_actividad"]!=0) {
                                        $descripcion_actividad_actual=utf8_encode($programacion["actividad"]);
                                        } else {
                                        $descripcion_actividad_actual=$programacion["actividad"];
                                        }
	
										// Busca nombre objetivo
										$objetivos_dia="";
                                        if ($programacion["objetivo"]!=0) {
                                        $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$programacion["objetivo"]." AND id_area=".$programacion["area"]);
                                        if (db_num_rows($busca_datos_objetivo)!=0) {
                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                        $objetivos_dia=utf8_encode($datos_objetivo["nombre_objetivo"]);
                                                        }
                                                } 	
         
        $descripcion_actividad_actual=$descripcion_actividad_actual;                                      
		$input_arrays[] = array("id"=>$programacion["id_prog"],"title"=>$descripcion_actividad_actual,"start"=>$programacion["fecha"]." ".($programacion["tiempo_trabajo"]+8).":00:00","end"=>$programacion["fecha"]." ".($programacion["tiempo_trabajo"]+9).":00:00","color"=>$colores_areas[$programacion["area"]],"url"=>"/gestion/agenda_dia/semana_programacion.php?id_aula=$id_aula&fecha_inicio=".$programacion["fecha"]."&pagina_activa=".$programacion["tiempo_trabajo"]);
		
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