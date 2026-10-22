<?php 
require("../configuracion/config.php");

// Busca pedidos penientes de servir

$busca_pedidos_pendientes=db_query("SELECT detalles_pedido_material.generico_solicitado, sum(detalles_pedido_material.cantidad) AS suma_total FROM detalles_pedido_material,pedidos_material,centro_aulas WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_pedido_empresa=0 AND detalles_pedido_material.confirmado=1 GROUP BY detalles_pedido_material.generico_solicitado ORDER BY detalles_pedido_material.generico_solicitado") or die(db_error()." -1");
                                
$contador_filas=0;
$nombre_articulo="";

$id_nuevo_pedido=0;

while($datos_detalle=db_fetch_array($busca_pedidos_pendientes)) {

        $contador_filas++;

        // Detalle aulas
        $busca_material_aulas=db_query_params('SELECT * FROM detalles_pedido_material,pedidos_material,centro_aulas WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_albaran=0 AND detalles_pedido_material.generico_solicitado=? AND detalles_pedido_material.id_pedido_empresa=0 AND detalles_pedido_material.confirmado=1 ORDER BY centro_aulas.id_centro, centro_aulas.nombre', 's', array((string)($datos_detalle["generico_solicitado"]))) or die(db_error());
                                
        while($material_aulas=db_fetch_array($busca_material_aulas)) {
                                
                                        $cantidad_pedido=$material_aulas["cantidad"];
                                        
                                        // Introduce si fuera necesario
                                        if ($_POST["lista_pedidos_validar_".$contador_filas]==1) {
                                                if ($id_nuevo_pedido==0) {
                                                        // Inserta nuevo pedido centro
                                                        $nuevo_pedido_centro=db_query_params('INSERT INTO pedidos_material_empresa (fecha_pedido) values (?)', 's', array((string)(date("Y-m-d"))));
                                                        $id_nuevo_pedido=db_insert_id();
                                                        }
                                                // Intro fila
                                                db_query_params('UPDATE detalles_pedido_material SET id_pedido_empresa=? WHERE id_detalle=?', 'ss', array($id_nuevo_pedido, $material_aulas["id_detalle"]));
                                                }
                                        }
                                }

// Observaciones
$observaciones=$_POST["observaciones"];
if (strlen($observaciones)!=0) {

$observaciones=db_escape_string($observaciones);

if ($id_nuevo_pedido==0) {
                        // Inserta nuevo pedido centro
                        $nuevo_pedido_centro=db_query("INSERT INTO pedidos_material_empresa (fecha_pedido,observaciones) values ('".date("Y-m-d")."','$observaciones')");
                        $id_nuevo_pedido=db_insert_id();
                        } else {
                        db_query("UPDATE pedidos_material_empresa SET observaciones='$observaciones' WHERE id_pedido_empresa=$id_nuevo_pedido");
                        }
                        
}


if ($id_nuevo_pedido!=0) {
        header("Location: ver_pedido_empresa.php?id_pedido=$id_nuevo_pedido");
        } else {
        header("Location: calendario_pedidos_empresa.php");
        }

?>
