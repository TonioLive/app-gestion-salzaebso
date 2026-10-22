<?php
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

foreach ($_POST['actividad'] as $position => $item)
{
	$actualiza_orden=db_query("UPDATE agenda_programacion_plantillas_actividades SET orden_actividad=".$position." WHERE id_prog=".$item);
}	

?>