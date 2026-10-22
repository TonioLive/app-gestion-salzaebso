<?php 

require("../configuracion/parte_superior_sin_login_ancho_bootstrap.php");

$id_escuela=$_GET["id_escuela"];

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso=$datos_curso_actual["id_curso"];
                } else {
                $id_curso=0;
                }                

                $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($id_escuela));
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro=$datos_centro["nombre_centro"];
                        }

// $busca_alumnos_escuela=mysql_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso AND alumnos.id_centro=$id_escuela ORDER BY usuarios.fecha_nacimiento") or die(mysql_error());
$busca_alumnos_escuela=db_query_params('SELECT * FROM usuarios,alumnos LEFT JOIN aulas_alumnos_grupos ON aulas_alumnos_grupos.id_alumno=alumnos.id_alumno LEFT JOIN aulas_grupos ON aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=? AND alumnos.id_centro=? ORDER BY aulas_grupos.dia, aulas_grupos.id_grupo', 'ss', array($id_curso, $id_escuela)) or die(db_error());
?>
<table class="table table-bordered table-striped">
<tr>
	<td class="col-md-1"><b>Nº</b></td>
	<td class="col-md-3"><b>Nombre alumno</b></td>
	<td class="col-md-2"><b>Grupo</b></td>
	<td class="col-md-2"><b>Padres</b></td>
	<td class="col-md-2"><b>Info. de Contacto</b></td>
	<td class="col-md-2"><b>Teléfono</b></td>
</tr>	
<?php

$contador=0;

while($alumnos_escuela=db_fetch_array($busca_alumnos_escuela)) {

	$contador++;

	$busca_padres_alumnos_escuela=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.padre_de=?', 's', array($alumnos_escuela["id_usuario"]));
		
	$lista_padres_alumno="";	
	
	$lista_grupos_alumno="";
	
	$lista_grupos_alumno.=$dia_sem_largo[$alumnos_escuela["dia"]]." de ".date("H:i",strtotime($alumnos_escuela["hora_inicio"]))." a ".date("H:i",strtotime($alumnos_escuela["hora_fin"])).".<br />";
	
	// Comprueba si tiene contacto
	$lista_contactos_alumno="";
	$busca_contactos_de_alumno=db_query_params('SELECT *, cursos_academicos.nombre as nombre_curso FROM contactos, cursos_academicos WHERE contactos.id_curso=cursos_academicos.id_curso AND contactos.id_usuario=? ORDER BY cursos_academicos.id_curso', 's', array($alumnos_escuela["id_usuario"])) or die(db_error());
	
	while($contactos_de_alumno=db_fetch_array($busca_contactos_de_alumno)) {
		$lista_contactos_alumno.="<a href='/contactos/nuevo_contacto.php?id_contacto=".$contactos_de_alumno["id_contacto"]."' target='_blank'>".$contactos_de_alumno["nombre_curso"]."</a><br />";	
	}
		
	
// Busca padres

if (db_num_rows($busca_padres_alumnos_escuela)==0) {
	$lista_padres_alumno.="<a href='/alumnos/nuevo_alumno2.php?u=".$alumnos_escuela["id_usuario"]."' target='_blank'><font color='#ff0000'>Faltan datos padres</font></a>";
}

while($padres_alumnos_escuela=db_fetch_array($busca_padres_alumnos_escuela)){
	
	$nombre_padres=$padres_alumnos_escuela["Nombre"]." ".$padres_alumnos_escuela["Apellido1"]." ".$padres_alumnos_escuela["Apellido2"];
	
	if (strlen($nombre_padres)<3) {
		$nombre_padres="<font color='#ff0000'>NOMBRE DE PADRE MAL</font>";	
	}
	
	$lista_padres_alumno.="<a href='/alumnos/nuevo_alumno2.php?u=".$alumnos_escuela["id_usuario"]."' target='_blank'>".$nombre_padres."</a><br />";

	}

?>
	<tr>
		<td><?=$contador?></td>
		<td><?=security_escape($alumnos_escuela["Nombre"])." ".security_escape($alumnos_escuela["Apellido1"])." ".security_escape($alumnos_escuela["Apellido2"])?></td>
		<td><?=$lista_grupos_alumno?></td>
		<td><?=$lista_padres_alumno?></td>
		<td><?=$lista_contactos_alumno?></td>
		<td><?=$alumnos_escuela["telefono1"]." ".$alumnos_escuela["telefono2"]." ".$alumnos_escuela["movil"];?></td>
	</tr>
<?php

}


?>
</table>
<?php  require("../configuracion/parte_inferior.php"); ?>