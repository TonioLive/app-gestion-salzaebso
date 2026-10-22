<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$categoria=$_POST["categoria"];
$porcentaje_iva=$_POST["porcentaje_iva"];
$tipo_servicios=$_POST["tipo_servicios"];

$consulta2="SELECT * FROM tipos_iva_servicios WHERE categoria='".$categoria."'";
$count = db_query ($consulta2);
$numRecords = db_num_rows($count);

if ($numRecords!=0) {
        header("Location:nueva_familia.php?categoria=".urlencode($categoria)."&duplicado=1&porcentaje_iva=$porcentaje_iva");
        } else {
        // Introduce la nueva familia
        
        $nuevo_articulo = db_query("INSERT INTO tipos_iva_servicios (categoria, porcentaje_iva, tipo_servicios) VALUES ('$categoria','$porcentaje_iva','$tipo_servicios')") or die (db_error());

        
        header("Location:gestion_familias.php");
        }

 db_close();

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

