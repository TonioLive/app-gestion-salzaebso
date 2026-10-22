<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$imagen=$_GET["imagen"];
$carpeta=$_GET["carpeta"];

unlink($imagen);

header("Location: index.php?carpeta=$carpeta&id_aula=$id_aula");
?>