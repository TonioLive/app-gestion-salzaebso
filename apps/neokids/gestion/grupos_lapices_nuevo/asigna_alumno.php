<?php 
require("../../configuracion/config.php");

$id_grupo=$_GET["id_grupo"];
$id_alumno=$_GET["id_alumno"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha=$_GET["fecha"];
$origen=$_GET["origen"];


// Busca nivel alumno
$busca_datos_alumno=db_query_params('SELECT * FROM gestionguarderias.alumnos WHERE id_alumno=?', 's', array($id_alumno));

$datos_alumno=db_fetch_array($busca_datos_alumno);

$nivel_anterior=$datos_alumno["nivel_actual"];

$busca_subgrupo=db_query_params('SELECT * FROM aulas_grupos WHERE id_grupo=?', 's', array($id_grupo));

$datos_grupo=db_fetch_array($busca_subgrupo);

$id_curso_grupo=$datos_grupo["id_curso"];

// busca si está
$busca_en_grupo=db_query_params('SELECT * FROM aulas_alumnos_lapices_grupos WHERE id_alumno=? AND id_grupo=? AND id_curso=?', 'sss', array($id_alumno, $id_grupo, $id_curso_grupo));

if (db_num_rows($busca_en_grupo)==0) {
	db_query_params('INSERT INTO aulas_alumnos_lapices_grupos (id_alumno,id_grupo,id_curso,id_subgrupo) VALUES (?,?,?,?)', 'ssss', array((string)($id_alumno), (string)($id_grupo), (string)($id_curso_grupo), (string)($id_subgrupo)));
	}

header("Location: $origen");        
?>
