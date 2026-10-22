<?php  require("../../configuracion/config.php");

require("../../configuracion/autentifica_admin.php");

require("config/config_personal.php");

// Crea el documento definitivo
$nombre_doc=$_GET["id_doc"];
$id_usu=$_GET["id_usu"];

$carpeta_documentos_actual=$carpeta_documentos."\\usuario_$id_usu";

$nombre_archivo=$carpeta_documentos_actual."\\$nombre_doc";

// Borra archivo

@unlink($nombre_archivo);

header("Location: ficha.php?id_usu=$id_usu");

?>
<html>
<head>
</head>
<body>
</body>
</html>

