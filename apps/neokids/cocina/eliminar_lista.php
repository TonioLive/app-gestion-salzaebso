<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");


$id_lista=$_GET["id_lista"];

$borra1=db_query_params('DELETE FROM menu_lista_compra WHERE id_lista=?', 's', array($id_lista));
$borra2=db_query_params('DELETE FROM id_lista WHERE id_lista=?', 's', array($id_lista));
$borra3=db_query_params('DELETE FROM menu_productos_proveedor WHERE id_lista=?', 's', array($id_lista));

header("Location: gestion_cocina.php");

?>
