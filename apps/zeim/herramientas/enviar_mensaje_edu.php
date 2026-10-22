<?php  require("../configuracion/parte_superior_privado.php");

if (($nivel_acceso==4) or ($zona_mensajes==1)) {

$id_centro=$centro_actual;

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        if ($id_curso_actual!=0) {
                                $filtro=" AND (id_curso=$id_curso_actual OR id_curso=0)";
                                } else {
                                $filtro="";
                                }
?>
<script language="JavaScript" type="text/javascript" src="wysiwyg.js"></script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Nuevo mensaje<br></b></font><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width=100%>
                        <?php 
                        // http://www.escuelas-infantiles.com/mailing/enviar_mensaje_todos.php
                        ?>
                        <form name="edicion_mensaje" method="POST" target="_new" action="guardar_mensaje_edu.php">
                        <input type="hidden" value="<?=$id_centro?>" name="id_centro">
                        <input type="hidden" value="0" name="accion">
                        <input type="hidden" value="<?=$_SERVER["HTTP_HOST"]?>" name="retorno">
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Enviar a educadoras:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?php 
                          $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro");
                          while($datos_escuela=db_fetch_array($busca_centros)) {
                          ?><input type="checkbox" value="1" name="edu_<?=$datos_escuela["id_centro"];?>"> <?=$datos_escuela["nombre_centro"];?><br><?php 
                          }
                          ?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Enviar a coordinadoras:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?php 
                          $busca_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro");
                          while($datos_escuela=db_fetch_array($busca_centros)) {
                          ?><input type="checkbox" value="1" name="coo_<?=$datos_escuela["id_centro"];?>"> <?=$datos_escuela["nombre_centro"];?><br><?php 
                          }
                          ?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Otros destinatarios:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="checkbox" value="1" name="adm"> Administración</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" bgcolor="#ececec"><font size=1 face="<?= $fuente_general; ?>">Asunto:</font></td>
                          <td align="left" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="asunto" value="" size=60 style="font-size: 10px"></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="center" bgcolor="#ffffff" colspan=2><font size=1 face="<?= $fuente_general; ?>">
                          <textarea id="textarea1" name="texto_editor" style="height: 250px; width: 700px;">

                          </textarea>
                          </font></td>
                        </tr>
                         </table><br>
                         <input type="button" value="Galería de imágenes" onclick="javascript:window.open('galeria.php','OpenWindow17880','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=no,width=560,height=450,titlebar=0,minimize=no,maximize=no')" style="font-size: 10px">
                         <input type="button" value="Documentos" onclick="javascript:window.open('documentos.php','OpenWindow17845','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,menubar=no,width=560,height=450,titlebar=0,minimize=no,maximize=no')" style="font-size: 10px">
                         <input style="font-size: 10px" type="button" onclick="vista_previa()" value="Vista previa"> <input style="font-size: 10px" type="button" onclick="prueba()" value="Enviar mensaje de prueba"><br>
                         <input style="font-size: 10px" type="submit" value="Enviar mensaje"> <input type="button" value="Cancelar" onclick="window.location.href='/index.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
                        </form>
</table>
<br>
<br>
<script language="javascript1.2">

generate_wysiwyg('textarea1');


function vista_previa() {

        updateTextArea('textarea1');
        document.edicion_mensaje.accion.value="1";
        document.edicion_mensaje.submit();

        }

function prueba() {

        updateTextArea('textarea1');
        document.edicion_mensaje.accion.value=2;
        document.edicion_mensaje.submit();

        }

</script>
<?php 
}
 require("../configuracion/parte_inferior.php"); ?>
