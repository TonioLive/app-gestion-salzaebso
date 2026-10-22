<?php 
function microtime_float()
{
list($useg, $seg) = explode(" ", microtime());
return ((float)$useg + (float)$seg);
}

$tiempo_inicio = microtime_float();

require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$mostrar_niveles_futuros=$_GET["subniveles"];
$curso_seleccionado=$_GET["curso_seleccionado"];
$mostrar_lista_espera=$_GET["lista_espera"];

$fecha_listado_bebes="";  
if (strlen($curso_seleccionado)!=0) {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];        
        
        $fecha_time_stamp=strtotime($datos_curso_actual["fecha_inicio"]);
        $fecha_inicio_curso_actual=$datos_curso_actual["fecha_inicio"];
        $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];

        if ($fecha_time_stamp<time()) {
                // Si el curso está iniciado vamos al día actual
                $fecha_time_stamp=time();
        }       
        
        if (strtotime($datos_curso_actual["fecha_fin"])<time()) {
                        // Si el curso está finalizado vamos a la fecha de finalización
                        $fecha_time_stamp=strtotime($datos_curso_actual["fecha_fin"]);
        }
        
} else {

    $fecha_time_stamp=$_GET["fecha"];
        if (strlen($fecha_time_stamp)==0) {
            $fecha_time_stamp=time();      
    }
    $fecha_listado_bebes=$fecha_time_stamp;  
} 
        
$error_al_quitar=$_GET["error_al_quitar"];

$array_listas_de_espera=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");
$colores_listas_de_espera=array("#fff","#9ed09c","#9cbdd0","#d0af9c","#f9a63b","#ffa9f0");

$total_grupos=0;
$total_grupos_monitor=0;

$total_por_tipo=array();

$filtro_monitores=$_GET["monitor"];
if (strlen($filtro_monitores)==0) {
        $filtro_monitores=0;
        }        

// Inicio y fin de semana
$diasemana=date("w",$fecha_time_stamp);
$dia_inicio_semana=$fecha_time_stamp-(($diasemana-1)*86400);

$dia_fin_semana=$dia_inicio_semana+(7*86400);

$dias_maximo=7;

$mes=date("n",$fecha_time_stamp);
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

$timestamp_siguiente=strtotime($ano_siguiente."-".$mes_siguiente."-01");
$timestamp_anterior=strtotime($ano_anterior."-".$mes_anterior."-01");


// Plantilla

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

// Busca grupos donde ya está asignado

$busca_programacion_plantillas=db_query("SELECT * FROM agenda_programacion_aulas_vinculadas WHERE id_plantilla=$id_plantilla") or die(db_error());

$array_grupos=array();

while($programacion_plantillas=db_fetch_array($busca_programacion_plantillas)) {

			$dia_prog=strtotime($programacion_plantillas["fecha_inicio"]." 19:00"); 
			
			if ($periodicidad==1) {
						
				// Mensual
				$fecha_inicio=$dia_prog;
				$mes_inicio=date("n",$dia_prog);
				$anio_inicio=date("Y",$dia_prog);
				
				$mes_fin=$mes_inicio+($num_sesiones-1);
				$anio_fin=$anio_inicio;
				
				
				if ($mes_fin>12) {
				
					$mes_fin=$mes_fin-12;
					$anio_fin++;
				
				}
				
				$fecha_fin_previo=strtotime($anio_fin."-".$mes_fin."-01");
				$fecha_fin=strtotime($anio_fin."-".$mes_fin."-".date("t",$fecha_fin_previo));							
				
				if ((($dia_inicio_semana>=$fecha_inicio) and ($dia_inicio_semana<=$fecha_fin)) or (($dia_fin_semana>=$fecha_inicio) and ($dia_fin_semana<=$fecha_fin))) {
				
					$array_grupos[]=$programacion_plantillas["id_grupo"]."-".$programacion_plantillas["id_subgrupo"];
									
				}
								
			} else {
				// Semanal
				for($c_semanas=1;$c_semanas<=$num_sesiones;$c_semanas++) {
							
					if (($dia_prog>=$dia_inicio_semana) and ($dia_prog<=$dia_fin_semana)) {
					
						$array_grupos[]=$programacion_plantillas["id_grupo"]."-".$programacion_plantillas["id_subgrupo"];					
					
					}													
					
					$dia_prog = $dia_prog + (86400*7);					
					
				}												
			}


}

// Fin grupos asignados

// Busca el curso actual

        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $nombre_curso_actual=$datos_curso_actual["nombre"];
                $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];
                $fecha_inicio_curso_actual=$datos_curso_actual["fecha_inicio"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY fecha_inicio");

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        $nombre_curso_actual=$datos_curso_actual["nombre"];
                        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                        $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];
                        $fecha_inicio_curso_actual=$datos_curso_actual["fecha_inicio"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
        }

if ((strtotime($fecha_inicio_curso_actual)>time()) and (strlen($_GET["subniveles"])==0)) {
        
	// Si el curso no está iniciado muestra siempre los niveles futuros
	$mostrar_niveles_futuros=1;
        
	}

// Provisional!!!!
$mostrar_niveles_futuros=1;

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
        
// Busca nuevo curso
$busca_nuevo_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso<>$id_curso_actual AND fecha_inicio>='".date("Y",strtotime($fecha_fin_curso_actual))."-08-31' AND month(fecha_inicio)!=7");

if (db_num_rows($busca_nuevo_curso)!=0) {

		$datos_nuevo_curso=db_fetch_array($busca_nuevo_curso);
        $nombre_curso_siguente=$datos_nuevo_curso["nombre"];
        $id_nuevo_curso=$datos_nuevo_curso["id_curso"];

}        

// busca grupos

$busca_numero_maximo_de_grupos=db_query("SELECT * FROM aulas_grupos WHERE id_curso=$id_curso_actual ORDER BY orden DESC LIMIT 0,1");
if (db_num_rows($busca_numero_maximo_de_grupos)!=0) {
	
	$datos_num_grupos=db_fetch_array($busca_numero_maximo_de_grupos);
	$num_max_grupo=$datos_num_grupos["orden"];

} else {
	$num_max_grupo=24;
}

$busca_grupos=db_query("SELECT * FROM aulas_grupos WHERE id_curso=$id_curso_actual ORDER BY dia,hora_inicio");

$lista_cuadros_grupos0="";
$lista_cuadros_grupos1="";
$lista_cuadros_grupos2="";
$lista_cuadros_grupos3="";

$lista_li_grupos0="";
$lista_li_grupos1="";
$lista_li_grupos2="";
$lista_li_grupos3="";

while($grupos=db_fetch_array($busca_grupos)) {
        $lista_cuadros_grupos0.="#grupo_0_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos1.="#grupo_1_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos2.="#grupo_2_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos3.="#grupo_3_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos4.="#grupo_4_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos5.="#grupo_5_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos6.="#grupo_6_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos7.="#grupo_7_".$grupos["id_grupo"].", ";
        $lista_cuadros_grupos8.="#grupo_8_".$grupos["id_grupo"].", ";
                        
        $lista_li_grupos0.="#grupo_0_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos1.="#grupo_1_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos2.="#grupo_2_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos3.="#grupo_3_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos4.="#grupo_4_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos5.="#grupo_5_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos6.="#grupo_6_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos7.="#grupo_7_".$grupos["id_grupo"]." li, ";
        $lista_li_grupos8.="#grupo_8_".$grupos["id_grupo"]." li, ";
        }
        
if (strlen($lista_cuadros_grupos1)!=0) {
        $lista_cuadros_grupos0=substr($lista_cuadros_grupos0,0,strlen($lista_cuadros_grupos0)-2);
        $lista_cuadros_grupos1=substr($lista_cuadros_grupos1,0,strlen($lista_cuadros_grupos1)-2);
        $lista_cuadros_grupos2=substr($lista_cuadros_grupos2,0,strlen($lista_cuadros_grupos2)-2);
        $lista_cuadros_grupos3=substr($lista_cuadros_grupos3,0,strlen($lista_cuadros_grupos3)-2);
        $lista_cuadros_grupos4=substr($lista_cuadros_grupos4,0,strlen($lista_cuadros_grupos4)-2);
        $lista_cuadros_grupos5=substr($lista_cuadros_grupos5,0,strlen($lista_cuadros_grupos5)-2);
        $lista_cuadros_grupos6=substr($lista_cuadros_grupos6,0,strlen($lista_cuadros_grupos6)-2);
        $lista_cuadros_grupos7=substr($lista_cuadros_grupos7,0,strlen($lista_cuadros_grupos7)-2);
        $lista_cuadros_grupos8=substr($lista_cuadros_grupos8,0,strlen($lista_cuadros_grupos8)-2);                                 
        
        $lista_li_grupos0=substr($lista_li_grupos0,0,strlen($lista_li_grupos0)-2);
        $lista_li_grupos1=substr($lista_li_grupos1,0,strlen($lista_li_grupos1)-2);
        $lista_li_grupos2=substr($lista_li_grupos2,0,strlen($lista_li_grupos2)-2);
        $lista_li_grupos3=substr($lista_li_grupos3,0,strlen($lista_li_grupos3)-2);
        $lista_li_grupos4=substr($lista_li_grupos4,0,strlen($lista_li_grupos4)-2);
        $lista_li_grupos5=substr($lista_li_grupos5,0,strlen($lista_li_grupos5)-2);
        $lista_li_grupos6=substr($lista_li_grupos6,0,strlen($lista_li_grupos6)-2);
        $lista_li_grupos7=substr($lista_li_grupos7,0,strlen($lista_li_grupos7)-2);
        $lista_li_grupos8=substr($lista_li_grupos8,0,strlen($lista_li_grupos8)-2);                                         
        }

$contador_ocupacion=0;
$contador_ocupacion_prox=0;
$contador_matriculados=0;
$contador_matriculados_prox=0;

$capacidad_total=0;
$matriculados_total=0;

$matriculados_por_tipos=array();
$capacidad_por_tipos=array();

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Horario Grupos</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>
#lista_alumnos0 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
#lista_alumnos1 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #9ed09c; padding: 5px; width: 123px; }
#lista_alumnos2 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #9cbdd0; padding: 5px; width: 123px; }
#lista_alumnos3 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #d0af9c; padding: 5px; width: 123px; }
#lista_alumnos4 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #f9a63b; padding: 5px; width: 123px; }
#lista_alumnos5 { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #ffa9f0; padding: 5px; width: 123px; }

.lista_espera { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #fcffd4; padding: 5px; width: 123px; }
.lista_espera li{ margin: 2px; padding: 2px; font-size: 0.8em; }
                                     

<?=$lista_cuadros_grupos0?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos1?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos2?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos3?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos4?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos5?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos6?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos7?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }
<?=$lista_cuadros_grupos8?> { list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #efefef; padding: 5px; width: 123px; }

#lista_alumnos0 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos1 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos2 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos3 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos4 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

#lista_alumnos5 li{ margin: 2px; padding: 2px; font-size: 0.8em; }

<?=$lista_li_grupos0;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos1;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos2;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos3;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos4;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos5;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos6;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos7;?> { margin: 2px; padding: 2px; font-size: 0.8em; }
<?=$lista_li_grupos8;?> { margin: 2px; padding: 2px; font-size: 0.8em; }

.fondo_nivel0 {
        background: #fff;
        }
.fondo_nivel1 {
        background: #9ed09c;
        }
.fondo_nivel2 {
        background: #9cbdd0;
        }
.fondo_nivel3 {
        background: #d0af9c;
        } 
.fondo_nivel4 {
        background: #fff;
        } 
.fondo_nivel5 {
        background: #fff;
        }   
.fondo_nivelB {
        background: #000;
        }                        
 
.textoBlanco {
                color: #fff;
                }  
                
                     
<?php 
foreach ($colores_subniveles as $indice => $color) {
?>

.fondo_subnivel<?=$indice?> {
background: <?=$color?>;
}

<?php 
}

$colores_subgrupos=array();

$busca_tipos_grupos=db_query("SELECT * FROM aulas_grupos_tipos;");
while($datos_grupos=db_fetch_array($busca_tipos_grupos)) {
        $colores_subgrupos[$datos_grupos["id_tipo_grupo"]]=$datos_grupos["color_tipo_subgrupo"];
?>
.fondo_tipo_grupo<?=$datos_grupos["id_tipo_grupo"]?> {
        background: <?=$datos_grupos["color_tipo_subgrupo"]?> ;
        }
<?php 
}

for ($c_anios=0;$c_anios<count($colores_listas_de_espera);$c_anios++) {
?>
.fondo_espera<?=$c_anios?> {
        background: <?=$colores_listas_de_espera[$c_anios];?> ;
        }
<?php 
        }
?>   

.panelFijo{
    height: 1000vh;
    overflow-y: no;
    overflow-x: hidden;
}
 
.modal.modal-wide .modal-dialog {
  width: 95%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 
 
.tachado {
	text-decoration:line-through;
} 
   
</style>
<script>

function programar(id_aula, fecha) {

	window.location.href='vincular_plantilla1.php?id_plantilla=<?=$id_plantilla?>&id_aula='+id_aula+'&fecha_inicio='+fecha;

}

</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin">
<div class="container-fluid">
<div class="row">
  <div class="col-md-12">
     <table class="table">
     <tr>
        <td colspan="2"><a class="btn btn-primary" href="<?=$SERVER["PHP_SELF"]?>?id_plantilla=<?=$id_plantilla?>&fecha=<?=$dia_inicio_semana-(7*86400);?>"><i class="fa fa-angle-left"></i> Semana Anterior</a> <a class="btn btn-primary" href="<?=$SERVER["PHP_SELF"]?>?id_plantilla=<?=$id_plantilla?>&fecha=<?=$timestamp_anterior;?>"><i class="fa fa-angle-double-left"></i> Mes Anterior</a></td>
        <td colspan="2" align="center"><?=$nombre_monitor_seleccionado?><h4>Semana del <?=date("d/m/Y",$dia_inicio_semana);?> al <?=date("d/m/Y",$dia_inicio_semana+(86400*6));?></h4><h5><form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form></h5></td>
                <td colspan="3" align="right"><a class="btn btn-primary" href="<?=$SERVER["PHP_SELF"]?>?id_plantilla=<?=$id_plantilla?>&fecha=<?=$timestamp_siguiente;?>">Mes Siguiente <i class="fa fa-angle-double-right"></i></a> <a class="btn btn-primary" href="<?=$SERVER["PHP_SELF"]?>?id_plantilla=<?=$id_plantilla?>&fecha=<?=$dia_inicio_semana+(7*86400);?>">Semana Siguiente <i class="fa fa-angle-right"></i></a><br />                                                               
                </td>
     </tr>
     <tr>
        <td>Lunes<br><?=date("d/m/Y",$dia_inicio_semana);?></td>
        <td>Martes<br><?=date("d/m/Y",$dia_inicio_semana+(86400*1));?></td>
        <td>Miércoles<br><?=date("d/m/Y",$dia_inicio_semana+(86400*2));?></td>
        <td>Jueves<br><?=date("d/m/Y",$dia_inicio_semana+(86400*3));?></td>
        <td>Viernes<br><?=date("d/m/Y",$dia_inicio_semana+(86400*4));?></td>
        <td>Sábado<br><?=date("d/m/Y",$dia_inicio_semana+(86400*5));?></td>
        <td>Domingo<br><?=date("d/m/Y",$dia_inicio_semana+(86400*6));?></td>
     </tr>
     <?php 
     // $busca_turnos=mysql_query("SELECT hora_inicio,hora_fin FROM aulas_grupos WHERE dia=1 AND id_curso=$id_curso_actual ORDER BY hora_inicio");

         $contador_grupos=0;

     //while($turnos=mysql_fetch_array($busca_turnos))
     for ($turnos=1;$turnos<=$num_max_grupo;$turnos++)
     {
     $contador_grupos++;
     ?>
     <tr>
     <?php 
     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {
     
     $busca_hora_inicio=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual AND dia=".$c_dia);
     
     if (db_num_rows($busca_hora_inicio)!=0) {
     
            $datos_grupo_inicio=db_fetch_array($busca_hora_inicio);
            
            $nombre_horario_mostrado=$dia_sem_largo[$c_dia]." de ".date("H:i",strtotime($datos_grupo_inicio["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo_inicio["hora_fin"]));
            
             ?>
             <td><?=date("H:i",strtotime($datos_grupo_inicio["hora_inicio"]))?> - <?=date("H:i",strtotime($datos_grupo_inicio["hora_fin"]))?></td>
             <?php  
             } else {
             ?>
             <td>&nbsp;</td>
             <?php 
             }
     
     }
     ?>
     </td>
     <tr>
     <?php 
     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {
     
     $busca_grupos=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual AND dia=".$c_dia);
     
     if (db_num_rows($busca_grupos)!=0) {
     
     $datos_grupo=db_fetch_array($busca_grupos);
     
     // Comprueba si es festivo
     $fecha_dia_sel=$dia_inicio_semana+(($c_dia-1)*86400);
                  
     $busca_festivos=db_query("SELECT * FROM calendario_laboral WHERE dia='".date("Y-m-d",$fecha_dia_sel)."' AND estado=1");
     
     $color_fondo="#fff"; 
         
     if (db_num_rows($busca_festivos)!=0) {
     // es festivo
     $color_fondo="#ffcccc";  
     }
   
     ?>
         <td bgcolor="<?=$color_fondo?>">
         <table class="table">
         <?php 
         $num_subgrupos=$datos_grupo["num_subgrupos"];
         
         for ($c_subgrupos=0;$c_subgrupos<$num_subgrupos;$c_subgrupos++) {
         
         $total_grupos++;         
         
         $nivel_actual=$c_subgrupos;
         
         // Busca datos subgrupo
         
         $busca_datos_subgrupo=db_query("SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual;");
         
         if (db_num_rows($busca_datos_subgrupo)==0) {
                 $inserta_subgrupo=db_query("INSERT INTO aulas_grupos_subgrupos (id_grupo,id_subgrupo) VALUES (".$datos_grupo["id_grupo"].",".$nivel_actual.");");
                 $busca_datos_subgrupo=db_query("SELECT * FROM aulas_grupos_subgrupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual;");
                 }
         
         $datos_subgrupo=db_fetch_array($busca_datos_subgrupo);
         
         $capacidad_subgrupo=$datos_subgrupo["capacidad"];                  
         
         if ($datos_subgrupo["tipo_subgrupo"]!=5) {
         	$busca_alumnos_grupo=db_query("SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND ((alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_curso=$id_curso_actual AND aulas_alumnos_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel-$tiempo_gracia)."') OR (alumnos.tipo_baja<>0 AND aulas_alumnos_grupos.id_curso=$id_curso_actual AND aulas_alumnos_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel)."'))") or die(db_error());
         	} else {
         	$busca_alumnos_grupo=db_query("SELECT * FROM usuarios, alumnos, servicios_consumidos_faltas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=servicios_consumidos_faltas.id_alumno AND alumnos.id_curso=$id_curso_actual AND servicios_consumidos_faltas.id_grupo=".$datos_grupo["id_grupo"]." AND servicios_consumidos_faltas.id_subgrupo=$nivel_actual AND servicios_consumidos_faltas.fecha_recuperacion='".date("Y-m-d",$fecha_dia_sel)."' ORDER BY usuarios.Nombre, usuarios.Apellido1, usuarios.Apellido2") or die(db_error());
         	}
         
         $num_alumnos_subgrupo=db_num_rows($busca_alumnos_grupo);
         
         
         // Busca alumnos grupo en el otro horario
         $busca_alumnos_grupo_ext=db_query("SELECT id_grupo FROM aulas_alumnos_lapices_grupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual");
         
         $num_alumnos_subgrupo_ext=db_num_rows($busca_alumnos_grupo_ext);
         
         $nombre_horario_mostrado=$dia_sem_largo[$c_dia]." de ".date("H:i",strtotime($datos_grupo["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo["hora_fin"]));
         
         // Busca monitores
         $lista_monitores_grupo="";
         $busca_monitores=db_query("SELECT * FROM aulas_grupos_monitores,usuarios WHERE aulas_grupos_monitores.id_usuario=usuarios.id_usuario AND aulas_grupos_monitores.id_grupo=".$datos_grupo["id_grupo"]." AND aulas_grupos_monitores.id_subgrupo=$c_subgrupos") or die(db_error());

                 $mostrar_grupo=false;
                                if (db_num_rows($busca_monitores)!=0) {
                                        $lista_monitores_grupo="<b>Monitor/a</b>";
                                        while($lista_monitores=db_fetch_array($busca_monitores)) {
                                                if ($lista_monitores["id_usuario"]!=0) {
                                                        $lista_monitores_grupo.="<br />".$lista_monitores["Nombre"]." ".$lista_monitores["Apellido1"];
                                                        if ($filtro_monitores==$lista_monitores["id_usuario"]) {
                                                                $mostrar_grupo=true;
                                                                }
                                                        }
                                                }
                                        }
                                if ((strlen($filtro_monitores)==0) or ($filtro_monitores==0)) {
                                        $mostrar_grupo=true;
                                        }
		if ($mostrar_grupo==true) {
		
        		$total_grupos_monitor++;   
                  
                  
                $capacidad_total=$capacidad_total+$capacidad_subgrupo;
                $matriculados_total=$matriculados_total+$num_alumnos_subgrupo;
                                
                $matriculados_por_tipos[$datos_subgrupo["tipo_subgrupo"]]=$matriculados_por_tipos[$datos_subgrupo["tipo_subgrupo"]]+$num_alumnos_subgrupo;
                $capacidad_por_tipos[$datos_subgrupo["tipo_subgrupo"]]=$capacidad_por_tipos[$datos_subgrupo["tipo_subgrupo"]]+$capacidad_subgrupo;    
                                
                                
                 $nombre_subgrupo=$nombre_horario_mostrado.", Grupo: ".($c_subgrupos+1);                   
                  
                 $funcion_agregar_grupos="agrega_grupo";
                 $funcion_mostrar_grupo="muestra_grupo";
                 if ($datos_subgrupo["tipo_subgrupo"]==5) {
                 	$funcion_agregar_grupos="agrega_grupo_recuperacion";
                 	$funcion_mostrar_grupo="muestra_grupo_recuperacion";
                 }   
                 
                 // Bloqueado
                 $texto_bloqueado="";
                 $boton_agregar_alumnos='<i class="fa fa-user-plus"></i>';
                 $color_boton_agregar_alumnos="primary";
                 if ($datos_subgrupo["bloqueado"]==1) {
                 
                 	$boton_agregar_alumnos='<i class="fa fa-lock"></i>';
                 	$color_boton_agregar_alumnos="danger";
                 	$texto_bloqueado="<br /><span class='label label-danger'>Grupo Bloqueado</span>";
                 	
                 }
                              
            	$ya_asignado="";
            	// Comprueba si está ya asignada a este grupo
            	if (in_array($datos_grupo["id_grupo"]."-".($c_subgrupos+1), $array_grupos)) {
            	
            		$ya_asignado="<br /><span class='label label-danger'>Ya programado</span>";
            	
            	} else {
            	
            		$ya_asignado="<br /><button onclick='programar(".$datos_grupo["id_grupo"].",".($c_subgrupos+1).",\"".$fecha_dia_sel."\")' class='btn btn-primary btn-xs'>Programar</button>";	
            	
            	}
            	
                                                              
          ?>
         <tr>
         <td>         
                <ul id="grupo_<?=$nivel_actual?>_<?=$datos_grupo["id_grupo"]?>" class="droptrue" style="background: <?=$colores_subgrupos[$datos_subgrupo["tipo_subgrupo"]]?>;">
                <li class="dropfalse" id="<?=$datos_grupo["id_grupo"];?>_<?=($c_subgrupos+1)?>"><big><b>Grupo <?=$c_subgrupos+1?><?=$ya_asignado?></b></big><?=$texto_bloqueado?> <br />Capacidad: <?=$capacidad_subgrupo?><br />Ocupación: <?=$num_alumnos_subgrupo?><br />Ocupación Ext.: <?=$num_alumnos_subgrupo_ext?><br />Disponibles: <?=$capacidad_subgrupo-$num_alumnos_subgrupo-$num_alumnos_subgrupo_ext?> <br /></li>
                <?php 

                while($alumnos=db_fetch_array($busca_alumnos_grupo)) {
                
              	$coletilla_pagos="";
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>=strtotime(date("Y-m-d",$fecha_dia_sel))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        }
                        
                $color_fuente_linea="";
                if ($alumnos["fecha_baja"]<date("Y-m-d",$fecha_dia_sel)) {
                $color_fuente_linea=" text-danger";
                }         
         
                
                if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                                if ($dif_meses==0) {
                                        $edad_d_l="";
                                        } else {
                                        $unidad_meses="meses";
                                        if ($dif_meses==1) {
                                                $unidad_meses="mes";
                                                }
                                        if ($dif_meses<0) {
                                                $dif_meses=12+$dif_meses;
                                                $edad_a_l=$edad_a_l-1;
                                        }
                                        $edad_d_l=" y ".$dif_meses." ".$unidad_meses;
                                        }                                
                $edad_alumno=$edad_a_l." años".$edad_d_l;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        }          
        
        		if ($mostrar_niveles_futuros==1) {
        		
        			// Nivel proximo curso
        			$nivel_proximo_curso_tmp=($alumnos["nivel_actual"]*3)-2;
        	
        			$nivel_proximo_curso=$alumnos["nivel_proximo"]; 
        			if (($nivel_proximo_curso==0) and ($alumnos["nivel_actual"]!=0)) {
        	
        				$nivel_proximo_curso=$nivel_proximo_curso_tmp;
        	
        			}
        		
        			$estilo_linea="fondo_subnivel".$nivel_proximo_curso." textoBlanco";
        			
        			// Comprueba si está ya inscrito
        			if ($id_nuevo_curso) {
    	    			$busca_si_proximo_alumnos=db_query("SELECT * FROM alumnos WHERE id_usuario=".$alumnos["id_usuario"]." AND id_curso=$id_nuevo_curso"); 
	        			if (db_num_rows($busca_si_proximo_alumnos)!=0) {
	        				$estilo_linea.=" tachado";
	        				}
        				}      		
        		
        		} else {
        		
        			$estilo_linea="fondo_nivel".$alumnos["nivel_actual"].$color_fuente_linea;
        			if ($alumnos["tipo_baja"]==2) {
        				$estilo_linea="fondo_nivelB textoBlanco";	
        			}        		
        		
        		}                                          		
                                 
                ?>
                <li class="ui-state-default <?=$estilo_linea?>" id="alumno_<?=$alumnos["id_alumno"]?>_<?=$alumnos["id_alumno_grupo"]?>"><?=$fecha_incorporacion.$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"]?> (<?=$edad_alumno;?>)</li>
                <?php 
                        }
                ?>
                <li><?=$lista_monitores_grupo;?></li>
                </ul>
         </td>
         </tr>
                <?php 
                }
         }
		?>
         </table>
         </td>
     <?php   
             } else {
             // En blanco
             ?>
             <td>&nbsp;</td>
             <?php 
             }
     }
     ?>
     </tr>
     <?php 
     }
     ?>
     </table>
  </div>
</div>
</div>

<?php 
$tiempo_fin = microtime_float();
$tiempo = $tiempo_fin - $tiempo_inicio;

// echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio); 
?>
</body>
</html>