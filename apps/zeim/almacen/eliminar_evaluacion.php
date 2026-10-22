<?php 
require("../configuracion/config.php");

$e=$_GET["e"];
$u=$_GET["u"];

// Borra fila
db_query("DELETE FROM proveedores_evaluaciones WHERE id_proveedor=$u AND id_evaluacion=$e");

// Retorno
header("Location: nuevo_proveedor_b.php?u=$u");
        

?>
<html>
<head>
</head>
<body>
</body>
</html>

