<?php 
require("../../configuracion/config.php");

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
                        }

if ($num_errores==0) {
        // Introduce informaci¨®n
        // Introduce datos personales
        db_query_params('UPDATE agenda_alumnos_seguimiento SET hora=?, nota=?, avisar=?,tipo_nota=? WHERE id=?', 'sssss', array((string)($hora_nota), (string)($nota), (string)($avisar), (string)($tipo_nota), $id_nota)) or die(db_error()."-2");

        }

switch($id_area){

	case 1: 
		$pagina_Destino=1;
		break;
	
	case 3:
		$pagina_Destino=2;
		break;

	}

if ($id_alumno!=0) {
        header("Location: editar_notas.php?id_alumno=$id_alumno&dia=".$dia."&id_grupo=$id_aula&area=$id_area");
        } else {

        header("Location: dia_aula.php?dia=$dia&id_grupo=$id_aula&pagina_activa=".$pagina_Destino);
        }
?>
