<script language="Javascript">
function cerrar() {
        top.window.close();
        top.window.opener.close();
        }
</script>
      <table border="0" cellpadding="0" cellspacing="4" width="<?= $ancho_pagina_total-245; ?>" id="AutoNumber3" align="right">
        <tr>
<?php 
if ($permitido!=true)
{

?>
<form method="POST" action="<?= $url_root; ?>/configuracion/inicio_sesion.php" id="form1" name="form1" target="_self">
          <td width="100%" valign="bottom">
          <p align="center">
		  <table width="100%" border=0 cellpadding=0 cellspacing=0>
          <tr>
          <td><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=1 height=29></td>
          <td>
          <p align="center"><font size="1" face="Trebuchet MS"><b>Usuario:
          <input type="text" name="user" size="8" style="font-size: 10">
          &nbsp;&nbsp;Password:
          <input type="password" name="pass" size="8" style="font-size: 10">
          <input type="submit" value="Entrar" name="B1" style="font-size: 10px; font-family: Arial"></b></font>
          </td></tr>
          </table><br><a href="#" onclick="javascript:menu_preins=window.open('<?php  echo $url_root; ?>/gestion/usuarios/solicitar_password.php','OpenWindow_pass','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=no,width=600,height=500,titlebar=0,minimize=no,maximize=no')"><font size=1 face="Arial" color="#ff3322">¿Has olvidado tu contraseña?</font></a>
          </td>
          </form>
<?php  
}
  else
{

?>
          <td width="60%" valign="Top">
          <table width="100%" border=0 cellpadding=0 cellspacing=0>
                  <tr>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=8 height=8></td>
                        <td><img src="<?php    echo $url_root; ?>/imagen/nada.gif" border=0 width=1 height=8></td>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=8 height=8></td>
                  </tr>
                  </table><table width="100%" border=0 cellpadding=3 cellspacing=0>
          <tr>
          <td><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=1 height=29></td>
          <td align="center"><font face="<?= $fuente_general; ?>" color="<?= $color_fuente; ?>" size=1><b>Usuario:</b> <?= $nombre_completo; ?><?php 
          if (strlen($centro_actual)!=0) {
          ?>
          <br><b>Centro seleccionado:</b> <?=$nombre_centro_actual;?> (<?=$poblacion_centro_actual;?>)
          <?php 
          }
          ?></font></td></tr>
          </table><table width="100%" border=0 cellpadding=0 cellspacing=0>
                  <tr>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=8 height=8></td>
                        <td><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=1 height=8></td>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=8 height=8></td>
                  </tr>
                  </table>
          </td>
                  <td valign="middle" align="center" width="40%">
          <table width="100%" border=0 cellpadding=0 cellspacing=0>
                  <tr>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/esquina_si.gif" border=0 width=8 height=8></td>
                        <td bgcolor="#ffffff"><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=1 height=8></td>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/esquina_sd.gif" border=0 width=8 height=8></td>
                  </tr>
                  </table><table width="100%" border=0 cellpadding=0 cellspacing=0>
                  <tr>
          <td bgcolor="#ffffff" align="center">
                        <a title="Cerrar sesión" href="<?= $url_root; ?>/configuracion/cerrar_sesion.php"><img src="<?= $url_root; ?>/imagen/menu/fin.gif" border=0></a>
<?php  
  $Pagina_Actual=$url_root."/index.php";
  if ($Pagina_Actual!=$path_index)
  {

?>                        
<a title="Página de inicio" href="<?= $url_root; ?>/index.php"><img src="<?= $url_root; ?>/imagen/menu/home.gif" border=0></a>
<?php  
  }
    else
  {

?>
<img src="<?= $url_root; ?>/imagen/menu/home_des.gif" border=0>
<?php  
  } 

?></td></tr>
          </table><table width="100%" border=0 cellpadding=0 cellspacing=0>
                  <tr>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/esquina_ii.gif" border=0 width=8 height=8></td>
                        <td bgcolor="#FFFFFF"><img src="<?= $url_root; ?>/imagen/nada.gif" border=0 width=1 height=8></td>
                        <td width="8"><img src="<?= $url_root; ?>/imagen/esquina_id.gif" border=0 width=8 height=8></td>
                  </tr>
                  </table>                        
          </td>
<?php  
} 

?>
        </tr>
      </table>

