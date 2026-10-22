<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_factura=$_POST["id_factura"];
$numero_factura=$_POST["numero_factura"];
$id_centro_factura=$_POST["id_centro_factura"];

$pago_fechains_dia=$_POST["pago_fechains_dia"];
$pago_fechains_mes=$_POST["pago_fechains_mes"];
$pago_fechains_ano=$_POST["pago_fechains_ano"];
$fecha_pago=$pago_fechains_ano."-".$pago_fechains_mes."-".$pago_fechains_dia;
$importe_pago=$_POST["importe_pago"];
$forma_pago=$_POST["forma_pago"];
$observaciones_pago=$_POST["observaciones_pago"];

$nombre_alumno=$_POST["nombre_alumno"];

$nuevo_pago = db_query("INSERT INTO pagos_a_cuenta (id_factura, id_centro, id_centro_cobro, fecha_pago, importe_pago, forma_pago, observaciones_pago, comentario, hora_pago, signo, anotado_por) VALUES ('$id_factura', '$id_centro_factura', '$centro_actual', '$fecha_pago', '$importe_pago', '$forma_pago', '$observaciones_pago', 'Pago a cuenta $nombre_alumno factura nº $numero_factura', '".date("H:i:s")."', 1, $idUsuario)") or die (db_error());

        
header("Location:pagos_a_cuenta.php?id_factura=".$id_factura);

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

