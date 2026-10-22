<?php  require("../../configuracion/parte_superior_docente_ancho.php");

require("config/config_personal.php");

// Busca contrato en vigor

$id_docente=$idUsuario;

if (strlen($centro_actual)==0) {
        $centro_actual=0;
        }

if (strlen($id_docente)!=0) {

        $busca_datos_docente=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_docente, $centro_actual)) or die(db_error().".1");

        if (db_num_rows($busca_datos_docente)==0) {
                db_query_params('INSERT INTO horario_docentes (id_usuario, id_centro) VALUES (?,?)', 'ss', array($id_docente, $centro_actual)) or die(db_error().".2");
                // Vuelve a ejecutar la consulta
                $busca_datos_docente=db_query_params('SELECT * FROM horario_docentes, usuarios WHERE usuarios.id_usuario=horario_docentes.id_usuario AND horario_docentes.id_usuario=? AND horario_docentes.id_centro=?', 'ss', array($id_docente, $centro_actual)) or die(db_error().".3");
                }

        $datos_docente=db_fetch_array($busca_datos_docente);
        $id_horario=$datos_docente["id_horario"];
                $mensaje="Edición";
                $nombre_docente=$datos_docente["Nombre"]." ".$datos_docente["Apellido1"]." ".$datos_docente["Apellido2"];
                $nombre_docente=ucwords(strtolower($nombre_docente));
                        // Fechas
                        if ($datos_docente["fecha_inicio"]=="0000-00-00") {
                                $fecha_inicio_dia="";
                                $fecha_inicio_mes="";
                                $fecha_inicio_ano="";
                                } else {
                                $fecha_inicio_dia=date("j",strtotime($datos_docente["fecha_inicio"]));
                                $fecha_inicio_mes=date("m",strtotime($datos_docente["fecha_inicio"]));
                                $fecha_inicio_ano=date("Y",strtotime($datos_docente["fecha_inicio"]));
                                }

                        if ($datos_docente["fecha_fin"]=="0000-00-00") {
                                $fecha_fin_dia="";
                                $fecha_fin_mes="";
                                $fecha_fin_ano="";
                                } else {
                                $fecha_fin_dia=date("j",strtotime($datos_docente["fecha_fin"]));
                                $fecha_fin_mes=date("m",strtotime($datos_docente["fecha_fin"]));
                                $fecha_fin_ano=date("Y",strtotime($datos_docente["fecha_fin"]));
                                }
                $hora_entrada_man=$datos_docente["hora_entrada1"];
                if ($hora_entrada_man=="00:00:00") {
                        $hora_entrada_man=$hora_entrada_centro_man;
                        }
                $hora_salida_man=$datos_docente["hora_salida1"];
                if ($hora_salida_man=="00:00:00") {
                        $hora_salida_man=$hora_salida_centro_man;
                        }
                $hora_entrada_tar=$datos_docente["hora_entrada2"];
                if ($hora_entrada_tar=="00:00:00") {
                        $hora_entrada_tar=$hora_entrada_centro_tar;
                        }
                $hora_salida_tar=$datos_docente["hora_salida2"];
                if ($hora_salida_tar=="00:00:00") {
                        $hora_salida_tar=$hora_salida_centro_tar;
                        }
                $observaciones=$datos_docente["observaciones"];
                $l=$datos_docente["l"];
                $m=$datos_docente["m"];
                $x=$datos_docente["x"];
                $j=$datos_docente["j"];
                $v=$datos_docente["v"];
                $s=$datos_docente["s"];
                $d=$datos_docente["d"];
        }

$url_retorno="/index.php";

?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Ficha de personal</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=4 face="<?= $fuente_general; ?>"><b><?=$nombre_docente?></b></font></td>
                        </tr>
                        <tr>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Contratos laborales</b></font></td>
                        </tr>
                        <?php 
                        // Busca contratos

                        $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=?', 's', array($id_docente));

                        if (db_num_rows($busca_contratos)==0) {
                                ?>
                        <tr bgcolor="#ffffff">
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>">No se han encontrado contratos.</font></td>
                        </tr>
                                <?php 
                                } else {
                                ?>
                        <tr bgcolor="#ffffff">
                        <td colspan=2 align="center">
                        <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                                <?php 

                        while($lista_contratos=db_fetch_array($busca_contratos)){

                        // Busca cat laboral
                        $busca_tipos_cat=db_query_params('SELECT * FROM personal_cat_laborales WHERE id_categoria=?', 's', array($lista_contratos["id_categoria"]));

                        if (db_num_rows($busca_tipos_cat)==0) {
                                $nombre_tipo_cat="Desconocida";
                                } else {
                                $tipos_cat=db_fetch_array($busca_tipos_cat);
                                $nombre_tipo_cat=$tipos_cat["nombre_categoria"];
                                }

                        // Busca tipos contrato
                        $busca_tipos_contrato=db_query("SELECT * FROM personal_tipos_contrato WHERE id_tipo_contrato=".$lista_contratos["tipo_contrato"]);

                        if (db_num_rows($busca_tipos_contrato)==0) {
                                $nombre_tipo_contrato="Desconocido";
                                } else {
                                $tipos_contrato=db_fetch_array($busca_tipos_contrato);
                                $nombre_tipo_contrato=$tipos_contrato["nombre_tipo_contrato"];
                                }

                        // Fechas
                        if ($lista_contratos["tipo_contrato"]!=2) {
                                $fechas_contrato1=date("d/m/Y",strtotime($lista_contratos["fecha_inicio"]));
                                $fechas_contrato2=date("d/m/Y",strtotime($lista_contratos["fecha_fin"]));
                                } else {
                                $fechas_contrato1=date("d/m/Y",strtotime($lista_contratos["fecha_inicio"]));
                                $fechas_contrato2="-";
                                }

                        ?>
                        <tr bgcolor="#efefef">
                                <td><font size=2><b>Categoría</b></font></td>
                                <td><font size=2><b>Tipo contrato</b></font></td>
                                <td><font size=2><b>Inicio</b></font></td>
                                <td><font size=2><b>Fin</b></font></td>
                        </tr>
                        <tr bgcolor="#ffffff">
                                <td><font size=2><?=$nombre_tipo_cat?></font></td>
                                <td><font size=2><?=$nombre_tipo_contrato?></font></td>
                                <td align="center"><font size=2><?=$fechas_contrato1?></font></td>
                                <td align="center"><font size=2><?=$fechas_contrato2?></font></td>
                        </tr>
                        <tr bgcolor="#efefef">
                                <td colspan=4><b><font size=2>Documentación prevención de riesgos laborales recibida en este contrato</font></b></td>
                        </tr>
                        <?php 
                        // Busca documentación PRL requerida
                        
                        $busca_doc_prl=db_query("SELECT * FROM personal_tipos_doc_prevencion ORDER BY nombre_tipo_doc");
                        
                        while($doc_prl=db_fetch_array($busca_doc_prl)) {
                        
                                // Busca si se ha entregado
                                $busca_doc_entregada=db_query_params('SELECT * FROM personal_doc_prevencion WHERE id_contrato=? AND tipo_doc=?', 'ss', array($lista_contratos["id_contrato"], $doc_prl["id_tipo_doc"])) or die(db_error().".4");
                                
                                $color_fila="#000000";
                                $texto_advertencia="";
                                
                                if ((db_num_rows($busca_doc_entregada)==0) and ($doc_prl["requerido"]==1)) {
                                        $color_fila="#ff0000";
                                        $texto_advertencia=" ¡ATENCIÓN! Documentación requerida, rellena inmediatamente el documento.";
                                        }

                                $esta_creado=false;
                                        
                                // Comprueba si ya existe la carpeta de documentos
                                $carpeta_documentos_actual=$carpeta_documentos."//usuario_$idUsuario";

                                if (!file_exists($carpeta_documentos_actual)) {
                                        @mkdir($carpeta_documentos_actual) ;
                                        }

                                // Comprueba si hay documento es la carpeta

                                $nombre_doc=$doc_prl["nombre_doc"];

                                if (strlen($nombre_doc)!=0) {

                                $d = dir($carpeta_documentos_actual);
                                $nombre_doc1=$nombre_doc."_".$id_docente."_".$lista_contratos["id_contrato"];
                                while($s=$d->read()) {
                                        if (substr($s,0,strlen($nombre_doc1))==$nombre_doc1) {
                                                $esta_creado=true;
                                                }
                                        }
                                $d->close();
                        
                                }
                        
                                if ($esta_creado==true) {
                                        $color_fila="#000000";
                                        $texto_advertencia="";
                                        }

                                ?>
                                <tr>
                                <td colspan=2><font size=2 color="<?=$color_fila?>"><b><?=$doc_prl["nombre_tipo_doc"]?></b></td>
                                <td align="center"><font size=2 color="<?=$color_fila?>"><?=$texto_advertencia?></td>
                                <td align="center"><?php 
                                if (strlen($doc_prl["nombre_plantilla"])!=0) {
                                        ?><a target="_new" href="abrir_plantilla.php?id_doc=<?=$doc_prl["id_tipo_doc"]?>&id_usu=<?=$id_docente?>&id_contrato=<?=$lista_contratos["id_contrato"]?>" title="Abrir plantilla" onclick="history.go(0);"><img src="plantilla.gif" border=0></a><?php 
                                        }
                                        ?> <a href="subir_documento.php?id_doc=<?=$doc_prl["id_tipo_doc"]?>&id_usu=<?=$id_docente?>&id_contrato=<?=$lista_contratos["id_contrato"]?>" title="Guardar documento firmado"><img src="upload.gif" border=0></a></td>
                                </tr>
                                <?php 
                                $nombre_doc=$doc_prl["nombre_doc"];

                                if ($esta_creado==true) {

                                $d = dir($carpeta_documentos_actual);
                                while($s=$d->read()) {
                                        if (substr($s,0,strlen($nombre_doc))==$nombre_doc) {
                                                ?>
                                                <tr>
                                                <td colspan=3><font size=2 color="<?=$color_fila?>">&nbsp; - <?=$s?></font></td>
                                                <td align="center"><font size=2 color="<?=$color_fila?>"><a href="descargar_documento.php?id_doc=<?=$s?>" title="Abrir documento" target="_new"><img src="download.gif" border=0></a> <a href="#" onclick="if (confirm('¿Deseas eliminar permanentemente el documento <?=$s?>?\n\nEsta acción no puede deshacerse.')) { self.location.href='eliminar_documento.php?id_doc=<?=$s?>'; }" title="Eliminar documento"><img src="borrar.gif" border=0></a></font></td>
                                                </tr>
                                                <?php 
                                                }
                                        }
                                $d->close();

                                }
                                ?>
                                <tr>
                                <td colspan=4><font size=2 color="<?=$color_fila?>">&nbsp;</font></td>
                                </tr>
                                <?php 
                                }
                        
                        }
                        ?>
                        </table>
                        </td></tr>
                        <?php 
                        }
                        ?>
                        <tr>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Historial de cambios de aula</b></font></td>
                        </tr>
                        <?php 
                        // Busca cambios

                        $busca_cambios=db_query_params('SELECT * FROM personal_historial WHERE id_empleado=?', 's', array($id_docente));

                        if (db_num_rows($busca_cambios)==0) {
                                ?>
                        <tr bgcolor="#ffffff">
                                <td colspan=2 align="center"><font size=2 face="<?= $fuente_general; ?>">No se han encontrado cambios de aula.</font></td>
                        </tr>
                                <?php 
                                } else {
                                ?>
                        <tr bgcolor="#ffffff">
                        <td colspan=2 align="center">
                        <table width=100% border=1 bordercolor="#000000" style="border-collapse: collapse">
                        <tr bgcolor="#efefef">
                                <td><font size=2><b>Escuela</b></font></td>
                                <td><font size=2><b>Aula</b></font></td>
                                <td><font size=2><b>Inicio/Fin</b></font></td>
                        </tr>
                                <?php 

                        while($lista_cambios=db_fetch_array($busca_cambios)){

                        // Busca centro
                        $busca_centros_cambio=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($lista_cambios["id_escuela"]));

                        if (db_num_rows($busca_centros_cambio)==0) {
                                $nombre_centro_cambio="Desconocida";
                                } else {
                                $centros_cambio=db_fetch_array($busca_centros_cambio);
                                $nombre_centro_cambio=$centros_cambio["nombre_centro"];
                                }

                        // Busca aula
                        $busca_aulas_cambio=db_query_params('SELECT cursos_academicos.nombre AS nombre_curso,centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=?', 's', array($lista_cambios["id_aula"]));

                        if (db_num_rows($busca_aulas_cambio)==0) {
                                $nombre_aula_cambio="Desconocida";
                                } else {
                                $aulas_cambio=db_fetch_array($busca_aulas_cambio);
                                $nombre_aula_cambio=$aulas_cambio["nombre_aula"].", Curso: ".$aulas_cambio["nombre_curso"];
                                }

                        // Fechas
                        if (strlen($lista_cambios["fecha_fin"])!=0) {
                                $fechas_cambio=date("d/m/Y",strtotime($lista_cambios["fecha_inicio"]))."-".date("d/m/Y",strtotime($lista_cambios["fecha_fin"]));
                                } else {
                                $fechas_cambio=date("d/m/Y",strtotime($lista_cambios["fecha_inicio"]))."-Actualidad";
                                }

                        ?>
                        <tr bgcolor="#ffffff">
                                <td><font size=2><?=$nombre_centro_cambio?></font></td>
                                <td><font size=2><?=$nombre_aula_cambio?></font></td>
                                <td align="center"><font size=2><?=$fechas_cambio?></font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                        </td></tr>
                        <?php 
                        }
                        ?>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Horario de entrada y salida</b></font></td>
                        </tr>
                        <tr>
                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mañanas:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <?=date("H:i",strtotime($hora_entrada_man))?> a <?=date("H:i",strtotime($hora_salida_man))?></font></td>
                        </tr>
                        <tr>
                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Tardes:</b></font></td>
                        <td><font size=2 face="<?= $fuente_general; ?>">De <?=date("H:i",strtotime($hora_entrada_tar))?> a <?=date("H:i",strtotime($hora_salida_tar))?></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Días de la semana</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="center" colspan=2>
                                <table border=1 bordercolor="#000000" style="border-collapse: collapse" cellpadding=2 cellspacing=0>
                                <tr>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>L</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>M</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>X</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>J</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>V</b></font></td>
                                <td bgcolor="#CDCDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>S</b></font></td>
                                <td bgcolor="#EECDCD" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>D</b></font></td>
                                </tr>
                                <tr>
                                <td bgcolor="#ffffff" align="center"><?php  if ($l==1) { print "X"; } ?></td>
                                <td bgcolor="#ffffff" align="center"><?php  if ($m==1) { print "X"; } ?></td>
                                <td bgcolor="#ffffff" align="center"><?php  if ($x==1) { print "X"; } ?></td>
                                <td bgcolor="#ffffff" align="center"><?php  if ($j==1) { print "X"; } ?></td>
                                <td bgcolor="#ffffff" align="center"><?php  if ($v==1) { print "X"; } ?></td>
                                <td bgcolor="#ffffff" align="center"><?php  if ($s==1) { print "X"; } ?></td>
                                <td bgcolor="#ffffff" align="center"><?php  if ($d==1) { print "X"; } ?></td>
                                </tr>
                                </table>
                                </td>
                        </tr>
                        </table><br>
                        <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno;?>'" style="font-size: 14px"> <input type="button" value="Calendario laboral detallado" onclick="window.location.href='ver_calendario.php'" style="font-size: 14px">
                        <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php  require("../../configuracion/parte_inferior.php"); ?>
