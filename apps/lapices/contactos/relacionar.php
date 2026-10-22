<?php  require("../configuracion/config.php"); ?>
<?php 
$id_contacto=$_GET["id_contacto"];
$id_usuario=$_GET["id_alumno"];

$fecha_c=date("Y-m-d");
db_query("UPDATE contactos SET id_usuario='$id_usuario', fecha_c='$fecha_c', proceso_decision='c' WHERE id_contacto=$id_contacto;") or die(db_error());

$url_retorno="nuevo_contacto.php?id_contacto=$id_contacto";

header("Location: ".$url_retorno);
?>
