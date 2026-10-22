<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$dia=$_GET["dia"];
$id_alumno=$_GET["id_alumno"];
$estado_inicial=$_GET["estado_inicial"];

if ($estado_inicial=="true") {
        $estado_inicial=1;
        } else {
        $estado_inicial=0;
        }

        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query_params('SELECT * FROM horario_alumnos_dias WHERE id_usuario=? AND fecha=?', 'ss', array($id_alumno, (string)(date("Y-m-d", $dia)))) or die(db_error());
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["no_comedor"]!=0) {
                        $cambia_a_0=db_query_params('UPDATE horario_alumnos_dias SET no_comedor=0 WHERE id_horario=?', 's', array($datos_dia_en_calendario["id_horario"])) or die(db_error());
                        } else {
                        $cambia_a_1=db_query_params('UPDATE horario_alumnos_dias SET no_comedor=1 WHERE id_horario=?', 's', array($datos_dia_en_calendario["id_horario"])) or die(db_error());
                        }
                } else {
                $intro_0=db_query_params('INSERT INTO horario_alumnos_dias (id_usuario,fecha,no_asistencia,no_comedor) VALUES (?,?,\'0\',?);', 'sss', array((string)($id_alumno), (string)(date("Y-m-d", $dia)), (string)($estado_inicial))) or die(db_error());
                }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<script language="Javascript">
// self.opener.history.go(0);
self.close();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

