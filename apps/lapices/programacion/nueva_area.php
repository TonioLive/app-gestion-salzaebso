<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

// Valores por defecto
$nombre_area="";
$descripcion_area="";
$texto_accion="Nueva";

$id_area=$_GET["id_area"];

if (strlen($id_area)!=0) {
        $texto_accion="Editar";
        $busca_area=db_query("SELECT * FROM programacion_areas WHERE id_area=".$id_area.";") or die(db_error());

        $datos_area=db_fetch_array($busca_area);
        
        $orden_area=$datos_area["orden"];
        $nombre_area=stripslashes($datos_area["nombre_area"]);
        $descripcion_area=stripslashes($datos_area["descripcion_area"]);
        
        } else {
        
        $id_area=0;
        
        }

?>
<script language="Javascript">
function comprueba_y_envia() {
        var errores="";

        if (self.document.datos.nombre_area.value=="") {
                errores=errores+"Falta introducir la nombre del area.\n";
                }
        if (self.document.datos.descripcion_area.value=="") {
                errores=errores+"Falta introducir la descripción del area.\n";
                }

        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.datos.submit();
                }
        }

</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_accion?> area<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos" method="POST" action="nueva_area1.php">
                        <input type="hidden" name="id_area" value="<?=$id_area?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_area" value="<?=$nombre_area?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Descripción:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><textarea name="descripcion_area" cols=60 rows=10 style="font-size: 11px; font-family: Arial"><?=$descripcion_area?></textarea></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Posición, detrás de:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="posicion" cols=60 rows=10 style="font-size: 11px; font-family: Arial">
                                <option value=0>Primera de la lista</option>
                                <?php 
                                $busca_areas=db_query("SELECT * FROM programacion_areas WHERE id_area<>$id_area ORDER BY orden");

                                while($areas=db_fetch_array($busca_areas)){
                                        ?>
                                        <option value="<?=$areas["orden"]?>"<?php  if ($areas["orden"]==($orden_area-1)) { print " selected"; } ?>><?=$areas["nombre_area"]?></option>
                                        <?php 
                                        }
                                ?>
                          </select></font></td>
                        </tr>
                        </form>
                         </table><br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"> <input type="button" value="Cancelar" onclick="window.location.href='index.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
