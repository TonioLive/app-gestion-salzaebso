<?php  
$hay=false;

// Abrimos la base de datos

$db_i=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_i);

// Consultamos citas de la agenda
$rsNot_query=db_query_params('SELECT * FROM Agenda WHERE DATE_FORMAT(Fecha,\'%d/%m/%Y\')=?', 's', array((string)(strftime("%d/%m/%Y"))));
if (!$rsNot_query) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

if (db_num_rows($rsNot_query) != 0)
{
  $hay=true;
} 

?>
<table border="0" width="98%" cellspacing="0" cellpadding="0">
  <tr valign=top>
    <td width="100%" align="center">
    <font face="<?php  echo $fuente_general; ?>"><small><small><b>Actividades programadas Hoy</b></small></small></font>
  </tr>
</table>
<div align="center">
<center>
<?php  
//Encabezado
?>
<table border="1" width="98%" cellspacing="0">
<?php 
if ($hay==true)
{
?>
  <tr valign=top>
    <td width="100%" bgcolor="#E6EFF3" colspan="2"><font face="Arial" color="#800000"><small><small><small><?= strftime("%Y")." ".$nombre_mes[intval(strftime("%m"))]; ?></small></small></small><br>
    <big><?php  echo strftime("%d"); ?> </big><?php  echo ($dia_sem_largo[strftime("%w")]); ?></big></font></td>
  </tr>
<?php  

  // Citas de la agenda
  while($rsNot=db_fetch_array($rsNot_query))
  {
?>
  <tr valign=top>
    <td width="17%" bgcolor="#E6EFF3"><font face="Arial" color="#800000"><small><small><b><?= date("H:i",strtotime($rsNot["Hora"])); ?></b></small></small></font></td>
    <td width="83%" bgcolor="#E6EFF3"><small><small><b><?= $rsNot["Titulo"]; ?></b></small></small></td>
  </tr>
  <tr valign=top>
    <td width="17%" bgcolor="#E6EFF3" align="center">
    &nbsp;
    </td>
    <td width="83%" bgcolor="#E6EFF3"><small><small><?= $rsNot["Texto"]; ?></small></small></td>
  </tr>
<?php 
  }
  }  else {
?>
  <tr valign=top>
    <td width="100%" bgcolor="#E6EFF3" align="center"><small><small>No hay citas en la agenda</small></small></td>
  </tr>
<?php 
  }
?>
</table>
</center>
</div>
<?php  
  //Cierra la base de datos... que se lo merece
  db_close($db_i);

?>
