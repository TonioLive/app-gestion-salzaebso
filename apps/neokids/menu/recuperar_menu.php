<?php  require("../configuracion/parte_superior_privado.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Menús</b></font>
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
                                // $filtro=" AND (id_curso=$id_curso_actual OR id_curso=0)";
                                $filtro="";
                                } else {
                                $filtro="";
                                }
                                
                        $consulta_aulas=db_query("SELECT * FROM menu_menus_borrados WHERE (id_centro=0 OR id_centro=$id_centro)$filtro ORDER BY edad_ini_menu;") or die(db_error());
                        if (db_num_rows($consulta_aulas)==0) {
                        ?>
                        <tr>
                        <td colspan=4 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay menús borrados</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Centro</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>"><b>Nombre menú</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Edad</b></font></td>
                                <td><font size=2 face="<?=$fuente_general;?>">&nbsp;</font></td>
                        </tr>
                        <?php 
                        }
                        while($aulas=db_fetch_array($consulta_aulas)){
                        if ($aulas["id_centro"]==0) {
                                $nombre_centro="Todos los centros";
                                } else {
                                $busca_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($aulas["id_centro"]));
                                if (db_num_rows($busca_nombre_centro)!=0) {
                                        $nombres_de_centro=db_fetch_array($busca_nombre_centro);
                                        $nombre_centro=$nombres_de_centro["nombre_centro"];
                                        } else {
                                        $nombre_centro="Desconocido";
                                        }
                                }
$edad_ini_actual=$aulas["edad_ini_menu"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$aulas["edad_fin_menu"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                        ?>
                        <tr valign="top" bgcolor="#ffffff">
                                <td width="15%"><font size=2 face="<?=$fuente_general;?>"><?=$nombre_centro?></font></td>
                                <td width="20%"><font size=2 face="<?=$fuente_general;?>"><?=urldecode($aulas["nombre_menu"])?></font></td>
                                <td align="center" width="15%"><font size=1 face="<?= $fuente_general; ?>"><?=$edades;?></font></td>
                                <td width="35%" align="center"><font size=2 face="<?=$fuente_general;?>"><input type="button" style="font-size: 10px" value="Recuperar" onclick="if (confirm('¿Desea recuperar el menú <?=urldecode($aulas["nombre_menu"])?>?')) { window.open('recuperar_menu1.php?id_menu_borrado=<?=$aulas["id_menu_borrado"]?>','borrar','toolbar=no'); }"> <input type="button" style="font-size: 10px" value="Eliminar definitivamente" onclick="if (confirm('¿Desea eliminar definitivamente el menú <?=urldecode($aulas["nombre_menu"])?>?')) { window.open('borrar_def_menu.php?id_menu_borrado=<?=$aulas["id_menu_borrado"]?>','borrar','toolbar=no'); }"></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr valign="top">
                                <td colspan=4 align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Salir" onclick="window.location.href='gestion_menu.php'" style="font-size:14px; width: 220; height: 32"></font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
<br>
</td>
</tr>
</table>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
