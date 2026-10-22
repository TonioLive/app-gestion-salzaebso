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

                if (substr($id_aula_webcam,0,1)=="C") {
                        $id_centro_webcam=substr($id_aula_webcam,1,(strlen($id_aula_webcam)-1));
                        $id_aula_webcam=0;
                        } else {
                        $busca_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula_webcam));
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

                db_query_params('UPDATE webcam SET id_centro=?,id_aula=?,nombre_webcam=?,url_webcam=?,puerto_webcam=?,tipo=?,hora_conexion=?,hora_desconexion=?,usuario_visor=?,password_visor=?, modelo=?, ip_local=? WHERE id_webcam=?;', 'sssssssssssss', array((string)($id_centro_webcam), (string)($id_aula_webcam), (string)($nombre_webcam), (string)($url_webcam), (string)($puerto_webcam), (string)($tipo_webcam), (string)($hora_conexion_webcam), (string)($hora_desconexion_webcam), (string)($usuario_visor_webcam), (string)($password_visor_webcam), (string)($modelo_webcam), (string)($ip_local), $id_camara)) or die(db_error());
                }
                
header("Location: $url_retorno");
?>
