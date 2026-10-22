<?php  require("../../configuracion/config.php");

require("../../configuracion/autentifica_docente.php");

require("config/config_personal.php");

// Crea el documento definitivo
$nombre_doc=$_GET["id_doc"];

$carpeta_documentos_actual=$carpeta_documentos."//usuario_$idUsuario";

$nombre_archivo=$carpeta_documentos_actual."//$nombre_doc";

// Borra archivo

@unlink($nombre_archivo);

header("Location: ficha.php");

?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

