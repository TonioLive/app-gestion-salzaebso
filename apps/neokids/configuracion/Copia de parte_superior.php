<?php  require("configuracion/config.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("estilos/estilo1.php"); ?>
<?php  require("scripts/scripts.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td width="185" style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/sup_izq.gif"></td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium">
        <?php  require("inc/login_sup.php"); ?>
    </td>
  </tr>
  <tr>
    <td width="<?= $ancho_pagina_total; ?>" style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111" colspan="2">
        <?php  require("inc/menu_h.php"); ?>
        </td>
  </tr>
  <tr>
    <td width="185" style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111" valign="top">
    <div align="center">
      <center>
      <table border="0" cellpadding="0" cellspacing="0" width="175" id="AutoNumber2">
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_si.gif" border=0 width=8 height=8></td>
                        <td width="159" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=159 height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_sd.gif" border=0 width=8 height=8></td>
                </tr>
                <tr>
                        <td width="175" valign="top" bgcolor="FFFFFF" colspan=3>
                        <?php  require("inc/opciones_menu_h.php"); ?>
                        <?php  require("inc/noticias/noticias_hoy_v.php"); ?>
                        <br>
                        <font size=2 face="<?php  echo $fuente_general ; ?>"><a href="javascript:window.print();" class="opciones_menu"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 align="absmiddle" width=3 height=1><img src="<?php  echo $url_root; ?>/imagen/imprimir.gif" border=0 align="absmiddle">&nbsp;&nbsp;Imprimir página</a>
                        </td>
                </tr>
                <tr>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_ii.gif" border=0 width=8 height=8></td>
                        <td width="159" bgcolor="#ffffff"><img src="<?php  echo $url_root ; ?>/imagen/nada.gif" border=0 width=159 height=1></td>
                        <td width="8"><img src="<?php  echo $url_root; ?>/imagen/esquina_id.gif" border=0 width=8 height=8></td>
                </tr>
    </table>
    <p align="center">
<form name="casilla">
<font face="<?php  echo $fuente_general ; ?>" size=1>
<input type="checkbox" name="no_deseo" <?php  if ($HTTP_COOKIE_VARS[ $nombre_cookie  ]  == "1" )
{
  print  "checked " ;} ?>value="True" onclick="clickea()"><b>Ocultar autoayuda</b>.
</font></form>
</p>
      </center>
    </div>
    </td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
