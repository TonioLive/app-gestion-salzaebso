<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_almacen.php");

if (!app_verify_csrf((string) app_request_value($_GET, "_csrf", ""))) {
        http_response_code(403);
        exit("Solicitud no valida.");
}

$id_pedido=app_request_int($_GET, "id_pedido", 0);

// Borra fila
db_query("DELETE FROM detalles_pedido_extendido WHERE id_pedido=$id_pedido");
db_query("DELETE FROM detalles_pedido_material WHERE id_pedido=$id_pedido");
db_query("DELETE FROM pedidos_material WHERE id_pedido=$id_pedido");

// Retorno
header("Location: pedidos_pendientes.php");
        

?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

