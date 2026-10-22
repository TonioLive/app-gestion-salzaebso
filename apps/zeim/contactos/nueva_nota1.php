<?php 
require("../configuracion/config.php");

$id_documento_seleccionado=$_POST["u"];
$id_contacto=$_POST["id_contacto"];

                // Comprobamos si es nuevo
                if ($id_documento_seleccionado==0) {
                        $consulta_insert=array('INSERT INTO contactos_seguimiento (id_contacto,fecha,id_usuario) VALUES (?,?,?)', 'sss', array((string)($id_contacto), (string)(date("Y-m-d")), (string)($idUsuario)));
                        db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
                        $id_documento_seleccionado=db_insert_id();
                        }

                // Recibimos datos del formulario
                $comentarios=$_POST["comentarios"];

                        
if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query_params('UPDATE contactos_seguimiento SET comentarios=?, fecha_modificacion=? WHERE id_nota=?', 'sss', array((string)($comentarios), (string)(date("Y-m-d")), $id_documento_seleccionado)) or die(db_error()."-2");
        
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

