<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$categoria=$_POST["categoria"];
$id_categoria=$_POST["id_categoria"];
$porcentaje_iva=$_POST["porcentaje_iva"];
$tipo_servicios=$_POST["tipo_servicios"];

        // Introduce la nueva familia
        
        $nuevo_articulo = db_query_params('UPDATE tipos_iva_servicios SET tipo_servicios=?, categoria=?, porcentaje_iva=? WHERE id_tipo_iva=?', 'ssss', array((string)($tipo_servicios), (string)($categoria), (string)($porcentaje_iva), $id_categoria)) or die (db_error());

        
        header("Location:gestion_familias.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

