<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_tipo=$_POST["id_tipo"];
$nombre_tipo=$_POST["nombre_tipo"];
if (strlen($nombre_tipo)!=0) {
        $nombre_tipo=urlencode($nombre_tipo);
        }

        if ($id_tipo==0) {
	    $busca_el_ultimo=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo DESC");
	    
	    if (db_num_rows($busca_el_ultimo)!=0) {
		    
		    $datos_tipos=db_fetch_array($busca_el_ultimo);
		    $id_nuevo_tipo=$datos_tipos["id_tipo"]+1;
		    	
	    	} else {
		    
		    $id_nuevo_tipo=0;	
		    	
	    	}   
	        
        $inserta_tipo=db_query("INSERT INTO contactos_tipo (nombre_tipo, id_tipo) VALUES ('$nombre_tipo', '$id_nuevo_tipo')");
        $id_tipo=db_insert_id();
        } else {
        
        $nuevo_articulo = db_query("UPDATE contactos_tipo SET nombre_tipo='$nombre_tipo' WHERE id_tipo=$id_tipo") or die (db_error());
		}
        
        header("Location:listado_tipos.php");


?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

