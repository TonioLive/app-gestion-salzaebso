<?php  require("../configuracion/config.php");


$id_pedido=$_POST["id_pedido"];
$id_proveedor=$_POST["id_proveedor"];
$contador_filas=$_POST["contador_filas"];
$observaciones=$_POST["observaciones"];

// Guarda cambios

if (strlen($id_pedido)==0) {
        db_query_params('INSERT INTO pedidos_proveedor (id_proveedor, fecha_pedido) VALUES (?, ?);', 'ss', array((string)($id_proveedor), (string)(date("Y-m-d"))));
        $id_pedido=db_insert_id();
        }

db_query_params('UPDATE pedidos_proveedor SET observaciones=? WHERE id_pedido=?', 'ss', array((string)($observaciones), $id_pedido)) or die(db_error()." -1");

// Busca datos pedido
$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_proveedor WHERE id_pedido=?', 's', array($id_pedido)) or die(db_error()." -2");

$datos_pedido=db_fetch_array($busca_datos_pedido);

// Introduce o actualiza filas de material pedido
for ($fila=0;$fila<=intval($contador_filas-1);$fila++) {

        $id_fila_detalles=$_POST["id_fila_detalles_".$fila];
        $id_articulo_tarifa=$_POST["id_articulo_tarifa_".$fila];
        $id_fila_tipo_iva=$_POST["id_fila_tipo_iva_".$fila];
        $precio_articulo_tarifa=$_POST["precio_articulo_tarifa_".$fila];

        $cantidad=$_POST["cantidad_".$fila];
        if (strlen($cantidad)!=0) {
                        $cantidad=str_replace(",",".",$cantidad);
                        }

        if (strlen($id_fila_detalles)!=0) {
                if ($cantidad!=0) {
                        db_query_params('UPDATE detalles_pedido_proveedor SET cantidad=? WHERE id_detalle=?;', 'ss', array((string)($cantidad), $id_fila_detalles))or die(db_error()." -5");
                        } else {
                        db_query_params('DELETE FROM detalles_pedido_proveedor WHERE id_detalle=?;', 's', array($id_fila_detalles));
                        }
                } else {
                if ($cantidad!=0) {
                        db_query_params('INSERT INTO detalles_pedido_proveedor (id_pedido, id_articulo, cantidad, precio, tipo_iva) VALUES (?, ?, ?, ?, ?)', 'sssss', array((string)($id_pedido), (string)($id_articulo_tarifa), (string)($cantidad), (string)($precio_articulo_tarifa), (string)($id_fila_tipo_iva))) or die(db_error()." -4");
                        }
                }
        }
header("Location: ver_pedido_proveedor.php?id_pedido=$id_pedido");
?>


