<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$id_registro=$_GET["id_registro"];

$hora_salida=date("H:i:s");

// Busca info IP
$info_s="";
if (strlen($REMOTE_ADDR)!=0) {
        $info_s="Desde IP: $REMOTE_ADDR\n";
        include("../../inc/ipology.class.php");
        $ipology = new ipology(array($REMOTE_ADDR));
        $out = $ipology->out();
        foreach($out as $ip)
        {
            if(is_array($ip))
            {
                $info_s=$info_s."Block: " . $ip['inetnum'] . "\n";
                $info_s=$info_s."Country: " . $ip['country'] . "\n";
                $info_s=$info_s."Netname: " . $ip['netname'] . "\n";
                $info_s=$info_s."Person: " . implode(", ", (array) $ip['person']) . "\n";
            }
            else
            {
                // echo "ERR: $ip\n------------------------------\n";
            }
        }
        }

db_query_params('UPDATE asistencia_docentes SET hora_salida=?,sesion_s=?,info_s=? WHERE id_asistencia=?', 'ssss', array((string)($hora_salida), (string)(session_id()), (string)($info_s), $id_registro)) or die(db_error());

$retorno=$_GET["retorno"];
if ($retorno!=1) {
	$url_retorno="index.php";
	} else {
	$url_retorno="control_asistencia.php";
	}

header("Location: /$url_retorno");
?>
