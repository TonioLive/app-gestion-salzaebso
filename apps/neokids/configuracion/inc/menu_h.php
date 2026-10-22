<div align="right">
<?php  
$submenu_activo=$_GET["Submenu"];

$tabla_menu="menu_principal";

if (strlen($submenu_activo)==0)
{
  $submenu_activo="0";
} 

?>
<br>
<table border="0" cellpadding=0 cellspacing=0 width="<?php  echo $ancho_pagina_total-5; ?>">
<tr>
        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si_gris.gif" border=0 width=8 height=8></td>
        <td bgcolor="#cfcfcf"><img src="<?php  echo $url_root; ?>/imagen/nada.gif" border=0 width=1 height=1></td>
        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd_gris.gif" border=0 width=8 height=8></td>
</tr>
</table>
<table border="1" style="border-collapse: collapse" bordercolor="#acacac" cellpadding=0 cellspacing=0 width="<?php  echo $ancho_pagina_total-5; ?>">
<tr bgcolor="#cfcfcf">
<?php  
$columna_actual=0;
if ($path_index==$url_root."/index.php")
{

  $n1="<b>";
  $n2="</b>";
}
  else
{

  $n1="";
  $n2="";
} 

$columna_actual=$columna_actual+1;
?>
<td align="center"><font face="<?php  echo $fuente_general; ?>"><small><a href="<?php  echo $url_root; ?>/index.php"><?php  echo $n1; ?>Página principal<?php  echo $n2; ?></a></small></td>
<?php  

$db_m1=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta=db_query("SELECT * FROM " . $tabla_menu . " WHERE Encabezado=1 AND Activo=1 ORDER BY id_menu");

if (!$consulta) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

$fila_actual=1;
$registro_actual=0;
$numero_registros=db_num_rows($consulta);

while ($rssubmenu=db_fetch_array($consulta))
{

  $submenu_sel=$rssubmenu["IdSubmenu"];
  
?>
<td align="center">
<font face="<?php    echo $fuente_general; ?>"><small>
<?php  
  if (substr($rssubmenu["link"],0,10)=="javascript")
  {

    $accion_link=$rssubmenu["link"];
    $accion_link=str_replace("<+Url_web+>",$url_root,$accion_link);
    $accion="onClick=\"".$accion_link."\" ";
    $vinculo_menu="#";
  }
    else
  {

    $accion="";
    if (($rssubmenu["Contenido"]==1) && ($rssubmenu["id_contenido"]!=0))
    {

      $separador="&";
    }
      else
    {

      $separador="?";
    } 

    $vinculo_menu=$url_root.$rssubmenu["link"].$separador."Submenu=".$submenu_sel;
  } 

  mt_srand((float)microtime()*1000000);
  $vinculo_menu=$vinculo_menu."&al=".round(100000*(mt_rand(0,10000000)/10000000),0);
  $texto_ayuda=$rssubmenu["Texto_ayuda"];
  if ($path_index==$url_root."/index_curso.php" && strlen($texto_ayuda)>0)
  {

    $link_ayuda_m="OnMouseOver=\"seguir_xy('capa1',".$rssubmenu["Altura_ayuda"].",'".$texto_ayuda."',".$rssubmenu["Desfase_y"].",".$rssubmenu["Desfase_x"].")\" OnMouseOut=\"ocultar('capa1')\" ";
  }
    else
  {

    $link_ayuda_m="";
  } 

?>
<a <?php  echo $link_ayuda_m; ?><?php    echo $accion; ?>href="<?= $vinculo_menu; ?>"><?php 
   if ($rssubmenu["IdSubmenu"]==intval($submenu_activo) && $rssubmenu["Encabezado"]==true)
  {
    if ($path_index==$url_web."/pagina.php") {
        $pos = strpos ($rssubmenu["link"], "id=".$_GET["id"]);
        if ($pos != false) {
                              print "<b>";
                             }
                                             } else {
                                                    print "<b>";
                                                    }
  }
   ?><?php  echo $rssubmenu["texto"]; ?><?php 
   if ($rssubmenu["IdSubmenu"]==intval($submenu_activo) && $rssubmenu["Encabezado"]==true)
  {
    if ($path_index==$url_web."/pagina.php") {
        $pos = strpos ($rssubmenu["link"], "id=".$_GET["id"]);
        if ($pos != false) {
                              print "</b>";
                             }
                                             } else {
                                                    print "</b>";
                                                    }
  }
  ?></a>
</small>
</font>
</td>
<?php  

  $registro_actual=$registro_actual+1;
  $columna_actual=$columna_actual+1;

  if ($columna_actual==$numero_columnas_menu && $registro_actual!=$numero_registros)
  {
    $columna_actual=0;
    $fila_actual=$fila_actual+1;
?>
</tr><tr bgcolor="#cfcfcf">
<?php  
  } 

} 

db_close($db_m1);

if ($fila_actual>1)
{

  for ($nc=$columna_actual; $nc<=$numero_columnas_menu-1; $nc=$nc+1)
  {

?>
<td><small><small>&nbsp;</small></small></td>
<?php  

  } 

} 

?>
</tr>
</table>
<table border="0" cellpadding=0 cellspacing=0 width="<?php  echo $ancho_pagina_total-5; ?>">
        <tr>
                <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_ii_gris.gif" border=0 width=8 height=8></td>
                <td        bgcolor="#cfcfcf"><img src="<?php  echo $url_root; ?>/imagen/nada.gif" border=0 width=1 height=1></td>
                <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_id_gris.gif" border=0 width=8 height=8></td>
        </tr>
</table>
<br></div>
