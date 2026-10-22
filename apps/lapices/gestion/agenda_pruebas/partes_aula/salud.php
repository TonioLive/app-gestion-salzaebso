<?php 
$tipo_incidencias=1;
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

$busca_alumnos=db_query("SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND aula=$id_aula AND fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=5><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }

        $listado_incidencias="";

        $busca_incidencias=db_query("SELECT * FROM agenda_alumnos_seguimiento WHERE area=$tipo_incidencias  AND dia='".date("Y-m-d",$fecha_time_stamp)."' AND id_alumno=".$listado_alumnos["id_alumno"]." ORDER BY hora");

        while($incidencias=db_fetch_array($busca_incidencias)) {
                $listado_incidencias=$listado_incidencias.date("H:i",strtotime($incidencias["hora"])).". ".$tipos_notas_inci[$incidencias["tipo_nota"]].": ".$incidencias["nota"]."<br>";
                }

        ?>
        <tr valign="top">
        <td width="20%"><font size=2 face="<?= $fuente_general; ?>"><a href="javascript:editar_nota(<?=$id_aula?>,<?=$listado_alumnos["id_alumno"]?>,<?=$fecha_time_stamp?>,<?=$tipo_incidencias?>)"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="left" width="70%"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_incidencias?></font></td>
        <td align="center" width="10%"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><a href='javascript:editar_nota(<?=$id_aula?>,<?=$listado_alumnos["id_alumno"]?>,<?=$fecha_time_stamp?>,<?=
$tipo_incidencias?>)'>Editar</a></font></td>
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
