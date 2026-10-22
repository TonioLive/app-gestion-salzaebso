<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$fila_modificada=$_POST["fila"];
$campo_modificado=$_POST["campo"];
$valor_modificado=app_text_utf8($_POST["valor"]);

$cambia_linea=db_query("UPDATE agenda_alumnos_comidas SET $campo_modificado=$valor_modificado WHERE id=$fila_modificada") or die(db_error());

echo('ok');
?>
