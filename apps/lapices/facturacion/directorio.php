<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_admin.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<script language="Javascript">
self.focus();

function eliminar(nombre_archivo) {
        if (confirm("¿Deseas eliminar el archivo \""+nombre_archivo+"\"?")) {
                window.location.href='borrar_directorio.php?archivo='+nombre_archivo;
                }
        }
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center">
   <table border="0" cellpadding="0" cellspacing="0" width="100%" id="AutoNumber2">
                <tr>
                            <font size="3" color="#000000"><b>Ficheros exportación Norma CSB 19<br><small>Listado completo de ficheros creados</small></font></b>
                </tr>
    </table><br><p align="center">
<input type="button" value="Cerrar" style="font-size: 10" onclick='window.close()'>
</p>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<tr bgcolor="<?php  echo $fondo_at; ?>">
<td><small><small><b>Archivo</b></small></small></td>
<td align="center"><small><small><b>Acción</b></small></small></td>
</tr>
<?php 
$contador=0;
$d = dir("$web_path/recibos");
      while($s=$d->read()) {
        if (($s!=".") && ($s!=".."))
        {
        ?>
        <tr bgcolor="<?= $color_lat2; ?>">
        <td><small><small><?= $s; ?></small></small></td>
        <td align="center"><small><small><a href="<?php    echo $url_web; ?>/recibos/<?php    echo $s; ?>">Descargar</a> | <a href="javascript:eliminar('<?php    echo $s; ?>')">Eliminar</a></small></small></td>
        </tr>
        <?php 
        $contador=$contador+1;
        }
      }
$d->close();
if ($contador==0) {
?>
        <tr bgcolor="<?= $color_lat2; ?>">
        <td colspan=2><small>Todavía no se ha generado ningún archivo de exportación de recibos</small></td>
        </tr>
<?php 
}
?>
</td></tr>
</table>
</center>
</div>
<br>
<font face="<?php  echo $fuente_general; ?>"><small>
<b>Archivos en la carpeta:</b> <?php  echo $contador; ?><br>
</small></font>
</body>
</html>
