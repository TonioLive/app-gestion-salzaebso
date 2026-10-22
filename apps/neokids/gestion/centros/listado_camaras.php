<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function ver_camara(id_camara) {
        window.open('ver_camara.php?local=0&id_webcam='+id_camara,'ver_camara','width=385,height=355,toolbar=no');
        }
function ver_camaralocal(id_camara) {
        window.open('ver_camara.php?local=1&id_webcam='+id_camara,'ver_camara','width=385,height=355,toolbar=no');
        }
function eliminar_camara(id_camara) {
        if (confirm("¿Quieres eliinar la información sobre la cámara seleccionada?")) {
                window.location.href='eliminar_camara.php?id_webcam='+id_camara;
                }
        }
</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Cámaras Web</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                         <?php 
                         $tipos_camaras[0]="Aula";
                         $tipos_camaras[1]="Comedor";
                         $tipos_camaras[2]="Zona común";
                         $id_centro_actual=-1;
				 $nombre_centro="Camarás desactivadas";
                         $consulta_camaras=db_query("SELECT * FROM webcam ORDER BY id_centro,nombre_webcam;");
                         while($camaras=db_fetch_array($consulta_camaras)){
                        if ($id_centro_actual!=$camaras["id_centro"]) {
                                $id_centro_actual=$camaras["id_centro"];
                                // Busca datos del centro
                                $busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro_actual));
                                        if (db_num_rows($busca_centro)!=0) {
                                                $datos_centro=db_fetch_array($busca_centro);
                                                $nombre_centro=$datos_centro["nombre_centro"];
                                                }
                         ?>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef" colspan=6><font size=1 face="<?= $fuente_general; ?>"><b>Escuela: <?=$nombre_centro;?></b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Hora encendido</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Hora apagado</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        }
                        if ($camaras["tipo"]==0) {
                                $busca_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($camaras["id_aula"]));
                                if (db_num_rows($busca_aula)!=0) {
                                        $datos_aula=db_fetch_array($busca_aula);
                                        $nombre_aula=$datos_aula["nombre"];
                                        } else {
                                        $nombre_aula="No asignada";
                                        }
                                } else {
                                $nombre_aula="-";
                                }
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$camaras["nombre_webcam"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$tipos_camaras[$camaras["tipo"]];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_aula;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i",strtotime($camaras["hora_conexion"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i",strtotime($camaras["hora_desconexion"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="editar_camara.php?id_camara=<?=$camaras["id_webcam"];?>"><img src="editar.gif" border=0></a> <a href="javascript:ver_camara(<?=$camaras["id_webcam"];?>);"> <img src="webcam.gif" border=0></a> <a href="javascript:ver_camaralocal(<?=$camaras["id_webcam"];?>);"><img src="webcam.gif" border=0></a> <a href="javascript:eliminar_camara(<?=$camaras["id_webcam"];?>);"><img src="borrar.gif" border=0></a></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                         </table><br>
                         <input type="button" onclick="window.location.href='editar_camara.php'" value="Introducir Nueva Cámara" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_web?>/index.php'" style="font-size: 10px">
                </td>
        </tr>
</table><br><br>
<?php  require("../../configuracion/parte_inferior.php"); ?>
