<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$fecha=$_GET["fecha"];
$fecha_original=$_GET["fecha_original"];
$id_objetivo=$_GET["id_objetivo"];

// Comprueba si ya está

$mes_sel=date("n",strtotime($fecha_original));
$anyo_sel=date("Y",strtotime($fecha_original));


$busca_objetivo_mes=db_query("SELECT * FROM agenda_programacion_ingles_objetivos_recuperados WHERE aula=$id_aula AND id_objetivo=$id_objetivo AND mes=$mes_sel AND anyo=$anyo_sel") or die(db_error());

if (db_num_rows($busca_objetivo_mes)==0) {
	// Inserta mes
	$inserta_objetivo=db_query_params('INSERT INTO agenda_programacion_ingles_objetivos_recuperados (aula, id_objetivo, mes, anyo) VALUES (?, ?, ?, ?);', 'ssss', array((string)($id_aula), (string)($id_objetivo), (string)($mes_sel), (string)($anyo_sel)));
	}
	
header("Location: informe_actividades.php?id_aula=$id_aula&fecha=$fecha&$fecha_original=$fecha_original")
?>