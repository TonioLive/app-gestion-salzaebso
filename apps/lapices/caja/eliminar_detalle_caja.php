<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_pago=$_GET["id_pago"];
$id_centro_caja=$_GET["id_centro_caja"];
$id_cierre_caja=$_GET["id_cierre_caja"];

db_query("DELETE FROM pagos_a_cuenta WHERE id_pago=$id_pago") or die (db_error());

        
header("Location:listado_caja.php?id_cierre_caja=".$id_cierre_caja."&id_centro_caja=".$id_centro_caja);

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

