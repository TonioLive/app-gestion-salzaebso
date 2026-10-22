<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");


$id_mensaje=$_GET["id_mensaje"];

$busca_datos_mensaje=db_query("SELECT * FROM agenda_notas_padres WHERE id_nota_padre=$id_mensaje");

$datos_mensaje=db_fetch_array($busca_datos_mensaje);

if ($datos_mensaje["id_padre"]==$id_usuario) {
	$borra_mensaje=db_query("DELETE FROM agenda_notas_padres WHERE id_nota_padre=$id_mensaje");
	}

header("Location: mensajeria.php");

?>