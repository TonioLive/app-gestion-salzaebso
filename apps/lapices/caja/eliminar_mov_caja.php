<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_movimiento=$_GET["id_mov"];
$id_centro_caja=$_GET["id_centro_caja"];
$id_cierre_caja=$_GET["id_cierre_caja"];

db_query("DELETE FROM movimientos_caja WHERE id_movimiento=$id_movimiento") or die (db_error());

        
header("Location:listado_caja.php?id_cierre_caja=".$id_cierre_caja."&id_centro_caja=".$id_centro_caja);

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

