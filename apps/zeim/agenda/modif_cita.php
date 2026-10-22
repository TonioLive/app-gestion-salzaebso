<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$origen_cita=$_GET["oc"];

$idcita=$_GET["id"];

?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo1.php"); ?>
</style>
</head>

<body bgcolor="<?= $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<img src="<?= $url_web; ?>/imagen/logo1.gif">
<div align="center">
<center>
<?php 
$db_cc=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_cc);

$consulta_cita=db_query("SELECT * FROM Agenda WHERE id=".$idcita) or die (db_error());

if (db_num_rows($consulta_cita)!=0) {

$rscita=db_fetch_array($consulta_cita);

// Calcula avisos

$dia_avisar=$rscita["avisar"];

if (strlen($dia_avisar)!=0) {
        $avisar_timestamp=strtotime($dia_avisar);
        $cita_timestamp=strtotime($rscita["Fecha"]);
        $dias_avisar=($cita_timestamp-$avisar_timestamp)/86400;
        } else {
        $dias_avisar=0;
        }
        
$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)($rscita["Fecha"]), (string)($rscita["Fecha"])));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        
$fecha_time_stamp=strtotime($rscita["Fecha"]);
?>
<table border="1" width="100%" cellspacing="0">
<form action="modif_cita2.php?id_curso=<?= $id_curso; ?>" method="post" name="edicioncitas">
<input type="hidden" name="idcita" value="<?= $idcita; ?>">
<input type="hidden" name="id_aula_cita0" value="0">
<input type="hidden" name="oc" value="<?=$origen_cita?>">
  <tr valign=top>
    <td width="100%" bgcolor="#fff8c6" colspan="2"><font face="Arial" color="#800000"><small><small><small><?= date("Y",$fecha_time_stamp)." ".$nombre_mes[intval(date("m",$fecha_time_stamp))]; ?></small></small></small><br>
    <big><?php  echo date("j",$fecha_time_stamp); ?> </big><?php  echo ($dia_sem_largo[date("w",$fecha_time_stamp)]); ?></big></font></td>
  </tr>
<?php 
if (($nivel_acceso==4) or ($nivel_acceso==3)) {
?>
  <tr>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Escuela:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"></font><select name="id_centro_cita" style="font-size=10" onchange="cambia_listado()">
    <option value="0">Común</option>
    <?php 
    $busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
    while($lista_centros=db_fetch_array($busca_centros)){
    if (($nivel_acceso==4) or ($centro_actual==$lista_centros["id_centro"]) or ($usuario_multicentro==1)) {
    ?>
    <option value="<?=$lista_centros["id_centro"];?>"<?php  if ($lista_centros["id_centro"]==$rscita["id_centro"]) { print " selected"; } ?>><?=$lista_centros["nombre_centro"];?></option>
    <?php 
    }
    }
    ?></select></td>
  </tr>
<?php 
    $busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
    while($lista_centros=db_fetch_array($busca_centros)){

    $centro_actual_lista=$lista_centros["id_centro"];
    $array_para_javascript="";
    $busca_aulas=db_query("SELECT *, centro_aulas.nombre AS nombre_aula, cursos_academicos.nombre AS nombre_curso FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_centro=$centro_actual_lista AND centro_aulas.id_curso=cursos_academicos.id_curso ORDER BY centro_aulas.id_curso DESC,centro_aulas.nombre");

    if ($lista_centros["id_centro"]==$rscita["id_centro"]) {
          $visible_inicial="block";
          } else {
          $visible_inicial="none";
          }

?>
  <tr id="aulas<?=$centro_actual_lista?>" style="display:<?=$visible_inicial;?>">
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Aula:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><select name="id_aula_cita<?=$centro_actual_lista?>" style="font-size=10">
    <option value="0">Común</option>
    <?php 
    while($lista_aulas=db_fetch_array($busca_aulas)) {
    ?>
    <option value="<?=$lista_aulas["id_aula"];?>"<?php  if ($lista_aulas["id_aula"]==$rscita["id_aula"]) { print " selected"; } ?>><?=$lista_aulas["nombre_aula"];?> Curso: <?=$lista_aulas["nombre_curso"]?></option>
    <?php 
    }
    ?></select></td>
  </tr>
<?php 
}

} else {

$aula_asignada_usuario=0;

$busca_aula_asignada=db_query("SELECT * FROM centro_aulas WHERE docente_titular=$idUsuario AND id_curso=$id_curso_actual LIMIT 0,1");

if (db_num_rows($busca_aula_asignada)!=0) {
        $datos_aula_asignada=db_fetch_array($busca_aula_asignada);
        $aula_asignada_usuario=$datos_aula_asignada["id_aula"];
        }

?>
<input type="hidden" name="id_centro_cita" value="<?=$centro_actual?>">
<input type="hidden" name="id_aula_cita<?=$centro_actual?>" value="<?=$aula_asignada_usuario?>">
<?php 
}
?>
  <tr>
    <td width="17%" bgcolor="#fff8c6">&nbsp;</td>
    <td width="83%" bgcolor="#fff8c6"><small><b>Cambiar día:&nbsp;&nbsp;&nbsp;</b>
    <select size=1 style="font-size=10" name=dia>
    <?php  
  $dia=date("d",strtotime($rscita["Fecha"]));
  $sele="";
  for ($d=1; $d<=31; $d=$d+1)
  {

    if ($d<10)
    {
      $d="0".$d;
    } 
    if (intval($d)==intval($dia))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $d; ?>"><?= $d; ?></option>
    <?php  

  } 

?>
    </select>/
    <select size=1 style="font-size=10" id=select1 name=mes>
    <?php  
  $mes=date("m",strtotime($rscita["Fecha"]));
  $sele="";
  for ($m=1; $m<=12; $m=$m+1)
  {

    if ($m<10)
    {
      $m="0".$m;
    } 
    if (intval($m)==intval($mes))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
    <?php  

  } 

?>
    </select>/
    <select size=1 style="font-size=10" id=select2 name=ano>
    <?php  
  $ano=date("Y",strtotime($rscita["Fecha"]));;
  $sele="";
  for ($a=2009; $a<=date("Y")+2; $a=$a+1)
  {

    if (intval($a)==intval($ano))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $a; ?>"><?= $a; ?></option>
    <?php  

  } 

?>
    </select>
    </small></td>
  </tr>
  <tr>
    <td width="17%" bgcolor="#fff8c6">&nbsp;</td>
    <td width="83%" bgcolor="#fff8c6"><small><b>Cambiar hora:</b>
    <select size=1 style="font-size=10" name=hora>
    <?php  
  $hora=date("H",strtotime($rscita["Hora"]));
  $sele="";
  for ($h=0; $h<=23; $h=$h+1)
  {

    if ($h<10)
    {
      $h="0".$h;
    } 
    if (intval($h)==intval($hora))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $h; ?>"><?= $h; ?></option>
    <?php  

  } 

?>
    </select>:
    <select size=1 style="font-size=10" id=select1 name=min>
    <?php  
  $minuto=date("i",strtotime($rscita["Hora"]));
  $sele="";
  for ($m=0; $m<=59; $m=$m+5)
  {
    if ($m<10)
    {
      $m="0".$m;
    } 
    if (intval($m)==intval($minuto))
    {

      $sele=" selected";
    }
      else
    {

      $sele="";
    } 

?>
    <option<?= $sele; ?> value="<?= $m; ?>"><?= $m; ?></option>
    <?php  

  } 

?>
    </select>
    </small></td>
  </tr>
  <tr>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Avisar:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><font face="Arial" color="#800000" size=2><select style="font-size=10" name="avisar">
    <option value="0">No avisar</option>
    <option value="1"<?php  if ($dias_avisar==1) { print " selected"; } ?>>1 día antes</option>
    <?php  for($c_dias=2;$c_dias<=30;$c_dias++) { ?>
    <option value="<?=$c_dias?>"<?php  if ($dias_avisar==$c_dias) { print " selected"; } ?>><?=$c_dias?> días antes</option>
    <?php  } ?>
    </select> <input type="checkbox" value="1" name="enviarmail" disabled<?php  if ($rscita["enviarmail"]==1) { print " checked"; } ?>> Notificar por email.</font></td>
  </tr>
<?php 
if (($nivel_acceso==4) or ($nivel_acceso==3)) {
?>
  <tr valign="top">
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Visible por:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><table width=100% border=0 cellpadding=0 cellspacing=0><tr><td><font face="Arial" color="#800000" size=2>
    <?php 
    $busca_si_es_solo_yo=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita");
    if (db_num_rows($busca_si_es_solo_yo)==1) {
        $datos_solo_yo=db_fetch_array($busca_si_es_solo_yo);
        if ($datos_solo_yo["id_usuario"]==$idUsuario) {
                $estado_solo_yo1=" checked";
                }
        } else {
        $busca_si_es_muchos=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario Is Null") or die(db_error());

        if (db_num_rows($busca_si_es_muchos)>1) {
                $estado_solo_yo0=" checked";
                } else {
                $estado_solo_yo2=" checked";
                }
        }
    ?>
    <input type="radio" name="solo_yo" value="1"<?=$estado_solo_yo1?>> Solo yo<br>
    </td><td><font face="Arial" color="#800000" size=2>&nbsp;</font></td></tr>
    <tr valign="top">
    <td><font face="Arial" color="#800000" size=2><input type="radio" name="solo_yo" value="0"<?=$estado_solo_yo0?>> Todos los...</font></td>
    <td><font face="Arial" color="#800000" size=2><input type="radio" name="solo_yo" value="2"<?=$estado_solo_yo2?>> Los usuarios seleccionados...</font></td>
    </tr>
    <?php 
    // Busca si es de niveles
    $busca_4=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=4 AND id_usuario Is Null") or die(db_error());
    $busca_3=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=3 AND id_usuario Is Null") or die(db_error());
    $busca_2=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=2 AND id_usuario Is Null") or die(db_error());
    $busca_1=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=1 AND id_usuario Is Null") or die(db_error());
    $busca_10=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=10 AND id_usuario Is Null") or die(db_error());
    $busca_11=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND nivel=11 AND id_usuario Is Null") or die(db_error());
    ?>
    <tr valign="bottom">
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel4" value="1"<?php  if (db_num_rows($busca_4)!=0) { print " checked"; } ?>> Administradores</font></td>
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:seleccionar_usuarios(4)">Administradores</a></font></td>
    </tr><tr valign="bottom">
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel3" value="1"<?php  if (db_num_rows($busca_3)!=0) { print " checked"; } ?>> Coordinadoras de centro</font></td>
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:seleccionar_usuarios(3)">Coordinadoras de centro</a></font></td>
    </tr><tr valign="bottom">
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel2" value="1"<?php  if (db_num_rows($busca_2)!=0) { print " checked"; } ?>> Educadoras</font></td>
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:seleccionar_usuarios(2)">Educadoras</a></font></td>
    </tr><tr valign="bottom">
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel10" value="1"<?php  if (db_num_rows($busca_10)!=0) { print " checked"; } ?>> Personal cocina</font></td>
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:seleccionar_usuarios(10)">Personal cocina</a></font></td>
    </tr><tr valign="bottom">
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel11" value="1"<?php  if (db_num_rows($busca_11)!=0) { print " checked"; } ?>> Personal no docente</font></td>
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:seleccionar_usuarios(11)">Personal no docente</a></font></td>
    </tr><tr valign="bottom">
    <td><font face="Arial" color="#800000" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel1" value="1"<?php  if (db_num_rows($busca_1)!=0) { print " checked"; } ?>> Padres</font></td>
    <td><font face="Arial" color="#800000" size=2>&nbsp;</font></td>
    </tr></table></td>
  </tr>
<?php 
} else {
// Busca si es soloYo
$busca_yo=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND (id_usuario<>$idUsuario OR id_usuario Is Null)");
?>
  <tr valign="top">
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Visible por:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><font face="Arial" color="#800000" size=2>
    <input type="checkbox" name="solo_yo" value="1"<?php  if (db_num_rows($busca_yo)==0) { print " checked"; } ?>> Solo yo<br>
    </font></td>
  </tr>
<?php 
}
?>
  <tr>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b><?= date("H:i",strtotime($rscita["Hora"])); ?></b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><input type="text" value="<?= $rscita["Titulo"]; ?>" size=59 style="font-size=10" name="Titulo"></td>
  </tr>
  <tr>
    <td width="17%" bgcolor="#fff8c6">&nbsp;</td>
    <td width="83%" bgcolor="#fff8c6"><small><textarea name="Texto" cols=59 rows=6 style="font-size: 11px; font-family: Arial"><?= $rscita["Texto"]; ?></textarea></small></td>
  </tr>
  <tr>
    <td width="17%" bgcolor="#fff8c6">&nbsp;</td>
    <td width="83%" bgcolor="#fff8c6">&nbsp;</td>
  </tr>
</table>
</center>
</div>
<p align="center">
<small>
<input type="submit" value="Guardar cambios" style="font-size=10">
<?php  
} else {
?>
La cita seleccionada ya ha sido eliminada de la base de datos.
<?php 
}
 if ($origen_cita!=1) { ?><input type="button" onclick="history.go(-1)" value="Volver"  style="font-size=10"> <?php  } ?><input type="button" onclick="window.close()" value="Cerrar ventana"  style="font-size=10">
</small>
</p>
</form>
<script language="Javascript">

function seleccionar_usuarios(nivel) {
        self.document.edicioncitas.solo_yo[2].click();
        window.open("seleccionar_usuarios.php?id_cita=<?=$idcita?>&nivel="+nivel,"seleccion_usuarios","resizable=yes,toolbars=no,width=450,height=550");
        }

function cambia_listado() {

        id_listado=document.datos_citas.id_centro_cita.value;
        <?php 
        $busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
        while($lista_centros=db_fetch_array($busca_centros)){

        $centro_actual_lista=$lista_centros["id_centro"];
        ?>
        document.getElementById('aulas<?=$centro_actual_lista?>').style.display='none';
        <?php 
        }
        ?>
        document.getElementById('aulas'+id_listado).style.display='block';
        }
        
</script>
</body>
</html>

