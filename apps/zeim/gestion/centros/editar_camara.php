<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 

$formulario_name="camatas";
$formulario_accion="editar_camara1.php";

$id_camara=$_GET["id_camara"] ;

$url_retorno="listado_camaras.php";

if (strlen($id_camara)!=0) {

        $busca_datos_camara=db_query_params('SELECT * FROM webcam WHERE id_webcam=?', 's', array($id_camara));
        if (db_num_rows($busca_datos_camara)!=0) {
                $datos_camara=db_fetch_array($busca_datos_camara);
                $mensaje="Edición de la";

                $id_centro_webcam=$datos_camara["id_centro"];
                $id_aula_webcam=$datos_camara["id_aula"];
                $nombre_webcam=$datos_camara["nombre_webcam"];
                $url_webcam=$datos_camara["url_webcam"];
                $puerto_webcam=$datos_camara["puerto_webcam"];
                $tipo_webcam=$datos_camara["tipo"];
                $hora_conexion_webcam=$datos_camara["hora_conexion"];
                $hora_desconexion_webcam=$datos_camara["hora_desconexion"];
                $usuario_visor_webcam=$datos_camara["usuario_visor"];
                $password_visor_webcam=$datos_camara["password_visor"];
                $modelo_webcam=$datos_camara["modelo"];
                $ip_local=$datos_camara["ip_local"];
                
                } else {
                $id_camara="";
                $mensaje="Alta Nueva";
                }
        } else {
        $mensaje="Alta Nueva";
        }
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

?>
<script language="Javascript">
function comprobar_y_enviar() {
                var errores=0;

                var mensajes_error="";
                if (self.document.<?=$formulario_name;?>.id_aula_webcam.value=="") {
                        mensajes_error=mensajes_error+"Hay que seleccionar el aula o indicar si es un servicio común de un centro.\u000A";errores=1;
                        } else {
                        if (self.document.<?=$formulario_name;?>.tipo_webcam.value=="0") {
                                if (isNaN(self.document.<?=$formulario_name;?>.id_aula_webcam.value)==true) {
                                        mensajes_error=mensajes_error+'Si seleccionas el tipo de cámara "Aula" no puedes seleccionar una zona común.\u000A';errores=1;
                                        }
                                } else {
                                if (isNaN(self.document.<?=$formulario_name;?>.id_aula_webcam.value)==false) {
                                        mensajes_error=mensajes_error+'No puedes seleccionar un aula del listado, debes indicar a que escuela pertenece el comedor o zona común.\u000A';errores=1;
                                        }
                                }
                        }
                if (self.document.<?=$formulario_name;?>.nombre_webcam.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre de la cámara.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.url_webcam.value=="") { mensajes_error=mensajes_error+"Falta introducir la URL de la webcam (solo nombre de dominio o IP, Por ejemplo: rionervion.homeip.not o 80.33.176.143).\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.puerto_webcam.value=="") { mensajes_error=mensajes_error+"Hay que introducir el puerto de la cámara (Por ejemplo: 8051).\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.usuario_visor_webcam.value=="") { mensajes_error=mensajes_error+"Falta introducir el nombre de usuario para acceder al visor.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.password_visor_webcam.value=="") { mensajes_error=mensajes_error+"Falta introducir la contraseña de usuario para acceder al visor.\u000A";errores=1; }

                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }

</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_camara" value="<?=$id_camara;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> Cámara Web</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos de la cámara</b></font></td>
                        </tr>
                        <tr>
                              <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo: </b></font></td>
                              <td><font size=1 face="<?= $fuente_general; ?>">
                              <select name="tipo_webcam" size="1" style="font-size: 10px">
                              <option value="0"<?php  if ($tipo_webcam==0) { print " selected";} ?>>Aula</option>
                              <option value="1"<?php  if ($tipo_webcam==1) { print " selected";} ?>>Comedor</option>
                              <option value="2"<?php  if ($tipo_webcam==2) { print " selected";} ?>>Zona común</option>
                              </select>
                              </font></td>
                        </tr>
                        <tr>
                              <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Aula: </b></font></td>
                              <td><font size=1 face="<?= $fuente_general; ?>">
                              <select name="id_aula_webcam" size="1" style="font-size: 10px">
                                <option value=""></option>
                                <?php 
                                $consulta_centros=db_query("SELECT * FROM centros ORDER BY id_centro;");
                                while($centros=db_fetch_array($consulta_centros)){
                                ?>
                                <option value="C<?=$centros["id_centro"];?>"<?php  if (($tipo_webcam!=0) and ($id_centro_webcam==$centros["id_centro"])) { print " selected"; } ?>>Comedor o zona común - <?=$centros["nombre_centro"];?></option>
                                <?php 
                                }
                                $consulta_centros=db_query_params('SELECT centro_aulas.id_aula,centro_aulas.nombre,centros.nombre_centro,cursos_academicos.nombre AS nombre_curso FROM centro_aulas,centros,cursos_academicos WHERE centro_aulas.id_centro=centros.id_centro AND cursos_academicos.id_curso=centro_aulas.id_curso AND centro_aulas.id_curso=? ORDER BY centro_aulas.id_curso,centro_aulas.id_centro,centro_aulas.nombre;', 's', array($id_curso_actual));
                                while($centros=db_fetch_array($consulta_centros)){
                                ?>
                                <option value="<?=$centros["id_aula"];?>"<?php  if ($id_aula_webcam==$centros["id_aula"]) { print " selected"; } ?>><?=$centros["nombre"];?> - <?=$centros["nombre_centro"];?> (Curso <?=$centros["nombre_curso"];?>)</option>
                                <?php 
                                }
                                ?>
                                </select>
                              </font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre de la cámara:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_webcam" value="<?=$nombre_webcam;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>URL:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="url_webcam" value="<?=$url_webcam;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>IP Local:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="ip_local" value="<?=$ip_local;?>" size="80" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Puerto:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="puerto_webcam" value="<?=$puerto_webcam;?>" size="10" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre de usuario:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="usuario_visor_webcam" value="<?=$usuario_visor_webcam;?>" size="15" style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Password:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="password_visor_webcam" value="<?=$password_visor_webcam;?>" size="15" style="font-size: 10px"></font></td>
                        </tr>
                        <tr>
                              <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Modelo: </b></font></td>
                              <td><font size=1 face="<?= $fuente_general; ?>">
                              <select name="modelo_webcam" size="1" style="font-size: 10px">
                              <option value="0"<?php  if ($modelo_webcam==0) { print " selected";} ?>>AXIS 206</option>
                              <option value="1"<?php  if ($modelo_webcam==1) { print " selected";} ?>>AXIS 207</option>
                              <option value="2"<?php  if ($modelo_webcam==2) { print " selected";} ?>>EDIMAX</option>
                              <option value="3"<?php  if ($modelo_webcam==3) { print " selected";} ?>>EDIMAX Sant Pau</option>
							  <option value="4"<?php  if ($modelo_webcam==4) { print " selected";} ?>>APPROX 2019</option>
							  <option value="5"<?php  if ($modelo_webcam==5) { print " selected";} ?>>D-Link 2021</option>							  
                              </select>
                              </font></td>
                        </tr>
                        <tr>
                        <td width=210 align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Horario de la cámara:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_conexion_webcam))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_conexion_webcam))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_desconexion_webcam))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_desconexion_webcam))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                         </table>
                         <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Guardar cambios y salir >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php 
 require("../../configuracion/parte_inferior.php"); ?>
