<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$carpeta=$_GET["carpeta"];

rmdir($carpeta);

header("Location: index.php?id_aula=$id_aula");
?>