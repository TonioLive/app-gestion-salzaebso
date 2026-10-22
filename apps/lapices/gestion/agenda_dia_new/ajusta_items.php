<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

//  Mueres por seguridad…
die();


$busca_objetivos=db_query("SELECT * FROM agenda_programacion_objetivos");

while($objetivos=db_fetch_array($busca_objetivos)){

	$inserta=db_query("INSERT INTO agenda_evaluacion_items (id_evaluacion,descripcion) VALUES ('".$objetivos["id_objetivo"]."','".$objetivos["nombre_objetivo"]."');");

	}


$update_dd=db_query("UPDATE agenda_programacion_objetivos_ITEMS SET id_evaluacion=0");

$busca_objetivos1=db_query("SELECT * FROM agenda_programacion_objetivos_import WHERE id_evaluacion>0") or die(db_error());

echo(db_num_rows($busca_objetivos1));

while($objetivos1=db_fetch_array($busca_objetivos1)){

	$actualiza=db_query("UPDATE agenda_programacion_objetivos_ITEMS SET id_evaluacion=".$objetivos1["id_evaluacion"]." WHERE id_objetivo=".$objetivos1["id_objetivo"].";") or die(db_error());

	}


?>
