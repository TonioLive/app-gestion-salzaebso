<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$filtro_busqueda="";

$id_expediente=$_GET["id_expediente"];

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function tipo_sel(tipo) {
        self.opener.datos_articulo.ARTICULO_GENERICO.value=tipo;
        window.close();
        }
function intro_nuevo_tipo(tipo) {
        if (document.nuevo.nuevo_tipo.value!="") {
                self.opener.datos_articulo.ARTICULO_GENERICO.value=document.nuevo.nuevo_tipo.value;
                window.close();
                } else {
                alert("Debes introducir el nombre del producto genérico.");
                }
        }

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<form name="nuevo" action="javascript:intro_nuevo_tipo()">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=1><b>Nuevo tipo</b><br><input type="text" name="nuevo_tipo" size=40 style="font-size: 10px"></small></small></td>
 </tr>
 <tr>
    <td align="center"><input type="button" onclick="intro_nuevo_tipo()" style="font-size: 10px" value="Introducir nuevo genérico"></td>
  </tr>
</form>
<?php 
$busca_tipos=db_query("SELECT ARTICULO_GENERICO, count(ARTICULO_GENERICO) AS total FROM tarifa WHERE ARTICULO_GENERICO<>'' GROUP BY ARTICULO_GENERICO ORDER BY ARTICULO_GENERICO");
if (db_num_rows($busca_tipos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan=2>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><i>Para seleccionar, haz clic sobre el nombre.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>PRODUCTOS GENERICOS</b></font></td>
        </tr>
        <?php 
        while($listado_tipos=db_fetch_array($busca_tipos)){
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('<?=$listado_tipos["ARTICULO_GENERICO"];?>')"><b><?=$listado_tipos["ARTICULO_GENERICO"]?></b></a>  (<a href="busca_productos.php?generico=<?=$listado_tipos["ARTICULO_GENERICO"]?>" target="_new"><?=$listado_tipos["total"]?></a>)</font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center" colspan=2><br><br><small><small><b>Todavia no se han introducido artículos genéricos<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
</body>
</html>

