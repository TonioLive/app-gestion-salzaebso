<table width="100%" border="0">
  <tr>
    <td align="center">
    <b>Seguimiento del aula</font></b><br>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_notas=db_query("SELECT * FROM agenda_alumnos_seguimiento WHERE area=$tipo_incidencias AND dia='".date("Y-m-d",$fecha_time_stamp)."' AND id_alumno=0 ORDER BY hora") or die(db_error());
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
                                        <td align="center" width=5%><?php  if ($datos_notas["avisar"]==1) { ?><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href='javascript:cambiar_estado(<?=$datos_notas["id"];?>,<?=$datos_notas["realizada"];?>)'><?=$estado_nota[$datos_notas["realizada"]]?></a><?php  } else { ?>-<?php  }?></td>
                                        <td align="center" width=5%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a title="Modificar la nota" href="javascript:editar_nota_comun(<?=$datos_notas["id"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a title="Eliminar de la lista" href="javascript:borrar_nota(<?=$datos_notas["id"];?>,'')"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="nueva_nota(<?=$tipo_incidencias?>)" style="font-size: 10px" value="Agregar instrucción, nota o incidencia a toda el aula"><br>
</td>
  </tr>
</table><br><b><font size=3>Seguimiento Individualizado</font></b><br>
