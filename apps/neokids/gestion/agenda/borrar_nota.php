<?php 
require("../../configuracion/config.php");

$id_nota=$_GET["id_nota"];

if (strlen($id_nota)!=0) {

        // busca datos nota
        $busca_nota=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE id=?', 's', array($id_nota));

        $datos_nota=db_fetch_array($busca_nota);
        
        $id_aula=$datos_nota["id_aula"];
        $dia=strtotime($datos_nota["dia"]);
        $id_alumno=$datos_nota["id_alumno"];
        $id_area=$datos_nota["area"];

        // Actualiza
        
        $update_nota=db_query_params('DELETE FROM agenda_alumnos_seguimiento WHERE id=?', 's', array($id_nota));

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
