<?php require("../configuracion/parte_superior_privado_cocina.php");
require("inc/config_platos.php");

// Valores por defecto
// Valores de la tabla

$id_menu=$_GET["id_menu"];

if (strlen($id_menu)!=0) {

$consulta_articulo = db_query_params('SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=?', 's', array($id_menu)) or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$grupomenus=$datos_articulo["grupomenus"];
if (strlen($grupomenus)==0) {
        $grupomenus=0;
}

$nombre_menu=$datos_articulo["nombre_menu"];
if (strlen($nombre_menu)!=0) {
        $nombre_menu=urldecode($nombre_menu);
        }
$id_centro_menu=$datos_articulo["id_centro"];

$edad_ini_menu=$datos_articulo["edad_ini_menu"];
$edad_fin_menu=$datos_articulo["edad_fin_menu"];
$regimen=$datos_articulo["regimen"];
$texto_encabezado="Editar";
} else {
$id_menu=0;
$id_centro_menu=0;
$edad_ini_menu=0;
$edad_fin_menu=100;
$regimen=0;
}


?>
<script language="Javascript">

function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.nombre_menu.value=="") {
                errores=errores+"Falta introducir la nombre del menú.\n";
                }

        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.datos_familia.submit();
                }
        }
        
</script>
<?php 

?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_encabezado?> menú<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="modificar_menu1.php">
                        <input type="hidden" name="id_menu" value="<?=$id_menu?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_menu" value="<?=$nombre_menu?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Grupo:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="grupomenus" style="font-size: 10px">
                          <?php 
                          foreach ($gruposMenus as $claveGrupo=>$nombreGrupo) {
                          ?>
                          <option value="<?=$claveGrupo?>"<?php  if ($claveGrupo==$grupomenus) { print " selected"; } ?>><?=$nombreGrupo?></option>
                          <?php 
                                }
                          ?>
                          </select></font></td> 
                        </tr>                         
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Centro:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="id_centro_menu" size=1 style="font-size: 10px; text-align: right">
                          <option value="0"<?php  if ($id_centro_menu==0) { print " selected"; } ?>>Todos los centros</option>
                                <?php 
                                $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
                                while($centros=db_fetch_array($consulta_centros)){
                                ?>
                                <option value="<?=$centros["id_centro"];?>"<?php  if ($id_centro_menu==$centros["id_centro"]) { print " selected"; } ?>><?=$centros["nombre_centro"];?></option>
                                <?php 
                                }
                                ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Edad mínima menu:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="edad_ini_menu" style="text-align: right; font-size: 10px">
                          <?php 
                          for ($c1=0;$c1<=1.92;$c1=$c1+0.083) {
                          ?>
                          <option value="<?=round($c1,3)?>"<?php  if (round($c1,3)==$edad_ini_menu) { print " selected"; } ?>><?=round(($c1*12),0)?> meses</option>
                          <?php 
                                }
                          for ($c1=2;$c1<18;$c1++) {
                          ?>
                          <option value="<?=$c1?>"<?php  if ($c1==$edad_ini_menu) { print " selected"; } ?>><?=$c1?> años</option>
                          <?php 
                                }
                          ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Edad máxima menu:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="edad_fin_menu" style="text-align: right; font-size: 10px">
                          <?php 
                          for ($c1=0;$c1<=1.92;$c1=$c1+0.083) {
                          ?>
                          <option value="<?=round($c1,3)?>"<?php  if (round($c1,3)==$edad_fin_menu) { print " selected"; } ?>><?=round(($c1*12),0)?> meses</option>
                          <?php 
                                }
                          for ($c1=2;$c1<18;$c1++) {
                          ?>
                          <option value="<?=$c1?>"<?php  if ($c1==$edad_fin_menu) { print " selected"; } ?>><?=$c1?> años</option>
                          <?php 
                                }
                          ?>
                          <option value="100"<?php  if ($edad_fin_menu==100) { print " selected"; } ?>>>18 años</option>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de menú:</b></font></td>
                                <td bgcolor="#ffffff"><font size="1" face="<?= $fuente_general; ?>">
                                <input type="radio" name="regimen" value="0"<?php  if ($regimen==0) { print " checked"; } ?>> Normal <br>
                                <input type="radio" name="regimen" value="1"<?php  if ($regimen==1) { print " checked"; } ?>> Régimen<br>
                                <input type="radio" name="regimen" value="2"<?php  if ($regimen==2) { print " checked"; } ?>> Especial (Navidad, Fallas, Verano, Excursión, etc.)<br />
                                <input type="radio" name="regimen" value="3"<?php  if ($regimen==3) { print " checked"; } ?>> No asignado automáticamente por edades.</font></td>
                        </tr>
                        </form>
                         </table>
                         <br>
                         <!--<input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();">--> <input type="button" value="Cancelar" onclick="window.location.href='gestion_menu.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
