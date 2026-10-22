<?php 
require("../configuracion/config.php");

$id_albaran=$_GET["id_albaran"];
$id_pedido=$_GET["id_pedido"];

// Borra fila
db_query_params('DELETE FROM detalles_albaran_entrega WHERE id_albaran=?', 's', array($id_albaran));
db_query_params('DELETE FROM albaranes_entrega WHERE id_albaran=?', 's', array($id_albaran));

// Retorno
header("Location: listado_albaranes.php?id_pedido=".$id_pedido);
        

?>
<html>
<head>
</head>
<body>
</body>
</html>

