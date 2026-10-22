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
		$busca_centro_ip=db_query_params('SELECT * FROM centros WHERE id_centro=? AND ip_fija=0', 's', array($id_ip));
		if (db_num_rows($busca_centro_ip)!=0) {
			// Actualiza IP camaras
			$actualiza_ip_camaras=db_query_params('UPDATE webcam SET url_webcam=? WHERE id_centro=?', 'ss', array((string)($ip_actual), $id_ip));
			}
		}
	}

header("Location: /index.php");

?>
