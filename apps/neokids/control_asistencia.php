<?php 

$ip_segura=false;

$REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];

if (substr($REMOTE_ADDR,0,8)=="10.10.1.") {
        $ip_segura=true;
        }
if ($REMOTE_ADDR=="62.43.225.70") {
        $ip_segura=true;
        }
if ($REMOTE_ADDR=="88.26.220.146") {
        $ip_segura=true;
        }
        
if ($ip_segura==false) {
        header("Location: /configuracion/acceso_denegado.htm");
        }        

require("configuracion/parte_superior_sin_login_ancho_publica.php"); ?>
    <div align="center"><?php  require("inc/control_asistencia.php"); ?></div>
<?php  require("configuracion/parte_inferior.php"); ?>
