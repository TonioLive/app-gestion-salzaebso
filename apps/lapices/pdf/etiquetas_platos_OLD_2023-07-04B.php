<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicio=$_GET["dia"];

if (strlen($inicio)==0) {
    $inicio=time();  
    $fecha_time_stamp=time();
} else {
    $fecha_time_stamp=$inicio;
}
 
$fecha_albaran=date("Y-m-d",$fecha_time_stamp);

$busca_alabaran=db_query("SELECT * FROM cocina_albaranes_entrega WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."';");

if (db_num_rows($busca_alabaran)!=0) {

    $datos_albaran=db_fetch_array($busca_alabaran);

    $temperatura_salida=unserialize($datos_albaran["temperaturas_salida"]);
    if (strlen($datos_albaran["numescuelas"])!=0) {
        $numescuelas=unserialize($datos_albaran["numescuelas"]);
    } else {
        $numescuelas=array();    
    }

} else {
    // No hay, busca platos

    $mostrar_alergenos=false; 
    $mostrar_ingredientes=false;
    $solo_escuela=0;
    
    $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");
              
    $mes=date("n",$fecha_time_stamp);
    $inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
    $inicio_mes_time_stamp=strtotime($inicio_mes);
    $diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
    $diasemana=date("w",$fecha_time_stamp);
    $dianombre=$dia_sem_largo[$diasemana];
    $mesnombre=$nombre_mes[$mes];
    $hay_incidencias=false;
    
    if ($solo_escuela==1) {
            $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.id_centro=$centro_actual AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
            $busca_alumnos1=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos, escuelamunicipal.servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.id_centro=$centro_actual AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
            } else {
            $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
            $busca_alumnos1=db_query("SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos, escuelamunicipal.servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno") or die(db_error());
            }
    
    if ((db_num_rows($busca_alumnos)!=0) or (db_num_rows($busca_alumnos1)!=0)) {
    
            $hayAlgo=true;
    
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
                                                                
                                                                    if (strlen($lista_ingredientes[$ingredientes["id_alimento"]])==0) {
                                                                        $lista_ingredientes[$ingredientes["id_alimento"]]=$ingredientes["nombre_alimento"];	
                                                                    }
                                                                
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
                                                                
                                                                    if (strlen($lista_ingredientes[$ingredientes["id_alimento"]])==0) {
                                                                        $lista_ingredientes[$ingredientes["id_alimento"]]=$ingredientes["nombre_alimento"];	
                                                                    }
                                                                
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
            // $mostrar_listado=1;
            if (($motivo_no_hay!="") or ($mostrar_listado==1)) {
            $hay_incidencias=true;
            }
    
            $usuario_anterior=$listado_alumnos["id_usuario"];
                    }
    
                    // }
            }
            // Fin Sant Pau
            
            // Cuenta los menús de educador
            $menus_edu=0;
            
            $busca_menu_educadora=db_query("SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1 ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy");
    
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
            $busca_menu_educadora=db_query("SELECT * FROM escuelamunicipal.asistencia_docentes, escuelamunicipal.usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1 ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy");
    
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
            // Fin Sant Pau
    }       
            // Inicio Escuelas Externas
                    
            $buscaMenusExternos = db_query("SELECT * FROM menu_pedidos_externos WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."' AND cantidad<>0 AND confirmado=1 ORDER BY id_centro,id_menu;");
                    
            while($menusExternos=db_fetch_array($buscaMenusExternos)) {
    
                    $hayAlgo=true;
    
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
   
    if ($hayAlgo==true) {
    
                    $busca_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 OR asociado=1 OR menu_externo=1 ORDER BY nombre_centro;");
                    
                    $num_c=db_num_rows($busca_centros);
                    
                    $contador_c=0;
                    
            while($centros=db_fetch_array($busca_centros)) {
            
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
        
            foreach($array_campos_platos as $platoActual) {
    
            $comidaActual=$datos_platos_dia[$platoActual];
    
            $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comidaActual ORDER BY nombre_plato");
            if (db_num_rows($busca_comida)!=0) {
                    $datos_comida=db_fetch_array($busca_comida);
    
                    $id_plato=$datos_comida["id_plato"];
 
                    $temperatura_salida[$id_plato] = "             ";
                    
                    }
                    
                    
            }        
    
            }
    
                    }
            }
    
    $contador_c++;
    
            }
    }

}

// --- FIN DATOS ---
$cantidades = $_GET["cantidades"];

if ($cantidades!=1) {
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
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link href="/inc/bootstrap-toggle-master/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="/inc/bootstrap-toggle-master/js/bootstrap-toggle.min.js"></script>

<script src="/js/datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="/js/datepicker/locales/bootstrap-datepicker.es.min.js" charset="UTF-8"></script>

</head>
        <body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
                <div align="center">
                        <form name="cantidades" methos="GET" action="<?=$_SERVER["PHP_SELF"]?>">
                                <input type="hidden" name="dia" value="<?=$_GET["dia"];?>">
                                <input type="hidden" name="cantidades" value="1">                                
                                <h1>Indica cuantas etiquetas quieres imprimir de cada plato</h1>
                                <table class="table table-bordered table-striped">
                                <?php
                                foreach ($temperatura_salida as $clave => $valor) {
                                        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$clave ORDER BY nombre_plato");
                                        if (db_num_rows($busca_comida)!=0) {
                                                $datos_comida=db_fetch_array($busca_comida);

                                                $edad_ini_actual=$datos_comida["edad_ini_plato"];
                                                if ($edad_ini_actual<2) {
                                                        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
                                                        } else {
                                                        $edad_ini_actual=$edad_ini_actual." años";
                                                        }
                                                $edad_fin_actual=$datos_comida["edad_fin_plato"];
                                                if ($edad_fin_actual<2) {
                                                        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
                                                        } else {
                                                        if ($edad_fin_actual==100) {
                                                                                $edad_fin_actual=">18";
                                                                                }
                                                        $edad_fin_actual=$edad_fin_actual." años";
                                                        }
                                                $edades="De ".$edad_ini_actual." a ".$edad_fin_actual;

                                                $cantidadEtiquetas=0;
                                                if ($numescuelas[$clave]) {
                                                        $cantidadEtiquetas=$numescuelas[$clave];
                                                }

                                                ?>
                                                <tr>
                                                        <td><?=app_url_text_utf8($datos_comida["nombre_plato"])?> <span class="text-info">(<?=$edades;?>)</span></td>
                                                        <td><input type="number" min="0" max="20" name="cantidad_<?=$clave?>" id="cantidad_<?=$clave?>" value="<?=$cantidadEtiquetas?>"></td>
                                                </tr>
                                                <?php
                                        }        
                                }
                                ?>
                                </table>
                                <button type="button" onclick="window.close();" class="btn btn-danger">Cancelar</button> <input type="submit" value="Imprimir etiquetas" class="btn btn-primary">
                        </form>
                </div>
        </body>
</html>
<?php

} else {

        error_reporting(7);

        include 'class.ezpdf.php';

        $pdf = new Cezpdf('a4','portrait');

        $pdf -> ezSetMargins(0,0,0,0);

        // Color de las lineas Negro
        $pdf->setStrokeColor('0.0','0.00','0.00','');

        $numeroFilas = 6;
        $numeroColumnas = 2;

        $filaActual = 0;
        $columnaActual = 0;

        // $alturaFilas = array(841.89,631.43,420.95,210.48); // 8 etiquetas por hoja
        $alturaFilas = array(829.13,693.07,557.01,420.95,284.88,148.82);
        $anchuraColumnas = array(0,297.64,595.28);
        $margenEtiqueta = 8;
        $etiquetaspagina = 12;

        // Calculamos numero de páginas
        $numPaginas = 1;
        $numeroEtiquetas=0;
        foreach ($temperatura_salida as $clave => $valor) {
                $numeroEtiquetas = $numeroEtiquetas + $_GET["cantidad_".$clave];
        }

        if ($numeroEtiquetas!=0) {
                $numPaginas = ceil($numeroEtiquetas/$etiquetaspagina);
        }

        $contadorPaginas=0;

        foreach ($temperatura_salida as $clave => $valor) {

        // Bucle numero de etiquetas
        $numeroEtiquetas = $_GET["cantidad_".$clave];

        for($contadorEtiquetas=1;$contadorEtiquetas<=$numeroEtiquetas;$contadorEtiquetas++) {

                // Busca plato
                $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$clave ORDER BY nombre_plato");
                if (db_num_rows($busca_comida)!=0) {

                        $datos_comida=db_fetch_array($busca_comida);

                        $id_plato=$datos_comida["id_plato"];
                        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$id_plato ORDER BY nombre_alimento;");
                        

                        $alergenos="";
                        $ingredientes="";
                        $lista_alergenos=array();

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

                        if ($ingredientes!="") {
                                $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                        }

                        $alergenos = implode(", ", $lista_alergenos);        

                        if (strlen($alergenos)==0) {
                        $alergenos = "No contiene alergenos conocidos";
                        }        
                        
                        $nombres_platos[$comidaActual]=app_url_text_utf8($datos_comida["nombre_plato"]);

                        $pdf->selectFont('./fonts/Helvetica');
                        $pdf->ezSetY($alturaFilas[$filaActual]-$margenEtiqueta);
                        $pdf->ezText(app_url_text_utf8("ZAEBSAL SL - C/ RIO NERVION, 21-23. 46025 VALENCIA - 96 338 44 08"),4,array('aleft'=>$anchuraColumnas[$columnaActual]+$margenEtiqueta,'aright'=>$anchuraColumnas[$columnaActual+1]-$margenEtiqueta,'justification'=>'left'));
                        $pdf->ezSetY($alturaFilas[$filaActual]-$margenEtiqueta);
                        $pdf->ezText(app_url_text_utf8("Nº RGSEAA: 26.09175/4
"),4,array('aleft'=>$anchuraColumnas[$columnaActual]+$margenEtiqueta,'aright'=>$anchuraColumnas[$columnaActual+1]-$margenEtiqueta,'justification'=>'right'));
                        $pdf->selectFont('./fonts/Helvetica-Bold');
                        $pdf->ezText(app_url_text_utf8($datos_comida["nombre_plato"]."
"),10,array('aleft'=>$anchuraColumnas[$columnaActual]+$margenEtiqueta,'aright'=>$anchuraColumnas[$columnaActual+1]-$margenEtiqueta,'justification'=>'left'));
                        $pdf->selectFont('./fonts/Helvetica');
                        $pdf->ezText("Ingredientes: ".$ingredientes."
                
Tª salida: ".$valor." ºC
        
<b>Alérgenos:</b> ".$alergenos."

<b>Conservacion:</b> ".ucfirst(app_url_text_utf8($datos_comida["instrucciones_conservacion"]))."

<b>Fecha de caducidad / Lote:</b> ".date("d/m/Y",$fecha_time_stamp)."
",6,array('aleft'=>$anchuraColumnas[$columnaActual]+$margenEtiqueta,'aright'=>$anchuraColumnas[$columnaActual+1]-$margenEtiqueta,'justification'=>'left'));

                        $columnaActual++;
                        if ($columnaActual>=$numeroColumnas) {
                                $columnaActual=0;
                                $filaActual++;
                                if ($filaActual>=$numeroFilas) {
                                        $filaActual=0; 
                                        $contadorPaginas++;
                                        if ($contadorPaginas<$numPaginas) {
                                                $pdf->ezNewPage(); 
                                        }       
                                }
                        }
                }

        }
                
                
        }  

        $pdf->ezStream();
}
?>
