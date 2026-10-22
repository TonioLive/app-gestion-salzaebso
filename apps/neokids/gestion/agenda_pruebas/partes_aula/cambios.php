<?php 
$tipo_incidencias=2;
?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#cdcdcd">
                                <table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                                <?php 
                                $pagina_act=$contador_capas;
                                for ($contador_pagi=0;$contador_pagi<count($array_paginas);$contador_pagi++) {

                                if (substr($nombres_partes[$contador_pagi],0,5)!="link:") {

                                        if ($pagina_act==$contador_pagi) {
                                ?>
                                <td bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><b> <?=$array_paginas[$contador_pagi]?></b></font></td>
                                <?php 
                                        } else {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='javascript:mostrar(<?=$contador_pagi?>)'><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                <?php 
                                        }
                                        } else {
                                        ?>
                                        <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='<?=substr($nombres_partes[$contador_pagi],5,strlen($nombres_partes[$contador_pagi])-5);?>?id_aula=<?=$id_aula?>' target="_new"><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                        <?php 
                                        }
                                }
                                ?>
                                </tr></table>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Incidencias de <?=$nombres_areas[$tipo_incidencias];?>, <i><?=$datos_aula["nombre"];?></i></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>">
<div align="center"><center>
<?php 
$listado_incidencias_aula_centro="";

$busca_incidencias=db_query("SELECT * FROM agenda_alumnos_seguimiento WHERE area=$tipo_incidencias  AND dia='".date("Y-m-d",$fecha_time_stamp)."' AND (para_id_aula=$id_aula OR para_id_centro=$centro_actual) ORDER BY hora") or die(db_error());

while($incidencias=db_fetch_array($busca_incidencias)) {
                $listado_incidencias_aula_centro=$listado_incidencias_aula_centro.date("H:i",strtotime($incidencias["hora"])).". ".$tipos_notas_inci[$incidencias["tipo_nota"]].": ".$incidencias["nota"]."<br>";
                }

if (strlen($listado_incidencias_aula_centro)!=0){
        ?>
        <table width=90% border=1 width=8% style="border-collapse: collapse" bordercolor="#000000">
        <tr>
        <td><?=$listado_incidencias_aula_centro?></td>
        </tr>
        </table>
        <?php 
        }
?>
&nbsp;
<?php  include("seguimiento.php"); ?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 

$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'ssss', array($centro_actual, $id_aula, (string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center"><div id="tablaCambios" align="center"><?php  include("tabla_cambios.php"); ?></div>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>No está prevista la asistencia de ningún alumno.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</center></div>
</div>
                                </font></td>
                        </tr>
                </table>
<SCRIPT LANGUAGE=javascript>
<!--

function editar_salud(id_alua,id_alumno,dia) {
        window.open("editar_notas.php?area=<?=$tipo_incidencias?>&dia="+dia+"&id_aula="+id_alua+"&id_alumno="+id_alumno,"ventana_notas","resizable=yes,toolbar=no,width=750,height=550");
        }
        
function nuevo_cambio(id_alumno) {

        parte1=document.getElementById("deposicion"+id_alumno).value;

        if (parte1!="") {
                texto_nota="Deposición: "+parte1+".";
                } else {
                texto_nota="Sin deposición";
                }

        parte2=document.getElementById("nota"+id_alumno).value;
        if (parte2!="") {
                texto_nota=texto_nota+" "+parte2;
                }

        url_destino="/gestion/agenda/partes_aula/nueva_nota_tabla.php?id_aula=<?=$id_aula_seleccionada?>&dia=<?=$fecha_time_stamp?>&centro_aula=<?=$centro_aula?>&id_alumno="+id_alumno+"&texto_nota="+texto_nota;

        actualizarTabla("tablaCambios",url_destino);

        }

//-->
</SCRIPT>
