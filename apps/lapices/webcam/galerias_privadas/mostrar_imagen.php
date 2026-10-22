<?php
require("../../configuracion/config.php");
require("../../configuracion/autentifica_padre.php");

$file = $_GET["file"];
$width = $_GET["width"];

/*
if (strlen($width)==0) {
	$width=640;
	}

// Ponemos el . antes del nombre del archivo porque estamos considerando que la ruta está a partir del archivo thumb.php
$file_info = getimagesize($file);
// Obtenemos la relacion de aspecto
$ratio = $file_info[0] / $file_info[1];

// Calculamos las nuevas dimensiones
$newwidth = $width;
$newheight = round($newwidth / $ratio);

// Sacamos la extension del archivo
$ext = explode(".", $file);
$ext = strtolower($ext[count($ext) - 1]);
if ($ext == "jpeg") $ext = "jpg";

// Dependiendo de la extension llamamos a distintas funciones
switch ($ext) {
        case "jpg":
                $img = imagecreatefromjpeg($file);
        break;
        case "png":
                $img = imagecreatefrompng($file);
        break;
        case "gif":
                $img = imagecreatefromgif($file);
        break;
}
// Creamos la miniatura
$thumb = imagecreatetruecolor($newwidth, $newheight);
// La redimensionamos
imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newwidth, $newheight, $file_info[0], $file_info[1]);
// La mostramos como jpg
header("Content-type: image/jpeg");
imagejpeg($thumb, null, 80);
*/

// Verifica que se haya recibido la ruta completa a través del parámetro GET
if (!isset($_GET['file'])) {
        http_response_code(400); // Solicitud incorrecta
        echo "Parámetro faltante";
        exit();
    }
    
// Obtén la ruta completa desde el parámetro GET
$ruta_imagen = $_GET['file'];

// Concatenar la ruta base y la imagen solicitada, y obtener la ruta absoluta
$ruta_imagen_solicitada = realpath($directorio_base . '/' . $ruta_imagen);

// Verifica que la ruta sea válida, que el archivo exista, y que esté dentro del directorio permitido
if (!$ruta_imagen_solicitada || !file_exists($ruta_imagen_solicitada) || !is_readable($ruta_imagen_solicitada)) {
http_response_code(404);
echo "Imagen no encontrada";
exit();
}

// Obtén la información del archivo
$info_imagen = getimagesize($ruta_imagen_solicitada);
$tipo_imagen = $info_imagen['mime'];

// Establece las cabeceras para que el navegador trate la respuesta como una imagen
header("Content-Type: $tipo_imagen");
header("Content-Length: " . filesize($ruta_imagen_solicitada));

// Sirve la imagen
readfile($ruta_imagen_solicitada);
exit();
?>
