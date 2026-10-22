<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$origen_cita=$_GET["oc"];

$dia_sel=$_GET["dia"];
$id_curso=$_GET["id_curso"];

if (strlen($id_curso)==0) {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d")."' ORDER BY fecha_inicio");

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso=$datos_curso_actual["id_curso"];
                        } else {
                        $id_curso=0;
                        }
                }
	}

if (strlen($dia_sel)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$dia_sel;
}

$fecha_agenda=date("d/m/Y",$dia_sel);

?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>

<script language="Javascript">

</script>

</head>

<body bgcolor="<?= $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="yes">
<img src="<?= $url_web; ?>/imagen/logo1.gif">
<div align="center">
<center>
<table border="1" width="100%" cellspacing="0">
<form action="add_cita2.php" method="post" name="datos_citas">
<input type="hidden" name="dia_sel" value="<?php  echo $fecha_time_stamp; ?>">
<input type="hidden" name="id_curso" value="<?php  echo $id_curso; ?>">
<input type="hidden" name="id_aula_cita0" value="0">
<input type="hidden" name="oc" value="<?=$origen_cita?>">
  <tr valign=top>
    <td width="100%" bgcolor="#fff8c6" colspan="2"><font face="Arial" color="#800000"><small><small><small><?= date("Y",$fecha_time_stamp)." ".$nombre_mes[intval(date("m",$fecha_time_stamp))]; ?></small></small></small><br>
    <big><?php  echo date("j",$fecha_time_stamp); ?> </big><?php  echo ($dia_sem_largo[date("w",$fecha_time_stamp)]); ?></big></font></td>
  </tr>
   <tr>
    <td valign="top" width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Hora:</b></small></font></td>
    <td valign="top" width="30%" bgcolor="#fff8c6" colspan="3">
  <select size=1 style="font-size=10" name=hora>
  <?php 
for ($h=0; $h<=23; $h=$h+1)
  {
  if ($h<10)
  {
  $h="0".$h;
  }
  ?>
  <option value="<?php    echo $h; ?>"><?php    echo $h; ?></option>
  <?php 
  }
  ?>
  </select>:
  <select size=1 style="font-size=10" id=select1 name=min>
  <?php 
for ($m=0; $m<=59; $m=$m+5)
  {
  if ($m<10)
  {
  $m="0".$m;
  }
  ?>
  <option value="<?= $m; ?>"><?= $m; ?></option>
  <?php 
  }
?>
    </select>
    </td>
  </tr>
<?php 
if (($nivel_acceso==4) or ($nivel_acceso==3) or ($zona_actividades==1)) {
?>
  <tr>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Escuela:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><select name="id_centro_cita" style="font-size=10" onchange="cambia_listado()">
    <option value="0">Común</option>
    <?php 
    $busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
    while($lista_centros=db_fetch_array($busca_centros)){
    if (($nivel_acceso==4) or ($centro_actual==$lista_centros["id_centro"]) or ($usuario_multicentro==1)) {
    ?>
    <option value="<?=$lista_centros["id_centro"];?>"><?=$lista_centros["nombre_centro"];?></option>
    <?php 
    }
    }
    ?></select></td>
  </tr>
</table>
<?php 
    $busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
    while($lista_centros=db_fetch_array($busca_centros)){

    $centro_actual_lista=$lista_centros["id_centro"];
    $array_para_javascript="";
    $busca_aulas=db_query("SELECT *, centro_aulas.nombre AS nombre_aula, cursos_academicos.nombre AS nombre_curso FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_centro=".$lista_centros["id_centro"]." AND centro_aulas.id_curso=cursos_academicos.id_curso AND cursos_academicos.id_curso=$id_curso ORDER BY centro_aulas.id_curso DESC,centro_aulas.nombre");

    $visible_inicial="none";

?>
<div id="aulas<?=$centro_actual_lista?>" style="display:<?=$visible_inicial;?>">
<table border="1" width="100%" cellspacing="0">
  <tr>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Aula:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><select name="id_aula_cita<?=$lista_centros["id_centro"];?>" style="font-size=10">
    <option value="0">Común</option>
    <?php 
    while($lista_aulas=db_fetch_array($busca_aulas)) {
    ?>
    <option value="<?=$lista_aulas["id_aula"];?>"><?=$lista_aulas["nombre_aula"];?> Curso: <?=$lista_aulas["nombre_curso"]?></option>
    <?php 
    }
    ?></select></td>
  </tr>
  </table>
</div>
<?php 
}
}
?>
<table border="1" width="100%" cellspacing="0">
  <tr>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Avisar:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><font face="Arial" color="#800000" size=2><select style="font-size=10" name="avisar">
    <option value="0">No avisar</option>
    <option value="1">1 día antes</option>
    <?php  for($c_dias=2;$c_dias<=30;$c_dias++) { ?>
    <option value="<?=$c_dias?>"><?=$c_dias?> días antes</option>
    <?php  } ?>
    </select> <input type="checkbox" value="1" name="enviarmail" disabled> Notificar por email.</font></td>
  </tr>
<?php 
if (($nivel_acceso==4) or ($nivel_acceso==3) or ($zona_actividades==1)) {
?>
  <tr valign="top">
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Visible por:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><font face="Arial" color="#800000" size=2>
    <input type="radio" name="solo_yo" value="1" checked> Solo yo<br>
    <input type="radio" name="solo_yo" value="0"> Todos los...<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel4" value="1"> Administradores<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel3" value="1"> Coordinadoras de centro<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel2" value="1"> Educadoras<br>
    <?php 
	if (($nivel_acceso==4) or ($nivel_acceso==3)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel10" value="1"> Personal cocina<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel11" value="1"> Personal no docente<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="asistentes_nivel1" value="1"> Padres<br>
    <?php 
    }
    ?>
    <font color="red"><small>* Para poder seleccionar los asistentes individualmente es necesario guardar primero la cita en la agenda. Una vez guardada, abrela para modificarla y selecciona los asistentes de la lista.</small></font>
    </font></td>
  </tr>
<?php 
} else {
?>
  <tr valign="top">
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Visible por:</b></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><font face="Arial" color="#800000" size=2>
    <input type="checkbox" name="solo_yo" value="1"> Solo yo<br>
    </font></td>
  </tr>
<?php 
}
?>
  <tr>
    <td valign="top" width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Actividad:</b></small></font></td>
    <td valign="top" width="83%" bgcolor="#fff8c6" colspan=3><input type="text" size=58 style="font-size=10" name="Titulo"></td>
  </tr>
  <tr>
    <td valign="top" width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><b>Descripción:</b></small></font></td>
    <td valign="top" width="83%" bgcolor="#fff8c6" colspan=3><small><textarea name="Texto" cols=36 rows=6></textarea></small></td>
  </tr>
</table>
</center>
</div>
<p align="center">
<input type="submit" value="Publicar actividad" style="fnt-size=10" style="font-size=10"><?php 
 if ($origen_cita!=1) { ?><input type="button" onclick="history.go(-1)" value="Volver"  style="font-size=10"> <?php  } ?><input type="button" onclick="window.close()" value="Cerrar ventana"  style="font-size=10">
</p>
</form>
<script language="Javascript">
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
