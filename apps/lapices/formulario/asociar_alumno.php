<?php
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

$id_contacto = $_GET["id_contacto"];
$id_usuario = $_GET["id_usuario"];
$origen = $_GET["origen"];
$centro_origen = $_GET["centro_origen"];
$id_curso = $_GET["id_curso"];

// Asociar el alumno   
$asociarAlumno = db_query("UPDATE contactos_web SET id_usuario_alumno='$id_usuario' WHERE id_contacto=$id_contacto;");

header("Location: listado.php?id_curso=$id_curso");
?>