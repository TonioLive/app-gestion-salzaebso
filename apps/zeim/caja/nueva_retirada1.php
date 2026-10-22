<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_centro_caja=$_POST["id_centro_caja"];
$id_cierre_caja=$_POST["id_cierre_caja"];

$fecha_pago=date("Y-m-d");
$importe_pago=$_POST["importe_pago"];
$comentario_pago=$_POST["comentario"];

$nuevo_pago = db_query("INSERT INTO movimientos_caja (id_centro, fecha, importe, comentario, hora, signo, anotado_por, id_cierre_anterior) VALUES ('$id_centro_caja', '$fecha_pago', '$importe_pago', '$comentario_pago', '".date("H:i:s")."', '-1', $idUsuario, '$id_cierre_caja')") or die (db_error());
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

