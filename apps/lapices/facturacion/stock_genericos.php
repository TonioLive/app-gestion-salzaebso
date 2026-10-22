<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

$desglosado=$_GET["d"];
if (strlen($desglosado)==0) {
        $desglosado=0;
        }

if ($desglosado==1) {
        $entradilla="Total: ";
        } else {
        $entradilla="";
        }

$corregir_stock=$_GET["corregir_stock"];

$unidad_medida[0]="g.&nbsp;&nbsp;";
$unidad_medida[1]="ml.";

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

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
                        <?php 
                        if ($desglosado==1) {
                        ?>
                        <input type="button" value="Mostrar solo totales" onclick="window.location.href='stock_genericos.php?d=0&familia=<?=$familia?>'" style="font-size: 10px">
                        <?php 
                        } else {
                        ?>
                        <input type="button" value="Mostrar detalles" onclick="window.location.href='stock_genericos.php?d=1&familia=<?=$familia?>'" style="font-size: 10px">
                        <?php }
                        ?><input type="button" value="Salir" onclick="window.location.href='/almacen/gestion_almacen.php'" style="font-size: 10px"><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#ececec">
                          <?php 
                          if ($desglosado==1) {
                          ?>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Denominación</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Unidades</font></td>
                          <?php 
                          } else {
                          ?>
                          <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>">Denominación</font></td>
                          <?php 
                          }
                          ?>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Entradas</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Salidas</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Stock</font></td>
                        </tr>
                        <?php 
                        $consulta1="SELECT * FROM tarifa ".$where_de_consulta."AND NOT EXISTS (SELECT 1 FROM material_catalogo_retirado retirado WHERE retirado.id_material=tarifa.ID AND retirado.activo=1) AND (id_curso=$id_curso_actual OR id_curso=0) ORDER BY ID_ALIMENTO_GENERICO,ARTICULO";

                        $consulta_stock = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows ($consulta_stock);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF" colspan=8>
                          <td align="center" colspan=8><font size=2 face="<?= $fuente_general; ?>">No se han encontrado servicios/artículos en la familia seleccionada</font></td>
                        </tr>
                        <?php 
                        } else {
                        $id_generico_actual=0;
                        $total_entradas_generico=0;
                        $total_salidas_generico=0;
                        $total_generico=0;
                        while($datos_stock = db_fetch_array($consulta_stock)){
                        
                        // Busca unidad generico
                        $busca_generico=db_query("SELECT * FROM menu_alimentos WHERE id_alimento=".$datos_stock["ID_ALIMENTO_GENERICO"]);

                        if (db_num_rows($busca_generico)!=0) {
                                $datos_generico=db_fetch_array($busca_generico);
                                $unidad_de_medida=$unidad_medida[$datos_generico["unidad_medida"]];
                                $nombre_generico=$datos_generico["nombre_alimento"];
                                } else {
                                $unidad_de_medida="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                $nombre_generico="Desconocido";
                                }
                                
                        if ($id_generico_actual!=$datos_stock["ID_ALIMENTO_GENERICO"]) {
                                if ($id_generico_actual!=0) {
                                        if ($total_generico>0) {
                                                $color_stock="#000000";
                                                } else {
                                                $color_stock="#ff0000";
                                                }
                                        ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="left" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b><?=$entradilla?><?=$nombre_generico_actual;?></b></font></td>
                                  <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_entradas_generico,2,",",".");?> <?=$unidad_de_medida_generico_actual;?></font></td>
                                  <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_salidas_generico,2,",",".");?> <?=$unidad_de_medida_generico_actual;?></font></td>
                                  <td align="right"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_stock?>"><?=number_format($total_generico,2,",",".");?> <?=$unidad_de_medida_generico_actual;?></font></td>
                                </tr>
                                        <?php 
                                        }
                                $id_generico_actual=$datos_stock["ID_ALIMENTO_GENERICO"];
                                $total_entradas_generico=0;
                                $total_salidas_generico=0;
                                $total_generico=0;
                                
                                // Busca unidad generico
                                $busca_generico_actual=db_query("SELECT * FROM menu_alimentos WHERE id_alimento=".$id_generico_actual);

                                if (db_num_rows($busca_generico_actual)!=0) {
                                        $datos_generico_actual=db_fetch_array($busca_generico_actual);
                                        $unidad_de_medida_generico_actual=$unidad_medida[$datos_generico_actual["unidad_medida"]];
                                        $nombre_generico_actual=urldecode($datos_generico["nombre_alimento"]);
                                        } else {
                                        $unidad_de_medida_generico_actual="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                        $nombre_generico_actual="Desconocido";
                                        }
                                if ($desglosado==1) {
                                        ?>
                                        <tr valign="top" bgcolor="#efefef">
                                          <td align="left" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_generico_actual;?></b></font></td>
                                        </tr>
                                        <?php 
                                        }
                                }
                        
                        $se_puede_eliminar=true;

                        $id_articulo_tarifa=$datos_stock["ID"];
                        $unidad_articulo_actual=$datos_stock["UNIDAD"];
                        $detalles_stock="";
                        if (is_numeric($unidad_articulo_actual)) {
                        
                                                // Comprueba stock actual del articulo
                                                $busca_entradas=db_query("SELECT id_articulo, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_pedido_proveedor WHERE id_articulo=$id_articulo_tarifa GROUP BY id_articulo;");
                                                $entradas=db_fetch_array($busca_entradas);
                                                $total_entradas=$entradas["SumaDeCANTIDAD"];
                                                if (strlen($total_entradas)==0) {
                                                        $total_entradas=0;
                                                        }
                                                $total_entradas=$total_entradas*$unidad_articulo_actual;
                                                $total_entradas_generico=$total_entradas_generico+$total_entradas;

                                                // $busca_salidas=mysql_query("SELECT id_material, Sum(cantidad) AS SumaDeCANTIDAD FROM detalles_albaran_entrega WHERE id_material=$id_articulo_tarifa GROUP BY id_material;");
                                                $busca_salidas=db_query("SELECT id_producto_tarifa, Sum(cantidad) AS SumaDeCANTIDAD FROM menu_productos_consumo_cocina WHERE id_producto_tarifa=$id_articulo_tarifa GROUP BY id_producto_tarifa;");
                                                $salidas=db_fetch_array($busca_salidas);
                                                $total_salidas=$salidas["SumaDeCANTIDAD"];

                                                if (strlen($total_salidas)==0) {
                                                        $total_salidas=0;
                                                        }
                                                $total_salidas_generico=$total_salidas_generico+$total_salidas;

                                                $stock_articulo=round($total_entradas-$total_salidas,2);
                                                $total_generico=$total_generico+$stock_articulo;
                                                $cantidad_stock_numero=$stock_articulo;
                                                if ($stock_articulo<=0) {
                                                        $stock_articulo="<font color=red>$stock_articulo</font>";
                                                        }
                                                $cantidad_stock=$stock_articulo;
                                                } else {
                                                $stock_articulo="-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                                $cantidad_stock=0;
                                                $cantidad_stock_numero=0;
                                                }
                                                
                          if ($corregir_stock==1) {
                                        $cantidad_consumida=($cantidad_stock_numero/$unidad_articulo_actual)*-1;
                                        db_query("INSERT INTO detalles_pedido_proveedor (id_pedido,id_articulo,cantidad) VALUES ('196','$id_articulo_tarifa','$cantidad_consumida')") or die(db_error());
                                        $stock_articulo="-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                        $cantidad_stock=0;
                                        $cantidad_stock_numero=0;
                                        }

                        if (strlen($datos_stock["PROVEEDOR"])!=0) {
                                $consulta2="SELECT * FROM proveedores WHERE ID_PROVEEDOR=".$datos_stock["PROVEEDOR"];
                                $consulta_proveedor = db_query($consulta2) or die (db_error());
                                $datos_proveedor = db_fetch_array ($consulta_proveedor);
                                
                                } else {
                                $proveedor_linea="Desconocido";
                                }
                                
                        $detalles_stock=$detalles_stock."Total compras: ".number_format($total_entradas,2,",",".")." ".$unidad_de_medida;
                        $detalles_stock=$detalles_stock."Total consumo: ".number_format($total_salidas,2,",",".")." ".$unidad_de_medida;
                        if ($desglosado==1) {
                        ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_stock["ARTICULO"];?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_stock["UNIDAD"];?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_entradas,2,",",".");?> <?=$unidad_de_medida?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_salidas,2,",",".");?> <?=$unidad_de_medida?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><a title="<?=$detalles_stock?>"><?=$stock_articulo;?> <?=$unidad_de_medida;?></a></font></td>
                                </tr>
                        <?php 
                        }
                        $contador=$contador+1;
                        }
                                if ($id_generico_actual!=0) {
                                        if ($total_generico>0) {
                                                $color_stock="#000000";
                                                } else {
                                                $color_stock="#ff0000";
                                                }
                                        ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="left" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b><?=$entradilla?><?=$nombre_generico_actual;?></b></font></td>
                                  <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_entradas_generico,2,",",".");?> <?=$unidad_de_medida_generico_actual;?></font></td>
                                  <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_salidas_generico,2,",",".");?> <?=$unidad_de_medida_generico_actual;?></font></td>
                                  <td align="right"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_stock?>"><?=number_format($total_generico,2,",",".");?> <?=$unidad_de_medida_generico_actual;?></font></td>
                                </tr>
                                        <?php 
                                        }
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='/almacen/gestion_almacen'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
<br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php 
require("../configuracion/parte_inferior.php"); ?>
