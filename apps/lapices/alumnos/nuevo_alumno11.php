<?php  require("../configuracion/parte_superior_privado_alumnos.php"); ?>
<?php 
$paso_anterior=1;
$paso_actual=11;
$paso_siguiente=2;

$accion_texto="Editar";
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

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso".$paso_anterior.".php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query("UPDATE usuarios SET fecha_edicion='".strftime("%Y/%m/%d")."' WHERE id_usuario=$id_usuario_seleccionado") or die(db_error()."-1");
                             $formulario_enviado=0;
                             }
        }
if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                // Datos del paso
                $usuario_sexo=$datos_usuario["sexo"];
                $usuario_fechanac_dia=date("j",strtotime($datos_usuario["fecha_nacimiento"]));
                $usuario_fechanac_mes=date("m",strtotime($datos_usuario["fecha_nacimiento"]));
                $usuario_fechanac_ano=date("Y",strtotime($datos_usuario["fecha_nacimiento"]));
                $usuario_direccion=$datos_usuario["Direccion"];
                $usuario_cp=$datos_usuario["cp"];
                $usuario_poblacion=$datos_usuario["Poblacion"];
                $usuario_provincia=$datos_usuario["Provincia"];
                $usuario_telefono1=$datos_usuario["telefono1"];
                $usuario_telefono2=$datos_usuario["telefono2"];
                $usuario_movil=$datos_usuario["movil"];
                $usuario_email=$datos_usuario["email"];
                }
        }
        
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_alumno".$paso_siguiente.".php?formulario_enviado=1&u=".$id_usuario_seleccionado;

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form ENCTYPE="multipart/form-data" name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> Alumno -  Paso 1 de 4</b></font><br><br>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr bgcolor="#EFEFEF" valign="top">
                                <?php  if ($accion_texto=="Editar") { ?>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno1.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font></a><br><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno11.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;&nbsp;1.1. Insertar Foto</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno2.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno3.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Información adicional</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno4.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">4. Inscripciones</font></a></td>
                                <?php  } else { ?>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font><font size=1 face="<?= $fuente_general; ?>" color="#1D50C3"><br>&nbsp;&nbsp;1.1. Insertar Foto</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">2. Padres y personas autorizadas</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">3. Información adicional</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">4. Inscripciones</font></td>
                                <?php  } ?>
                                </tr>
                        </table><BR>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje_foto;?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" width="185" border=0></a></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Selecciona aquí el archivo con la Foto</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="file" name="File1" size=50 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>"><i>La Foto debe ser una imagen JPG. Una vez pulsado el botón siguiente, se enviará la imagen al servidor<br>Si el archivo es muy grande puede tardar bastante, dependiendo de la velocidad de la conexión.</i></font></td>
                        </tr>
                         </table>
                         <br><input type="button" onclick="cambia(2);" value="Guardar cambios y siguiente paso >>"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
function cambia(mostrar) {
        if (mostrar!=<?=$paso_actual;?>) {
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
                        if (confirm("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A¿Deseas continuar sin corregir los errores?")) {
                                document.<?=$formulario_name;?>.submit();
                                }
                        }
                }
        }
//-->
</script>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
