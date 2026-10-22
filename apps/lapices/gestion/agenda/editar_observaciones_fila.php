<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_fila=$_GET["id_fila"];

// Datos para el formulario
$formulario_name = "observaciones";
$formulario_datos_name = "observaciones";
$formulario_method = "POST";
$formulario_action = "editar_observaciones_fila1.php";

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
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin">
<?php 

$accion_texto="Editar";

if ($id_fila!=0) {
        $busca_registro_comida=db_query("SELECT * FROM agenda_alumnos_comidas WHERE id=$id_fila");

        if (db_num_rows($busca_registro_comida)!=0)
                {
                $datos_registro_comida=db_fetch_array($busca_registro_comida);

                // Datos
                $id_alumno=$datos_registro_comida["id_alumno"];
                $dia=$datos_registro_comida["dia"];
                $observaciones=$datos_registro_comida["observaciones_comida"];
                
                $busca_datos_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=$id_alumno");
                
                $datos_alumno=db_fetch_array($busca_datos_alumno);
                
                $nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];
                
                }
        }

?>
<table width="100%" border="0">
  <tr>
    <td align="center">
    <b><br>Observaciones sobre la comida<br>Alumno <i><?=$nombre_completo_alumno;?></i> el día <?=date("d/m/Y",strtotime($dia))?></b><br><br>
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
            <form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
            <input type="hidden" name="id_fila" value="<?=$id_fila?>">
            <tr>
              <td align="right" valign="top"><b>Observaciones:</b></td>
              <td align="left"><textarea name="observaciones" style="font-size: 11px; font-family: Arial" cols=80 rows=8><?=$observaciones;?></textarea></td>
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
