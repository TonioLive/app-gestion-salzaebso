<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_privado_cocina.php"); ?>
<?php  require("inc/config_platos.php"); ?>
<?php 

// Valores por defecto
// Valores de la tabla

$id_plato=$_GET["id_plato"];

if (strlen($id_plato)!=0) {

$consulta_articulo = db_query("SELECT * FROM menu_platos WHERE id_plato=$id_plato") or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$nombre_plato=$datos_articulo["nombre_plato"];
if (strlen($nombre_plato)!=0) {
        $nombre_plato=app_url_text_utf8($nombre_plato);
        }
$tipo_plato=$datos_articulo["tipo_plato"];
$edad_ini_plato=$datos_articulo["edad_ini_plato"];
$edad_fin_plato=$datos_articulo["edad_fin_plato"];

$texto_encabezado="Editar";
} else {
$id_plato=0;
$texto_encabezado="Nuevo";
}


?>
<script language="Javascript">

function comprueba_y_envia() {
        var errores="";

        if (self.document.datos_familia.id_alimento.value=="") {
                errores=errores+"Falta seleccionar el ingrediente.\n";
                }
        cantidad=self.document.datos_familia.cantidad_racion.value
        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        self.document.datos_familia.cantidad_racion.value=cantidad;
                        if (isNaN(cantidad)) {
                                errores=errores+"La cantidad debe debe ser un valor numérico.\u000A";
                                }
                        } else {
                                errores=errores+"Falta introducir la cantidad.\u000A";
                        }
        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.datos_familia.submit();
                }
        }
function comprueba_cantidad_articulo() {
        cantidad=self.document.datos_familia.cantidad_racion.value
        if (cantidad!="") {
                        cantidad=cantidad.replace(",",".");
                        self.document.datos_familia.cantidad_racion.value=cantidad;
                        if (isNaN(cantidad)) {
                                errores=errores+"La cantidad debe debe ser un valor numérico.\u000A";
                                }
                        }
        if (errores!="") {
                alert(errores);
                }
        }
</script>
<?php 

?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Composición del plato<br><br><?=$nombre_plato;?><br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <form name="datos_familia" method="POST" action="composicion_plato1.php">
                        <input type="hidden" name="id_plato" value="<?=$id_plato?>">
                        <tr>
                        <td bgcolor="#ececec" align="center"><font size=1><b>Ingrediente</b></font></td>
                        <td bgcolor="#ececec" align="center"><font size=1><b>Cantidad por niño</b></font></td>
                        <td bgcolor="#ececec" align="center"><font size=1><b>Preparar separado</b></font></td>
                        <td bgcolor="#ececec" align="center"><font size=1><b>Indicación cantidad*</b></font></td>
                        </tr>
                        <tr>
                                <td bgcolor="#ffffff" align="center"><font size=1><select name="id_alimento" size=1 style="font-size: 10px">
                                <option value=""></option>
                                <?php 
                                $busca_articulos=db_query("SELECT * FROM menu_alimentos ORDER BY nombre_alimento");

                                while($listado_articulos=db_fetch_array($busca_articulos)) {
                                // Busca si esta
                                $busca_ing=db_query("SELECT * FROM menu_ingredientes WHERE id_plato=$id_plato AND id_alimento=".$listado_articulos["id_alimento"]);
                                if (db_num_rows($busca_ing)==0) {
                                        ?>
                                        <option value="<?=$listado_articulos["id_alimento"]?>"><?=app_url_text_utf8($listado_articulos["nombre_alimento"])?></option>
                                        <?php 
                                        }
                                }
                                ?>
                                </select></font></td>
                                <td bgcolor="#ffffff" align="center"><font size=1><input type="text" name="cantidad_racion" size=5 style="font-size: 10px;text-align: right" onblur="comprueba_cantidad_articulo();"></td>
                                <td bgcolor="#ffffff" align="center"><font size=1><input type="radio" name="preparar_separado" value=0 checked> No <input type="radio" name="preparar_separado" value=1> Si</font></td>
                                <td bgcolor="#ffffff" align="center"><font size=1><input type="text" name="cantidad_medida" size=12 style="font-size: 10px"> <input type="button" onclick="comprueba_y_envia()" style="font-size: 10px" value="Introducir"></td>
                        </tr>
                        </form>
                         </table>
                         <font size=1>* Utilizar solo cuando sea necesario indicar a la educadora o a la cocinera de una forma más clara<br>la cantidad a servir por cada niño. Por ejemplo, 1 magdalena en lugar de 100 gr. de magdalenas.</font>
                         <br><br>
                         <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                                <tr bgcolor="#efefef" valign="top">
                                        <td width="20%"><font size=1 face="<?=$fuente_general;?>"><b>Ingrediente</b></font></td>
                                        <td align="left" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad/niño</b></font></td>
                                        <td align="left" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>KCal/Ración</b></font></td>
                                        <td align="center" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>H.C.</b></font></td>
                                        <td align="center" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>Proteinas</b></font></td>
                                        <td align="center" width="15%"><font size=1 face="<?= $fuente_general; ?>"><b>Grasas</b></font></td>
                                        <td align="center" width="5%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                </tr>
                                <?php 
                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                                if (db_num_rows($busca_ingredientes)==0) {
                                ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                        <td align="center" colspan=7><font size=1 face="<?= $fuente_general; ?>">No hay ingredientes en el plato</font></td>
                                </tr>
                                <?php 
                                }
                                $array_articulos="";
                                $contador_ing=0;
                                $total_kcal=0;
                                $total_hc=0;
                                $total_prot=0;
                                $total_grasas=0;
                                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $array_articulos=$array_articulos.$datos_ingredientes["id_ingrediente"].",";
                                $contador_ing++;
                                $total_kcal=$total_kcal+round((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                $total_hc=$total_hc+round((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                $total_prot=$total_prot+round((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                $total_grasas=$total_grasas+round((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2);
                                if ($datos_ingredientes["unidad_medida"]==0) {
                                        $coletilla_unidad="gr.";
                                        } else {
                                        $coletilla_unidad="ml.";
                                        }
                                ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                        <td width=50%><font size=1 face="<?= $fuente_general; ?>"><?=app_url_text_utf8($datos_ingredientes["nombre_alimento"]);?><?php  if ($datos_ingredientes["preparar_separado"]==1) { print " (separado)"; } ?></font></td>
                                        <td align="right" width=15%><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_ingredientes["cantidad_racion"],2,",",".");?> <?=$coletilla_unidad;?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["kcal_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> KCal</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["hc_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["prot_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format((($datos_ingredientes["grasas_gramo"]*$datos_ingredientes["cantidad_racion"])/100),2,",",".");?> g</font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="eliminar_ingrediente.php?id_plato=<?=$id_plato;?>&id_ingrediente=<?=$datos_ingredientes["id_ingrediente"];?>"><img src='borrar.gif' border=0></a></font></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                        <td align="right" colspan=2><font size=1 face="<?= $fuente_general; ?>">Información nutricional plato:</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_kcal,2,",",".");?> KCal</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_hc,2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_prot,2,",",".");?> g</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_grasas,2,",",".");?> g</font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                </tr>
                                </table>
                         <br>
                         <input type="button" value="Cancelar" onclick="window.location.href='gestion_platos.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
