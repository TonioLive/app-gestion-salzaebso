<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_almacen.php");

if (!app_verify_csrf((string) app_request_value($_GET, "_csrf", ""))) {
        http_response_code(403);
        exit("Solicitud no valida.");
}

$e=app_request_int($_GET, "e", 0);
$u=app_request_int($_GET, "u", 0);

// Borra fila
db_query("DELETE FROM proveedores_evaluaciones WHERE id_proveedor=$u AND id_evaluacion=$e");

// Retorno
header("Location: nuevo_proveedor_b.php?u=$u");
        

?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

