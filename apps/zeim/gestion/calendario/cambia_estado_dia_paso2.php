<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$dia=$_GET["dia"];
$curso=$_GET["curso"];
$centro=$_GET["centro"];

        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query("SELECT * FROM calendario_laboral WHERE id_curso=$curso AND id_centro=$centro AND dia='".date("Y-m-d",$dia)."'") or die(db_error());
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                echo 1;
                if ($datos_dia_en_calendario["estado"]!=0) {
                        echo 2;
                        $cambia_a_0=db_query_params('UPDATE calendario_laboral SET estado=0 WHERE id_dia=?', 's', array($datos_dia_en_calendario["id_dia"])) or die(db_error());
                        } else {
                        echo 3;
                        $cambia_a_1=db_query_params('UPDATE calendario_laboral SET estado=1 WHERE id_dia=?', 's', array($datos_dia_en_calendario["id_dia"])) or die(db_error());
                        }
                } else {
                echo 4;
                $intro_0=db_query_params('INSERT INTO calendario_laboral (id_curso,id_centro,dia,estado) VALUES (?,?,?,1);', 'sss', array((string)($curso), (string)($centro), (string)(date("Y-m-d", $dia)))) or die(db_error());
                }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<script language="Javascript">
self.opener.history.go(0);
self.close();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

