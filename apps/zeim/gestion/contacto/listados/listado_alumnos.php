<?php
require("../../../configuracion/config.php");
require("../../../configuracion/autentifica.php");

$consulta="SELECT usuarios.id_usuario,Nombre,Apellido1,Apellido2,sexo,fecha_nacimiento,telefono1,telefono2,movil,Direccion,coordenadas,cp,Poblacion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND year(fecha_nacimiento)>=2012 AND year(fecha_nacimiento)<=2017 AND (telefono1<>'' or telefono2<>'' or movil<>'') GROUP BY usuarios.id_usuario ORDER BY Apellido1,Apellido2,Nombre";

$buscaAlumnos=db_query($consulta);


?>
<table>
	<tr>
		<td>Origen</td>
		<td>Nombre</td>
		<td>Apellido1</td>
		<td>Apellido2</td>
		<td>sexo</td>
		<td>fecha_nacimiento</td>
		<td>telefono1</td>
		<td>telefono2</td>
		<td>movil</td>
		<td>Direccion</td>
		<td>coordenadas</td>
		<td>cp</td>
		<td>Poblacion</td>
		<td>Nombre Padre/Madre 1</td>
		<td>Teléfono Padre/Madre 1</td>
		<td>Nombre Padre/Madre 2</td>
		<td>Teléfono Padre/Madre 2</td>
	</tr>
<?php
while($datosAlumno=db_fetch_array($buscaAlumnos)) {
	
	// Busca padres
	$nombreP=array();
	$telefonoP=array();
	$nombreP[0]="";
	$nombreP[1]="";
	$telefonoP[0]="";
	$telefonoP[1]="";
	
	$busca_padres=db_query_params('SELECT * FROM usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND padres.parentesco=1 AND padre_de=? LIMIT 0,2', 's', array($datosAlumno["id_usuario"])) or die(db_error());
	$contaP=0;
	while($padres=db_fetch_array($busca_padres)) {
			
		$nombreP[$contaP]=$padres["Nombre"]." ".$padres["Apellido1"]." ".$padres["Apellido2"];
		if (strlen($padres["telefono1"])>1) { $telefonoP[$contaP][]=$padres["telefono1"]; }
		if (strlen($padres["telefono2"])>1) { $telefonoP[$contaP][]=$padres["telefono2"]; }
		if (strlen($padres["movil"])>1) { $telefonoP[$contaP][]=$padres["movil"]; }
		$contaP++;
	}
	
	$telefonoP0="";
	if ($telefonoP[0]) {
		$telefonoP0=implode(",",$telefonoP[0]);
	}
	$telefonoP1="";
	if ($telefonoP[1]) {
		$telefonoP1=implode(",",$telefonoP[1]);
	}
	
	?>
	<tr>
		<td>Sant Pau</td>
		<td><?=$datosAlumno["Nombre"]?></td>
		<td><?=$datosAlumno["Apellido1"]?></td>
		<td><?=$datosAlumno["Apellido2"]?></td>
		<td><?=$datosAlumno["sexo"]?></td>
		<td><?=$datosAlumno["fecha_nacimiento"]?></td>
		<td><?=$datosAlumno["telefono1"]?></td>
		<td><?=$datosAlumno["telefono2"]?></td>
		<td><?=$datosAlumno["movil"]?></td>
		<td><?=$datosAlumno["Direccion"]?></td>
		<td><?=$datosAlumno["coordenadas"]?></td>
		<td><?=$datosAlumno["cp"]?></td>
		<td><?=$datosAlumno["Poblacion"]?></td>
		<td><?=$nombreP[0]?></td>
		<td><?=$telefonoP0?></td>
		<td><?=$nombreP[1]?></td>
		<td><?=$telefonoP1?></td>
	</tr>
	<?php	
	
}



?>
</table>