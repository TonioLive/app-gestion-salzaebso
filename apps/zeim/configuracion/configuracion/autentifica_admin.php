<?php 
if ($permitido!=true) { header("Location: ".$url_web."/configuracion/acceso_denegado.htm");}
if ($nivel_acceso<3) { header("Location: ".$url_web."/configuracion/acceso_denegado.htm");}
if ($nivel_acceso>=10) { header("Location: ".$url_web."/configuracion/acceso_denegado.htm");}
?>
