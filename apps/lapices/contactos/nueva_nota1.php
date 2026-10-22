<?php 
require("../configuracion/config.php");

$id_documento_seleccionado=$_POST["u"];
$id_contacto=$_POST["id_contacto"];

                // Comprobamos si es nuevo
                if ($id_documento_seleccionado==0) {
                        $consulta_insert="INSERT INTO contactos_seguimiento (id_contacto,fecha,id_usuario) VALUES ('$id_contacto','".date("Y-m-d")."','$idUsuario')";
                        db_query($consulta_insert) or die(db_error());
                        $id_documento_seleccionado=db_insert_id();
                        }

                // Recibimos datos del formulario
                $comentarios=$_POST["comentarios"];

                        
if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query("UPDATE contactos_seguimiento SET comentarios='$comentarios', fecha_modificacion='".date("Y-m-d")."' WHERE id_nota=".$id_documento_seleccionado) or die(db_error()."-2");
        
        }
        

?>
<html>
<head>
<script Language="JavaScript">
self.opener.contactos.retorno.value=1;
self.opener.contactos.submit();
window.close();
</SCRIPT>
</head>
<body>
</body>
</html>

