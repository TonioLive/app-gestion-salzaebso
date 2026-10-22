<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];
$dia=$_GET["dia"];

// Datos para el formulario
$formulario_name = "observaciones";
$formulario_datos_name = "observaciones";
$formulario_method = "POST";
$formulario_action = "editar_observaciones_todos1.php";

?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function seleccionar_frase(frase) {
        frase_actual=document.<?=$formulario_name?>.observaciones.value+frase;
        document.<?=$formulario_name?>.observaciones.value=frase_actual;
        }
function borrar_observaciones() {
        document.<?=$formulario_name?>.observaciones.value="";
        }

//-->
</SCRIPT>
</head>

<body>
<?php 

$accion_texto="Nuevo";

if ($id_aula!=0) {
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

        if (db_num_rows($busca_aulas)!=0)
                {
                $datos_aula=db_fetch_array($busca_aulas);

                // Datos
                $nombre_aula=$datos_aula["nombre"];
                
                }
        }

?>
<table width="100%" border="0">
  <tr>
    <td align="center">
    <b>Observaciones sobre la comida<br>Aula <i><?=$nombre_aula;?></i> el día <?=date("d/m/Y",$dia)?><br><font color="#ff0000">¡ATENCION! Esta acción agregará este texto a todos los alumnos del aula.</font></b><br><br>
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
            <form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
            <input type="hidden" name="id_aula" value="<?=$id_aula?>">
            <input type="hidden" name="dia" value="<?=$dia?>">
            <tr>
              <td align="right" valign="top"><b>Observaciones:</b></td>
              <td align="left"><textarea name="observaciones" style="font-size: 11px; font-family: Arial" cols=80 rows=8></textarea></td>
            </tr>
            <tr>
              <td align="right" valign="top"><font size=1><b>Frases tipo<br>
              Clic sobre ellas para añadir al texto</b></font></td>
              <td colspan="2" align="left"><font size=2><?php 

              // Busca frases tipo
              $busca_frases=db_query("SELECT * FROM agenda_alumnos_observaciones_tipo WHERE tipo=0 ORDER BY observacion");
              while($frases=db_fetch_array($busca_frases)) {
                        ?><a href="javascript:seleccionar_frase('<?=$frases["observacion"]?>')">- <?=$frases["observacion"]?></a><br><?php 
                        }

              ?></font></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><br><input name="Cancelar" type="button" id="Cancelar" tabindex="8" value="Cancelar" onclick="window.close();" /> <input name="Borrar_texto" type="button" id="Borrar_texto" tabindex="8" value="Borrar texto" onclick="borrar_observaciones();" />
                   <input name="Finalizar" type="submit" id="Finalizar" value="Finalizar" /></td>
            </tr>
            <tr>
              <td align="right" valign="top">&nbsp;</td>
              <td colspan="2" align="left">&nbsp;</td>
            </tr>
          </table>

        </td>
      </tr>
    </table></td>
  </tr>
</form>
</table>
</body>
</html>
<?php 
db_close();
?>
