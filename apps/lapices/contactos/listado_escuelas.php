<?php  require("../configuracion/parte_superior_privado.php");

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

?>
<SCRIPT LANGUAGE=javascript>
<!--

function eliminar_contacto(id) {
        if (confirm("Se va ha eliminar al escuela seleccionada.\n\n¿Estas seguro?")) {
                window.location.href='eliminar_escuela.php?id_competencia='+id;
                }
        }

//-->
</SCRIPT>
<div align="center"><center>
<input type="button" onclick="window.location.href='nueva_escuela.php'" value="Nueva escuela" style="font-size: 10px"> <input type="button" onclick="window.location.href='mapa.php'" value="Salir" style="font-size: 10px">
<br><br>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de escuelas infantiles</b></small></td>
  </tr>
<?php 
$busca_cotactos=db_query("SELECT * FROM escuelas_competencia ORDER BY poblacion,cp,nombre_escuela");

if (db_num_rows($busca_cotactos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=5><font size=1 face="<?= $fuente_general; ?>">Para editar un contacto, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre escuela</b></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Calle</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><b>C.P.</b></font></td>
        <td align="center" width=7%><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        $proceso_actual="";
        $curso_actual="";
        $centro_actual_contacto="";
        
        while($listado_alumnos=db_fetch_array($busca_cotactos)){

        $color_fuente_linea="#000000";
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["nombre_escuela"]?></font></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["calle"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["poblacion"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["cp"]?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="nueva_escuela.php?id_competencia=<?=$listado_alumnos["id_competencia"];?>" title="Editar contacto"><img src="modificar.gif" border=0></a> <a href="javascript:eliminar_contacto(<?=$listado_alumnos["id_competencia"];?>);" title="Eliminar escuela"><img src="borrar.gif" border=0></a> </font></td>
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
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ninguna escuela.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

