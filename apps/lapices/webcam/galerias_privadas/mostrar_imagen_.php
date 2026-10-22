<?php
require("../../configuracion/config.php");
require("../../configuracion/autentifica_padre.php");

$file = $_GET["file"];
$width = isset($_GET["width"]) ? (int)$_GET["width"] : 0;

// Verifica que se haya recibido la ruta completa a través del parámetro GET
if (!isset($file)) {
    http_response_code(400); // Solicitud incorrecta
    echo "Parámetro faltante";
    exit();
}

// Obtén la ruta completa desde el parámetro GET
$ruta_imagen = $file;

// Concatenar la ruta base y la imagen solicitada, y obtener la ruta absoluta
$ruta_imagen_solicitada = realpath($directorio_base . '/' . $ruta_imagen);

// Verifica que la ruta sea válida, que el archivo exista, y que esté dentro del directorio permitido
if (!$ruta_imagen_solicitada || !file_exists($ruta_imagen_solicitada) || !is_readable($ruta_imagen_solicitada)) {
    http_response_code(404);
    echo "Imagen no encontrada";
    exit();
}

// Si el ancho es 0, muestra la imagen original
if ($width === 0) {
    header("Content-Type: " . mime_content_type($ruta_imagen_solicitada));
    header("Content-Length: " . filesize($ruta_imagen_solicitada));
    readfile($ruta_imagen_solicitada);
    exit();
}

// Genera la ruta del thumbnail
$directorio_thumbnail = dirname($ruta_imagen_solicitada) . '/' . $width;
if (!file_exists($directorio_thumbnail)) {
    mkdir($directorio_thumbnail, 0755, true);
}

$ruta_thumbnail = $directorio_thumbnail . '/' . basename($ruta_imagen_solicitada);

// Si el thumbnail ya existe, muéstralo
if (file_exists($ruta_thumbnail)) {
    header("Content-Type: " . mime_content_type($ruta_thumbnail));
    header("Content-Length: " . filesize($ruta_thumbnail));
    readfile($ruta_thumbnail);
    exit();
}

// Genera el thumbnail si no existe
$info_imagen = getimagesize($ruta_imagen_solicitada);
$tipo_imagen = $info_imagen['mime'];
list($ancho_original, $alto_original) = $info_imagen;

$alto = ($width / $ancho_original) * $alto_original;

$imagen_original = null;
switch ($tipo_imagen) {
    case "image/jpeg":
        $imagen_original = imagecreatefromjpeg($ruta_imagen_solicitada);
        break;
    case "image/png":
        $imagen_original = imagecreatefrompng($ruta_imagen_solicitada);
        break;
    case "image/gif":
        $imagen_original = imagecreatefromgif($ruta_imagen_solicitada);
        break;
    default:
        http_response_code(415); // Tipo de archivo no compatible
        echo "Tipo de archivo no compatible";
        exit();
}

$imagen_thumbnail = imagecreatetruecolor($width, $alto);
imagecopyresampled($imagen_thumbnail, $imagen_original, 0, 0, 0, 0, $width, $alto, $ancho_original, $alto_original);

// Guarda el thumbnail
switch ($tipo_imagen) {
    case "image/jpeg":
        imagejpeg($imagen_thumbnail, $ruta_thumbnail, 85);
        break;
    case "image/png":
        imagepng($imagen_thumbnail, $ruta_thumbnail);
        break;
    case "image/gif":
        imagegif($imagen_thumbnail, $ruta_thumbnail);
        break;
}

imagedestroy($imagen_original);
imagedestroy($imagen_thumbnail);

// Muestra el thumbnail generado
header("Content-Type: $tipo_imagen");
header("Content-Length: " . filesize($ruta_thumbnail));
readfile($ruta_thumbnail);
exit();
?>
