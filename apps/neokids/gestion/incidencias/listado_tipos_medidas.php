<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function tipo_sel(tipo) {
        top.window.opener.document.formulario1.nombre_incidencia.value=tipo;
        window.close();
        }
function intro_nuevo_tipo(tipo) {
        if (document.nuevo.nuevo_tipo.value!="") {
                top.window.opener.document.formulario1.tipo_medida.value=document.nuevo.nuevo_tipo.value;
                window.close();
                } else {
                alert("Debes introducir el nombre de la incidencia.");
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
    <td align="center"><font size=1><b>Nuevo tipo de medida</b><br><input type="text" name="nuevo_tipo" size=40 style="font-size: 10px"></small></small></td>
 </tr>
 <tr>
    <td align="center"><input type="button" onclick="intro_nuevo_tipo()" style="font-size: 10px" value="Introducir nuevo"></td>
  </tr>
</form>
<?php 
$busca_tipos=db_query("SELECT tipo_medida FROM incidencias WHERE nombre_incidencia<>'' GROUP BY tipo_medida");
?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan=2>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><i>Para seleccionar, haz clic sobre el tipo.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>POSIBLES TIPOS DE MEDIDAS</b></font></td>
        </tr>
        <?php 
        while($listado_tipos=db_fetch_array($busca_tipos)){
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('<?=urldecode($listado_tipos["tipo_medida"]);?>')"><b><?=urldecode($listado_tipos["tipo_medida"])?></b></a></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
</table>
</center></div>
</div>
</body>
</html>

