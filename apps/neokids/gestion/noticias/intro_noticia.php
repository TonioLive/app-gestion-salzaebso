<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/parte_superior.php");

mt_srand((float)microtime()*1000000);
$id=(mt_rand(0,10000000)/10000000);

$ancho_tabla=$ancho_pagina_total-185;
?>
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si_at.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="<%= $fondo_at%>"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd_at.gif" border=0 width=8 height=8></td>
                </tr>
                <tr>
                        <td width="<?= $ancho_tabla; ?>" valign="top" bgcolor="<%= $fondo_at%>" colspan=3 align="center">
                            <font size="3" color="#000000"><b>Gestión del tablón de anuncios</font></b>
                        </td>
                </tr>
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_ii_at.gif" border=0 width=8 height=8></td>
                        <td width="<?= $ancho_tabla-16; ?>" bgcolor="<%= $fondo_at%>"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=<?= $ancho_tabla-16; ?> height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_id_at.gif" border=0 width=8 height=8></td>
                </tr>
    </table><br>
<input type="button" value="Galería de imágenes" onclick="javascript:window.open('<?php  echo $url_web; ?>/contenidos/galeria.php','OpenWindow17880','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=no,width=300,height=450,titlebar=0,minimize=no,maximize=no')" style="font-size: 10px" id=button1 name=button1>
<table border="0" width="100%">
<form method="POST" action="intro_noticia2.php?Submenu=<?php  echo $submenu_activo; ?>" id=form1 name="form_noticias">
  <tr>
    <td><p align="center"><font size=2 face="<?=$fuente_general?>">Desde está página puedes insertar noticias en el tablón de
    anuncios del curso.<br>
    Escribe el texto que deseas publicar y pulsa el botón <em>Enviar</em><br>
    </p></font>
    <div align="center"><center><table border="0" width="80%" cellpadding="4"
    bgcolor="#C0C0C0">
                <tr><td width="100%" align="center">
        <small><strong>Titular: <input type="text" name="titular" style="font-size: 10" size=80></strong></small>
        </td></tr>
                <tr><td width="100%" align="center">
        <small><strong>Imagen: <input type="text" name="Nombre_img" style="font-size: 10" size=40 readonly> <input type="button" value="borrar imagen" onclick="window.document.form_noticias.Nombre_img.value='';" style="font-size: 10px" id=button2 name=button2></strong></small>
        </td></tr>   
                <tr><td width="100%" align="center">
        <small><strong>Texto de la noticia: </strong></small>
        </td></tr>
        <tr><td align="center">
          <textarea rows="8" name="Texto_not" cols="69"></textarea>
        </td></tr>
        <tr><td align="center">          
                  <small>Fecha de caducidad de la noticia:</small>
                  <select name="dia" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php  
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{

  if ($c_dia<10)
  {

    $dia_a="0".$c_dia;
  }
    else
  {

    $dia_a=$c_dia;
  } 

?>
    <option value="<?php    echo $dia_a; ?>"><?php    echo $dia_a; ?></option>
    <?php  

} 

?>
        </select>/<select name="mes" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php  
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{

  if ($c_mes<10)
  {

    $mes_a="0".$c_mes;
  }
    else
  {

    $mes_a=$c_mes;
  } 

?>
    <option value="<?php    echo $mes_a; ?>"><?php    echo $mes_a; ?></option>
    <?php  

} 

?>
        </select>/<select name="ano" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php  
for ($c_ano=strftime("%Y"); $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
{

  if ($c_ano<10)
  {

    $ano_a="0".$c_ano;
  }
    else
  {

    $ano_a=$c_ano;
  } 

?>
    <option value="<?php    echo $ano_a; ?>"><?php    echo $ano_a; ?></option>
    <?php  

} 

?>
</select>
&nbsp; <small><small>dd/mm/aaaa</small></small><br><br>
          <input type="submit" value="Enviar" name="B1" style="font-size=10"><input type="button" value="Cancelar" onclick="window.location.href='<?=$url_web?>/opciones_menu.php?Submenu=<?=$submenu_activo;?>'" style="font-size=10">
          </p>
        </td>
      </tr>
    </table>
    </center></div></td>
  </tr>
</form>  
</table>
</div>
<!-- Hasta aquí -->  
<?php  require("../../configuracion/parte_inferior.php"); ?>
