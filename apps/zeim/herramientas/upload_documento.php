<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$subcarpeta_actual=$_GET["sub"];


?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>
</head>

<body>
<?php 

$accion_texto="Editar";
$estado_selects=1;
if (strlen($id_documento_seleccionado)==0) {
        $id_documento_seleccionado=$_GET["u"];
        if (strlen($id_documento_seleccionado)==0) {
                $id_documento_seleccionado=$_POST["u"];
                if (strlen($id_documento_seleccionado)==0) {
                        $id_documento_seleccionado=0;
                        $accion_texto="Nuevo";
                        $estado_selects=0;
                        }
                }
        }

// Datos para el formulario
// Datos para el formulario
$formulario_name = "upload_doc";
$formulario_datos_name = "datos_doc";
$formulario_method = "POST";
$formulario_action = "upload_documento1.php?sub=$subcarpeta_actual";

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
              <td colspan="2" align="center"><br><input name="Cancelar" type="button" id="Cancelar" tabindex="8" value="Cancelar" onclick="window.location.href='documentos.php?sub=<?=$subcarpeta_actual?>'" />
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
