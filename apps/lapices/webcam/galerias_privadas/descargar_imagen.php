<?php 
require("../../configuracion/config.php");

$file = $_GET["file"];

$file_size = filesize($file);

$s = basename($file);

if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (mb_strtoupper(substr($s,strlen($s)-4,4))=="JPEG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PNG") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="GIF")) {

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$s.'"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . $file_size); //Absolute URL
    ob_clean();
    flush();
    readfile($file);
    exit();

} else {

    exit("No es una imagen");

}
?>
