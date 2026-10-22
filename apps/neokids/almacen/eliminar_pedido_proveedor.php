<?php 
require("../configuracion/config.php");

$id_pedido=$_GET["id_pedido"];
$fecha=$_GET["fecha"];

if (strlen($fecha)!=0) {
        // Pone a 0 el marcador
        $busca_articulos=db_query_params('SELECT * FROM detalles_pedido_proveedor,tarifa,tipos_iva_servicios WHERE detalles_pedido_proveedor.id_articulo=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND detalles_pedido_proveedor.id_pedido=?', 's', array($id_pedido));
        while($lista_articulos=db_fetch_array($busca_articulos)) {
                if ($lista_articulos["tipo_servicios"]==2) {
                        $actualiza_dato=db_query_params('UPDATE menu_productos_proveedor SET pedido=0 WHERE id_producto_lista=?', 's', array($lista_articulos["id_origen"]));
                        }
                if ($lista_articulos["tipo_servicios"]==1) {
                        $actualiza_dato=db_query_params('UPDATE detalles_pedido_empresa SET pedido=0 WHERE id_articulo=?', 's', array($lista_articulos["id_origen"]));
                        }
                }
        }

// Borra fila
db_query_params('DELETE FROM detalles_pedido_proveedor WHERE id_pedido=?', 's', array($id_pedido));
db_query_params('DELETE FROM pedidos_proveedor WHERE id_pedido=?', 's', array($id_pedido));

// Retorno
if (strlen($fecha)!=0) {
header("Location: ver_pedidos_fecha.php?fecha=$fecha");
} else {
header("Location: listado_proveedores.php");
}
        

?>
<html>
<head>
</head>
<body>
</body>
</html>

