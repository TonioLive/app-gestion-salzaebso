<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$errores="";

$idcita=$_POST["id_cita"];
$nivel=$_POST["nivel"];


$db_cc=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db_cc);


// Borra todo menos YO
db_query("DELETE FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario Is Null");

// Añade asistentes

$busca_alumnos=db_query("SELECT * FROM usuarios WHERE nivel_acceso=$nivel ORDER BY id_centro, Apellido1, Apellido2, Nombre");

while($listado_alumnos=db_fetch_array($busca_alumnos)){

                $asiste=$_POST["asistentes_".$listado_alumnos["id_usuario"]];

                if ($asiste==1) {
                $busca_si_esta=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario=".$listado_alumnos["id_usuario"]." LIMIT 0,1");
                if (db_num_rows($busca_si_esta)==0) {
                        db_query("INSERT INTO agenda_asistentes (id_cita,id_usuario) VALUES ('$idcita','".$listado_alumnos["id_usuario"]."');");
                        }
                } else {
                db_query("DELETE FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario=".$listado_alumnos["id_usuario"]);
                }

                }

// YO
$busca_si_estoy=db_query("SELECT * FROM agenda_asistentes WHERE id_cita=$idcita AND id_usuario=$idUsuario LIMIT 0,1");
if (db_num_rows($busca_si_estoy)==0) {
        db_query("INSERT INTO agenda_asistentes (id_cita,id_usuario) VALUES ('$idcita','$idUsuario');");
        }
        

// Los demás


db_close($db_cc);

?>
<script language="Javascript">
window.close();
</script>
