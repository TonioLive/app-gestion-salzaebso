<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

// Valores por defecto
$nombre_objetivo="";
$descripcion_objetivo="";
$texto_accion="Nuevo";

$id_objetivo=$_GET["id_objetivo"];
$id_area_objetivo=$_GET["id_area"];

if (strlen($id_objetivo)!=0) {
        $texto_accion="Editar";
        $busca_objetivo=db_query_params('SELECT * FROM programacion_objetivos WHERE id_objetivo=?;', 's', array($id_objetivo)) or die(db_error());

        $datos_objetivo=db_fetch_array($busca_objetivo);
        
        $orden_objetivo=$datos_objetivo["orden"];
        $nombre_objetivo=stripslashes($datos_objetivo["nombre_objetivo"]);
        $descripcion_objetivo=stripslashes($datos_objetivo["descripcion_objetivo"]);
        $objetivo_padre=$datos_objetivo["padre"];
        $id_area_objetivo=$datos_objetivo["id_area"];
        
        } else {
        
        $id_objetivo=0;
        $objetivo_padre=0;
        
        }

?>
<script language="Javascript">
function comprueba_y_envia() {
        var errores="";

        if (self.document.datos.nombre_objetivo.value=="") {
                errores=errores+"Falta introducir la nombre del objetivo.\n";
                }
        if (self.document.datos.descripcion_objetivo.value=="") {
                errores=errores+"Falta introducir la descripción del objetivo.\n";
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
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$texto_accion?> objetivo<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos" method="POST" action="nuevo_objetivo1.php">
                        <input type="hidden" name="id_objetivo" value="<?=$id_objetivo?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Area:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="id_area" cols=60 rows=10 style="font-size: 11px; font-family: Arial">
                                <?php 
                                $busca_areas=db_query("SELECT * FROM programacion_areas ORDER BY orden");

                                while($areas=db_fetch_array($busca_areas)){
                                        ?>
                                        <option value="<?=$areas["id_area"]?>"<?php  if ($areas["id_area"]==$id_area_objetivo) { print " selected"; } ?>><?=$areas["nombre_area"]?></option>
                                        <?php 
                                        }
                                ?>
                          </select></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Nombre:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_objetivo" value="<?=$nombre_objetivo?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Descripción:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><textarea name="descripcion_objetivo" cols=60 rows=10 style="font-size: 11px; font-family: Arial"><?=$descripcion_objetivo?></textarea></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Jerarquía, dentro de:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="padre" style="font-size: 11px; font-family: Arial">
                                <option value=0>Área</option>
                                <?php 
                                if ($id_objetivo!=0) {
                                        $busca_objetivos=db_query_params('SELECT * FROM programacion_objetivos WHERE id_objetivo<>? AND id_area=? AND padre<>? ORDER BY orden', 'sss', array($id_objetivo, $id_area_objetivo, $id_objetivo));
                                        } else {
                                        $busca_objetivos=db_query_params('SELECT * FROM programacion_objetivos WHERE id_objetivo<>? AND id_area=? ORDER BY orden', 'ss', array($id_objetivo, $id_area_objetivo));
                                        }

                                while($objetivos=db_fetch_array($busca_objetivos)){
                                        $tabulaciones="";
                                        if ($objetivos["padre"]!=0) {
                                                $tabulaciones="--";
                                                }
                                        ?>
                                        <option value="<?=$objetivos["id_objetivo"]?>"<?php  if ($objetivo_padre==$objetivos["id_objetivo"]) { print " selected"; } ?>><?=$tabulaciones.$objetivos["nombre_objetivo"]?></option>
                                        <?php 
                                        }
                                ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Posición, detrás de:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><select name="posicion" style="font-size: 11px; font-family: Arial">
                                <option value=0>Primero del área</option>
                                <?php 
                                $busca_objetivos=db_query("SELECT * FROM programacion_objetivos WHERE id_objetivo<>$id_objetivo AND id_area=".$id_area_objetivo." AND padre=$objetivo_padre ORDER BY orden");
                                
                                while($objetivos=db_fetch_array($busca_objetivos)){
                                        ?>
                                        <option value="<?=$objetivos["orden"]?>"<?php  if ($objetivos["orden"]==($orden_objetivo-1)) { print " selected"; } ?>><?=$objetivos["nombre_objetivo"]?></option>
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
