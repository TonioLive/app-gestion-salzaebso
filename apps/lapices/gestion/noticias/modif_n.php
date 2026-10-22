<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/parte_superior.php");

mt_srand((double)microtime()*1000000);
$id=(mt_rand(0,10000000)/10000000);
$codigo=$_GET["noticia"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$consulta_noticia=db_query("SELECT * FROM NoticiasN WHERE Cod=".$codigo);


if (db_num_rows($consulta_noticia) != 0)
{
  $rs1=db_fetch_array($consulta_noticia);
  $titular=$rs1["Texto_Titular"];
  $texto=$rs1["Texto"];
  $nombre_img=$rs1["Nombre_img"];
  $texto=str_replace("<br>",chr(13),$texto);
  $fecha_cad=$rs1["Fecha_cad"];
  $dia_cad=date("d",strtotime($fecha_cad));
  $mes_cad=date("m",strtotime($fecha_cad));
  $ano_cad=date("Y",strtotime($fecha_cad));
}

$ancho_tabla=$ancho_pagina_total-185
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
<form method="POST" action="modif_noticia2.php?Submenu=<?php  echo $submenu_activo; ?>" id=form1 name="form_noticias">
<input type="hidden" name="noticia" value="<?php  echo $codigo; ?>">
  <tr>
    <td><p align="center"><font size=2 face="<?=$fuente_general?>">Desde está página puedes modificar las noticias del tablón de
    anuncios del curso.<br>
    Cuando termines de editar la noticia, pulsa el botón <em>Enviar</em> para aplicar los cambios<br>
    </p></font>
    <div align="center"><center><table border="0" width="80%" cellpadding="4"
    bgcolor="#C0C0C0">
                <tr><td width="100%" align="center">
        <small><strong>Titular: <input type="text" name="titular" style="font-size: 10" size=80 value="<?=$titular?>"></strong></small>
        </td></tr>
                <tr><td width="100%" align="center">
        <small><strong>Imagen: <input type="text" name="Nombre_img" style="font-size: 10" size=40 readonly value="<?=$nombre_img?>"> <input type="button" value="borrar imagen" onclick="window.document.form_noticias.Nombre_img.value='';" style="font-size: 10px" id=button2 name=button2></strong></small>
        </td></tr>
                <tr><td width="100%" align="center">
        <small><strong>Texto de la noticia: </strong></small>
        </td></tr>
        <tr><td align="center">
          <textarea rows="8" name="Texto_not" cols="69"><?=$texto?></textarea>
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
    <option value="<?php    echo $dia_a; ?>"<?php if ($dia_a==$dia_cad) { print(" selected");}?>><?php    echo $dia_a; ?></option>
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
    <option value="<?php    echo $mes_a; ?>"<?php if ($mes_a==$mes_cad) { print(" selected");}?>><?php    echo $mes_a; ?></option>
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
    <option value="<?php    echo $ano_a; ?>"<?php if ($ano_a==$ano_cad) { print(" selected");}?>><?php    echo $ano_a; ?></option>
    <?php 

}

?>
</select>
&nbsp; <small><small>dd/mm/aaaa</small></small><br><br>
          <input type="submit" value="Enviar" name="B1" style="font-size=10"><input type="button" value="Cancelar" onclick="window.location.href='modif_noticia.php?Submenu=<?=$submenu_activo;?>'" style="font-size=10">
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

