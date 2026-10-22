<?php 
require("../configuracion/config.php");

$id_pedido=$_POST["id_pedido"];
$contador_filas=$_POST["contador_filas"];
$contador_total==$_POST["contador_total"];
$observaciones=$_POST["observaciones"];

// Guarda cambios
if (strlen($observaciones)!=0) {
        if ($observaciones=="borrar") {
                $observaciones="";
                }
        db_query_params('UPDATE pedidos_material SET observaciones=? WHERE id_pedido=?', 'ss', array((string)($observaciones), $id_pedido)) or die(db_error()." -1");
        }

// Busca datos pedido
$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_material WHERE id_pedido=?', 's', array($id_pedido)) or die(db_error()." -2");

$datos_pedido=db_fetch_array($busca_datos_pedido);
$id_aula=$datos_pedido["id_aula"];

// Introduce o actualiza filas de material pedido
for ($fila=1;$fila<=intval($contador_filas);$fila++) {

        $id_fila_detalles=$_POST["id_fila_detalles_".$fila];

        $cantidad=$_POST["cantidad_".$fila];
        if (strlen($cantidad)!=0) {
                        $cantidad=str_replace(",",".",$cantidad);
                        }


        db_query_params('UPDATE detalles_pedido_material SET cantidad=? WHERE id_detalle=?;', 'ss', array((string)($cantidad), $id_fila_detalles))or die(db_error()." -5");
        }

// Comprueba si hay que almacenar el pedido para luego
$se_puede=false;
for ($fila=0;$fila<=intval($contador_total)-1;$fila++) {

        $id_articulo_tarifa=$_POST["id_articulo_tarifa_".$fila];
        $cantidad_servida=$_POST["cantidad_servida_".$fila];
        $id_detalle_pedido=$_POST["id_detalle_pedido_".$fila];

        if (strlen($cantidad_servida)!=0) {
                        $cantidad_servida=str_replace(",",".",$cantidad_servida);
                        if ($cantidad_servida!=0) {
                                $se_puede=true;
                                }
                        }

        // Comprueba si ya existe pedido de este material
        $busca_cantidad=db_query_params('SELECT * FROM detalles_pedido_extendido WHERE id_detalle_pedido=? AND id_tarifa=?', 'ss', array($id_detalle_pedido, $id_articulo_tarifa)) or die(db_error()." -3");
        if (db_num_rows($busca_cantidad)==0) {
                        // Inserta la fila nueva con cantidad solicitada
                        db_query_params('INSERT INTO detalles_pedido_extendido (id_detalle_pedido, id_tarifa, cantidad) VALUES (?, ?, ?)', 'sss', array((string)($id_detalle_pedido), (string)($id_articulo_tarifa), (string)($cantidad_servida))) or die(db_error()." -4");
                } else {
                        // Actualiza las cantidades
                        $datos_cantidad_articulo=db_fetch_array($busca_cantidad);
                        $id_fila_detalles=$datos_cantidad_articulo["id_articulo"];
                        db_query_params('UPDATE detalles_pedido_extendido SET cantidad=? WHERE id_articulo=?;', 'ss', array((string)($cantidad_servida), $id_fila_detalles))or die(db_error()." -5");
                }
        }

// Comprueba si hay que emitir un albarán
$generar_albaran=$_GET["generar_albaran"];
if (($generar_albaran==1) and ($se_puede==true)) {
        db_query_params('INSERT INTO albaranes_entrega (id_pedido,fecha,id_aula,fecha_entrega) VALUES (?,?,?,?);', 'ssss', array((string)($id_pedido), (string)(date("Y-m-d")), (string)($id_aula), (string)(date("Y-m-d"))));
        $id_nuevo_albaran=db_insert_id();
        
        $busca_filas=db_query_params('SELECT detalles_pedido_extendido.*,detalles_pedido_material.*,detalles_pedido_extendido.cantidad AS cantidad_albaran,detalles_pedido_material.cantidad AS cantidad_inicial FROM detalles_pedido_extendido,detalles_pedido_material WHERE detalles_pedido_extendido.id_detalle_pedido=detalles_pedido_material.id_detalle AND detalles_pedido_material.id_pedido=?', 's', array($id_pedido)) or die(db_error());
        while($filas_albaran=db_fetch_array($busca_filas)) {
                if ($filas_albaran["cantidad_albaran"]!=0) {
                        db_query_params('INSERT INTO detalles_albaran_entrega (id_albaran,id_detalle_pedido,generico_solicitado,id_material,cantidad) VALUES (?,?,?,?,?);', 'sssss', array((string)($id_nuevo_albaran), (string)($filas_albaran["id_detalle_pedido"]), (string)($filas_albaran["generico_solicitado"]), (string)($filas_albaran["id_tarifa"]), (string)($filas_albaran["cantidad_albaran"]))) or die(db_error());
                        }
                // Borra la tabla temporal
                db_query_params('DELETE FROM detalles_pedido_extendido WHERE id_detalle_pedido=?', 's', array($filas_albaran["id_detalle_pedido"]));
                
                // Comprueba si ya he servido todo lo que necesitaba
                
                $busca_totales=db_query_params('SELECT id_detalle_pedido, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_entrega WHERE id_detalle_pedido=? GROUP BY id_detalle_pedido;', 's', array($filas_albaran["id_detalle_pedido"]));
                $totales=db_fetch_array($busca_totales);
                $total_servido=$totales["SumaDeCANTIDAD"];
                
                if ($filas_albaran["cantidad_inicial"]<=$total_servido) {
                        db_query_params('UPDATE detalles_pedido_material SET id_albaran=1 WHERE id_pedido=?', 's', array($filas_albaran["id_detalle_pedido"]));
                        }
                
                
                }

        header("Location: ver_albaran.php?id_albaran=$id_nuevo_albaran");
        }

if ($se_puede==false) {
        // Retorno

        if ($_GET["r"]==1) {
                header("Location: pedidos_pendientes.php");
                }
        if ($_GET["r"]==2) {
                        header("Location: listado_albaranes.php?id_pedido=".$id_pedido);
                        }
        if ($_GET["r"]==3) {
?>
<html>
<head>
<script language="Javascript">
self.opener.history.go(0);
window.close();
</script>
</head>
<body>
</body>
</html>
<?php 
                        } else {
                        header("Location: editar_pedido.php?id_pedido=".$id_pedido);
                        }
        }
        

?>
