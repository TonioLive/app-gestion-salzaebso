<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
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
$paso_anterior=2;
$paso_actual=1;
$paso_siguiente=11;

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

$id_alumno_origen=app_request_int($_GET, "alumno", app_request_int($_POST, "alumno", 0));
if ($id_usuario_seleccionado==0 && $id_alumno_origen>0) {
        $consulta_contacto_alumno=db_query_params(
                "SELECT Direccion, cp, Poblacion, Provincia, telefono1, telefono2, movil, email FROM usuarios WHERE id_usuario=? AND tipo_inscripcion=1",
                "i",
                array($id_alumno_origen)
        );
        if ($consulta_contacto_alumno && db_num_rows($consulta_contacto_alumno)!=0) {
                $contacto_alumno=db_fetch_assoc($consulta_contacto_alumno);
                $usuario_direccion=$contacto_alumno["Direccion"];
                $usuario_cp=$contacto_alumno["cp"];
                $usuario_poblacion=$contacto_alumno["Poblacion"];
                $usuario_provincia=$contacto_alumno["Provincia"];
                $usuario_telefono1=$contacto_alumno["telefono1"];
                $usuario_telefono2=$contacto_alumno["telefono2"];
                $usuario_movil=$contacto_alumno["movil"];
                $usuario_email=$contacto_alumno["email"];
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
                $usuario_DNI=$datos_usuario["DNI"];
                $usuario_profesion=$datos_usuario["profesion"];
                }
        }
        
// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_padre11.php?u=".$id_usuario_seleccionado."&alumno=".$id_alumno_origen;

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
?>

<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_usuario_seleccionado;?>">
<input type="hidden" name="alumno" value="<?=$id_alumno_origen;?>">
<input type="hidden" name="formulario_enviado" value="1">	

<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> Familiar</b></font><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td rowspan=5 align="center" valign="middle" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" width="100" border=0></a></td>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos personales</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="Nombre" value="<?=$usuario_nombre;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Apellidos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="Apellido1" value="<?=$usuario_apellido1;?>" style="font-size: 10px"> <input type="text" name="Apellido2" value="<?=$usuario_apellido2;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>DNI/NIF:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="DNI" value="<?=$usuario_DNI;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Profesión:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="profesion" size=60 value="<?=$usuario_profesion;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="2" face="<?= $fuente_general; ?>"> <input type="radio" name="sexo" value="V"<?php  if ($usuario_sexo=="V") { print " checked"; } ?>> Varón&nbsp; &nbsp;<input type="radio" name="sexo" value="H"<?php  if ($usuario_sexo=="H") { print " checked"; } ?>>Hembra </font></td>
                        </tr>
                    <tr valign="top">
                                <td colspan=3 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Información de contacto</b></font></td>
                        </tr>
                    <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Domicilio</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="Direccion" size=50 value="<?=$usuario_direccion;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Código postal</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="CP" size=5 value="<?=$usuario_cp;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="poblacion" size=50 value="<?=$usuario_poblacion;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Provincia</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="provincia" style="font-size: 10px">
                                <option value="">- Seleccione la provincia donde reside -</option>
                                <?php 
                                $provincia=$usuario_provincia;
                                $busca_provincia=db_query("SELECT * FROM provincias ORDER BY id_provincia");
                                if (strlen($provincia)==0) { $provincia=46; }
                                while($lista_provincias=db_fetch_array($busca_provincia)) {
                                        print "<option value=".$lista_provincias["id_provincia"];
                                                if (intval($lista_provincias["id_provincia"])==intval($provincia)) { print " selected"; }
                                                                           print ">".$lista_provincias["provincia"]."</option>";
                                                                        }
                                ?>
                                </select></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Teléfonos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono1" value="<?=$usuario_telefono1;?>" style="font-size: 10px"> <input type="text" name="telefono2" value="<?=$usuario_telefono2;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Móvil</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="movil" name="movil" size=50 value="<?=$usuario_movil;?>" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=310 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Email</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email" size=50 value="<?=$usuario_email;?>" style="font-size: 10px"></font></td>
                        </tr>
                        </tr>
                         </table>
                         <br><input type="button" onclick="window.close();" value="Cancelar" style="font-size: 10px"> <input type="button" onclick="cambia();" value="Siguiente >>" style="font-size: 10px"><br><br>
                </td>
        </tr>
</table>
</td>
</tr>
</table>

</form>

<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function cambia() {
                var errores=0;

                var mensajes_error="";
                if (self.document.<?=$formulario_name;?>.Nombre.value=="") { mensajes_error=mensajes_error+"Es obligatorio introducir el nombre.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.Apellido1.value=="") { mensajes_error=mensajes_error+"Hay que introducir el primer apellido.\u000A";errores=1; }

                if ((self.document.<?=$formulario_name;?>.sexo[0].checked==false)&&(self.document.<?=$formulario_name;?>.sexo[1].checked==false)) { mensajes_error=mensajes_error+"Es necesario indicar el sexo.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.Direccion.value=="") { mensajes_error=mensajes_error+"Falta introducir la dirección.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.CP.value=="") { mensajes_error=mensajes_error+"No se ha introducido el código postal.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.poblacion.value=="") { mensajes_error=mensajes_error+"Es obligatorio introducir la población.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.provincia.value=="") { mensajes_error=mensajes_error+"Hay que indicar la provincia de residencia.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono1.value=="")&&(self.document.<?=$formulario_name;?>.telefono2.value=="")&&(self.document.<?=$formulario_name;?>.movil.value=="")) { mensajes_error=mensajes_error+"Es necesario disponer de un teléfono de contacto.\u000A";errores=1; }

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
