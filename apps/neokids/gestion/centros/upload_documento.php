<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_curso_actual=$_GET["id_curso_alumno"];
$id_usuario_seleccionado=$_GET["id_alumno"];
$subcarpeta_actual=$_GET["sub"];


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
//-->
</SCRIPT>
</head>

<body>
<?php 
// Datos para el formulario
// Datos para el formulario
$formulario_name = "upload_doc";
$formulario_datos_name = "datos_doc";
$formulario_method = "POST";
$formulario_action = "upload_documento1.php?id_alumno=$id_usuario_seleccionado&id_curso_alumno=$id_curso_actual&sub=$subcarpeta_actual";

?>
<table width="100%" border="0">
  <tr>
    <td align="center">
    <b><br>Guardar archivo en la carpeta del alumno</b><br><br>
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
            <form ENCTYPE="multipart/form-data" name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
            <tr>
              <td align="right" valign="top"><b>Archivo:</b></td>
              <td align="left"><input type="file" name="File1" size=50></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><i>Una vez pulsado el botón <b>Finalizar</b>, se enviará el documento al servidor. Si el archivo es muy grande puede tardar bastante, dependiendo de la velocidad de la conexión.</i></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><br><input name="Cancelar" type="button" id="Cancelar" tabindex="8" value="Cancelar" onclick="window.location.href='ver_carpeta_alumno.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$subcarpeta_actual?>'" />
                   <input name="Finalizar" type="button" onclick="cambia();" id="Finalizar" value="Finalizar" /></td>
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
<script Language="JavaScript">

function cambia() {
                var errores=0;

                var mensajes_error="";
                        
                if (self.document.<?=$formulario_name;?>.File1.value!="") {
                        nombre_archivo=self.document.<?=$formulario_name;?>.File1.value;
                        nombre_archivo=nombre_archivo.toUpperCase();
                        longitud_nombre=nombre_archivo.length;
                        // if (nombre_archivo.substr(longitud_nombre-3,3)!="JPG") {
                        //        mensajes_error=mensajes_error+"El archivo de imagen debe tener extensión JPG.\u000A";errores=1;
                        //        }
                        } else {
                        mensajes_error=mensajes_error+"No has seleccionado ningún documento.\u000A";errores=1;
                        }
                        
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A");
                        }
        }
        
//-->
</script>
</body>
</html>
<?php 
db_close();
?>
