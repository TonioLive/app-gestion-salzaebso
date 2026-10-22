<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$unidad_medida[0]="g.&nbsp;&nbsp;";
$unidad_medida[1]="ml.";

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $id_curso_actual=$curso_seleccionado;
        }

                        $familia=$_GET["familia"];

                        $where_de_consulta=" WHERE ";

                        if (strlen($familia)!=0) {
                                $familia=urldecode($familia);
                                $consulta_datos_familia=db_query("SELECT * FROM tipos_iva_servicios WHERE id_tipo_iva=$familia");
                                if (db_num_rows($consulta_datos_familia)!=0) {
                                        $datos_familia=db_fetch_array($consulta_datos_familia);
                                        $nombre_familia=$datos_familia["categoria"];
                                        } else {
                                        $nombre_familia="Desconocida";
                                        }
                                if ($where_de_consulta!=" WHERE ") {
                                        $where_de_consulta=$where_de_consulta."AND ";
                                        }
                                $where_de_consulta=$where_de_consulta."CATEGORIA=".$familia." ";
                                }

                        if ($where_de_consulta==" WHERE ") {
                                $where_de_consulta="";
                                }

?>
<script language="Javascript">
function eliminar(codigo) {
        alert("No se pueden eliminar artículos que ya han sido facturados.");
        /*
        if (confirm("¿Realmente quieres eliminar este artículo?")) {
                window.location.href="eliminar_articulo.php?codigo="+codigo+"&familia=<?=urlencode($familia);?>";
                }
        */
        }
</script>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="familia" value="<?=$familia?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Artículos de la familia <i><?=$nombre_familia?></i><br></b></font><br>
                         <input style="font-size: 10px" type="button" value="Nuevo artículo" onclick="window.location.href='nuevo_articulo.php?familia=<?=urlencode($familia);?>';"><input type="button" value="Volver a listado de familias" onclick="window.location.href='gestion_familias.php'" style="font-size: 10px"><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">ID</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Denominación</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Unidades</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Stock</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Precio de venta</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Precio de coste</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">Proveedor</font></td>
                          <td align="center"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        </tr>
                        <?php 
                        $consulta1="SELECT * FROM tarifa ".$where_de_consulta."AND (id_curso=$id_curso_actual OR id_curso=0) ORDER BY ARTICULO";

                        $consulta_stock = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows ($consulta_stock);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF" colspan=8>
                          <td align="center" colspan=8><font size=2 face="<?= $fuente_general; ?>">No se han encontrado servicios/artículos en la familia seleccionada</font></td>
                        </tr>
                        <?php 
                        } else {
                        while($datos_stock = db_fetch_array($consulta_stock)){
                        
                        $se_puede_eliminar=true;

                        $id_articulo_tarifa=$datos_stock["ID"];
                        $unidad_articulo_actual=$datos_stock["UNIDAD"];
                        
                        if (is_numeric($unidad_articulo_actual)) {
                        
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

                                                if ($stock_articulo<=0) {
                                                        $stock_articulo="<font color=red>$stock_articulo</font>";
                                                        }
                                                $cantidad_stock=$stock_articulo;
                                                } else {
                                                $stock_articulo="-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                                $cantidad_stock=0;
                                                }
                        if ($cantidad_stock!=0) {
                                $cantidad_consumida=($cantidad_stock/$unidad_articulo_actual)*-1;
                                $corregir_stock=true;
                                if ($corregir_stock==true) {
                                        echo("INSERT INTO detalles_pedido_proveedor (id_pedido,id_articulo,cantidad) VALUES ('196','$id_articulo_tarifa','$cantidad_consumida')") or die(db_error());
                                        }
                                
                                }

                        // Busca unidad generico
                        $busca_generico=db_query_params('SELECT * FROM menu_alimentos WHERE id_alimento=?', 's', array($datos_stock["ID_ALIMENTO_GENERICO"]));
                        
                        if (db_num_rows($busca_generico)!=0) {
                                $datos_generico=db_fetch_array($busca_generico);
                                $unidad_de_medida=$unidad_medida[$datos_generico["unidad_medida"]];
                                } else {
                                $unidad_de_medida="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                }
                                
                        if (strlen($datos_stock["PROVEEDOR"])!=0) {
                                $consulta2="SELECT * FROM proveedores WHERE ID_PROVEEDOR=".$datos_stock["PROVEEDOR"];
                                $consulta_proveedor = db_query($consulta2) or die (db_error());
                                $datos_proveedor = db_fetch_array ($consulta_proveedor);
                                
                                } else {
                                $proveedor_linea="Desconocido";
                                }
                        ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_stock["ID"];?></font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_stock["ARTICULO"];?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_stock["UNIDAD"];?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$stock_articulo;?> <?=$unidad_de_medida;?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_stock["PRECIO_VENTA"],2,",",".");?> €</font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_stock["PRECIO_COSTE"],2,",",".");?> €</font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_proveedor["RAZON_SOCIAL"];?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?php if ($se_puede_eliminar==true) {?><a href="javascript:eliminar('<?=$datos_stock["ID"];?>')">Eliminar</a> | <?php }?><a href="modificar_articulo.php?cod=<?=$datos_stock["ID"]?>">Modificar</a></font></td>
                                </tr>
                        <?php 
                        $contador=$contador+1;
                        }
                        }
                        ?>
                         </table>
                         <br>
                         <input style="font-size: 10px" type="button" value="Nuevo artículo" onclick="window.location.href='nuevo_articulo.php?familia=<?=urlencode($familia);?>';"><input type="button" value="Volver a listado de familias" onclick="window.location.href='gestion_familias.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
<br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php 
require("../configuracion/parte_inferior.php"); ?>
