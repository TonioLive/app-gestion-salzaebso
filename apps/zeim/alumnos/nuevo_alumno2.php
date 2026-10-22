<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$paso_anterior=11;
$paso_actual=2;
$paso_siguiente=3;

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

$formulario_enviado=$_GET["formulario_enviado"];

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];
                }
        }

if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso".$paso_anterior.".php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query_params('UPDATE usuarios SET fecha_edicion=? WHERE id_usuario=?', 'ss', array((string)(strftime("%Y/%m/%d")), $id_usuario_seleccionado)) or die(db_error()."-1");
                             $formulario_enviado=0;
                             }
        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_alumno".$paso_siguiente.".php?u=".$id_usuario_seleccionado;

// COmprueba si ya hay relaciones introducidas
$consulta_relaciones=db_query_params('SELECT * FROM padres WHERE padre_de=?', 's', array($id_usuario_seleccionado));
$numero_de_relaciones=db_num_rows($consulta_relaciones);

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
<input type="hidden" name="filas_actual" value="<?=($numero_de_relaciones+1);?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b><?=$accion_texto?> Alumno -  Paso 2 de 4</b></font><br><br>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr bgcolor="#EFEFEF" valign="top">
                                <?php  if ($accion_texto=="Editar") { ?>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno1.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font></a><br><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno11.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;&nbsp;1.1. Insertar Foto</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno2.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">2. Padres y personas autorizadas</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno3.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">3. Información adicional</font></a></td>
                                        <td align="left"><a href="#" onclick="if (confirm('Al cambiar de pantalla de esta forma no se guardan los cambios. Si has realizado modificaciones en los datos, pulsa Cancelar y utiliza el botón que hay en la parte inferior de la pantalla para guardarlos. Si no has realizado cambios o quieres descartarlos pulsa Aceptar.')) { self.location.href='nuevo_alumno4.php?u=<?=$id_usuario_seleccionado;?>'; }"><font size=1 face="<?= $fuente_general; ?>" color="#000000">4. Inscripciones</font></a></td>
                                <?php  } else { ?>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#000000">1. Datos personales</font><font size=1 face="<?= $fuente_general; ?>" color="#000000"><br>&nbsp;&nbsp;1.1. Insertar Foto</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#1D50C3">2. Padres y personas autorizadas</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">3. Información adicional</font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>" color="#B4C2E2">4. Inscripciones</font></td>
                                <?php  } ?>
                                </tr>
                        </table><BR>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                <tr valign="top">
                                        <td align="center" valign="middle" width="100" rowspan=2><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
                                        <td align="center" bgcolor="#efefef" width="100%"><font size=3 face="<?= $fuente_general; ?>"><b>Alumno:</b> <?=$usuario_nombre;?> <?=$usuario_apellido1;?>, <?=$usuario_apellido2;?><b><small><br><br>Padres y personas autorizadas</b></small></font></td>
                                </tr>
                                <?php
                                if ($idUsuario<>6297) {
                                ?>
                                <tr valign="top">
                                        <td align="center" bgcolor="#efefef"><input type="button" value="Nuevo familiar" onclick="window.open('nuevo_padre1.php','nuevo','toolbar=no,resizable=no,scrollbars=yes,width=600,height=500');"> <input type="button" value="Buscar familiar" onclick="window.open('seleccionar_padre.php','nuevo','toolbar=no,resizable=no,scrollbars=yes,width=500,height=400');"></td>
                                </tr>
                                <?php
                                }
                                ?>
                                <tr valign="top">
                                        <td colspan=2 align="center" bgcolor="#efefef">
                                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                                <tr bgcolor="#dedede">
                                                <td align="left" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                <td align="left" width=27%><font size=1 face="<?= $fuente_general; ?>" color="#000000">Nombre</font></td>
                                                <td align="center" width=13%><font size=1 face="<?= $fuente_general; ?>" color="#000000">DNI</font></td>
                                                <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>" color="#000000">Relación de parentesco</font></td>
                                                <?php
                                                if ($idUsuario<>6297) {
                                                ?>
                                                <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>" color="#000000">Usu. Webcam</font></td>
                                                <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>" color="#000000">Password</font></td>
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                <?php
                                                }
                                                ?>                                                
                                                </tr>
                                        </table>
                                        <div id="tablaformulario">
                                        <?php 
                                        // Busca relaciones
                                        $contador_relaciones=1;
                                        while($relacion=db_fetch_array($consulta_relaciones)) {
                                        // Busca datos padres
                                        $consulta_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($relacion["id_usuario"]));
                                        
                                        if (db_num_rows($consulta_datos_padre)!=0) {
                                                $datos_padre=db_fetch_array($consulta_datos_padre);
                                                ?>
                                                <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                                        <input type="hidden" value="<?=$datos_padre["id_usuario"];?>" name="codigo<?=$contador_relaciones;?>">
                                                        <tr bgcolor="#ffffff">
                                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><input type="radio" value="<?=$contador_relaciones;?>" name="seleccionado"></font></td>
                                                        <td align="left" width=27%><input type="text" name="nombre<?=$contador_relaciones;?>" value="<?=$datos_padre["Apellido1"]?> <?=$datos_padre["Apellido2"]?>, <?=$datos_padre["Nombre"]?>" size=34 style="font-size: 10px" readonly></td>
                                                        <td align="center" width=13%><input type="text" name="dni<?=$contador_relaciones;?>" value="<?=$datos_padre["DNI"]?>" size=9 style="font-size: 10px" readonly></td>
                                                        <td align="center" width=15%><select name="parentesco<?=$contador_relaciones;?>" value="" size=1 style="font-size: 10px">
                                                        <option value="0"></option>
                                                        <option value="1"<?php  if ($relacion["parentesco"]==1) { print " selected"; } ?>>Padre/Madre</option>
                                                        <option value="2"<?php  if ($relacion["parentesco"]==2) { print " selected"; } ?>>Abuelo/a</option>
                                                        <option value="3"<?php  if ($relacion["parentesco"]==3) { print " selected"; } ?>>Tio/a</option>
                                                        <option value="4"<?php  if ($relacion["parentesco"]==4) { print " selected"; } ?>>Autorizado/a</option>
                                                        </select></td>
                                                        <?php
                                                        if ($idUsuario<>6297) {

                                                                if (strlen($relacion["usuario_webcam"])!=0) {
                                                                        ?>
                                                                <td align="center" width=15%><input type="text" name="usuario<?=$contador_relaciones;?>" value="<?=$relacion["usuario_webcam"]?>" size=10 style="font-size: 10px" readonly></td>
                                                                <td align="center" width=15%><input type="text" name="pass<?=$contador_relaciones;?>" value="<?=security_escape(security_family_password($relacion))?>" size=10 style="font-size: 10px" readonly><?php if(in_array((int)$nivel_acceso,array(3,4),true)){ ?> <a href="generar_password.php?id_usuario=<?=(int)$id_usuario_seleccionado?>&amp;id_padre=<?=(int)$relacion['id_padre']?>">Cambiar contrase&ntilde;a</a><?php } ?></td>
                                                                <?php 
                                                                } else {
                                                                ?>
                                                                <td align="center" width=30%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><input type="button" value="Generar" onclick="self.location.href='generar_password.php?id_usuario=<?=$id_usuario_seleccionado;?>&id_padre=<?=$relacion["id_padre"];?>';" style="font-size:10px"></font></td>
                                                                <?php 
                                                                }                                                                
                                                        ?>
                                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$contador_relaciones;?>)"><img src="editar.gif" border=0></a></font></td>
                                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar(<?=$relacion["id_padre"];?>)"><img src="borrar.gif" border=0></a></td>                                                        
                                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a target="_new" href="/pdf/carta.php?id_alumno=<?=$id_usuario_seleccionado?>&id_padre=<?=$relacion["id_padre"]?>&id_escuela=<?=$centro_actual?>"><img src="correo.gif" border=0></a>
                                                        <br /><small>La clave actual se conserva hasta que confirmes un cambio de contrase&ntilde;a.</small>
                                                        <?php
                                                        if ($centro_actual==25) {
                                                                echo("<br />");
                                                                // Muestra botones para sacar informe gastos de comedor de cada curso
                                                                // 1º Busca inscripciones del alumno en distintos cursos
                                                                $busca_inscripciones_alumno=db_query_params('SELECT * FROM alumnos LEFT JOIN cursos_academicos ON alumnos.id_curso = cursos_academicos.id_curso WHERE alumnos.id_usuario=? AND fecha_fin<=CURDATE()+ INTERVAL 1 MONTH ORDER BY alumnos.id_curso DESC', 's', array($id_usuario_seleccionado));
                                                                while($inscripciones_alumno=db_fetch_array($busca_inscripciones_alumno)) {
                                                                        ?>
                                                                        <a target="_new" href="/pdf/informe_comedor_curso_todos.php?id_alumno=<?=$inscripciones_alumno["id_alumno"]?>&id_curso=<?=$inscripciones_alumno["id_curso"]?>&id_escuela=<?=$centro_actual?>&id_padre=<?=$relacion["id_usuario"]?>">Gastos comedor <?=$inscripciones_alumno["nombre"]?></a><br />
                                                                        <?php

                                                                }

                                                        }                
                                                        }
                                                        ?></td>
                                                       </tr>
                                                </table>
                                                <?php 
                                                $contador_relaciones=$contador_relaciones+1;
                                                }
                                        }
                                        if ($idUsuario<>6297) {
                                        ?>
                                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                                <input type="hidden" value="" name="codigo<?=$contador_relaciones;?>">
                                                <tr bgcolor="#ffffff">
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><input type="radio" value="<?=$contador_relaciones;?>" name="seleccionado" checked></font></td>
                                                <td align="left" width=27%><input type="text" name="nombre<?=$contador_relaciones;?>" value="" size=34 style="font-size: 10px" readonly></td>
                                                <td align="center" width=13%><input type="text" name="dni<?=$contador_relaciones;?>" value="" size=9 style="font-size: 10px" readonly></td>
                                                <td align="center" width=15%><select name="parentesco<?=$contador_relaciones;?>" value="" size=1 style="font-size: 10px">
                                                <option value="0"></option>
                                                <option value="1">Padre/Madre</option>
                                                <option value="2">Abuelo/a</option>
                                                <option value="3">Tio/a</option>
                                                <option value="4">Autorizado/a</option>
                                                </select></td>
                                                <td align="center" width=30%><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$contador_relaciones;?>)"><img src="editar.gif" border=0></a></font></td>
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar(<?=$contador_relaciones;?>)"><img src="borrar.gif" border=0></a></td>
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><img src="nada.gif" border=0></td>
                                                </tr>
                                        </table>
                                        <?php
                                        }
                                        ?>
                                        </div>
                                        <?php
                                        if ($idUsuario<>6297) {
                                        ?>
                                        <input type="button" value="Nueva fila" onclick="nueva_fila()" style="font-size:10px">
                                        <?php
                                        }
                                        ?>
                                        </td>
                                </tr>
                         </table>
                         <br><input type="button" onclick="cambia(3);" value="Guardar cambios y siguiente paso >>"><br><br>
                </td>
        </tr>
</form>
</table>
<script type="text/javascript" src="../inc/validacion_fechas.js"></script>
<script Language="JavaScript">
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
function nuevo_progenitor(codigo,nombre_completo,dni,poblacion) {
        fila_seleccionada=0;
        filas_actual=Number(document.<?=$formulario_name;?>.filas_actual.value);
        
        if (filas_actual>1) {
                        var ya_esta=false;
                        for (ff=1;ff<=filas_actual;ff++) {
                                if (eval("document.<?=$formulario_name;?>.codigo"+ff+".value==codigo")) { ya_esta=true; }
                                }
                        if (ya_esta==false) {
                                for (ff=1;ff<=filas_actual;ff++) {
                                        if (document.<?=$formulario_name;?>.seleccionado[ff-1].checked) {
                                                eval("document.<?=$formulario_name;?>.codigo"+ff+".value=codigo;");
                                                eval("document.<?=$formulario_name;?>.nombre"+ff+".value=nombre_completo;");
                                                eval("document.<?=$formulario_name;?>.dni"+ff+".value=dni;");
                                                alert("Selecciona ahora el parentesco");
                                                }
                                        }
                                } else {
                                alert("El usuario ya está seleccionado en otra fila");
                                }
                } else {
                        if (document.<?=$formulario_name;?>.codigo1.value==codigo) {
                                alert("El usuario ya está seleccionado en otra fila");
                                } else {
                                document.<?=$formulario_name;?>.codigo1.value=codigo;
                                document.<?=$formulario_name;?>.nombre1.value=nombre_completo;
                                document.<?=$formulario_name;?>.dni1.value=dni;
                                alert("Selecciona ahora el parentesco");
                                }
                }
        }
function borrar(fila) {
        if (confirm("¿Quieres eliminar la línea seleccionada?")) {
                self.location.href='borrar_fila.php?id_usuario=<?=$id_usuario_seleccionado;?>&id_padre='+fila;
                }
        }
function editar(fila) {
        codigo=eval("document.<?=$formulario_name;?>.codigo"+fila+".value");
        if (codigo!="") {
                window.open('nuevo_padre1.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
                } else {
                alert("Primero debes seleccionar el usuario");
                }
        }
function nueva_fila() {
        filas_actual=Number(document.<?=$formulario_name;?>.filas_actual.value)+1;
        document.<?=$formulario_name;?>.filas_actual.value=filas_actual;
        html_fila='<table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%"><input type="hidden" value="" name="codigo'+filas_actual+'"><tr bgcolor="#ffffff"><td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><input type="radio" value="'+filas_actual+'" name="seleccionado"></font></td><td align="left" width=27%><input type="text" name="nombre'+filas_actual+'" value="" size=34 style="font-size: 10px" readonly></td><td align="center" width=13%><input type="text" name="dni'+filas_actual+'" value="" size=9 style="font-size: 10px" readonly></td><td align="center" width=15%><select name="parentesco'+filas_actual+'" value="" size=1 style="font-size: 10px"><option value="0"></option><option value="1">Padre/Madre</option><option value="2">Abuelo/a</option><option value="3">Tio/a</option><option value="4">Autorizado/a</option></select></td><td align="center" width=30%><font size=1>&nbsp;</font></td><td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar('+filas_actual+')"><img src="editar.gif" border=0></a></font></td><td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:borrar('+filas_actual+')"><img src="borrar.gif" border=0></a></td></tr></table>';
        document.getElementById('tablaformulario').innerHTML=document.getElementById('tablaformulario').innerHTML+html_fila;
        }
        
function cambia(mostrar) {
        if (mostrar!=<?=$paso_actual;?>) {
                var errores=0;


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
</td>
</tr>
</table>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
