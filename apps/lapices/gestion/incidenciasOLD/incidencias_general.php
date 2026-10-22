<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $fecha_ini_curso_act=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso_act=$datos_curso_actual["fecha_fin"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $fecha_ini_curso_act=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso_act=$datos_curso_actual["fecha_fin"];
                }
        }
        if ($id_curso_actual!=0) {
                                $filtro_curso=" AND fecha_incidencia>='$fecha_ini_curso_act' AND fecha_incidencia<='$fecha_fin_curso_act'";
                                } else {
                                $filtro_curso="";
                                }

$mostrar_todas=$_GET["todas"];
if ($mostrar_todas==1) {
        $filtro_curso="";
        }

$mostrar_cerradas=$_GET["c"];
if ($mostrar_cerradas=="1") {
        $texto_titulo="Listado de incidencias notificadas ya resueltas";
        $filtro_consulta="1";
        } else {
        $texto_titulo="Listado de incidencias notificadas pendientes de resolver";
        $filtro_consulta="0";
        }
?>
<SCRIPT LANGUAGE=javascript>
<!--

function eliminar_incidencia(id_elim_incidencia) {
        if (confirm("¿Deseas eliminar la incidencia seleccionada?")) {
                window.location.href='eliminar_incidencia.php?c=<?=$filtro_consulta;?>&id_incidencia='+id_elim_incidencia;
                }
        }

//-->
</SCRIPT>
<div align="center"><center>
<form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="c" value="<?=$mostrar_cerradas;?>">
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
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<tr bgcolor="#FFFFFF">
        <td align="center">
        <form name="listado_incidencias" method="POST">
        <?php 
        $busca_incidencias=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=$filtro_consulta$filtro_curso ORDER BY incidencias.tipo, incidencias.fecha_incidencia");
        if (db_num_rows($busca_incidencias)==0) {
                ?>
                <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                <tr>
                <td align="center" colspan="4"><font size=2><b>No hay incidencias para mostrar</b></font></td>
                </tr>
                </table>
                <?php 
                }
        $tipo_actual=-1;
        
        while($listado_incidencias=db_fetch_array($busca_incidencias)){
        
        if ($tipo_actual!=$listado_incidencias["tipo"]) {
        
        if ($tipo_actual!=-1) {
                ?>
                </table>
                </div>
                <div id="cerrado<?=$tipo_actual?>" style="display:block">
                <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                <tr>
                <td align="left" colspan="4"><br><font size=2><a href="javascript:abrir(<?=$tipo_actual;?>)" title="Mostar incidencias"><img src="mas.gif" border=0 align="absmiddle"></a> Incidencias de <b><?=$nombre_tipo_actual;?></b> (<?=$contador_actual;?>)</font></td>
                </tr>
                </table>
                </div>
                <?php 
                }
        
        $nombre_tipo_actual=$listado_incidencias["nombre_tipo"];
        $tipo_actual=$listado_incidencias["tipo"];
        $contador_actual=0;
        ?>
        <div id="abierto<?=$tipo_actual?>" style="display:none">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td align="left" colspan="4"><br><font size=2><a href="javascript:cerrar(<?=$tipo_actual;?>)" title="Ocultar incidencias"><img src="menos.gif" border=0 align="absmiddle"></a> Incidencias de <b><?=$nombre_tipo_actual;?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Notificada por...</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Seguimiento realizado por...</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        }
        
        // Busca nombre de la persona que notifica
        
        $busca_notificador=db_query("SELECT * FROM usuarios WHERE id_usuario=".$listado_incidencias["notificada_por"]);
        
        if (db_num_rows($busca_notificador)!=0) {
                $datos_notificador=db_fetch_array($busca_notificador);
                $nombre_notificador=$datos_notificador["Apellido1"]." ".$datos_notificador["Apellido2"].", ".$datos_notificador["Nombre"];
                } else {
                $nombre_notificador="Desconocido";
                }
        
        
        // Busca nombre de la persona que hace el seguimiento
        
        $busca_seguimiento=db_query("SELECT * FROM usuarios WHERE id_usuario=".$listado_incidencias["seguimiento_por"]);

        if (db_num_rows($busca_seguimiento)!=0) {
                $datos_seguimiento=db_fetch_array($busca_seguimiento);
                $nombre_seguimiento=$datos_seguimiento["Apellido1"]." ".$datos_seguimiento["Apellido2"].", ".$datos_seguimiento["Nombre"];
                } else {
                $nombre_seguimiento="Desconocido";
                }
                
        ?>
        <tr>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_incidencias["fecha_incidencia"]))?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_notificador;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_seguimiento;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="detalles_incidencia_general.php?id_incidencia=<?=$listado_incidencias["id_incidencia"];?>"><font color="blue">Ver detalles</font></a> | <a href="javascript:eliminar_incidencia(<?=$listado_incidencias["id_incidencia"];?>)"><font color="blue">Eliminar</font></a></font></td>
        <?php 
        $contador_actual++;
        }
        
        if ($tipo_actual!=-1) {
                ?>
                </table>
                </div>
                <div id="cerrado<?=$tipo_actual?>" style="display:block">
                <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                <tr>
                <td align="left" colspan="4"><br><font size=2><a href="javascript:abrir(<?=$tipo_actual;?>)" title="Mostar incidencias"><img src="mas.gif" border=0 align="absmiddle"></a> Incidencias de <b><?=$nombre_tipo_actual;?></b> (<?=$contador_actual;?>)</font></td>
                </tr>
                </table>
                </div>
                <?php 
                } else {
                ?>
                <div id="cerrado<?=$tipo_actual?>" style="display:block">
                <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                <tr>
                <td align="left" colspan="4"><br><font size=2><a href="javascript:abrir(<?=$tipo_actual;?>)" title="Mostar incidencias"><img src="mas.gif" border=0 align="absmiddle"></a> Incidencias de <b><?=$nombre_tipo_actual;?></b> (<?=$contador_actual;?>)</font></td>
                </tr>
                </table>
                </div>
                <?php 
                }
        ?>
        </form>
        </td>
</tr>
</table>
<br><?php 
if ($mostrar_cerradas=="1") {
?><input type="button" onclick="window.location.href='incidencias_general.php'" value="Mostrar incidencias no resueltas" style="font-size: 10px"><?php 
} else { ?>
<input type="button" value="Mostrar las incidencias de cualquier curso" onclick="window.location.href='incidencias_general.php?todas=1';" style="font-size: 10px"> <input type="button" onclick="window.location.href='incidencias_general.php?c=1'" value="Mostrar incidencias resueltas" style="font-size: 10px"><?php  } ?> <input type="button" onclick="window.location.href='menu_incidencias.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<script language="JavaScript">
function abrir(capaincidencias) {
        document.getElementById('cerrado'+capaincidencias).style.display='none';
        document.getElementById('abierto'+capaincidencias).style.display='block';
        }
function cerrar(capaincidencias) {
        document.getElementById('abierto'+capaincidencias).style.display='none';
        document.getElementById('cerrado'+capaincidencias).style.display='block';
        }
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>

