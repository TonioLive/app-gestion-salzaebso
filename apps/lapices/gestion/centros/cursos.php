<?php  require("../../configuracion/parte_superior_privado.php"); ?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Cursos académicos</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Inicio</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Fin</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Estado</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $consulta_centros=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio;");
                        while($centros=db_fetch_array($consulta_centros)){
                         
                        $estado_curso="En peparación";
                        if ($centros["estado"]==1) { $estado_curso="Iniciado"; }
                        if ($centros["estado"]==2) { $estado_curso="Finalizado"; }
                         
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$centros["nombre"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($centros["fecha_inicio"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($centros["fecha_fin"]));?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$estado_curso;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="editar_curso.php?o=cursos.php&id_curso=<?=$centros["id_curso"];?>"><img src="editar.gif" border=0></a> <a href="generar_estructura_curso.php?id_curso=<?=$centros["id_curso"];?>" title="Actualizar estructura de carpetas"><img src="carpetas.gif" border=0></a></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                         </table><br>
                         <input type="button" onclick="window.location.href='editar_curso.php?o=cursos.php&'" value="Introducir Nuevo Curso" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_web?>/index.php'" style="font-size: 10px">
                </td>
        </tr>
</table><br><br>
<?php  require("../../configuracion/parte_inferior.php"); ?>
