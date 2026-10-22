<?php  require("../configuracion/parte_superior_privado.php"); ?>
<script language="Javascript">
function eliminar(codigo) {
        if (confirm("¿Realmente quieres eliminar esta pregunta?")) {
                window.location.href="eliminar_pregunta.php?pregunta="+codigo;
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=3 face="<?= $fuente_general; ?>"><b>Gestión del cuestionario<br></b><small>Lista de espera.</small></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_pregunta = db_query("SELECT * FROM lista_espera_cuestionarios ORDER BY desactivada,id_curso, descripcion") or die (db_error());
                        
                        if (db_num_rows($consulta_pregunta)==0) {
                        ?>
                        <tr>
                        <td colspan=3 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay preguntas, haz clic en el botón <i>"Nueva pregunta"</i> para empezar a trabajar</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        }
                        $estado_actual=-1;
                        
                        $array_nombre_estado=array("Activas","Desactivadas");
                        
                        $curso_actual=-1;
                        
                        $array_nombre_cursos=array();
                        $array_nombre_cursos[0]="Todos los cursos";
                        
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                                $array_nombre_cursos[$datos_listado_cursos["id_curso"]]=$datos_listado_cursos["nombre"];
                                }
                        
                        while($datos_pregunta = db_fetch_array($consulta_pregunta)){
                        
                        if ($estado_actual!=$datos_pregunta["desactivada"]) {
                                $estado_actual=$datos_pregunta["desactivada"];
                                ?>
                                <tr>
                                <td colspan=3 align=left bgcolor="#efefef"><font size=3><b><?=$array_nombre_estado[$estado_actual]?></b></font></td>
                                </tr>
                                <?php 
                                }
                        
                        if ($curso_actual!=$datos_pregunta["id_curso"]) {
                                $curso_actual=$datos_pregunta["id_curso"];
                                ?>
                                <tr>
                                <td colspan=3 align=left bgcolor="#efefef"><font size=3><b><?=$array_nombre_cursos[$curso_actual]?></b></font></td>
                                </tr>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=2 face="<?=$fuente_general;?>"><b>Pregunta</b></font></td>
                                        <td><font size=2 face="<?=$fuente_general;?>"><b>Valor de la respuesta afirmativa</b></font></td>
                                        <td><font size=2 face="<?=$fuente_general;?>"><b>&nbsp;</b></font></td>
                                </tr>
                                <?php 
                                }

                        // comprueba si ha sido respondida
                        $consulta2=array('SELECT * FROM lista_espera_respuestas WHERE id_tipo_puntos=?', 's', array($datos_pregunta["id_tipo_punto"]));
                        $count=db_query_params($consulta2[0], $consulta2[1], $consulta2[2]);
                        $numRecords = db_num_rows($count);
                        if ($numRecords==0) {
                                $link_elim1="<a href='javascript:eliminar(\"".urlencode($datos_pregunta["id_tipo_punto"])."\")'>";
                                $link_elim2="</a>";
                                } else {
                                $link_elim1="<a href='javascript:alert(\"No se puede eliminar una pregunta ya respondida.\")'><font color=#999999>";
                                $link_elim2="</font></a>";
                                }
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td><font size=2 face="<?= $fuente_general; ?>"><?=$datos_pregunta["descripcion"];?></font></td>
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_pregunta["valor"];?></font></td>
                                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><a href="modificar_pregunta.php?pregunta=<?=$datos_pregunta["id_tipo_punto"];?>">Modificar</a> | <?=$link_elim1?>Eliminar<?=$link_elim2?></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px"> <input type="button" value="Nueva pregunta" style="font-size: 10px" onclick="window.location.href='modificar_pregunta.php';">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../configuracion/parte_inferior.php"); ?>
