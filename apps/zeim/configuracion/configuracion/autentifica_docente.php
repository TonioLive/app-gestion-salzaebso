<?php 
if ($permitido!=true) { header("Location: ".$url_web."/configuracion/acceso_denegado.htm");}
if ($nivel_acceso<2) { header("Location: ".$url_web."/configuracion/acceso_denegado.htm");}
?>
