<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_curso_actual=$_GET["id_curso_alumno"];
$id_usuario_seleccionado=$_GET["id_alumno"];

?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
                self.location.href='borrar_documento.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$subcarpeta_actual?>&carpeta_descarga='+carpeta+'&archivo_descarga='+documento;
                }

        }

//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center"><center>
<b>Selecciona el curso:</b> <?php 

// Muestra los cursos del alumno
$busca_cursos=db_query("SELECT * FROM cursos_academicos,alumnos WHERE cursos_academicos.id_curso=alumnos.id_curso AND alumnos.id_usuario=$id_usuario_seleccionado ORDER BY cursos_academicos.fecha_inicio");

while($datos_cursos=db_fetch_array($busca_cursos)) {
?>
<input type="button" style="font-size: 10px" value="Curso <?=$datos_cursos["nombre"]?>" onclick="window.location.href='ver_carpeta_alumno.php?id_curso_alumno=<?=$datos_cursos["id_curso"]?>&id_alumno=<?=$id_usuario_seleccionado?>';">
<?php 
        }
        
if (strlen($id_curso_actual)!=0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        // datos aula y alumnos
        $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=$id_usuario_seleccionado AND alumnos.id_curso=$id_curso_actual ORDER BY Apellido1, Apellido2, Nombre");
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_alumnos["aula"].";");
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_aulas["id_centro"]);
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $descarga_docs_path="/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno";

?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 

$columnas=2;

$fecha=strftime("%m/%d/%Y");

$subcarpeta_actual=$_GET["sub"];

$nombre_subcarpeta_actual=$subcarpeta_actual;

if (strlen($nombre_subcarpeta_actual)==0) {
        $nombre_subcarpeta_actual="/";
        }
        
$array_subcarpetas=explode("/",$nombre_subcarpeta_actual);

$carpeta_superior="";

for($ca=1;$ca<count($array_subcarpetas)-1;$ca++) {
        $carpeta_superior=$carpeta_superior."/".$array_subcarpetas[$ca];
        }

?>
<tr>
<td colspan=<?=$columnas?>><font size=1><b>Documentación del Curso <?=$datos_curso_actual["nombre"];?> de <?=$datos_alumnos["Nombre"];?> <?=$datos_alumnos["Apellido1"];?> <?=$datos_alumnos["Apellido2"];?><br>Carpeta actual:</b> <?=$nombre_subcarpeta_actual?> <?php  if ($nombre_subcarpeta_actual!="/") { ?><a href="ver_carpeta_alumno.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$carpeta_superior?>" title="Subir"><img align="absmiddle" src="subir.gif" border=0></a><?php  } ?><br><?php 
$d = dir($descarga_docs_path.$subcarpeta_actual);

$contador_sc=0;

while($s=$d->read()) {
        if (($s!=".") and ($s!="..") and (substr($s,(strlen($s)-4),1)!=".")) {
                if ($contador_sc==0) { echo("<b>Listado de subcarpetas</b> <i>Haz clic en ellas para ver su contenido</i><br>"); }
                ?><img src="/imagen/nada.gif" border=0 height=1 width=77><a href="ver_carpeta_alumno.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$subcarpeta_actual."/".$s?>" title="Abrir carpeta <?=$subcarpeta_actual."/".$s?>"><?=$subcarpeta_actual."/".$s?></a><br><?php 
                $contador_sc++;
                }
        }

?></td>
</tr>
<tr bgcolor="#efefef">
        <td><font size=1><b>Link</b></font></td><td align="right"><font size=1>&nbsp;</font></td>
</tr>
<?php 
$contador=0;
$d->close();
$d = dir($descarga_docs_path.$subcarpeta_actual);

      while($s=$d->read()) {

        if ((mb_strtoupper(substr($s,strlen($s)-3,3))=="XLS") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (mb_strtoupper(substr($s,strlen($s)-3,3))=="PPS"))
        {
        ?>
        <tr>
        <td><font size=2><?=app_text_utf8($s);?></font></td>
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
<br><br><input type="button" value="Guardar archivo en esta carpeta" style="font-size: 10px" onclick="window.location.href='upload_documento.php?id_alumno=<?=$id_usuario_seleccionado?>&id_curso_alumno=<?=$id_curso_actual?>&sub=<?=$subcarpeta_actual?>'"><br></font>
<?php 
}
?>
</center></div>
</div>
</body>
</html>
