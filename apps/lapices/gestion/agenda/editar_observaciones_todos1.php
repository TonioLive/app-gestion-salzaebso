<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_POST["id_aula"];
$dia=$_POST["dia"];
$observaciones=$_POST["observaciones"];

if ($id_aula!=0) {
        $busca_registro_comida=db_query("SELECT * FROM agenda_alumnos_comidas WHERE id_aula=$id_aula AND dia='".date("Y-m-d",$dia)."'") or die(db_error());

        if (db_num_rows($busca_registro_comida)!=0)
                {
                while($datos_registro_comida=db_fetch_array($busca_registro_comida)) {

                        // Datos
                        $observaciones_actuales=$datos_registro_comida["observaciones_comida"]."\n".$observaciones;
                        $id_fila=$datos_registro_comida["id"];

                        // Actualiza
                
                        $actualiza_regitro_comida=db_query("UPDATE agenda_alumnos_comidas SET observaciones_comida='".db_real_escape_string($observaciones_actuales)."' WHERE id=$id_fila") or die(db_error());
                }
                

                }
        }

?>
<html>
<head>
<SCRIPT LANGUAGE=javascript>
<!--

self.opener.window.location.href='dia_aula.php?dia=<?=$dia?>&id_aula=<?=$id_aula?>&pagina_activa=1';
window.close();

//-->
</SCRIPT>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
</body>
</html>

