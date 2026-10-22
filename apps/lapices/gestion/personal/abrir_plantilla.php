<?php  require("../../configuracion/config.php");

require("../../configuracion/autentifica_docente.php");

require("config/config_personal.php");

$id_doc=$_GET["id_doc"];
$id_usu=$_GET["id_usu"];
$id_contrato=$_GET["id_contrato"];

// Etiquetas comunes
$FECHA=date("d/m/Y");

// Busca datos usuario
$busca_usu=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usu");
$datos_usu=db_fetch_array($busca_usu);

$NOMBRE_TRABAJADOR=$datos_usu["Nombre"]." ".$datos_usu["Apellido1"]." ".$datos_usu["Apellido2"];
$DNI_TRABAJADOR=$datos_usu["DNI"];

// Centro de trabajo
$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$datos_usu["id_centro"]);
if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $CENTRO_TRABAJO=$datos_centro["nombre_centro"];
                } else {
                $CENTRO_TRABAJO="";
                }

// Datos contrato
$busca_contrato=db_query("SELECT * FROM personal_contratos WHERE id_contrato=$id_contrato");
$datos_contrato=db_fetch_array($busca_contrato);

// Busca cat laboral
$busca_tipos_cat=db_query("SELECT * FROM personal_cat_laborales WHERE id_categoria=".$datos_contrato["id_categoria"]);

if (db_num_rows($busca_tipos_cat)==0) {
        $PUESTO_TRABAJO="";
        } else {
        $tipos_cat=db_fetch_array($busca_tipos_cat);
        $PUESTO_TRABAJO=$tipos_cat["nombre_categoria"];
        }
        
// Busca centro de trabajo


// Busca plantilla
$busca_doc_prl=db_query("SELECT * FROM personal_tipos_doc_prevencion WHERE id_tipo_doc=$id_doc");

$doc_prl=db_fetch_array($busca_doc_prl);

// Abre plantilla
$nombre_plantilla=$doc_prl["nombre_plantilla"];

$archivo_plantilla = $carpeta_plantillas."/$nombre_plantilla";

$gestor2 = fopen($archivo_plantilla, "rb");
$contenido = fread($gestor2, filesize($archivo_plantilla));
fclose($gestor2);

// Sustituye campos de plantilla por datos

$contenido = str_replace("[FECHA]", $FECHA, $contenido);
$contenido = str_replace("[NOMBRE_TRABAJADOR]", $NOMBRE_TRABAJADOR, $contenido);
$contenido = str_replace("[PUESTO_TRABAJO]", $PUESTO_TRABAJO, $contenido);
$contenido = str_replace("[CENTRO_TRABAJO]", $CENTRO_TRABAJO, $contenido);
$contenido = str_replace("[DNI_TRABAJADOR]", $DNI_TRABAJADOR, $contenido);

// Crea el documento definitivo
$nombre_doc=$doc_prl["nombre_doc"];

$carpeta_documentos_actual=$carpeta_documentos."/usuario_$idUsuario";

// Comprueba si existe la carpeta

@mkdir($carpeta_documentos_actual) ;

$nombre_nuevo_archivo=$carpeta_documentos_actual."/$nombre_doc"."_".$id_usu."_".$id_contrato."_".date("d-m-Y").".rtf";

if (file_exists($nombre_nuevo_archivo)) {
        if (is_writable($nombre_nuevo_archivo)) {
                                unlink ($nombre_nuevo_archivo);
                }
        }

$gestor_nuevo = fopen($nombre_nuevo_archivo,"w+");
fwrite($gestor_nuevo,$contenido);
fclose($gestor_nuevo);

include("EasyDownload.php");


// path example
$myPath = $carpeta_documentos_actual;

// New Object
$objDownload = new EasyDownload();

// Set physical path
$objDownload->setPath($myPath);

// Set file name on the server (real full name)
$objDownload->setFileName($nombre_doc."_".$id_usu."_".$id_contrato."_".date("d-m-Y").".rtf");

// In case that it does not desire to effect download with original name.
// It configures the alternative name
$objDownload->setFileNameDown($nombre_doc."_".$id_usu."_".$id_contrato."_".date("d-m-Y") . ".rtf");

// get file
$objDownload->Send();

?>
<html>
<head>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

