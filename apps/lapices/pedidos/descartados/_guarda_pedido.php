<?php 
require("../configuracion/config.php");

$id_pedido=$_POST["id_pedido"];

$id_quitar_fila=$_POST["id_quitar_fila"];

$observaciones=$_POST["observaciones"];

// Guarda cambios
db_query("UPDATE pedidos_material SET observaciones='$observaciones' WHERE id_pedido=$id_pedido");

// Borra fila
if (strlen($id_quitar_fila)!=0) {
                        db_query("DELETE FROM detalles_pedido_material WHERE id_detalle=$id_quitar_fila");
                        }

// Retorno

if ($_GET["r"]==1) {
        header("Location: /gestion/centros/gestion_aulas.php");
        } else {
        header("Location: introducir_nuevo_pedido.php?id_pedido=".$id_pedido);
        }
        

?>
<html>
<head>
</head>
<body>
</body>
</html>

