<?php  require("../configuracion/parte_superior_privado_ancho_cocina_bootstrap.php");
include("../cocina/actualiza_comedor.php");
require("inc/config_platos.php");

$grupomenus=$_GET["grupomenus"];
if (strlen($grupomenus)==0) {
        $grupomenus=1;
}

?>
<div class="container">
        <form id="lista_menus" name="lista_menus">
        <input type="hidden" value="<?=$grupomenus?>" name="grupomenus">
        <div class="row">

                <div class="col-md-12">
                
                        <h2>Menús</h2>

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                                <?php
                                foreach ($gruposMenus as $idGrupoMenu => $nombreGrupoMenu) {
                                        $activa='';                                        
                                        if ($idGrupoMenu==$grupomenus) {
                                                $activa = ' class="active"';
                                        }
                                        ?>
                                        <li role="presentation"<?=$activa?>><a href="#menus<?=$idGrupoMenu?>" aria-controls="menus<?=$idGrupoMenu?>" role="tab" data-toggle="tab"><?=$nombreGrupoMenu?></a></li>
                                        <?php                                }
                                ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
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

                                $filtro = " AND disponible_hasta>='".date('Y-m-d')."'";                                
                                if ($_GET['todos']==1) {
                                        $filtro="";
                                }            
                                                // Por Grupos                                
                                                foreach ($gruposMenus as $idGrupoMenu => $nombreGrupoMenu) {
                                                        $activa = ''; 
                                                        if ($idGrupoMenu==$grupomenus) {
                                                                $activa = ' active';    
                                                        }
                                                ?>
                                                <div role="tabpanel" class="tab-pane<?=$activa?>" id="menus<?=$idGrupoMenu?>">
                                                        <table class="table table-bordered table-striped">
                                                        <?php    
                                                        $consulta_aulas=db_query("SELECT * FROM gestionguarderias.menu_menus WHERE (id_centro=0 OR id_centro=$id_centro) AND grupomenus=".$idGrupoMenu.$filtro." ORDER BY disponible_desde DESC, edad_ini_menu;") or die(db_error());
                                                        if (db_num_rows($consulta_aulas)==0) {
                                                        ?>
                                                        <tr>
                                                                <td colspan=4 class="text-center"><br><br>No hay menús en el grupo <b>"<?=$nombreGrupoMenu?>"</b>, haz clic en el botón <i>"Añadir nuevo menú"</i> o mueve menús de otros grupos a este para empezar a trabajar<br><br><br></td>
                                                        </tr>
                                                        <?php 
                                                        } else {
                                                        ?>
                                                        <tr>
                                                                <td><input type="checkbox" id="checkAll"> <b>Nombre menú</b></td>
                                                                <td><b>Centro</b></td>
                                                                <td><b>Edad</b></td>
                                                                <td>&nbsp;</td>
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

                                                        $colorCeldas = "";

                                                        if ($aulas["disponible_desde"]>date("Y-m-d")) {
                                                                $colorCeldas = ' class="danger"';	
                                                        }

                                                        ?>
                                                        <tr<?=$colorCeldas;?>>
                                                                <td class="col-md-4"><input type="checkbox" value="<?=$aulas["id_menu"]?>" name="menus[]"> <?=urldecode($aulas["nombre_menu"])?></td>
                                                                <td class="col-md-3"><?=$nombre_centro?></td>
                                                                <td class="text-center col-md-4"><?=$edades;?></td>
                                                                <td class="text-center col-md-1"><a href='calendario_menu.php?id_menu=<?=$aulas["id_menu"]?>'><img src='calendario.gif' border=0></a></td>
                                                        </tr>
                                                        <?php 
                                                        }
                                                        ?>
                                                        </table>                                                                        
                                                </div> <!-- TAB PANEL -->
                                                <?php
                                                }                        
                                                ?>
                        </div> <!-- TAB CONTENT -->

                </div> <!-- col-md-12 -->     
        </div> <!-- row --> 

        <div class="row">
                
                <div class="col-md-6 text-center">
                        <table class="table">  
                                <tr>
                                        <td class="text-center col-md-12">
                                        <select id="nuevogrupo" name="nuevogrupo">
                                        <?php
                                foreach ($gruposMenus as $idGrupoMenu => $nombreGrupoMenu) {
                                        ?>
                                        <option value="<?=$idGrupoMenu?>"<?php  if ($idGrupoMenu==$grupomenus) { print " selected"; } ?>><?=$nombreGrupoMenu?></option>
                                        <?php
                                                }
                                        ?>                                        
                                        </select> <button type="button" id="mover_grupos" onclick="moverGrupos();" style="font-size:14px;">Mover platos marcados al grupo seleccionado</button>
                                        </td>
                                </tr>                                
                                <tr>  
                                        <td class="text-center col-md-12">                    
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
                                        </td>
                                </tr>
                        </table>
                </div>
                <div class="col-md-6 text-center">
                        <table class="table">
                                <tr>
                                        <td class="col-md-2"><input type="button" value="Alimentos" onclick="window.location.href='gestion_alimentos.php'" style="font-size:14px; width: 220; height: 32"></td>
                                        <td class="col-md-8">Gestión de las tablas de información nutricional de los alimentos.</td>
                                </tr>
                                <tr>
                                        <td width=210><input type="button" value="Platos" onclick="window.location.href='gestion_grupos_platos.php'" style="font-size:14px; width: 220; height: 32"></td>
                                        <td>Elaboración de los platos que componen los distintos menús.</td>
                                </tr>
                                <tr>
                                        <td width=210><input type="button" value="Buscar platos" onclick="window.location.href='busqueda_ingrediente.php'" style="font-size:14px; width: 220; height: 32"></td>
                                        <td>Busca platos que contengan un determinado ingrediente.</td>
                                </tr>
                        </table>
                </div>
        </div> <!-- row --> 
        </form>
</div> <!-- container --> 
<script>
        $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
        });

        function moverGrupos() {
                $('#lista_menus').attr('action', 'mover_menus.php');
                $('#lista_menus').attr('method', 'POST');
                $("#lista_menus").submit();
        }
</script>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
