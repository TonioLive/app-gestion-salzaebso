<?php 
if ($_SERVER["REMOTE_ADDR"]!="10.10.1.12") { header("Location: ".$url_web."/configuracion/acceso_denegado.htm");}
?>
