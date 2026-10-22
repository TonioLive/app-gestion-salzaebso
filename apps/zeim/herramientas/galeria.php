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
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 

$columnas=4;

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
<td colspan=<?=$columnas?>><font size=1><b>Carpeta actual:</b> <?=$nombre_subcarpeta_actual?> <?php  if ($nombre_subcarpeta_actual!="/") { ?><a href="galeria.php?sub=<?=$carpeta_superior?>" title="Subir"><img align="absmiddle" src="subir.gif" border=0></a><?php  } ?><br><?php 
$d = dir($galeria_fotos_path.$subcarpeta_actual);

$contador_sc=0;

while($s=$d->read()) {
        if (($s!=".") and ($s!="..") and (substr($s,(strlen($s)-4),1)!=".")) {
                if ($contador_sc==0) { echo("<b>Listado de subcarpetas</b> <i>Haz clic en ellas para ver su contenido</i><br>"); }
                ?><img src="/imagen/nada.gif" border=0 height=1 width=77><a href="galeria.php?sub=<?=$subcarpeta_actual."/".$s?>" title="Abrir carpeta <?=$subcarpeta_actual."/".$s?>"><?=$subcarpeta_actual."/".$s?></a><br><?php 
                $contador_sc++;
                }
        }

?><br><i>Para usar una imagen arrastrala sobre el texto.</i></font></td>
</tr>
<?php 
$contador=0;

$d->close();
$d = dir($galeria_fotos_path.$subcarpeta_actual);

$col=1;

      while($s=$d->read()) {

        if ((strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (strtoupper(substr($s,strlen($s)-3,3))=="GIF"))
        {
         if ($col==1) {
          print "<tr>";
          }
          $col++;
          print "<td valign=top align='center'><a href='".$url_web."/galeria/".$subcarpeta_actual."/".$s."' target='_blank'><img src='".$url_web."/galeria/".$subcarpeta_actual."/".$s."' width='75' border=0></a></td>";
         if ($col==$columnas) {
          print "</tr>";
          $col=1;
         }
        $contador=$contador+1;
        }
      }
$d->close();

if ($contador==0) {
        ?>
        <tr>
<td colspan=<?=$columnas?> align="center"><font size=3><b>La carpeta no contiene imágenes</b></font></td></tr>
        <?php 
        }

?>
</table><font size=1><br><?=$contador?> Imágenes encontradas en la carpeta.</font>
</center></div>
</div>
</body>
</html>
