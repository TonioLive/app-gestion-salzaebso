<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_plato=$_GET["id_plato"];
$subcarpeta_actual=$_GET["sub"];

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" type="text/css" media="screen" />
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" type="text/css" media="screen" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

<title>Subir imagen del plato</title>
<style type="text/css">
<!--
body {
        margin-left: 0px;
        margin-top: 0px;
        margin-right: 0px;
        margin-bottom: 0px;
}
-->
</style>
<link rel="stylesheet" href="/css/estilo1.css" type="text/css" media="screen" />
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
$formulario_action = "upload_documento1.php?id_plato=$id_plato&sub=$subcarpeta_actual";

?>
<table width="100%" border="0">
  <tr>
    <td align="center">
    <b><br>Guardar archivo con la imagen del plato</b><br><br>
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
            <form ENCTYPE="multipart/form-data" name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
            <tr>
              <td align="right" valign="top"><b>Archivo:</b></td>
              <td align="left"><input class="form-control" type="file" name="File1" size=50></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><i>Una vez pulsado el botón <b>Finalizar</b>, se enviará el documento al servidor. Si el archivo es muy grande puede tardar bastante, dependiendo de la velocidad de la conexión.</i></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><br><input name="Cancelar" class="btn btn-primary btn-default" type="button" id="Cancelar" tabindex="8" value="Cancelar" onclick="window.close();" />
                   <input name="Finalizar" class="btn btn-primary btn-default" type="button" onclick="cambia();" id="Finalizar" value="Finalizar" /></td>
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