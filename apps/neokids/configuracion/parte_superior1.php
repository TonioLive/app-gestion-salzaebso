<?php  require_once("configuracion/config.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="shortcut icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="http://gestion.neo-kids.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("estilos/estilo1.php"); ?>
<?php  require("scripts/scripts.php"); ?>
</head>
<body class="legacy-admin" topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="3">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
    <td colspan="2" width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=155 height=1 align=left>
        <?php  require("inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="15"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=15 height=600></td>
    <td width="<?= $ancho_pagina_total-30; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
