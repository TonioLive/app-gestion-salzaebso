<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_lista=$_GET["id_lista"];

$busca_datos_lista=db_query("SELECT * FROM menu_lista_compra WHERE id_lista=$id_lista") or die(db_error());

$datos_lista=db_fetch_array($busca_datos_lista);

$texto_observaciones=$datos_lista["observaciones"];

if (strlen($texto_observaciones)!=0) {
        $texto_observaciones=app_url_text_utf8($texto_observaciones);
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

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center><font size=2><br><br><b>Lista de la compra para <?=$texto_encabezado;?></b></font>
<div id="botonera" style="display:block;padding:0px;border:0px solid"><br>
<input type="button" value="Guardar cambios" style="font-size: 10px" onclick="guardar_cambios()"> <input type="button" onclick="imprimir()" value="Imprimir" style="font-size: 10px"> <input type="button" onclick="window.close()" value="Salir" style="font-size: 10px"><br><br>
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
    <td align="center" width="15%"><font size=1><b>Total</b></font></td>
  </tr>
<?php 
$total_pedido=0;
$contador1=0;
$contador2=0;
$unidad_medida[0]="g.&nbsp;&nbsp;";
$unidad_medida[1]="ml.";
$busca_alimentos=db_query("SELECT * FROM menu_productos_lista,menu_alimentos WHERE menu_productos_lista.id_alimento=menu_alimentos.id_alimento AND menu_productos_lista.id_lista=$id_lista ORDER BY menu_alimentos.nombre_alimento");
while($listado_alimentos=db_fetch_array($busca_alimentos)) {
?>
<tr>
        <input type="hidden" name="id_alimento[]" value="<?=$listado_alimentos["id_alimento"];?>">
        <input type="hidden" name="id_lista_alimento[]" value="<?=$listado_alimentos["id_producto"];?>">
        <td bgcolor="#efefef"><font size=2><?=app_url_text_utf8($listado_alimentos["nombre_alimento"]);?></font></td>
        <td align="right"><font size=1><?=number_format($listado_alimentos["cantidad_recomendada"],2,",",".");?> <?=$unidad_medida[$listado_alimentos["unidad_medida"]]?></font></td>
        <td align="right"><input type="text" size=10 name="cantidad_redondeada[]" id="cantidad_redondeada_<?=$contador1;?>" value="<?=number_format($listado_alimentos["cantidad_redondeada"],2,".","");?>" style="font-size: 10px; text-align: right"> <?=$unidad_medida[$listado_alimentos["unidad_medida"]]?></td>
        <td align="center" colspan=7><?php 
        $busca_productos_de_proveedores=db_query("SELECT * FROM tarifa WHERE ID_ALIMENTO_GENERICO=".$listado_alimentos["id_alimento"]);
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
                                                        
                                                while($referencias_gen=db_fetch_array($busca_productos_de_proveedores)) {
                                                $contador_filas1++;

                                                $id_articulo_tarifa=$referencias_gen["ID"];
                                                $precio_articulo=$referencias_gen["PRECIO_VENTA"];
                                                $nombre_articulo_tarifa=$referencias_gen["ARTICULO"];

                                                // Comprueba stock actual del articulo
                                                $busca_entradas=db_query("SELECT id_articulo, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_proveedor WHERE id_articulo=$id_articulo_tarifa GROUP BY id_articulo;");
                                                $entradas=db_fetch_array($busca_entradas);
                                                $total_entradas=$entradas["SumaDeCANTIDAD"];

                                                $busca_salidas=db_query("SELECT id_material, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_entrega WHERE id_material=$id_articulo_tarifa GROUP BY id_material;");
                                                $salidas=db_fetch_array($busca_salidas);
                                                $total_salidas=$salidas["SumaDeCANTIDAD"];

                                                $stock_articulo=$total_entradas-$total_salidas;

                                                if ($stock_articulo<=0) {
                                                        $stock_articulo="<font color=red>$stock_articulo</font>";
                                                        }

                                                $detalle_articulo=$nombre_articulo_tarifa."\nUnidades: ".$referencias_gen["UNIDAD"]."\nPrecio coste: ".number_format($referencias_gen["PRECIO_COSTE"],2,",",".")." €";
                                                $cantidad_servida=0;
                                                
                                                $busca_cantidades_servidas=db_query("SELECT * FROM menu_productos_proveedor WHERE id_lista=$id_lista AND id_producto_tarifa=$id_articulo_tarifa");
                                                if (db_num_rows($busca_cantidades_servidas)!=0) {
                                                        $cantidades_servidas=db_fetch_array($busca_cantidades_servidas);
                                                        $cantidad_servida=$cantidades_servidas["cantidad"];
                                                        $stock_guardado=$cantidades_servidas["stock"];
                                                        $cantidad_resultante_guardada=$cantidades_servidas["cantidad_resultante"];
                                                        $unidades_guardadas=$cantidades_servidas["unidades"];
                                                        }

                                                if ($cantidad_servida==0) {
                                                        if ($solo1==true) {
                                                                $cantidad_servida=$listado_alimentos["cantidad_redondeada"];
                                                                $color_fila="#ffffff";
                                                                
                                                                $stock_guardado=$stock_articulo;
                                                                // Comprueba los datos almacenados

                                                                $cantidad_resultante_guardada=$cantidad_servida-$stock_articulo;
                                                                if ($cantidad_resultante_guardada<0) {
                                                                        $cantidad_resultante_guardada=0;
                                                                        }
                                                                $unidades_guardadas=$cantidad_resultante_guardada/$unidad_articulo_actual;
                                                        
                                                                } else {
                                                                $color_fila="#ffc478";
                                                                $cantidad_servida=0;
                                                                $stock_guardado=0;
                                                                $cantidad_resultante_guardada=0;
                                                                $unidades_guardadas=0;
                                                                }
                                                        }
                                                $unidad_articulo_actual=$referencias_gen["UNIDAD"];
                                                if (is_numeric($unidad_articulo_actual)==false) {
                                                        $unidad_articulo_actual=1;
                                                        }
                                                ?>
                                                <tr bgcolor="<?=$color_fila;?>" valign="top">
                                                <input type="hidden" name="id_articulo_tarifa[]" value="<?=$id_articulo_tarifa?>">
                                                <input type="hidden" name="unidad_articulo_actual[]" id="unidad_articulo_actual_<?=$contador2;?>" value="<?=$unidad_articulo_actual?>">
                                                <input type="hidden" name="precio_articulo_actual[]" id="precio_articulo_actual_<?=$contador2;?>" value="<?=$precio_articulo?>">
                                                <td align="left" width=30%><font size=1 face="<?= $fuente_general; ?>"><a href='javascript:seleccionar(<?=$contador1;?>,<?=$contador2;?>);' title="<?=$detalle_articulo;?>"><?=$nombre_articulo_tarifa;?></a></font></td>
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

function guardar_cambios() {
        self.document.lista_compra.submit();
        }

function imprimir() {
        document.getElementById('botonera').style.display='none';
        document.getElementById('observaciones1').style.display='none';
        texto_observaciones=escape(self.document.lista_compra.observaciones.value);
        texto_observaciones=texto_observaciones.replace(/%0D%0A/g,"<BR>");
        document.getElementById('observaciones2').innerHTML='<table border=1 width=100% style="border-collapse: collapse" bordercolor="#000000"><tr valign="top"><td align="right" width=25% bgcolor="<?php  echo $fondo_at; ?>"><font size=2><b>Observaciones</b></font></td><td align="left" bgcolor="#ffffff"><font size=2>'+texto_observaciones+'</font></td></tr></table>';
        document.getElementById('observaciones2').style.display='block';
        window.print();
        document.getElementById('botonera').style.display='block';
        document.getElementById('observaciones2').style.display='none';
        document.getElementById('observaciones1').style.display='block';
        }
</script>
</center></div>
</div>
</body>
</html>
