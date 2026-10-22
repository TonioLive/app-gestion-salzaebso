<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

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
        self.opener.formulario1.motivo_baja.value=tipo;
        window.close();
        }
function intro_nuevo_tipo(tipo) {
        if (document.nuevo.nuevo_tipo.value!="") {
                self.opener.formulario1.motivo_baja.value=document.nuevo.nuevo_tipo.value;
                window.close();
                } else {
                alert("Debes introducir la pobación.");
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
    <td align="center"><font size=1><b>Nuevo motivo de baja</b><br><input type="text" name="nuevo_tipo" size=40 style="font-size: 10px"></small></small></td>
 </tr>
 <tr>
    <td align="center"><input type="button" onclick="intro_nuevo_tipo()" style="font-size: 10px" value="Introducir motivo"></td>
  </tr>
</form>
<?php 
$busca_tipos=db_query("SELECT motivo_baja FROM alumnos WHERE motivo_baja<>'' GROUP BY motivo_baja");
?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan=2>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><i>Para seleccionar, haz clic sobre el motivo.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>POSIBLES MOTIVOS DE BAJA</b></font></td>
        </tr>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('Cambio de domicilio')"><b>Cambio de domicilio</b></a></font></td>
        </tr>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('Insatisfacción con el servicio')"><b>Insatisfacción con el servicio</b></a></font></td>
        </tr>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('Finalización del curso')"><b>Finalización del curso</b></a></font></td>
        </tr>
        <?php 
        while($listado_tipos=db_fetch_array($busca_tipos)){
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('<?=$listado_tipos["motivo_baja"];?>')"><b><?=$listado_tipos["motivo_baja"]?></b></a></font></td>
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

