<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");


$id_encuesta=$_POST["id_encuesta"];
$numero_hijos=$_POST["numero_hijos"];

for ($c_hijos=0;$c_hijos<$numero_hijos;$c_hijos++) {                        

    $id_hijo=$_POST["id_hijo_".$c_hijos];
    $opcion=$_POST["asiste_".$id_encuesta."_".$id_hijo];
    $horario=$_POST["horario_".$id_encuesta."_".$id_hijo];


    if (strlen($horario)!=0) {

        $horario=db_real_escape_string($horario);
            
    }

    $inserta_respuesta=db_query("INSERT INTO consultas_padres_respuestas (id_consulta,id_alumno,id_usuario,asistira,horario_deseado) VALUES ('$id_encuesta','$id_hijo','$id_usuario','$opcion','$horario');") or die(db_error());

}

header("Location: /webcam/menu.php?mensajeOK=1");

?>