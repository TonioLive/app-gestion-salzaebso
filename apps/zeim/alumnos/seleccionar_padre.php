<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicial=$_GET["i"];
if (strlen($inicial)==0) {
        $inicial="A";
        }
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

function editar(codigo) {
        window.open('nuevo_padre1.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td colspan=<?=$num_filas;?>><small><small><b>Selecciona la persona</b></small></small></td>
  </tr>
  <tr bgcolor="#efefef">
  <td align="center"><font size=2 face="<?= $fuente_general; ?>">
<?php 
for ($letra=65;$letra<=90;$letra++) {
        // Comprueba si hay alumnos con esa letra
        $busca_alumnos_en=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion!=1 AND Apellido1 like ?', 's', array((string)(chr($letra)) . '%'));
        if (db_num_rows($busca_alumnos_en)==0) {
                echo "<font color='#cecece'>";
                } else {
                echo "<a href='".$_SERVER["PHP_SELF"]."?i=".chr($letra)."'><font color='#000000'><b>";
                }
        echo chr($letra);
        if (db_num_rows($busca_alumnos_en)==0) {
                echo "</font>";
                } else {
                echo "</b></font></a>";
                }
        if ($letra<90) { echo " "; }
        }
?>
</font></td>
</tr>
<?php 
$busca_alumnos=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion!=1 AND Apellido1 like ?', 's', array((string)($inicial) . '%'));
if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>"><i>Personas cuyo apellido comienza por la letra <b><?=$inicial?></b></i><br>Para seleccionar una persona, haz clic sobre su nombre.</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>DNI</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        $nombre_completo_padre=$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"].", ".$listado_alumnos["Nombre"];
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:self.opener.nuevo_progenitor(<?=$listado_alumnos["id_usuario"];?>,'<?=$nombre_completo_padre;?>','<?=$listado_alumnos["DNI"];?>');window.close();"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$listado_alumnos["DNI"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$listado_alumnos["Poblacion"]?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)"><img src="editar.gif" border=0></a></font></td>
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
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ningún usuario cuyo apellido comienza por la letra <?=$inicial;?><br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
</body>
</html>

