<?php  require("../configuracion/parte_superior_privado.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Alumnos</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Inscribir Alumno" onclick="window.location.href='<?=$url_web;?>/alumnos/nuevo_alumno.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Inscribir nuevo alumno o alumno de otros años.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Buscar Alumno" onclick="window.location.href='<?=$url_web;?>/alumnos/busqueda_alumno.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Busqueda de alumnos matriculados en el centro.</font></td>
                        </tr>	
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Info. ocupación aulas" onclick="window.location.href='<?=$url_web;?>/gestion/centros/recuento_alumnos_aulas.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Tabla resumen de alumnos matriculados por aulas, cursos y centros, de los cursos anteriores al actual.</font></td>
                        </tr>                         				
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Resumen Matriculas" onclick="window.location.href='<?=$url_web;?>/gestion/centros/recuento_alumnos.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Tabla resumen de alumnos matriculados por edades, cursos y centros, de los cursos anteriores al actual.</font></td>
                        </tr> 
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Asistencia Piscina" onclick="window.location.href='<?=$url_web;?>/gestion/centros/recuento_alumnos_piscina.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Tabla resumen de alumnos que acuden a la piscina por edades, cursos y centros, de los cursos anteriores al actual.</font></td>
                        </tr>                                                
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Listados Alumnos" onclick="window.location.href='<?=$url_web;?>/alumnos/seleccion_listados.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Exporta listados de alumnos a Excel para poder hacer etiquetas y combinaciones de correspondencia.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210 align="center"><font size=3 face="<?= $fuente_general; ?>"><a href="<?=$url_web;?>/pdf/cartas.php?id_escuela=<?=$centro_actual?>" target="_new"><b>Cartas Webcam</b></a></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Imprime listado completo de cartas notificando a los padres el nombre de usuario y contraseña para acceder a la webcam.</font></td>
                        </tr>
                         </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
