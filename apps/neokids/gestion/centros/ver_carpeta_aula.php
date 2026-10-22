<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
        if (db_num_rows($busca_datos_aula)!=0) {
                $datos_aula=db_fetch_array($busca_datos_aula);
                $nombre_aula=$datos_aula["nombre"];
                $id_curso_actual=$datos_aula["id_curso"];
                $id_centro_aula=$datos_aula["id_centro"];
                }
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function copiar_link(texto) {
        window.clipboardData.setData('text',texto);
        alert("Ahora puedes 'Pegar' el link en tu documento.");
        }

function borrar_documento(carpeta,documento) {

        if (confirm("Se va a borrar el documento: "+documento+"\n\nEsta acción no se puede deshacer. Al borrar el documento se eliminará de forma definitiva.\n\n¿estás seguro/a?")) {
                self.location.href='borrar_documento_aula.php?id_aula=<?=$id_aula;?>&carpeta_descarga='+carpeta+'&archivo_descarga='+documento;
                }

        }

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center"><center>
<?php 
        
if (strlen($id_curso_actual)!=0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y centro
        
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($id_aula));
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_centro_aula));
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $descarga_docs_path="/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/padres";

?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 

$columnas=2;

$fecha=strftime("%m/%d/%Y");

$nombre_subcarpeta_actual="/";

?>
<tr>
<td colspan=<?=$columnas?>><font size=1><b>Documentación del Curso <?=$datos_curso_actual["nombre"];?> del aula <?=$datos_aulas["nombre"];?></td>
</tr>
<tr bgcolor="#efefef">
        <td><font size=1><b>Link</b></font></td><td align="right"><font size=1>&nbsp;</font></td>
</tr>
<?php 
$contador=0;

$d = dir($descarga_docs_path.$subcarpeta_actual);

      while($s=$d->read()) {

        if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS"))
        {
        ?>
        <tr>
        <td><font size=2><?=utf8_decode($s);?></font></td>
        <td align="center"><font size=2><a href="abrir_documento.php?carpeta_descarga=<?=$descarga_docs_path.$subcarpeta_actual;?>&archivo_descarga=<?=$s;?>">Abrir</a> | <a href="javascript:borrar_documento('<?=$descarga_docs_path.$subcarpeta_actual;?>','<?=$s;?>');">Borrar</a><br>
        </font></td>
        </tr>
        <?php 
        $contador=$contador+1;
        }
      }
$d->close();

if ($contador==0) {
        ?>
        <tr>
<td colspan=<?=$columnas?> align="center"><font size=3><b>La carpeta no contiene documentos</b></font></td></tr>
        <?php 
        }

?>
</table><font size=1><br><?=$contador?> Documentos encontrados en la carpeta. Solo se muestran documentos de Adobe Acrobat (PDF),Microsoft Word (DOC o RTF), Microsoft Excel (XLS) y PowerPoint (PPT o PPS)
<br><br><input type="button" value="Guardar archivo en esta carpeta" style="font-size: 10px" onclick="window.location.href='upload_documento_aula.php?id_aula=<?=$id_aula?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$subcarpeta_actual?>'"><br></font>
<?php 
}
?>
</center></div>
</div>
</body>
</html>
