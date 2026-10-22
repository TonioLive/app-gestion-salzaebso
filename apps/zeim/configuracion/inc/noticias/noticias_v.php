<?php  
if (strlen($num_not_p_ini)==0)
{
  $num_not_p_ini=3;
} 

$db_m1=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m1);

$consulta=db_query("SELECT * FROM NoticiasN WHERE Activada=1 ORDER BY Fecha_ins DESC");

if (!$consulta) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

if (db_num_rows($consulta) != 0)
{

?>
    <table width="100%" border=0 cellpadding=0 cellspacing=0>
      <tr>
        <td width="100%" valign="middle"><font face="<?php    echo $fuente_general; ?>" color="<?php    echo $color_fuente_opciones; ?>">&nbsp;<small>Noticias</small></font><br>
        <div align="center"><img src="<?php    echo $url_root; ?>/imagen/Linea_horizontal.gif" width="95%" height="5"></center>
        </td>
      </tr>
      <tr>
        <td width="100%" valign="top">
        <table width="100%">
<?php 
  $cuenta=0;
  while($rsslc=db_fetch_array($consulta))
  {

    $texto_not=$rsslc["Texto"];

    $longitud1=strlen($texto_not);
    $altura=round((235+($longitud1/47)*15),0);
    if ($altura>525)
    {
      $altura=525;
    } 

    $link_ayuda_m="";

    if (strlen($texto_not)>=$lonfitud_not)
    {

      $cadena=substr($texto_not,0,$lonfitud_not);

      $posicion=0;
      for ($caracter=strlen($cadena); $caracter>=1; $caracter=$caracter-1)
      {
        if (substr($cadena,$caracter-1,1)==" ")
        {

          $posicion=$caracter;
          break;
        } 
      } 

      $parte_b=substr($cadena,0,$posicion-1);

      $texto_not=$parte_b."...<br><a href=\"javascript:openWindow('".$url_root."/ampliar.php?N=".$rsslc["Cod"]."',400,".$altura.",".$rsslc["Cod"].")\"><b>Ver noticia completa</b></a>";

      $link_ayuda_m="OnMouseOver=\"seguir_xy('capa1',60,'Para ver la noticia completa haz click sobre el titular.',15,25)\" OnMouseOut=\"ocultar('capa1')\" ";

    } 
?>
        <tr>
        <td>
<?php  
    if ($rsslc["Cod"]/2==round($rsslc["Cod"]/2,0))
    {

      $alineacion="left";
    }
      else
    {

      $alineacion="right";
    } 

    //quitando los comentarios de esta sección, activamos las imagenes en las noticias del lateral
    $imagen_n="";
    //if (strlen($rsslc["Nombre_img"])>0)
    //{
    //$imagen_n="<img src='" . $url_root . "/descarga/".$rsslc["Nombre_img"]."' align=center width='35'>";
    //}
    //  else
    //{
    //$imagen_n="<img src='" . $url_root . "/imagen/nada.gif' align=center width='35' height='1'>";
    //}


    $noticia_n="<font size=1 face='".$fuente_general."'>".$texto_not."<br><br><b>Fecha de publicación:</b> <i>".date("j/n/Y",strtotime($rsslc["Fecha_ins"]))."</i></font>";

    if (($cuenta/2)!=round($cuenta/2,0))
    {

      $izquierda_n=$imagen_n;
      $derecha_n=$noticia_n;
    }
      else
    {

      $izquierda_n=$imagen_n;
      $derecha_n=$noticia_n;
    } 

?>
                <table border="0" width="100%" cellpadding=0 cellspacing=3>
                <tr valign="top">
                <td>&nbsp;</td>
                <td><font face="<?= $fuente_general; ?>"><small><a href="javascript:openWindow('<?= $url_root; ?>/ampliar.php?N=<?= $rsslc["Cod"]; ?>',400,<?= $altura; ?>,<?= $rsslc["Cod"]; ?>)"<?= $link_ayuda_m; ?>><b><?= $rsslc["Texto_Titular"]; ?></b><img src="<?= $url_root; ?>/imagen/Vinyeta.gif" border=0 align="absmiddle"></a><br><small></td>
        </tr>
                <tr valign="top">
                <td><?php      echo $izquierda_n; ?></td>
                <td><?php      echo $derecha_n; ?></td>
        </tr>
        </table><img src="<?= $url_root; ?>/imagen/Linea_horizontal.gif" width="80%" height="5" align="right">
        </small></small>
        </font>
        </td>
        </tr>
<?php 
    $cuenta=$cuenta+1;
    if ($cuenta==$num_not_p_ini) {
                                 break;
                                 }
  }
?>
                </table>
        </td>
      </tr>
</table>
<?php 
}
db_close($db_m1);
?>
