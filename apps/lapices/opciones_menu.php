<?php 
require("configuracion/config.php");
require("configuracion/parte_superior.php");

$submenu_activo=$_GET["Submenu"];

$tabla_menu="menu_principal";

$link_ayuda_m="";

if (strlen($submenu_activo)==0)
{
  $submenu_activo="0";
}

if ($submenu_activo=="0")
{
  require("inc/default_curso.php");
}
  else
{

  $db_m1=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
  db_select_db("$sql_db",$db_m1);

  $consulta=db_query("SELECT * FROM ".$tabla_menu." WHERE IdSubmenu='$submenu_activo' AND Encabezado=0 AND Activo=1 ORDER BY id_menu");

  $rsopciones=db_fetch_array($consulta)
?>
<table width="100%" border="0" cellpadding=0 cellspacing=0>
<tr>
        <td width="8"><img src="<?php    echo $url_web; ?>/imagen/esquina_si_gris.gif" border=0 width=8 height=8></td>
        <td bgcolor="#cfcfcf"><img src="<?php    echo $url_web; ?>/imagen/nada.gif" border=0 width=1 height=8></td>
        <td width="8"><img src="<?php    echo $url_web; ?>/imagen/esquina_sd_gris.gif" border=0 width=8 height=8></td>
</tr>
<tr>
<td width="8" bgcolor="#cfcfcf"><img src="<?php    echo $url_web; ?>/imagen/nada.gif" border=0 width=8 height=8></td>
<td bgcolor="#cfcfcf"><font size=2 face="<?php    echo $fuente_general; ?>">Opciones de: <b>"<?php    echo $rsopciones["Submenu"]; ?>"</b></font></td>
<td width="8" bgcolor="#cfcfcf"><img src="<?php    echo $url_web; ?>/imagen/nada.gif" border=0 width=8 height=8></td>
</tr>
</table>
<table width="100%" border="1" style="border-collapse: collapse" bordercolor="#cdcdcd" bgcolor="#efefef">
<?php  

  $numero_filas=2;
  $cuenta_filas=1;

  $consulta=db_query("SELECT * FROM ".$tabla_menu." WHERE IdSubmenu='$submenu_activo' AND Encabezado=0 AND Activo=1 ORDER BY id_menu");
  
  while ($rsopciones=db_fetch_array($consulta))
  {
      $texto_ayuda=$rsopciones["Texto_ayuda"];
      if (strlen($texto_ayuda)>0)
      {
        for ($contador_pos=1; $contador_pos<=strlen($texto_ayuda); $contador_pos=$contador_pos+1)
        {
          if (substr($texto_ayuda,$contador_pos-1,8)=="<BR><BR>" || substr($texto_ayuda,$contador_pos-1,8)=="<br><br>")
          {
            break;
          } 
        } 
        $texto_ayuda=substr($texto_ayuda,strlen($texto_ayuda)-(strlen($texto_ayuda)-$contador_pos-7));
        if (strlen($texto_ayuda)==0) {
                                     $texto_ayuda=$rsopciones["Texto_ayuda"];
                                     }
      } 
      if ($cuenta_filas==1)
      {
?>
<tr valign="top">
<?php  
      } 
?>
<td width="50%">
<table width="100%" cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="right"><font face="<?php        echo $fuente_general; ?>" size=2><?php  
      if (substr($rsopciones["link"],0,10)=="javascript")
      {
        $accion_link=$rsopciones["link"];
        $accion_link=str_replace("<+Url_web+>",$url_web,$accion_link);
?>
        <a <?php          echo $link_ayuda; ?>href="#" onClick="<?php          echo $accion_link; ?>" class="opciones_menu"><?php          echo $rsopciones["texto"]; ?></a>
  <?php  
      }
        else
      {
        if (($rsopciones["Contenido"]==1) && ($rsopciones["id_contenido"]!=0))
        {
          $separador="&";
        }
          else
        {
          $separador="?";
        }
        if ($rsopciones["link"]=="/opciones_menu.php")
        {
          $submenu_proximo=$rsopciones["id_menu"];
        }
          else
        {
          $submenu_proximo=$rsopciones["IdSubmenu"];
        }
?>
        <a <?php          echo $link_ayuda; ?>href="<?= $url_web.$rsopciones["link"].$separador; ?>Submenu=<?= $submenu_proximo; ?>&al=<?= round(100000*(mt_rand(0,10000000)/10000000),0); ?>" class="opciones_menu"><?= $rsopciones["texto"]; ?></a>
  <?php  
      } 
?><img src="<?= $url_web; ?>/imagen/Vinyeta.gif" border=0 align="absmiddle"></font></td>
<td><br><font face="<?= $fuente_general; ?>" size=1><?= $texto_ayuda; ?></font></td>
</tr>
</table>
</td>
<?php  
      if ($cuenta_filas==2)
      {
        $cuenta_filas=0;
?>
</tr>
<?php  
      } 
      $cuenta_filas=$cuenta_filas+1;
  }
?>
</table>
<?php  
} 

require("configuracion/parte_inferior.php");
?>
