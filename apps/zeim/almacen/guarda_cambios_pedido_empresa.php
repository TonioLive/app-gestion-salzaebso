<?php 
require("../configuracion/config.php");

$id_pedido_empresa=$_POST["id_pedido"];

$array_id_articulo_tarifa=$_POST["id_articulo_tarifa"];
$array_id_extendido=$_POST["id_extendido"];
$array_cantidad_inicial_fila=$_POST["cantidad_inicial_fila"];
$array_cantidad_servida=$_POST["cantidad_servida"];
$array_stock_articulo=$_POST["stock_articulo"];
$array_cantidad_resultante=$_POST["cantidad_resultante"];

for($c_array=0;$c_array<count($array_id_articulo_tarifa);$c_array++) {

        if (strlen($array_cantidad_servida[$c_array])!=0) {
                $cantidad_servida=number_format($array_cantidad_servida[$c_array],2,".","");
                } else {
                $cantidad_servida=0;
                }
        $actualiza_fila_lista=db_query("UPDATE detalles_pedido_empresa_extendido SET cantidad_pedido='$cantidad_servida', stock_articulo='".$array_stock_articulo[$c_array]."', cantidad_resultante='".$array_cantidad_resultante[$c_array]."'  WHERE id_extendido=".$array_id_extendido[$c_array].";") or die(db_error()." - 2");

        }

if (strlen($_POST["observaciones"])!=0) {
        $observaciones=db_escape_string($_POST["observaciones"]);
        db_query("UPDATE pedidos_material_empresa SET observaciones='$observaciones' WHERE id_pedido_empresa=$id_pedido_empresa");
        }

if ($_POST["retorno"]==1) {
        header("Location: ver_pedido_empresa.php?id_pedido=".$id_pedido_empresa);
} else {
if ($_POST["pedidos"]==1) {

// Ya se ha guardado. Ahora generamos los pedidos

// 1º Lista de proveedores
$listado_proveedores=db_query_params('SELECT * FROM detalles_pedido_empresa,gestionguarderias.tarifa,detalles_pedido_empresa_extendido WHERE detalles_pedido_empresa.id_articulo=detalles_pedido_empresa_extendido.id_articulo_pedido AND detalles_pedido_empresa_extendido.id_tarifa=tarifa.ID AND detalles_pedido_empresa.id_pedido_empresa=? AND detalles_pedido_empresa_extendido.cantidad_resultante<>0 AND detalles_pedido_empresa.pedido=0 GROUP BY tarifa.PROVEEDOR', 's', array($id_pedido_empresa));

while($datos_proveedores=db_fetch_array($listado_proveedores)) {
// Introduce pedido del proveedor

$intro_nuevo_pedido=db_query("INSERT INTO pedidos_proveedor (id_proveedor,fecha_pedido,observaciones) VALUES ('".$datos_proveedores["PROVEEDOR"]."','".date("Y-m-d")."','$observaciones');");

$id_nuevo_pedido=db_insert_id();

// Busca artículos de este proveedor

$busca_articulos_lista=db_query("SELECT * FROM detalles_pedido_empresa,gestionguarderias.tarifa,gestionguarderias.tipos_iva_servicios,detalles_pedido_empresa_extendido WHERE detalles_pedido_empresa.id_articulo=detalles_pedido_empresa_extendido.id_articulo_pedido AND detalles_pedido_empresa_extendido.id_tarifa=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND detalles_pedido_empresa.id_pedido_empresa=$id_pedido_empresa AND detalles_pedido_empresa_extendido.cantidad_resultante<>0 AND detalles_pedido_empresa.pedido=0 AND tarifa.PROVEEDOR=".$datos_proveedores["PROVEEDOR"]);
while($articulos_lista=db_fetch_array($busca_articulos_lista)) {
        $inserta_fila=db_query_params('INSERT INTO detalles_pedido_proveedor (id_pedido,id_articulo,cantidad,precio,tipo_iva,id_origen) VALUES (?,?,?,?,?,?)', 'ssssss', array((string)($id_nuevo_pedido), (string)($articulos_lista["ID"]), (string)($articulos_lista["cantidad_resultante"]), (string)($articulos_lista["PRECIO_VENTA"]), (string)($articulos_lista["porcentaje_iva"]), (string)($articulos_lista["id_articulo"])));
        $actualiza_dato=db_query_params('UPDATE detalles_pedido_empresa SET pedido=1 WHERE id_articulo=?', 's', array($articulos_lista["id_articulo"]));
        }
}

        header("Location: /almacen/ver_pedidos_fecha.php?fecha=".time());
        } else {
        header("Location: calendario_pedidos_empresa.php");
        }
}
?>
