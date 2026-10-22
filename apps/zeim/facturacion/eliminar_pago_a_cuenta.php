<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_pago=$_GET["id_pago"];
$id_factura=$_GET["id_factura"];

db_query_params('DELETE FROM pagos_a_cuenta WHERE id_pago=?', 's', array($id_pago)) or die (db_error());

        
header("Location:pagos_a_cuenta.php?id_factura=".$id_factura);

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

