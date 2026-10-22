<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Set the uplaod directory

$id_aula=$_POST["id_aula"];

$carpeta=$_POST["carpeta"];

// datos aula y centro
$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($id_aula));
$datos_aulas=db_fetch_array($busca_aulas);
$busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aulas["id_centro"]));
$datos_escuelas=db_fetch_array($busca_escuelas);
$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($datos_aulas["id_curso"]));
$datos_curso_actual=db_fetch_array($busca_curso_actual);

$nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
$nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$datos_aulas["id_curso"];
$nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
$nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];

$uploadDir="/var/www/vhosts/escuelainfantilsantpau.com/fotos_padres/$nombre_del_curso/$nombre_escuela/$nombre_aula".$carpeta."/";

// Set the allowed file extensions
$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile   = $_FILES['Filedata']['tmp_name'];
	// $uploadDir  = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
	$targetFile = $uploadDir . $_FILES['Filedata']['name'];

	// Validate the filetype
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

		// Save the file
		move_uploaded_file($tempFile, $targetFile);
		echo 1;

	} else {

		// The file type wasn't allowed
		echo 'Tipo de archivo no permitido.';

	}
}
?>