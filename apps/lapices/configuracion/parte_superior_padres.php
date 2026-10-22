<?php  require("configuracion/config.php"); ?>
<?php  require("configuracion/autentifica_padre.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<link href='https://fonts.googleapis.com/css?family=Ovo&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Delius+Swash+Caps' rel='stylesheet' type='text/css'>
<link href='/css/padres.css' rel='stylesheet' type='text/css'>
<?php  require("scripts/scripts.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0; background-repeat: no-repeat" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="3" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10><?php 
if (!empty($_SESSION['permitido_padre']))
{
?>
<br><br><a title="Cerrar sesión" href="<?= $url_root; ?>/configuracion/cerrar_sesion_padres.php"><img src="<?= $url_root; ?>/imagen/menu/fin.gif" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
}
?></td>
    </td>
  </tr>
  <tr>
    <td colspan=3 style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
  </tr>
  <tr>
    <td width="15"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=15 height=600></td>
    <td width="<?= $ancho_pagina_total-30; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
