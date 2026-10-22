<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

// Limpia nombre archivo
if (!function_exists('limpiarCadena')) {
        function limpiarCadena($cadena) {
            // Tabla de transliteración manual compatible con versiones antiguas de PHP
            $transliteracion = array(
                'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE',
                'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
                'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
                'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
                'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
                'Ý' => 'Y', 'Þ' => 'TH', 'ß' => 'ss',
                'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
                'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
                'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
                'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
                'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
                'ý' => 'y', 'þ' => 'th', 'ÿ' => 'y',
                'Œ' => 'OE', 'œ' => 'oe', 'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z'
            );
            
            // Reemplazar caracteres acentuados usando strtr()
            $cadena = strtr($cadena, $transliteracion);
    
            // Reemplazar los espacios por guiones
            $cadena = str_replace(' ', '-', $cadena);
    
            // Eliminar todos los caracteres que no sean letras, números, guiones o puntos
            $cadena = preg_replace('/[^A-Za-z0-9\-.]/', '', $cadena);
    
            return $cadena;
        }
}

$subcarpeta_actual=$_GET["sub"];


$carpeta_docs="$descarga_docs_path$subcarpeta_actual/";

if (is_uploaded_file($_FILES['File1']['tmp_name'])) {

        $nombre_archivo1=$_FILES['File1']['name'];
        $nombre_temp=$_FILES['File1']['tmp_name'];

        if (strlen($nombre_archivo1)>3) {
                $extension_documento=strtolower(substr($nombre_archivo1,strlen($nombre_archivo1)-3,3));
                $extension_documento1=strtolower(substr($nombre_archivo1,strlen($nombre_archivo1)-4,4));
                }


        if (($extension_documento=="png") or ($extension_documento=="gif") or ($extension_documento=="jpg") or ($extension_documento1=="jpeg") or ($extension_documento1=="docx") or ($extension_documento1=="xlsx") or ($extension_documento1=="pptx") or ($extension_documento=="pdf") or ($extension_documento=="doc") or ($extension_documento=="rtf") or ($extension_documento=="ppt") or ($extension_documento=="pps") or ($extension_documento=="xls")) {

        $nombre_archivo=limpiarCadena($nombre_archivo1);

        if (!file_exists($carpeta_docs.$nombre_archivo)) {
                                                            $cambia_nombre=copy($nombre_temp, $carpeta_docs.$nombre_archivo);
                                                            } else {
                                                            unlink($carpeta_docs.$nombre_archivo);
                                                            $cambia_nombre=copy($nombre_temp, $carpeta_docs.$nombre_archivo);
                                                            }

        } else {
        $errores=1;
        }
        
if ($errores==0) {
        header("Location: documentos.php?sub=$subcarpeta_actual");
        }

} else {
    // echo "Posible ataque file upload. Nombre del archivo: " . $_FILES['File1']['name'];
}
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

<body>
<div class="container">
<p align="center">
<font face="Arial"><br><br><br>
<b>Error: Solo se permiten imágenes (JPG, JPEG, PNG y GIF), documentos de Adobe Acrobat (PDF),Microsoft Word (DOC o RTF), Microsoft Excel (XLS) y PowerPoint (PPT o PPS)
<br><br><br><input name="Volver" type="button" id="Cancelar" tabindex="8" value="Cancelar" onclick="window.location.href='documentos.php?sub=<?=$subcarpeta_actual?>'" />
</font></p>
</div>
</body>
</html>

