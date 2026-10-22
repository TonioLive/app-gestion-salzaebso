<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_plantilla=$_POST["id_plantilla"];
$monitor=$_POST["monitor"];
$descripcion=$_POST["descripcion"];
$nivel=$_POST["nivel"];
$num_sesiones=$_POST["num_sesiones"];
$retorno=$_POST["retorno"];
$sesion_editada=$_POST["sesion_editada"];

if ($id_plantilla!=0) {

	$modifica_actividad=db_query_params('UPDATE agenda_programacion_plantillas SET descripcion=?, nivel=?, num_sesiones=?, modif_por=?, fecha_ultima_mod=?, monitor=? WHERE id_plantilla=?', 'sssssss', array((string)($descripcion), (string)($nivel), (string)($num_sesiones), (string)($idUsuario), (string)(date("Y-m-d")), (string)($monitor), $id_plantilla));

} else {

	$inserta_actividad=db_query_params('INSERT INTO agenda_programacion_plantillas (descripcion, nivel, num_sesiones, creada_por, fecha_ultima_mod, modif_por, monitor) VALUES (?, ?, ?,?, ?,?, ?)', 'sssssss', array((string)($descripcion), (string)($nivel), (string)($num_sesiones), (string)($idUsuario), (string)(date("Y-m-d")), (string)($idUsuario), (string)($monitor)));
	
	$id_plantilla=db_insert_id();

}



switch ($retorno) {
    case 0:
		header("Location: gestion_programaciones.php");
        break;
    case 1:
        header("Location: gestion_plantillas.php?id_plantilla=$id_plantilla");
        break;
    case 2:
        header("Location: editar_plantillas.php?sesion_editada=$sesion_editada&id_plantilla=$id_plantilla");
        break;
}

?>