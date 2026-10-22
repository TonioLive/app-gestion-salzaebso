<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_docente_ancho.php");

$tiposCocina = array();

$buscaTiposCocina = db_query("SELECT * FROM menu_lista_tipos_lista ORDER BY id_tipo") or die (db_error());
while ($datosTipoCocina = db_fetch_array($buscaTiposCocina)) {
    $tiposCocina[$datosTipoCocina["id_tipo"]] = $datosTipoCocina["nombre_tipo"];
}

$estado_botones=" disabled='disabled'";

?>
<script language="Javascript">
<?php 
if (($nivel_acceso>=3) and ($nivel_acceso<10)) {
	$estado_botones="";
?>
function eliminar(codigo) {
        if (confirm("¿Realmente quieres eliminar esta lista?")) {
                window.location.href="eliminar_lista.php?id_lista="+codigo;
                }
        }
<?php 
}
?>
        
function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }
        
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de compras</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Nueva lista" onclick="window.location.href='nueva_lista_compra.php'"<?=$estado_botones?> style="font-size:14px; width: 220; height: 32"></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>">Genera lista de la compra entre dos fechas.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Listas de compras.</b></font>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=2 cellpadding=3 width="98%">
                        <?php 
                        $consulta_lista = db_query("SELECT * FROM menu_lista_compra ORDER BY fecha_ini DESC") or die (db_error());
                        
                        if (db_num_rows($consulta_lista)==0) {
                        ?>
                        <tr>
                        <td colspan=5 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay listas elaboradas</font><br><br><br></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#EFEFEF">
                                <td width='5%'><font size=2 face="<?=$fuente_general;?>"><b>ID</b></font></td>
                                <td width='5%'><font size=2 face="<?=$fuente_general;?>"><b>Cocina</b></font></td>
                                <td width='15%'><font size=2 face="<?=$fuente_general;?>"><b>Del día</b></font></td>
                                <td width='15%'><font size=2 face="<?=$fuente_general;?>"><b>Al día</b></font></td>
                                <td width='45%'><font size=2 face="<?=$fuente_general;?>"><b>Observaciones</b></font></td>
                                <td width='20%'><font size=2 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        }
                        
                        while($datos_lista = db_fetch_array($consulta_lista)){
                        $texto_observaciones=$datos_lista["observaciones"];

                        if (strlen($texto_observaciones)!=0) {
                                $texto_observaciones=app_url_text_utf8($texto_observaciones);
                                }
                        $color_fila="#fff";
                        ?>
                        <tr bgcolor="<?=$color_fila;?>" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fila;?>')">
                                <td><font size=2 face="<?= $fuente_general; ?>"><?=$datos_lista["id_lista"];?></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><?=$tiposCocina[$datos_lista["tipo_lista"]];?></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_lista["fecha_ini"]));?></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_lista["fecha_fin"]));?></font></td>
                                <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$texto_observaciones;?></font></td>
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><a href="#" onclick="window.open('ver_lista_compra.php?id_lista=<?=$datos_lista["id_lista"];?>','lista_compra','toolbar=no,resizable=yes,scrollbars=yes')">Editar</a> | <a href='/cocina/imprimir_lista_compra.php?no_imprimir=1&id_lista=<?=$datos_lista["id_lista"]?>' target='_blank'>Ver/Imprimir</a><?php  if (($nivel_acceso>=3) and ($nivel_acceso<10)) { ?> | <a href="#" onclick="eliminar(<?=$datos_lista["id_lista"];?>)">Eliminar</a> <?php  } ?></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
