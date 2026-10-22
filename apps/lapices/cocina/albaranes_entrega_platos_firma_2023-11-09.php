<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$id_centro=$_GET["id_centro"];
$imprimir=$_GET["imprimir"];

if (strlen($id_centro)==0) {
	$id_centro = 0;	
}

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $inicio=time();  
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

// Comprueba si tiene fecha y temperatura

$fecha_albaran=date("Y-m-d",$fecha_time_stamp);

$busca_alabaran=db_query("SELECT * FROM cocina_albaranes_entrega WHERE fecha='".$fecha_albaran."';");

if (db_num_rows($busca_alabaran)==0) {

	if ($id_centro!=0) {
	
		exit("Los albaranes de cocina todavía no están disponibles.");
		
	} else {
	
		header("Location: intro_temperaturas.php?dia=".$fecha_time_stamp);	
		
	}

}
	
$datos_albaran=db_fetch_array($busca_alabaran);
	
$hora_salida=$datos_albaran["hora_salida"];
$temperatura_salida=app_safe_unserialize($datos_albaran["temperaturas_salida"]);

$nombres_platos=array();

$centrosConMenus = array();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<!-- <link rel="stylesheet" href="/js/signature_pad-master/css/signature-pad.css"> -->
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<style>        
    .wrapper {
      width: 100%;
      max-width: 500px;
      height: 300px;
      -moz-user-select: none;
      -webkit-user-select: none;
      -ms-user-select: none;
      user-select: none;
      border-radius: 10px;
      border-style: solid;
      border-width: 1px;
      border-color: #cdcdcd 
      margin-bottom: 10px!important;  
      background-color: white;   
    }
    
    .signature-pad {
      left: 0;
      top: 0;
      width:100%;
      border-radius: 10px;
      max-width: 500px;
      height:300px;
      background-color: transparent;
    }

    .signature {
      left: 0;
      top: 0;
      width:100%;
      border-radius: 10px;
      max-width: 500px;
      background-color: white;
      border-radius: 10px;
      border-style: solid;
      border-width: 1px;
      border-color: #cdcdcd         
    }   
    </style>  
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="/js/signature_pad-master/js/signature_pad.umd.js"></script>
<script src="/js/signature_pad-master/js/app.js"></script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0; background-repeat: no-repeat" bordercolor="#111111" width="100%" id="AutoNumber1">
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center"> 
<?php 

$mostrar_alergenos=false;
if ($_GET["alergenos"]==1) {
        $mostrar_alergenos=true;
        }

$mostrar_ingredientes=false;
if ($_GET["ingredientes"]==1) {
        $mostrar_ingredientes=true;
        }
$omitir_merienda=0;
$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro");
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro="ESCUELA: ".$datos_centro["nombre_centro"];
                        
                        $razon_social=$datos_centro["razon_social"];
                        $nombre_centro_completo=$datos_centro["nombre_centro"];
                        $domicilio_centro_completo=$datos_centro["domicilio_centro"];
                        $CP_centro_completo=$datos_centro["CP_centro"];
                        $poblacion_centro_completo=$datos_centro["poblacion_centro"];
                        $telefono_centro_completo=$datos_centro["telefono_centro"];
                        $tope_maximo2=$centros["tope_maximo2"];
                        $subvencion2=$centros["subvencion2"];
                        $omitir_merienda=$datos_centro['omitir_merienda'];
                        }
echo("Omitir Merienda: ".$omitir_merienda);
$solo_escuela=$_GET["solo_escuela"];
if (strlen($solo_escuela)==0) {
        $solo_escuela=0;
        }

if ($id_centro!=0) {

	$solo_escuela=1;
	
}

$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");        

if ($omitir_merienda==0) {
        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");        
} else {
        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1");
}

$edad_menu=2;	

$estado_botones="";
if (date("d-m-Y",$fecha_time_stamp)!=date("d-m-Y")) {
        $estado_botones=" disabled";
        }
        
$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];
$hay_incidencias=false;

if ($solo_escuela==1) {
        $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.id_centro=$id_centro AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
        $busca_alumnos1=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos, escuelamunicipal.servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.id_centro=$id_centro AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
        } else {
        $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
        $busca_alumnos1=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos, escuelamunicipal.servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
        }

if ((db_num_rows($busca_alumnos)!=0) or (db_num_rows($busca_alumnos1)!=0)) {

        $totales_menu=array();
        $totales_platos=array();

        $totales_menu_escuelas=array();
        $totales_platos_escuelas=array();
        
        $totales_menu_aulas=array();
        $totales_platos_aulas=array();

        $totales_ingredientes=array();
        
        $usuario_anterior=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
                $no_merienda=$listado_alumnos["no_merienda"];
                
        // Comprueba si está inscrito en una actividad que anula comedor
        $busca_actividad=db_query("SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=".$listado_alumnos["id_usuario"]." AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

        $tipo_menu_alu=$listado_alumnos["tipo_menu"];

        $menu_especial_hoy=false;
        if (db_num_rows($busca_actividad)!=0) {
                $menu_especial_hoy=true;
                $tipo_menu_alu=1;
                }
        
        // if ($menu_especial_hoy==true) {
        
        if (($tipo_menu_alu<>2) and ($tipo_menu_alu>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

        // Consulta centro
        $busca_datos_centro=db_query("SELECT * FROM centros,centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=".$listado_alumnos["aula"]);
        $datos_centro=db_fetch_array($busca_datos_centro);
        
        $nombre_centro_alumno=$datos_centro["nombre_centro"];
        $nombre_aula_alumno=$datos_centro["nombre"];


        $nombre_centro_alumno=$datos_centro["nombre_centro"];
        
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

                if ($menu_especial_hoy==true) {
                
                        // Edad actual del niño en años con decimales
                        $edad_alumno_a=date("Y",$fecha_time_stamp)-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                        if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                $edad_alumno_a=$edad_alumno_a-1;
                                }
                        $edad_alumno_a_anos=$edad_alumno_a;
                        // Decimales
                        $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                        if ($mes_edad_alumno<0) {
                                $edad_alumno_a_meses=(12+$mes_edad_alumno);
                                $edad_alumno_a=$edad_alumno_a+round(((12+$mes_edad_alumno)/12),3);
                                } else {
                                $edad_alumno_a_meses=$mes_edad_alumno;
                                $edad_alumno_a=$edad_alumno_a+round(($mes_edad_alumno/12),3);
                                }
                
                        // Comprueba si es de regimen
                        $regimen=$listado_alumnos["comedor"];

                        $filtro_regimen=" AND menu_menus.regimen=2";

                        // Calcula el menú que le toca por edades
                        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_alumno_a$filtro_regimen");
                        $diferencia_actual=1000;
                        $id_menu_seleccionado=0;

                        while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                                if ($minimo_minimorum>$menu_apropiado["edad_ini_menu"]) {
                                        $minimo_minimorum=$menu_apropiado["edad_ini_menu"];
                                        }
                                $diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
                                if ($diferencia<=$diferencia_actual) {
                                        $diferencia_actual=$diferencia;
                                        $id_menu_seleccionado=$menu_apropiado["id_menu"];
                                        }
                                        }
                                        
                        $menu_seleccionado_hoy=$id_menu_seleccionado;
                        } else {
                        $menu_seleccionado_hoy=$listado_alumnos["menu_seleccionado_hoy"];
                        }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_seleccionado_hoy);
        if (db_num_rows($busca_datos_menu)!=0) {
                $id_menu_seleccionado=$menu_seleccionado_hoy;
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                
                // Comprueba los alimentos del menú y comprueba las alergias del niño
                
                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_seleccionado AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                $motivo_no_hay="";
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) { // Inicio bucle platos

                                        $procesar_plato=true;

                                        if ((substr($array_campos_platos[$c_platos],0,8)=="merienda") and ($no_merienda==1)) {
                                                $procesar_plato=false;
                                                }

                                        if ($procesar_plato==true) { // Inicio Procesar plato

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {
                                                $sumar_plato=true;
                                                $consulta_datos_plato=db_query("SELECT * FROM menu_platos WHERE id_plato=$codigo_plato") or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                        $numero_ingredientes=db_num_rows($busca_ingredientes);

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alimento NO introducido: ".app_url_text_utf8($ingredientes["nombre_alimento"]).", ingrediente del plato ".app_url_text_utf8($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alergia a $nombre_alergia: ".app_url_text_utf8($ingredientes["nombre_alimento"]).", ingrediente del plato ".app_url_text_utf8($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }
                                                                if ($sumar_plato==true) {
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=app_url_text_utf8($ingredientes["nombre_alimento"]);
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+$ingredientes["cantidad_racion"];
                                                                        }
                                                                }
                                                        if ($sumar_plato==true) {
                                                        $edad_ini_actual=$datos_plato["edad_ini_plato"];
                                                                if ($edad_ini_actual<2) {
                                                                        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
                                                                        } else {
                                                                        $edad_ini_actual=$edad_ini_actual." años";
                                                                        }
                                                                $edad_fin_actual=$datos_plato["edad_fin_plato"];
                                                                if ($edad_fin_actual<2) {
                                                                        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
                                                                        } else {
                                                                        if ($edad_fin_actual==100) {
                                                                                                $edad_fin_actual=">18";
                                                                                                }
                                                                        $edad_fin_actual=$edad_fin_actual." años";
                                                                        }
                                                                $edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                                                $totales_platos[$codigo_plato][1]=app_url_text_utf8($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                                $totales_platos[$codigo_plato][2]=$numero_ingredientes;
                                                                
                                                                $totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]=$totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]+1;
                                                                
                                                                $totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]=$totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]+1;
                                                                
                                                                }
                                                        }

                                                }
                                                        } // Fin procesar platos
                                                } // Fin bucle platos

                                        } else {
                                        $motivo_no_hay="Día no programado para el menú asignado: ".app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                                        }
                
                } else {
                $nombre_menu="Menú no asignado";
                }
        
        $totales_menu[$menu_seleccionado_hoy]=$totales_menu[$menu_seleccionado_hoy]+1;
        $totales_menu_escuelas[$menu_seleccionado_hoy][$listado_alumnos["centro_alumno"]]=$totales_menu_escuelas[$menu_seleccionado_hoy][$listado_alumnos["centro_alumno"]]+1;
        $totales_menu_aulas[$menu_seleccionado_hoy][$listado_alumnos["aula"]]=$totales_menu_aulas[$menu_seleccionado_hoy][$listado_alumnos["aula"]]+1;
        $centrosConMenus[$listado_alumnos["centro_alumno"]]=1;

        // $mostrar_listado=1;
        if (($motivo_no_hay!="") or ($mostrar_listado==1)) {
        $hay_incidencias=true;
        }
        
        $usuario_anterior=$listado_alumnos["id_usuario"];
                }
                
                // }
        }
        
        // Fin Lapices
        // Inicio Sant Pau

        if ($incluir_santpau_cocina==true) {

                $usuario_anterior=0;
                
                while($listado_alumnos=db_fetch_array($busca_alumnos1)){
                
                $no_merienda=$listado_alumnos["no_merienda"];

                // Comprueba si está inscrito en una actividad que anula comedor
                $busca_actividad=db_query("SELECT * FROM escuelamunicipal.servicios_consumidos_asistencia,escuelamunicipal.tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=".$listado_alumnos["id_usuario"]." AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

                $tipo_menu_alu=$listado_alumnos["tipo_menu"];

                $menu_especial_hoy=false;
                if (db_num_rows($busca_actividad)!=0) {
                        $menu_especial_hoy=true;
                        $tipo_menu_alu=1;
                        }

                // if ($menu_especial_hoy==true) {

                if (($tipo_menu_alu<>2) and ($tipo_menu_alu>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {
                // Consulta centro
                $busca_datos_centro=db_query("SELECT * FROM escuelamunicipal.centros,escuelamunicipal.centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=".$listado_alumnos["aula"]);
                $datos_centro=db_fetch_array($busca_datos_centro);

                $nombre_centro_alumno=$datos_centro["nombre_centro"];
                $nombre_aula_alumno=$datos_centro["nombre"];


                $nombre_centro_alumno=$datos_centro["nombre_centro"];

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

                        if ($menu_especial_hoy==true) {

                                // Edad actual del niño en años con decimales
                                $edad_alumno_a=date("Y",$fecha_time_stamp)-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                        $edad_alumno_a=$edad_alumno_a-1;
                                        }
                                $edad_alumno_a_anos=$edad_alumno_a;
                                // Decimales
                                $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                                if ($mes_edad_alumno<0) {
                                        $edad_alumno_a_meses=(12+$mes_edad_alumno);
                                        $edad_alumno_a=$edad_alumno_a+round(((12+$mes_edad_alumno)/12),3);
                                        } else {
                                        $edad_alumno_a_meses=$mes_edad_alumno;
                                        $edad_alumno_a=$edad_alumno_a+round(($mes_edad_alumno/12),3);
                                        }

                                // Comprueba si es de regimen
                                $regimen=$listado_alumnos["comedor"];

                                $filtro_regimen=" AND menu_menus.regimen=2";

                                // Calcula el menú que le toca por edades
                                $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_alumno_a$filtro_regimen");
                                $diferencia_actual=1000;
                                $id_menu_seleccionado=0;

                                while($menu_apropiado=db_fetch_array($busca_menu_apropiado)){
                                        if ($minimo_minimorum>$menu_apropiado["edad_ini_menu"]) {
                                                $minimo_minimorum=$menu_apropiado["edad_ini_menu"];
                                                }
                                        $diferencia=$edad_alumno-$menu_apropiado["edad_ini_menu"];
                                        if ($diferencia<=$diferencia_actual) {
                                                $diferencia_actual=$diferencia;
                                                $id_menu_seleccionado=$menu_apropiado["id_menu"];
                                                }
                                                }

                                $menu_seleccionado_hoy=$id_menu_seleccionado;
                                } else {
                                $menu_seleccionado_hoy=$listado_alumnos["menu_seleccionado_hoy"];
                                }

                // Consulta el nombre del menú
                $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_seleccionado_hoy);

                if (db_num_rows($busca_datos_menu)!=0) {
                        $id_menu_seleccionado=$menu_seleccionado_hoy;
                        $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                        $nombre_menu=app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);

                        // Comprueba los alimentos del menú y comprueba las alergias del niño

                                        // Comprueba si ese día hay comida programada
                                        $hay_comida=false;

                                        $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_seleccionado AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                        $motivo_no_hay="";

                                        if (db_num_rows($busca_comida_ese_dia)!=0) {
                                                $hay_comida=true;
                                                $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                                for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) { // Inicio bucle platos

                                                $procesar_plato=true;

                                                if ((substr($array_campos_platos[$c_platos],0,8)=="merienda") and ($no_merienda==1)) {
                                                        $procesar_plato=false;
                                                        }

                                                if ($procesar_plato==true) { // Inicio Procesar plato

                                                $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                                if ($codigo_plato!=0) {
                                                        $sumar_plato=true;
                                                        $consulta_datos_plato=db_query("SELECT * FROM menu_platos WHERE id_plato=$codigo_plato") or die(db_error());

                                                        if (db_num_rows($consulta_datos_plato)!=0) {
                                                                $datos_plato=db_fetch_array($consulta_datos_plato);
                                                                                                                        
                                                                // Comprueba platos
                                                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                                $numero_ingredientes=db_num_rows($busca_ingredientes);

                                                                while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                                for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                        if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                                $hay_comida=false;
                                                                                                $sumar_plato=false;
                                                                                                $motivo_no_hay=$motivo_no_hay."Alimento NO introducido: ".app_url_text_utf8($ingredientes["nombre_alimento"]).", ingrediente del plato ".app_url_text_utf8($datos_plato["nombre_plato"])."<br>";
                                                                                                }
                                                                                        }

                                                                                for ($c=0;$c<count($array_alergias);$c++) {
                                                                                        if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                                $nombre_alergia=$nombres_alergias[$c];
                                                                                                $hay_comida=false;
                                                                                                $sumar_plato=false;
                                                                                                $motivo_no_hay=$motivo_no_hay."Alergia a $nombre_alergia: ".app_url_text_utf8($ingredientes["nombre_alimento"]).", ingrediente del plato ".app_url_text_utf8($datos_plato["nombre_plato"])."<br>";
                                                                                                }
                                                                                        }
                                                                        if ($sumar_plato==true) {
                                                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=app_url_text_utf8($ingredientes["nombre_alimento"]);
                                                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                                                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                                                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
                                                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+$ingredientes["cantidad_racion"];
                                                                                }
                                                                        }
                                                                if ($sumar_plato==true) {
                                                                $edad_ini_actual=$datos_plato["edad_ini_plato"];
                                                                        if ($edad_ini_actual<2) {
                                                                                $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
                                                                                } else {
                                                                                $edad_ini_actual=$edad_ini_actual." años";
                                                                                }
                                                                        $edad_fin_actual=$datos_plato["edad_fin_plato"];
                                                                        if ($edad_fin_actual<2) {
                                                                                $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
                                                                                } else {
                                                                                if ($edad_fin_actual==100) {
                                                                                                        $edad_fin_actual=">18";
                                                                                                        }
                                                                                $edad_fin_actual=$edad_fin_actual." años";
                                                                                }
                                                                        $edades="De ".$edad_ini_actual." a ".$edad_fin_actual;
                                                                        $totales_platos[$codigo_plato][1]=app_url_text_utf8($datos_plato["nombre_plato"])." ".$edades;
                                                                        $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                                        $totales_platos[$codigo_plato][2]=$numero_ingredientes;

                                                                        $totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]=$totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]+1;

                                                                        $totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]=$totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]+1;

                                                                        }
                                                                }

                                                        }
                                                                } // Fin procesar platos
                                                        } // Fin bucle platos

                                                } else {
                                                $motivo_no_hay="Día no programado para el menú asignado: ".app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                                                }

                        } else {
                        $nombre_menu="Menú no asignado";
                        }

                $totales_menu[$menu_seleccionado_hoy]=$totales_menu[$menu_seleccionado_hoy]+1;
                $totales_menu_escuelas[$menu_seleccionado_hoy][$listado_alumnos["centro_alumno"]]=$totales_menu_escuelas[$menu_seleccionado_hoy][$listado_alumnos["centro_alumno"]]+1;
                $totales_menu_aulas[$menu_seleccionado_hoy][$listado_alumnos["aula"]]=$totales_menu_aulas[$menu_seleccionado_hoy][$listado_alumnos["aula"]]+1;
                $centrosConMenus[$listado_alumnos["centro_alumno"]]=1;
                // $mostrar_listado=1;
                if (($motivo_no_hay!="") or ($mostrar_listado==1)) {
                $hay_incidencias=true;
                }

                $usuario_anterior=$listado_alumnos["id_usuario"];
                        }

                        // }
                }
        }
        // Fin Sant Pau
        
        // Cuenta los menús de educador
        $menus_edu=0;
        
        $filtro_educadoras_escuela = "";
        $filtro_albaranes_centros  = "";
        if ($id_centro!=0) {
	      
	     	$filtro_educadoras_escuela = " AND asistencia_docentes.id_centro_asistencia=".$id_centro; 
	        $filtro_albaranes_centros = " AND centros.id_centro=".$id_centro; 
	        
	    }
        
        $busca_menu_educadora=db_query("SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1".$filtro_educadoras_escuela." ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy");

        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {

        // Consulta centro
        $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$menu_educadora["id_centro_asistencia"]);
        if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $nombre_centro_educador=$datos_centro["nombre_centro"];
                } else {
                $nombre_centro_educador="No indicado";
                }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]);
        if (db_num_rows($busca_datos_menu)!=0) {
                $id_menu_seleccionado=$menu_educadora["menu_seleccionado_hoy"];
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                }
                
         $totales_menu[$menu_educadora["menu_seleccionado_hoy"]]=$totales_menu[$menu_educadora["menu_seleccionado_hoy"]]+1;
         $totales_menu_escuelas[$menu_educadora["menu_seleccionado_hoy"]][$menu_educadora["id_centro_asistencia"]]=$totales_menu_escuelas[$menu_educadora["menu_seleccionado_hoy"]][$menu_educadora["id_centro_asistencia"]]+1;
         $centrosConMenus[$menu_educadora["id_centro_asistencia"]]=1;
         
                // Comprueba los alimentos del menú y comprueba las alergias del niño

                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;
                                $observaciones_edu="";
                                $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]." AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                                $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                                if ($codigo_plato!=0) {
                                                        $totales_platos[$codigo_plato][1]=app_url_text_utf8($datos_plato["nombre_plato"])." ".$edades;
                                                        $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                        $totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]=$totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]+1;
                                                        }

                        $busca_ingredientes_plato=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=".$codigo_plato." ORDER BY menu_alimentos.id_alimento;");

                        while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=app_url_text_utf8($ingredientes["nombre_alimento"]);
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+$ingredientes["cantidad_racion"];

                                }
                                                        
                                                }
                                        } else {
                                        $observaciones_edu="Día no programado para el menú seleccionado";
                                        }

        $menus_edu++;
        }
        // Fin lapices
        // Inicio Sant Pau
        if ($incluir_santpau_cocina==true) {
                $busca_menu_educadora=db_query("SELECT * FROM escuelamunicipal.asistencia_docentes, escuelamunicipal.usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1".$filtro_educadoras_escuela." ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy");

                while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {

                // Consulta centro
                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$menu_educadora["id_centro_asistencia"]);
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);
                        $nombre_centro_educador=$datos_centro["nombre_centro"];
                        } else {
                        $nombre_centro_educador="No indicado";
                        }

                // Consulta el nombre del menú
                $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]);
                if (db_num_rows($busca_datos_menu)!=0) {
                        $id_menu_seleccionado=$menu_educadora["menu_seleccionado_hoy"];
                        $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                        $nombre_menu=app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                        }

                $totales_menu[$menu_educadora["menu_seleccionado_hoy"]]=$totales_menu[$menu_educadora["menu_seleccionado_hoy"]]+1;
                $totales_menu_escuelas[$menu_educadora["menu_seleccionado_hoy"]][$menu_educadora["id_centro_asistencia"]]=$totales_menu_escuelas[$menu_educadora["menu_seleccionado_hoy"]][$menu_educadora["id_centro_asistencia"]]+1;
                $centrosConMenus[$menu_educadora["id_centro_asistencia"]]=1;

                        // Comprueba los alimentos del menú y comprueba las alergias del niño

                                        // Comprueba si ese día hay comida programada
                                        $hay_comida=false;
                                        $observaciones_edu="";
                                        $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]." AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                        if (db_num_rows($busca_comida_ese_dia)!=0) {

                                                $hay_comida=true;
                                                $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                                for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                                        if ($codigo_plato!=0) {
                                                                $totales_platos[$codigo_plato][1]=app_url_text_utf8($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                                $totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]=$totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]+1;
                                                                }

                                $busca_ingredientes_plato=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=".$codigo_plato." ORDER BY menu_alimentos.id_alimento;");

                                while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=app_url_text_utf8($ingredientes["nombre_alimento"]);
                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+$ingredientes["cantidad_racion"];

                                        }

                                                        }
                                                } else {
                                                $observaciones_edu="Día no programado para el menú seleccionado";
                                                }

                $menus_edu++;
                }
        }
        // Fin Sant Pau
        
        }  
        // Inicio Escuelas Externas
        
        // datos centros externos
        $buscaCentrosExt=db_query("SELECT * FROM centros;");
        $omitirMeriendaCentro=array();
        while($datosCentrosExt=db_fetch_array($buscaCentrosExt)) {
                $omitirMeriendaCentro[$datosCentrosExt["id_centro"]]=$datosCentrosExt["omitir_merienda"];
        }

        $buscaMenusExternos = db_query("SELECT * FROM menu_pedidos_externos WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."' AND cantidad<>0 AND confirmado=1 ORDER BY id_centro,id_menu;");
        
        while($menusExternos=db_fetch_array($buscaMenusExternos)) {

                if ($omitirMeriendaCentro[$menusExternos['id_centro']]==0) {
                        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");
                } else {
                        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1");
                }                

        	$numero_menus_extras = $menusExternos["cantidad"]; 
        
        	if ($numero_menus_extras!=0) {
            	// Agrega ingredientes de extras
               
        		// Consulta el nombre del menú
        		$id_menu_seleccionado=$menusExternos["id_menu"];

	         	$totales_menu[$id_menu_seleccionado]=$totales_menu[$id_menu_seleccionado]+$numero_menus_extras;
    	     	$totales_menu_escuelas[$id_menu_seleccionado][$menusExternos["id_centro"]]=$totales_menu_escuelas[$id_menu_seleccionado][$menusExternos["id_centro"]]+$numero_menus_extras;

        	    // Comprueba si ese día hay comida programada
	            $hay_comida=false;
    	        $observaciones_edu="";
        	    $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=".$id_menu_seleccionado." AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
            	if (db_num_rows($busca_comida_ese_dia)!=0) {

                	$hay_comida=true;
	                $datos_comida=db_fetch_array($busca_comida_ese_dia);

    	        	for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                        
                        if ($codigo_plato!=0) {
                            $totales_platos[$codigo_plato][1]=app_url_text_utf8($datos_plato["nombre_plato"])." ".$edades;
                            $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+$numero_menus_extras;
                        	$totales_platos_escuelas[$codigo_plato][$menusExternos["id_centro"]]=$totales_platos_escuelas[$codigo_plato][$menusExternos["id_centro"]]+$numero_menus_extras;
                            $centrosConMenus[$menusExternos["id_centro"]]=1;
                        }

                        $busca_ingredientes_plato=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=".$codigo_plato." ORDER BY menu_alimentos.id_alimento;");

                        while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                        	$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=app_url_text_utf8($ingredientes["nombre_alimento"]);
                            $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                            $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                            $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
                            $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+($ingredientes["cantidad_racion"]*$numero_menus_extras);

                    	}

                	}
                } else {
                       $observaciones_edu="Día no programado para el menú seleccionado";
            	}
        
        	}  // Fin ingredientes menú        
        }
        // Fin escuelas externas        		
        
        $busca_centros=db_query("SELECT * FROM centros WHERE id_centro=".$id_centro." ORDER BY nombre_centro;");
        
        $num_c=db_num_rows($busca_centros);
        
        $contador_c=0;
        
        while($centros=db_fetch_array($busca_centros)) {

                $omitir_merienda=$centros["omitir_merienda"];
                
                if ($omitir_merienda==0) {
                        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");        
                } else {
                        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1");
                }

            if ($centrosConMenus[$centros["id_centro"]]==1) {

        ?>
        <div><center>
        <br>
        <table width=100% border=0>
        <tr>
        <td align="center" width=25%><img src="/pdf/logo_albaran_comida.png" height="80"></td>
        <td align="center"><font size=2><b>Albarán de entrega de menús servidos</b><br />ZAEBSAL SL<br>C/ Rio Nervión, 21-23 46025 Valencia<br>
Teléfono: 96 338 44 08<br>Nº de autorización: 26.09175/4</font></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="right" width=25%><font size=2 face="<?= $fuente_general; ?>"><b>Fecha:</b></font></td>
        <td bgcolor="#ffffff" align="left" width=20%><font size=2 face="<?= $fuente_general; ?>"> <?=date("d/m/Y",$fecha_time_stamp)?></font></td>
        <td bgcolor="#efefef" align="right" width=20%><font size=2 face="<?= $fuente_general; ?>"><b>Hora de entrega:</b></font></td>
        <td bgcolor="#ffffff" align="left" width=35%><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>        
        </tr>
        <tr>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cliente:</b></font></td>
        <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"> <?=$centros["nombre_centro"]?></font></td>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Dirección entrega:</b></font></td>
        <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>" ><?=$centros["domicilio_centro"]?>. <?=$centros["poblacion_centro"]?></font></td>
        </tr>
        <tr>
	        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha de elaboración:</b></font></td>
			<td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>" ><?=date("d/m/Y",$fecha_time_stamp)?></font></td>
			<td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha de consumo:</b></font></td>
			<td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"> Consumo inmediato</font></td>
        </tr>
		<tr>
	        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Hora de salida:</b></font></td>
			<td bgcolor="#ffffff" align="left" colspan="3"><font size=2 face="<?= $fuente_general; ?>"> <?=$hora_salida?></font></td>
        </tr>
        </tr>
        </table>
        <table cellpadding=0 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre menú</b></font></td>
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Raciones</b></font></td>
        </tr>
        <?php 
        $consulta_datos_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu;") or die(db_error());
        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
        if ($totales_menu_escuelas[$datos_menus["id_menu"]][$centros["id_centro"]]>0) {

        $busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=".$datos_menus["id_menu"]." AND fecha='".date("Y-m-d",$fecha_time_stamp)."'");
if (db_num_rows($busca_platos_dia)!=0) {

                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                // Edita el nuevo día
                $actualizar=false;
                $id_dia_menu=$datos_platos_dia["id_dia"];
                $comida1=$datos_platos_dia["comida1"];
                $comida2=$datos_platos_dia["comida2"];
                $comida3=$datos_platos_dia["comida3"];
                $comida4=$datos_platos_dia["comida4"];
                $comida5=$datos_platos_dia["comida5"];
                $postre1=$datos_platos_dia["postre1"];
                $merienda1=$datos_platos_dia["merienda1"];
                $merienda2=$datos_platos_dia["merienda2"];

                if ($omitir_merienda==1) {
                        $merienda1=0;        
                        $merienda2=0;        
                }

        $datos_plato_hoy="<table width=100% border=1 cellpadding=0 cellspacing=1 style='border-collapse: collapse' bordercolor='dcdcdc'>";


		foreach($array_campos_platos as $platoActual) {

		$comidaActual=$datos_platos_dia[$platoActual];

        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comidaActual ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                $id_plato=$datos_comida["id_plato"];
                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                
                $ingredientes="";
                $alergenos="";

                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                    $ingredientes.=app_url_text_utf8($datos_ingredientes["nombre_alimento"]).", ";

                    $contadorAlergias=0;
                    foreach ($array_alergias as $alergia) {
                        if (($datos_ingredientes[$alergia]==1) and (in_array($lista_alergenos, $array_nombres_alergias[$contadorAlergias])==false))  {
                            $alergiasYa[$alergia]=1;
                            $lista_alergenos[$alergia]=$array_nombres_alergias[$contadorAlergias];
                        }
                        $contadorAlergias++;
                    }
                }
                
                if ($mostrar_ingredientes==true) {
                    $num_cols++;
                    if ($ingredientes!="") {
                        $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                    }                      
                }              

                if ($mostrar_alergenos==true) {
                    $num_cols++;
                    $alergenos = implode(", ", $lista_alergenos);        
                }
                       
                $anchura_celdas=50;       
				if ($num_cols>0) {	   
	                $anchura_celdas=floor(100/$num_cols);
                }

				
				$nombres_platos[$comidaActual]=app_url_text_utf8($datos_comida["nombre_plato"]);
				
                $datos_plato_hoy.="<tr valign=top><td width=".$anchura_celdas."%><font size=2>".app_url_text_utf8($datos_comida["nombre_plato"])."</font></td>";
                if ($mostrar_ingredientes==true) {
                        $datos_plato_hoy.="<td width=".$anchura_celdas."%><font size=2><b>Ingredientes:</b><br><font size=1>".$ingredientes."</font></font></td>";
                        }
                if ($mostrar_alergenos==true) {
                        $datos_plato_hoy.="<td width=".$anchura_celdas."%><font size=2><b>Alérgenos:</b><br><font size=1>".$alergenos."</font></font></td>";
                        }
                $arrayConservacion[$comidaActual]=ucfirst(app_url_text_utf8($datos_comida["instrucciones_conservacion"]));
                /*        
                $datos_plato_hoy.="<td width=".$anchura_celdas."%><font size=1>";
                $datos_plato_hoy.="<b>Conservación:</b> ".ucfirst(app_url_text_utf8($datos_comida["instrucciones_conservacion"]))."</font></td></tr>";
                */
                }
                
                
        }        

        $datos_plato_hoy.="</table>";
        }

        ?>
        <tr>
        <td bgcolor="#ffffff" align="left" width=90%><font size=2 face="<?= $fuente_general; ?>"><b><?=app_url_text_utf8($datos_menus["nombre_menu"])?></b><br><?=$datos_plato_hoy?></font></td>
        <td bgcolor="#ffffff" align="right" width=5%><?=number_format($totales_menu_escuelas[$datos_menus["id_menu"]][$centros["id_centro"]],0,",",".");?></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table></td>
</tr>
</table>
<br>
<?php
// Busca firma registrada
$buscaFirma=db_query("SELECT * FROM albaranes_entrega_comida WHERE fecha='".date("Y-m-d",$inicio)."' AND id_centro=".$id_centro.";");

$temperatura_entrada=array();
if (db_num_rows($buscaFirma)!=0) {
    $datosFirma=db_fetch_array($buscaFirma);
    $temperatura_entrada=app_safe_unserialize($datosFirma["temperaturas_entrada"]);
}

?>
<table width="100%" cellpadding=0 cellspacing=0 border=0>
	<tr valign="top">
		<td width="100%">
			<table cellpadding=2 cellspacing=0 border=0 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100% align=center>
				<tr>
                                   
    <?php

// Busca firma registrada
$buscaFirma=db_query("SELECT * FROM albaranes_entrega_comida WHERE fecha='".date("Y-m-d",$inicio)."' AND id_centro=".$id_centro." AND firma IS NOT NULL;");

if (db_num_rows($buscaFirma)!=0) {
    $datosFirma=db_fetch_array($buscaFirma);

    $horaRecibi = $datosFirma["hora_entrega"];
    $firmaRecibi = $datosFirma["firma"];
    $observacionesRecibi = $datosFirma["observaciones"];
    if (strlen($observacionesRecibi)!=0) {
        $observacionesRecibi = '<b>Observaciones:</b> '.$observacionesRecibi;
    }
    ?>
        <td width="50%" valign="top">   
                        <small>Firmado el <?=date("d/m/Y",strtotime($horaRecibi));?> a las <?=date("H:i:s",strtotime($horaRecibi));?>.</small>
                <br />
                <?=$observacionesRecibi?>
        </td>
        <td width="50%" align="right" valign="top">         
                <div>
                <img id="signature-pad" class="signature" src="<?=$firmaRecibi;?>">
                </div>
    <?php

} else {

    // '/js/signature_pad-master/js/signature_pad.umd.js'
    // '/js/signature_pad-master/css/signature-pad.css'

    ?>
        <td width="50%" valign="top">  
                <legend>Observaciones:</legend>               
                <textarea id="observaciones" name="observaciones" cols="62" rows="4"><?=$observacionesRecibi?></textarea>      
        </td>
        <td width="50%" valign="top">  
        <?php
        if ($imprimir==1) {
        ?>
                <legend>Firma:</legend>               
                <textarea id="observaciones" name="observaciones" cols="62" rows="4"><?=$observacionesRecibi?></textarea>   
        <?php        
        } else {
        ?>

        <div class="col-md-12 col-sm-12 col-xs-12">
                <legend>Firma</legend>
                <div class="row" style="margin-bottom: 10px">
                <div class="col-md-12 col-sm-12 col-xs-12 text-right">
                        <div class="wrapper">
                        <canvas id="signature-pad" class="signature-pad"></canvas>
                        </div>
                </div>
                </div>
                <div class="row" style="margin-bottom: 10px">
                <div class="col-md-12 col-sm-12 col-xs-12" id="resultado"></div>
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <button id="save-png" class="btn btn-sm btn-info">Guardar Firma</button>
                        <button id="clear" class="btn btn-sm btn-danger">Limpiar firma</button>
                </div>
                </div>
        </div>
    <script>

        var canvas = document.getElementById('signature-pad');

        // Adjust canvas coordinate space taking into account pixel ratio,
        // to make it look crisp on mobile devices.
        // This also causes canvas to be cleared.
        function resizeCanvas() {
            // When zoomed out to less than 100%, for some very strange reason,
            // some browsers report devicePixelRatio as less than 1
            // and only part of the canvas is cleared then.
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }

        window.onresize = resizeCanvas;
        resizeCanvas();

        var signaturePad = new SignaturePad(canvas, {
            // backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
        });

        document.getElementById('save-png').addEventListener('click', function () {

            if (signaturePad.isEmpty()) {
                return alert("Por favor, firma en el panel antes de finalizar.");
            }

            var data = signaturePad.toDataURL('image/png');

            // Guarda Firma
            var valoresPost = {
                "firmaRecibi" : data
            };
            $.ajax({
                data:  valoresPost,
                url:   '/cocina/guardar_firma.php?dia=<?=$inicio?>&id_centro=<?=$id_centro?>&observaciones='+$("#observaciones").val(),
                type:  'post',
                beforeSend: function () {
                    $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                    var esOk = response.substr(0, 2);
                    if (esOk=='OK') {
                        // Si OK desactiva el panel de firma
                        $("#save-png").prop("disabled",true);
                        $("#clear").prop("disabled",true);
                        $("#signature-pad").prop("disabled",true);
                    }
                    $("#resultado").html('<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                        '    <span class="alert-inner--icon"><i class="ni ni-like-2"></i></span>' +
                        '    <span class="alert-inner--text">'+response+'</span>' +
                        '    <button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '        <span aria-hidden="true">&times;</span>' +
                        '    </button>');
                    if (confirm("¿Quieres guardar las temperaturas ahora?")) {
                        $("#temperaturas").submit();            
                    }
                }
            });


        });
        /*
        document.getElementById('save-jpeg').addEventListener('click', function () {
            if (signaturePad.isEmpty()) {
                return alert("Please provide a signature first.");
            }

            var data = signaturePad.toDataURL('image/jpeg');
            console.log(data);
            window.open(data);
        });

        document.getElementById('save-svg').addEventListener('click', function () {
            if (signaturePad.isEmpty()) {
                return alert("Please provide a signature first.");
            }

            var data = signaturePad.toDataURL('image/svg+xml');
            console.log(data);
            console.log(atob(data.split(',')[1]));
            window.open(data);
        });
        */
        document.getElementById('clear').addEventListener('click', function () {
            signaturePad.clear();
        });
        /*
        document.getElementById('undo').addEventListener('click', function () {
            var data = signaturePad.toData();
            if (data) {
                data.pop(); // remove the last dot or line
                signaturePad.fromData(data);
            }
        });
        */

    </script>
    <?php
}
?>   
<?php
}
?>             
            </td></tr>
			</table>
                        <form method="POST" action="intro_temperaturas_entrada1.php" name="temperaturas" id="temperaturas">
                                <input type="hidden" name="dia" value="<?=$fecha_time_stamp;?>">
                                <input type="hidden" name="id_centro" value="<?=$id_centro;?>">
                                <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc"	width="100%" align=center>
                                        <tr>
                                                <td><small><small><b>Temperaturas</b></small></small></td>
                                                <td><small><small><b>Salida</b></small></small></td>
                                                <td><small><small><b>Entrada</b></small></small></td>
                                                <td><small><small><b>Conservación</b></small></small></td>
                                        </tr>
                                        <?php
                                        $tabindex=1;
                                        foreach ($temperatura_salida as $clave => $valor) {
                                                if ($nombres_platos[$clave]) {
                                        ?>
                                        <tr>
                                                <td bgcolor="#ffffff" align="left" width=40%><small><small><?=$nombres_platos[$clave];?></small></small></td>
                                                <td bgcolor="#ffffff" align="right" width=10%><small><small><?=$temperatura_salida[$clave];?> ºC</small></small></td>
                                                <td bgcolor="#ffffff" align="right" width=10%>
                                                        <input tabindex="<?=$tabindex?>" size="6" type="text" name="temperatura_<?=$clave?>" id="temperatura_<?=$clave?>" value="<?=$temperatura_entrada[$clave];?>"> ºC
                                                </td>
                                                <td bgcolor="#ffffff" align="right" width=40%><small><small><?=$arrayConservacion[$clave]?></small></small></td>                                                
                                        </tr>
                                        <?php
                                                $tabindex++;
                                                }
                                        }
                                        if ($imprimir!=1) {
                                        ?>
                                        <tr>
                                                <td colspan="3" align="center">
                                                        <input type="submit" value="Guardar temperaturas">
                                                </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                </table>
                        </form>                        
		</td>
	</tr>
</table>	
<br>
</center></div>
<?php 
$contador_c++;
if ($contador_c<$num_c) {
?>
<div align="center" style="page-break-after: always;"></div>
<?php 
                }
            }
        }
if ($imprimir!=1) {
?>
<div align="center">
<button class="btn btn-lg btn-success" style="font-size: 24px;" onclick="window.location.href='/cocina/albaranes_entrega_platos_firma.php?id_centro=<?=$id_centro?>&dia=<?=$fecha_time_stamp?>&imprimir=1';">Imprimir</button> <button class="btn btn-lg btn-success" style="font-size: 24px;" onclick="window.location.href='/cocina/albaranes_entrega_seleccion.php?dia=<?=$fecha_time_stamp?>';"><< Volver</button>
</div>
<?php
        } else {
?>
<script>
        window.print();
        history.go(-1);
</script>
<?php
        }
?>
</body>
</html>
