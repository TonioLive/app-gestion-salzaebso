<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

// Recibimos los datos


$id_curso_grupo=$_POST["id_curso_grupo"];
if (strlen($id_curso_grupo)==0) {
        $id_curso_grupo=$_GET["id_curso_grupo"];
        }
        
$grupo=$_POST["grupo"];
if (strlen($grupo)==0) {
        $grupo=$_GET["grupo"];
        }
        
$alumno=$_POST["alumno"];
if (strlen($alumno)==0) {
        $alumno=$_GET["alumno"];
        }
        

$datos_grupo=explode("_",$grupo);
$id_grupo=$datos_grupo[2];
$nivel_actual=$datos_grupo[1];

$datos_alumno=explode("_",$alumno);
$id_alumno=$datos_alumno[1];
$estado_anterior=$datos_alumno[2];

if ($datos_grupo[0]=="grupo") {

// Busca nivel alumno
$busca_datos_alumno=db_query_params('SELECT * FROM gestionguarderias.alumnos WHERE id_alumno=?', 's', array($id_alumno));

$datos_alumno=db_fetch_array($busca_datos_alumno);

$nivel_anterior=$datos_alumno["nivel_actual"];

if ($nivel_anterior!=intval($nivel_actual)) {
        // Cambia nivel alumno
        // mysql_query("UPDATE gestionguarderias.alumnos SET nivel_actual=$nivel_actual WHERE id_alumno=$id_alumno");
        }

if (intval($estado_anterior)!=0) {
        db_query("DELETE FROM aulas_alumnos_lapices_grupos WHERE id_alumno_grupo=".$estado_anterior);
        }

// busca si está
$busca_en_grupo=db_query_params('SELECT * FROM aulas_alumnos_lapices_grupos WHERE id_alumno=? AND id_grupo=? AND id_curso=?', 'sss', array($id_alumno, $id_grupo, $id_curso_grupo));

if (db_num_rows($busca_en_grupo)==0) {
	db_query_params('INSERT INTO aulas_alumnos_lapices_grupos (id_alumno,id_grupo,id_curso,id_subgrupo) VALUES (?,?,?,?)', 'ssss', array((string)($id_alumno), (string)($id_grupo), (string)($id_curso_grupo), (string)($nivel_actual)));
	} else {
	// CAMBIA DE SUBGRUPO
	$datos_grupo_actual=db_fetch_array($busca_en_grupo);	
	
	$actualiza=db_query("UPDATE aulas_alumnos_lapices_grupos SET id_subgrupo=$nivel_actual WHERE id_alumno_grupo=".$datos_grupo_actual["id_alumno_grupo"]);
	
	}

} else {

// borra del grupo actual

if (intval($estado_anterior)!=0) {
        db_query("DELETE FROM aulas_alumnos_lapices_grupos WHERE id_alumno_grupo=".$estado_anterior);
        }

}

?>
