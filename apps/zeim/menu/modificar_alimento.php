<?php  require("../configuracion/parte_superior_privado_cocina.php"); ?>
<?php 

// Valores por defecto
// Valores de la tabla



$array_valores_alimentos=array();



$array_valores_alergias=array();

$id_alimento=$_GET["id_alimento"];

if (strlen($id_alimento)!=0) {

$consulta_articulo = db_query_params('SELECT * FROM gestionguarderias.menu_alimentos WHERE id_alimento=?', 's', array($id_alimento)) or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$nombre_alimento=$datos_articulo["nombre_alimento"];
if (strlen($nombre_alimento)!=0) {
        $nombre_alimento=urldecode($nombre_alimento);
        }
$kcal_gramo=$datos_articulo["kcal_gramo"];
$hc_gramo=$datos_articulo["hc_gramo"];
$prot_gramo=$datos_articulo["prot_gramo"];
$grasas_gramo=$datos_articulo["grasas_gramo"];
$grasas_sat_gramo=$datos_articulo["grasas_sat_gramo"];
$azucar_gramo=$datos_articulo["azucar_gramo"];
$sodio_mg=$datos_articulo["sodio_mg"];
$unidad_medida=$datos_articulo["unidad_medida"];
$peso_unidad=$datos_articulo["peso_unidad"];

                for ($c=0;$c<count($array_alimentos);$c++) {
                        $array_valores_alimentos[$c]=$datos_articulo[$array_alimentos[$c]];
                        }
                for ($c=0;$c<count($array_alergias);$c++) {
                        $array_valores_alergias[$c]=$datos_articulo[$array_alergias[$c]];
                        }

$texto_encabezado="Editar";
} else {
$id_alimento=0;
$texto_encabezado="Nuevo";
$kcal_gramo="0.00";
$hc_gramo="0.00";
$prot_gramo="0.00";
$grasas_gramo="0.00";
$grasas_sat_gramo="0.00";
$azucar_gramo="0.00";
$sodio_mg="0.00";
$unidad_medida=0;
$peso_unidad="1.00";
}


?>
<script language="Javascript">

function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.nombre_alimento.value=="") {
                errores=errores+"Falta introducir la nombre del alimento.\n";
                }
        if (self.document.datos_familia.peso_unidad.value=="") {
                errores=errores+"Falta introducir el valor del peso de la unidad.";
                } else {
                self.document.datos_familia.peso_unidad.value=self.document.datos_familia.peso_unidad.value.replace(",",".");
                if (isNaN(self.document.datos_familia.peso_unidad.value)) {
                        errores=errores+"El valor del peso de la unidad debe ser un número\n";
                        }
                }
        if (self.document.datos_familia.kcal_gramo.value=="") {
                errores=errores+"Falta introducir el valor de KCal/100 gr.\n";
                } else {
                self.document.datos_familia.kcal_gramo.value=self.document.datos_familia.kcal_gramo.value.replace(",",".");
                if (isNaN(self.document.datos_familia.kcal_gramo.value)) {
                        errores=errores+"El valor de KCal/100 gr. debe ser un número\n";
                        }
                }
        if (self.document.datos_familia.hc_gramo.value=="") {
                errores=errores+"Falta introducir el valor de Hidratos de Carbono.\n";
                } else {
                self.document.datos_familia.hc_gramo.value=self.document.datos_familia.hc_gramo.value.replace(",",".");
                if (isNaN(self.document.datos_familia.hc_gramo.value)) {
                        errores=errores+"El valor de Hidratos de Carbono debe ser un número\n";
                        }
                }
        if (self.document.datos_familia.prot_gramo.value=="") {
                errores=errores+"Falta introducir el valor de proteinas.\n";
                } else {
                self.document.datos_familia.prot_gramo.value=self.document.datos_familia.prot_gramo.value.replace(",",".");
                if (isNaN(self.document.datos_familia.prot_gramo.value)) {
                        errores=errores+"El valor de proteinas debe ser un número\n";
                        }
                }
        if (self.document.datos_familia.grasas_gramo.value=="") {
                errores=errores+"Falta introducir el valor de grasas.\n";
                } else {
                self.document.datos_familia.grasas_gramo.value=self.document.datos_familia.grasas_gramo.value.replace(",",".");
                if (isNaN(self.document.datos_familia.grasas_gramo.value)) {
                        errores=errores+"El valor de grasas debe ser un número\n";
                        }
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
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_encabezado?> alimento<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="modificar_alimento1.php">
                        <input type="hidden" name="id_alimento" value="<?=$id_alimento?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_alimento" value="<?=$nombre_alimento?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Peso unidad:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="peso_unidad" value="<?=$peso_unidad?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">KCal:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="kcal_gramo" value="<?=$kcal_gramo?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">H.C.:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="hc_gramo" value="<?=$hc_gramo?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Proteinas:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="prot_gramo" value="<?=$prot_gramo?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Grasas:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="grasas_gramo" value="<?=$grasas_gramo?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Grasas saturadas:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="grasas_sat_gramo" value="<?=$grasas_sat_gramo?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Azúcares:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="azucar_gramo" value="<?=$azucar_gramo?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Sodio:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="sodio_mg" value="<?=$sodio_mg?>" size=6 style="font-size: 10px; text-align: right"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Unidad de medida:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="unidad_medida" size=1 style="font-size: 10px; text-align: right">
                          <option value="0"<?php  if ($unidad_medida==0) { print " selected"; } ?>>Masa: g</option>
                          <option value="1"<?php  if ($unidad_medida==1) { print " selected"; } ?>>Volum: ml</option>
                          </select></font></td>
                        </tr>
                                <tr vAlign="top">
                                        <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Introducción de alimentos:</font></td>
                                        <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>">
                                        <?php 
                                        // Alimentos
                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                        ?>
                                        <input type="checkbox" name="<?=$array_alimentos[$c]?>" value="1"<?php  if ($array_valores_alimentos[$c]==1) { print " checked"; } ?>><?=$array_nombres_alimentos[$c];?><br>
                                        <?php 
                                        }
                                        ?></font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Alergias alimentos:</font></td>
                                        <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>">
                                        <?php 
                                        // alergias
                                        for ($c=0;$c<count($array_alergias);$c++) {
                                        ?>
                                        <input type="checkbox" name="<?=$array_alergias[$c]?>" value="1"<?php  if ($array_valores_alergias[$c]==1) { print " checked"; } ?>><?=$array_nombres_alergias[$c];?><br>
                                        <?php 
                                        }
                                        ?></font></td>
                                </tr>
                        </form>
                         </table>
                         <br>
                         <!--<input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();">--> <input type="button" value="Cancelar" onclick="window.location.href='gestion_alimentos.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
