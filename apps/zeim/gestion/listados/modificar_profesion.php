<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$id_profesion=$_GET["id_profesion"];

if (strlen($id_profesion)!=0) {

$consulta_articulo = db_query_params('SELECT * FROM padres_profesiones WHERE id_profesion=?', 's', array($id_profesion)) or die (db_error());
$datos_articulo = db_fetch_array($consulta_articulo);

$nombre_profesion=$datos_articulo["nombre_profesion"];
if (strlen($nombre_profesion)!=0) {
        $nombre_profesion=urldecode($nombre_profesion);
        }

$texto_encabezado="Editar";
} else {
$id_profesion=0;
$texto_encabezado="Nueva";
}


?>
<script language="Javascript">

function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.nombre_profesion.value=="") {
                errores=errores+"Falta introducir la nombre del profesion.\n";
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
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_encabezado?> profesion<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="modificar_profesion1.php">
                        <input type="hidden" name="id_profesion" value="<?=$id_profesion?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_profesion" value="<?=$nombre_profesion?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        </form>
                         </table>
                         <br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"> <input type="button" value="Cancelar" onclick="window.location.href='listado_profesiones.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
