<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<?php 
include("../../inc/codigos_ausencias.php");

$formulario_name="horario";
$formulario_accion="cambiar_dia1.php";

$id_docente=$_GET["id_docente"];

$dia=$_GET["dia"];

if (strlen($id_docente)!=0) {

        $busca_datos_docente=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_docente, $centro_actual)) or die(db_error());
        if (db_num_rows($busca_datos_docente)==0) {
                db_query_params('INSERT INTO horario_docentes (id_usuario, id_centro) VALUES (?,?)', 'ss', array($id_docente, $centro_actual)) or die(db_error());
                // Vuelve a ejecutar la consulta
                $busca_datos_docente=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_docente, $centro_actual)) or die(db_error());
                }

        $datos_docente=db_fetch_array($busca_datos_docente);
                $centro_del_docente=$datos_docente["id_centro"];
                $mensaje="Edición";
                $nombre_docente=$datos_docente["Nombre"]." ".$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"];
        $id_horario=$datos_docente["id_horario"];
                $hora_entrada_man=$datos_docente["hora_entrada1"];
                if ($hora_entrada_man=="00:00:00") {
                        $hora_entrada_man=$hora_entrada_centro_man;
                        }
                $hora_salida_man=$datos_docente["hora_salida1"];
                if ($hora_salida_man=="00:00:00") {
                        $hora_salida_man=$hora_salida_centro_man;
                        }
                $hora_entrada_tar=$datos_docente["hora_entrada2"];
                if ($hora_entrada_tar=="00:00:00") {
                        $hora_entrada_tar=$hora_entrada_centro_tar;
                        }
                $hora_salida_tar=$datos_docente["hora_salida2"];
                if ($hora_salida_tar=="00:00:00") {
                        $hora_salida_tar=$hora_salida_centro_tar;
                        }
            $busca_horario_hoy=db_query_params('SELECT * FROM horario_docentes_dias WHERE id_usuario=? AND fecha=?', 'ss', array($id_docente, (string)(date("Y-m-d", $dia))));
            if (db_num_rows($busca_horario_hoy)!=0) {
                $horario_hoy=db_fetch_array($busca_horario_hoy);
                $hora_entrada_man_h=$horario_hoy["hora_entrada1"];
                $hora_salida_man_h=$horario_hoy["hora_salida1"];
                $hora_entrada_tar_h=$horario_hoy["hora_entrada2"];
                $hora_salida_tar_h=$horario_hoy["hora_salida2"];
                $tipo_ausencia=$horario_hoy["tipo_ausencia"];
                $sustituido_por=$horario_hoy["sustituido_por"];
                
                $hora_ini_falta=$horario_hoy["hora_ini_falta"];
                $hora_fin_falta=$horario_hoy["hora_fin_falta"];
                
                if (strlen($hora_ini_falta)==0) {
                        $hora_ini_falta="00:00:00";
                        }
                if (strlen($hora_fin_falta)==0) {
                        $hora_fin_falta="00:00:00";
                        }
                
                $hora_i_f=$hora_ini_falta;
                $hora_s_f=$hora_fin_falta;
             } else {
                $hora_entrada_man_h=$hora_entrada_man;
                $hora_salida_man_h=$hora_salida_man;
                $hora_entrada_tar_h=$hora_entrada_tar;
                $hora_salida_tar_h=$hora_salidaa_tar;
                $tipo_ausencia=0;
                $sustituido_por=0;
                }
        }

$url_retorno="calendario_mes.php?id_docente=$id_docente";

?>
<script language="Javascript">
function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";
                
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
<input type="hidden" name="id_docente" value="<?=$id_docente;?>">
<input type="hidden" name="dia" value="<?=$dia;?>">
<input type="hidden" name="o" value="<?=$o;?>">
<input type="hidden" name="no_asistencia" value="0">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> de horarios</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Horario del docente: <?=$nombre_docente?> para el día <?=date("d/m/Y",$dia);?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Horario de entrada y salida</b></font></td>
                        </tr>
                        <tr>
                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mañanas:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_man))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
                        <tr>
                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tardes:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_e1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_entrada_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_e1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_entrada_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_salida_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s1" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_salida_tar))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>
<tr>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo ausencia:</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><select name="tipo_ausencia" size=1 style="font-size: 10px">
                <option value="0"<?php  if ($tipo_ausencia==0) { print " selected"; } ?>></option>
<?php 
for($ci=1;$ci<=count($nombres_ausencias);$ci++) {
?>
                <option value="<?=$ci?>"<?php  if ($tipo_ausencia==$ci) { print " selected"; } ?>><?=$nombres_ausencias[$ci-1];?></option>
<?php 
}
?>
        </select></font></td>
  </tr>
                        <tr>
                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Horas ausencia</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <select name="hora_i_f" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_i_f))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_i_f" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_i_f))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select> a <select name="hora_s_f" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("H",strtotime($hora_s_f))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="min_s_f" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==date("i",strtotime($hora_s_f))) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></font></td>
  </tr>

<tr>
        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Ausencia cubierta por:</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><select name="sustituido_por" size=1 style="font-size: 10px">
                <option value="0"<?php  if ($sustituido_por==0) { print " selected"; } ?>></option>
                <?php 
  $consulta_usuarios=db_query("SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=$centro_del_docente OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1") or die(db_error());

  mt_srand((float)microtime()*1000000);
  $contador=0;
  $id_tipo_listado=-1;
  while($rsslc=db_fetch_array($consulta_usuarios))
  {

  $codigo=$rsslc["id_usuario"];
  $nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"];
  
  // Comprueba si tiene contrato en vigor
  $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? ORDER BY fecha_inicio DESC', 's', array($codigo)) or die(db_error());

                if (db_num_rows($busca_contratos)!=0) {
                ?>
                <option value="<?=$codigo?>"<?php  if ($sustituido_por==$codigo) { print " selected"; } ?>><?=ucwords(strtolower($nombre_ins))?></option>
                <?php 
                        }
                }
                ?>
        </select></font></td>
  </tr>
                        <tr>
                        </table><br>
                        <input type="Button" onclick="document.<?=$formulario_name;?>.no_asistencia.value=1;document.<?=$formulario_name;?>.submit();" value="Eliminar día" style="font-size: 10px"> <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                        <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php  require("../../configuracion/parte_inferior.php"); ?>
