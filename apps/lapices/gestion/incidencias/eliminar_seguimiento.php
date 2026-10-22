<?php
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_incidencia=$_POST["id_incidencia"];
$id_seguimiento=$_POST["id_seguimiento"];

$elimina_seguimiento=db_query("DELETE FROM incidencias_seguimiento WHERE id_seguimiento=$id_seguimiento");
	
?>