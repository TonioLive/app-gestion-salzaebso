<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$inicio_dia_origen=$_POST["inicio_dia_origen"];
$inicio_dia_destino=$_POST["inicio_dia_destino"];
$id_aula=$_POST["id_aula"];
$id_aula_destino=$_POST["id_aula_destino"];

$inicio_dia_origen=$inicio_dia_origen+7200;
$inicio_dia_destino=$inicio_dia_destino+7200;

$grupo_origen=$_POST["grupo_origen"];
$grupo_destino=$_POST["grupo_destino"];

if (strlen($grupo_origen)!=0) {
	$filtro_grupo_origen=" AND edad=$grupo_origen";
	$filtro_grupo_destino=" AND edad=$grupo_destino";
	} else {	
	$filtro_grupo_origen="";
	$filtro_grupo_destino="";
	}


$busca_actividades_dia=db_query("SELECT * FROM agenda_programacion WHERE id_aula=$id_aula AND fecha='".date("Y-m-d",$inicio_dia_origen)."'".$filtro_grupo_origen);

if (db_num_rows($busca_actividades_dia)!=0) {

// Borra actividades de esa dia
$borra_actividades_dia=db_query("DELETE FROM agenda_programacion WHERE id_aula=$id_aula_destino AND fecha='".date("Y-m-d",$inicio_dia_destino)."'".$filtro_grupo_destino);

while($datos_actividades_dia=db_fetch_array($busca_actividades_dia)) {

	if (strlen($grupo_destino)!=0) {
		$edad_actividad_copiada=$grupo_destino;
		} else{
		$edad_actividad_copiada=$datos_actividades_dia["edad"];
		}

	$inserta_actividades=db_query_params('INSERT INTO agenda_programacion (id_aula,fecha,tiempo_trabajo,area,id_actividad,actividad,metodologia,tiempo,materiales,lugar,evaluacion,orden_actividad,objetivo,edad,objetivos,turno) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);', 'ssssssssssssssss', array((string)($id_aula_destino), (string)(date("Y-m-d", $inicio_dia_destino)), (string)($datos_actividades_dia["tiempo_trabajo"]), (string)($datos_actividades_dia["area"]), (string)($datos_actividades_dia["id_actividad"]), (string)($datos_actividades_dia["actividad"]), (string)($datos_actividades_dia["metodologia"]), (string)($datos_actividades_dia["tiempo"]), (string)($datos_actividades_dia["materiales"]), (string)($datos_actividades_dia["lugar"]), (string)($datos_actividades_dia["evaluacion"]), (string)($datos_actividades_dia["orden_actividad"]), (string)($datos_actividades_dia["objetivo"]), (string)($edad_actividad_copiada), (string)($datos_actividades_dia["objetivos"]), (string)($datos_actividades_dia["turno"]))) or die(db_error());

	}             
                
} else {
$borra_actividades_dia=db_query("DELETE FROM agenda_programacion WHERE id_aula=$id_aula_destino AND fecha='".date("Y-m-d",$inicio_dia_destino)."'".$filtro_grupo_destino);
}

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
self.opener.history.go(0);
window.close();
</script>
</head>
<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>