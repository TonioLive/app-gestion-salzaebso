<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_pedido_empresa=$_POST["id_pedido"];
$id_pedido=$id_pedido_empresa;

$idProveedor=$_POST["id_proveedor"];

$array_id_articulo_tarifa=$_POST["id_articulo_tarifa"];
$array_id_extendido=$_POST["id_extendido"];
$array_cantidad_inicial_fila=$_POST["cantidad_inicial_fila"];
$array_cantidad_servida=$_POST["cantidad_servida"];
$array_stock_articulo=$_POST["stock_articulo"];
$array_cantidad_resultante=$_POST["cantidad_resultante"];

for($c_array=0;$c_array<count($array_id_articulo_tarifa);$c_array++) {

        if (strlen($array_cantidad_servida[$c_array])!=0) {
                $cantidad_servida=$array_cantidad_servida[$c_array];
                } else {
                $cantidad_servida=0;
                }
        $actualiza_fila_lista=db_query("UPDATE detalles_pedido_empresa_extendido SET cantidad_pedido='$cantidad_servida', stock_articulo='".$array_stock_articulo[$c_array]."', cantidad_resultante='".$array_cantidad_resultante[$c_array]."'  WHERE id_extendido=".$array_id_extendido[$c_array].";") or die(db_error()." - 2");

        }

if (strlen($_POST["observaciones"])!=0) {
        $observaciones=db_escape_string($_POST["observaciones"]);
        db_query("UPDATE pedidos_material_empresa SET observaciones='$observaciones' WHERE id_pedido_empresa=$id_pedido_empresa");
        }


$busca_datos_lista=db_query_params('SELECT * FROM pedidos_material_empresa WHERE id_pedido_empresa=?', 's', array($id_pedido)) or die(db_error());

$datos_lista=db_fetch_array($busca_datos_lista);

$texto_observaciones=$datos_lista["observaciones"];

if (strlen($texto_observaciones)!=0) {
        $texto_observaciones=urldecode($texto_observaciones);
        }

$texto_encabezado="material aulas"

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>

<?php  require("../configuracion/estilos/estilo2.php"); ?>

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
<?php
$filtroProveedor = "";
$numeroColumnas=7;
if (strlen($idProveedor)!=0) {

        // Filtro proveedor
        $filtroProveedor = " AND tarifa.PROVEEDOR=".$idProveedor;
        $numeroColumnas = 2;
        ?>
        <div align="center"><center><font size=3><b>Pedido de Material - <?=date("d/m/Y",strtotime($datos_lista["fecha_pedido"]));?></b></font>
        <br><br>
        <?php
} else {
?>
<div align="center"><center><font size=3><b>Lista de la compra <?=$texto_encabezado;?></b></font>
<br><br>
<table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
  <tr valign="top">
    <td align="right" width=25% bgcolor="#efefef"><font size=2><b>Observaciones</b></font></td>
    <td align="left" bgcolor="#ffffff"><font size=2><?=$texto_observaciones?></font></td>
  </tr>
</table>
<br>
<?php
}
?>
<table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">

<?php 
$total_pedido_proveedor=0;
$total_pedido=0;

$proveedor_actual="";
$nombre_proveedor="";

$unidad_medida[0]="g.&nbsp;&nbsp;";
$unidad_medida[1]="ml.";
$busca_alimentos=db_query("SELECT * FROM detalles_pedido_empresa, gestionguarderias.tarifa, detalles_pedido_empresa_extendido WHERE  detalles_pedido_empresa.id_articulo=detalles_pedido_empresa_extendido.id_articulo_pedido AND detalles_pedido_empresa_extendido.id_tarifa=tarifa.ID AND detalles_pedido_empresa.id_pedido_empresa=$id_pedido AND detalles_pedido_empresa_extendido.cantidad_resultante<>0".$filtroProveedor." ORDER BY tarifa.PROVEEDOR,tarifa.ARTICULO_GENERICO") or die(db_error());
while($listado_alimentos=db_fetch_array($busca_alimentos)) {


if ($proveedor_actual!=$listado_alimentos["PROVEEDOR"]) {

// Total proveedor
if ($proveedor_actual!="") {

        ?>
        <tr valign="top">
        <td align="right" colspan=6 bgcolor="#efefef"><font size=2><b>Total <?=$nombre_proveedor?></b></font></td>
        <td align="right" width=75 bgcolor="#ffffff"><font size=2><?=number_format($total_pedido_proveedor,2,",",".");?> €</font></td>
        </tr>
        </table><br>
        <table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
        <?php 

        }

// Datos proveedor

$consulta_datos_proveedor=db_query("SELECT * FROM gestionguarderias.proveedores WHERE ID_PROVEEDOR=".$listado_alimentos["PROVEEDOR"]);
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
    <td align="left" colspan=<?=$numeroColumnas?> bgcolor="#efefef"><font size=3><b><?=$nombre_proveedor?></b></font></td>
  </tr>
  <?php
    if (strlen($idProveedor)==0) {
    ?>  
  <tr valign="top">
    <td align="left" bgcolor="#efefef" colspan=2><font size=2><b>&nbsp;</b></font></td>
    <td align="left" bgcolor="#efefef" colspan=3><font size=2><b>Cantidades</b></font></td>
    <td align="center" bgcolor="#efefef" colspan=2><font size=2><b>&nbsp;</b></font></td>
  </tr>
  <?php
    }
  ?>
  <tr valign="top">
    <?php
    if (strlen($idProveedor)==0) {
    ?>
    <td align="left" width=190 bgcolor="#efefef"><font size=2><b>Genérico</b></font></td>
    <td align="left" width=350 bgcolor="#efefef"><font size=2><b>Denominación</b></font></td>
    <?php
    } else {
    ?>
    <td align="left" width=350 bgcolor="#efefef"><font size=2><b>Producto</b></font></td>
    <?php    
    }
    ?>
    <?php
    if (strlen($idProveedor)==0) {
    ?>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Suma Pedidos</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Stock almacen</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Real Comprada</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Precio</b></font></td>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>Total</b></font></td>    
    <?php
    } else {
    ?>
    <td align="center" width=60 bgcolor="#efefef"><font size=2><b>cantidad</b></font></td>
    <?php    
    }
    ?>
  </tr>
<?php 
        $proveedor_actual=$listado_alimentos["PROVEEDOR"];
        $total_pedido_proveedor=0;
        }

// Busca genérico
if (strlen($listado_alimentos["ARTICULO_GENERICO"])!=0) {
        $nombre_generico=$listado_alimentos["ARTICULO_GENERICO"];
        } else {
        $nombre_generico="Desconocido";
        }
        
// Cantidad pedido inicial
$busca_cantidades_pedidas=db_query_params('SELECT detalles_pedido_material.generico_solicitado, sum(detalles_pedido_material.cantidad) AS suma_total FROM detalles_pedido_material,pedidos_material_empresa WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido_empresa=pedidos_material_empresa.id_pedido_empresa AND detalles_pedido_material.id_pedido_empresa=? AND detalles_pedido_material.generico_solicitado=? GROUP BY detalles_pedido_material.generico_solicitado ', 'ss', array($id_pedido_empresa, (string)($nombre_generico))) or die(db_error()." -1");
if (db_num_rows($busca_cantidades_pedidas)!=0) {
        $cantidades_pedidas=db_fetch_array($busca_cantidades_pedidas);
        $unidades_pedidas=$cantidades_pedidas["suma_total"];
        } else {
        $unidades_pedidas=0;
        }

$precio_articulo=$listado_alimentos["PRECIO_VENTA"];
$nombre_articulo_tarifa=$listado_alimentos["ARTICULO"];
$unidades_guardadas=$listado_alimentos["cantidad_resultante"];
$stock_almacen=$listado_alimentos["stock_articulo"];

?>
<tr bgcolor="#ffffff">
        <?php
        if (strlen($idProveedor)==0) {
        ?>
                <td align="left"><font size=2><?=urldecode($nombre_generico);?></font></td>
        <?php
        }
        ?>
        <td align="left"><font size=2><?=$nombre_articulo_tarifa;?></font></td>
        <?php

        $unidades_guardadas_string = number_format($unidades_guardadas,2,",",".");
        $unidades_guardadas_string = str_replace(",00", "", $unidades_guardadas_string);        
    if (strlen($idProveedor)==0) {
        $total_pedido_string = number_format(($unidades_guardadas*$precio_articulo),2,",",".");
    ?>
        <td align="right"><font size=2><?=number_format($unidades_pedidas,2,",",".")." ".$listado_alimentos["unidad_medida"];?></font></td>
        <td align="right"><font size=2><?=number_format($stock_almacen,2,",",".");?></font></td>
        <td align="right"><font size=2><?=$unidades_guardadas_string." ".$listado_alimentos["unidad_medida"];?></font></td>
        <td align="right"><font size=2><?=number_format($precio_articulo,2,",",".");?> €</font></td>
        <td align="right"><font size=2><?=$total_pedido_string;?> €</font></td>        
    <?php
    } else {
    ?>
        <td align="right"><font size=2><?=$unidades_guardadas_string." ".$listado_alimentos["unidad_medida"];?></font></td>
    <?php    
    }
    ?>        
</tr>
                                                <?php 
                                                $total_pedido=$total_pedido+round(($unidades_guardadas*$precio_articulo),2);
                                                $total_pedido_proveedor=$total_pedido_proveedor+round(($unidades_guardadas*$precio_articulo),2);
                                                }

if (strlen($idProveedor)==0) {                                                
?>
  <tr valign="top">
    <td align="right" colspan=<?=$numeroColumnas-1;?> bgcolor="#efefef"><font size=2><b>Total <?=$nombre_proveedor?></b></font></td>
    <td align="right" width=75 bgcolor="#ffffff"><font size=2><?=number_format($total_pedido_proveedor,2,",",".");?> €</font></td>
  </tr>
  <?php
}
?>
  </table><br>
<?php
if (strlen($idProveedor)==0) {                                                
?>
  <table border=1 width=780 cellpadding=2 style="border-collapse: collapse" bordercolor="#000000">
  <tr valign="top">
    <td align="right" bgcolor="#efefef"><font size=2><b>Total COMPRA</b></font></td>
    <td align="right" width=75 bgcolor="#ffffff"><font size=2><?=number_format($total_pedido,2,",",".");?> €</font></td>
  </tr>
</table>
<?php
}
?>
</center></div>
</div>
</body>
</html>
