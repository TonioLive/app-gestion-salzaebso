<?php 
require("../configuracion/config.php");

$id_pedido=$_POST["id_pedido"];
$contador_filas=$_POST["contador_filas"];

// Busca datos pedido
$busca_datos_pedido=db_query("SELECT * FROM pedidos_material_empresa WHERE id_pedido_empresa=$id_pedido") or die(db_error()." -2");

$datos_pedido=db_fetch_array($busca_datos_pedido);

// Introduce o actualiza filas de material pedido
for ($fila=1;$fila<=intval($contador_filas);$fila++) {

        $nombre_generico=$_POST["nombre_generico_".$fila];

        $cantidad=$_POST["cantidad_".$fila];
        if (strlen($cantidad)!=0) {
                        $cantidad=str_replace(",",".",$cantidad);
                        }
                        
        // Comprueba si ya existe pedido de este material
        $busca_cantidad=db_query("SELECT * FROM detalles_pedido_empresa WHERE id_pedido_empresa=$id_pedido AND id_tarifa=0 AND generico='".$nombre_generico."'") or die(db_error()." -3");
        if (db_num_rows($busca_cantidad)==0) {
                        // Inserta la fila nueva con cantidad solicitada
                        if (floatval($cantidad)!=0) {
                                db_query("INSERT INTO detalles_pedido_empresa (id_pedido_empresa, generico, cantidad) VALUES ('$id_pedido', '$nombre_generico', '$cantidad')") or die(db_error()." -4");
                                }
                } else {
                        // Actualiza las cantidades
                        if (floatval($cantidad)!=0) {
                                $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                $id_fila_detalles=$datos_cantidad_articulo["id_articulo"];
                                db_query("UPDATE detalles_pedido_empresa SET cantidad='$cantidad' WHERE id_articulo=$id_fila_detalles;")or die(db_error()." -5");
                                }
                }

        }
?>
<html>
<head>
<script language="Javascript">
self.opener.location.href='ver_pedido_empresa.php?id_pedido=<?=$id_pedido?>';
self.close();
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

