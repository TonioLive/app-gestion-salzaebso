<?php 

$nombre_pagina=$path_index;


$criterio=$_GET["c"];
$tipo_b=$_GET["Tipo_b"];
$ampliadas=$_GET["ampliadas"];

if (strlen($tipo_b)==0)
{
  $tipo_b="OR";
} 
$inicio=$_GET["Inicio"];
$total_abs=$_GET["T"];

if (strlen($criterio)==0)
{

  $query="SELECT * FROM NoticiasN WHERE Activada=1 ORDER BY Fecha_ins DESC;";
}
  else
{

  $inicio_p=1;
  $fin=1;
  $palabra_actual=1;
  $query="SELECT * FROM NoticiasN WHERE Activada=1 AND ";
  for ($cont=1; $cont<=strlen($criterio); $cont=$cont+1)
  {

    if (substr($criterio,$cont-1,1)==" " || $cont==strlen($criterio))
    {

      $fin=$cont;
      if ($cont==strlen($criterio))
      {

        $palabra[$palabra_actual]=substr($criterio,$inicio_p-1,$fin-$inicio_p+1);
      }
        else
      {

        $palabra[$palabra_actual]=substr($criterio,$inicio_p-1,$fin-$inicio_p);
      } 

      $query=$query."((Texto like '%".$palabra[$palabra_actual]."%') OR (Texto_titular like '%".$palabra[$palabra_actual]."%')) ".$tipo_b." ";
      $palabra_actual=$palabra_actual+1;
      $inicio_p=$cont+1;
      if ($palabra_actual==11)
      {
        break;
      } 

    } 


  } 

  if ($tipo_b=="OR")
  {
    $cacho=4;
  } 
  if ($tipo_b=="AND")
  {
    $cacho=5;
  } 
  $query=substr($query,0,(strlen($query)-$cacho));
  $query=$query." ORDER BY Fecha_ins DESC;";

} 

?>
<table border=0 width="100%">
      <tr>
        <td width="100%" valign="top" bgcolor="<?php  echo $fondo_at; ?>"><p align="left"><font face="<?php  echo $fuente_general; ?>"
        color="<?php  echo $color_fuente; ?>"><strong><small>Noticias</strong> <small>Haz Click sobre el titular para ampliar la noticia</small></small></font></td>
      </tr>
      <tr>
      <td width="100%" valign="top">
      <tr>
      <td width="100%" valign="top" align="center">
        <font face="<?php  echo $fuente_general; ?>">
<small>
<form action="<?php  echo $nombre_pagina; ?>" method="GET">
<input type="hidden" name="Submenu" value="<?php  echo $submenu_activo; ?>">
Buscar: <input type="text" name="c" value="<?php  echo $criterio; ?>" size=60 style="font-size: 10"> <input type="submit" value="Buscar!" style="font-size: 10">
<br><small>Frase completa <input type="radio" name="Tipo_b" <?php  if ($tipo_b=="AND")
{
  print "checked ";} ?>value="AND"> Alguna de las palabras <input type="radio" <?php  if ($tipo_b!="AND")
{
  print "checked ";} ?>name="Tipo_b" value="OR"> Mostrar las noticias completas <input type="checkbox" name="ampliadas" value="1"<?php  if ($ampliadas=="1") { echo(" checked"); } ?>>
</small></small>
</font>
</td>
</tr>
</form>
<tr>
<?php 
// Ejecutamos la consulta

$db_n2=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_n2);
$rsSlc_query=db_query(($query),$db_n2);


if (db_num_rows($rsSlc_query) == 0)
{

?>
<tr><td align="center"><small><b><font face=<?php    echo $fuente_general; ?>>
No se han encontrado noticias<?php 
if (strlen($criterio)>0) {
?> con el texto: </b><i><?php  echo $criterio; ?></i><?php 
 }
 ?></font></small>
</tr></td>
<?php  
}  else {

// Hemos encontrado algo... pues lo mostramos

if (strlen($total_abs)==0)
{
    $total=db_num_rows($rsSlc_query);
}

$ultimo=0;

while($rsSlc=db_fetch_array($rsSlc_query))
{
  $ultimo=$ultimo+1;
  $texto_not=$rsSlc["Texto"];
  $longitud1=strlen($texto_not);
  $altura=round((235+($longitud1/47)*15),0);
  if ($altura>525)
  {
    $altura=525;
  } 
  $posicion=0;
  $cadena=$rsSlc["Texto_Titular"];
  $fecha_noticia=date("j/n/Y",strtotime($rsSlc["Fecha_ins"]));
  $codigo_noticia=$rsSlc["Cod"];

  if ($ultimo>=$inicio) {
  
  if ($ampliadas!="1") {
?>
<tr><td>
<font face="<?php  echo $fuente_general; ?>">
<small><b><?php  echo $fecha_noticia; ?></b> <a href="javascript:openWindow('<?php  echo $url_root; ?>/ampliar.php?N=<?php  echo $codigo_noticia; ?>',400,<?php  echo $altura; ?>,<?php  echo $codigo_noticia; ?>)"> <?php  echo $cadena; ?></a></small>
</font>
</td></tr>
<?php  
  } else {
  
    if ($lonfitud_not!="X")
    {
      $cadena=substr($texto_not,0,$lonfitud_not);

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
    if ($rsslc["Cod"]/2==round($rsSlc["Cod"]/2,0))
    {
      $alineacion="left";
    }
      else
    {
      $alineacion="right";
    }


    if (strlen($rsSlc["Nombre_img"])>0)
    {
      $imagen_n="<img src='" . $url_root . "/descarga/".$rsSlc["Nombre_img"]."' align=center width='75'>";
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
  }
  }
  if ($ultimo==$inicio+$num_not-1)
          {
            break;
          }
} 
if (strlen($total_abs)==0)
{
  $total_abs=$total;
}
  else
{
  $total=$total_abs;
} 

if ($total>0)
{

?>
<tr>
<td>
<font face="<?php    echo $fuente_general; ?>"><small><br>
<b>Noticias encontradas:</b> <?php    echo $total; ?><br>
<?php  
  $paginas=round($total/$num_not,0);
  if ($paginas<$total/$num_not)
  {
    $paginas=$paginas+1;
  } 
  $pagina_act=($inicio/$num_not)+1;
  if ($inicio>0)
  {
    print "<a href='".$nombre_pagina."?ampliadas=".$ampliadas."&Submenu=".$submenu_activo."&t=".$total_abs."&c=".$criterio."&Tipo_b=".$tipo_b."&Inicio=".($inicio-$num_not)."'>[Anterior]</a> ";  }


  for ($pag=1; $pag<=$paginas; $pag=$pag+1)
  {

    if ($pag==$pagina_act)
    {
      print "[".$pag."]";
    }
      else
    {
      print "<a href='".$nombre_pagina."?ampliadas=".$ampliadas."&Submenu=".$submenu_activo."&t=".$total_abs."&c=".$criterio."&Tipo_b=".$tipo_b."&Inicio=".(($pag*$num_not)-$num_not)."'>".$pag."</a> ";
    } 
  } 
  if (intval($ultimo)!=intval($total))
  {
    print " <a href='".$nombre_pagina."?ampliadas=".$ampliadas."&Submenu=".$submenu_activo."&t=".$total_abs."&c=".$criterio."&Tipo_b=".$tipo_b."&Inicio=".($inicio+$num_not)."'>[Siguiente]</a>";  }

?>
</small></font>
</td>
</tr>
<?php  
} 
// Hasta aquí las noticias mostradas
}
db_close($db_n2);

?>
</table>

