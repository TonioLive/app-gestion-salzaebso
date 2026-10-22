<?php 
 require("../configuracion/config.php");
 require("../configuracion/autentifica.php");
?>
<html>
<head>
<meta name="Description" content="<?php  echo $meta_description; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords; ?>">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo1.php"); ?>
</style>
<script language="javascript">
self.focus();
</script>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<?php 

$ejercicio=$_GET["ejercicio"];
$mes_seleccionado=$_GET["mes"];
$id_cliente=$_GET["id_cliente"];

$filtros_fecha="";
if (strlen($mes_seleccionado)!=0) {
        $filtros_fecha=$filtros_fecha." AND month(PEDIDOS_CLIENTES.FECHA_PEDIDO)=".$mes_seleccionado;
        }
if (strlen($ejercicio)!=0) {
        $filtros_fecha=$filtros_fecha." AND year(PEDIDOS_CLIENTES.FECHA_PEDIDO)=".$ejercicio;
        }

if ((strlen($ejercicio)!=0) and (strlen($mes_seleccionado)==0)) {
      $coletilla=" durante el año ".$ejercicio;
      } else {
      if (strlen($mes_seleccionado)!=0) {
              $coletilla=" durante el mes de ".$nombre_mes[intval($mes)]." de ".$ejercicio;
              }
      }

$num_errores=0;

if ($num_errores==0)
{

$odbc = odbc_connect ($access_database, '', '') or die( "Could    Not Connect to ODBC Database!" );

$consulta_datos="SELECT * FROM TABLA_CIENTES WHERE id_cliente=".$id_cliente;
$datos_cliente = odbc_exec($odbc, $consulta_datos) or die (odbc_errormsg());
$Razon_social = odbc_result ($datos_cliente, "Razon_social");
$CIF = odbc_result ($datos_cliente, "CIF");
?>
<script language="Javascript">
function comprobar_y_enviar() {

        top.window.document.albaranes.submit();
}
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Resumen de Ventas<?=$coletilla;?></b></font><br>
                                         <br>
                        <?php 
                        
                        $contador=0;
                        
                        $total_absoluto=0;
                        $iva_4_absoluto=0;
                        $iva_7_absoluto=0;
                        $iva_16_absoluto=0;
                        $base_iva_4_absoluto=0;
                        $base_iva_7_absoluto=0;
                        $base_iva_16_absoluto=0;
                        $descuento_absoluto=0;
                        $recargo_absoluto=0;
                        $total_familias=array();
                        $total_proveedor=array();
                        
                        
                        // Mira si los albaranes de la factura y calcula los totales
                        $id_factura=$datos_factura["id_factura"];
// Busca albaranes
$consulta_datos_albaranes = odbc_exec($odbc, "SELECT * FROM PEDIDOS_CLIENTES WHERE ID_CLIENTE=".$id_cliente.$filtros_fecha) or die (odbc_errormsg());
$total_base=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_dto_fact=0;

while($datos_albaranes = odbc_fetch_array($consulta_datos_albaranes)){
        $contador=$contador+1;
        $consulta_detalle_albaranes = odbc_exec($odbc, "SELECT * FROM DETALLES_PEDIDO WHERE ID_PEDIDO=".$datos_albaranes["ID_PEDIDO"]) or die (odbc_errormsg());
        while($detalle_albaranes = odbc_fetch_array($consulta_detalle_albaranes)){
        
        $consulta_datos_articulo = odbc_exec($odbc, "SELECT * FROM TARIFA WHERE COD='".$detalle_albaranes["ARTICULO"]."'") or die (odbc_errormsg());
        $datos_articulo= odbc_fetch_array($consulta_datos_articulo);

        $consulta_datos_familia = odbc_exec($odbc, "SELECT * FROM TIPOS_IVA WHERE categoria='".$datos_articulo["CATEGORIA"]."'") or die (odbc_errormsg());
        $datos_familia= odbc_fetch_array($consulta_datos_familia);
        
        $total_linea=$detalle_albaranes["CANTIDAD"]*$detalle_albaranes["PRECIO"];
        $total_dto=$total_linea*$detalle_albaranes["DTO"];
        $total_dto_fact=$total_dto_fact+$total_dto;
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$detalle_albaranes["TIPO_IVA"];
        $total_abs=$base_linea+$iva_linea;

        $total_proveedor[intval($datos_articulo["PROVEEDOR"])]=$total_proveedor[intval($datos_articulo["PROVEEDOR"])]+$total_abs;
        $total_familias[intval($datos_familia["id_tipo_iva"])]=$total_familias[intval($datos_familia["id_tipo_iva"])]+$total_abs;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($detalle_albaranes["TIPO_IVA"])
                {
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.07:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.16:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
        }
}
if ($datos_del_cliente["recargo_equivalencia"]==true) {
        $total_recargo=nRound(($total_base*0.04),2);
        } else {
        $total_recargo=0;
        }
$total_factura=($total_base+$total_iva+$total_recargo);

$total_absoluto=$total_absoluto+$total_factura;
$base_absoluta=$base_absoluta+$total_base;
$iva_4_absoluto=$iva_4_absoluto+$total_iva_4;
$iva_7_absoluto=$iva_7_absoluto+$total_iva_7;
$iva_16_absoluto=$iva_16_absoluto+$total_iva_16;
$base_iva_4_absoluto=$base_iva_4_absoluto+$total_base_4;
$base_iva_7_absoluto=$base_iva_7_absoluto+$total_base_7;
$base_iva_16_absoluto=$base_iva_16_absoluto+$total_base_16;
$descuento_absoluto=$descuento_absoluto+$total_dto_fact;
$recargo_absoluto=$recargo_absoluto+$total_recargo;

$total_factura=number_format($total_factura, 2, ',', '.');
$total_recargo=number_format($total_recargo, 2, ',', '.');
$total_base=number_format(Round($total_base,2), 2, ',', '.');
$total_base_4=number_format(Round($total_base_4,2), 2, ',', '.');
$total_base_7=number_format(Round($total_base_7,2), 2, ',', '.');
$total_base_16=number_format(Round($total_base_16,2), 2, ',', '.');
$total_iva=number_format(Round($total_iva,2), 2, ',', '.');
$total_iva_4=number_format(Round($total_iva_4,2), 2, ',', '.');
$total_iva_7=number_format(Round($total_iva_7,2), 2, ',', '.');
$total_iva_16=number_format(Round($total_iva_16,2), 2, ',', '.');

                        ?>
                        <table border=1 bordercolor="<?= $fondo_at; ?>" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>">ID.</font></td>
                          <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>">Razón social</font></td>
                          <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>">CIF</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$id_cliente;?></font></td>
                          <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$Razon_social;?></font></td>
                          <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$CIF;?></font></td>
                        </tr>
                        </table>
                        <br>
                        <table border=1 bordercolor="<?= $fondo_at; ?>" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Detalles</b></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de alabaranes emitidos:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$contador;?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total ventas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Ventas por proveedor:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><table><?php 
                          $consulta_proveedores = odbc_exec($odbc, "SELECT * FROM PROVEEDORES ORDER BY RAZON_SOCIAL") or die (odbc_errormsg());
                          while($proveedores= odbc_fetch_array($consulta_proveedores)){
                          if ($total_proveedor[$proveedores["ID_PROVEEDOR"]]!=0) {
                          ?>
                          <tr><td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$proveedores["RAZON_SOCIAL"];?>:</font></td><td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_proveedor[$proveedores["ID_PROVEEDOR"]], 2, ',', '.');?> € (<?=round(100*($total_proveedor[$proveedores["ID_PROVEEDOR"]]/$total_absoluto),2)?> %)</font></td></tr><?php 
                          }
                          }
                          ?>
                          </table></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Ventas por familias:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><table><?php 
                          $consulta_proveedores = odbc_exec($odbc, "SELECT * FROM TIPOS_IVA ORDER BY categoria") or die (odbc_errormsg());
                          while($proveedores= odbc_fetch_array($consulta_proveedores)){
                          if ($total_familias[$proveedores["id_tipo_iva"]]!=0) {
                          ?>
                          <tr><td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$proveedores["categoria"];?>:</font></td><td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_familias[$proveedores["id_tipo_iva"]], 2, ',', '.');?> € (<?=round(100*($total_familias[$proveedores["id_tipo_iva"]]/$total_absoluto),2)?> %)</font></td></tr><?php 
                          }
                          }
                          ?>
                          </table></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">I.V.A. total cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($iva_4_absoluto+$iva_7_absoluto+$iva_16_absoluto, 2, ',', '.');?> €<br>
                          <table>
                          <tr><td align="right"><font size=2 face="<?= $fuente_general; ?>">4 %:</font></td><td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($iva_4_absoluto, 2, ',', '.');?> €</font></td></tr>
                          <tr><td align="right"><font size=2 face="<?= $fuente_general; ?>">7 %:</font></td><td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($iva_7_absoluto, 2, ',', '.');?> €</font></td></tr>
                          <tr><td align="right"><font size=2 face="<?= $fuente_general; ?>">16 %:</font></td><td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($iva_16_absoluto, 2, ',', '.');?> €</font></td></tr>
                          </table></font></td>
                        </tr>
                        <?php 
                        if ($recargo_absoluto>0) {
                        ?>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total R.E. cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($recargo_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <?php 
                        }
                        if ($descuento_absoluto>0) {
                        ?>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total descuentos:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($descuento_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                         <br>
                </td>
        </tr>
</table><br><br>
<?php 
// Ultimos 12 meses
if (strlen($mes_seleccionado)==0) {
        $mes_inicio=12;
        } else {
        $mes_inicio=intval($mes_seleccionado);
        }
if (strlen($ejercicio)==0) {
        $ejercicio_inicio=strftime("%Y");
        } else {
        $ejercicio_inicio=intval($ejercicio);
        }
        
$ejercicio_actual=$ejercicio_inicio;
$meses=array();
$ventas=array();
$ventas_helados=array();
$ventas_granizados=array();
$ventas_bolleria=array();
$iva=array();
$descuento=array();
$maximo=0;
$hay_helados=false;
$hay_bolleria=false;
$hay_granizados=false;
for ($c_meses=0; $c_meses<=12; $c_meses=$c_meses+1)
        {
        $mes_actual=$mes_inicio-$c_meses;

        if ($mes_actual==0) { $ejercicio_actual=$ejercicio_actual-1; }
        if ($mes_actual<1) { $mes_actual=$mes_actual+12; }
        
        $mes_buscado=$mes_actual."/".$ejercicio_actual;
        $consulta_meses="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva FROM PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."'));";
        
        $datos_mes = odbc_exec ($odbc, $consulta_meses);
        
        // Consumo proveedores
        $cod_helados=9;
        $consulta_helados="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva, TARIFA_COD_COMO_T.PROVEEDOR FROM (PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO) INNER JOIN TARIFA_COD_COMO_T ON DETALLES_PEDIDO.ARTICULO = TARIFA_COD_COMO_T.CODIGO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]), TARIFA_COD_COMO_T.PROVEEDOR HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."') AND ((TARIFA_COD_COMO_T.PROVEEDOR)=".$cod_helados."));";
        $datos_helados = odbc_exec ($odbc, $consulta_helados);

        $cod_granizados=18;
        $consulta_granizados="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva, TARIFA_COD_COMO_T.PROVEEDOR FROM (PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO) INNER JOIN TARIFA_COD_COMO_T ON DETALLES_PEDIDO.ARTICULO = TARIFA_COD_COMO_T.CODIGO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]), TARIFA_COD_COMO_T.PROVEEDOR HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."') AND ((TARIFA_COD_COMO_T.PROVEEDOR)=".$cod_granizados."));";
        $datos_granizados = odbc_exec ($odbc, $consulta_granizados);

        $cod_bolleria=4;
        $consulta_bolleria="SELECT Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]) AS Mes, Sum(([CANTIDAD]*[precio])*(1-[DTO])*(1+[TIPO_IVA])) AS Importe, Sum(([CANTIDAD]*[precio])*([DTO])) AS Descuento, Sum(([CANTIDAD]*[precio])*([TIPO_IVA])) AS Iva, TARIFA_COD_COMO_T.PROVEEDOR FROM (PEDIDOS_CLIENTES INNER JOIN DETALLES_PEDIDO ON PEDIDOS_CLIENTES.ID_PEDIDO = DETALLES_PEDIDO.ID_PEDIDO) INNER JOIN TARIFA_COD_COMO_T ON DETALLES_PEDIDO.ARTICULO = TARIFA_COD_COMO_T.CODIGO WHERE (((PEDIDOS_CLIENTES.ID_CLIENTE)=".$id_cliente.")) GROUP BY Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]), TARIFA_COD_COMO_T.PROVEEDOR HAVING (((Month([FECHA_PEDIDO]) & '/' & Year([fecha_pedido]))='".$mes_buscado."') AND ((TARIFA_COD_COMO_T.PROVEEDOR)=".$cod_bolleria."));";
        $datos_bolleria = odbc_exec ($odbc, $consulta_bolleria);

        // Guarda totales
        
        $meses[$c_meses]=$mes_buscado;
        $ventas[$c_meses]=odbc_result ($datos_mes, "Importe");
        $iva[$c_meses]=odbc_result ($datos_mes, "Iva");
        $descuento[$c_meses]=odbc_result ($datos_mes, "Descuento");
        $ventas_helados[$c_meses]=odbc_result ($datos_helados, "Importe");
        if ($ventas_helados[$c_meses]>0) { $hay_helados=true; }
        $ventas_granizados[$c_meses]=odbc_result ($datos_granizados, "Importe");
        if ($ventas_granizados[$c_meses]>0) { $hay_granizados=true; }
        $ventas_bolleria[$c_meses]=odbc_result ($datos_bolleria, "Importe");
        if ($ventas_bolleria[$c_meses]>0) { $hay_bolleria=true; }
        
        if ($ventas[$c_meses]>=$maximo) {
                $maximo=$ventas[$c_meses];
                }
        }
?>
<div align="center">
<table cellpadding=0 cellspacing=0>
<tr valign="bottom">
<td colspan=13 align="center"><font size=1><b>Consumo durante el último año</b></font></td>
</tr>
<tr valign="bottom">
<?php 
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas[$c_meses], 2, ',', '.')." €";
        $descuento_formateado=number_format($descuento[$c_meses], 2, ',', '.')." €";
        $iva_formateado=number_format($iva[$c_meses], 2, ',', '.')." €";
        
        if ($ventas[$c_meses]>0) {
                $altura_grafico=round(($ventas[$c_meses]*$unidad),0);
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><?=$ventas_formateado;?><br><img src="grafico_ventas.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                } else {
                $altura_grafico=0;
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><img src="grafico_vacio.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                }
        }
?>
</tr><tr>
<?php 
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        ?>
        <td align="center" style="border-right-style: solid; border-right-width: 1px" bordercolor="#000000"><font size=1><?=$nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))]."<br>".substr($meses[$c_meses],strlen($meses[$c_meses])-4,4);?></font></td>
        <?php 
        }
?>
</tr>
</table>
<br>
<?php 
if ($hay_helados==true) {
?>
<table cellpadding=0 cellspacing=0>
<tr valign="bottom">
<td colspan=13 align="center"><font size=1><b>Consumo de Helados durante el último año</b></font></td>
</tr>
<tr valign="bottom">
<?php 
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas_helados[$c_meses], 2, ',', '.')." €";

        if ($ventas_helados[$c_meses]>0) {
                $altura_grafico=round(($ventas_helados[$c_meses]*$unidad),0);
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><?=$ventas_formateado;?><br><img src="grafico_ventas.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                } else {
                $altura_grafico=0;
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><img src="grafico_vacio.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                }
        }
?>
</tr><tr>
<?php 
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        ?>
        <td align="center" style="border-right-style: solid; border-right-width: 1px" bordercolor="#000000"><font size=1><?=$nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))]."<br>".substr($meses[$c_meses],strlen($meses[$c_meses])-4,4);?></font></td>
        <?php 
        }
?>
</tr>
</table>
<br>
<?php 
}

if ($hay_granizados==true) {
?>
<table cellpadding=0 cellspacing=0>
<tr valign="bottom">
<td colspan=13 align="center"><font size=1><b>Consumo de Granizados durante el último año</b></font></td>
</tr>
<tr valign="bottom">
<?php 
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas_granizados[$c_meses], 2, ',', '.')." €";

        if ($ventas_granizados[$c_meses]>0) {
                $altura_grafico=round(($ventas_granizados[$c_meses]*$unidad),0);
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><?=$ventas_formateado;?><br><img src="grafico_ventas.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                } else {
                $altura_grafico=0;
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><img src="grafico_vacio.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                }
        }
?>
</tr><tr>
<?php 
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        ?>
        <td align="center" style="border-right-style: solid; border-right-width: 1px" bordercolor="#000000"><font size=1><?=$nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))]."<br>".substr($meses[$c_meses],strlen($meses[$c_meses])-4,4);?></font></td>
        <?php 
        }
?>
</tr>
</table>
<br>
<?php 
}

if ($hay_bolleria==true) {
?>
<table cellpadding=0 cellspacing=0>
<tr valign="bottom">
<td colspan=13 align="center"><font size=1><b>Consumo de Masas Congeladas durante el último año</b></font></td>
</tr>
<tr valign="bottom">
<?php 
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $ventas_formateado=number_format($ventas_bolleria[$c_meses], 2, ',', '.')." €";

        if ($ventas_bolleria[$c_meses]>0) {
                $altura_grafico=round(($ventas_bolleria[$c_meses]*$unidad),0);
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><?=$ventas_formateado;?><br><img src="grafico_ventas.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                } else {
                $altura_grafico=0;
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><img src="grafico_vacio.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                }
        }
?>
</tr><tr>
<?php 
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        ?>
        <td align="center" style="border-right-style: solid; border-right-width: 1px" bordercolor="#000000"><font size=1><?=$nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))]."<br>".substr($meses[$c_meses],strlen($meses[$c_meses])-4,4);?></font></td>
        <?php 
        }
?>
</tr>
</table>
<br>
<?php 
}

if (($hay_helados==true) or ($hay_ganizados==true) or ($hay_bolleria==true)) {
?>
<table cellpadding=0 cellspacing=0>
<tr valign="bottom">
<td colspan=13 align="center"><font size=1><b>Consumo de Otros Productos durante el último año</b></font></td>
</tr>
<tr valign="bottom">
<?php 
$altura_total=150;
if ($maximo>0) {
               $unidad=$altura_total/$maximo;
               } else {
               $unidad=1;
               }
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        $total_otros=$ventas[$c_meses]-$ventas_helados[$c_meses]-$ventas_granizados[$c_meses]-$ventas_bolleria[$c_meses];
        $ventas_formateado=number_format($total_otros, 2, ',', '.')." €";

        if ($total_otros>0) {
                $altura_grafico=round(($total_otros*$unidad),0);
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><?=$ventas_formateado;?><br><img src="grafico_ventas.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                } else {
                $altura_grafico=0;
                ?>
                <td align="center" bgcolor="#FFFFFF" style="border-bottom-style: solid; border-bottom-width: 1px" bordercolor="#000000"><font size=1><img src="grafico_vacio.gif" border=0 width="32" height="<?=$altura_grafico;?>"></font></td>
                <?php 
                }
        }
?>
</tr><tr>
<?php 
for ($c_meses=12; $c_meses>=0; $c_meses=$c_meses-1)
        {
        ?>
        <td align="center" style="border-right-style: solid; border-right-width: 1px" bordercolor="#000000"><font size=1><?=$nombre_mes[intval(substr($meses[$c_meses],0,strlen($meses[$c_meses])-5))]."<br>".substr($meses[$c_meses],strlen($meses[$c_meses])-4,4);?></font></td>
        <?php 
        }
?>
</tr>
</table>
<br>
<?php 
}
?>
</div>
<br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php 
 odbc_close($odbc);
 } else {
?><br><br>
<font size=2>
<p align="center"><b><i>
Se han detectado errores al cumplimentar el formulario.<br>Por favor, vuelva atras para revisar
los siguientes puntos:<br>
</i></b></p>
<p align="center">
<font face="<?php  echo $fuente_general; ?>">
<table width="75%" bgcolor="#ececec">
<?php 
    for ($contador=1; $contador<=$num_errores; $contador=$contador+1)
    {
      print "<tr><td><font face=\"Verdana\" size=1 color=\"".$color_fuente."\"><b>- $errores[$contador]</b></font></td></tr>";
    }
?>
</table>
<?php 
  print "</small></small><br><br><b>Vuelve al paso anterior y corrige los errores.</b><br><br>";
  print "<input type=\"button\" value=\"<< Volver al paso anterior\" onclick=\"history.go(-1)\" id=1 name=1></p></font>";
 }
?>
</body>
</html>
