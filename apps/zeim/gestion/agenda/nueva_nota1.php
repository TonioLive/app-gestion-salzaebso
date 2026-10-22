<?php 
require("../../configuracion/config.php");

$lista_ventanas=array(1,3,4,5);

$id_aula=$_POST["id_aula"];
$dia=$_POST["dia"];
$id_alumno=$_POST["id_alumno"];
$id_area=$_POST["area"];

$id_nota=$_POST["id_nota"];

$hora=$_POST["hora"];
$min=$_POST["min"];

$hora_nota=$hora.":".$min.":00";
$nota=$_POST["nota"];
$avisar=$_POST["avisar"];
$tipo_nota=$_POST["tipo_nota"];


                // Comprobamos si es nuevo
                if ($id_nota==0) {
                        $consulta_insert=array('INSERT INTO agenda_alumnos_seguimiento (id_aula,dia,id_alumno,area,introducida_por) VALUES (?,?,?,?,?)', 'sssss', array((string)($id_aula), (string)(date("Y-m-d", $dia)), (string)($id_alumno), (string)($id_area), (string)($idUsuario)));
                        db_query_params($consulta_insert[0], $consulta_insert[1], $consulta_insert[2]) or die(db_error());
                        $id_nota=db_insert_id();

                        // Inserta notificaciones app movil
                        if ($id_alumno!=0) {
                                /* De momento esto no
                                // Busca padres
                                $padresAlumno = mysql_query("SELECT * FROM padres WHERE padre_de=".$id_alumno.";");
                                while($datosPadres=mysql_fetch_array($padresAlumno)) {

                                        // Busca TOKENS
                                        $buscaTokens = mysql_query("SELECT * FROM tokens_fcm WHERE id_usuario=".$datosPadres['id_usuario']." GROUP BY FCMtoken;");
                                        if (mysql_num_rows($buscaTokens)!=0) {
                                                // Inserta Notificación
                                                $nuevaNotificacion = mysql_query("INSERT INTO notificaciones_app (fecha_envio,destino,tipo,titulo,mensaje,enviada,id_nota_seguimiento) VALUES ('".date('Y-m-d')."','".$datosPadres['id_usuario']."','token','Escuela Infantil','La educadora ha introducido una nueva nota.',0,'".$id_nota."');");
                                                if ($id_alumno==10200) {
                                                        exit("SI");                
                                                }
                                        }

                                }
                                */
                        } else {
                                // Inserta Notificación
                                $nuevaNotificacion = db_query_params('INSERT INTO notificaciones_app (fecha_envio,destino,tipo,titulo,mensaje,enviada,id_nota_seguimiento) VALUES (?,?,\'aula\',\'Escuela Infantil\',\'La educadora ha introducido una nueva nota.\',0,?);', 'sss', array((string)(date('Y-m-d')), (string)($id_aula), (string)($id_nota)));
                        }    

                        }

if ($num_errores==0) {
        // Introduce información
        // Introduce datos personales
        db_query_params('UPDATE agenda_alumnos_seguimiento SET hora=?, nota=?, avisar=?,tipo_nota=? WHERE id=?', 'sssss', array((string)($hora_nota), (string)($nota), (string)($avisar), (string)($tipo_nota), $id_nota)) or die(db_error()."-2");
        }

if ($id_alumno!=0) {
        header("Location: editar_notas.php?id_alumno=$id_alumno&dia=".$dia."&id_aula=$id_aula&area=$id_area");
        } else {

        header("Location: dia_aula.php?dia=$dia&id_aula=$id_aula&pagina_activa=".$lista_ventanas[$id_area]);
        }
?>
