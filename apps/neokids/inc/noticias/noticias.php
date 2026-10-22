<?php  

if (strlen($num_not_p_ini)==0)
{
  $num_not_p_ini=3;
} 

$query="SELECT * FROM NoticiasN WHERE Activada=1 ORDER BY Fecha_ins DESC;";

$db_m4=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m4);
$rsSlc_query=db_query(($query),$db_m4);

if (db_num_rows($rsSlc_query) != 0)
{
?>
    <table width="100%" border=0 cellpadding=0 cellspacing=0>
      <tr>
        <td width="100%" valign="middle"><b><font face="<?= $fuente_general; ?>"
        color="<?= $color_fuente; ?>"><small>Noticias</small></b></font><br>
        <a href="<?= $url_root; ?>/mas_noticias.php?Submenu=<?= $submenu_activo; ?>"><small><small>Haz click aquí para ver más<img src="<?= $url_root; ?>/imagen/Vinyeta.gif" border=0 align="absmiddle"></a><br>
        <img src="<?= $url_root; ?>/imagen/Linea_horizontal.gif" width="100%" height="5">
        </td>
      </tr>
      <tr>
        <td width="100%" valign="top">
        <table width="100%">
<?php 
  $cuenta=0;
  
  while($rsslc=db_fetch_array($rsSlc_query))
  {
    $texto_not=$rsslc["Texto"];
    $fecha_noticia=date("j/n/Y",strtotime($rsSlc["Fecha_ins"]));
    if ($lonfitud_not!="X")
    {

      $cadena=substr($texto_not,0,$lonfitud_not);

      $longitud1=strlen($texto_not);
      $altura=round((235+($longitud1/47)*15),0);
      if ($altura>525)
      {
        $altura=525;
      } 

      $posicion=0;
      $cuenta_espacio=0;
      for ($caracter=1; $caracter<=strlen($cadena); $caracter=$caracter+1)
      {

        if (substr($cadena,$caracter-1,1)==" ")
        {

          $cuenta_espacio=$cuenta_espacio+1;
          $posicion=$caracter;
          if ($cuenta_espacio==3)
          {
            break;
          } 

        } 


      } 


      $parte_b=substr($cadena,strlen($cadena)-(200-$posicion));

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


    if (strlen($rsslc["Nombre_img"])>0)
    {


      $imagen_n="<img src='" . $url_root . "/descarga/".$rsslc["Nombre_img"]."' align=center width='75'>";

    }
      else
    {


      $imagen_n="<img src='" . $url_root . "/imagen/nada.gif' align=center width='75' height='1'>";

    } 


    $noticia_n="<font size=1 face='".$fuente_general."'>".$texto_not."<br><br><b>Fecha de publicación:</b> <i>".$fecha_noticia."</i></font>";

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
                <td><font face="<?= $fuente_general; ?>"><small><a href="javascript:openWindow('<?= $url_root; ?>/ampliar.php?N=<?= $rsslc["Cod"]; ?>',400,<?= $altura; ?>,<?= $rsslc["Cod"]; ?>)"><b><?= $rsslc["Texto_Titular"]; ?></b><img src="<?= $url_root; ?>/imagen/Vinyeta.gif" border=0 align="absmiddle"></a><br><small></td>
        </tr>
                <tr valign="top">
                <td><?php      echo $izquierda_n; ?></td>
                <td><?php      echo $derecha_n; ?></td>
        </tr>
        </table>
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
db_close($db_m4);
?>
