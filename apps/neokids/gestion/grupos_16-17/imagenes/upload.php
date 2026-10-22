<?php

require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

// upload.php
// 'images' refers to your file input name attribute
if (empty($_FILES['images'])) {
    $output = array('error'=>'Error: No se ha encontrado el archivo seleccionado.');
    echo json_encode($output); 
    // or you can throw an exception 
    return; // terminate
}

// get the files posted
$images = $_FILES['images'];

$id_actividad = $_POST['id_actividad'];

if ($id_actividad) {

if (substr($id_actividad,0,1)=="O") {

	$output = array('error'=>'Error: Has seleccionado un objetivo, solo se pueden asociar imágenes a las actividades.');

	echo json_encode($output);
	
	exit();
	
}

// a flag to see if everything is ok
$success = null;

// get file names
$filenames = $images['name'];

// loop and process files
for($i=0; $i < count($filenames); $i++){
    $ext = explode('.', basename($filenames[$i]));
    $target = $web_path."/gestion/grupos_16-17/imagenes";
    
	$nombre_archivo=$images['name'];
    $nombre_temp=$images['tmp_name'];

    $nombre_archivo=$id_actividad.".jpg";

    if (!file_exists($target."/$nombre_archivo")) {
			$cambia_nombre=rename($nombre_temp, $target."/$nombre_archivo");
		} else {
			unlink($target."/$nombre_archivo");
			$cambia_nombre=rename($nombre_temp, $target."/$nombre_archivo");
  		}    
    
    $success = true;
    
}

// check and process based on successful status 
if ($success === true) {

    $output = array();

} else {

    $output = array('error'=>'Error: No se ha procesado el archivo.');
    
}

// return a json encoded response for plugin to process successfully
echo json_encode($output);

} else {

$output = array('error'=>'Error: Selecciona primero la actividad');

echo json_encode($output);

}