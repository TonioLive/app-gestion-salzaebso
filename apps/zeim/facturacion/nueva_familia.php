<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

$error_duplicado=$_GET["duplicado"];

// Valores por defecto
$categoria="";
$porcentaje_iva="";

?>
<script language="Javascript">
<?php 
if (strlen($error_duplicado)!=0) {

$categoria=$_GET["categoria"];
if (strlen($categoria)!=0) {
        $categoria=urldecode($categoria);
        }
$porcentaje_iva=$_GET["porcentaje_iva"];
$tipo_servicios=$_GET["tipo_servicios"];

?>
alert("El nombre introducido ya se encuentra en uso, selecciona un nombredistinto.");
<?php 
}
?>
function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.categoria.value=="") {
                errores=errores+"Falta introducir la nombre de la familia.\n";
                }
        if (self.document.datos_familia.porcentaje_iva.value=="") {
                errores=errores+"Falta seleccionar el tipo de IVA de la familia.\n";
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
                <font size=2 face="<?= $fuente_general; ?>"><b>Nueva familia<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="nueva_familia1.php">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="categoria" value="<?=$categoria?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Tipo de IVA:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="porcentaje_iva" size=1 style="font-size: 10px">
                                <option value="">---</option>
                                <option value="0"<?php  if ($porcentaje_iva==0) { echo(" selected"); } ?>>0.00 %</option>
                                <option value="0.04"<?php  if ($porcentaje_iva==0.04) { echo(" selected"); } ?>>4.00 %</option>
                                <option value="0.07"<?php  if ($porcentaje_iva==0.07) { echo(" selected"); } ?>>7.00 %</option>
                                <option value="0.16"<?php  if ($porcentaje_iva==0.16) { echo(" selected"); } ?>>16.00 %</option>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Tipo de servicio:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="tipo_servicios" size=1 style="font-size: 10px">
                                <?php 
                                $busca_tipos=db_query("SELECT * FROM tipos_categorias ORDER BY id_tipo");
                                while($tipos=db_fetch_array($busca_tipos)){
                                ?>
                                <option value="<?=$tipos["id_tipo"];?>"<?php  if ($tipos["id_tipo"]==$tipo_servicios) { echo(" selected"); } ?>><?=$tipos["nombre_tipo_cat"];?></option>
                                <?php 
                                }
                                ?>
                          </select></font></td>
                        </tr>
                        </form>
                         </table><br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"> <input type="button" value="Cancelar" onclick="window.location.href='gestion_familias.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
