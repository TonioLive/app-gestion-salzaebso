<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$fecha=$_GET["fecha"];
$grupo=$_POST["id_grupo"];
if (strlen($grupo)==0) {
        $grupo=$_GET["id_grupo"];
        }

$error_no_quita="";

// Comprueba si está ocupado
$busca_ultimo_subgrupo=db_query("SELECT * FROM aulas_grupos WHERE id_grupo=".$grupo);
$datos_grupo=db_fetch_array($busca_ultimo_subgrupo);

$id_curso_actual=$datos_grupo["id_curso"];

$busca_alumnos_subgrupo=db_query("(SELECT id_grupo FROM aulas_alumnos_grupos WHERE id_grupo=$grupo AND id_subgrupo=".($datos_grupo["num_subgrupos"]-1).") UNION (SELECT id_grupo FROM aulas_alumnos_lapices_grupos WHERE id_grupo=$grupo AND id_subgrupo=".($datos_grupo["num_subgrupos"]-1).")");

if (db_num_rows($busca_alumnos_subgrupo)==0) {

	// No hay alumnos, se puede borrar 

	$actualiza=db_query("UPDATE aulas_grupos SET num_subgrupos=(num_subgrupos-1) WHERE id_grupo=".$grupo) or die(db_error());

	// busca datos subgrupo

	$busca_subgrupo=db_query("SELECT * FROM aulas_grupos WHERE id_grupo=".$grupo);

	$datos_grupo=db_fetch_array($busca_subgrupo);

	$datos_subgrupo=db_query("DELETE FROM aulas_grupos_subgrupos WHERE id_grupo=".$grupo." AND id_subgrupo=".	($datos_grupo["num_subgrupos"]+1).";");

} else {

	// Hay alumnos, NO se puede borrar

	$error_no_quita="&error_al_quitar=El grupo contiene alumnos (¿Hás comprobado el horario de alumnos externos?), no se puede quitar.";

}

header("Location: index.php?curso_seleccionado=$id_curso_actual&fecha=$fecha".$error_no_quita);

?>
