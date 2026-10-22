<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_almacen.php");

if (app_request_value($_SERVER, "REQUEST_METHOD", "") !== "POST") {
        http_response_code(405);
        exit("Metodo no permitido.");
}
if (!app_verify_csrf((string) app_request_value($_POST, "_csrf", ""))) {
        http_response_code(403);
        exit("Solicitud no valida.");
}

$id_pedido=app_request_int($_POST, "id_pedido", 0);
$contador_filas=max(0, min(500, app_request_int($_POST, "contador_filas", 0)));
$contador_total=max(0, min(500, app_request_int($_POST, "contador_total", 0)));
$observaciones=trim((string) app_request_value($_POST, "observaciones", ""));
$observaciones=substr($observaciones, 0, 2000);

// Guarda cambios
if (strlen($observaciones)!=0) {
        if ($observaciones=="borrar") {
                $observaciones="";
                }
        db_query_params("UPDATE pedidos_material SET observaciones=? WHERE id_pedido=?", "si", array($observaciones, $id_pedido)) or die(db_error()." -1");
        }

// Busca datos pedido
$busca_datos_pedido=db_query("SELECT * FROM pedidos_material WHERE id_pedido=$id_pedido") or die(db_error()." -2");

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];

// Introduce o actualiza filas de material pedido
for ($fila=1;$fila<=intval($contador_filas);$fila++) {

        $id_fila_detalles=app_request_int($_POST, "id_fila_detalles_".$fila, 0);

        $cantidad=str_replace(",", ".", (string) app_request_value($_POST, "cantidad_".$fila, "0"));
        $cantidad=is_numeric($cantidad) ? (float) $cantidad : 0;


        db_query("UPDATE detalles_pedido_material SET cantidad='$cantidad' WHERE id_detalle=$id_fila_detalles;")or die(db_error()." -5");
        }

// Comprueba si hay que almacenar el pedido para luego
$se_puede=false;
for ($fila=0;$fila<=intval($contador_total)-1;$fila++) {

        $id_articulo_tarifa=app_request_int($_POST, "id_articulo_tarifa_".$fila, 0);
        $cantidad_servida=str_replace(",", ".", (string) app_request_value($_POST, "cantidad_servida_".$fila, "0"));
        $cantidad_servida=is_numeric($cantidad_servida) ? (float) $cantidad_servida : 0;
        $id_detalle_pedido=app_request_int($_POST, "id_detalle_pedido_".$fila, 0);

        if ($cantidad_servida!=0) {
                $se_puede=true;
        }

        // Comprueba si ya existe pedido de este material
        $busca_cantidad=db_query("SELECT * FROM detalles_pedido_extendido WHERE id_detalle_pedido=$id_detalle_pedido AND id_tarifa=$id_articulo_tarifa") or die(db_error()." -3");
        if (db_num_rows($busca_cantidad)==0) {
                        // Inserta la fila nueva con cantidad solicitada
                        db_query("INSERT INTO detalles_pedido_extendido (id_detalle_pedido, id_tarifa, cantidad) VALUES ('$id_detalle_pedido', '$id_articulo_tarifa', '$cantidad_servida')") or die(db_error()." -4");
                } else {
                        // Actualiza las cantidades
                        $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                        $id_fila_detalles=$datos_cantidad_articulo["id_articulo"];
                        db_query("UPDATE detalles_pedido_extendido SET cantidad='$cantidad_servida' WHERE id_articulo=$id_fila_detalles;")or die(db_error()." -5");
                }
        }

// Comprueba si hay que emitir un albarán
$generar_albaran=app_request_int($_GET, "generar_albaran", 0);
if (($generar_albaran==1) and ($se_puede==true)) {
        db_query("INSERT INTO albaranes_entrega (id_pedido,fecha,id_aula,fecha_entrega) VALUES ('$id_pedido','".date("Y-m-d")."','$id_aula','".date("Y-m-d")."');");
        $id_nuevo_albaran=db_insert_id();
        
        $busca_filas=db_query("SELECT detalles_pedido_extendido.*,detalles_pedido_material.*,detalles_pedido_extendido.cantidad AS cantidad_albaran,detalles_pedido_material.cantidad AS cantidad_inicial FROM detalles_pedido_extendido,detalles_pedido_material WHERE detalles_pedido_extendido.id_detalle_pedido=detalles_pedido_material.id_detalle AND detalles_pedido_material.id_pedido=$id_pedido") or die(db_error());
        while($filas_albaran=db_fetch_array($busca_filas)) {
                if ($filas_albaran["cantidad_albaran"]!=0) {
                        db_query("INSERT INTO detalles_albaran_entrega (id_albaran,id_detalle_pedido,generico_solicitado,id_material,cantidad) VALUES ('$id_nuevo_albaran','".$filas_albaran["id_detalle_pedido"]."','".$filas_albaran["generico_solicitado"]."','".$filas_albaran["id_tarifa"]."','".$filas_albaran["cantidad_albaran"]."');") or die(db_error());
                        }
                // Borra la tabla temporal
                db_query("DELETE FROM detalles_pedido_extendido WHERE id_detalle_pedido=".$filas_albaran["id_detalle_pedido"]);
                
                // Comprueba si ya he servido todo lo que necesitaba
                
                $busca_totales=db_query("SELECT id_detalle_pedido, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_entrega WHERE id_detalle_pedido=".$filas_albaran["id_detalle_pedido"]." GROUP BY id_detalle_pedido;");
                $totales=db_fetch_array($busca_totales);
                $total_servido=$totales["SumaDeCANTIDAD"];
                
                if ($filas_albaran["cantidad_inicial"]<=$total_servido) {
                        db_query("UPDATE detalles_pedido_material SET id_albaran=1 WHERE id_pedido=".$filas_albaran["id_detalle_pedido"]);
                        }
                
                
                }

        header("Location: ver_albaran.php?id_albaran=$id_nuevo_albaran");
        }

if ($se_puede==false) {
        // Retorno

        if (app_request_int($_GET, "r", 0)==1) {
                header("Location: pedidos_pendientes.php");
                }
        if (app_request_int($_GET, "r", 0)==2) {
                        header("Location: listado_albaranes.php?id_pedido=".$id_pedido);
                        }
        if (app_request_int($_GET, "r", 0)==3) {
?>
<html>
<head>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>
<?php 
                        } else {
                        header("Location: editar_pedido.php?id_pedido=".$id_pedido);
                        }
        }
        

?>
