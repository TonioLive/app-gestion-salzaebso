<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$formulario_name="centros";
$formulario_accion="editar_centro2.php";

$total_ninos=0;
$total_ninos=0;

$fecha_seleccionada=$_GET["fecha_seleccionada"];

if (strlen($fecha_seleccionada)==0) {
        $fecha_seleccionada=date("U");
        }

$url_retorno="/index.php";
        
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
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }

include("../centros/crea_estructura_carpetas.php");

?>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
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
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br>
                <?php 
                $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro");
                while($datos_centros=db_fetch_array($busca_centros)) {
                $id_centro=$datos_centros["id_centro"];
                ?>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listado de aulas del Centro <i><?=$datos_centros["nombre_centro"]?></i></font></b></font></td>
                        </tr>
                         <?php 
                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo<3 AND id_centro=$id_centro$filtro ORDER BY id_curso,nombre;") or die(db_error());
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$aulas["id_curso"]);
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                }

                        // Analiza el aula
                        /*
                        // Calcula numero de inscripciones
                        $busca_inscripcion=mysql_query("SELECT alumnos.id_alumno,usuarios.Nombre,usuarios.Apellido1,usuarios.Apellido2 FROM alumnos, usuarios, servicios_contratados, tarifa WHERE alumnos.id_usuario=usuarios.id_usuario AND servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=alumnos.id_alumno AND tarifa.subcat=1 AND alumnos.aula=".$aulas["id_aula"]." GROUP BY alumnos.id_alumno") or die(mysql_error());
                        $inscripciones_total=mysql_num_rows($busca_inscripcion);

                        // Calcula ocupación actual
                        $consulta_ocupacion=mysql_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=".$aulas["id_aula"]." AND fecha_matricula<='".date("Y-m-d",$fecha_seleccionada)."' AND fecha_baja>='".date("Y-m-d",$fecha_seleccionada)."'") or die(mysql_error()."-1");
                        $ocupacion_actual=mysql_num_rows($consulta_ocupacion);

                        // Calcula ocupación actual niños
                        $consulta_ocupacion_ninos=mysql_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND usuarios.sexo='V' AND alumnos.aula=".$aulas["id_aula"]." AND fecha_matricula<='".date("Y-m-d",$fecha_seleccionada)."' AND fecha_baja>='".date("Y-m-d",$fecha_seleccionada)."'") or die(mysql_error()."-1");
                        $ocupacion_actual_ninos=mysql_num_rows($consulta_ocupacion_ninos);
                        $ocupacion_actual_ninas=$ocupacion_actual-$ocupacion_actual_ninos;
                        
                        $total_ninos=$total_ninos+$ocupacion_actual_ninos;
                        $total_ninas=$total_ninas+$ocupacion_actual_ninas;
                        
                        // Calcula ocupación total
                        $consulta_ocupacion_total=mysql_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=".$aulas["id_aula"]) or die(mysql_error()."-1");
                        $ocupacion_total=mysql_num_rows($consulta_ocupacion_total);
                        
                        // Calcula asistencia
                        $consulta_asistencia_hoy=mysql_query("SELECT servicios_consumidos.id_alumno FROM servicios_consumidos WHERE (((servicios_consumidos.id_servicio)=".$aulas["id_aula"].") AND ((servicios_consumidos.fecha)='".date("Y-m-d",$fecha_seleccionada)."')) GROUP BY servicios_consumidos.id_alumno") or die(mysql_error()."-1");
                        $asistencia_hoy=mysql_num_rows($consulta_asistencia_hoy);
                        
                        $ocupacion_max=$aulas["capacidad"];
                        $ocupacion_min=$aulas["ocupacion_minima"];
                        $color_ocupacion="#000000";
                        if ($ocupacion_actual>$ocupacion_max) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="Se ha superado la capacidad del aula.";
                                }
                        if ($ocupacion_actual<$ocupacion_min) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="No se alcanza la ocupación mínima.";
                                }

                        // Calcula promedio de edades del aula
                        $promedio_edades=0;
                        if ($ocupacion_actual>0) {
                                while($listado_alumnos_aula=mysql_fetch_array($consulta_ocupacion)) {
                                        // 1. Comprueba la edad restando el año de nacimiento al año actual
                                        $fecha_l=$listado_alumnos_aula["fecha_nacimiento"];
                                        $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                        // 1.1. Cacula edad con decimales
                                        $mes_d_l=date("m",strtotime($fecha_l));
                                        $mes_d_n=date("m");
                                        $dif_meses=$mes_d_n-$mes_d_l;
                                        
                                        $edad_d_l=($edad_a_l*12)+$dif_meses;
                                        $promedio_edades=$promedio_edades+$edad_d_l;

                                        }
                                $promedio_edades=round($promedio_edades/$ocupacion_actual,2);
                                }
                        
                        */
                        
                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=4 align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>">Curso: <?=$nombre_curso_academico;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Mín.</b></font></td>
                                <!--
                                <td align="center" bgcolor="#efefef" width=35%><a href="javascript:selecciona_fecha();" title="Selecciona la fecha en el calendario"><font size=1 face="<?= $fuente_general; ?>"><font size=1><img src="/imagen/calendario.gif" border=0 align="right"><b>Ocupación<br><?=date("d/m/Y",$fecha_seleccionada);?></b></font></a>
                                <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <tr>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Preins.</font></td>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Actual</font></td>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Asistencia</font></td>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Ocupación</font></td>
                                </tr>
                                </table></td>-->
                                <!-- <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Promedio edades</b></font></td> -->
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$nombre_curso_academico;
                        }
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["nombre"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["capacidad"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["ocupacion_minima"];?></font></td>
                                <!--
                                <td align="center" bgcolor="#ffffff"><table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <tr>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(12,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_total-$inscripciones_total;?></a></font></td>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(1,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_actual;?><br>(V: <?=$ocupacion_actual_ninos;?> H:<?=$ocupacion_actual_ninas;?>)</font></a></td>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(2,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy;?></font></a></td>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(5,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_total;?></font></a></td>
                                </tr>
                                </table><font size=1><?=$mensaje_ocupacion;?></font></td>
                                -->
                                <!-- <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$promedio_edades;?> meses</font></td> -->
                               
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="listado_alumnos.php?id_aula=<?=$aulas["id_aula"];?>" title="Listado de Alumnos en Aula"><img src="alumnos.gif" border=0></a> <a href="#" onclick="window.open('fotos_alumnos.php?id_aula=<?=$aulas["id_aula"];?>','orla','toolbar=yes,width=800,height=600');" title="Orla de Alumnos en Aula"><img src="fotos.gif" border=0></a> <a target="_blank" href="/gestion/calendar/demos/agenda.php?id_aula=<?=$aulas["id_aula"];?>"><img src="calendario.gif" border=0></a></font></td>
                        </tr>
                         <?php 
                         }
                         
                        $total_alumnos=$total_ninos+$total_ninas;
                        if ($total_alumnos>0) {
                                $total_ninos_t=$total_ninos." (".round((($total_ninos/$total_alumnos)*100),2)."%)";
                                $total_ninas_t=$total_ninas." (".round((($total_ninas/$total_alumnos)*100),2)."%)";
                                }
                        ?>
                        <!--
                        <tr>
                        <td colspan=4 align="center"><font size=2><b>Total:</b> <?=$total_alumnos?> - <b>Total actual niños:</b> <?=$total_ninos_t?> - <b>Total actual niñas:</b> <?=$total_ninas_t?></td>
                        </tr>-->
                        </table><br>
                        <?php 
                        }
                        ?>
                        <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table><br><br>
<script language="Javascript">

function mostrar_ocupacion(tipo_listado,id_aula) {
        window.open("detalle_informe_ocupacion.php?fecha=<?=$fecha_seleccionada;?>&tipo="+tipo_listado+"&id_aula="+id_aula,"ventana_detalle","toolbar=no,width=750,height=550");
        }

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

        mes_presel=<?=date("m",$fecha_seleccionada);?>;
        dia_presel=<?=date("d",$fecha_seleccionada);?>;
        ano_presel=<?=date("Y",$fecha_seleccionada);?>;
        fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
        
        window.open('/herramientas/calendario_mes1.php?dia='+fecha_preseleccionada+'&pagina_destino=/gestion/gabinete/gestion_aulas.php','calendario','toolbar=no,resizable=no,width=200,height=216');
        }
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>
