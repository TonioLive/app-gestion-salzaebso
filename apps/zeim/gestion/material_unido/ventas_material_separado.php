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

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND (gestionguarderias.tarifa.id_curso=$id_curso_actual OR gestionguarderias.tarifa.id_curso=0)";
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

                        $consulta_actividades=db_query("SELECT *,gestionguarderias.tipos_iva_servicios.categoria AS nombre_cat FROM gestionguarderias.tarifa, gestionguarderias.tipos_iva_servicios WHERE gestionguarderias.tarifa.CATEGORIA=gestionguarderias.tipos_iva_servicios.id_tipo_iva AND gestionguarderias.tipos_iva_servicios.categoria like 'Material%'$filtro AND gestionguarderias.tipos_iva_servicios.tipo_servicios=0 ORDER BY gestionguarderias.tipos_iva_servicios.categoria, gestionguarderias.tarifa.ARTICULO;") or die(db_error());

                        $cat_actual=0;
                        
                        while($actividades=db_fetch_array($consulta_actividades)){
                        
                        if ($cat_actual!=$actividades["id_tipo_iva"]) {
                        
                               $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                $numero_escuelas=db_num_rows($busca_escuelas);
                                if ($nivel_acceso==4) {
                                $num_cols=4;
                                } else {
                                $num_cols=1;
                                }
                                ?>
                        <tr valign="top">
                                <td colspan=<?=($num_cols+($numero_escuelas*3));?>><font size=2 face="<?= $fuente_general; ?>"><b><?=$actividades["nombre_cat"];?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td rowspan=2><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <?php 
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td colspan=3><font size=2 face="<?= $fuente_general; ?>"><b><?=$datos_escuelas["nombre_centro"]?></b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso==4) {
                                ?>
                                <td colspan=3><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                <?php 
                                }
                                ?>
                        </tr>
                        <tr valign="top">
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas niños</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas niñas</b></font></td>
                                <?php 
                                }
                                if ($nivel_acceso==4) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Total Ventas</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Total niños</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Total niñas</b></font></td>
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
                                $total_ventas_ninos=0;
                                $total_entradas_escuelas=0;
                                
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                
                                $busca_ventas_escuela=db_query_params('SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,servicios_contratados WHERE alumnos.id_alumno = servicios_contratados.id_alumno AND (((alumnos.id_centro)=?) AND ((servicios_contratados.id_servicio)=?));', 'ss', array($datos_escuelas["id_centro"], $actividades["ID"])) or die(db_error()."-2");
                                $datos_ventas=db_fetch_array($busca_ventas_escuela);
                                
                                $busca_ventas_escuela_ninos=db_query_params('SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,usuarios,servicios_contratados WHERE alumnos.id_alumno = servicios_contratados.id_alumno AND alumnos.id_usuario = usuarios.id_usuario AND usuarios.sexo = \'V\'  AND (((alumnos.id_centro)=?) AND ((servicios_contratados.id_servicio)=?));', 'ss', array($datos_escuelas["id_centro"], $actividades["ID"])) or die(db_error()."-2");
                                $datos_ventas_ninos=db_fetch_array($busca_ventas_escuela_ninos);
                                
                                $total_ventas_ninos=$total_ventas_ninos+$datos_ventas_ninos["SumaDecantidad"];
                                $total_ventas=$total_ventas+$datos_ventas["SumaDecantidad"];
                                
                                ?>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($datos_ventas["SumaDecantidad"]);?></font></td>
                                <td align="right" bgcolor="#efefcc"><font size=2 face="<?= $fuente_general; ?>"><?=intval($datos_ventas_ninos["SumaDecantidad"]);?></font></td>
                                <td align="right" bgcolor="#ddefef"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_ventas["SumaDecantidad"]-$datos_ventas_ninos["SumaDecantidad"];?></font></td>
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

                                if ($nivel_acceso==4) {
                                ?>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$total_ventas;?></font></td>
                                <td align="right" bgcolor="#efefcc"><font size=2 face="<?= $fuente_general; ?>"><?=$total_ventas_ninos;?></font></td>
                                <td align="right" bgcolor="#ddefef"><font size=2 face="<?= $fuente_general; ?>"><?=$total_ventas-$total_ventas_ninos;?></font></td>
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
