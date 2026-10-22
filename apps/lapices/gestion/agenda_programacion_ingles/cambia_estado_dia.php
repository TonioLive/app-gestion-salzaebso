<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$dia=$_GET["dia"];
$id_aula=$_GET["id_aula"];
$id_plantilla=$_GET["id_plantilla"];

        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query("SELECT * FROM agenda_programacion_ingles_calendario WHERE id_aula=$id_aula AND id_programacion=$id_plantilla AND dia='".date("Y-m-d",$dia)."'") or die(db_error());
        if (db_num_rows($busca_dia_en_calendario)!=0) {
        
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
        
                if ($datos_dia_en_calendario["estado"]!=0) {
                        $estado_final=0;
                        } else {
                        $estado_final=1;
                        }

                $cambia_a_0=db_query("UPDATE agenda_programacion_ingles_calendario SET estado=$estado_final WHERE id_aula=$id_aula AND id_programacion=$id_plantilla AND dia='".date("Y-m-d",$dia)."'") or die(db_error());

                } else {

					$intro_0=db_query("INSERT INTO agenda_programacion_ingles_calendario (id_aula,id_programacion,dia,estado) VALUES ('$id_aula','".$id_plantilla."','".date("Y-m-d",$dia)."',1);") or die(db_error());

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

