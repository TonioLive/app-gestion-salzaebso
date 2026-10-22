<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");
 require("../../configuracion/parte_superior.php");

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
<form action="chequear.php?Submenu=<?php  echo $submenu_activo; ?>" method="POST" id="NoticiasN" name="NoticiasN">
<input type=submit style="font-size: 10" value="Actualizar tablas"> <input type="button" value="Volver al menú" onclick="window.location.href='<?=$url_web?>/opciones_menu.php?Submenu=<?=$submenu_activo;?>'" style="font-size:10px"><br><br>
<table BORDER="1" CELLSPACING="0" CELLPADDING="1" width="90%" style="border-collapse: collapse; border-width: 0" bordercolor="#000000">
  <tr bgcolor="<?php  echo $color_lat2; ?>">
        <td colspan=5>
        <b>Noticias publicadas</b>
        </td>
  </tr>
  <tr bgcolor="<?php  echo $color_lat; ?>">
    <td><small><b>Fecha ins.</b></small></td>
    <td><small><b>Noticia</b></small></td>
    <td><small><b>Fecha cad.</b></small></td>
    <td><small><b>ACT</b></small></td>    
    <td><small><b>Acción</b></small></td>
  </tr>
<?php 
  $db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
  db_select_db("$sql_db",$db);

  $query_noticias=db_query("SELECT * FROM NoticiasN ORDER BY Fecha_ins;");

  mt_srand((double)microtime()*1000000);
  $contador=0;
  while($rsslc=db_fetch_array($query_noticias))
  {
    $noticia=substr($rsslc["Texto_Titular"],0,50);
    $codigo=$rsslc["Cod"];
    ?>
    <tr bgcolor="<?=$color_menu?>">
        <td><font size=1 face="<?=$fuente_general?>"><?=date("d/m/Y",strtotime($rsslc["Fecha_ins"]))?></font></td>
        <td><font size=1 face="<?=$fuente_general?>"><?=$noticia?>...</font></td>
        <td><font size=1 face="<?=$fuente_general?>"><?=date("d/m/Y",strtotime($rsslc["Fecha_cad"]))?></font></td>
        <td><INPUT onclick="top.window.document.NoticiasN.submit()" type="checkbox" value="SI" id=ACT<?= $rsslc["Cod"]; ?> name=ACT<?= $rsslc["Cod"]; ?> <?php  if ($rsslc["Activada"]==true)   {      print " checked";    } ?>></td>
        <td><font size=1 face="<?=$fuente_general?>"><a href="modif_n.php?Submenu=<?=$submenu_activo?>&noticia=<?=$codigo?>">Modificar</a> | <a href="elim_n.php?Submenu=<?=$submenu_activo?>&noticia=<?=$codigo?>">Eliminar</a></font></td>
    </tr>
    <?php 
    $contador=$contador+1;
  }
  if ($contador==0)
  {

?>
  <tr bgcolor="#FFFFFF">
        <td colspan=5><small>
        No hay noticias comunes
        </small>
        </td>
  </tr>
<?php 
  }
?>
</table><br>
</form>
</center></div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

