<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$estado_nota[0]="<font color='#ff0000'>Pendiente</font>";
$estado_nota[1]="<font color='#00ff00'>Realizada</font>";

$tipos_notas_inci=array();

$busca_notas_inci=db_query("SELECT * FROM agenda_alumnos_tipos_notas");
while($notas_inci=db_fetch_array($busca_notas_inci)) {
        $tipos_notas_inci[$notas_inci["id_tipo"]]=$notas_inci["nombre_tipo"];
        }

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

$id_aula=$_GET["id_aula"];
$dia=$_GET["dia"];
$id_area=$_GET["area"];

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        }

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

self.opener.window.location.href='dia_aula.php?dia=<?=strtotime($dia)?>&id_aula=<?=$id_aula?>&pagina_activa=<?=$id_area+1?>';

function nueva_nota() {
        window.location.href='nueva_nota.php?id_aula=<?=$id_aula;?>&dia=<?=$dia;?>&id_alumno=0&area=<?=$id_area?>';
        }
function editar_nota(fila) {
        if (fila!="") {
                window.location.href='nueva_nota.php?id_nota='+fila;
                } else {
                alert("Primero debes seleccionar la nota que deseas editar");
                }
        }
function borrar_nota(id_borrar,nombre_mostrar) {
        if (confirm("¿Deseas confirmar la eliminación de la nota?")) {
                window.location.href='borrar_nota.php?id_nota='+id_borrar;
                }
        }
function cambiar_estado(fila,estado_actual) {
        nuevo_estado="Realizada";
        if (estado_actual==1) {
                nuevo_estado="Pendiente";
                }
        if (confirm("¿Deseas cambiar el estado a "+nuevo_estado+"?")) {
                window.location.href='cambiar_estado_nota.php?id_nota='+fila;
                }
        }
        

        
//-->
</SCRIPT>
</head>

<body>
<table width="100%" border="0">
  <tr>
    <td align="center">
    <b>Seguimiento de <?=$nombres_areas[$id_area]?><br>Aula <i><?=$nombre_aula;?></i> el día <?=date("d/m/Y",$dia)?></font></b><br><br>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_notas=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE area=? AND dia=? AND para_id_aula=? ORDER BY hora', 'sss', array($id_area, (string)(date("Y-m-d", $dia)), $id_aula)) or die(db_error());
                                ?><br>
                                <table width="98%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="9%"><font size=2 face="<?= $fuente_general; ?>"><b>Hora</b></font></td>
                                                <td align="left" width="20%"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo</b></font></td>
                                                <td align="center" width="54%"><font size=2 face="<?= $fuente_general; ?>"><b>Comentarios</b></font></td>
                                                <td align="center" width="4%"><font size=2 face="<?= $fuente_general; ?>"><b>Estado</b></font></td>
                                                <td align="center" width="4%"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                                <td align="center" width="4%"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        </tr>
                                <?php 
                                if (db_num_rows($busca_notas)==0) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center" colspan=5><font size=2 face="<?= $fuente_general; ?>"><b>No se han introducido instrucciones, notas o incidencias, haz clic sobre el botón <i>Agregar instrucción, nota o incidencias</i> para añadirlas.</b></font></td>
                                </tr>
                                <?php 
                                }

                                while($datos_notas=db_fetch_array($busca_notas)) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("H:i",strtotime($datos_notas["hora"]));?></font></td>
                                        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$tipos_notas_inci[$datos_notas["tipo_nota"]];?></font></td>
                                        <td align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_notas["nota"];?></font></td>
                                        <td align="center" width=5%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href='javascript:cambiar_estado(<?=$datos_notas["id"];?>,<?=$datos_notas["realizada"];?>)'><?=$estado_nota[$datos_notas["realizada"]]?></a></td>
                                        <td align="center" width=5%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a title="Modificar la nota" href="javascript:editar_nota(<?=$datos_notas["id"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a title="Eliminar de la lista" href="javascript:borrar_nota(<?=$datos_notas["id"];?>,'')"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="nueva_nota()" style="font-size: 10px" value="Agregar instrucción, nota o incidencia">
</td>
  </tr>
</table>
</body>
</html>
<?php 
db_close();
?>
