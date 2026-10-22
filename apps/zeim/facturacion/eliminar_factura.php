<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");

if ($nivel_acceso==4) {

$id_factura=$_GET["id_factura"];
$id_cliente=$_GET["id_cliente"];
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];

db_query_params('DELETE FROM facturas_emitidas WHERE id_factura=?', 's', array($id_factura));
db_query_params('DELETE FROM detalles_factura WHERE id_factura=?', 's', array($id_factura));
db_query_params('DELETE FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));

header("Location: listado_facturas.php?mes=$mes&centro_factura=$centro_factura&id_cliente=$id_cliente&ejercicio=$ejercicio");
} else {
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<p align="center"><br><br>
<font size=3 face="<?=$fuente_general?>"><b>No tienes permiso para eliminar facturas.</b><br><br>
<input type="button" value="Volver" onclick="window.location.href='<?php  echo ("listado_facturas.php?mes=$mes&centro_factura=$centro_factura&id_cliente=$id_cliente&ejercicio=$ejercicio");?>';">
</p>
</body>
</html>
<?php 
}
?>

