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
                top.window.opener.document.formulario1.nombre_incidencia.value=document.nuevo.nuevo_tipo.value;
                window.close();
                } else {
                alert("Debes introducir el nombre de la incidencia.");
                }
        }

//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<form name="nuevo" action="javascript:intro_nuevo_tipo()">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=1><b>Nuevo nombre de incidencia</b><br><input type="text" name="nuevo_tipo" size=40 style="font-size: 10px"></small></small></td>
 </tr>
 <tr>
    <td align="center"><input type="button" onclick="intro_nuevo_tipo()" style="font-size: 10px" value="Introducir nuevo"></td>
  </tr>
</form>
<?php 
$busca_tipos=db_query("SELECT nombre_incidencia FROM incidencias_2017 WHERE nombre_incidencia<>'' GROUP BY nombre_incidencia");
?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan=2>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><i>Para seleccionar, haz clic sobre el motivo.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>POSIBLES INCIDENCIAS</b></font></td>
        </tr>
        <?php 
        while($listado_tipos=db_fetch_array($busca_tipos)){
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('<?=urldecode($listado_tipos["nombre_incidencia"]);?>')"><b><?=urldecode($listado_tipos["nombre_incidencia"])?></b></a></font></td>
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

