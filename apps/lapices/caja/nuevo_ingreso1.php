<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_centro_caja=$_POST["id_centro_caja"];
$id_cierre_caja=$_POST["id_cierre_caja"];

$fecha_pago=date("Y-m-d");
$importe_pago=$_POST["importe_pago"];
$forma_pago=$_POST["forma_pago"];
$comentario_pago=$_POST["comentario"];
$observaciones_pago="Pago introducido desde control de caja.";

$nuevo_pago = db_query("INSERT INTO pagos_a_cuenta (id_factura, id_centro, id_centro_cobro, fecha_pago, importe_pago, forma_pago, observaciones_pago, comentario, hora_pago, signo, anotado_por) VALUES ('0', '$id_centro_caja', '$centro_actual', '$fecha_pago', '$importe_pago', '$forma_pago', '$observaciones_pago', '$comentario_pago', '".date("H:i:s")."', 1, $idUsuario)") or die (db_error());
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
window.close();
self.opener.history.go(0);
//-->
</SCRIPT>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

