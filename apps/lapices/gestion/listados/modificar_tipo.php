<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$id_tipo=$_GET["id_tipo"];

if (strlen($id_tipo)!=0) {

$consulta_articulo = db_query("SELECT * FROM contactos_tipo WHERE id_tipo=$id_tipo") or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$nombre_tipo=$datos_articulo["nombre_tipo"];
if (strlen($nombre_tipo)!=0) {
        $nombre_tipo=urldecode($nombre_tipo);
        }

$texto_encabezado="Editar";
} else {
$id_tipo=0;
$texto_encabezado="Nuevo";
}


?>
<script language="Javascript">

function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.nombre_tipo.value=="") {
                errores=errores+"Falta introducir la nombre del tipo.\n";
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
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_encabezado?> tipo<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="modificar_tipo1.php">
                        <input type="hidden" name="id_tipo" value="<?=$id_tipo?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_tipo" value="<?=$nombre_tipo?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        </form>
                         </table>
                         <br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"> <input type="button" value="Cancelar" onclick="window.location.href='listado_tipos.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
