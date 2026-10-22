<?php  
$parte=$_GET["p"];
if (strlen($parte)==0)
{
  $parte=1;
} 
if (strlen($_GET["id"])!=0)
        {
        $pagina_actual=$_GET["id"];
        } else {
        if (strlen($pagina_actual)==0) { $pagina_actual="0"; }
        }

$query="SELECT * FROM contenidos WHERE Pagina='" . $pagina_actual . "' AND num_act=" . $parte;

$db_m1=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta=db_query($query);

if (!$consulta) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

if (db_num_rows($consulta) != 0)
{

$rscontenido=db_fetch_array($consulta);

?>
<table BORDER="0" CELLPADDING="2" width="100%" border="1" style="border-collapse: collapse" bordercolor="#000000">
<td colspan=2>
<font face=<?php    echo $fuente_general; ?> color=<?php    echo $color_fuente; ?>>
<?php    echo $rscontenido["Texto"]; ?>
<br><br><small><small>
<?php  
  if ($rscontenido["anterior"]!=0)
  {

?>
<a href="pagina.php?Submenu=<?= $submenu_activo; ?>&p=<?= $rscontenido["anterior"]; ?>&id=<?= $pagina_actual; ?>">Página anterior</a>
<?php  
  } 

  if ($rscontenido["anterior"]!=0 && $rscontenido["siguiente"]!=0)
  {

?>
 | 
<?php  
  } 

  if ($rscontenido["siguiente"]!=0)
  {

?>                  
<a href="pagina.php?Submenu=<?= $submenu_activo; ?>&p=<?= $rscontenido["siguiente"]; ?>&id=<?= $pagina_actual; ?>">Página siguiente</a>
<?php  
  } 

?>
<br><br>
</small></small>
</font>
</td></tr>
<tr>
<td style="border-right-style: none">
<font face=<?php  echo $fuente_general; ?> color=<?php  echo $color_fuente; ?> size=1>
<small>
<b>estás en la página:</b> <?php  echo $rscontenido["titulo"]; ?>
<small>
</td><td align="right" style="border-left-style: none">
<?php 
  if ($rscontenido["num_pag_tot"]!=1)
  {

?>
<small>
<font face=<?= $fuente_general; ?> color=<?= $color_fuente; ?>>
<small><b>
Página <?= $parte; ?> de <?= $rscontenido["num_pag_tot"]; ?> | <?php 
    if ($rscontenido["anterior"]!=0)
    {

?>
<a href="pagina.php?Submenu=<?php  echo $submenu_activo; ?>&p=<?php  echo $rscontenido["anterior"]; ?>&id=<?php  echo $pagina_actual; ?>">Página anterior</a>
<?php 
    }

    if ($rscontenido["anterior"]!=0 && $rscontenido["siguiente"]!=0)
    {

?>
 |
<?php 
    }

    if ($rscontenido["siguiente"]!=0)
    {

?>
<a href="pagina.php?Submenu=<?= $submenu_activo; ?>&p=<?= $rscontenido["siguiente"]; ?>&id=<?= $pagina_actual; ?>">Página siguiente</a>
<?php 
    }

  }

?>
</b>
</td>
<tr>
</table>
<?php  
} 
?>  
