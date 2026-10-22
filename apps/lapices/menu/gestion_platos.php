<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_privado_cocina.php"); ?>
<?php  require("inc/config_platos.php");

$grupo=$_GET["grupo"];
if (strlen($grupo)==0) {
        $grupo=0;
}

?>
<script language="Javascript">
        function eliminar(codigo) {
                if (confirm("¿Realmente quieres eliminar este plato?")) {
                        window.location.href="eliminar_plato.php?id_plato="+codigo;
                        }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=3 face="<?= $fuente_general; ?>"><b>Gestión de menús<br></b><small>Listado de Platos <b>Grupo: <?=$grupos[$grupo];?></b></small></font><br>
                <input type="button" value="Salir" onclick="window.location.href='gestion_grupos_platos.php'"> <input type="button" onclick="window.location.href='listado_platos_composicion.php'" value="Ver listado composición"> <input type="button" onclick="window.location.href='gestion_grupos_platos.php'" value="Cambiar grupo">

                        <form id="lista_platos" name="lista_platos">
                                <input type="hidden" value="<?=$grupo?>" name="grupo">				
                                <select id="nuevogrupo" name="nuevogrupo">
                                <?php
                                foreach ($grupos as $claveGrupo=>$nombreGrupo) {
                                ?>
                                <option value="<?=$claveGrupo?>"<?php  if ($claveGrupo==$grupo) { print " selected"; } ?>><?=$nombreGrupo?></option>
                                <?php
                                        }
                                ?>                                        
                                </select> <button type="button" id="mover_grupos" onclick="moverGrupos();">Mover platos marcados al grupo seleccionado</button>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_familia = db_query("SELECT * FROM menu_platos WHERE grupo=$grupo ORDER BY nombre_plato") or die (db_error());
                        
                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=5 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay platos, haz clic en el botón <i>"Nuevo plato"</i> para empezar a trabajar</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td><font size=1 face="<?=$fuente_general;?>"><input type="checkbox" id="checkAll"></font></td> 
                                <td><font size=1 face="<?=$fuente_general;?>"><b>ID</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Plato</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Tipo</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>Edad</b></font></td>
                                <td><font size=1 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        }
                        $tipo_actual="";
                        
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        // comprueba si hay articulos
                        $id_plato_actual=$datos_familia["id_plato"];
                        $consulta2="SELECT * FROM menu_calendario WHERE comida1=$id_plato_actual OR comida2=$id_plato_actual OR comida3=$id_plato_actual OR comida4=$id_plato_actual OR merienda1=$id_plato_actual OR merienda2=$id_plato_actual";
                        $count=db_query($consulta2);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_familia["id_plato"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"Solo se pueden eliminar platos que no se encuentren en la composición de los menús.\")'><font color=#999999>";
                                $link_elim2="</font></a>";
                                }
                        $nombre_plato=$datos_familia["nombre_plato"];
                                if (strlen($nombre_plato)!=0) {
                                        $nombre_plato=app_url_text_utf8($nombre_plato);
                                        }
$edad_ini_actual=$datos_familia["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_familia["edad_fin_plato"];
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
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><input type="checkbox" value="<?=$id_plato_actual?>" name="platos[]"></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$id_plato_actual;?></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_plato;?></font></td>
                                <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$tipos_plato[$datos_familia["tipo_plato"]];?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$edades;?></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="modificar_plato.php?id_plato=<?=$datos_familia["id_plato"];?>">Modificar</a> | <a href="composicion_plato.php?id_plato=<?=$datos_familia["id_plato"];?>">Composición</a> | <?=$link_elim1?>Eliminar<?=$link_elim2?></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                </form>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_grupos_platos.php'" style="font-size: 10px"> <input type="button" value="Nuevo plato" style="font-size: 10px" onclick="window.location.href='modificar_plato.php';">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<script>
        $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
        });

        function moverGrupos() {
                $('#lista_platos').attr('action', 'mover_platos.php');
                $('#lista_platos').attr('method', 'POST');
                $("#lista_platos").submit();
        }
</script>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
