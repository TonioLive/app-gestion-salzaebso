<?php  require("../configuracion/parte_superior_docente.php"); ?>
<?php 

$formulario_name="centros";
$formulario_accion="editar_centro2.php";

$id_centro=$centro_actual;

$fecha_seleccionada=$_GET["fecha_seleccionada"];

if (strlen($fecha_seleccionada)==0) {
        $fecha_seleccionada=time();
        }

$url_retorno="/aulas/gestion_aulas.php";
        
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
                $id_curso_actual=0;
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
                        $busca_listado_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_fin>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", time() - 365 * 86400 * 4))));

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Aulas del Centro <i><?=$db_nombre_centro?></i></b><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=6 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listado de aulas</b></font></td>
                        </tr>
                         <?php 
                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro$filtro".$filtroViAulas." ORDER BY id_curso,nombre;") or die(db_error());
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
                        $consulta_ocupacion=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=?', 'sss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)), (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);

                        // Calcula ocupación total
                        $consulta_ocupacion_total=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=?', 's', array($aulas["id_aula"])) or die(db_error()."-1");
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
                        /*
                        if ($fecha_fin_curso>=date("Y-m-d")) {
                                $curso_actual_o_futuro=true;
                                } else {
                                $curso_actual_o_futuro=false;
                                }
                                */
                        $curso_actual_o_futuro=true;
                        
                        // Comprueba si el aula es de 0-1
                        if ($aulas["edad_minima_prog"]==0) {
                                // $curso_actual_o_futuro=false;
                                }

                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=6 align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>">Curso: <?=$nombre_curso_academico;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Mín.</b></font></td>
                                <td align="center" bgcolor="#efefef"><a href="javascript:selecciona_fecha();" title="Selecciona la fecha en el calendario"><font size=1 face="<?= $fuente_general; ?>"><font size=1><img src="/imagen/calendario.gif" border=0 align="right"><b>Ocupación<br><?=date("d/m/Y",$fecha_seleccionada);?></b></font></a>
                                <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <tr>
                                <td width=33% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Hoy</font></td>
                                <td width=33% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Asistencia</font></td>
                                <td width=34% align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000">Máxima</font></td>
                                </tr>
                                </table></td>
                                <!-- <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Promedio edades</b></font></td> -->
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
                                <td align="center" bgcolor="#ffffff"><table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <tr>
                                <td width=33% align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_actual;?></font></td>
                                <td width=33% align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy;?></font></td>
                                <td width=34% align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_total;?></font></td>
                                </tr>
                                </table><font size=1><?=$mensaje_ocupacion;?></font></td>
                                <!-- <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$promedio_edades;?> meses</font></td> -->
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href='/pedidos/introducir_nuevo_pedido.php?id_cliente=<?=$aulas["id_aula"]?>'><b>Pedido de material</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><!--<a href="gestion_docentes.php?id_aula=<?=$aulas["id_aula"];?>" title="Gestión de docentes del Aula">D</a> --><a href="listado_alumnos.php?id_aula=<?=$aulas["id_aula"];?>" title="Listado de Alumnos en Aula"><img src="alumnos.gif" border=0></a><?php  if ($curso_actual_o_futuro==true) { ?> <a href="/gestion/agenda_dia/calendario_programacion.php?id_aula=<?=$aulas["id_aula"];?>" title="Programación" target="_new"><img src="/imagen/calendario.gif" border=0> <a href="/gestion/agenda_ingles_dia/calendario_programacion.php?id_aula=<?=$aulas["id_aula"];?>" title="Programación Inglés" target="_new"><img src="/imagen/ingles.gif" border=0></a></a><?php  } ?></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                        </table><br>
                        <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table><br><br>
<script language="Javascript">
function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

        mes_presel=<?=date("m",$fecha_seleccionada);?>;
        dia_presel=<?=date("d",$fecha_seleccionada);?>;
        ano_presel=<?=date("Y",$fecha_seleccionada);?>;
        fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;

        window.open('/herramientas/calendario_mes1.php?dia='+fecha_preseleccionada+'&pagina_destino=/aulas_docentes/listado_aulas.php','calendario','toolbar=no,resizable=no,width=200,height=216');
        }
</script>
<?php 
} else {
?>
<br><br>Error: <a href="/index.php">Volver a la página de inicio</a></br></br>
<?php 
}
 require("../configuracion/parte_inferior.php"); ?>
