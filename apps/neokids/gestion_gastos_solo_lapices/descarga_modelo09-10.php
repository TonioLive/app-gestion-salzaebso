<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("../configuracion/downloadfile.php");


$dFile = new Download_file("/var/descarga" , "JUSTIF_2C_2009-10.rtf");

?>
