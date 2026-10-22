<?php  require("../configuracion/parte_superior_privado.php");

$id_pedido_empresa=$_GET["id_pedido"];

$busca_datos_pedido=db_query_params('SELECT * FROM pedidos_material_empresa WHERE id_pedido_empresa=?', 's', array($id_pedido_empresa));
$datos_pedido=db_fetch_array($busca_datos_pedido);

// Proveedores
$array_proveedores=array();
$array_proveedores_actual = array();

$busca_proveedores=db_query("SELECT * FROM gestionguarderias.proveedores ORDER BY ID_PROVEEDOR");

while($lista_proveedores=db_fetch_array($busca_proveedores)) {
        $array_proveedores[$lista_proveedores["ID_PROVEEDOR"]]=$lista_proveedores["RAZON_SOCIAL"];
        }

// Busca pedidos penientes de servir

$busca_genericos=db_query("SELECT ARTICULO_GENERICO FROM gestionguarderias.tarifa GROUP BY ARTICULO_GENERICO ORDER BY ARTICULO_GENERICO");

if (db_num_rows($busca_genericos)!=0) {

$busca_nombres_centros=db_query("SELECT * FROM centros ORDER BY id_centro");

$nombres_centros=array();

while($datos_centros=db_fetch_array($busca_nombres_centros)) {
        $nombres_centros[$datos_centros["id_centro"]]=$datos_centros["nombre_centro"];
        }

?>
<input type="button" value="Añadir artículo" onclick="mas_articulos()"><br>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                <form name="pedidos_pendientes" action="guarda_cambios_pedido_empresa.php" method="POST">
                <input type="hidden" name="id_pedido" value="<?=$id_pedido_empresa?>">
                <input type="hidden" name="pedidos" value="0">
                <input type="hidden" name="retorno" value="0">
                        <tr valign="top">
                                <td width=100% align="center" colspan=5><br>
                                <font size=2 face="<?= $fuente_general; ?>"><b>Pedido Nº <?=$id_pedido_empresa?>/<?=date("Y",strtotime($datos_pedido["fecha_pedido"]))?></b></font><br><br></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br>
                                <table cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width="100%">
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="left" width="30%" rowspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Articulo Genérico</b></font></td>
                                                <td align="center" width="15%" rowspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad pedidos</b></font></td>
                                                <td align="left" width="40%" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Artículo</b></font></td>
                                        </tr>
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="40%" colspan=2><table cellpadding=1 cellspacing=1 border=0 width=100%>
        <tr>
    <td align="center" width="40%"><font size=1><b>Descripción</b></font></td>
    <td align="center" width="20%"><font size=1><b>Cantidad</b></font></td>
    <td align="center" width="20%"><font size=1><b>Stock<br>C. Compra</b></font></td>
    <td align="center" width="20%"><font size=1><b>Precio</b></font></td>
  </tr>
                                                </table></td>
                                        </tr>
                                <?php 

                                $contador_filas=0;
                                $contador1=0;
                                $contador2=0;
                                $nombre_articulo="";

                                while($datos_genericos=db_fetch_array($busca_genericos)) {

                                $nombre_generico_actual=$datos_genericos["ARTICULO_GENERICO"];

                                $busca_detalle_pedido_empresa=db_query_params('SELECT * FROM detalles_pedido_empresa WHERE id_pedido_empresa=? AND generico=?', 'ss', array($id_pedido_empresa, (string)($nombre_generico_actual)));
                                
                                $busca_pedidos_pendientes=db_query_params('SELECT detalles_pedido_material.generico_solicitado, sum(detalles_pedido_material.cantidad) AS suma_total FROM detalles_pedido_material,pedidos_material_empresa WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido_empresa=pedidos_material_empresa.id_pedido_empresa AND detalles_pedido_material.id_pedido_empresa=? AND detalles_pedido_material.generico_solicitado=? GROUP BY detalles_pedido_material.generico_solicitado', 'ss', array($id_pedido_empresa, (string)($nombre_generico_actual))) or die(db_error()." -1");
                                
                                if (db_num_rows($busca_pedidos_pendientes)!=0) {
                                        $datos_detalle=db_fetch_array($busca_pedidos_pendientes);
                                        $suma_total=$datos_detalle["suma_total"];
                                        } else {
                                        $suma_total=0;
                                        }

                                if ((db_num_rows($busca_pedidos_pendientes)!=0) or (db_num_rows($busca_detalle_pedido_empresa)!=0)) {
                                        
                                $contador_filas++;

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left" width="30%"><font size=2 face="<?= $fuente_general; ?>"><?=$nombre_generico_actual;?></font></td>
                                        <td align="right" width="15%"><font size=2 face="<?= $fuente_general; ?>"><?=$suma_total;?></font></td>
                                        <td align="left" width="40%" colspan=2><font size=2 face="<?= $fuente_general; ?>">
                                        <table cellpadding=1 cellspacing=1 border=0 width=100%>
                                        <?php 
                                        
                                        if (db_num_rows($busca_detalle_pedido_empresa)!=0) {
                                                $datos_detalle_pedido_empresa=db_fetch_array($busca_detalle_pedido_empresa);
                                                $id_detalle_pedido_seleccionado=$datos_detalle_pedido_empresa["id_articulo"];
                                                $id_articulo_seleccionado=$datos_detalle_pedido_empresa["id_tarifa"];
                                                $cantidad_articulo_seleccionado=$datos_detalle_pedido_empresa["cantidad"];
                                                } else {
                                                $busca_generico_en_pedido_centro=db_query_params('SELECT * FROM detalles_pedido_material WHERE id_pedido_empresa=? AND generico_solicitado=?', 'ss', array($id_pedido_empresa, (string)($nombre_generico_actual)));
                                                if (db_num_rows($busca_generico_en_pedido_centro)!=0) {
                                                        $inserta_detalle_pedido_empresa=db_query_params('INSERT INTO detalles_pedido_empresa (id_pedido_empresa,generico,id_tarifa,cantidad) VALUES (?,?,0,0);', 'ss', array($id_pedido_empresa, (string)($nombre_generico_actual))) or die(db_error()."-2");
                                                        $id_detalle_pedido_seleccionado=db_insert_id();
                                                        $id_articulo_seleccionado=0;
                                                        $cantidad_articulo_seleccionado=$datos_detalle["suma_total"];
                                                        }
                                                }
                                                
                                        $busca_referencias_gen=db_query_params('SELECT * FROM gestionguarderias.tarifa WHERE ARTICULO_GENERICO=?', 's', array((string)($nombre_generico_actual)));
                                        
                                        if (db_num_rows($busca_referencias_gen)==1) {
                                                        $solo1=true;
                                                        } else {
                                                        $solo1=false;
                                                        }
                                        
                                        $es_este=false;
                                        $comando_borrar="";
                                        
                                        while($referencias_gen=db_fetch_array($busca_referencias_gen)) {
                                                $id_articulo_tarifa=$referencias_gen["ID"];
                                                $precio_articulo=$referencias_gen["PRECIO_VENTA"];
                                                $nombre_articulo_tarifa=$array_proveedores[$referencias_gen["PROVEEDOR"]]." - ".$referencias_gen["ARTICULO"];
                                                                                                $detalle_articulo=$nombre_articulo_tarifa."\nUnidades: ".$referencias_gen["UNIDAD"]."\nPrecio coste: ".number_format($referencias_gen["PRECIO_COSTE"],2,",",".")." €";
                                                $color_fila="#ffffff";
                                                if (($solo1==true) or ($es_este==true)) {
                                                                $color_fila="#ffffff";
                                                                } else {
                                                                $color_fila="#ffc478";
                                                                }
                                                                
                                                $cantidad_servida=0;
                                                $cantidad_resultante_guardada=0;
                                                $unidades_guardadas=0;

                                                $busca_cantidades_servidas=db_query_params('SELECT * FROM detalles_pedido_empresa_extendido WHERE id_articulo_pedido=? AND id_tarifa=?', 'ss', array($id_detalle_pedido_seleccionado, $id_articulo_tarifa));
                                                if (db_num_rows($busca_cantidades_servidas)!=0) {
                                                        $cantidades_servidas=db_fetch_array($busca_cantidades_servidas);
                                                        $cantidad_servida=$cantidades_servidas["cantidad_pedido"];
                                                        $stock_articulo=$cantidades_servidas["stock_articulo"];
                                                        $cantidad_resultante=$cantidad_servida-$stock_articulo;
                                                        if ($cantidad_resultante<0) {
                                                                $cantidad_resultante=0;
                                                                }
                                                        $id_extendido=$cantidades_servidas["id_extendido"];
                                                        } else {
                                                        if ($solo1==true) {
                                                                $cantidad_servida=$cantidad_articulo_seleccionado;
                                                                } else {
                                                                $cantidad_servida=0;
                                                                }
                                                        $stock_articulo=0;
                                                        $cantidad_resultante=$cantidad_servida;
                                                        $inserta=db_query_params('INSERT INTO detalles_pedido_empresa_extendido (id_tarifa,cantidad_pedido,id_articulo_pedido) VALUES (?,?,?)', 'sss', array((string)($id_articulo_tarifa), (string)($cantidad_servida), (string)($id_detalle_pedido_seleccionado)));
                                                        $id_extendido=db_insert_id();
                                                        }
                                                        
                                        $comando_borrar=$comando_borrar."document.getElementById('cantidad_servida_".$contador2."').value='0.00';";
                                         
                                        $array_proveedores_actual[$referencias_gen["PROVEEDOR"]] = $array_proveedores[$referencias_gen["PROVEEDOR"]];

                                        ?>
                                                <tr bgcolor="<?=$color_fila;?>" valign="top">
                                                <input type="hidden" name="id_articulo_tarifa[]" value="<?=$id_articulo_tarifa?>">
                                                <input type="hidden" name="id_extendido[]" value="<?=$id_extendido?>">
                                                <input type="hidden" name="cantidad_inicial_fila[]" id="cantidad_inicial_fila_<?=$contador2;?>" value="<?=number_format($suma_total,2,".","")?>">
                                                <input type="hidden" name="precio_articulo_actual[]" id="precio_articulo_actual_<?=$contador2;?>" value="<?=$precio_articulo?>">
                                                <td align="left" width=40%><font size=1 face="<?= $fuente_general; ?>"><?=$muestra_ultimo?><?=$nombre_proveedor?>. <a href='javascript:seleccionar(<?=$contador2;?>,<?=$contador1;?>);' title="<?=$detalle_articulo;?>"><?=$nombre_articulo_tarifa;?></a></font></td>
                                                <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>"><input type="text" size=8 name="cantidad_servida[]" id="cantidad_servida_<?=$contador2;?>" value="<?=number_format($cantidad_servida,2,".","");?>" style="font-size: 10px;text-align= right" onblur="recalcular_fila(<?=$contador2?>)"></font></td>
                                                <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>">
                                                <input type="text" size=8 name="stock_articulo[]" id="stock_articulo_<?=$contador2;?>" value="<?=number_format($stock_articulo,2,".","");?>" style="font-size: 10px;text-align= right" onblur="recalcular_fila(<?=$contador2?>)"><br>
                                                <input type="text" size=8 name="cantidad_resultante[]" id="cantidad_resultante_<?=$contador2;?>" value="<?=number_format($cantidad_resultante,2,".","");?>" style="font-size: 10px;text-align= right"></font></td>
                                                <td align="right" width=20%><font size=1 face="<?= $fuente_general; ?>"><?=number_format($precio_articulo,2,",",".");?> €</font></td>
                                                </tr>
                                        <?php 
                                        $contador2++;
                                        }
                                        ?>
                                        <input type="hidden" name="comando_borrar_[]" id="comando_borrar_<?=$contador1;?>" value="<?=$comando_borrar?>">
                                        </table></td>
                                </tr>
                                <?php 
                                if (db_num_rows($busca_pedidos_pendientes)!=0) {
                                ?>
                                <tr>
                                <td colspan=5>
                                <table bordercolor="#000000" style="border-collapse: collapse" width=100% border=1 cellpadding=1 cellspacing=0 id="capa_detalles_o_<?=$contador_filas?>" style="display: block">
                                <tr bgcolor="#efefef">
                                        <td><a href='javascript: mostrar("<?=$contador_filas?>")'><img src="/imagen/menu/mas.gif" border=0></a></td>
                                </tr>
                                </table>
                                <table bordercolor="#000000" style="border-collapse: collapse" width=100% border=1 cellpadding=1 cellspacing=0 id="capa_detalles_m_<?=$contador_filas?>" style="display: none">
                                        <tr bgcolor="#efefef" valign="top">
                                        <td align="left" colspan=5><font size=2 face="<?= $fuente_general; ?>"><a href='javascript: ocultar("<?=$contador_filas?>")'><img src="/imagen/menu/menos.gif" border=0></a></font></td>
                                        </tr>

                                <?php 
                                
                                // Detalle aulas
                                // $busca_material_aulas=mysql_query("SELECT * FROM detalles_pedido_material,pedidos_material_empresa WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido_empresa=pedidos_material_empresa.id_pedido_empresa AND detalles_pedido_material.id_pedido_empresa=$id_pedido_empresa AND detalles_pedido_material.generico_solicitado='".$datos_detalle["generico_solicitado"]."'") or die(mysql_error()." -1");
                                $busca_material_aulas=db_query_params('SELECT * FROM detalles_pedido_material,pedidos_material,pedidos_material_empresa,centro_aulas WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND detalles_pedido_material.id_pedido_empresa=pedidos_material_empresa.id_pedido_empresa AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_pedido_empresa=? AND detalles_pedido_material.generico_solicitado=? ORDER BY centro_aulas.id_centro, centro_aulas.nombre', 'ss', array($id_pedido_empresa, (string)($datos_detalle["generico_solicitado"]))) or die(db_error());
                                
                                $centro_actual_filas=0;
                                $total_centro_actual=0;
                                
                                while($material_aulas=db_fetch_array($busca_material_aulas)) {
                                

                                if ($centro_actual_filas!=$material_aulas["id_centro"]) {
                                        $centro_actual_filas=$material_aulas["id_centro"];
                                        ?>
                                        <tr bgcolor="#efefef" valign="top">
                                        <td align="left" colspan=5><font size=2 face="<?= $fuente_general; ?>"><?=$nombres_centros[$centro_actual_filas];?></font></td>
                                        </tr>
                                        <tr bgcolor="#efefef" valign="top">
                                        <td align="left" width="45%"><font size=2 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
                                        <td align="right" width="15%"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Pedido</b></font></td>
                                        <td align="right" width="15%"><font size=2 face="<?= $fuente_general; ?>"><b>Existencias Aula</b></font></td>
                                        <td align="right" width="15%"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad pedido</b></font></td>
                                        <td align="center" width=10%><font size=2 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                        </tr>
                                        <?php 
                                        }
                                
                                $busca_existencias_articulo=db_query_params('SELECT * FROM existencias_aulas WHERE id_aula=? AND nombre_generico=?', 'ss', array($material_aulas["id_aula"], (string)($datos_detalle["generico_solicitado"])));
                                if (db_num_rows($busca_existencias_articulo)==0) {
                                        $existencias_articulo=number_format(0,2,",",".");
                                        } else {
                                        $datos_existencias_articulo=db_fetch_array($busca_existencias_articulo);
                                        $existencias_articulo=number_format($datos_existencias_articulo["stock_actual"],2,",",".");
                                        }
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$material_aulas["nombre"];?></font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($material_aulas["fecha_pedido"]));?></font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$existencias_articulo;?></font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($material_aulas["cantidad"],2,",",".");?></font></td>
                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar_pedido_aula('<?=$material_aulas["id_pedido"];?>')"><img src="editar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                        }
                                ?>
                                </table>
                                </td>
                                </tr>
                                <?php 
                                                }
                                        }
                                        $contador1++;
                                }
                                
                                // Busca observaciones
                                $busca_lista_observaciones=db_query_params('SELECT *,pedidos_material.observaciones AS observaciones_pedido FROM detalles_pedido_material,pedidos_material,pedidos_material_empresa,centro_aulas WHERE detalles_pedido_material.cantidad>0 AND detalles_pedido_material.id_pedido=pedidos_material.id_pedido AND detalles_pedido_material.id_pedido_empresa=pedidos_material_empresa.id_pedido_empresa AND pedidos_material.id_aula=centro_aulas.id_aula AND detalles_pedido_material.id_pedido_empresa=? GROUP BY detalles_pedido_material.id_pedido ORDER BY centro_aulas.id_centro, centro_aulas.nombre', 's', array($id_pedido_empresa)) or die(db_error());
                                ?>
                                <tr>
                                        <td colspan=5 bgcolor="#efefef">
                                        <font size=2><b>Peticiones recibidas desde observaciones:</b></font>
                                        </td>
                                </tr>
                                <?php 
                                $centro_listado_actual=0;
                                while($lista_observaciones=db_fetch_array($busca_lista_observaciones)) {
                                
                                        if ($centro_listado_actual!=$lista_observaciones["id_centro"]) {
                                                $centro_listado_actual=$lista_observaciones["id_centro"];
                                                $busca_datos_centro_listado=db_query("SELECT * FROM centros WHERE id_centro=$centro_listado_actual");
                                                if (db_num_rows($busca_datos_centro_listado)!=0) {
                                                        $datos_centro_listado=db_fetch_array($busca_datos_centro_listado);
                                                        $nombre_centro_listado=$datos_centro_listado["nombre_centro"];
                                                        ?>
                                        <tr>
                                        <td colspan=5>
                                        <font size=2><b>Escuela: <?=$nombre_centro_listado?></b>
                                        </td>
                                        </tr>
                                                        <?php 
                                                        } else {
                                                        $nombre_centro_listado="";
                                                        }
                                                }
                                
                                        if (strlen($lista_observaciones["observaciones_pedido"])!=0) {
                                        ?>
                                        <tr>
                                        <td colspan=5>
                                        <font size=2><b>Aula <?=$lista_observaciones["nombre"]?></b><br>
                                        <?=$lista_observaciones["observaciones_pedido"];?></font>
                                        </td>
                                        </tr>
                                        <?php 
                                                }
                                        }
                                
                                $observaciones_pedido=$datos_pedido["observaciones"];
                                if (strlen($observaciones_pedido)!=0) {
                                        $observaciones_pedido=str_replace(chr(92),"",$observaciones_pedido);
                                        }
                                ?>
                                <tr>
                                        <td  colspan=5 bgcolor="#efefef">
                                        <font size=2><b>Observaciones:</b></font>
                                        </td>
                                </tr>
                                <tr>
                                        <td  colspan=5><font size=1 face="<?= $fuente_general; ?>"><textarea  name="observaciones" cols=140 rows=8 style="font-size: 12px; font-family: Arial"><?=$observaciones_pedido;?></textarea></font></td>
                                </tr>
                                </table>
                                <br><input type="button" onclick="validar_pedidos(0)" style="font-size: 10px" value="Guardar cambios"> <input type="button" onclick="validar_pedidos(1)" style="font-size: 10px" value="Guardar cambios y generar pedidos proveedores"><br /><br />
                                <select name="id_proveedor" id="id_proveedor">
                                        <option value="">Todos los proveedores</option>
                                        <?php
                                        foreach ($array_proveedores_actual as $idProveedor => $nombreProveedor) {
                                                ?>
                                                <option value="<?=$idProveedor?>"><?=$nombreProveedor?></option>
                                                <?php
                                        }
                                        ?>
                                </select> <input type="button" onclick="imprimir()" value="Imprimir proveedor seleccionado" style="font-size: 10px"> <input type="button" onclick="imprimir2()" value="Imprimir proveedor seleccionado (escuelas separadas)" style="font-size: 10px"><br /><br />
                                <!--<input type="button" onclick="imprimir2_pruebas()" value="PRUEBAS Imprimir proveedor seleccionado (escuelas separadas)" style="font-size: 10px"><br /><br />-->
                                <input type="button" onclick="window.location.href='calendario_pedidos_empresa.php?dia_sel=<?=strtotime($datos_pedido["fecha_pedido"])?>'" style="font-size: 10px" value="Salir"><br><br>
                                </td>
                        </tr>
                        </tr>
                        </form>
                </table>
<script language="Javascript">

 function validar_pedidos(accion_despues) {
        self.document.pedidos_pendientes.pedidos.value=accion_despues;
        self.document.pedidos_pendientes.action="guarda_cambios_pedido_empresa.php";
        self.document.pedidos_pendientes.submit();
        }

function mostrar(id_capa) {
        document.getElementById('capa_detalles_o_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_m_'+id_capa).style.display='block';
        }

function ocultar(id_capa) {
        document.getElementById('capa_detalles_m_'+id_capa).style.display='none';
        document.getElementById('capa_detalles_o_'+id_capa).style.display='block';
        }
        
function editar_pedido_aula(id_pedido) {
        window.open('editar_pedido_aula.php?id_pedido='+id_pedido,'pedido','toolbar=yes,resizable=yes,width=800,height=600');
        }
function imprimir() {
        self.document.pedidos_pendientes.target="_new";
        self.document.pedidos_pendientes.action="imprimir_lista_compra.php";
        self.document.pedidos_pendientes.submit();
        }
function imprimir2() {
        self.document.pedidos_pendientes.target="_new";
        self.document.pedidos_pendientes.action="imprimir_lista_compra_separada.php";
        self.document.pedidos_pendientes.submit();
        }   
function imprimir2_pruebas() {
        self.document.pedidos_pendientes.target="_new";
        self.document.pedidos_pendientes.action="imprimir_lista_compra_separada_pruebas.php";
        self.document.pedidos_pendientes.submit();
        }               
function mas_articulos() {
        window.open('nuevos_articulos.php?id_pedido=<?=$id_pedido_empresa?>','nuevo_articulo','width=550,height=450')
        self.document.pedidos_pendientes.retorno.value=1;
        self.document.pedidos_pendientes.submit();
        }

function recalcular_fila(destino) {

        cantidad=document.getElementById('cantidad_servida_'+destino).value;
        stock=document.getElementById('stock_articulo_'+destino).value;
        resultante=document.getElementById('cantidad_resultante_'+destino).value;

        // recalcula la fila
        if ((isNaN(cantidad))||(isNaN(stock))||(isNaN(resultante))) {
        // Nada
        } else {
        total_resultante=Number(cantidad)-Number(stock);
        if (total_resultante<0) {
                total_resultante=0;
                }
        // if (total_resultante<resultante) {
        //         total_resultante=resultante;
        //        }
        document.getElementById('cantidad_resultante_'+destino).value=Math.round(100*(total_resultante))/100;
        }

        }

function seleccionar(destino,borrar) {
        comando_borrar=document.getElementById('comando_borrar_'+borrar).value;
        
        eval(comando_borrar);
        
        document.getElementById('cantidad_servida_'+destino).value=document.getElementById('cantidad_inicial_fila_'+destino).value;

        recalcular_fila(destino);
         
        }

</script>
<?php 
} else {
?>
<p align="center"><br><br>No hay pedidos nuevos</p><br>
<p align="center"><input type="button" onclick="window.location.href='calendario_pedidos_empresa.php'" style="font-size: 10px" value="Ver otros pedidos"> <input type="button" onclick="window.location.href='/index.php'" style="font-size: 10px" value="Salir"></p>
<?php 
}
require("../configuracion/parte_inferior.php"); ?>
