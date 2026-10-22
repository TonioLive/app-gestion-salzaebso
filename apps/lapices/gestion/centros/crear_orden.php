<?php 
 require("../../configuracion/config.php");
 require("../../configuracion/autentifica.php");

$id_aula=$_GET["id_aula"];

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$busca_orden_en_aula=db_query("SELECT * FROM alumnos WHERE alumnos.aula_ayudas=$id_aula AND orden_en_aula=0");

if (db_num_rows($busca_orden_en_aula)!=0) {

        $busca_el_ultimo=db_query("SELECT * FROM alumnos WHERE alumnos.aula_ayudas=$id_aula AND orden_en_aula<>0 ORDER BY orden_en_aula DESC");
        
        if (db_num_rows($busca_el_ultimo)==0) {
                $contador_actual=1;
                } else {
                $datos_ultimo=db_fetch_array($busca_el_ultimo);
                $contador_actual=$datos_ultimo["orden_en_aula"]+1;
                }

        $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula_ayudas=$id_aula AND alumnos.orden_en_aula=0 ORDER BY Apellido1, Apellido2, Nombre");

        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
        if ($_POST["numerar_alumno_".$listado_alumnos["id_alumno"]]==1) {

                $fecha_baja=$listado_alumnos["fecha_baja"];

                if ($fecha_baja>=date("Y-m-d")) {

                        $actualiza_alumno=db_query("UPDATE alumnos SET orden_en_aula=$contador_actual WHERE id_alumno=".$listado_alumnos["id_alumno"]);
                
                        $contador_actual++;
                        }
                    }
                }

        }

db_close();

mt_srand((double)microtime()*1000000);
header("Location: "."listado_alumnos_ayudas.php?id_aula=".$id_aula."&ran=".(mt_rand(0,10000000)/10000000));
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>
