<?php 
require("../configuracion/config.php");

$id_pedido=$_GET["id_pedido"];

// Borra fila
db_query_params('DELETE FROM detalles_pedido_extendido WHERE id_pedido=?', 's', array($id_pedido));
db_query_params('DELETE FROM detalles_pedido_material WHERE id_pedido=?', 's', array($id_pedido));
db_query_params('DELETE FROM pedidos_material WHERE id_pedido=?', 's', array($id_pedido));

// Retorno
header("Location: pedidos_pendientes.php");
        

?>
<html>
<head>
</head>
<body>
</body>
</html>

