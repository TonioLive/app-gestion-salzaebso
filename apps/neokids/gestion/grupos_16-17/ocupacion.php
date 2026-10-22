<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$capacidadTotal=array(0,0,0,0,0,0,0,0,0);
$ocupacionTotal=array(0,0,0,0,0,0,0,0,0);
$ocupacionProximoI=array(0,0,0,0,0,0,0,0,0);
$ocupacionExtTotal=array(0,0,0,0,0,0,0,0,0);
$enListaEsperaTotal=array(0,0,0,0,0,0,0,0,0); 
$capacidadExternos=array(0,0,0,0,0,0,0,0,0);

$curso_seleccionado=$_GET["curso_seleccionado"];
  
if (strlen($curso_seleccionado)!=0) {
	$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
	$datos_curso_actual=db_fetch_array($busca_curso_actual);
	$id_curso_actual=$datos_curso_actual["id_curso"];
	$nombre_curso_actual=$datos_curso_actual["nombre"];
	$permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
	
	$fecha_time_stamp=strtotime($datos_curso_actual["fecha_inicio"]);
	
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

} 
 
$fecha_informe=$fecha_time_stamp;
        
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

// Busca el curso actual

        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $nombre_curso_actual=$datos_curso_actual["nombre"];
                $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $id_curso_actual=$datos_curso_actual["id_curso"];
                        $nombre_curso_actual=$datos_curso_actual["nombre"];
                        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
                        } else {
                        $id_curso_actual=0;
                        }
                }
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
<title>Ocupación piscina</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">
<style>
                                     
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
        
                       
<?php 
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
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
}
 
.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
} 
   
</style>
</head>
<body>
<div class="container-fluid">
<div class="row">
  <!-- <div class="col-md-2 panelFijo" data-spy="affix" data-offset-top="60" data-offset-bottom="200"> -->
  <div class="col-md-2" data-offset-top="60" data-offset-bottom="200"> 
<?php 
// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=0;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {

                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos,aulas_grupos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;

if ($nivel_alumno==0) {

        // Busca servicios contratados
        $busca_servicios_ag=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? AND tarifa.tipo_neokids=3 ORDER BY servicios_contratados.fecha_inicio', 's', array($alumnos["id_alumno"]));
        
        if (db_num_rows($busca_servicios_ag)!=0) {
                $nivel_alumno=4;
                }

        $busca_servicios_mn=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? AND tarifa.tipo_neokids=4 ORDER BY servicios_contratados.fecha_inicio', 's', array($alumnos["id_alumno"]));
        
        if (db_num_rows($busca_servicios_mn)!=0) {
                $nivel_alumno=5;
                }
        
        }

$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

for ($c_dias_alu=1; $c_dias_alu<=$dias_faltan;$c_dias_alu++) {
$coletilla="";
if ($dias_faltan>1) {
        $coletilla=", Día: ".$c_dias_alu;
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
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 

                }
        }
}

// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=1;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos,aulas_grupos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

for ($c_dias_alu=1; $c_dias_alu<=$dias_faltan;$c_dias_alu++) {
$coletilla="";
if ($dias_faltan>1) {
        $coletilla=", Día: ".$c_dias_alu;
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
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 
                }
        }
}

// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=2;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;                        
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos,aulas_grupos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

for ($c_dias_alu=1; $c_dias_alu<=$dias_faltan;$c_dias_alu++) {
$coletilla="";
if ($dias_faltan>1) {
        $coletilla=", Día: ".$c_dias_alu;
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
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 

                }
        }
}

// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=3;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
              
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;                        
                        }
                        
// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos,aulas_grupos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

for ($c_dias_alu=1; $c_dias_alu<=$dias_faltan;$c_dias_alu++) {
$coletilla="";
if ($dias_faltan>1) {
        $coletilla=", Día: ".$c_dias_alu;
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
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        }        

                }
        }
}

// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=4;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos,aulas_grupos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

for ($c_dias_alu=1; $c_dias_alu<=$dias_faltan;$c_dias_alu++) {
$coletilla="";
if ($dias_faltan>1) {
        $coletilla=", Día: ".$c_dias_alu;
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
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 

                }
        }
}

// ¡¡IMPORTANTE!! Falta hacer la comprobación de fechas... alta, baja, etc...
$nivel_actual=5;
// $busca_alumnos=mysql_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(mysql_error());

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_curso=$id_curso_actual AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

while($alumnos=db_fetch_array($busca_alumnos)) {
                
                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d",$fecha_time_stamp))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                                        $contador_matriculados_prox++;
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos,aulas_grupos WHERE aulas_alumnos_grupos.id_grupo=aulas_grupos.id_grupo AND aulas_alumnos_grupos.id_alumno=? AND aulas_alumnos_grupos.id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

$contador_ocupacion=$contador_ocupacion+$alumnos["num_dias_asiste"];

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;
$total_por_tipo[$nivel_alumno]=$total_por_tipo[$nivel_alumno]+$alumnos["num_dias_asiste"];

$contador_matriculados++;
if (db_num_rows($busca_grupo_alumno)<$alumnos["num_dias_asiste"]) {

$dias_faltan=$alumnos["num_dias_asiste"]-db_num_rows($busca_grupo_alumno);

for ($c_dias_alu=1; $c_dias_alu<=$dias_faltan;$c_dias_alu++) {
$coletilla="";
if ($dias_faltan>1) {
        $coletilla=", Día: ".$c_dias_alu;
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
                $edad_alumno=$edad_alumno." años";
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 
                }
        }
}
?>
<h5><span class='pull-right'><?=$contador_matriculados;?></span>Matriculados:</h5>
<h6><span class='pull-right'>Próx. inc.: <?=$contador_matriculados_prox?></span>Actuales: <?=$contador_matriculados-$contador_matriculados_prox?></h6>  
<br />
<h5><span class='pull-right'><?=$contador_ocupacion;?></span>Sesiones contratadas(1):</h5>
<h6><span class='pull-right'>Próx. inc.: <?=$contador_ocupacion_prox?></span>Actuales: <?=$contador_ocupacion-$contador_ocupacion_prox?></h6>  
<br />
<h5>Sesiones por tipo</h5>
<?php 
for ($c_tipos=0;$c_tipos<count($array_listas_de_espera);$c_tipos++) {
                echo("<h6><span class='pull-right'>" . $total_por_tipo[$c_tipos] . "</span>" . $array_listas_de_espera[$c_tipos] . ":</h6>");
        }

?>
</div> 
  <div class="col-md-10">
     <table class="table">
     <tr>
        <td colspan="2"><a class="btn btn-primary" href="ocupacion.php?fecha=<?=$dia_inicio_semana-(7*86400);?>"><i class="fa fa-angle-left"></i> Semana Anterior</a> <a class="btn btn-primary" href="ocupacion.php?fecha=<?=$timestamp_anterior;?>"><i class="fa fa-angle-double-left"></i> Mes Anterior</a></td>
        <td colspan="3" align="center"><?=$nombre_monitor_seleccionado?><h4>Semana del <?=date("d/m/Y",$dia_inicio_semana);?> al <?=date("d/m/Y",$dia_inicio_semana+(86400*6));?></h4><h5><form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
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
                <td colspan="2" align="right"><a class="btn btn-primary" href="ocupacion.php?fecha=<?=$timestamp_siguiente;?>">Mes Siguiente <i class="fa fa-angle-double-right"></i></a> <a class="btn btn-primary" href="ocupacion.php?fecha=<?=$dia_inicio_semana+(7*86400);?>">Semana Siguiente <i class="fa fa-angle-right"></i></a></td>
     </tr>
     <tr>
        <td>Lunes<br><?=date("d/m/Y",$dia_inicio_semana);?></td>
        <td>Martes<br><?=date("d/m/Y",$dia_inicio_semana+(86400*1));?></td>
        <td>Miercoles<br><?=date("d/m/Y",$dia_inicio_semana+(86400*2));?></td>
        <td>Jueves<br><?=date("d/m/Y",$dia_inicio_semana+(86400*3));?></td>
        <td>Viernes<br><?=date("d/m/Y",$dia_inicio_semana+(86400*4));?></td>
        <td>Sábado<br><?=date("d/m/Y",$dia_inicio_semana+(86400*5));?></td>
        <td>Domingo<br><?=date("d/m/Y",$dia_inicio_semana+(86400*6));?></td>
     </tr>
     <?php 
     // $busca_turnos=mysql_query("SELECT hora_inicio,hora_fin FROM aulas_grupos WHERE dia=1 AND id_curso=$id_curso_actual ORDER BY hora_inicio");

         $contador_grupos=0;

     //while($turnos=mysql_fetch_array($busca_turnos))
     for ($turnos=1;$turnos<=24;$turnos++)
     {
     $contador_grupos++;

     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {
     
     $busca_hora_inicio=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual AND dia=".$c_dia);
     
     if (db_num_rows($busca_hora_inicio)!=0) {
     
            $datos_grupo_inicio=db_fetch_array($busca_hora_inicio);
            
            $nombre_horario_mostrado=$dia_sem_largo[$c_dia]." de ".date("H:i",strtotime($datos_grupo_inicio["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo_inicio["hora_fin"]));
            
 
             }
     
     }

     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {
     
     $busca_grupos=db_query("SELECT * FROM aulas_grupos WHERE orden=".$contador_grupos." AND id_curso=$id_curso_actual AND dia=".$c_dia);
     
     if (db_num_rows($busca_grupos)!=0) {
     
     $datos_grupo=db_fetch_array($busca_grupos);
     
     // Comprueba si es festivo
     $fecha_dia_sel=$dia_inicio_semana+(($c_dia-1)*86400);
                  
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
         
         // $busca_alumnos_grupo=mysql_query("SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_curso=$id_curso_actual AND aulas_alumnos_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_dia_sel)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel)."'") or die(mysql_error());
         
         $busca_alumnos_grupo=db_query("SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_curso=$id_curso_actual AND aulas_alumnos_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel)."'") or die(db_error());
         
        $num_alumnos_subgrupo=db_num_rows($busca_alumnos_grupo);
         
		$busca_alumnos_grupo_proximoI=db_query("SELECT * FROM usuarios,aulas_alumnos_grupos,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_grupos.id_alumno=alumnos.id_alumno AND alumnos.tipo_baja=0 AND aulas_alumnos_grupos.id_curso=$id_curso_actual AND aulas_alumnos_grupos.id_subgrupo=$nivel_actual AND aulas_alumnos_grupos.id_grupo=".$datos_grupo["id_grupo"]." AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_dia_sel)."' AND alumnos.fecha_matricula>'".date("Y-m-d",$fecha_dia_sel)."'") or die(db_error());
         
         $num_alumnos_proximoI=db_num_rows($busca_alumnos_grupo_proximoI);         
         
         // Busca alumnos grupo en el otro horario
         $busca_alumnos_grupo_ext=db_query("SELECT id_grupo FROM aulas_alumnos_lapices_grupos WHERE id_grupo=".$datos_grupo["id_grupo"]." AND id_subgrupo=$nivel_actual");
         
         $num_alumnos_subgrupo_ext=db_num_rows($busca_alumnos_grupo_ext);
         
         $nombre_horario_mostrado=$dia_sem_largo[$c_dia]." de ".date("H:i",strtotime($datos_grupo["hora_inicio"]))." a ".date("H:i",strtotime($datos_grupo["hora_fin"]));
                                           
         $capacidad_total=$capacidad_total+$capacidad_subgrupo;
         $matriculados_total=$matriculados_total+$num_alumnos_subgrupo;
		 $matriculados_por_tipos[$datos_subgrupo["tipo_subgrupo"]]=$matriculados_por_tipos[$datos_subgrupo["tipo_subgrupo"]]+$num_alumnos_subgrupo;
		 $capacidad_por_tipos[$datos_subgrupo["tipo_subgrupo"]]=$capacidad_por_tipos[$datos_subgrupo["tipo_subgrupo"]]+$capacidad_subgrupo;                                      
                                       
 				// Busca listas de espera
                $busca_lista_espera=db_query("SELECT * FROM lista_espera,usuarios,lista_espera_orden WHERE lista_espera.id_usuario=usuarios.id_usuario AND lista_espera.id_usuario=lista_espera_orden.id_usuario AND lista_espera.id_curso=$id_curso_actual AND lista_espera.dia_deseado_$c_dia=1 AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_inicio"]."' AND lista_espera.ini_horario_deseado_$c_dia<='".$datos_grupo["hora_fin"]."' AND lista_espera.fin_horario_deseado_$c_dia>='".$datos_grupo["hora_fin"]."' AND lista_espera_orden.id_grupo=".$datos_grupo["id_grupo"]." AND lista_espera_orden.no_mostrado=0 ORDER BY lista_espera_orden.orden") or die(db_error());                                  
                
                $total_lista_espera=db_num_rows($busca_lista_espera);                        
                
                $capacidadTotal[$c_dia]=$capacidadTotal[$c_dia]+$capacidad_subgrupo;
				$ocupacionTotal[$c_dia]=$ocupacionTotal[$c_dia]+$num_alumnos_subgrupo;
				$ocupacionExtTotal[$c_dia]=$ocupacionExtTotal[$c_dia]+$num_alumnos_subgrupo_ext;
				$enListaEsperaTotal[$c_dia]=$enListaEsperaTotal[$c_dia]+$total_lista_espera; 
				$ocupacionProximoI[$c_dia]=$ocupacionProximoI[$c_dia]+$num_alumnos_proximoI; 
				
				if ($datos_subgrupo["tipo_subgrupo"]==1) {
					$capacidadExternos[$c_dia]=$capacidadExternos[$c_dia]+$capacidad_subgrupo;
					$capacidadExternos[8]=$capacidadExternos[8]+$capacidad_subgrupo;
				}
				               
                                
                $capacidadTotal[8]=$capacidadTotal[8]+$capacidad_subgrupo;
				$ocupacionTotal[8]=$ocupacionTotal[8]+$num_alumnos_subgrupo;
				$ocupacionExtTotal[8]=$ocupacionExtTotal[8]+$num_alumnos_subgrupo_ext;
				$enListaEsperaTotal[8]=$enListaEsperaTotal[8]+$total_lista_espera; 				
				$ocupacionProximoI[8]=$ocupacionProximoI[8]+$num_alumnos_proximoI;

         		}
        	}
	 	}
     }
     ?>
     <tr>
     <?php 
     for ($c_dia=1;$c_dia<=$dias_maximo;$c_dia++) {
     ?>
	 	<td><small>
     		Capacidad total: <?=$capacidadTotal[$c_dia]?><br />
     		Neokids: <?=$capacidadTotal[$c_dia]-$capacidadExternos[$c_dia];?> &nbsp; Ext.: <?=$capacidadExternos[$c_dia];?><br />
     		Ocupación: <?=$ocupacionTotal[$c_dia]?><br />
     		Actual: <?=$ocupacionTotal[$c_dia]-$ocupacionProximoI[$c_dia]?><br />
     		Próx. Ini.:<?=$ocupacionProximoI[$c_dia]?><br />
     		Ocupación externos: <?=$ocupacionExtTotal[$c_dia]?><br />
     		Disponible total: <?=$capacidadTotal[$c_dia]-$ocupacionTotal[$c_dia]-$ocupacionExtTotal[$c_dia]?><br />
     		Neokids: <?=$capacidadTotal[$c_dia]-$capacidadExternos[$c_dia]-$ocupacionTotal[$c_dia]?> &nbsp; Ext.: <?=$capacidadExternos[$c_dia]-$ocupacionExtTotal[$c_dia]?><br />
     		<br />     		
     		En lista de espera: <?=$enListaEsperaTotal[$c_dia]?><br />
     		</small>
    	</td>
     <?php 
     }
     
     // Busca listas de espera
     $busca_lista_espera_real=db_query_params('SELECT * FROM lista_espera,lista_espera_orden WHERE lista_espera.id_usuario=lista_espera_orden.id_usuario AND lista_espera.id_curso=? AND lista_espera_orden.no_mostrado=0 GROUP BY lista_espera.id_usuario', 's', array($id_curso_actual)) or die(db_error());
     
     ?>
     </tr>
          <tr>
	 	<td colspan="3">
     		<b>Capacidad total:</b> <span class="pull-right"><?=$capacidadTotal[8]?></span><br />
     		<b>Ocupación Neokids(2):</b> <span class="pull-right"><?=$ocupacionTotal[8]?> <?php  if (($capacidadTotal[8]-$capacidadExternos[8])!=0) { echo("(".number_format(100*($ocupacionTotal[8]/($capacidadTotal[8]-$capacidadExternos[8])),2,",",".")." %)"); } ?></span><br />
     		&nbsp;&nbsp;&nbsp;<b>- Actual:</b> <span class="pull-right"><?=$ocupacionTotal[8]-$ocupacionProximoI[8]?></span><br />
     		&nbsp;&nbsp;&nbsp;<b>- Próximo Inicio:</b> <span class="pull-right"><?=$ocupacionProximoI[8]?></span><br />     		
     		<b>Disponible total:</b> <span class="pull-right"><?=$capacidadTotal[8]-$ocupacionTotal[8]-$ocupacionExtTotal[8]?></span><br /><br />     		
     		<b>En lista de espera(3):</b> <span class="pull-right"><?=$enListaEsperaTotal[8]?></span><br />
     		<b>Número real de personas en lista de espera:</b> <span class="pull-right"><?=db_num_rows($busca_lista_espera_real);?></span><br />
     		<br />
    	</td>
    	<td>&nbsp;</td>
    	<td colspan=3>
    		<b>Capacidad Neokids:</b> <span class="pull-right"><?=$capacidadTotal[8]-$capacidadExternos[8];?></span><br />
     		<b>Disponible Neokids:</b> <span class="pull-right"><?=$capacidadTotal[8]-$capacidadExternos[8]-$ocupacionTotal[8]?> <?php  if (($capacidadTotal[8]-$capacidadExternos[8])!=0) { echo("(".number_format(100*(($capacidadTotal[8]-$capacidadExternos[8]-$ocupacionTotal[8])/($capacidadTotal[8]-$capacidadExternos[8])),2,",",".")." %)"); }?></span><br />
     		<b>Capacidad Externos:</b> <span class="pull-right"><?=$capacidadExternos[8];?></span><br />
			<b>Ocupación externos:</b> <span class="pull-right"><?=$ocupacionExtTotal[8]?> <?php  if ($capacidadExternos[8]!=0) { echo("(".number_format(100*($ocupacionExtTotal[8]/$capacidadExternos[8]),2,",",".")." %)"); }?></span><br />     		
     		<b>Disponible Externos:</b> <span class="pull-right"><?=$capacidadExternos[8]-$ocupacionExtTotal[8]?> <?php  if ($capacidadExternos[8]!=0) { echo("(".number_format(100*(($capacidadExternos[8]-$ocupacionExtTotal[8])/$capacidadExternos[8]),2,",",".")." %)"); } ?></span><br />     	    </td>
     </tr>
     <tr>
     <td colspan="7"><i>(1) y (2): En algunas fechas el dato (1) puede ser diferente del dato (2) ya que el primero está calculado para la fecha <?=date("d/m/Y",$fecha_informe)?> mientras que el segundo refleja las altas y bajas producidas durante la semana.</i><br /><br /><i>(3): No se trata de personas distintas ya que una misma persona puede estar inscrita en la lista de espera de distintos grupos.</i></td>
     </tr>
     </table>
  </div>
</div>
</div>

</body>
</html>
