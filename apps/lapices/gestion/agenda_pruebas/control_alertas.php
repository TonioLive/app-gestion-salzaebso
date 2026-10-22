<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
include("../../configuracion/config.php");

$estado_nota[0]="<font color='#ff0000'>Pendiente</font>";
$estado_nota[1]="<font color='#00ff00'>Realizada</font>";

$tipos_notas_inci=array();

$busca_notas_inci=db_query("SELECT * FROM agenda_alumnos_tipos_notas");
while($notas_inci=db_fetch_array($busca_notas_inci)) {
        $tipos_notas_inci[$notas_inci["id_tipo"]]=$notas_inci["nombre_tipo"];
        }

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

?>
<table width=600 border=0 cellpadding=4 cellspacing=1 align="right">
<tr bgcolor="#dedede"><td align="center" colspan=4><font color="red" size=3 face="Arial"><b>ALERTAS</b></font></td></tr>
<?php 
$id_aula=$_GET["id_aula"];

$busca_alertas=db_query("SELECT * FROM agenda_alumnos_seguimiento WHERE realizada=0 AND id_aula=$id_aula AND dia='".date("Y-m-d")."' AND avisar>0 ORDER BY hora;") or die(db_error());

while($alertas=db_fetch_array($busca_alertas)) {

	$hora_actual=strtotime(date("H:i:s"));
	$hora_aviso=strtotime($alertas["hora"]);
	
	if ($alertas["avisar"]>1) {	
		$hora_aviso=$hora_aviso-($alertas["avisar"]*60);
		}

	if ($hora_actual>=$hora_aviso) {
	
		// Datos alumno
		if ($alertas["id_alumno"]!=0) {
		$busca_datos_alumno=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=".$alertas["id_alumno"]);

		$datos_alumno=db_fetch_array($busca_datos_alumno);

		$nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"].". ";
		} else {
		$nombre_completo_alumno="";
		}
		?>
		<tr bgcolor="#efefef">
		<td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("H:i",strtotime($alertas["hora"]));?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=app_text_utf8($tipos_notas_inci[$alertas["tipo_nota"]]);?></font></td>
		<td><font size=2 face="Arial"><b><?=app_text_utf8($nombre_completo_alumno)?></b><?=app_text_utf8($alertas["nota"]);?></font></td>
		<td align="center" width=5%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href='javascript:cambiar_estado(<?=$alertas["id"];?>,<?=$alertas["realizada"];?>)'><?=$estado_nota[$alertas["realizada"]]?></a></td>
		</tr>
		<?php 
		}


	}

?>
</table>