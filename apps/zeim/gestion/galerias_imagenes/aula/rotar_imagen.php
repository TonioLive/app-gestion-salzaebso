<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

ini_set("display_errors", '0');
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

$id_aula=$_GET["id_aula"];

$imagen=$_GET["imagen"];
$carpeta=$_GET["carpeta"];
$angulo=$_GET["angulo"];

// Sacamos la extension del archivo
$ext = explode(".", $imagen);
$ext = strtolower($ext[count($ext) - 1]);
if ($ext == "jpeg") $ext = "jpg";

// Dependiendo de la extension llamamos a distintas funciones

switch ($ext) {
    case "jpg":
            $img = imagecreatefromjpeg($imagen);
    break;
    case "png":
            $img = imagecreatefrompng($imagen);
    break;
    case "gif":
            $img = imagecreatefromgif($imagen);
    break;
}

$rotar = imagerotate($img, $angulo, 0);
   
switch ($ext) {
    case "jpg":
        imagejpeg($rotar,$imagen);
    break;
    case "png":
        imagepng($rotar,$imagen);
    break;
    case "gif":
        imagegif($rotar,$imagen);
    break;
}

imagedestroy($img);

header("Location: index.php?carpeta=$carpeta&id_aula=$id_aula");

/*
header("Content-type: image/jpeg");
imagejpeg($rotar, null, 80);
*/
?>