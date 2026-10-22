<?php
require("../../configuracion/config.php");
require("../../configuracion/autentifica_padre.php");

$id_alu=$_GET["id_alu"];
$id_autorizado=$_GET["id_autorizado"];
$newwidth=$_GET["width"];

$nombre_usuario_padre=$_SESSION['nombre_padre'];

// Busca usuario
$busca_usuario_padre=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'") or die(db_error());
        
if (db_num_rows($busca_usuario_padre)==0) {                
                $id_usuario_padre=0;
                } else {
                $datos_usuario_padre=db_fetch_array($busca_usuario_padre);
                $id_usuario_padre=$datos_usuario_padre["id_usuario"];
                }

$busca_hijo_de_padre=db_query("SELECT * FROM padres WHERE id_usuario=$id_usuario_padre AND padre_de=$id_alu") or die(db_error());

if (db_num_rows($busca_hijo_de_padre)!=0) {

	// Comprueba si puedo mostrar esta imagen
	$busca_datos_padre=db_query("SELECT * FROM padres WHERE id_padre=$id_autorizado AND padre_de=$id_alu") or die(db_error()); 
	
	if (db_num_rows($busca_datos_padre)==0) {
	
		exit("Se ha detectado una actividad sospechosa. No tienes permiso para acceder a los datos que solicitas.");	
		
	}

	$datos_autorizado=db_fetch_array($busca_datos_padre);
	
	$id_usu_autorizado=$datos_autorizado["id_usuario"];

	header('Content-Type: image/jpeg');

	$filename='../../dni_usuarios/dni_usu'.$id_usu_autorizado.'.jpg';

	list($width, $height) = getimagesize($filename);
	
	if (!$newwidth) {
		$newwidth = $width;
		$newheight = $height;	
	} else {
		
		$percent = round($newwidth/$width,2);		
		
		$newheight = $height * $percent;	
	}

	// Load
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	$source = imagecreatefromjpeg($filename);
	
	// Resize
	imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	
	// Output and free memory
	//the resized image will be 400x300
	imagejpeg($thumb);
	imagedestroy($thumb);

}	
	
