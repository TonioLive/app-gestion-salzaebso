<?php 
require("../configuracion/config.php");

$id_pedido=$_POST["id_pedido"];
$contador_filas=$_POST["contador_filas"];
$observaciones=$_POST["observaciones"];

// Guarda cambios
db_query_params('UPDATE pedidos_material SET observaciones=? WHERE id_pedido=?', 'ss', array((string)($observaciones), $id_pedido)) or die(db_error()." -1");

// Busca datos pedido
$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_material WHERE id_pedido=?', 's', array($id_pedido)) or die(db_error()." -2");

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
        $busca_cantidad=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido=? AND generico_solicitado=?', 'ss', array($id_pedido, (string)($nombre_generico))) or die(db_error()." -3");
        if (db_num_rows($busca_cantidad)==0) {
                        // Inserta la fila nueva con cantidad solicitada
                        if (floatval($cantidad)!=0) {
                                db_query_params('INSERT INTO detalles_pedido_material (id_pedido, generico_solicitado, cantidad, cantidad_original) VALUES (?, ?, ?, ?)', 'ssss', array((string)($id_pedido), (string)($nombre_generico), (string)($cantidad), (string)($cantidad))) or die(db_error()." -4");
                                }
                } else {
                        // Actualiza las cantidades
                        if (floatval($cantidad)!=0) {
                                $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                                $id_fila_detalles=$datos_cantidad_articulo["id_detalle"];
                                db_query_params('UPDATE detalles_pedido_material SET cantidad=?,cantidad_original=? WHERE id_detalle=?;', 'sss', array((string)($cantidad), (string)($cantidad), $id_fila_detalles))or die(db_error()." -5");
                                } else {
                                db_query_params('DELETE FROM detalles_pedido_material WHERE id_detalle=?;', 's', array($id_fila_detalles))or die(db_error()." -5");
                                }
                }
        // Actualiza el stock del almacén
        $busca_existencias_articulo=db_query_params('SELECT * FROM existencias_aulas WHERE id_aula=? AND nombre_generico=?', 'ss', array($id_aula, (string)($nombre_generico))) or die(db_error()." -6");
        if (db_num_rows($busca_existencias_articulo)==0) {
                        // Introduce una nueva fila con la catidad
                        db_query_params('INSERT INTO existencias_aulas (id_aula,nombre_generico,stock_actual,fecha_ultimo_cambio) VALUES (?,?,?,?)', 'ssss', array((string)($id_aula), (string)($nombre_generico), (string)($existencias), (string)(date("Y-m-d")))) or die(db_error()." -7");
                } else {
                       $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                        $id_stock=$datos_existencias_articulo["id_stock"];
                        // Actualiza la fila
                        db_query_params('UPDATE existencias_aulas SET stock_actual=?, fecha_ultimo_cambio=? WHERE id_stock=?;', 'sss', array((string)($existencias), (string)(date("Y-m-d")), $id_stock)) or die(db_error()." -8");
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

