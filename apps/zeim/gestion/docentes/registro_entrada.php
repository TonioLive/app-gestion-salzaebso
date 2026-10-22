<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos
$turno=$_GET["turno"];
$id_centro_asistencia=$_GET["id_centro_asistencia"];

$fecha=date("Y-m-d");
$hora_entrada=date("H:i:s");

// Busca info IP
$info_e="";
if (strlen($REMOTE_ADDR)!=0) {
        $info_e="Desde IP: $REMOTE_ADDR\n";
        include("../../inc/ipology.class.php");
        $ipology = new ipology(array($REMOTE_ADDR));
        $out = $ipology->out();
        foreach($out as $ip)
        {
            if(is_array($ip))
            {
                $info_e=$info_e."Block: " . $ip['inetnum'] . "\n";
                $info_e=$info_e."Country: " . $ip['country'] . "\n";
                $info_e=$info_e."Netname: " . $ip['netname'] . "\n";
                $info_e=$info_e."Person: " . implode(", ", (array) $ip['person']) . "\n";
            }
            else
            {
                // echo "ERR: $ip\n------------------------------\n";
            }
        }
        }


if (strlen($id_centro_asistencia)!=0) {
        db_query_params('INSERT INTO asistencia_docentes (id_usuario,turno,fecha,hora_entrada,id_centro_asistencia,sesion_e,info_e) VALUES (?,?,?,?,?,?,?)', 'sssssss', array((string)($idUsuario), (string)($turno), (string)($fecha), (string)($hora_entrada), (string)($id_centro_asistencia), (string)(session_id()), (string)($info_e))) or die(db_error());
        } else {
        db_query_params('INSERT INTO asistencia_docentes (id_usuario,turno,fecha,hora_entrada,sesion_e,info_e) VALUES (?,?,?,?,?,?)', 'ssssss', array((string)($idUsuario), (string)($turno), (string)($fecha), (string)($hora_entrada), (string)(session_id()), (string)($info_e))) or die(db_error());
        }

header("Location: /index.php");

?>
