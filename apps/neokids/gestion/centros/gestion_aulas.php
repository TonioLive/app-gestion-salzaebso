<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$mostrar_estadisticas=false;

$num_columnas_tabla=7;
if ($_GET["mostrar_estadisticas"]==1) {
        $mostrar_estadisticas=true;
        $num_columnas_tabla++;
        } 

$formulario_name="centros";
$formulario_accion="editar_centro2.php";

$total_ninos=0;
$total_ninos=0;

$id_centro=$centro_actual;

$fecha_seleccionada=$_GET["fecha_seleccionada"];

if (strlen($fecha_seleccionada)==0) {
        $fecha_seleccionada=date("U");
        }

$url_retorno="/index.php";
        
if (strlen($id_centro)!=0) {
        
                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro));
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);
                        
                        $db_nombre_centro=$datos_centro["nombre_centro"];

                        }

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }
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
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Grupos de <i><?=$db_nombre_centro?></i></b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=<?=$num_columnas_tabla?> align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listado de grupos</b></font></td>
                        </tr>
                         <?php 
                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo<3 AND id_centro=$id_centro$filtro ORDER BY id_curso,nombre;") or die(db_error());
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($aulas["id_curso"]));
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                $fecha_fin_curso=$datos_curso["fecha_fin"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                $fecha_fin_curso=date();
                                }

                        // Analiza el aula
                        // Calcula ocupación actual
                        $consulta_ocupacion=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? AND fecha_matricula<>fecha_baja', 'sss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)), (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);
                        // Calcula ocupación actual niños
                        $consulta_ocupacion_ninos=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND usuarios.sexo=\'V\' AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? AND fecha_matricula<>fecha_baja', 'sss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)), (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $ocupacion_actual_ninos=db_num_rows($consulta_ocupacion_ninos);
                        $ocupacion_actual_ninas=$ocupacion_actual-$ocupacion_actual_ninos;

                        $total_ninos=$total_ninos+$ocupacion_actual_ninos;
                        $total_ninas=$total_ninas+$ocupacion_actual_ninas;

                        if ($ocupacion_actual!=0) {
                                $porcentaje_ninos=number_format(($ocupacion_actual_ninos/$ocupacion_actual)*100,2,",",".");
                                $porcentaje_ninas=number_format(($ocupacion_actual_ninas/$ocupacion_actual)*100,2,",",".");
                                } else {
                                $porcentaje_ninos="0";
                                $porcentaje_ninas="0";
                                }

if ($mostrar_estadisticas==true) {                        
                        // Calcula numero de inscripciones
                        $busca_inscripcion=db_query_params('SELECT alumnos.id_alumno,usuarios.Nombre,usuarios.Apellido1,usuarios.Apellido2 FROM alumnos, usuarios, servicios_contratados, tarifa WHERE alumnos.id_usuario=usuarios.id_usuario AND servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=alumnos.id_alumno AND tarifa.subcat=1 AND alumnos.aula=? GROUP BY alumnos.id_alumno', 's', array($aulas["id_aula"])) or die(db_error());
                        $inscripciones_total1=db_num_rows($busca_inscripcion);

                        // Inscripciones con servicio aula
                        
                        $busca_inscripcion=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=? AND tarifa.subcat=1', 's', array($aulas["id_aula"])) or die(db_error());
                        $inscripciones_total2=0;
                        while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $consulta_ocupacion_total=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=?', 's', array($aulas["id_aula"])) or die(db_error()."-1");
                        while($datos_ocupacion_total=db_fetch_array($consulta_ocupacion_total)) {
                                $busca_si_activado=db_query_params('SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=? AND id_usuario=?', 'ss', array($datos_inscripcion["id_servicio_contratado"], $datos_ocupacion_total["id_alumno"]));
                                        if (db_num_rows($busca_si_activado)==0) {
                                                $inscripciones_total2++;
                                                }
                                        }
                                }

                        $inscripciones_total=$inscripciones_total1+$inscripciones_total2;

                        // Calcula ocupación total
                        $consulta_ocupacion_total=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND fecha_matricula<>fecha_baja AND alumnos.aula=?', 's', array($aulas["id_aula"])) or die(db_error()."-1");
                        $ocupacion_total=db_num_rows($consulta_ocupacion_total);
                        
                        // Calcula asistencia
                        $consulta_asistencia_hoy=db_query_params('SELECT servicios_consumidos.id_alumno FROM servicios_consumidos WHERE (((servicios_consumidos.id_servicio)=?) AND ((servicios_consumidos.fecha)=?)) GROUP BY servicios_consumidos.id_alumno', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $asistencia_hoy=db_num_rows($consulta_asistencia_hoy);
                        
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
                                while($listado_alumnos_aula=db_fetch_array($consulta_ocupacion)) {
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

                        // Comprueba si es actual o futuro
                        if ($fecha_fin_curso>=date("Y-m-d")) {
                                $curso_actual_o_futuro=true;
                                } else {
                                $curso_actual_o_futuro=false;
                                }
                        // Comprueba si el aula es de 0-1
                        if ($aulas["edad_minima_prog"]==0) {
                                // $curso_actual_o_futuro=false;
                                }
        }
                        
                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=<?=$num_columnas_tabla?> align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>">Curso: <?=$nombre_curso_academico;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Mín.</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Niños y niñas</b></font></td>
                                <?php 
                                if ($mostrar_estadisticas==true) {  
                                ?>
                                <td align="center" bgcolor="#efefef" width=35%><a href="javascript:selecciona_fecha();" title="Selecciona la fecha en el calendario"><font size=1 face="<?= $fuente_general; ?>"><font size=1><img src="/imagen/calendario.gif" border=0 align="right"><b>Ocupación<br><?=date("d/m/Y",$fecha_seleccionada);?></b></font></a>
                                <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <tr>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Preins.</font></td>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Actual</font></td>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Asistencia</font></td>
                                <td width=25% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Ocupación</font></td>
                                </tr>
                                </table></td>
                                <!-- <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Promedio edades</b></font></td> -->
                                <?php 
                                }
                                ?>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
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
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion(5,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_actual;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_actual_ninos;?> niños (<?=$porcentaje_ninos?> %) / <?=$ocupacion_actual_ninas;?> niñas (<?=$porcentaje_ninas?> %)</font></td>
                        <?php 
                          if ($mostrar_estadisticas==true) { 
                         ?>       
                                <td align="center" bgcolor="#ffffff"><table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <tr>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(12,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_total-$inscripciones_total;?></a></font></td>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(1,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_actual;?><br>(V: <?=$ocupacion_actual_ninos;?> H:<?=$ocupacion_actual_ninas;?>)</font></a></td>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(2,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy;?></font></a></td>
                                <td width=25% align="center"><a href='javascript:mostrar_ocupacion(5,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_total;?></font></a></td>
                                </tr>
                                </table><font size=1><?=$mensaje_ocupacion;?></font></td>
                                <!-- <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$promedio_edades;?> meses</font></td> -->
                                <?php 
                                }
                                ?>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href='/pedidos/introducir_nuevo_pedido.php?id_cliente=<?=$aulas["id_aula"]?>'><b>Pedido de material</b></a></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="/aulas/nuevo_presupuesto.php?id_aula=<?=$aulas["id_aula"];?>" title="Presupuesto docencia Aula"><img src="servicios.gif" border=0></a> <a href="listado_alumnos.php?id_aula=<?=$aulas["id_aula"];?>" title="Listado de Alumnos en Aula"><img src="alumnos.gif" border=0></a> <a href="#" onclick="window.open('fotos_alumnos.php?id_aula=<?=$aulas["id_aula"];?>','orla','toolbar=yes,width=800,height=600');" title="Orla de Alumnos en Aula"><img src="fotos.gif" border=0></a> <a href="editar_aula.php?o=1&id_aula=<?=$aulas["id_aula"];?>" title="Editar información del aula"><img src="modificar.gif" border=0></a><?php  if (($nivel_acceso==3) or ($nivel_acceso==4)) { ?> <a href="listado_alumnos_ayudas.php?id_aula=<?=$aulas["id_aula"];?>" title="Listado de alumnos para ayudas"><img src="alumnos_ayudas.gif" border=0></a><?php  } ?>
                                 <a href="/gestion/agenda_dia/calendario_programacion.php?id_aula=<?=$aulas["id_aula"];?>" title="Programación" target="_new"><img src="/imagen/calendario.gif" border=0></a> <a href="/gestion/agenda_ingles_dia/calendario_programacion.php?id_aula=<?=$aulas["id_aula"];?>" title="Programación Inglés" target="_new"><img src="/imagen/ingles.gif" border=0></a>  <a title="Examinar carpetas de informes" href="javascript:ver_carpeta(<?=$aulas["id_aula"]?>);"><img src="carpetas.gif" border=0></a></font></td>
                        </tr>
                         <?php 
                         }

                        $total_alumnos=$total_ninos+$total_ninas;
                        if ($total_alumnos>0) {
                                $total_ninos_t=$total_ninos." (".round((($total_ninos/$total_alumnos)*100),2)."%)";
                                $total_ninas_t=$total_ninas." (".round((($total_ninas/$total_alumnos)*100),2)."%)";
                                }
                        ?>
                        <tr>
                        <td colspan=7 align="center"><font size=2><b>Total:</b> <?=$total_alumnos?> - <b>Total actual niños:</b> <?=$total_ninos_t?> - <b>Total actual niñas:</b> <?=$total_ninas_t?></td>
                        </tr>
                        </table><br><br>                        
                        <input type="button" onclick="window.location.href='editar_aula.php?o=1'" value="Agregar grupo" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table><br><br>
<input type="button" value="Mostrar estadísticas de ocupación de los grupos" onclick="window.location.href='gestion_aulas.php?mostrar_estadisticas=1&fecha_seleccionada=<?=$fecha_seleccionada?>'">
<script language="Javascript">

function ver_carpeta(id_aula) {
        
        window.open('ver_carpeta_aula.php?id_aula='+id_aula,'carpetas','width=550,height=450toolbar=yes,scrollbars=yes,resizables=yes');
        
        }
        
function mostrar_ocupacion(tipo_listado,id_aula) {
        window.open("detalle_informe_ocupacion.php?fecha=<?=$fecha_seleccionada;?>&tipo="+tipo_listado+"&id_aula="+id_aula,"ventana_detalle","toolbar=no,width=750,height=550");
        }

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

        mes_presel=<?=date("m",$fecha_seleccionada);?>;
        dia_presel=<?=date("d",$fecha_seleccionada);?>;
        ano_presel=<?=date("Y",$fecha_seleccionada);?>;
        fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
        
        window.open('/herramientas/calendario_mes1.php?dia='+fecha_preseleccionada+'&pagina_destino=/gestion/centros/gestion_aulas.php','calendario','toolbar=no,resizable=no,width=200,height=216');
        }
</script>
<?php 
} else {
?>
<br><br>Error: <a href="/index.php">Volver a la página de inicio</a></br></br>
<?php 
}
 require("../../configuracion/parte_inferior.php"); ?>
