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
        
                if ($datos_dia_en_calendario["estado"]!=0) {
                        $estado_final=0;
                        } else {
                        $estado_final=1;
                        }
                echo 1;
                $cambia_a_0=db_query("UPDATE calendario_laboral SET estado=$estado_final WHERE id_curso=$curso AND dia='".date("Y-m-d",$dia)."'") or die(db_error());

                } else {
                echo 4;
                // Busca centros
                $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                        $intro_0=db_query("INSERT INTO calendario_laboral (id_curso,id_centro,dia,estado) VALUES ('$curso','".$centros["id_centro"]."','".date("Y-m-d",$dia)."',1);") or die(db_error());
                        }
                }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<script language="Javascript">
self.opener.history.go(0);
self.close();
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

