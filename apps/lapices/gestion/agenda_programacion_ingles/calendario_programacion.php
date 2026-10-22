<?php  require("../../configuracion/parte_superior_sin_login_ancho_bootstrap.php"); ?>
<SCRIPT language="JavaScript">
<!--hide

function ampliar_dia(dia)
{
window.open('/actividades.php?dia='+dia,'ampliar_dia','toolbar=no,resizable=yes,width=550,height=550')
}
//-->
</SCRIPT>
<div align="center">
<?php 

$id_aula=$_GET["id_aula"];
$id_plantilla=$_GET["id_plantilla"];

// Busca datos plantilla
$busca_datos=db_query("SELECT * FROM agenda_programacion_plantillas WHERE id_plantilla=$id_plantilla");

if (db_num_rows($busca_datos)!=0) {

		$datos_plantilla=db_fetch_array($busca_datos);
		
		$descripcion=$datos_plantilla["descripcion"];
		$nivel=$datos_plantilla["nivel"];
		$num_sesiones=$datos_plantilla["num_sesiones"];
		$monitor=$datos_plantilla["monitor"];
		$periodicidad=$datos_plantilla["periodicidad"];
				
}


$nombres_escuelas=array();
$nombres_escuelas[0]="Actividades comunes";

$busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
while($lista_centros=db_fetch_array($busca_centros)){
        $nombres_escuelas[$lista_centros["id_centro"]]=$lista_centros["nombre_centro"];
        }
                
$busca_aulas=db_query("SELECT * FROM centro_aulas ORDER BY id_aula");
$nombres_aulas=array();
$nombres_aulas[0]="Actividades comunes";
while($lista_aulas=db_fetch_array($busca_aulas)){
        $nombres_aulas[$lista_aulas["id_aula"]]="Aula ".$lista_aulas["nombre"];
		
		if ($lista_aulas["id_aula"]==$id_aula) {
			// Aula seleccionada
			$curso_seleccionado=$lista_aulas["id_curso"];
			$centro_actual=$lista_aulas["id_centro"];
		}
		
        }

// Busca el curso actual
if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }
        
if ($id_curso_actual!=0) {

$inicio=$datos_curso_actual["fecha_inicio"];


if (strlen($inicio)!=0) {
        $anio_fecha=date("Y",strtotime($inicio));
        } else {
        $anio_fecha=date("Y");
        }

if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
} 

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];

$mes_anterior=$mes-1;
$ano_anterior=date("Y",$fecha_time_stamp);
$mes_siguiente=$mes+1;
$ano_siguiente=date("Y",$fecha_time_stamp);
if ($mes==1) {
        $mes_anterior=12;
        $ano_anterior=$ano_anterior-1;
        }
if ($mes==12) {
        $mes_siguiente=1;
        $ano_siguiente=$ano_siguiente+1;
        }

//Inicio es el día en el que empieza la primera semana del mes + 6 horas (por lo del cambio horario, mire usted)
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400+21600;

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;
?>
<font size=3><b>Calendario programación "<?=$descripcion?>" del aula <?=$nombres_aulas[$id_aula]?>(Curso <?=$datos_curso_actual["nombre"]?>)</b><br></font>
<br>
<font size=2>ATENCION: Los días no lectivos son los que aparecen en naranja en el calendario.</font><br><br>
<?php 
$anio_actual=date("Y",$fecha_time_stamp);

$dia_programacion_actual=1;

for ($mes_s=1; $mes_s<=12; $mes_s=$mes_s+1) {
$mesnombre=$nombre_mes[$mes];

?>
<font color="#000000" size=1 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
<table border=0 cellspacing="1" cellpadding="1" width="98%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center" width="14%">
<font face="<?=$fuente_general?>" color="#ffffff" size=1>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
</b>
</font>
</td>
<?php  
}
?>
</tr>
<?php  
$inicio_time_stamp=$inicio_time_stamp+3660;
$dia_mes=$inicio_time_stamp;
for ($semanas=1; $semanas<=6; $semanas=$semanas+1)
{
?>
<tr>
<?php  
  for ($dias=1; $dias<=7; $dias=$dias+1)
  {

    if ($mes==date("n",$inicio_time_stamp)) {
		
		$fondo_celda_dia="#EFEFEF";
		$estado_boton="";
		
		// Comprueba si es lectivo
        $busca_dia_en_calendario=db_query("SELECT * FROM calendario_laboral WHERE id_curso=$id_curso_actual AND id_centro=$centro_actual AND dia='".date("Y-m-d",$inicio_time_stamp)."'");
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
					// $estado_boton=" disabled";
                    $fondo_celda_dia="#F17327";
                }
        }
		
        // Comprueba si está marcado como programado
		$actividades_hoy="";
        $busca_dia_en_calendario=db_query("SELECT * FROM agenda_programacion_ingles_calendario WHERE id_aula=$id_aula AND id_programacion=$id_plantilla AND dia='".date("Y-m-d",$inicio_time_stamp)."'");
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        $estado_boton=" checked";
                        $fondo_celda_dia="#b6edc8";
						
						// Busca actividades hoy
						if ($centro_actual==8) {
							$filtro_sesiones="(numero_sesion=$dia_programacion_actual OR numero_sesion=$dia_programacion_actual+1)";
						} else {
							$filtro_sesiones="numero_sesion=$dia_programacion_actual";
						}
						
						$busca_actividades=db_query("SELECT * FROM agenda_programacion_plantillas_actividades LEFT JOIN agenda_programacion_ingles_actividades ON agenda_programacion_plantillas_actividades.id_actividad=agenda_programacion_ingles_actividades.id_actividad WHERE id_plantilla=$id_plantilla AND $filtro_sesiones ORDER BY orden_actividad,id_prog");
						
						if (db_num_rows($busca_actividades)==0) {
							$fondo_celda_dia="#ff7f7f";
							$actividades_hoy="<span class='text-danger'>No hay actividades previstas para este día.</span>";
						
						}												
						
						while($actividades=db_fetch_array($busca_actividades)) {
							
							$actividades_hoy.=$dia_programacion_actual.".- ".ucfirst($actividades["descripcion_actividad"])."<br />";
							$dia_programacion_actual++;

						}
						
                    }
					
                } else {
                // Si es sábado o domingo lo marca como desactivado
                if (($dias==7) or ($dias==6)) {
                        $estado_boton=" disabled";
                        }
                }
        ?>
        <td align="center" bgcolor="<?=$fondo_celda_dia;?>" valign="top">
        <font face="<?=$fuente_general?>" size=2 color="#000000">
        <?php 

        if ($mes==date("n",$inicio_time_stamp))
                {
                print "<b>";    }
        if ($dias==7)
                {
                print "<font color=#800000>";    }
        if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
                {
                print "<font color='blue'>";    }
                print (date("j",$inicio_time_stamp));
        if ($mes==date("n",$inicio_time_stamp))
                {
                print "</b>";
                }
        ?>
        <br><input type="checkbox" value="" name=""<?=$estado_boton?> onclick="cambia_dia(<?=$inicio_time_stamp;?>)">
        </font>
		<p class="text-left"><?=$actividades_hoy?></p>
        </td>
        <?php 
        } else {
        ?>
        <td align="center" bgcolor="#AAAAAA">
        <font face="<?=$fuente_general?>" color="#000000" size=1>&nbsp;</font>
        </td>
        <?php 
        }
  $inicio_time_stamp=$inicio_time_stamp+86400;
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php  
}
$mes=$mes+1;
if ($mes==13) {
        $mes=1;
        }
$anio_actual=date("Y",$inicio_time_stamp);
$inicio_time_stamp=$inicio_time_stamp-(86400*7);
?>
<tr>
<td>
</td>
</tr>
</table>
<?php 
}
?>
<br><br></font>
<b>Nº de sesiones previstas:</b> <?=$num_sesiones?><br>
<b>Nº de sesiones programadas:</b> <?=$dia_programacion_actual;?>
</div>
<script language="Javascript">
function cambia_dia(dia) {
        window.open("cambia_estado_dia.php?dia="+dia+"&id_aula=<?=$id_aula?>&id_plantilla=<?=$id_plantilla?>","cambia_dia","toolbar=no,width=150,height=100");
        }
</script>
<?php 
}
require("../../configuracion/parte_inferior.php"); ?>
