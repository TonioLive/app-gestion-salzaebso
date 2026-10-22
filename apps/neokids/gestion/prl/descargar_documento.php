<?php  require("../../configuracion/config.php");

require("../../configuracion/autentifica_admin.php");

require("config/config_personal.php");

// Crea el documento definitivo
$nombre_doc=$_GET["id_doc"];

$carpeta_documentos_actual=$carpeta_documentos."//usuario_$id_usu";

$nombre_archivo=$carpeta_documentos_actual."//$nombre_doc";

include("EasyDownload.php");


// path example
$myPath = $carpeta_documentos_actual;

// New Object
$objDownload = new EasyDownload();

// Set physical path
$objDownload->setPath($myPath);

// Set file name on the server (real full name)
$objDownload->setFileName($nombre_doc);

// In case that it does not desire to effect download with original name.
// It configures the alternative name
$objDownload->setFileNameDown($nombre_doc);

// get file
$objDownload->Send();

?>
<html>
<head>
</head>
<body>
</body>
</html>

