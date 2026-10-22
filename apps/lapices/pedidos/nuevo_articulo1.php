<?php 
require("../configuracion/config.php");

$id_pedido=$_POST["id_pedido"];

                // Recibimos datos del formulario
                $id_material=$_POST["articulo"];
                $cantidad=$_POST["cantidad"];
                
db_query("INSERT INTO detalles_pedido_material (id_pedido, id_material, cantidad) VALUES ('$id_pedido', '$id_material', '$cantidad');") or die(db_error());

?>
<html>
<head>
<script Language="JavaScript">
self.opener.nuevo_pedido.submit();
window.close();
</SCRIPT>
</head>
<body>
</body>
</html>

