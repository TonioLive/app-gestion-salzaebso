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
                                $filtro_curso=" AND fecha>='$fecha_ini_curso_act' AND fecha<='$fecha_fin_curso_act'";
                                } else {
                                $filtro_curso="";
                                }

$mostrar_cerradas=$_GET["c"];
if ($mostrar_cerradas=="1") {
        $texto_titulo="Listado de incidencias de asistencia notificadas ya resuletas";
        $filtro_consulta="1";
        } else {
        $texto_titulo="Listado de incidencias de asistencia notificadas pendientes de resolver";
        $filtro_consulta="0";
        }
?>
<SCRIPT LANGUAGE=javascript>
<!--


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
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?=$texto_titulo;?><br><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <form name="listado_incidencias" method="POST">
        <?php 
        $busca_incidencias=db_query("SELECT * FROM servicios_consumidos WHERE incidencia=1 AND resuelta=$filtro_consulta$filtro_curso ORDER BY fecha");
        if (db_num_rows($busca_incidencias)==0) {
                ?>
                <tr>
                <td align="center" colspan="4"><font size=2><b>No hay incidencias para mostrar</b></font></td>
                </tr>
                <?php 
                }
        while($listado_incidencias=db_fetch_array($busca_incidencias)){
        // Busca datos del aula
        $id_aula=$listado_incidencias["id_servicio"];
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
        $datos_aula=db_fetch_array($consulta_datos_aula);
        $id_alumno=$listado_incidencias["id_alumno"];
        $busca_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=$id_alumno");
        $datos_alumno=db_fetch_array($busca_alumno)
        ?>
        <tr>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_incidencias["fecha"]))?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>">Asistencia</font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_aula["nombre"]?> - Alumno: <?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="detalles_incidencia_asistecia.php?id_incidencia=<?=$listado_incidencias["id_servicio_consumido"];?>"><font color="blue">Ver detalles</font></a></font></td>
        <?php 
        }
        ?>
        </form>
        </table>
        </td>
</tr>
</table>
<br><?php 
if ($mostrar_cerradas=="1") {
?><input type="button" onclick="window.location.href='gestion_incidencias.php'" value="Mostrar incidencias no resueltas" style="font-size: 10px"><?php 
} else { ?>
<input type="button" onclick="window.location.href='gestion_incidencias.php?c=1'" value="Mostrar incidencias resueltas" style="font-size: 10px"><?php  } ?> <input type="button" onclick="window.location.href='menu_incidencias.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

