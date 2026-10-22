<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_privado_cocina_bootstrap.php");
include("../cocina/actualiza_comedor.php");
require("inc/config_platos.php");
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Menús</b></font>

                <div>

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                                <?php
                                $activa = ' class="active"';
                                foreach ($gruposMenus as $idGrupoMenu => $nombreGrupoMenu) {
                                        ?>
                                        <li role="presentation"<?=$activa?>><a href="#menus<?=$idGrupoMenu?>" aria-controls="menus<?=$idGrupoMenu?>" role="tab" data-toggle="tab">$nombreGrupoMenu</a></li>
                                        <?php
                                        $activa='';
                                }
                                ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                        <?php 
                        $id_centro=$centro_actual;
                        
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
                                // $filtro=" AND (id_curso=$id_curso_actual OR id_curso=0)";
                                $filtro="";
                                } else {
                                $filtro="";
                                }

        $filtro = " AND disponible_hasta>='".date('Y-m-d')."'";
        if ($_GET['todos']==1) {
                $filtro="";
        }
                          
                        // Por Grupos
                        $activa = ' active';
                        foreach ($gruposMenus as $idGrupoMenu => $nombreGrupoMenu) {

                        ?>
                        <div role="tabpanel" class="tab-pane<?=$activa?>" id="menus<?=$idGrupoMenu?>">
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php                
                        $consulta_aulas=db_query("SELECT * FROM menu_menus WHERE (id_centro=0 OR id_centro=$id_centro)$filtro ORDER BY disponible_desde DESC, edad_ini_menu;") or die(db_error());
                        if (db_num_rows($consulta_aulas)==0) {
                        ?>
                        <tr>
                        <td colspan=4 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay menús, haz clic en el botón <i>"Añadir nuevo menú"</i> para empezar a trabajar</b></font><br><br><br></td>
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
                                $busca_nombre_centro=db_query("SELECT * FROM centros WHERE id_centro=".$aulas["id_centro"]);
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

$colorCeldas = "#fff";

if ($aulas["disponible_desde"]>date("Y-m-d")) {
	$colorCeldas = "#ffcdcd";	
}

                        ?>
                        <tr valign="top" bgcolor="<?=$colorCeldas;?>">
                                <td width="25%"><font size=2 face="<?=$fuente_general;?>"><?=$nombre_centro?></font></td>
                                <td width="40%"><font size=2 face="<?=$fuente_general;?>"><?=app_url_text_utf8($aulas["nombre_menu"])?></font></td>
                                <td align="center" width="20%"><font size=1 face="<?= $fuente_general; ?>"><?=$edades;?></font></td>
                                <td width="15%" align="center"><font size=2 face="<?=$fuente_general;?>"><a href='calendario_menu.php?id_menu=<?=$aulas["id_menu"]?>'><img src='calendario.gif' border=0></a> <btn class="btn btn-primary btn-xs" onclick="window.open('/menu/calendario_menu_plantilla.php?id_menu=<?=$aulas["id_menu"]?>','plantilla','width=1024,height=768,toolbar=no,resizable=yes,scrollbars=yes')"><i class="fa fa-clipboard" aria-hidden="true"></i></btn> <a href='modificar_menu.php?id_menu=<?=$aulas["id_menu"]?>'><img src='modificar.gif' border=0></a> <a href='#' onclick="if (confirm('¿Desea eliminar el menú <?=app_url_text_utf8($aulas["nombre_menu"])?>?')) { window.open('borrar_menu.php?id_menu=<?=$aulas["id_menu"]?>','borrar','toolbar=no'); }"><img src='borrar.gif' border=0></a></font></td>
                        </tr>
                        <?php 
                                }
                                $activa='';
                                ?>
                        </table>                        
                </div>

                                <?php
                        }                        
                        ?>
                        </div>

                        </div>                         
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td colspan=4 align="center"><font size=2 face="<?= $fuente_general; ?>">
                                <?php
                                if ($_GET['todos']==1) {
                                        ?>
                                        <button type="button" onclick="window.location.href='gestion_menu.php?todos=0'" style="font-size:14px; width: 220; height: 32">Ver solo menús activos</button>
                                        <?php
                                } else {
                                        ?>
                                        <button type="button" onclick="window.location.href='gestion_menu.php?todos=1'" style="font-size:14px; width: 220; height: 32">Ver todos los menús</button>
                                        <?php
                                }
                                ?>
                                
                                <input type="button" value="Añadir nuevo menú" onclick="window.location.href='modificar_menu.php'" style="font-size:14px; width: 220; height: 32"> <input type="button" value="Recuperar menú borrado" onclick="window.location.href='recuperar_menu.php'" style="font-size:14px; width: 220; height: 32"></font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
<br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Platos y alimentos</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Alimentos" onclick="window.location.href='gestion_alimentos.php'" style="font-size:14px; width: 220; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de las tablas de información nutricional de los alimentos.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Platos" onclick="window.location.href='gestion_grupos_platos.php'" style="font-size:14px; width: 220; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Elaboración de los platos que componen los distintos menús.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Buscar platos" onclick="window.location.href='busqueda_ingrediente.php'" style="font-size:14px; width: 220; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Busca platos que contengan un determinado ingrediente.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
