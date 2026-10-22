<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_plantilla=$_POST["id_plantilla"];
$descripcion=$_POST["descripcion"];
$nivel=$_POST["nivel"];
$num_sesiones=$_POST["num_sesiones"];
$retorno=$_POST["retorno"];
$sesion_editada=$_POST["sesion_editada"];
$periodicidad=$_POST["periodicidad"];

if ($id_plantilla!=0) {

	$modifica_actividad=db_query("UPDATE agenda_programacion_plantillas SET descripcion='$descripcion', nivel='$nivel', num_sesiones='$num_sesiones', modif_por='$idUsuario', fecha_ultima_mod='".date("Y-m-d")."', periodicidad='$periodicidad' WHERE id_plantilla=$id_plantilla");

} else {

	$inserta_actividad=db_query("INSERT INTO agenda_programacion_plantillas (descripcion, nivel, num_sesiones, creada_por, fecha_ultima_mod, modif_por,periodicidad) VALUES ('$descripcion', '$nivel', '$num_sesiones','$idUsuario', '".date("Y-m-d")."','$idUsuario','$periodicidad')");
	
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