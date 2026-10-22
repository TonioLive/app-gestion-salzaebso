<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$id_motivo=$_GET["id_motivo"];

if (strlen($id_motivo)!=0) {

$consulta_articulo = db_query_params('SELECT * FROM contactos_motivos_cancelacion WHERE id_motivo=?', 's', array($id_motivo)) or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$nombre_motivo=$datos_articulo["nombre_motivo"];
if (strlen($nombre_motivo)!=0) {
        $nombre_motivo=urldecode($nombre_motivo);
        }

$texto_encabezado="Editar";
} else {
$id_motivo=0;
$texto_encabezado="Nuevo";
}


?>
<script language="Javascript">

function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.nombre_motivo.value=="") {
                errores=errores+"Falta introducir la nombre del motivo.\n";
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
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_encabezado?> motivo<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="modificar_motivo1.php">
                        <input type="hidden" name="id_motivo" value="<?=$id_motivo?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_motivo" value="<?=$nombre_motivo?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        </form>
                         </table>
                         <br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"> <input type="button" value="Cancelar" onclick="window.location.href='listado_motivos.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
