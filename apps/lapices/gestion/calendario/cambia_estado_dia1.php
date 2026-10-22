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

$no_asistencia_comedor=0;

// Busca datos alumno

$busca_datos_alumno=db_query("SELECT usuarios.*,alumnos.*,alumnos.id_centro AS centro_asistencia FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=$id_alumno LIMIT 0,1") or die(db_error());

if (db_num_rows($busca_datos_alumno)!=0) {
$datos_alumno=db_fetch_array($busca_datos_alumno);

$comedor_alumno=$datos_alumno["comedor"];
if ($comedor_alumno==2) {
        $no_asistencia_comedor=1;
        }
}
        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query("SELECT * FROM horario_alumnos_dias WHERE id_usuario=$id_alumno AND fecha='".date("Y-m-d",$dia)."'") or die(db_error());
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["no_asistencia"]!=0) {
                        $cambia_a_0=db_query("UPDATE horario_alumnos_dias SET no_asistencia=0 WHERE id_horario=".$datos_dia_en_calendario["id_horario"]) or die(db_error());
                        } else {
                        $cambia_a_1=db_query("UPDATE horario_alumnos_dias SET no_asistencia=1 WHERE id_horario=".$datos_dia_en_calendario["id_horario"]) or die(db_error());
                        }
                } else {
                $intro_0=db_query("INSERT INTO horario_alumnos_dias (id_usuario,fecha,no_asistencia,no_comedor) VALUES ('$id_alumno','".date("Y-m-d",$dia)."','$estado_inicial','$no_asistencia_comedor');") or die(db_error());
                }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<script language="Javascript">
// self.opener.history.go(0);
self.close();
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>

