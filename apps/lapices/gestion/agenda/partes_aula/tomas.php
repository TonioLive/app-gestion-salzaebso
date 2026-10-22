<?php 
$tipo_incidencias=0;
$tipo_nota_tabla=15;
?>
                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="background: #ffffff; border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#cdcdcd">
                                <table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                                <?php 
                                $pagina_act=$contador_capas;
                                for ($contador_pagi=0;$contador_pagi<count($array_paginas);$contador_pagi++) {
                                        if ($pagina_act==$contador_pagi) {
                                ?>
                                <td bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><b> <?=$array_paginas[$contador_pagi]?></b></font></td>
                                <?php 
                                        } else {
                                ?>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b> <a href='javascript:mostrar(<?=$contador_pagi?>)'><?=$array_paginas[$contador_pagi]?></a></b></font></td>
                                <?php 
                                        }
                                }
                                ?>
                                </tr></table>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b><?=$array_paginas[$pagina_act]?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100% align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>">
                                
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 

$busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND aula=$id_aula AND fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center"><div id="tablaTomas" align="center"><?php  include("tabla_tomas.php"); ?></div>
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
                                                              
                                </font></td>
                        </tr>
                </table>
<SCRIPT LANGUAGE=javascript>
<!--
        
function nueva_toma(id_alumno) {

        parte1=document.getElementById("toma"+id_alumno).value;

        if (parte1!="") {
                texto_nota="Biber&oacute;n: Ha tomado "+parte1+".";
                } else {
                texto_nota="Biber&oacute;n: No ha querido tomar nada.";
                }

        parte2=document.getElementById("nota_toma"+id_alumno).value;
        if (parte2!="") {
                texto_nota=texto_nota+" "+parte2;
                }

        url_destino="/gestion/agenda/partes_aula/nueva_toma_tabla.php?id_aula=<?=$id_aula_seleccionada?>&dia=<?=$fecha_time_stamp?>&centro_aula=<?=$centro_aula?>&id_alumno="+id_alumno+"&texto_nota="+encodeURIComponent(texto_nota);

        actualizarCambios("tablaTomas",url_destino);

        }

//-->
</SCRIPT>
