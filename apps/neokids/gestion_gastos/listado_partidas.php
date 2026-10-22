<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/autentifica_admin.php");

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
                if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
                        self.opener.document.coste.partida.value=tipo;
                        } else {
                        self.opener.coste.partida.value=tipo;
                        }

        window.close();
        }
function intro_nuevo_tipo(tipo) {
        if (document.nuevo.nuevo_tipo.value!="") {
                if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
                        self.opener.document.coste.partida.value=document.nuevo.nuevo_tipo.value;
                        } else {
                        self.opener.coste.partida.value=document.nuevo.nuevo_tipo.value;
                        }
                window.close();
                } else {
                alert("Debes introducir el nombre de la partida a la que se asignará el coste.");
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
    <td align="center"><font size=1><b>Nueva partida</b><br><input type="text" name="nuevo_tipo" size=40 style="font-size: 10px"></small></small></td>
 </tr>
 <tr>
    <td align="center"><input type="button" onclick="intro_nuevo_tipo()" style="font-size: 10px" value="Introducir nueva partida"></td>
  </tr>
</form>
<?php 
$busca_tipos=db_query("SELECT * FROM (SELECT partida FROM gestion_costes WHERE partida<>'' GROUP BY partida UNION SELECT nombre_partida AS partida FROM partidas_gastos order BY partida) AS union_partidas GROUP BY partida") or die(db_error());
// $busca_tipos=mysql_query("SELECT partida FROM gestion_costes WHERE partida<>'' GROUP BY partida");
?>
<tr bgcolor="#FFFFFF">
        <td align="center" colspan=2>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><i>Para seleccionar, haz clic sobre la partida.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>PARTIDAS DE GASTOS</b></font></td>
        </tr>
        </tr>
        <?php 
        while($listado_tipos=db_fetch_array($busca_tipos)){
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:tipo_sel('<?=$listado_tipos["partida"];?>')"><b><?=$listado_tipos["partida"]?></b></a></font></td>
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

