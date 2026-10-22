<?php  require("../../configuracion/config.php"); ?>
<?php 

$id_aula=$_POST["id_aula"];
$id_curso=$_POST["id_curso"];
$o=$_POST["o"];
if (strlen($o)==0) {
        $url_retorno="editar_aula.php?id_aula=".$id_aula;
        } else {
        $url_retorno=$o;
        }

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

                        $nombre_curso=$_POST["nombre_curso"];
                        
                        $fecha_inicio_dia=$_POST["fecha_inicio_dia"];
                        $fecha_inicio_mes=$_POST["fecha_inicio_mes"];
                        $fecha_inicio_ano=$_POST["fecha_inicio_ano"];
                        $fecha_inicio=$fecha_inicio_ano."/".$fecha_inicio_mes."/".$fecha_inicio_dia;
                        
                        $fecha_fin_dia=$_POST["fecha_fin_dia"];
                        $fecha_fin_mes=$_POST["fecha_fin_mes"];
                        $fecha_fin_ano=$_POST["fecha_fin_ano"];
                        $fecha_fin=$fecha_fin_ano."/".$fecha_fin_mes."/".$fecha_fin_dia;

                        $fecha_limite_dia=$_POST["fecha_limite_dia"];
                        $fecha_limite_mes=$_POST["fecha_limite_mes"];
                        $fecha_limite_ano=$_POST["fecha_limite_ano"];
                        $fecha_limite=$fecha_limite_ano."/".$fecha_limite_mes."/".$fecha_limite_dia;

                        $estado=$_POST["estado"];

                        $comprobar_inscripcion=$_POST["comprobar_inscripcion"];
                        $comprobar_cuotas=$_POST["comprobar_cuotas"];

                        $dia_fin_pedidos=$_POST["dia_fin_pedidos"];
                        
                        if (strlen($id_curso)!=0) {
                                        // Guarda cambios
                                        db_query("UPDATE cursos_academicos SET dia_fin_pedidos='$dia_fin_pedidos', comprobar_inscripcion='$comprobar_inscripcion', comprobar_cuotas='$comprobar_cuotas',nombre='$nombre_curso', fecha_inicio='$fecha_inicio', fecha_fin='$fecha_fin', estado='$estado', fecha_limite_presentacion='$fecha_limite' WHERE id_curso=$id_curso;") or die(db_error());
                                } else {
                                        // Introduce nuevo centro
                                        db_query("INSERT INTO cursos_academicos (nombre, fecha_inicio, fecha_fin, estado, comprobar_inscripcion, comprobar_cuotas,fecha_limite_presentacion,dia_fin_pedidos) VALUES ('$nombre_curso', '$fecha_inicio', '$fecha_fin', '$estado', '$comprobar_inscripcion', '$comprobar_cuotas','$fecha_limite','$dia_fin_pedidos');") or die(db_error());
                                        $id_aula=db_insert_id();
                                }
                        }
        
header("Location: ".$url_retorno);
?>
