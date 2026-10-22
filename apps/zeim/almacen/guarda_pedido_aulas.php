<?php 
require("../configuracion/config.php");

$id_pedido=$_POST["id_pedido"];
$contador_filas=$_POST["contador_filas"];
$contador_total==$_POST["contador_total"];

// Busca datos pedido
$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_material WHERE id_pedido=?', 's', array($id_pedido)) or die(db_error()." -2");

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];

// Introduce o actualiza filas de material pedido
for ($fila=1;$fila<=intval($contador_filas);$fila++) {

        $id_fila_detalles=$_POST["id_fila_detalles_".$fila];

        $cantidad=$_POST["cantidad_".$fila];
        if (strlen($cantidad)!=0) {
                        $cantidad=str_replace(",",".",$cantidad);
                        }


        db_query_params('UPDATE detalles_pedido_material SET cantidad=? WHERE id_detalle=?;', 'ss', array((string)($cantidad), $id_fila_detalles))or die(db_error()." -5");
        }
?>
<html>
<head>
<script language="Javascript">
self.opener.history.go(0);
self.close();
</script>
</head>
<body>
</body>
</html>

