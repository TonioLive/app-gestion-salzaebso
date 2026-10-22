<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

// Valores por defecto
// Valores de la tabla

$id_tipo_punto=$_GET["pregunta"];

if (strlen($id_tipo_punto)!=0) {
        $consulta_pregunta = db_query_params('SELECT * FROM lista_espera_cuestionarios WHERE id_tipo_punto=?', 's', array($id_tipo_punto)) or die (db_error());
        $datos_pregunta = db_fetch_array($consulta_pregunta);
        
        $descripcion=$datos_pregunta["descripcion"];
        $valor=$datos_pregunta["valor"];
        $id_curso_pregunta=$datos_pregunta["id_curso"];
        $desactivada=$datos_pregunta["desactivada"];
        } else {
        $id_tipo_punto=0;
        $descripcion="";
        $valor=1;
        $id_curso_pregunta=0;
        $desactivada=0;
        }



?>
<script language="Javascript">

function comprueba_y_envia() {
        var errores="";
        
        if (self.document.datos_familia.descripcion.value=="") {
                errores=errores+"Falta introducir el texto de la pregunta.\n";
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
                <font size=2 face="<?= $fuente_general; ?>"><b>Modificar pregunta<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2>
                        <form name="datos_familia" method="POST" action="modificar_pregunta1.php">
                        <input type="hidden" name="id_tipo_punto" value="<?=$id_tipo_punto?>">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Pregunta:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><input type="text" name="descripcion" value="<?=$descripcion?>" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Valor de la respuesta afirmativa:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><select name="valor" size=1 style="font-size: 10px">
                          <?php 
                          for ($c_valor=-10;$c_valor<=10;$c_valor=$c_valor+0.5) {
                          ?><option value="<?=$c_valor;?>"<?php  if ($c_valor==$valor) { print " selected"; } ?>><?=$c_valor;?></option><?php 
                          }
                          ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Curso:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><select name="id_curso" size=1 style="font-size: 10px">
                          <option value="0"<?php  if ($id_curso_pregunta==0) { print " selected"; } ?>>Todos los cursos</option>
                                <?php 
                                $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                                while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                                ?>
                                  <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php  if ($id_curso_pregunta==$datos_listado_cursos["id_curso"]) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                                <?php 
                                }
                                ?>
                          </select></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Estado:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><select name="desactivada" size=1 style="font-size: 10px">
                          <option value="0"<?php  if ($desactivada==0) { print " selected"; } ?>>Activa</option>
                          <option value="1"<?php  if ($desactivada==1) { print " selected"; } ?>>Desactivada</option>
                          </select></font></td>
                        </tr>
                        </form>
                         </table>
                         <br>
                         <input style="font-size: 10px" type="button" value="Guardar cambios" onclick="comprueba_y_envia();"> <input type="button" value="Cancelar" onclick="window.location.href='edicion.php'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
<br>
<br>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
