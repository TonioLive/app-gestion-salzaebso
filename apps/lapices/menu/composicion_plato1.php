<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_plato=$_POST["id_plato"];
$id_alimento=$_POST["id_alimento"];
$preparar_separado=$_POST["preparar_separado"];
$cantidad_racion=$_POST["cantidad_racion"];

$inserta_plato=db_query("INSERT INTO menu_ingredientes (id_plato,id_alimento,cantidad_racion,preparar_separado) VALUES ('$id_plato','$id_alimento','$cantidad_racion','$preparar_separado')");

header("Location:composicion_plato.php?id_plato=$id_plato");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

