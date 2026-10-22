<?php 
require("../configuracion/config.php");

$id_pedido=$_POST["id_pedido"];
$contador_filas=$_POST["contador_filas"];
$observaciones=$_POST["observaciones"];

$busca_detalle=db_query("SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.ARTICULO_GENERICO<>'' AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=1 GROUP BY tarifa.ARTICULO_GENERICO ORDER BY tarifa.CATEGORIA, tarifa.ARTICULO_GENERICO");

if (strlen($contador_filas)==0) {
        $contador_filas=db_num_rows($busca_detalle);
        }

// Guarda cambios
db_query("UPDATE pedidos_material SET observaciones='$observaciones' WHERE id_pedido=$id_pedido") or die(db_error()." -1");

// Busca datos pedido
$busca_datos_pedido=db_query("SELECT * FROM pedidos_material WHERE id_pedido=$id_pedido") or die(db_error()." -2");

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];

// Introduce o actualiza filas de material pedido
for ($fila=1;$fila<=intval($contador_filas);$fila++) {

        $nombre_generico=$_POST["nombre_generico_".$fila];
        $existencias=$_POST["existencias_".$fila];

        if (strlen($existencias)!=0) {
                        $existencias=str_replace(",",".",$existencias);
                        }
        $cantidad=$_POST["cantidad_".$fila];
        if (strlen($cantidad)!=0) {
                        $cantidad=str_replace(",",".",$cantidad);
                        }

        // Comprueba si ya existe pedido de este material
        $busca_cantidad=db_query("SELECT * FROM detalles_pedido_material WHERE id_pedido=$id_pedido AND generico_solicitado='".$nombre_generico."'") or die(db_error()." -3");
        if (db_num_rows($busca_cantidad)==0) {
                        // Inserta la fila nueva con cantidad solicitada
                        if (floatval($cantidad)!=0) {
                                db_query("INSERT INTO detalles_pedido_material (id_pedido, generico_solicitado, cantidad, cantidad_original) VALUES ('$id_pedido', '$nombre_generico', '$cantidad', '$cantidad')") or die(db_error()." -4");
                                }
                } else {
                        // Actualiza las cantidades
                        if (floatval($cantidad)!=0) {
                                $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                $id_fila_detalles=$datos_cantidad_articulo["id_detalle"];
                                db_query("UPDATE detalles_pedido_material SET cantidad='$cantidad',cantidad_original=$cantidad WHERE id_detalle=$id_fila_detalles;")or die(db_error()." -5 UPDATE detalles_pedido_material SET cantidad='$cantidad',cantidad_original=$cantidad WHERE id_detalle=$id_fila_detalles;");
                                } else {
                                $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                $id_fila_detalles=$datos_cantidad_articulo["id_detalle"];
                                db_query("DELETE FROM detalles_pedido_material WHERE id_detalle=$id_fila_detalles;")or die(db_error()." -5 DELETE FROM detalles_pedido_material WHERE id_detalle=$id_fila_detalles;");
                                }
                }
        // Actualiza el stock del almacén
        $busca_existencias_articulo=db_query("SELECT * FROM existencias_aulas WHERE id_aula=".$id_aula." AND nombre_generico='".$nombre_generico."'") or die(db_error()." -6");
        if (db_num_rows($busca_existencias_articulo)==0) {
                        // Introduce una nueva fila con la catidad
                        db_query("INSERT INTO existencias_aulas (id_aula,nombre_generico,stock_actual,fecha_ultimo_cambio) VALUES ('$id_aula','$nombre_generico','$existencias','".date("Y-m-d")."')") or die(db_error()." -7");
                } else {
                       $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                        $id_stock=$datos_existencias_articulo["id_stock"];
                        // Actualiza la fila
                        db_query("UPDATE existencias_aulas SET stock_actual='$existencias', fecha_ultimo_cambio='".date("Y-m-d")."' WHERE id_stock=$id_stock;") or die(db_error()." -8");
                }

        }

// Retorno

if ($_GET["r"]==1) {

        if ($nivel_acceso==2) {
                header("Location: /aulas_docentes/listado_aulas.php");
                } else {
                header("Location: /gestion/centros/gestion_aulas.php");
                }


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

