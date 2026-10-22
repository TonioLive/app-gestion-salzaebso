<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$subcarpeta_actual=$_GET["sub"];

switch ($centro_actual) {
	case 10:
		$mail_webmaster="santpau@escuelas-infantiles.com";
		$nombreRemitente="Escuela Infantil Sant Pau";
		$linkEscuela="santpau.escuelas-infantiles.com";
	break;	
	case 23:
		$mail_webmaster="benicalap@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal Benicalap";
		$linkEscuela="gestion.escuelainfantilbenicalap.com";
	break;
	case 23:
		$mail_webmaster="minipoli@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal Minipoli";
		$linkEscuela="gestion.escuelainfantilminipoli.com";
	break;	
	case 25:
		$mail_webmaster="lacreu@escuelas-infantiles.com";	
		$nombreRemitente="Escuela Infantil Municipal La Creu";
		$linkEscuela="gestion.escuelainfantillacreu.com";
	break;	        
}
$logoEscuela="https://".$linkEscuela."/imagen/logo_".$centro_actual.".jpg";

?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function copiar_link(texto) {
  navigator.clipboard.writeText(texto).then(function() {
    alert('Texto copiado al portapapeles: ' + texto +". \n\nAhora puedes 'Pegar' el link en tu mensaje.");
  }).catch(function(err) {
        alert('Error al copiar al portapapeles: ', err);
  });
}        

function borrar_documento(carpeta,documento) {

        if (confirm("Se va a borrar el documento: "+documento+"\n\nEsta acción no se puede deshacer. Al borrar el documento se eliminará de forma definitiva.\n\n¿estás seguro/a?")) {
                self.location.href='borrar_documento.php?sub=<?=$subcarpeta_actual?>&carpeta_descarga='+carpeta+'&archivo_descarga='+documento;
                }

        }

//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div class="container">
        <div align="center" class="text-center">
                <table class="table table-bordered table-hover">
                <?php 

                $columnas=2;

                $fecha=strftime("%m/%d/%Y");

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
                <td colspan=<?=$columnas?>><b>Carpeta actual:</b> <?=$nombre_subcarpeta_actual?> <?php  if ($nombre_subcarpeta_actual!="/") { ?><a href="documentos.php?sub=<?=$carpeta_superior?>" title="Subir"><img align="absmiddle" src="subir.gif" border=0></a><?php  } ?><br><?php 
                $d = dir($descarga_docs_path.$subcarpeta_actual);

                $contador_sc=0;

                while($s=$d->read()) {
                        if (($s!=".") and ($s!="..") and (substr($s,(strlen($s)-4),1)!=".") and (substr($s,(strlen($s)-5),1)!=".")) {
                                if ($contador_sc==0) { echo("<b>Listado de subcarpetas</b> <i>Haz clic en ellas para ver su contenido</i><br>"); }
                                ?><img src="/imagen/nada.gif" border=0 height=1 width=77><a href="documentos.php?sub=<?=$subcarpeta_actual."/".$s?>" title="Abrir carpeta <?=$subcarpeta_actual."/".$s?>"><?=$subcarpeta_actual."/".$s?></a><br><?php 
                                $contador_sc++;
                                }
                        }

                ?><br><i>Para usar un documento copia y pega el Link en tu mensaje o documento.</i></td>
                </tr>
                <tr bgcolor="#efefef">
                        <td><b>Link</b></td><td align="right">&nbsp;</td>
                </tr>
                <?php 
                $contador=0;
                $d->close();
                $d = dir($descarga_docs_path.$subcarpeta_actual);

                while($s=$d->read()) {

                        if ((strtoupper(substr($s,strlen($s)-3,3))=="PNG") or (strtoupper(substr($s,strlen($s)-3,3))=="GIF") or (strtoupper(substr($s,strlen($s)-3,3))=="JPG") or (strtoupper(substr($s,strlen($s)-4,4))=="JPEG") or (strtoupper(substr($s,strlen($s)-4,4))=="DOCX") or (strtoupper(substr($s,strlen($s)-4,4))=="XLSX") or (strtoupper(substr($s,strlen($s)-4,4))=="PPTX") or (strtoupper(substr($s,strlen($s)-3,3))=="PDF") or (strtoupper(substr($s,strlen($s)-3,3))=="DOC") or (strtoupper(substr($s,strlen($s)-3,3))=="RTF") or (strtoupper(substr($s,strlen($s)-3,3))=="PPT") or (strtoupper(substr($s,strlen($s)-3,3))=="PPS"))
                        {
                        ?>
                        <tr>
                        <td>
                        <?php 
                        $link_actual="https://".$linkEscuela."/descargas".$subcarpeta_actual."/".$s;
                        print "<a target='_new' href='https://".$linkEscuela."/descargas".$subcarpeta_actual."/".$s."'>".$link_actual."</a>";
                        ?>
                        </td><td align="center"><button type="button" class="btn btn-xs btn-primary" style="margin-top:5px;" onclick="copiar_link('<?=$link_actual?>');"><i class="fa fa-clipboard" aria-hidden="true"></i> Copiar link</button> 
                        <?php 
                        print "<a class=\"btn btn-xs btn-primary\" style=\"margin-top:5px;\" target=\"_new\" href=\"https://".$linkEscuela."/descargas".$subcarpeta_actual."/".$s."\">Vista previa</a>";
                        ?> <a class="btn btn-xs btn-danger" style="margin-top:5px;" href="javascript:borrar_documento('<?=$descarga_docs_path.$subcarpeta_actual;?>','<?=$s;?>');"><i class="fa fa-trash" aria-hidden="true"></i> Eliminar</a></td>
                        </tr>
                        <?php 
                        $contador=$contador+1;
                        }
                }
                $d->close();

                if ($contador==0) {
                        ?>
                        <tr>
                <td colspan=<?=$columnas?> align="center"><b>La carpeta no contiene documentos</b></td></tr>
                        <?php 
                        }

                ?>
                </table><br><?=$contador?> Documentos encontrados en la carpeta. Solo se muestran imágenes (JPG, JPEG, PNG y GIF), documentos de Adobe Acrobat (PDF),Microsoft Word (DOC o RTF) y PowerPoint (PPT o PPS)
                <br><br><button type="button" class="btn btn-primary" onclick="window.location.href='upload_documento.php?sub=<?=$subcarpeta_actual?>'"><i class="fa fa-upload" aria-hidden="true"></i> Guardar archivo en esta carpeta</button><br>
        </div>
</div>
</body>
</html>
