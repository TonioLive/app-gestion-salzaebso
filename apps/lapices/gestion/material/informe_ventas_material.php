<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<?php 
        $id_centro=$centro_actual;
        
        if ($nivel_acceso==4) {
        $filtro_centros="";
        } else {
        $filtro_centros=" WHERE id_centro=$id_centro";
        }
        
                        
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
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND (tarifa.id_curso=$id_curso_actual OR tarifa.id_curso=0)";
                                } else {
                                $filtro="";
                                }

                        ?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Materiales del curso: </b> <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();"><?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?></select></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                        $id_centro=$centro_actual;

                        $consulta_actividades=db_query("SELECT *,tipos_iva_servicios.categoria AS nombre_cat FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.categoria like 'Material%'$filtro ORDER BY tipos_iva_servicios.categoria, tarifa.ARTICULO;") or die(db_error());

                        $cat_actual=0;
                        
                        while($actividades=db_fetch_array($consulta_actividades)){
                        
                        if ($cat_actual!=$actividades["id_tipo_iva"]) {
                        
                               $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                $numero_escuelas=db_num_rows($busca_escuelas);
                                if ($nivel_acceso==4) {
                                $num_cols=8;
                                } else {
                                $num_cols=1;
                                }
                                ?>
                        <tr valign="top">
                                <td colspan=<?=($num_cols+($numero_escuelas*4));?>><font size=2 face="<?= $fuente_general; ?>"><b><?=$actividades["nombre_cat"];?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td rowspan=2><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <?php 
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td colspan=4><font size=2 face="<?= $fuente_general; ?>"><b><?=$datos_escuelas["nombre_centro"]?></b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso==4) {
                                ?>
                                <td colspan=7><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                        <tr valign="top">
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Entradas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Entregas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Stock actual</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Reservas</b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso==4) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>1. Entradas Almacen</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>2. Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>3. Salidas Almacen</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Stock almacén (1-3)</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Stock escuelas (3-2)</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Stock Total (1-2)</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Reservas</b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                                <?php 
                                $cat_actual=$actividades["id_tipo_iva"];
                                }
                        
                        $tam_texto=14;
                        if (strlen($actividades["ARTICULO"])>20) {
                        $tam_texto=12;
                        }
                        if (strlen($actividades["ARTICULO"])>25) {
                        $tam_texto=11;
                        }
                        if (strlen($actividades["ARTICULO"])>30) {
                        $tam_texto=10;
                        }
                        if (strlen($actividades["ARTICULO"])>35) {
                        $tam_texto=9;
                        }
                        
                        // onclick="window.location.href='control_asistencia_actividad.php?id_actividad='"
                        
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><a href="control_asistencia_actividad.php?id_actividad=<?=$actividades["ID"]?>" target="_new"><?=$actividades["ARTICULO"]?></a></font></td>
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                
                                $total_reservas=0;
                                $total_ventas=0;
                                $total_entradas_escuelas=0;
                                
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                
                                $busca_reservas_escuela=db_query("SELECT Sum(reserva_servicios.cantidad) AS SumaDecantidad FROM alumnos,reserva_servicios WHERE alumnos.id_alumno = reserva_servicios.id_alumno AND (((alumnos.id_centro)=".$datos_escuelas["id_centro"].") AND ((reserva_servicios.id_servicio)=".$actividades["ID"]."));") or die(db_error()."-1");
                                $datos_reservas=db_fetch_array($busca_reservas_escuela);
                                $busca_ventas_escuela=db_query("SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,servicios_contratados WHERE alumnos.id_alumno = servicios_contratados.id_alumno AND (((alumnos.id_centro)=".$datos_escuelas["id_centro"].") AND ((servicios_contratados.id_servicio)=".$actividades["ID"]."));") or die(db_error()."-2");
                                $datos_ventas=db_fetch_array($busca_ventas_escuela);
                                $busca_entradas_escuela=db_query("SELECT Sum(movimientos_material_escuelas.cantidad) AS SumaDecantidad FROM movimientos_material_escuelas WHERE (((movimientos_material_escuelas.id_escuela)=".$datos_escuelas["id_centro"].") AND ((movimientos_material_escuelas.id_material)=".$actividades["ID"]."));") or die(db_error()."-2");
                                $datos_entradas=db_fetch_array($busca_entradas_escuela);
                                
                                
                                $total_reservas=$total_reservas+$datos_reservas["SumaDecantidad"];
                                $total_ventas=$total_ventas+$datos_ventas["SumaDecantidad"];
                                $total_entradas_escuelas=$total_entradas_escuelas+$datos_entradas["SumaDecantidad"];
                                ?>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><a href="#" onclick="window.open('resumen_entradas_producto_escuela.php?id_escuela=<?=$datos_escuelas["id_centro"]?>&id_producto=<?=$actividades["ID"]?>','resumen','toolbar=yes,resizable=yes,width=800,height=600');"><?=floatval($datos_entradas["SumaDecantidad"]);?></a></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_ventas["SumaDecantidad"];?></font></td>
                                <td align="right" bgcolor="#efefcc"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_entradas["SumaDecantidad"]-$datos_ventas["SumaDecantidad"];?></font></td>
                                <td align="right" bgcolor="#ddefef"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_reservas["SumaDecantidad"];?></font></td>
                                <?php 
                                }
                                
                                $busca_stock=db_query("SELECT Sum(cantidad) AS SumaDecantidad FROM movimientos_material WHERE id_material=".$actividades["ID"]);
                                $datos_stock=db_fetch_array($busca_stock);
                                
                                $total_compras=floatval($datos_stock["SumaDecantidad"]);
                                $stock_actual=$total_compras-$total_ventas;

                                if ($stock_actual<0) {
                                        $stock_actual="<font color=red>$stock_actual</font>";
                                        }
                                        
                                $stock_tiendas=$total_entradas_escuelas-$total_ventas;

                                if ($stock_tiendas<0) {
                                        $stock_tiendas="<font color=red>$stock_tiendas</font>";
                                        }
                                        
                                $stock_almacen=$total_compras-$total_entradas_escuelas;

                                if ($stock_almacen<0) {
                                        $stock_almacen="<font color=red>$stock_almacen</font>";
                                        }

                                if ($nivel_acceso==4) {
                                ?>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><a href="#" onclick="window.open('resumen_entradas_producto.php?id_producto=<?=$actividades["ID"]?>','resumen','toolbar=yes,resizable=yes,width=800,height=600');"><?=$total_compras;?></a></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$total_ventas;?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$total_entradas_escuelas;?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$stock_almacen;?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$stock_tiendas;?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$stock_actual;?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$total_reservas;?></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                </td>
        </tr>
        </form>
</table>
</td>
</tr>
</table>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
