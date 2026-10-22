<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$categoria=$_POST["categoria"];
$id_categoria=$_POST["id_categoria"];
$porcentaje_iva=$_POST["porcentaje_iva"];
$tipo_servicios=$_POST["tipo_servicios"];

        // Introduce la nueva familia
        
        $nuevo_articulo = db_query("UPDATE tipos_iva_servicios SET tipo_servicios='$tipo_servicios', categoria='$categoria', porcentaje_iva='$porcentaje_iva' WHERE id_tipo_iva=$id_categoria") or die (db_error());

        
        header("Location:gestion_familias.php");


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

