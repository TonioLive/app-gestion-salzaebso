<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$grupo=$_POST["grupo"];
$nuevogrupo=$_POST["nuevogrupo"];
$platos=$_POST["platos"];

foreach ($platos as $plato) {

    $actualiza=db_query("UPDATE menu_platos SET grupo='$nuevogrupo' WHERE id_plato=$plato");

}



header("Location:gestion_platos.php?grupo=$nuevogrupo");


?>