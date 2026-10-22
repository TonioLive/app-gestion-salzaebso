<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_lista=$_GET["id_lista"];

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
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center><font size=2><br><br><b>Lista de la compra para <?=$texto_encabezado;?></b></font>
<div id="botonera" style="display:block;padding:0px;border:0px solid"><br>
<input type="button" value="Guardar cambios" style="font-size: 10px" onclick="guardar_cambios(1)"> <input type="button" value="Generar Albaranes" style="font-size: 10px" onclick="guardar_cambios(2)"> <input type="button" onclick="imprimir()" value="Imprimir" style="font-size: 10px"> <input type="button" onclick="window.close()" value="Salir" style="font-size: 10px"><br><br>
</div>
<form name="lista_compra" action="modificar_lista.php" method="POST">
<input type="hidden" name="id_lista" value="<?=$id_lista;?>">
<div id="observaciones1" style="display:block;padding:0px;border:0px solid"><br>
<table border=1 width=100% style="border-collapse: collapse" bordercolor="#000000">
  <tr valign="top">
    <td align="right" width=25% bgcolor="<?php  echo $fondo_at; ?>"><font size=2><b>Observaciones</b></font></td>
    <td align="left" bgcolor="#ffffff"><font size=2><b><textarea cols=150 rows=3 style="font-size: 11px; font-family: Arial" name="observaciones"><?=$texto_observaciones?></textarea></b></font></td>
  </tr>
</table>
</div>
<div id="observaciones2" style="display:none;padding:0px;border:0px solid"></div>
<br>
<table border=1 width=100% style="border-collapse: collapse" bordercolor="#000000">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" width=20% rowspan=2><font size=2><b>Producto</b></font></td>
    <td align="center" width=10% rowspan=2><font size=2><b>Cantidad inicial necesaria</b></font></td>
    <td align="center" width=10% rowspan=2><font size=2><b>Cantidad recomendada</b></font></td>
    <td align="center" width=60% colspan=7><font size=2><b>Productos proveedores</b></font></td>
</tr>
<tr>
    <td align="center" width="19%"><font size=1><b>Descripción</b></font></td>
    <td align="center" width="7%"><font size=1><b>Presentación</b></font></td>
    <td align="center" width="6%"><font size=1><b>Cantidad</b></font></td>
    <td align="center" width="7%"><font size=1><b>Precio</b></font></td>
    <td align="center" width="7%"><font size=1><b>Stock</b></font></td>
    <td align="center" width="15%"><font size=1><b>Uds.</b></font></td>
    <td align="center" width="15%"><font size=1><b>Total Euros</b></font></td>
  </tr>
<?php 
$total_pedido=0;
$contador1=0;
$contador2=0;
$unidad_medida[0]="g.&nbsp;&nbsp;";
$unidad_medida[1]="ml.";
$busca_alimentos=db_query_params('SELECT * FROM menu_productos_lista,menu_alimentos WHERE menu_productos_lista.id_alimento=menu_alimentos.id_alimento AND menu_productos_lista.id_lista=? ORDER BY menu_alimentos.nombre_alimento', 's', array($id_lista));
while($listado_alimentos=db_fetch_array($busca_alimentos)) {
?>
<tr>
        <input type="hidden" name="id_alimento[]" value="<?=$listado_alimentos["id_alimento"];?>">
        <input type="hidden" name="id_lista_alimento[]" value="<?=$listado_alimentos["id_producto"];?>">
        <td bgcolor="#efefef"><font size=2><?=urldecode($listado_alimentos["nombre_alimento"]);?></font></td>
        <td align="right"><font size=1><?=number_format($listado_alimentos["cantidad_recomendada"],2,",",".");?> <?=$unidad_medida[$listado_alimentos["unidad_medida"]]?></font></td>
        <td align="right"><font size=1><input type="text" size=10 name="cantidad_redondeada[]" id="cantidad_redondeada_<?=$contador1;?>" value="<?=number_format($listado_alimentos["cantidad_redondeada"],2,".","");?>" style="font-size: 10px; text-align: right"> <?=$unidad_medida[$listado_alimentos["unidad_medida"]]?></font></td>
        <td align="center" colspan=7><?php 
        $busca_productos_de_proveedores=db_query_params('SELECT * FROM tarifa WHERE ID_ALIMENTO_GENERICO=?', 's', array($listado_alimentos["id_alimento"]));
        if (db_num_rows($busca_productos_de_proveedores)==0) {
        ?><font size=2>No disponible</font><?php 
        } else {
        ?><table cellpadding=1 cellspacing=1 border=0 width=100%>
                                                <?php 
                                                // Busca referencias para el genérico
                                                $contador_filas1=0;
                                                if (db_num_rows($busca_productos_de_proveedores)==1) {
                                                        $solo1=true;
                                                        } else {
                                                        $solo1=false;
                                                        }
                                                        
                                                // Busca último producto comprado

                                                $busca_ultima=db_query_params('SELECT * FROM pedidos_proveedor,detalles_pedido_proveedor,tarifa WHERE pedidos_proveedor.id_pedido=detalles_pedido_proveedor.id_pedido AND detalles_pedido_proveedor.id_articulo=tarifa.ID AND tarifa.ID_ALIMENTO_GENERICO=? ORDER BY pedidos_proveedor.fecha_pedido DESC', 's', array($listado_alimentos["id_alimento"])) or die(db_error());

                                                if (db_num_rows($busca_ultima)==0) {
                                                        $id_ultimo=0;
                                                        $alt_ultimo="";
                                                        $id_ultimo_pedido=0;
                                                        } else {
                                                        $datos_ultima=db_fetch_array($busca_ultima);
                                                        $id_ultimo=$datos_ultima["ID"];
                                                        $alt_ultimo="Ultima compra ".date("d/m/Y",strtotime($datos_ultima["fecha_pedido"]));
                                                        $id_ultimo_pedido=$datos_ultima["id_pedido"];
                                                        }

                                                while($referencias_gen=db_fetch_array($busca_productos_de_proveedores)) {
                                                
                                                $muestra_ultimo="";
                                                $es_este=false;
                                                
                                                if ($id_ultimo==0) {
                                                        $id_ultimo=$referencias_gen["ID"];
                                                        }
                                                
                                                if ($referencias_gen["ID"]==$id_ultimo) {
                                                        $muestra_ultimo="<a target='_new' href='/almacen/ver_pedido_proveedor.php?id_pedido=$id_ultimo_pedido' title='$alt_ultimo'>*</a> ";
                                                        $es_este=true;
                                                        }
                                                
// Datos proveedor

$consulta_datos_proveedor=db_query("SELECT * FROM proveedores WHERE ID_PROVEEDOR=".$referencias_gen["PROVEEDOR"]);
if (db_num_rows($consulta_datos_proveedor)!=0) {
        $datos_proveedor=db_fetch_array($consulta_datos_proveedor);
        $nombre_proveedor=$datos_proveedor["nombre_comercial"];
        if(strlen($nombre_proveedor)==0) {
                $nombre_proveedor=$datos_proveedor["RAZON_SOCIAL"];
                }
        } else {
        $nombre_proveedor="Desconocido";
        }
                                                
                                                $contador_filas1++;

                                                $id_articulo_tarifa=$referencias_gen["ID"];
                                                $precio_articulo=$referencias_gen["PRECIO_VENTA"];
                                                $nombre_articulo_tarifa=$referencias_gen["ARTICULO"];
                                                
                                                $unidad_articulo_actual=$referencias_gen["UNIDAD"];
                                                if (is_numeric($unidad_articulo_actual)==false) {
                                                        $unidad_articulo_actual=1;
                                                        }
                                                        
                                                // Comprueba stock actual del articulo
                                                $busca_entradas=db_query_params('SELECT id_articulo, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_pedido_proveedor WHERE id_articulo=? GROUP BY id_articulo;', 's', array($id_articulo_tarifa));
                                                $entradas=db_fetch_array($busca_entradas);
                                                $total_entradas=$entradas["SumaDeCANTIDAD"];
                                                if (strlen($total_entradas)==0) {
                                                        $total_entradas=0;
                                                        }
                                                $total_entradas=$total_entradas*$unidad_articulo_actual;
                                                
                                                // $busca_salidas=mysql_query("SELECT id_material, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_entrega WHERE id_material=$id_articulo_tarifa GROUP BY id_material;");
                                                $busca_salidas=db_query_params('SELECT id_producto_tarifa, Sum(cantidad) AS SumaDeCANTIDAD FROM menu_productos_consumo_cocina WHERE id_producto_tarifa=? GROUP BY id_producto_tarifa;', 's', array($id_articulo_tarifa));
                                                $salidas=db_fetch_array($busca_salidas);
                                                $total_salidas=$salidas["SumaDeCANTIDAD"];
                                                if (strlen($total_salidas)==0) {
                                                        $total_salidas=0;
                                                        }

                                                $stock_articulo=$total_entradas-$total_salidas;
                                                // echo $id_articulo_tarifa." E: ".$total_entradas." S: ".$total_salidas." ST: ".$stock_articulo."<br>";
                                                if ($stock_articulo<=0) {
                                                        // $stock_articulo=$stock_articulo;
                                                        // Si es <0 ponemos 0
                                                        $stock_articulo=0;
                                                        }

                                                $detalle_articulo=$nombre_articulo_tarifa."\nUnidades: ".$referencias_gen["UNIDAD"]."\nPrecio coste: ".number_format($referencias_gen["PRECIO_COSTE"],2,",",".")." €";
                                                
                                                $cantidad_servida=0;
                                                $cantidad_resultante_guardada=0;
                                                $unidades_guardadas=0;
                                                
                                                $busca_cantidades_servidas=db_query_params('SELECT * FROM menu_productos_proveedor WHERE id_lista=? AND id_producto_tarifa=?', 'ss', array($id_lista, $id_articulo_tarifa));
                                                if (db_num_rows($busca_cantidades_servidas)!=0) {
                                                        $cantidades_servidas=db_fetch_array($busca_cantidades_servidas);
                                                        $cantidad_servida=$cantidades_servidas["cantidad"];
                                                        // $stock_guardado=$cantidades_servidas["stock"];
                                                        $cantidad_resultante_guardada=$cantidades_servidas["cantidad_resultante"];
                                                        $unidades_guardadas=$cantidades_servidas["unidades"];
                                                        }
                                                $stock_guardado=$stock_articulo;
                                               if ((db_num_rows($busca_cantidades_servidas)==0) and (($solo1==true) or ($es_este==true))) {
                                                                        $cantidad_servida=$listado_alimentos["cantidad_redondeada"];

                                                                        $stock_guardado=$stock_articulo;
                                                                        // Comprueba los datos almacenados

                                                                        $cantidad_resultante_guardada=$cantidad_servida-$stock_articulo;
                                                                        if ($cantidad_resultante_guardada<0) {
                                                                                $cantidad_resultante_guardada=0;
                                                                                }
                                                                        if ($unidad_articulo_actual>0) {
                                                                                $unidades_guardadas=$cantidad_resultante_guardada/$unidad_articulo_actual;
                                                                                } else {
                                                                                $unidades_guardadas=$cantidad_resultante_guardada;
                                                                                }
                                                                        }
                                                $color_fila="#ffffff";
                                                if (($solo1==true) or ($es_este==true)) {
                                                                $color_fila="#ffffff";
                                                                } else {
                                                                $color_fila="#ffc478";
                                                                }
                                                ?>
                                                <tr bgcolor="<?=$color_fila;?>" valign="top">
                                                <input type="hidden" name="id_articulo_tarifa[]" value="<?=$id_articulo_tarifa?>">
                                                <input type="hidden" name="unidad_articulo_actual[]" id="unidad_articulo_actual_<?=$contador2;?>" value="<?=$unidad_articulo_actual?>">
                                                <input type="hidden" name="precio_articulo_actual[]" id="precio_articulo_actual_<?=$contador2;?>" value="<?=$precio_articulo?>">
                                                <td align="left" width=30%><font size=1 face="<?= $fuente_general; ?>"><?=$muestra_ultimo?><?=$nombre_proveedor?>. <a href='javascript:seleccionar(<?=$contador1;?>,<?=$contador2;?>);' title="<?=$detalle_articulo;?>"><?=$nombre_articulo_tarifa;?></a></font></td>
                                                <td align="right" width=10%><font size=1 face="<?= $fuente_general; ?>"><?=$unidad_articulo_actual;?></font></td>
                                                <td align="center" width=10%><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=8 name="cantidad_servida[]" id="cantidad_servida_<?=$contador2;?>" value="<?=number_format($cantidad_servida,2,".","");?>" style="font-size: 10px;text-align= right" onblur="recalcular_fila(<?=$contador2?>)"></font></td>
                                                <td align="right" width=10%><font size=1 face="<?= $fuente_general; ?>"><?=number_format($precio_articulo,2,",",".");?> €</font></td>
                                                <td align="right" width=10%><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=8 name="stock_articulo[]" id="stock_articulo_<?=$contador2;?>" value="<?=number_format($stock_guardado,2,".","");?>" style="font-size: 10px;text-align= right" onblur="recalcular_fila(<?=$contador2?>)"><br><input type="text" size=8 name="stock_resultante_articulo[]" id="stock_resultante_articulo<?=$contador2;?>" value="<?=number_format($cantidad_resultante_guardada,2,".","");?>" style="font-size: 10px;text-align= right" readonly()></font></td>
                                                <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=8 name="cantidad_servida_uds[]" id="cantidad_servida_uds_<?=$contador2;?>" value="<?=number_format($unidades_guardadas,2,".","");?>" style="font-size: 10px;text-align= right" onblur="recalcular_fila_uds(<?=$contador2?>)"></font></td>
                                                <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=10 name="total_cantidad_servida[]" id="total_cantidad_servida_<?=$contador2;?>" value="<?=number_format($unidades_guardadas*$precio_articulo,2,".","");?>" style="font-size: 10px;text-align= right" readonly></font></td>
                                                </tr>
                                                <?php 
                                                $total_pedido=$total_pedido+round(($unidades_guardadas*$precio_articulo),2);
                                                $contador2++;
                                                }
                                                ?>
                                                </table>
        <?php 
        }
        ?></td>
</tr>
<?php 
$contador1++;
}
?>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="right" colspan=8 bgcolor="<?php  echo $fondo_at; ?>"><font size=2><b>Total pedido:</b></font></td>
    <td align="right" colspan=2 bgcolor="#ffffff"><font size=2><input type="text" size=10 name="total_pedido" id="total_pedido" style="font-size: 10px" value="<?=$total_pedido;?>"> €</font></td>
</tr>
</table>
</form>
<script language="Javascript">

function recalcular_totales() {
        total_pedido=0;

        for(contador=0;contador<<?=$contador2?>;contador++) {
                total_pedido=total_pedido+Number((Math.round(100*(document.getElementById('total_cantidad_servida_'+contador).value))/100));
                }

        document.getElementById('total_pedido').value=Math.round(100*(total_pedido))/100;
        }

function recalcular_fila(destino) {

        cantidad=document.getElementById('cantidad_servida_'+destino).value;
        unidad=document.getElementById('unidad_articulo_actual_'+destino).value;
        precio=document.getElementById('precio_articulo_actual_'+destino).value;
        stock=document.getElementById('stock_articulo_'+destino).value;
        cantidad_resultante=cantidad-stock;
        if (cantidad_resultante<0) {
                cantidad_resultante=0;
                }
        
        // recalcula la fila
        if (isNaN(cantidad_resultante/unidad)) {
        // Nada
        } else {
        document.getElementById('cantidad_servida_uds_'+destino).value=Math.round(100*(cantidad_resultante/unidad))/100;
        document.getElementById('stock_resultante_articulo'+destino).value=Math.round(100*cantidad_resultante)/100;
        document.getElementById('total_cantidad_servida_'+destino).value=Math.round(100*((Math.round(100*(cantidad_resultante/unidad))/100)*precio))/100;
        }

        recalcular_totales();
        }

function recalcular_fila_uds(destino) {

        cantidad=document.getElementById('cantidad_servida_uds_'+destino).value;
        unidad=document.getElementById('unidad_articulo_actual_'+destino).value;
        precio=document.getElementById('precio_articulo_actual_'+destino).value;

        // recalcula la fila
        if (isNaN(cantidad*unidad)) {
        // Nada
        } else {
        document.getElementById('stock_resultante_articulo'+destino).value=Math.round(100*(cantidad*unidad))/100;
        document.getElementById('total_cantidad_servida_'+destino).value=Math.round(100*(cantidad*precio))/100;
        }
        recalcular_totales();
        }

function seleccionar(origen,destino) {
        document.getElementById('cantidad_servida_'+destino).value=document.getElementById('cantidad_redondeada_'+origen).value;
        
        cantidad=document.getElementById('cantidad_servida_'+destino).value;
        unidad=document.getElementById('unidad_articulo_actual_'+destino).value;
        precio=document.getElementById('precio_articulo_actual_'+destino).value;
        stock=document.getElementById('stock_articulo_'+destino).value;
        cantidad_resultante=cantidad-stock;
        if (cantidad_resultante<0) {
                cantidad_resultante=0;
                }

        // recalcula la fila
        if (isNaN(cantidad_resultante/unidad)) {
        // Nada
        } else {
        document.getElementById('cantidad_servida_uds_'+destino).value=Math.round(100*(cantidad_resultante/unidad))/100;
        document.getElementById('stock_resultante_articulo'+destino).value=Math.round(100*cantidad_resultante)/100;
        document.getElementById('total_cantidad_servida_'+destino).value=Math.round(100*((Math.round(100*(cantidad_resultante/unidad))/100)*precio))/100;
        }
        
        recalcular_totales();
        }

function guardar_cambios(accion) {
        if (accion==1) {
                self.document.lista_compra.action="modificar_lista.php";
                }
        if (accion==2) {
                self.document.lista_compra.action="generar_pedidos.php";
                }
        self.document.lista_compra.submit();
        }

function imprimir() {
        self.document.lista_compra.target="_new";
        self.document.lista_compra.action="imprimir_lista_compra.php?g=1";
        self.document.lista_compra.submit();
        }
</script>
</center></div>
</div>
</body>
</html>
