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
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                        $id_centro=$centro_actual;

                        $consulta_actividades=db_query("SELECT *,tipos_iva_servicios.categoria AS nombre_cat FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.categoria like 'Material%'$filtro AND tipos_iva_servicios.tipo_servicios=0 ORDER BY tipos_iva_servicios.categoria, tarifa.ARTICULO;") or die(db_error());

                        $cat_actual=0;
                        
                        while($actividades=db_fetch_array($consulta_actividades)){
                        
                        if ($cat_actual!=$actividades["id_tipo_iva"]) {
                        
                               $busca_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE nombre not like \'Escuela%\' AND id_curso<>? ORDER BY fecha_inicio DESC LIMIT 0,5', 's', array($id_curso_actual));
                                $numero_cursos=db_num_rows($busca_cursos);

                                $num_cols=2;

                                ?>
                        <tr valign="top">
                                <td colspan=<?=($num_cols+$numero_cursos);?>><font size=2 face="<?= $fuente_general; ?>"><b><?=$actividades["nombre_cat"];?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Total Ventas</b></font></td>
                                <?php 
                                $busca_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE nombre not like \'Escuela%\' AND id_curso<=? ORDER BY fecha_inicio DESC LIMIT 0,5', 's', array($id_curso_actual));
                                while($datos_cursos=db_fetch_array($busca_cursos)) {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Ventas <?=$datos_cursos["nombre"]?></b></font></td>
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
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><?=$actividades["ARTICULO"]?></font></td>
                                <?php 
                                $busca_escuelas=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");


                                $total_ventas=0;

                                
                                while($datos_escuelas=db_fetch_array($busca_escuelas)) {
                                
                                $busca_ventas_escuela=db_query_params('SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,servicios_contratados WHERE alumnos.id_alumno = servicios_contratados.id_alumno AND (((alumnos.id_centro)=?) AND ((servicios_contratados.id_servicio)=?));', 'ss', array($datos_escuelas["id_centro"], $actividades["ID"])) or die(db_error()."-2");
                                $datos_ventas=db_fetch_array($busca_ventas_escuela);

                                $total_ventas=$total_ventas+$datos_ventas["SumaDecantidad"];

                                }
                                ?>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$total_ventas;?></font></td>
                                <?php 
                                $busca_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE nombre not like \'Escuela%\' AND id_curso<=? ORDER BY fecha_inicio DESC LIMIT 0,5', 's', array($id_curso_actual));
                                while($datos_cursos=db_fetch_array($busca_cursos)) {
                                
                                $total_ventas_curso=0;
                                $busca_escuelas_curso=db_query("SELECT * FROM centros$filtro_centros ORDER BY nombre_centro");
                                while($datos_escuelas_curso=db_fetch_array($busca_escuelas_curso)) {

                                        $busca_ventas_escuela=db_query_params('SELECT Sum(servicios_contratados.cantidad) AS SumaDecantidad FROM alumnos,servicios_contratados WHERE alumnos.id_alumno = servicios_contratados.id_alumno AND (alumnos.id_curso=?) AND (((alumnos.id_centro)=?) AND ((servicios_contratados.id_servicio)=?));', 'sss', array($datos_cursos["id_curso"], $datos_escuelas_curso["id_centro"], $actividades["ID"])) or die(db_error()."-2");
                                        $datos_ventas_curso=db_fetch_array($busca_ventas_escuela);

                                        $total_ventas_curso=$total_ventas_curso+$datos_ventas_curso["SumaDecantidad"];

                                        }
                                ?>
                                <td align="right" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><?=$total_ventas_curso?></font></td>
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
