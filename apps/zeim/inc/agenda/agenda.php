<?php 

$dia_sel=$_GET["dia"];
if (strlen($dia_sel)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$dia_sel;
}

$nombres_escuelas=array();
$nombres_escuelas[0]="Actividades comunes";

$busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
while($lista_centros=db_fetch_array($busca_centros)){
        $nombres_escuelas[$lista_centros["id_centro"]]="Actividades para ".$lista_centros["nombre_centro"];
        }
        
$busca_aulas=db_query("SELECT * FROM centro_aulas ORDER BY id_aula");
$nombres_aulas=array();
$nombres_aulas[0]="Actividades comunes";
while($lista_aulas=db_fetch_array($busca_aulas)){
        $nombres_aulas[$lista_aulas["id_aula"]]="Aula ".$lista_aulas["nombre"];
        }

$fecha_agenda=date("d/m/Y",$fecha_time_stamp);

$filtro_usuarios="";
// Filtros nivel acceso
if (($nivel_acceso>=4) or ($usuario_multicentro==1)) {
        // Nada
        } else {
        $filtro_usuarios=$filtro_usuarios." AND (Agenda.id_centro=0 OR Agenda.id_centro=$centro_actual)";
        }
if (($nivel_acceso==2)) {
        $filtro_usuarios=$filtro_usuarios." AND (Agenda.id_aula=0 OR Agenda.id_aula=$aula_asignada_usuario)";
        }

$hay=false;

// Abrimos la base de datos

$db_i=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_i);

// Consultamos citas de la agenda
// $rsNot_query=mysql_query("SELECT * FROM Agenda WHERE DATE_FORMAT(Fecha,'%d/%m/%Y')='".$fecha_agenda."' ORDER BY id_centro,Hora,id_aula");
$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND DATE_FORMAT(Agenda.Fecha,'%d/%m/%Y')='".$fecha_agenda."' AND ((agenda_asistentes.id_usuario=$idUsuario) OR (agenda_asistentes.nivel=$nivel_acceso) OR (Agenda.Nombre_usu='$identificador'))$filtro_usuarios GROUP BY Agenda.id ORDER BY Agenda.id_centro,Agenda.Hora,Agenda.id_aula");

if (!$rsNot_query) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

if (db_num_rows($rsNot_query) != 0)
{
  $hay=true;
}

?>
<img src="<?php  echo $url_web; ?>/imagen/logo1.gif">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr valign=top>
    <td width="100%" align="center">
    <font face="<?php  echo $fuente_general; ?>"><small><b>Agenda de actividades</b></small></font>
  </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr valign=top>
<td valign="top">
<table border="0" width="100%" cellpadding="5" cellspacing="0">
<tr valign=top>
<td>
<div align="center">
<center>
<?php 
//Encabezado
?>
<table border="1" width="98%" cellspacing="0">
  <tr valign=top>
    <td width="100%" bgcolor="#fff8c6" colspan="2"><font face="Arial" color="#800000"><small><small><small><?= date("Y",$fecha_time_stamp)." ".$nombre_mes[intval(date("m",$fecha_time_stamp))]; ?></small></small></small><br>
    <big><?php  echo date("j",$fecha_time_stamp); ?> </big><?php  echo ($dia_sem_largo[date("w",$fecha_time_stamp)]); ?></big></font></td>
  </tr>
<?php 
if ($hay==true)
{
  $escuela_actual=-1;
  $aula_actual=-1;
  // Citas de la agenda
  while($rsNot=db_fetch_array($rsNot_query))
  {
        if ($escuela_actual!=$rsNot["id_centro"]) {
                $escuela_actual=$rsNot["id_centro"];
                ?>
                <tr valign=top>
                        <td width="100%" bgcolor="#ffd0a6" colspan=2><font face="Arial" color="#800000"><small><b><?= $nombres_escuelas[$rsNot["id_centro"]]; ?></b></small></font></td>
                </tr>
                <?php 
                }
        if ($aula_actual!=$rsNot["id_aula"]) {
                $aula_actual=$rsNot["id_aula"];
                ?>
                <tr valign=top>
                        <td width="100%" bgcolor="#fff8c6" colspan=2><font face="Arial" color="#800000"><small><b><?= $nombres_aulas[$rsNot["id_aula"]]; ?></b></small></font></td>
                </tr>
                <?php 
                }
?>
  <tr valign=top>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><small><b><?= date("H:i",strtotime($rsNot["Hora"])); ?></b></small></small></font></td>
    <td width="83%" bgcolor="#fff8c6"><small><small><b><?= $rsNot["Titulo"]; ?></b></small></small></td>
  </tr>
  <tr valign=top>
    <td width="17%" bgcolor="#fff8c6" align="center">
    <?php 
     if ($rsNot["Nombre_usu"]==$identificador) {
                     $esmia=true;
             } else {
                     $esmia=false;
             }
     
     if (($nivel_acceso==4) or ($esmia==true)) {
    ?>
    <small><small>
    <a href="<?= $url_web; ?>/agenda/modif_cita.php?id=<?= $rsNot["id"]; ?>&id_curso=<?= $id_curso; ?>">Modificar</a><br><a href="<?= $url_web; ?>/agenda/borra_cita.php?id=<?= $rsNot["id"]; ?>&id_curso=<?= $id_curso; ?>">Eliminar</a>
    </small></small>
    <?php 
    } else {
    ?>&nbsp;<?php 
    }
?>

    </td>
    <td width="83%" bgcolor="#fff8c6"><small><small><?= $rsNot["Texto"]; ?></small></small></td>
  </tr>
<?php 
  }
} else {
?>
  <tr valign=top>
    <td width="100%" bgcolor="#fff8c6" colspan="2" align="center"><font face="Arial" color="#800000" size=3>No hay actividades programadas para este día</font></td>
  </tr>
<?php 
}
?>
</table>
<small>
<br>
<br>
<a href="actividades.php?id_curso=<?= $id_curso; ?>&dia=<?= $fecha_time_stamp-86400; ?>">¿y ayer?</a> | <?php 
if (($nivel_acceso==3) or ($nivel_acceso==4))
{

?>
<a href="<?= $url_web; ?>/agenda/add_cita.php?dia=<?= $fecha_time_stamp; ?>&id_curso=<?= $id_curso; ?>">Introducir actividad</a> |
<?php  
} 

?><a href="javascript:window.close()">Cerrar ventana</a> | <a href="actividades.php?id_curso=<?= $id_curso; ?>&dia=<?= $fecha_time_stamp+86400; ?>">¿y mañana?</a>
</small>
</center>
</div>
</tr>
</td>
</table>
</tr>
</table>
</body>
</html>
</center>
</div>
<?php 
//Cierra la base de datos... que se lo merece
db_close($db_i);
?>
