<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_centro_caja=$_POST["id_centro_caja"];
$id_cierre_caja=$_POST["id_cierre_caja"];

$fecha_pago=date("Y-m-d");
$importe_final=$_POST["importe_final"];
$importe_contado=$_POST["importe_contado"];
$importe_retirado=$_POST["importe_retirado"];
$comentario=$_POST["comentario"];
$ultimo_ticket_cierre=$_POST["ultimo_ticket_cierre"];

$importe_restante=$importe_final-$importe_retirado;

$nuevo_pago = db_query("INSERT INTO cierres_caja (id_centro, fecha, comentario, hora, cerrado_por, importe_al_cierre_real, ultimo_movimiento) VALUES ('$id_centro_caja', '$fecha_pago', '$comentario', '".date("H:i:s")."', $idUsuario, '$importe_contado', '$ultimo_ticket_cierre')") or die (db_error());

$id_nuevo_cierre=db_insert_id();
// Anota la retirada de fondos
$nuevo_pago = db_query("INSERT INTO movimientos_caja (id_centro, fecha, importe, comentario, hora, signo, anotado_por, id_cierre_anterior) VALUES ('$id_centro_caja', '$fecha_pago', '$importe_retirado', 'Retirada de efectivo al cierre de caja', '".date("H:i:s")."', '-1', $idUsuario, '$id_cierre_caja')") or die (db_error());

// Anota el importe inicial
$nuevo_pago = db_query("INSERT INTO movimientos_caja (id_centro, fecha, importe, comentario, hora, signo, anotado_por, id_cierre_anterior) VALUES ('$id_centro_caja', '$fecha_pago', '$importe_restante', 'Importe inical de la caja', '".date("H:i:s")."', '1', $idUsuario, '$id_nuevo_cierre')") or die (db_error());
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

