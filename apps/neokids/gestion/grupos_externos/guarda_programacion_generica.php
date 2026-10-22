<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_POST["id_grupo"];
$id_subgrupo=$_POST["id_subgrupo"];
$fecha=$_POST["fecha"];
$programacion=$_POST["duallistbox_ActividadesProgramadas"];
$nombre_mostrar=$_POST["nombre_mostrar"];

// Borra programacion actual
$borra_programacion=db_query_params('DELETE FROM agenda_programacion_externos WHERE id_grupo=? AND id_subgrupo=? AND fecha=?', 'sss', array($id_grupo, $id_subgrupo, (string)(date("Y-m-d", $fecha))));

foreach ($programacion as $fila){ 

	// Inserta
	$inserta=db_query_params('INSERT INTO agenda_programacion_externos (id_grupo,id_subgrupo,fecha,id_actividad) VALUES (?,?,?,?);', 'ssss', array((string)($id_grupo), (string)($id_subgrupo), (string)(date("Y-m-d", $fecha)), (string)($fila)));

}

// Borra programacion actual
$borra_programacion=db_query_params('DELETE FROM agenda_programacion_externos WHERE id_grupo=? AND id_subgrupo=? AND fecha=\'0000-00-00\'', 'ss', array($id_grupo, $id_subgrupo));

foreach ($programacion as $fila){ 

	// Inserta
	$inserta=db_query_params('INSERT INTO agenda_programacion_externos (id_grupo,id_subgrupo,fecha,id_actividad) VALUES (?,?,\'0000-00-00\',?);', 'sss', array((string)($id_grupo), (string)($id_subgrupo), (string)($fila)));

}

// header("Location: gestion_programacion.php?fecha=$fecha&id_grupo=$id_grupo&id_subgrupo=$id_subgrupo&nombre_mostrar=$nombre_mostrar");

?>
<script>
	window.close();
</script>