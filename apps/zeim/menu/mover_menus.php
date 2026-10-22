<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$grupo=$_POST["grupo"];
$nuevogrupo=$_POST["nuevogrupo"];
$menus=$_POST["menus"];

foreach ($menus as $menu) {

    // echo("UPDATE menu_menus SET grupomenus='$nuevogrupo' WHERE id_menu=$menu;<br>");
    $actualiza=db_query("UPDATE menu_menus SET grupomenus='$nuevogrupo' WHERE id_menu=$menu;");

}



header("Location:gestion_menu.php?grupomenus=$nuevogrupo");


?>