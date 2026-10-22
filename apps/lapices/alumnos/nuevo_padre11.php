<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["u"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["u"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }
        
                // Comprobamos si es nuevo
                if ($id_usuario_seleccionado==0) {
                        $consulta_insert="INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES ('2','".strftime("%Y/%m/%d")."','1')";
                        db_query($consulta_insert) or die(db_error());
                        $id_usuario_seleccionado=db_insert_id();
                        }


                // Recibimos datos del formulario
                $usuario_nombre=$_POST["Nombre"];
                $usuario_apellido1=$_POST["Apellido1"];
                $usuario_apellido2=$_POST["Apellido2"];
                $usuario_sexo=$_POST["sexo"];
                $usuario_direccion=$_POST["Direccion"];
                $usuario_DNI=$_POST["DNI"];
                $usuario_profesion=$_POST["profesion"];
                $usuario_cp=$_POST["CP"];
                $usuario_poblacion=$_POST["poblacion"];
                $usuario_provincia=$_POST["provincia"];
                $usuario_telefono1=$_POST["telefono1"];
                $usuario_telefono2=$_POST["telefono2"];
                $usuario_movil=$_POST["movil"];
                $usuario_email=$_POST["email"];

                $nombre_completo_padre=$usuario_apellido1." ".$usuario_apellido2.", ".$usuario_nombre;

if ($num_errores==0) {

        // Introduce información
        // Introduce datos personales
        db_query("UPDATE usuarios SET Nombre='".$usuario_nombre."', Apellido1='".$usuario_apellido1."', Apellido2='".$usuario_apellido2."', sexo='".$usuario_sexo."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-2");

        // Introduce información de contacto
        db_query("UPDATE usuarios SET DNI='".$usuario_DNI."', profesion='".$usuario_profesion."', Direccion='".$usuario_direccion."', cp='".$usuario_cp."', Poblacion='".$usuario_poblacion."', Provincia='".$usuario_provincia."', telefono1='".$usuario_telefono1."', telefono2='".$usuario_telefono2."', movil='".$usuario_movil."', email='".$usuario_email."' WHERE id_usuario=".$id_usuario_seleccionado) or die(db_error()."-15");
        }

        // Comprobamos si es nuevo
        if ($id_usuario_seleccionado!=0) {
                        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);

                        if (db_num_rows($consulta_datos_usuario)!=0)
                                {
                                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                                }
                        }
                        
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$paso_anterior=1;
$paso_actual=11;
$paso_siguiente=2;

$accion_texto="Editar";

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];
                }
        }
        
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_padre2.php?formulario_enviado=1&u=".$id_usuario_seleccionado;

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      }
// Nombre DNI
$nombre_dni="dni_usu".$id_usuario_seleccionado.".jpg";
if (!file_exists($upload_path_dni."//$nombre_dni")) {
      $nombre_dni="sin_dni.jpg";
      }
// Nombre firma
$nombre_firma="firma_usu".$id_usuario_seleccionado.".jpg";
if (!file_exists($upload_path_firma."//$nombre_firma")) {
      $nombre_firma="sin_firma.jpg";
      }

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form ENCTYPE="multipart/form-data" name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> Familiar</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>">
                                <table bgcolor="#FFFFFF">
                                <tr bgcolor="#efefef" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2>
                                <td align="center"><font size=2>Foto</font></td>
                                <td align="center"><font size=2>DNI/NIE</font></td>
                                <td align="center"><font size=2>Firma</font></td>
                                </tr>
                                <tr valign="middle">
                                <td align="center"><font size=2><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" width="140" border=0></a></font></td>
                                <td align="center"><font size=2><a href="javascript:ampliar('/dni_usuarios/<?=$nombre_dni?>')"><img src="/dni_usuarios/<?=$nombre_dni?>" width="140" border=0></a></font></td>
                                <td align="center"><font size=2><a href="javascript:ampliar('/firma_usuarios/<?=$nombre_firma?>')"><img src="/firma_usuarios/<?=$nombre_firma?>" width="140" border=0></a></font></td>

                                </table></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Selecciona aquí el archivo con la Foto</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="file" name="File1" size=50 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Selecciona aquí el archivo con el DNI escaneado por las dos caras</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="file" name="File2" size=50 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Selecciona aquí el archivo con la firma escaneada</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><input type="file" name="File3" size=50 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><i>Las imágenes deben tener formato JPG.<br>Una vez pulsado el botón siguiente, se enviará la imagen al servidor<br>Si el archivo es muy grande puede tardar bastante, dependiendo de la velocidad de la conexión.</i></font></td>
                        </tr>
                         </table>
                         <br><input type="button" onclick="window.close();" value="Cancelar" style="font-size: 10px"> <input type="button" onclick="cambia();" value="Finalizar >>" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function cambia() {
                var errores=0;

                var mensajes_error="";

                // Comprueba el nombre del archivo
                if (self.document.<?=$formulario_name;?>.File1.value!="") {
                        nombre_archivo=self.document.<?=$formulario_name;?>.File1.value;
                        nombre_archivo=nombre_archivo.toUpperCase();
                        longitud_nombre=nombre_archivo.length;
                        if (nombre_archivo.substr(longitud_nombre-3,3)!="JPG") {
                                mensajes_error=mensajes_error+"El archivo de imagen debe tener extensión JPG.\u000A";errores=1;
                                }
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
