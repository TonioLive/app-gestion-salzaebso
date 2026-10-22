<?php 
require("configuracion/config.php");

$id_ip=$_GET["id_ip"];

if (strlen($id_ip)==0) {
	$id_ip=0;
	}

if ($id_ip!=0) { 
	$ip_actual=$REMOTE_ADDR;
	if (strlen($ip_actual)!=0) {
		// busca datos centro
		$busca_centro_ip=db_query("SELECT * FROM centros WHERE id_centro=$id_ip AND ip_fija=0");
		if (db_num_rows($busca_centro_ip)!=0) {
			// Actualiza IP camaras
			$actualiza_ip_camaras=db_query("UPDATE webcam SET url_webcam='$ip_actual' WHERE id_centro=$id_ip");
			}
		}
	}

header("Location: /index.php");

?>
