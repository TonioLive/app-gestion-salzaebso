<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_almacen.php");

if (!app_verify_csrf((string) app_request_value($_GET, "_csrf", ""))) {
        http_response_code(403);
        exit("Solicitud no valida.");
}

$id_albaran=app_request_int($_GET, "id_albaran", 0);
$id_pedido=app_request_int($_GET, "id_pedido", 0);

// Borra fila
db_query("DELETE FROM detalles_albaran_entrega WHERE id_albaran=$id_albaran");
db_query("DELETE FROM albaranes_entrega WHERE id_albaran=$id_albaran");

// Retorno
header("Location: listado_albaranes.php?id_pedido=".$id_pedido);
        

?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

