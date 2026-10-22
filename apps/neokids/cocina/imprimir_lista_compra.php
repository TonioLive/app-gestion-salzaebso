<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_lista=$_GET["id_lista"];
$guardar=$_GET["g"];

$unidad_medida[0]="g.&nbsp;&nbsp;";
$unidad_medida[1]="ml.";

if ($guardar==1) {

        $id_lista=$_POST["id_lista"];
        $observaciones=$_POST["observaciones"];

        if (strlen($observaciones)!=0) {
                $observaciones=urlencode($observaciones);
                }

        $actualizar_lista=db_query_params('UPDATE menu_lista_compra SET observaciones=? WHERE id_lista=?', 'ss', array((string)($observaciones), $id_lista)) or die(db_error()." - 1");

        $array_id_alimento=$_POST["id_alimento"];
        $array_cantidad_alimento=$_POST["cantidad_redondeada"];
        $array_id_lista_alimento=$_POST["id_lista_alimento"];

        $array_stock_articulo=$_POST["stock_articulo"];
        $array_cantidad_servida_uds=$_POST["cantidad_servida_uds"];
        $array_stock_resultante_articulo=$_POST["stock_resultante_articulo"];

        for($c_array=0;$c_array<count($array_id_alimento);$c_array++) {

                if (strlen($array_cantidad_alimento[$c_array])!=0) {
                        $cantidad_alimento=number_format($array_cantidad_alimento[$c_array],2,".","");
                        } else {
                        $cantidad_alimento=0;
                        }
                $actualiza_fila_lista=db_query("UPDATE menu_productos_lista SET cantidad_redondeada='$cantidad_alimento' WHERE id_producto=".$array_id_lista_alimento[$c_array].";") or die(db_error()." - 2");

                }

        $array_id_articulo_tarifa=$_POST["id_articulo_tarifa"];
        $array_cantidad_servida=$_POST["cantidad_servida"];

        for($c_array=0;$c_array<count($array_id_articulo_tarifa);$c_array++) {

        if (strlen($array_cantidad_servida[$c_array])!=0) {
                $cantidad_alimento=number_format($array_cantidad_servida[$c_array],2,".","");
                } else {
                $cantidad_alimento=0;
                }
        if (strlen($array_stock_articulo[$c_array])!=0) {
                $stock_articulo=number_format($array_stock_articulo[$c_array],2,".","");
                } else {
                $stock_articulo=0;
                }
        if (strlen($array_cantidad_servida_uds[$c_array])!=0) {
                $cantidad_servida_uds=number_format($array_cantidad_servida_uds[$c_array],2,".","");
                } else {
                $cantidad_servida_uds=0;
                }
        if (strlen($array_stock_resultante_articulo[$c_array])!=0) {
                $stock_resultante_articulo=number_format($array_stock_resultante_articulo[$c_array],2,".","");
                } else {
                $stock_resultante_articulo=0;
                }
        // Comprueba si ya está
        $busca_cantidades_servidas=db_query("SELECT * FROM menu_productos_proveedor WHERE id_lista=$id_lista AND id_producto_tarifa=".$array_id_articulo_tarifa[$c_array]) or die(db_error()." - 3");
        if (db_num_rows($busca_cantidades_servidas)!=0) {
                        // Actualiza vieja
                        $cantidades_servidas=db_fetch_array($busca_cantidades_servidas);
                        $id_prod_servido=$cantidades_servidas["id_producto_lista"];
                        $actualiza_fila_lista=db_query_params('UPDATE menu_productos_proveedor SET cantidad=?, stock=?, cantidad_resultante=?, unidades=? WHERE id_producto_lista=?;', 'sssss', array((string)($cantidad_alimento), (string)($stock_articulo), (string)($stock_resultante_articulo), (string)($cantidad_servida_uds), $id_prod_servido)) or die(db_error()." - 4");
                        } else {
                        // Inserta nueva
                        $introduce_fila_lista=db_query_params('INSERT INTO menu_productos_proveedor (id_producto_tarifa,id_lista,cantidad,stock,cantidad_resultante,unidades) VALUES (?,?,?,?,?,?);', 'ssssss', array((string)($array_id_articulo_tarifa[$c_array]), (string)($id_lista), (string)($cantidad_alimento), (string)($stock_articulo), (string)($stock_resultante_articulo), (string)($cantidad_servida_uds))) or die(db_error()." - 5");
                        }

        }

        }

$busca_datos_lista=db_query_params('SELECT * FROM menu_lista_compra WHERE id_lista=?', 's', array($id_lista)) or die(db_error());

$datos_lista=db_fetch_array($busca_datos_lista);

$texto_observaciones=$datos_lista["observaciones"];

if (strlen($texto_observaciones)!=0) {
        $texto_observaciones=urldecode($texto_observaciones);
        }

        $fecha_time_stamp=strtotime($datos_lista["fecha_ini"]);
        $fin_time_stamp=strtotime($datos_lista["fecha_fin"]);

        if ($fin_time_stamp==$fecha_time_stamp) {
                $texto_encabezado="el día ".date("d/m/Y",$fecha_time_stamp);
                } else {
                if ($fin_time_stamp>$fecha_time_stamp) {
                        $texto_encabezado="el periodo entre el ".date("d/m/Y",$fecha_time_stamp)." y el ".date("d/m/Y",$fin_time_stamp);
                        } else {
                        $texto_encabezado="el día ".date("d/m/Y",$fecha_time_stamp);
                        }
                }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
self.print();
window.opener.history.go(0);
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<br><br>
<div align="center"><center><font size=3><b>Lista de la compra para <?=$texto_encabezado;?></b></font>
<br><br>
<table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
  <tr valign="top">
    <td align="right" width=25% bgcolor="#efefef"><font size=2><b>Observaciones</b></font></td>
    <td align="left" bgcolor="#ffffff"><font size=2><?=$texto_observaciones?></font></td>
  </tr>
</table>
<br>
<table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">

<?php 
$total_pedido_proveedor=0;
$total_pedido=0;

$proveedor_actual="";
$nombre_proveedor="";

$busca_alimentos=db_query_params('SELECT * FROM menu_productos_proveedor, tarifa WHERE menu_productos_proveedor.id_producto_tarifa=tarifa.ID AND menu_productos_proveedor.id_lista=? AND menu_productos_proveedor.cantidad<>0 ORDER BY tarifa.PROVEEDOR', 's', array($id_lista)) or die(db_error());
while($listado_alimentos=db_fetch_array($busca_alimentos)) {


if ($proveedor_actual!=$listado_alimentos["PROVEEDOR"]) {

// Total proveedor
if ($proveedor_actual!="") {
?>
  <tr valign="top">
    <td align="right" colspan=4 bgcolor="#efefef"><font size=2><b>Total <?=$nombre_proveedor?></b></font></td>
    <td align="right" width=75 bgcolor="#ffffff"><font size=2><?=number_format($total_pedido_proveedor,2,",",".");?> €</font></td>
  </tr>
  </table><br>
  <table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
<?php 
        }

// Datos proveedor

$consulta_datos_proveedor=db_query("SELECT * FROM proveedores WHERE ID_PROVEEDOR=".$listado_alimentos["PROVEEDOR"]);
if (db_num_rows($consulta_datos_proveedor)!=0) {
        $datos_proveedor=db_fetch_array($consulta_datos_proveedor);

        $nombre_proveedor=$datos_proveedor["nombre_comercial"];
        if(strlen($nombre_proveedor)!=0) {
                $nombre_proveedor=$nombre_proveedor." - ".$datos_proveedor["RAZON_SOCIAL"];
                } else {
                $nombre_proveedor=$datos_proveedor["RAZON_SOCIAL"];
                }
        } else {
        $nombre_proveedor="Desconocido";
        }
?>
  <tr valign="top">
    <td align="left" colspan=7 bgcolor="#efefef"><font size=3><b><?=$nombre_proveedor?></b></font></td>
  </tr>
  <tr valign="top">
    <td align="left" width=160 bgcolor="#efefef"><font size=2><b>Genérico</b></font></td>
    <td align="left" width=340 bgcolor="#efefef"><font size=2><b>Denominación</b></font></td>
    <td align="center" width=90 bgcolor="#efefef"><font size=2><b>Cantidad Total</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Cantidad a comprar</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Unidades a comprar</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Precio</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Total</b></font></td>
  </tr>
<?php 
        $proveedor_actual=$listado_alimentos["PROVEEDOR"];
        $total_pedido_proveedor=0;
        }

// Busca genérico
if (strlen($listado_alimentos["ID_ALIMENTO_GENERICO"])!=0) {
        $consulta_generico=db_query_params('SELECT * FROM menu_alimentos WHERE id_alimento=?', 's', array($listado_alimentos["ID_ALIMENTO_GENERICO"]));
        if (db_num_rows($consulta_generico)!=0) {
                $datos_generico=db_fetch_array($consulta_generico);
                $nombre_generico=$datos_generico["nombre_alimento"];
                $unidades_medida_actual=$unidad_medida[$datos_generico["unidad_medida"]];
                } else {
                $nombre_generico="Desconocido";
                $unidades_medida_actual="&nbsp;&nbsp;&nbsp;";
                }
        } else {
        $unidades_medida_actual="&nbsp;&nbsp;&nbsp;";
        $nombre_generico="Desconocido";
        }

$unidad_articulo_actual=$listado_alimentos["UNIDAD"];
if (is_numeric($unidad_articulo_actual)==false) {
                $unidad_articulo_actual=1;
                }

$precio_articulo=$listado_alimentos["PRECIO_VENTA"];
$nombre_articulo_tarifa=$listado_alimentos["ARTICULO"];
$unidades_guardadas=$listado_alimentos["unidades"];
$cantidad_guardadas=$listado_alimentos["cantidad"];
$cantidad_resultante=$listado_alimentos["cantidad_resultante"];
?>
<tr bgcolor="#ffffff">
        <td align="left"><font size=2><?=urldecode($nombre_generico);?></font></td>
        <td align="left"><font size=2><?=$nombre_articulo_tarifa;?></font></td>
        <td align="right"><font size=2><?=number_format($cantidad_guardadas,2,",",".");?> <?=$unidades_medida_actual?></font></td>
        <td align="right"><font size=2><?=number_format($cantidad_resultante,2,",",".");?></font></td>
        <td align="right"><font size=2><?=number_format($unidades_guardadas,2,",",".");?></font></td>
        <td align="right"><font size=2><?=number_format($precio_articulo,2,",",".");?> €</font></td>
        <td align="right"><font size=2><?=number_format(($unidades_guardadas*$precio_articulo),2,",",".");?> €</font></td>
</tr>
                                                <?php 
                                                $total_pedido=$total_pedido+round(($unidades_guardadas*$precio_articulo),2);
                                                $total_pedido_proveedor=$total_pedido_proveedor+round(($unidades_guardadas*$precio_articulo),2);
                                                }
                                                ?>
  <tr valign="top">
    <td align="right" colspan=5 bgcolor="#efefef"><font size=2><b>Total <?=$nombre_proveedor?></b></font></td>
    <td align="right" width=75 bgcolor="#ffffff"><font size=2><?=number_format($total_pedido_proveedor,2,",",".");?> €</font></td>
  </tr>
  </table><br>
  <table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
  <tr valign="top">
    <td align="right" bgcolor="#efefef"><font size=2><b>Total COMPRA</b></font></td>
    <td align="right" width=75 bgcolor="#ffffff"><font size=2><?=number_format($total_pedido,2,",",".");?> €</font></td>
  </tr>
</table>
</center></div>
</div>
</body>
</html>
