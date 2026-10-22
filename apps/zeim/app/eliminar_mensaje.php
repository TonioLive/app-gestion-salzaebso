<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");


$id_mensaje=$_POST["id_mensaje"];

$busca_datos_mensaje=db_query_params('SELECT * FROM agenda_notas_padres WHERE id_nota_padre=?', 's', array($id_mensaje));

$datos_mensaje=db_fetch_array($busca_datos_mensaje);

if ($datos_mensaje["id_padre"]==$id_usuario) {
	$borra_mensaje=db_query_params('DELETE FROM agenda_notas_padres WHERE id_nota_padre=?', 's', array($id_mensaje));
	}

header("Location: mensajeria.php");

?>