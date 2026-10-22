<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_aula=$_POST["id_aula"];
$dia=$_POST["dia"];
$observaciones=$_POST["observaciones"];

if ($id_aula!=0) {
        $busca_registro_comida=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id_aula=? AND dia=?', 'ss', array($id_aula, (string)(date("Y-m-d", $dia)))) or die(db_error());

        if (db_num_rows($busca_registro_comida)!=0)
                {
                while($datos_registro_comida=db_fetch_array($busca_registro_comida)) {

                        // Datos
                        $observaciones_actuales=$datos_registro_comida["observaciones_comida"]."\n".$observaciones;
                        $id_fila=$datos_registro_comida["id"];

                        // Actualiza
                
                        $actualiza_regitro_comida=db_query_params('UPDATE agenda_alumnos_comidas SET observaciones_comida=? WHERE id=?', 'ss', array((string)($observaciones_actuales), $id_fila)) or die(db_error());
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
</head>
<body>
</body>
</html>

