                <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="background: #ffffff; border-collapse: collapse" cellspacing=0 cellpadding=0 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#cdcdcd">
                                <table width=100% cellpadding=2 cellspacing=0 border=0><tr>
                                <?php
require_once __DIR__ . '/../../../configuracion/encoding.php';
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
                                <td width=100% align="left" colspan=2>
                                <table cellpadding="2" cellspacing="1" bgcolor="#cdcdcd">
                                <tr bgcolor="#efefef">
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Realizada</b></font></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Actividad</b></font></td>
                                </tr>                               
                                <?php 
                                $busca_programacion=db_query("SELECT * FROM agenda_programacion WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."' AND id_aula=$id_aula ORDER BY tiempo_trabajo,orden_actividad");
                                
                                $tiempo_trabajo_actual=0;
                                
                                while($programacion=db_fetch_array($busca_programacion)) {

        if ($programacion["id_actividad"]!=0) {
                $descripcion_actividad_actual=$programacion["actividad"];
                } else {
                $descripcion_actividad_actual=app_text_utf8($programacion["actividad"]);
                }
                                ?>
                                <tr bgcolor="#ffffff">
                                <td align="center"><input type="checkbox" value="1" name="realizada<?=$programacion["id_prog"]?>" id="realizada<?=$programacion["id_prog"]?>" onclick="cambia_estado_actividad(<?=$programacion["id_prog"]?>);"<?php  if ($programacion["realizada"]==1) { ?> checked<?php  } ?>></td>
                                <td><font size=2 face="<?= $fuente_general; ?>"><b>Tiempo de trabajo <?=$programacion["tiempo_trabajo"]?>, Actividad <?=$programacion["orden_actividad"]?></b><br><?=$descripcion_actividad_actual?></font></td></tr>
                                <?php 
                                }
                                ?>
                                </table> 
                                </font>
                                </td>
                        </tr>
                </table>
