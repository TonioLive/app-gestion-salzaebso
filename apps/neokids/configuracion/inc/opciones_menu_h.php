<?php  
$submenu_activo=$_GET["Submenu"];

$tabla_menu="menu_principal";

if (strlen($submenu_activo)==0)
{
  $submenu_activo="0";
} 

?>
<table border="0" width="175" cellpadding=0 cellspacing=0>
<?php  
$query="SELECT * FROM ".$tabla_menu." WHERE idSubmenu=".$submenu_activo." AND Activo=1 AND Encabezado=0 ORDER BY id_menu;";

$db_m1=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta=db_query($query);

if (!$consulta) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

$hay_opciones=false;

if (db_num_rows($consulta) != 0)
{
  $hay_opciones=true;
} 
$contador_opciones=0;
while($rssubmenu=db_fetch_array($consulta))
{
if ($contador_opciones==0) {
?>
<tr>
        <td align="left"><small>&nbsp;</small><font size=2 face="<?php    echo $fuente_general; ?>" color="<?php    echo $color_fuente_opciones; ?>">Opciones de:</font><font size=1 face="<?php    echo $fuente_general; ?>"><br>
                <div align="center"><b>"<?php    echo $rssubmenu["Submenu"]; ?>"</b></font><br>
                <img src="<?php    echo $url_root; ?>/imagen/Linea_horizontal.gif" width="95%" height="5">
                </div>
        </td>
</tr>
<?php 
  }
  $contador_opciones=$contador_opciones++;
  
  $texto_ayuda_2=$rssubmenu["Texto_ayuda"];
  if (strlen($texto_ayuda_2)>0)
  {

    $link_ayuda="OnMouseOver=\"seguir_xy('capa1',".$rssubmenu["Altura_ayuda"].",'".$texto_ayuda_2."',".$rssubmenu["Desfase_y"].",".$rssubmenu["Desfase_x"].")\" OnMouseOut=\"ocultar('capa1')\" ";
  }
    else
  {

    $link_ayuda="";
  } 

?>
  <tr>
  <td valign="top" align="left">
  <font face="<?php    echo $fuente_general; ?>" color="<?php    echo $color_menu; ?>"><small><img src='<?php    echo $url_root; ?>/imagen/menu/nada.gif' border='0' width=5 height=1>
  <?php  
  if (substr($rssubmenu["link"],0,10)=="javascript")
  {

    $accion_link=$rssubmenu["link"];
    $accion_link=str_replace("<+Url_web+>",$url_root,$accion_link);
?>
        <a <?php      echo $link_ayuda; ?>href="#" onClick="<?php      echo $accion_link; ?>"><img src="<?php      echo $url_root; ?>/imagen/Vinyeta.gif" border=0 align="absmiddle">&nbsp;<?php      echo $rssubmenu["texto"]; ?></a>
  <?php  
  }
    else
  {

    if ($rssubmenu["Contenido"]==true)
    {

      $separador="&";
    }
      else
    {

      $separador="?";
    } 

?>
        <a <?php      echo $link_ayuda; ?>href="<?php      echo $url_root.$rssubmenu["link"].$separador; ?>Submenu=<?php      echo $rssubmenu["IdSubmenu"]; ?>&al=<?php      echo round(100000*(mt_rand(0,10000000)/10000000),0); ?>"><img src="<?php      echo $url_root; ?>/imagen/Vinyeta.gif" border=0 align="absmiddle">&nbsp;<?php      echo $rssubmenu["texto"]; ?></a>
  <?php  
  } 

?>
  </small><br>
  </td>
  </tr>
<?php  
}
db_close($db_m1);
?>
</table><br>
