<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<SCRIPT LANGUAGE=javascript>
<!--

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

//-->
</SCRIPT>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<?php 

$fila_par_impar=0;

                        $id_centro=$centro_actual;
                        
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

                        $consulta_actividades=db_query("SELECT *,tipos_iva_servicios.categoria AS nombre_cat FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva$filtro AND tipos_iva_servicios.tipo_servicios=0 ORDER BY tipos_iva_servicios.categoria, tarifa.ID;") or die(db_error());

                        $cat_actual=0;
                        
                        while($actividades=db_fetch_array($consulta_actividades)){
                        
                        if ($cat_actual!=$actividades["id_tipo_iva"]) {
                                ?>
                        <tr valign="top">
                                <td colspan=2><font size=2 face="<?= $fuente_general; ?>"><b><?=$actividades["nombre_cat"];?></b></font></td>
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
                        
                        $precio_total=number_format(($actividades["PRECIO_VENTA"]*(1+$actividades["porcentaje_iva"])),2,",",".");
                        
                        $precio_5=number_format(($actividades["PRECIO_VENTA"]*0.95*(1+$actividades["porcentaje_iva"])),2,",",".");
                        
                        $precio_10=number_format(($actividades["PRECIO_VENTA"]*0.90*(1+$actividades["porcentaje_iva"])),2,",",".");
                        
                        
                $color_fondo_fila="#ffffff";                
        if ($fila_par_impar==1) {
                $color_fondo_fila="#cee1eb";
                $fila_par_impar=-1;
                } 
                
        $fila_par_impar++;   
        ?>
        <tr bgcolor="<?=$color_fondo_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fondo_fila;?>')">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="<?=$actividades["ARTICULO"]?>" onclick="window.location.href='control_asistencia_actividad.php?id_actividad=<?=$actividades["ID"]?>'" style="font-size:<?=$tam_texto;?>px; width: 320; height: 32"></font></td>
                                <td><font size=3>Precio: <?=$precio_total?> €<br><small>Con 5% dto.: <?=$precio_5?> €. Con 10% dto.: <?=$precio_10?> €</small></font></td>
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
