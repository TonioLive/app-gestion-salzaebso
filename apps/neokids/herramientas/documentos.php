<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function copiar_link(texto) {
        window.clipboardData.setData('text',texto);
        alert("Ahora puedes 'Pegar' el link en tu documento.");
        }

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center"><center>
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
<td colspan=<?=$columnas?>><font size=1><b>Carpeta actual:</b> <?=$nombre_subcarpeta_actual?> <?php  if ($nombre_subcarpeta_actual!="/") { ?><a href="documentos.php?sub=<?=$carpeta_superior?>" title="Subir"><img align="absmiddle" src="subir.gif" border=0></a><?php  } ?><br><?php 
$d = dir($descarga_docs_path.$subcarpeta_actual);

$contador_sc=0;

while($s=$d->read()) {
        if (($s!=".") and ($s!="..") and (substr($s,(strlen($s)-4),1)!=".")) {
                if ($contador_sc==0) { echo("<b>Listado de subcarpetas</b> <i>Haz clic en ellas para ver su contenido</i><br>"); }
                ?><img src="/imagen/nada.gif" border=0 height=1 width=77><a href="documentos.php?sub=<?=$subcarpeta_actual."/".$s?>" title="Abrir carpeta <?=$subcarpeta_actual."/".$s?>"><?=$subcarpeta_actual."/".$s?></a><br><?php 
                $contador_sc++;
                }
        }

?><br><i>Para usar un documento copia y pega el Link en tu documento.</i></font></td>
</tr>
<tr bgcolor="#efefef">
        <td><font size=1><b>Link</b></font></td><td align="right"><font size=1>&nbsp;</font></td>
</tr>
<?php 
$contador=0;
$d->close();
$d = dir($descarga_docs_path.$subcarpeta_actual);

      while($s=$d->read()) {

        if ((strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (strtoupper(substr($s,strlen($s)-3,3))=="PPS"))
        {
        ?>
        <tr>
        <td><font size=1>
        <?php 
        $link_actual="http://gestion.neo-kids.es/descargas".$subcarpeta_actual."/".$s;
          print $link_actual;
        ?>
        </font></td><td align="right"><font size=1><a href="javascript: copiar_link('<?=$link_actual?>');">Copiar link</a> |
        <?php 
          print "<a target='_new' href='".$url_web."/descargas".$subcarpeta_actual."/".$s."'>Vista previa</a><br>";
        ?>
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
</table><font size=1><br><?=$contador?> Documentos encontrados en la carpeta. Solo se muestran documentos de Adobe Acrobat (PDF),Microsoft Word (DOC o RTF) y PowerPoint (PPT o PPS)</font>
</center></div>
</div>
</body>
</html>
