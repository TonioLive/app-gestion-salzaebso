<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_fila=$_POST["id_fila"];
$observaciones=$_POST["observaciones"];

if ($id_fila!=0) {
        $busca_registro_comida=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id=?', 's', array($id_fila));

        if (db_num_rows($busca_registro_comida)!=0)
                {
                $datos_registro_comida=db_fetch_array($busca_registro_comida);

                // Datos
                $id_alumno=$datos_registro_comida["id_alumno"];
                $dia=$datos_registro_comida["dia"];
                $id_aula=$datos_registro_comida["id_aula"];


                // Actualiza
                
                $actualiza_regitro_comida=db_query_params('UPDATE agenda_alumnos_comidas SET observaciones_comida=? WHERE id=?', 'ss', array((string)($observaciones), $id_fila)) or die(db_error());
                

                }
        }

?>
<html>
<head>
<SCRIPT LANGUAGE=javascript>
<!--

self.opener.window.location.href='dia_aula.php?dia=<?=strtotime($dia)?>&id_aula=<?=$id_aula?>&pagina_activa=1';
window.close();

//-->
</SCRIPT>
</head>
<body>
</body>
</html>

