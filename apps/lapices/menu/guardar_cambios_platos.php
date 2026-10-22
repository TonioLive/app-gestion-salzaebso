<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$pu=$_POST["pu"];
$tipo_b=$_POST["tipo_b"];
$id_ingrediente_cambio=$_POST["id_ingrediente_cambio"];
$id_alimento_nuevo=$_POST["id_alimento_nuevo"];
$id_cantidad_nueva=$_POST["id_cantidad_nueva"];

if ($id_alimento_nuevo!=0) {
        $inserta_plato=db_query("UPDATE menu_ingredientes SET id_alimento=$id_alimento_nuevo WHERE id_ingrediente=$id_ingrediente_cambio");
        } else {
        $inserta_plato=db_query("UPDATE menu_ingredientes SET cantidad_racion=$id_cantidad_nueva WHERE id_ingrediente=$id_ingrediente_cambio");
        }

header("Location:busqueda_ingrediente.php?pu=$pu&tipo_b=$tipo_b");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

