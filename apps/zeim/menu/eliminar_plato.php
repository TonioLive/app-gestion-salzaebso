<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_plato=$_GET["id_plato"];

$borrar_ingredientes_plato = db_query_params('DELETE FROM menu_ingredientes WHERE id_plato=?', 's', array($id_plato)) or die (db_error());
$borrar_plato = db_query_params('DELETE FROM menu_platos WHERE id_plato=?', 's', array($id_plato)) or die (db_error());

header("Location:gestion_platos.php");
?>

