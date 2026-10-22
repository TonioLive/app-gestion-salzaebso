<?php  require("../../configuracion/parte_superior_docente.php");

require("config/config_personal.php");

$id_doc=$_GET["id_doc"];
$id_usu=$_GET["id_usu"];
$id_contrato=$_GET["id_contrato"];

// Etiquetas comunes
$FECHA=date("d/m/Y");

// Busca datos usuario
$busca_usu=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usu));
$datos_usu=db_fetch_array($busca_usu);

$NOMBRE_TRABAJADOR=$datos_usu["Nombre"]." ".$datos_usu["Apellido1"]." ".$datos_usu["Apellido2"];
$DNI_TRABAJADOR=$datos_usu["DNI"];

// Centro de trabajo
$busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_usu["id_centro"]));
if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $CENTRO_TRABAJO=$datos_centro["nombre_centro"];
                } else {
                $CENTRO_TRABAJO="";
                }

// Datos contrato
$busca_contrato=db_query_params('SELECT * FROM personal_contratos WHERE id_contrato=?', 's', array($id_contrato));
$datos_contrato=db_fetch_array($busca_contrato);

// Busca cat laboral
$busca_tipos_cat=db_query_params('SELECT * FROM personal_cat_laborales WHERE id_categoria=?', 's', array($datos_contrato["id_categoria"]));

if (db_num_rows($busca_tipos_cat)==0) {
        $PUESTO_TRABAJO="";
        } else {
        $tipos_cat=db_fetch_array($busca_tipos_cat);
        $PUESTO_TRABAJO=$tipos_cat["nombre_categoria"];
        }
        
// Busca centro de trabajo


// Busca plantilla
$busca_doc_prl=db_query_params('SELECT * FROM personal_tipos_doc_prevencion WHERE id_tipo_doc=?', 's', array($id_doc));

$doc_prl=db_fetch_array($busca_doc_prl);

$nombre_plantilla=$doc_prl["nombre_plantilla"];
$nombre_doc=$doc_prl["nombre_doc"];

$carpeta_documentos_actual=$carpeta_documentos."//usuario_$idUsuario";


$nombre_nuevo_archivo=$carpeta_documentos_actual."//$nombre_doc"."_".$id_usu."_".$id_contrato."_".date("d-m-Y").".rtf";

?>
<script language="JavaScript">
<!-- hide from JavaScript-challenged browsers

function envia_formulario(){
                Codigo=document.form1.File1.value;
                if (Codigo=="")
                              {
                              alert("Debes seleccionar el documento que será guardado en el servidor.");
                              } else {
                              errores=0;
                              mensaje_de_error="";

                              if (errores==0)
                                            {
                                            document.form1.action="subir_documento1.php?id_doc=<?=$id_doc;?>&id_usu=<?=$id_usu;?>&id_contrato=<?=$id_contrato;?>";
                                            document.form1.submit();
                                            } else {
                                            alert(mensaje_de_error);
                                            }
                              }
        }
// done hiding -->
</script>
<div align="center"><center>
<table border="0" width="100%">
<form ENCTYPE="multipart/form-data" method=POST action="javascript:envia_formulario()" id=form1 name=form1>
  <tr>
    <td align="center"><small>
    <br>
    Desde está página puedes enviar el archivo, desde tu equipo al servidor.<br><br>
    Lo único que debes hacer es indicar donde se encuentra el archivo que quieres guardar (puedes buscarlo con el botón <b>Examinar</b>) y despues pulsar el botón <b>Guardar documento</b> para que comience la transmisión.<br><br>
    Ten un poco de paciencia (sobre todo con archivos grandes), cuando acabe recibirás una notificación.<br><br>
    </small>
    <div align="center"><center>
    <table border="1" style="border-collapse: collapse" bordercolor="#000000" width="80%" bgcolor="#cecece">
        <tr>
                        <td width="100%" align="center" colspan=2><small><small><font face="<?= $fuente_general; ?>"><b>Nombre del archivo:</b> <INPUT NAME="File1" SIZE=30 TYPE="file" style="font-size: 10px"></small></small></td>
                </tr>
        </form>
</table>
<br>
<input type="button" value="Guardar documento" name="B1" style="font-size: 10" onClick="javascript:envia_formulario()"> <input type="button" value="Cancelar" name="B2" style="font-size: 10" onclick="history.go(-1)">
</center></div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

