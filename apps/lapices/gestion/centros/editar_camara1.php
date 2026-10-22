<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/autentifica_admin.php");

$id_camara=$_GET["id_camara"] ;
if (strlen($id_camara)==0) {
        $id_camara=$_POST["id_camara"];
        }

$url_retorno="listado_camaras.php";

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

                $id_aula_webcam=$_POST["id_aula_webcam"];

		    if (strlen($id_aula_webcam)==0) {
			$id_aula_webcam=0;
			}

                if (substr($id_aula_webcam,0,1)=="C") {
                        $id_centro_webcam=substr($id_aula_webcam,1,(strlen($id_aula_webcam)-1));
                        $id_aula_webcam=0;
                        } else {
                        $busca_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula_webcam");
                        if (db_num_rows($busca_aula)!=0) {
                                $datos_aula=db_fetch_array($busca_aula);
                                $id_centro_webcam=$datos_aula["id_centro"];
                                } else {
                                $id_centro_webcam=0;
                                }
                        }

                $nombre_webcam=$_POST["nombre_webcam"];
                $modelo_webcam=$_POST["modelo_webcam"];
                $url_webcam=$_POST["url_webcam"];
                $puerto_webcam=$_POST["puerto_webcam"];
                $tipo_webcam=$_POST["tipo_webcam"];
                $ip_local=$_POST["ip_local"];
                
                $hora_conexion_webcam=$_POST["hora_e"].":".$_POST["min_e"].":00";
                $hora_desconexion_webcam=$_POST["hora_s"].":".$_POST["min_s"].":00";;
                
                $usuario_visor_webcam=$_POST["usuario_visor_webcam"];
                $password_visor_webcam=$_POST["password_visor_webcam"];

                if (strlen($id_camara)==0) {
                        // Introduce nueva camara
                        db_query("INSERT INTO webcam (modelo) VALUES ('0');") or die(db_error());
                        $id_camara=db_insert_id();
                        }

                db_query("UPDATE webcam SET id_centro='$id_centro_webcam',id_aula='$id_aula_webcam',nombre_webcam='$nombre_webcam',url_webcam='$url_webcam',puerto_webcam='$puerto_webcam',tipo='$tipo_webcam',hora_conexion='$hora_conexion_webcam',hora_desconexion='$hora_desconexion_webcam',usuario_visor='$usuario_visor_webcam',password_visor='$password_visor_webcam', modelo='$modelo_webcam', ip_local='$ip_local' WHERE id_webcam=$id_camara;") or die(db_error());
                }
                
header("Location: $url_retorno");
?>
