<?php 
require("../../configuracion/config.php");

$tipo=$_POST["tipo"];
$descripcion=urlencode($_POST["descripcion"]);
$medidas_adoptadas1=urlencode($_POST["medidas_adoptadas1"]);
$nombre_incidencia=$_POST["nombre_incidencia"];
if (strlen($nombre_incidencia)!=0) {
        $nombre_incidencia=strtoupper($nombre_incidencia);
        $nombre_incidencia=urlencode($nombre_incidencia);
        }
$id_usuario_incidencia=urlencode($_POST["id_usuario_incidencia"]);

$consulta_insert=db_query("INSERT INTO incidencias_2017 (id_centro,tipo,nombre_incidencia,id_usuario_incidencia,fecha_incidencia,hora_incidencia,notificada_por,descripcion,medidas_adoptadas1) VALUES ('$centro_actual','$tipo','$nombre_incidencia','$id_usuario_incidencia','".date("Y-m-d")."','".date("H:i:s")."','$idUsuario','$descripcion','$medidas_adoptadas1')") or die(db_error());

header("Location: menu_incidencias.php");
?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

