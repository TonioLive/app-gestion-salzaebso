<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_pago=$_GET["id_pago"];
$id_factura=$_GET["id_factura"];

db_query("DELETE FROM pagos_a_cuenta WHERE id_pago=$id_pago") or die (db_error());

        
header("Location:pagos_a_cuenta.php?id_factura=".$id_factura);

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

