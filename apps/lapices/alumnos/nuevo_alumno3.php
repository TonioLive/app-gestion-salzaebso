<?php  require("../configuracion/parte_superior_privado_alumnos.php"); ?>
<?php 
$paso_anterior=2;
$paso_actual=3;
$paso_siguiente=4;

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



$array_valores_alimentos=array();

$array_valores_alergias=array();

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
                $alergias=$datos_usuario["alergias"];
                $vacunaciones=$datos_usuario["vacunaciones"];
                $observaciones=$datos_usuario["observaciones"];
                $contacto1=$datos_usuario["contacto1"];
                $telefono_contacto1=$datos_usuario["telefono_contacto1"];
                $contacto2=$datos_usuario["contacto2"];
                $telefono_contacto2=$datos_usuario["telefono_contacto2"];
                $contacto_medico=$datos_usuario["contacto_medico"];
                $telefono_medico=$datos_usuario["telefono_medico"];
                
                for ($c=0;$c<count($array_alimentos);$c++) {
                        $array_valores_alimentos[$c]=$datos_usuario[$array_alimentos[$c]];
                        }
                for ($c=0;$c<count($array_alergias);$c++) {
                        $array_valores_alergias[$c]=$datos_usuario[$array_alergias[$c]];
                        }
                }
        }

if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso".$paso_anterior.".php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query("UPDATE usuarios SET fecha_edicion='".strftime("%Y/%m/%d")."' WHERE id_usuario=$id_usuario_seleccionado") or die(db_error()."-1");
                             $formulario_enviado=0;
                             }
        }

$familiares_contacto=array();
$consulta_familiares_contacto=db_query_params(
        "SELECT u.id_usuario, u.Nombre, u.Apellido1, u.Apellido2, u.telefono1, u.telefono2, u.movil "
        . "FROM padres p INNER JOIN usuarios u ON u.id_usuario=p.id_usuario "
        . "WHERE p.padre_de=? ORDER BY p.parentesco, u.Apellido1, u.Apellido2, u.Nombre",
        "i",
        array((int) $id_usuario_seleccionado)
);
if ($consulta_familiares_contacto) {
        while($familiar_contacto=db_fetch_assoc($consulta_familiares_contacto)) {
                $telefono_familiar=trim($familiar_contacto["telefono1"]);
                if ($telefono_familiar==="") { $telefono_familiar=trim($familiar_contacto["movil"]); }
                if ($telefono_familiar==="") { $telefono_familiar=trim($familiar_contacto["telefono2"]); }
                $familiares_contacto[]=array(
                        "nombre"=>trim($familiar_contacto["Nombre"]." ".$familiar_contacto["Apellido1"]." ".$familiar_contacto["Apellido2"]),
                        "telefono"=>$telefono_familiar
                );
                }
        }
if (!isset($contacto1)) { $contacto1=""; }
if (!isset($telefono_contacto1)) { $telefono_contacto1=""; }
if (!isset($contacto2)) { $contacto2=""; }
if (!isset($telefono_contacto2)) { $telefono_contacto2=""; }
if ($contacto1==="" && isset($familiares_contacto[0])) {
        $contacto1=$familiares_contacto[0]["nombre"];
        $telefono_contacto1=$familiares_contacto[0]["telefono"];
        }
if ($contacto2==="" && isset($familiares_contacto[1])) {
        $contacto2=$familiares_contacto[1]["nombre"];
        $telefono_contacto2=$familiares_contacto[1]["telefono"];
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_alumno".$paso_siguiente.".php?u=".$id_usuario_seleccionado;

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<input type="hidden" name="u" value="<?=$id_usuario_seleccionado;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> Alumno -  Paso 3 de 4</b></font><br>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr bgcolor="#EFEFEF" valign="top">
                                <?php  if ($accion_texto=="Editar") { ?>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno1.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font></a><br><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno11.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;&nbsp;1.1. Insertar Foto</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno2.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno3.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Información adicional</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno4.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">4. Inscripciones</font></a></td>
                                <?php  } else { ?>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font><font size=1 face="<?= $fuente_general; ?>" color="#000000"><br>&nbsp;&nbsp;1.1. Insertar Foto</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#1D50C3">3. Información adicional</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">4. Inscripciones</font></td>
                                <?php  } ?>
                                </tr>
                        </table><BR>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#ffffff"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100" align="left"></a><font size=2 face="<?= $fuente_general; ?>"><br><br><b>Información Adicional</b></font></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Introducción de alimentos:</b></font></td>
                                        <td>
                                        <?php 
                                        // Alimentos
                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                        ?>
                                        <input type="checkbox" name="<?=$array_alimentos[$c]?>" value="1"<?php  if ($array_valores_alimentos[$c]==1) { print " checked"; } ?>><?=$array_nombres_alimentos[$c];?><br>
                                        <?php 
                                        }
                                        ?></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Alergias alimentos:</b></font></td>
                                        <td>
                                        <?php 
                                        // alergias
                                        for ($c=0;$c<count($array_alergias);$c++) {
                                        ?>
                                        <input type="checkbox" name="<?=$array_alergias[$c]?>" value="1"<?php  if ($array_valores_alergias[$c]==1) { print " checked"; } ?>><?=$array_nombres_alergias[$c];?><br>
                                        <?php 
                                        }
                                        ?></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Otras Alergias</b></font></td>
                                        <td><textarea style="FONT-SIZE: 12px; FONT-FAMILY: Arial" name="alergias" rows="6" cols="85"><?php  echo "$alergias"; ?></textarea></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Vacunaciones</b></font></td>
                                        <td><textarea style="FONT-SIZE: 12px; FONT-FAMILY: Arial" name="vacunaciones" rows="6" cols="85"><?php  echo "$vacunaciones"; ?></textarea></td>
                                </tr>
                                <tr vAlign="top">
                                        <td width=210 align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Observaciones</b></font></td>
                                        <td><textarea style="FONT-SIZE: 12px; FONT-FAMILY: Arial" name="observaciones" rows="6" cols="85"><?php  echo "$observaciones"; ?></textarea></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>1er. Contacto</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>">
                                                <select name="contacto1" id="contacto1" onchange="actualizaTelefonoContacto('contacto1','telefono_contacto1',false)">
                                                        <option value="">Selecciona un familiar</option>
                                                        <?php $contacto1_en_lista=false; foreach($familiares_contacto as $familiar_contacto) {
                                                                $seleccionado_contacto1=$contacto1===$familiar_contacto["nombre"];
                                                                if ($seleccionado_contacto1) { $contacto1_en_lista=true; }
                                                        ?>
                                                        <option value="<?=htmlspecialchars($familiar_contacto["nombre"],ENT_QUOTES,"UTF-8")?>" data-telefono="<?=htmlspecialchars($familiar_contacto["telefono"],ENT_QUOTES,"UTF-8")?>"<?php if ($seleccionado_contacto1) { print " selected"; } ?>><?=htmlspecialchars($familiar_contacto["nombre"],ENT_QUOTES,"UTF-8")?></option>
                                                        <?php } if ($contacto1!=="" && !$contacto1_en_lista) { ?>
                                                        <option value="<?=htmlspecialchars($contacto1,ENT_QUOTES,"UTF-8")?>" selected><?=htmlspecialchars($contacto1,ENT_QUOTES,"UTF-8")?></option>
                                                        <?php } ?>
                                                </select>
                                        </font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono 1er. contacto</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" id="telefono_contacto1" name="telefono_contacto1" size=50 value="<?=$telefono_contacto1;?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>2º Contacto</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>">
                                                <select name="contacto2" id="contacto2" onchange="actualizaTelefonoContacto('contacto2','telefono_contacto2',false)">
                                                        <option value="">Selecciona un familiar</option>
                                                        <?php $contacto2_en_lista=false; foreach($familiares_contacto as $familiar_contacto) {
                                                                $seleccionado_contacto2=$contacto2===$familiar_contacto["nombre"];
                                                                if ($seleccionado_contacto2) { $contacto2_en_lista=true; }
                                                        ?>
                                                        <option value="<?=htmlspecialchars($familiar_contacto["nombre"],ENT_QUOTES,"UTF-8")?>" data-telefono="<?=htmlspecialchars($familiar_contacto["telefono"],ENT_QUOTES,"UTF-8")?>"<?php if ($seleccionado_contacto2) { print " selected"; } ?>><?=htmlspecialchars($familiar_contacto["nombre"],ENT_QUOTES,"UTF-8")?></option>
                                                        <?php } if ($contacto2!=="" && !$contacto2_en_lista) { ?>
                                                        <option value="<?=htmlspecialchars($contacto2,ENT_QUOTES,"UTF-8")?>" selected><?=htmlspecialchars($contacto2,ENT_QUOTES,"UTF-8")?></option>
                                                        <?php } ?>
                                                </select>
                                        </font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono 2º contacto</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" id="telefono_contacto2" name="telefono_contacto2" size=50 value="<?=$telefono_contacto2;?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nómbre del médico</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="contacto_medico" size=50 value="<?=$contacto_medico;?>"></font></td>
                                </tr>
                                <tr valign="top">
                                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono médico</b></font></td>
                                        <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_medico" size=50 value="<?=$telefono_medico;?>"></font></td>
                                </tr>
                         </table>
                         <br><input type="button" onclick="cambia(4);" value="Guardar cambios y siguiente paso >>"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<script Language="JavaScript">
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
function actualizaTelefonoContacto(idContacto,idTelefono,soloSiVacio) {
        var selector=document.getElementById(idContacto);
        var telefono=document.getElementById(idTelefono);
        if (!selector || !telefono || selector.selectedIndex<0) { return; }
        var opcion=selector.options[selector.selectedIndex];
        var nuevoTelefono=opcion.getAttribute('data-telefono') || '';
        if (!soloSiVacio || telefono.value==='') { telefono.value=nuevoTelefono; }
        }
function cambia(mostrar) {
        if (mostrar!=<?=$paso_actual;?>) {
                var errores=0;

                var mensajes_error="";

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
