<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<?php 

        $id_centro=$centro_actual;
        
        if ($nivel_acceso>=3) {
        $filtro_centros="";
        } else {
        $filtro_centros=" WHERE id_centro=$id_centro";
        }
        
                        
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

        $filtro_fecha_baja="";

        if ($id_curso_actual!=0) {
                                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
                                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                                
                                if (strtotime($datos_curso_actual["fecha_fin"])>strtotime(date("Y-m-d"))) {
                                        $filtro_fecha_baja=" AND alumnos.fecha_baja>='".date("Y-m-d")."'";
                                        } else {
                                        $filtro_fecha_baja="";
                                        }
        
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

                        $consulta_actividades=db_query("SELECT *,tipos_iva_servicios.categoria AS nombre_cat FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.categoria like 'Material%'$filtro AND tipos_iva_servicios.tipo_servicios=0 ORDER BY tipos_iva_servicios.categoria, tarifa.ARTICULO;") or die(db_error());

                        $cat_actual=0;
                        
                        while($actividades=db_fetch_array($consulta_actividades)){
                        
                        if ($cat_actual!=$actividades["id_tipo_iva"]) {
                        
                               $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                $numero_escuelas=db_num_rows($busca_escuelas);
                                if ($nivel_acceso>=3) {
                                $num_cols=9;
                                } else {
                                $num_cols=1;
                                }
                                ?>
                        <tr valign="top">
                                <td colspan=<?=($num_cols+($numero_escuelas*8));?>><font size=2 face="<?= $fuente_general; ?>"><b><?=$actividades["nombre_cat"];?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td rowspan=4><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td colspan=8 align="center"><font size=2 face="<?= $fuente_general; ?>"><b><?=$datos_escuelas["nombre_centro"]?></b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso>=3) {
                                ?>
                                <td colspan=8 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                <td colspan=3 rowspan=3 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Stock</b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                        <tr valign="top">
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td colspan=4 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Niños</b></font></td>
                                <td colspan=4 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Niñas</b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso>=3) {
                                ?>
                                <td colspan=4 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Niños</b></font></td>
                                <td colspan=4 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Niñas</b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                        <tr valign="top">
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nuevos</b></font></td>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Viejos</b></font></td>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nuevas</b></font></td>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Viejas</b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso>=3) {
                                ?>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nuevos</b></font></td>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Viejos</b></font></td>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nuevas</b></font></td>
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Viejas</b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                        <tr valign="top">
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritos</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritos</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso>=3) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritos</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritos</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Inscritas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Stock almacén</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Stock escuelas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Stock Total</b></font></td>
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

                                $total_nuevos=0;
                                $total_viejos=0;
                                $total_nuevas=0;
                                $total_viejas=0;
                                $total_ventas_nuevos=0;
                                $total_ventas_nuevas=0;
                                $total_ventas_viejos=0;
                                $total_ventas_viejas=0;
                                
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                
                                $busca_reservas_escuela=db_query_params('SELECT Sum(reserva_servicios.cantidad) AS SumaDecantidad FROM alumnos,reserva_servicios WHERE alumnos.id_curso=? AND alumnos.id_alumno = reserva_servicios.id_alumno AND (((alumnos.id_centro)=?) AND ((reserva_servicios.id_servicio)=?));', 'sss', array($id_curso_actual, $datos_escuelas["id_centro"], $actividades["ID"])) or die(db_error()."-1");
                                $datos_reservas=db_fetch_array($busca_reservas_escuela);
                                $busca_ventas_escuela=db_query_params('SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,servicios_contratados WHERE alumnos.id_curso=? AND alumnos.id_alumno = servicios_contratados.id_alumno AND (((alumnos.id_centro)=?) AND ((servicios_contratados.id_servicio)=?));', 'sss', array($id_curso_actual, $datos_escuelas["id_centro"], $actividades["ID"])) or die(db_error()."-2");
                                $datos_ventas=db_fetch_array($busca_ventas_escuela);
                                $busca_entradas_escuela=db_query_params('SELECT Sum(movimientos_material_escuelas.cantidad) AS SumaDecantidad FROM movimientos_material_escuelas WHERE (((movimientos_material_escuelas.id_escuela)=?) AND ((movimientos_material_escuelas.id_material)=?));', 'ss', array($datos_escuelas["id_centro"], $actividades["ID"])) or die(db_error()."-2");
                                $datos_entradas=db_fetch_array($busca_entradas_escuela);

                                $busca_ultima_entrega=db_query_params('SELECT * FROM movimientos_material_escuelas WHERE id_material=? AND id_escuela=? ORDER BY fecha DESC LIMIT 0,1', 'ss', array($actividades["ID"], $datos_escuelas["id_centro"]));
                                if (db_num_rows($busca_ultima_entrega)!=0) {
                                        $datos_ultima_entrega=db_fetch_array($busca_ultima_entrega);
                                        $ultima_entrega_material=$datos_ultima_entrega["fecha"];
                                        } else {
                                        $ultima_entrega_material="2005-01-01";
                                        }

                                $busca_reservas_escuela_nuevas=db_query_params('SELECT Sum(reserva_servicios.cantidad) AS SumaDecantidad FROM alumnos,reserva_servicios WHERE alumnos.id_alumno = reserva_servicios.id_alumno AND (((alumnos.id_centro)=?) AND ((reserva_servicios.id_servicio)=?)) AND reserva_servicios.fecha>?;', 'sss', array($datos_escuelas["id_centro"], $actividades["ID"], (string)($ultima_entrega_material))) or die(db_error()."-1");
                                $datos_reservas_nuevas=db_fetch_array($busca_reservas_escuela_nuevas);

                                $total_reservas=$total_reservas+$datos_reservas["SumaDecantidad"];
                                $total_ventas=$total_ventas+$datos_ventas["SumaDecantidad"];
                                $total_entradas_escuelas=$total_entradas_escuelas+$datos_entradas["SumaDecantidad"];
                                
                                $busca_ventas_escuela=db_query("SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,servicios_contratados WHERE alumnos.id_curso=$id_curso_actual AND alumnos.id_alumno = servicios_contratados.id_alumno AND (((alumnos.id_centro)=".$datos_escuelas["id_centro"].")$filtro_fecha_baja AND ((servicios_contratados.id_servicio)=".$actividades["ID"]."));") or die(db_error()."-2");
                                $datos_ventas=db_fetch_array($busca_ventas_escuela);
                                
                                $busca_ventas_escuela_ninos=db_query("SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,usuarios,servicios_contratados WHERE alumnos.id_curso=$id_curso_actual AND alumnos.id_alumno = servicios_contratados.id_alumno AND alumnos.id_usuario = usuarios.id_usuario$filtro_fecha_baja  AND usuarios.sexo = 'V'  AND (((alumnos.id_centro)=".$datos_escuelas["id_centro"].") AND ((servicios_contratados.id_servicio)=".$actividades["ID"]."));") or die(db_error()."-2");
                                $datos_ventas_ninos=db_fetch_array($busca_ventas_escuela_ninos);

                                $busca_ventas_escuela_ninos_nuevos=db_query("SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad,alumnos.id_usuario FROM alumnos,usuarios,servicios_contratados WHERE alumnos.id_curso=$id_curso_actual AND alumnos.id_alumno = servicios_contratados.id_alumno AND alumnos.id_usuario = usuarios.id_usuario$filtro_fecha_baja AND usuarios.sexo = 'V'  AND (((alumnos.id_centro)=".$datos_escuelas["id_centro"].") AND ((servicios_contratados.id_servicio)=".$actividades["ID"].")) GROUP BY alumnos.id_usuario;") or die(db_error()."-2");

                                $ventas_nuevos=0;
                                $ventas_viejos=0;
                                $nuevos=0;
                                $viejos=0;
                                while($datos_ventas_ninos_nuevos=db_fetch_array($busca_ventas_escuela_ninos_nuevos)){
                                        // Comprueba si es nuevo
                                        if ($datos_ventas_ninos_nuevos["SumaDecantidad"]>0) {
                                                $busca_si_nuevo=db_query_params('SELECT * FROM alumnos,cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND cursos_academicos.tipo_curso=0 AND alumnos.id_curso<? AND alumnos.id_usuario=?', 'ss', array($id_curso_actual, $datos_ventas_ninos_nuevos["id_usuario"]));
                                                if (db_num_rows($busca_si_nuevo)==0) {
                                                        $ventas_nuevos=$ventas_nuevos+$datos_ventas_ninos_nuevos["SumaDecantidad"];
                                                        $nuevos++;
                                                        } else {
                                                        $ventas_viejos=$ventas_viejos+$datos_ventas_ninos_nuevos["SumaDecantidad"];
                                                        $viejos++;
                                                        }
                                                }
                                        }

                                $busca_ventas_escuela_ninas_nuevas=db_query("SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad,alumnos.id_usuario FROM alumnos,usuarios,servicios_contratados WHERE alumnos.id_curso=$id_curso_actual AND alumnos.id_alumno = servicios_contratados.id_alumno AND alumnos.id_usuario = usuarios.id_usuario$filtro_fecha_baja AND usuarios.sexo = 'H'  AND (((alumnos.id_centro)=".$datos_escuelas["id_centro"].") AND ((servicios_contratados.id_servicio)=".$actividades["ID"].")) GROUP BY alumnos.id_usuario;") or die(db_error()."-2");

                                $ventas_nuevas=0;
                                $ventas_viejas=0;
                                $nuevas=0;
                                $viejas=0;
                                while($datos_ventas_ninas_nuevas=db_fetch_array($busca_ventas_escuela_ninas_nuevas)){
                                        // Comprueba si es nueva
                                        if ($datos_ventas_ninas_nuevas["SumaDecantidad"]>0) {
                                                $busca_si_nueva=db_query_params('SELECT * FROM alumnos,cursos_academicos WHERE alumnos.id_curso=cursos_academicos.id_curso AND cursos_academicos.tipo_curso=0 AND alumnos.id_curso<? AND alumnos.id_usuario=?', 'ss', array($id_curso_actual, $datos_ventas_ninas_nuevas["id_usuario"]));
                                                if (db_num_rows($busca_si_nueva)==0) {
                                                        $ventas_nuevas=$ventas_nuevas+$datos_ventas_ninas_nuevas["SumaDecantidad"];
                                                        $nuevas++;
                                                        } else {
                                                        $ventas_viejas=$ventas_viejas+$datos_ventas_ninas_nuevas["SumaDecantidad"];
                                                        $viejas++;
                                                        }
                                                }
                                        }
                                
                                $total_ventas_nuevos=$total_ventas_nuevos+$ventas_nuevos;
                                $total_nuevos=$total_nuevos+$nuevos;
                                $total_ventas_nuevas=$total_ventas_nuevas+$ventas_nuevas;
                                $total_nuevas=$total_nuevas+$nuevas;
                                $total_ventas_viejos=$total_ventas_viejos+$ventas_viejos;
                                $total_viejos=$total_viejos+$viejos;
                                $total_ventas_viejas=$total_ventas_viejas+$ventas_viejas;
                                $total_viejas=$total_viejas+$viejas;
                                ?>
                                <td align="right" bgcolor="#efefcc"><font size=2 face="<?= $fuente_general; ?>"><?=intval($nuevos);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($ventas_nuevos);?></font></td>
                                <td align="right" bgcolor="#efefff"><font size=2 face="<?= $fuente_general; ?>"><?=intval($viejos);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($ventas_viejos);?></font></td>
                                <td align="right" bgcolor="#ffefef"><font size=2 face="<?= $fuente_general; ?>"><?=intval($nuevas);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($ventas_nuevas);?></font></td>
                                <td align="right" bgcolor="#efcccc"><font size=2 face="<?= $fuente_general; ?>"><?=intval($viejas);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($ventas_viejas);?></font></td>
                                <?php 
                                }
                                
                                $busca_stock=db_query_params('SELECT Sum(cantidad) AS SumaDecantidad FROM movimientos_material WHERE id_material=?', 's', array($actividades["ID"]));
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

                                if ($nivel_acceso>=3) {
                                ?>
                                <td align="right" bgcolor="#efefcc"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_nuevos);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_ventas_nuevos);?></font></td>
                                <td align="right" bgcolor="#efefff"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_viejos);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_ventas_viejos);?></font></td>
                                <td align="right" bgcolor="#ffefef"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_nuevas);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_ventas_nuevas);?></font></td>
                                <td align="right" bgcolor="#efcccc"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_viejas);?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_ventas_viejas);?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$stock_almacen;?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$stock_tiendas;?></font></td>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$stock_actual;?></font></td>
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
<script language="Javascript">

function abrir_ventana(ventana)
        nueva_ventana=window.open(ventana,'resumen','toolbar=yes,resizable=yes,width=800,height=600');
        }

</script>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
