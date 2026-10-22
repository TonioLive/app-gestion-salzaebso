<?php  require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_cuestionario=$_POST["id_cuestionario"];
$id_alumno=$_POST["id_alumno"];
$accion_despues=$_POST["accion_despues"];
$tipo_cuestionario=$_POST["tipo_cuestionario"];
                
$fecha_dia=$_POST["fecha_dia"];
$fecha_mes=$_POST["fecha_mes"];
$fecha_ano=$_POST["fecha_ano"];
$fecha=$fecha_ano."-".$fecha_mes."-".$fecha_dia;

$actualiza_brunet=db_query("UPDATE historial_educativo_cuestionarios$tipo_cuestionario SET fecha_historial='$fecha' WHERE id_cuestionario=$id_cuestionario") or die(db_error()."-1");

// Inserta resultados

// Busca preguntas cuestionario
$busca_cuestiones=db_query("SELECT * FROM historial_educativo_cuestiones$tipo_cuestionario ORDER BY orden;");
          
$consulta_update="UPDATE historial_educativo_cuestionarios$tipo_cuestionario SET ";          
                        
while($cuestiones=db_fetch_array($busca_cuestiones)) {
                        
		$dato_recibido=$_POST[$cuestiones["campo_db"]];
        
        $consulta_update.=$cuestiones["campo_db"]."='".$dato_recibido."', ";
                        
		}

$consulta_update=substr($consulta_update,0,strlen($consulta_update)-2);
  
$consulta_update.=" WHERE id_cuestionario=$id_cuestionario"; 

db_query($consulta_update);                     

$url_retorno="/gestion/gabinete/seguimiento.php?id_alumno=".$id_alumno;
                                      
header("Location: ".$url_retorno);
?>