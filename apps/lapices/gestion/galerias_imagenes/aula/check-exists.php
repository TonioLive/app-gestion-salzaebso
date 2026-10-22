<?php
require_once __DIR__ . '/../../../configuracion/deployment-config.php';
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Define a destination

$id_aula=$_POST["id_aula"];
$carpeta=$_POST["carpeta"];

// datos aula y centro
$busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula.";");
$datos_aulas=db_fetch_array($busca_aulas);
$busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_aulas["id_centro"]);
$datos_escuelas=db_fetch_array($busca_escuelas);
$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_aulas["id_curso"]);
$datos_curso_actual=db_fetch_array($busca_curso_actual);

$nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
$nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$datos_aulas["id_curso"];
$nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
$nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];

$uploadDir=(lapices_storage_path('fotos_padres') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula").$carpeta;

if (file_exists($uploadDir . '/' . $_POST['filename'])) {
	// echo 1;
	echo 0;
} else {
	echo 0;
}
?>