<?php  require("../configuracion/parte_superior_docente.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Aulas</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
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
                                $filtro=" AND (id_curso=$id_curso_actual OR id_curso=0)";
                                } else {
                                $filtro="";
                                }

                        $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE tipo<2".$filtroViAulas." AND id_centro=$id_centro$filtro ORDER BY id_curso,nombre;") or die(db_error());
                        while($aulas=db_fetch_array($consulta_aulas)){
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia <?=$aulas["nombre"]?>" onclick="window.location.href='calendario_mes.php?id_aula=<?=$aulas["id_aula"]?>'" style="font-size:14px; width: 220; height: 32"></font></td>
                                <td align="right"><input type="button" value="Fotos <?=$aulas["nombre"]?>" onclick="window.location.href='/gestion/galerias_imagenes/aula/index.php?id_aula=<?=$aulas["id_aula"]?>'" style="font-size:14px; width: 220; height: 32"></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia Extras" onclick="window.location.href='calendario_mes_extras.php'" style="font-size:20px; width: 220; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Control de asistencia de alumnos con Hora extra mensual</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Informe asistencia" onclick="window.location.href='/gestion/centros/informe_ocupacion_aulas.php'" style="font-size:20px; width: 220; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Informe de los alumnos que han asistido al centro.</font></td>
                        </tr>
                        <!--
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input disabled type="button" value="Listado Alumnos" onclick="window.location.href='<?=$url_web;?>/alumnos/listado_alumnos.php'" style="font-size:20px; width: 220; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Listado de alumnos inscritos por cursos.</font></td>
                        </tr>
                        -->
                         </table>
                </td>
        </tr>
</table>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Actividades del curso: </b> <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();"><?php 
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

                        $consulta_actividades=db_query("SELECT * FROM tarifa WHERE (ID_CENTRO_ASISTENCIA like '%$id_centro%') AND CONTROL_ASISTENCIA=1$filtro ORDER BY ARTICULO;") or die(db_error());
                        while($actividades=db_fetch_array($consulta_actividades)){
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
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia <?=$actividades["ARTICULO"]?>" onclick="window.location.href='calendario_mes_actividad.php?id_actividad=<?=$actividades["ID"]?>'" style="font-size:<?=$tam_texto;?>px; width: 230; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Control de asistencia a la actividad <b><?=$actividades["ARTICULO"]?></b>. <?php 
                                if ($actividades["FECHA_INICIO_ASISTENCIA"]!=$actividades["FECHA_FIN_ASISTENCIA"]) {
                                ?>Del día <?=date("d/m/Y", strtotime($actividades["FECHA_INICIO_ASISTENCIA"]))?> al día <?=date("d/m/Y", strtotime($actividades["FECHA_FIN_ASISTENCIA"]))?><?php 
                                } else {
                                ?>El día <?=date("d/m/Y", strtotime($actividades["FECHA_INICIO_ASISTENCIA"]))?><?php 
                                }?>.</font></td>
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
 require("../configuracion/parte_inferior.php"); ?>
