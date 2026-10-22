<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_almacen.php");

if (!app_verify_csrf((string) app_request_value($_GET, "_csrf", ""))) {
        http_response_code(403);
        exit("Solicitud no valida.");
}

$id_pedido=app_request_int($_GET, "id_pedido", 0);
$fecha=(string) app_request_value($_GET, "fecha", "");
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha)) { $fecha=""; }

if (strlen($fecha)!=0) {
        // Pone a 0 el marcador
        $busca_articulos=db_query("SELECT * FROM detalles_pedido_proveedor,tarifa,tipos_iva_servicios WHERE detalles_pedido_proveedor.id_articulo=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND detalles_pedido_proveedor.id_pedido=$id_pedido");
        while($lista_articulos=db_fetch_array($busca_articulos)) {
                if ($lista_articulos["tipo_servicios"]==2) {
                        $actualiza_dato=db_query("UPDATE menu_productos_proveedor SET pedido=0 WHERE id_producto_lista=".$lista_articulos["id_origen"]);
                        }
                if ($lista_articulos["tipo_servicios"]==1) {
                        $actualiza_dato=db_query("UPDATE detalles_pedido_empresa SET pedido=0 WHERE id_articulo=".$lista_articulos["id_origen"]);
                        }
                }
        }

// Borra fila
db_query("DELETE FROM detalles_pedido_proveedor WHERE id_pedido=$id_pedido");
db_query("DELETE FROM pedidos_proveedor WHERE id_pedido=$id_pedido");

// Retorno
if (strlen($fecha)!=0) {
header("Location: ver_pedidos_fecha.php?fecha=$fecha");
} else {
header("Location: listado_proveedores.php");
}
        

?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

