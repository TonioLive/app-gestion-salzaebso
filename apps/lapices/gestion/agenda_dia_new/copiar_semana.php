<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$inicio_semana_origen=$_POST["inicio_semana_origen"];
$inicio_semana_destino=$_POST["inicio_semana_destino"];
$id_aula=$_POST["id_aula"];
$id_aula_destino=$_POST["id_aula_destino"];

$inicio_semana_origen=$inicio_semana_origen+7200;
$inicio_semana_destino=$inicio_semana_destino+7200;
$incremento=86400;

$grupo_origen=$_POST["grupo_origen"];
$grupo_destino=$_POST["grupo_destino"];

if (strlen($grupo_origen)!=0) {
	$filtro_grupo_origen=" AND edad=$grupo_origen";
	$filtro_grupo_destino=" AND edad=$grupo_destino";
	} else {	
	$filtro_grupo_origen="";
	$filtro_grupo_destino="";
	}

for ($contador_repeticiones=0;$contador_repeticiones<7;$contador_repeticiones++) {

$busca_actividades_dia=db_query("SELECT * FROM agenda_programacion WHERE id_aula=$id_aula AND fecha='".date("Y-m-d",$inicio_semana_origen)."'".$filtro_grupo_origen);

if (db_num_rows($busca_actividades_dia)!=0) {

// Borra actividades de esa semana
$borra_actividades_dia=db_query("DELETE FROM agenda_programacion WHERE id_aula=$id_aula_destino AND fecha='".date("Y-m-d",$inicio_semana_destino)."'".$filtro_grupo_destino);

while($datos_actividades_dia=db_fetch_array($busca_actividades_dia)) {

	if (strlen($grupo_destino)!=0) {
		$edad_actividad_copiada=$grupo_destino;
		} else{
		$edad_actividad_copiada=$datos_actividades_dia["edad"];
		}
		
	$inserta_actividades=db_query("INSERT INTO agenda_programacion (id_aula,fecha,tiempo_trabajo,area,id_actividad,actividad,metodologia,tiempo,materiales,lugar,evaluacion,orden_actividad,objetivo,edad,objetivos,turno) VALUES ('".$id_aula_destino."','".date("Y-m-d",$inicio_semana_destino)."','".$datos_actividades_dia["tiempo_trabajo"]."','".$datos_actividades_dia["area"]."','".$datos_actividades_dia["id_actividad"]."','".db_real_escape_string($datos_actividades_dia["actividad"])."','".db_real_escape_string($datos_actividades_dia["metodologia"])."','".$datos_actividades_dia["tiempo"]."','".db_real_escape_string($datos_actividades_dia["materiales"])."','".db_real_escape_string($datos_actividades_dia["lugar"])."','".db_real_escape_string($datos_actividades_dia["evaluacion"])."','".$datos_actividades_dia["orden_actividad"]."','".$datos_actividades_dia["objetivo"]."','".$edad_actividad_copiada."','".$datos_actividades_dia["objetivos"]."','".$datos_actividades_dia["turno"]."');") or die(db_error());

	}             
                
} else {
$borra_actividades_dia=db_query("DELETE FROM agenda_programacion WHERE id_aula=$id_aula_destino AND fecha='".date("Y-m-d",$inicio_semana_destino)."'".$filtro_grupo_destino);
}

$inicio_semana_destino=$inicio_semana_destino+$incremento;
$inicio_semana_origen=$inicio_semana_origen+$incremento;
}

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<SCRIPT LANGUAGE=javascript>
self.opener.history.go(0);
window.close();
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
</body>
</html>