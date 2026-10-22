<?php  require("../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_articulo.PROVEEDOR.value=="") {
                errores=errores+"Falta seleccionar el proveedor.\n";
                }

        if (errores!="") {
                alert("Se han producido los siguientes errores:\n\n"+errores);
                } else {
                self.document.datos_articulo.submit();
                }
        }

function nuevo_proveedor() {
        window.open('/almacen/nuevo_proveedor_b.php','proveedores','toolbar=no,resizable=no,width=600,height=585');
        }
function editar_proveedor() {
        if (document.datos_articulo.PROVEEDOR.value=="") {
                alert("No has seleccionado un agente para editar");
                } else {
                window.open('/almacen/nuevo_proveedor_b.php?u='+document.datos_articulo.PROVEEDOR.value,'proveedores','toolbar=no,resizable=no,width=600,height=585');
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Nuevo pedido a proveedor</b></font><br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_articulo" method="POST" action="pedido_proveedor1.php">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Proveedor:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="PROVEEDOR" size=1 style="font-size: 10px">
                                <option value="">Selecciona el proveedor</option>
                                <?php 
                                $consulta_familia = db_query("SELECT * FROM proveedores ORDER BY RAZON_SOCIAL") or die (odbc_errormsg());
                                while($datos_familia = db_fetch_array($consulta_familia)){
                                ?>
                                <option value="<?=$datos_familia["ID_PROVEEDOR"];?>"<?php  if ($datos_familia["ID_PROVEEDOR"]==$PROVEEDOR) { echo(" selected"); } ?>><?=$datos_familia["RAZON_SOCIAL"];?></option>
                                <?php 
                                }
                                ?>
                          </select></font></td>
                        </tr>
                        </form>
                         </table><br>
                          <input type="button" style="font-size: 10px" value="Nuevo proveedor" onclick="nuevo_proveedor()"> <input type="button" style="font-size: 10px" value="Editar proveedor" onclick="editar_proveedor()"><br><br>
                         <input style="font-size: 10px" type="button" value="Siguiente >>" onclick="comprueba_y_envia();"> <input type="button" value="Cancelar" onclick="window.location.href='gestion_almacen.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
