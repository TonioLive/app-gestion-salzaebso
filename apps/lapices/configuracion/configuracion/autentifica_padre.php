<?php 
if ($_SESSION['permitido_padre']!=true) { header("Location: ".$url_web."/configuracion/acceso_denegado_padre.htm");}
?>
