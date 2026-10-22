<?php  require("../../configuracion/parte_superior_privado_ancho.php");

$id_curso=$_GET["id_curso"];
$ciclo=$_GET["ciclo"];

if (strlen($ciclo)==0) {
        $ciclo=1;
        }

$nombres_ciclos[1]="Educación Primaria de Primer Ciclo";
$nombres_ciclos[2]="Educación Primaria de Segundp Ciclo";

$id_centro=$centro_actual;

//Busca cursos
$consulta_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$id_curso);
if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                }

?>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=13><small><b>Listado de alumnos <?=$nombres_ciclos[$ciclo];?> <i>Escuela "<?=$nombre_centro_actual;?>"</i> <i>Curso <?=$nombre_curso_academico;?></i></b></small></td>
  </tr>
<tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>">Nº</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>1 er. Apellido</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>2º Apellido</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Nacionalidad</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>SIP</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Tipo educacion</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Inicio</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fin</b></font></td>
</tr>
<?php 
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND alumnos.id_curso=$id_curso ORDER BY alumnos.aula,Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_alumnos)!=0) {

$contador_alumnos=0;

while($listado_alumnos=db_fetch_array($busca_alumnos)){

$id_aula_alumno=$listado_alumnos["aula"];

$consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula_alumno;") or die(db_error());

$aulas=db_fetch_array($consulta_aulas);

$ciclo_aula=$aulas["ciclo"];

if (($ciclo_aula==$ciclo) or (($ciclo_aula==0) and ($ciclo==1))) {

$contador_alumnos++;

$id_aula=$aulas["id_aula"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT centro_aulas.*, cursos_academicos.*, centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);
$nombre_aula=$datos_aula["nombre_aula"];

$curso_del_aula=$datos_aula["id_curso"];

$fecha_de_inicio_curso=$datos_aula["fecha_inicio"];

if (intval(strtotime($fecha_de_inicio_curso))>intval(time())) {
        $curso_iniciado=false;
        } else {
        $curso_iniciado=true;
        }

        // Comprueba si se le han incluido los servicios
        $id_inscripcion=$listado_alumnos["id_alumno"];
        $busca_inscripcion=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=1") or die(db_error());
        if (db_num_rows($busca_inscripcion)==0) {
                $no_inscripcion=true;
                } else {
                $no_inscripcion=false;
                }
        if ($datos_aula["comprobar_inscripcion"]==0) {
                $no_inscripcion=false;
                }
        $busca_cuotas=db_query("SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=$id_inscripcion AND tarifa.subcat=2") or die(db_error());
        if (db_num_rows($busca_cuotas)==0) {
                $no_cuotas=true;
                } else {
                $no_cuotas=false;
                }
         if ($datos_aula["comprobar_cuotas"]==0) {
                $no_cuotas=false;
                }
        if ($no_inscripcion==true) {
                $busca_inscripcion=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.subcat=1") or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_inscripcion=false;
                                }
                        }
                }
                
        if ($no_cuotas==true) {
                $busca_inscripcion=db_query("SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_aula=$id_aula AND tarifa.subcat=2") or die(db_error());
                while($datos_inscripcion=db_fetch_array($busca_inscripcion)) {
                        $busca_si_activado=db_query("SELECT * FROM servicios_no_contratados WHERE id_servicio_aula=".$datos_inscripcion["id_servicio_contratado"]." AND id_usuario=$id_inscripcion");
                        if (db_num_rows($busca_si_activado)==0) {
                                $no_cuotas=false;
                                }
                        }
                }

        
        
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                
        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#ffacac";
                } else {
                if ($listado_alumnos["fecha_matricula"]>date("Y-m-d")) {
                        $color_fuente_linea="#0D0573";
                        } else {
                        $color_fuente_linea="#000000";
                        }
                }
                                // Tipos de comedor
                                $tipo_de_comedor[0]="Desconocido";
                                $tipo_de_comedor[1]="Si";
                                $tipo_de_comedor[2]="No";
                                $tipo_de_comedor[3]="Régimen";

                                // Tipos de menu
                                $tipo_de_menu[0]="Desconocido";
                                $tipo_de_menu[1]="Pequeños";
                                $tipo_de_menu[2]="Mayores";
                                $tipo_de_menu[3]="Colegial";
                                $tipo_de_menu[4]="Toma";
                                $tipo_de_menu[5]="Bebe";
                                
        // Dias asistidos
        $filtros_asistencia="year(servicios_consumidos.fecha)=".date("Y");
        $filtros_asistencia=$filtros_asistencia." AND month(servicios_consumidos.fecha)=".date("n");
        $busca_dias_asistidos=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_asistidos=db_num_rows($busca_dias_asistidos);
        
        $busca_dias_comedor=db_query("SELECT fecha FROM servicios_consumidos WHERE $filtros_asistencia AND (tipo_menu=1 OR tipo_menu=3) AND id_alumno=".$listado_alumnos["id_usuario"]." GROUP BY fecha;") or die(db_error());
        $total_dias_comedor=db_num_rows($busca_dias_comedor);
        
        ?>
        <tr>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$contador_alumnos;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?></font></b></a></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido2"]?></font></b></a></font></td>
        <?php 
        if ((($no_inscripcion==false) and ($no_cuotas==false)) or ($curso_iniciado==false)) {
        ?>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php  if ($listado_alumnos["sexo"]=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["nacionalidad"]?></font></td>
                <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["SIP"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$tipo_de_comedor[$listado_alumnos["comedor"]]?><?php  if ($listado_alumnos["comedor"]==1) { print " (".$tipo_de_menu[$listado_alumnos["tipo_menu_comedor"]].")"; } ?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php if ($listado_alumnos["tipo_mensualidad"]==1) { print "Media jornada"; } else { print "Jornada Completa"; } ?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$nombre_aula;?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));?></font></td>
        <?php 
        } else {
        $mensaje11="";
        if ($no_inscripcion==true) {
                $mensaje11=$mensaje11."Inscripción en el curso";
                }
        if ($no_cuotas==true) {
                if ($mensaje11!="") {
                        $mensaje11=$mensaje11." y ";
                        }
                $mensaje11=$mensaje11."Servicio de educación";
                }
        $mensaje11="Falta asigar al niño los servicios de: ".$mensaje11.".";
        ?>
        <td colspan=4 align="center"><font color="red" size=1><?=$mensaje11?></font></td>
        <?php 
        }
        ?>
        </tr>
        <?php 
        }
        }

}
?>
</table>
<br>
<input type="button" onclick="window.location.href='gestion_aulas.php?curso_seleccionado=<?=$curso_del_aula;?>'" value="Volver al listado de aulas" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
