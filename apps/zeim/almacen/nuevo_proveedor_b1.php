<?php 
require("../configuracion/config.php");

$id_usuario_seleccionado=$_POST["u"];

                // Comprobamos si es nuevo
                if ($id_usuario_seleccionado==0) {
                        $consulta_insert=array('INSERT INTO proveedores (fechaalta) VALUES (?)', 's', array((string)(strftime("%Y/%m/%d"))));
                        db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
                        $id_usuario_seleccionado=db_insert_id();
                        }


                // Recibimos datos del formulario
                $NIF=$_POST["NIF"];
                $RAZON_SOCIAL=$_POST["RAZON_SOCIAL"];
                $nombre_comercial=$_POST["nombre_comercial"];
                $DOMICILIO=$_POST["DOMICILIO"];
                $LOCALIDAD=$_POST["LOCALIDAD"];
                $CP=$_POST["CP"];
                $TELEFONO1=$_POST["TELEFONO1"];
                $TELEFONO2=$_POST["TELEFONO2"];
                $MOVIL=$_POST["MOVIL"];
                $FAX=$_POST["FAX"];
                $EMAIL=$_POST["EMAIL"];
                $REGISTRO_SANITARIO=$_POST["REGISTRO_SANITARIO"];
                $OBSERVACIONES=$_POST["OBSERVACIONES"];
                $tipo_proveedor=$_POST["tipo_proveedor"];
                


if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query_params('UPDATE proveedores SET tipo_proveedor=?,nombre_comercial=?,FORMA_DE_PAGO=?, CUENTA_CARGO=?, CUENTA_BANCO=?, NIF=?, RAZON_SOCIAL=?, DOMICILIO=?, LOCALIDAD=?, CP=?, TELEFONO1=?, TELEFONO2=?, MOVIL=?, FAX=?, EMAIL=?, REGISTRO_SANITARIO=?, OBSERVACIONES=? WHERE ID_PROVEEDOR=?', 'ssssssssssssssssss', array((string)($tipo_proveedor), (string)($nombre_comercial), (string)($FORMA_DE_PAGO), (string)($CUENTA_CARGO), (string)($CUENTA_BANCO), (string)($NIF), (string)($RAZON_SOCIAL), (string)($DOMICILIO), (string)($LOCALIDAD), (string)($CP), (string)($TELEFONO1), (string)($TELEFONO2), (string)($MOVIL), (string)($FAX), (string)($EMAIL), (string)($REGISTRO_SANITARIO), (string)($OBSERVACIONES), $id_usuario_seleccionado)) or die(db_error()."-2");
        
        }
        

?>
<html>
<head>
<script Language="JavaScript">
self.opener.history.go(0);
window.close();
</SCRIPT>
</head>
<body>
</body>
</html>

