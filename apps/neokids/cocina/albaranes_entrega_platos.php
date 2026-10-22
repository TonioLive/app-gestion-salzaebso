<?php  require("../configuracion/parte_superior_sin_login_ancho.php"); ?>
<?php 

$busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($centro_actual));
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

                        }

$solo_escuela=$_GET["solo_escuela"];
if (strlen($solo_escuela)==0) {
        $solo_escuela=0;
        }

$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_proternera","alergias_frutossecos","alergias_marisco","alergias_pielmelocoton","alergias_gluten");
$nombres_alergias=array("lacteos","huevo","proteina de la ternera"," frutos secos"," marisco"," piel de melocoton","celiaco");

$edad_menu=2;

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}

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
        $busca_alumnos=db_query_params('SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.id_centro=? AND servicios_consumidos.fecha=? ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno', 'ss', array($centro_actual, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
        $busca_alumnos1=db_query_params('SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos, escuelamunicipal.servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND alumnos.id_centro=? AND servicios_consumidos.fecha=? ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno', 'ss', array($centro_actual, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
        } else {
        $busca_alumnos=db_query_params('SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos, servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha=? ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
        $busca_alumnos1=db_query_params('SELECT usuarios.*, alumnos.*, servicios_consumidos.*, alumnos.id_centro AS centro_alumno, servicios_consumidos.tipo_menu_comedor AS tipo_menu_consumido FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos, escuelamunicipal.servicios_consumidos WHERE usuarios.id_usuario=alumnos.id_usuario AND usuarios.id_usuario=servicios_consumidos.id_alumno AND servicios_consumidos.fecha=? ORDER BY tipo_menu, servicios_consumidos.menu_seleccionado_hoy, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre, turno', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
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
        
        // Comprueba si está inscrito en una actividad que anula comedor
        $busca_actividad=db_query_params('SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=? AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha=?', 'ss', array($listado_alumnos["id_usuario"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $tipo_menu_alu=$listado_alumnos["tipo_menu"];

        $menu_especial_hoy=false;
        if (db_num_rows($busca_actividad)!=0) {
                $menu_especial_hoy=true;
                $tipo_menu_alu=1;
                }
        
        // if ($menu_especial_hoy==true) {
        
        if (($tipo_menu_alu<>2) and ($tipo_menu_alu>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM centros,centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=?', 's', array($listado_alumnos["aula"]));
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
                        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.edad_ini_menu<=$edad_alumno_a$filtro_regimen");
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
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);
                
                // Comprueba los alimentos del menú y comprueba las alergias del niño
                
                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?;', 'ss', array($id_menu_seleccionado, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                                $motivo_no_hay="";
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {
                                                $sumar_plato=true;
                                                $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                        $numero_ingredientes=db_num_rows($busca_ingredientes);

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alimento NO introducido: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alergia a $nombre_alergia: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }
                                                                if ($sumar_plato==true) {
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
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
                                                                $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                                $totales_platos[$codigo_plato][2]=$numero_ingredientes;
                                                                
                                                                $totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]=$totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]+1;
                                                                
                                                                $totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]=$totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]+1;
                                                                
                                                                }
                                                        }

                                                }
                                                }

                                        } else {
                                        $motivo_no_hay="Día no programado para el menú asignado: ".urldecode($datos_menu_seleccionado["nombre_menu"]);
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

        // Comprueba si está inscrito en una actividad que anula comedor
        $busca_actividad=db_query_params('SELECT * FROM escuelamunicipal.servicios_consumidos_asistencia,escuelamunicipal.tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=? AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha=?', 'ss', array($listado_alumnos["id_usuario"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $tipo_menu_alu=$listado_alumnos["tipo_menu"];

        $menu_especial_hoy=false;
        if (db_num_rows($busca_actividad)!=0) {
                $menu_especial_hoy=true;
                $tipo_menu_alu=1;
                }

        // if ($menu_especial_hoy==true) {

        if (($tipo_menu_alu<>2) and ($tipo_menu_alu>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {
        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM escuelamunicipal.centros,escuelamunicipal.centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=?', 's', array($listado_alumnos["aula"]));
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
                        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.edad_ini_menu<=$edad_alumno_a$filtro_regimen");
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
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);

                // Comprueba los alimentos del menú y comprueba las alergias del niño

                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?;', 'ss', array($id_menu_seleccionado, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                                $motivo_no_hay="";

                                if (db_num_rows($busca_comida_ese_dia)!=0) {
                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {
                                                $sumar_plato=true;
                                                $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                        $numero_ingredientes=db_num_rows($busca_ingredientes);

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alimento NO introducido: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alergia a $nombre_alergia: ".urldecode($ingredientes["nombre_alimento"]).", ingrediente del plato ".urldecode($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }
                                                                if ($sumar_plato==true) {
                                                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
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
                                                                $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                                $totales_platos[$codigo_plato][2]=$numero_ingredientes;

                                                                $totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]=$totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]+1;

                                                                $totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]=$totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]+1;

                                                                }
                                                        }

                                                }
                                                }

                                        } else {
                                        $motivo_no_hay="Día no programado para el menú asignado: ".urldecode($datos_menu_seleccionado["nombre_menu"]);
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
        
        $busca_menu_educadora=db_query_params('SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha=? AND asistencia_docentes.comedor=1 ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($menu_educadora["id_centro_asistencia"]));
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
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);
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
                                                        $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                        $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                        $totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]=$totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]+1;
                                                        }

                        $busca_ingredientes_plato=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=? ORDER BY menu_alimentos.id_alimento;', 's', array($codigo_plato));

                        while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
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
        $busca_menu_educadora=db_query_params('SELECT * FROM escuelamunicipal.asistencia_docentes, escuelamunicipal.usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha=? AND asistencia_docentes.comedor=1 ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy', 's', array((string)(date("Y-m-d", $fecha_time_stamp))));

        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($menu_educadora["id_centro_asistencia"]));
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
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);
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
                                                        $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                        $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                        $totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]=$totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]+1;
                                                        }

                        $busca_ingredientes_plato=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=? ORDER BY menu_alimentos.id_alimento;', 's', array($codigo_plato));

                        while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
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
        
                $busca_centros=db_query("SELECT * FROM centros WHERE centro_propio=1 OR asociado=1 ORDER BY nombre_centro;");
                
                $num_c=db_num_rows($busca_centros);
                
                $contador_c=0;
                
        while($centros=db_fetch_array($busca_centros)) {
        
        ?>
        <div><center>
        <br>
        <table width=100% border=0>
        <tr>
        <td align="center" width=25%><img src="/pdf/logo_factura.jpg"></td>
        <td align="center"><?=$razon_social;?><br><?=$domicilio_centro_completo;?><br>
<?=$CP_centro_completo." ".$poblacion_centro_completo;?><br>
Teléfono: <?=$telefono_centro_completo;?><br>Nº de autorización: 26.09175/4</td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=2><font size=3 face="<?= $fuente_general; ?>"><b>Albarán de entrega de menús servidos</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="right" width=50%><font size=3 face="<?= $fuente_general; ?>"><b>Fecha:</b></font></td>
        <td bgcolor="#ffffff" align="left" width=50%><font size=3 face="<?= $fuente_general; ?>"><?=date("d/m/Y",$fecha_time_stamp)?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="right"><font size=3 face="<?= $fuente_general; ?>"><b>Hora de entrega:</b></font></td>
        <td bgcolor="#ffffff" align="left"><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="right"><font size=3 face="<?= $fuente_general; ?>"><b>Cliente:</b></font></td>
        <td bgcolor="#ffffff" align="left"><font size=3 face="<?= $fuente_general; ?>"><?=$centros["nombre_centro"]?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="right"><font size=3 face="<?= $fuente_general; ?>"><b>Dirección de entrega:</b></font></td>
        <td bgcolor="#ffffff" align="left"><font size=3 face="<?= $fuente_general; ?>"><?=$centros["domicilio_centro"]?>. <?=$centros["poblacion_centro"]?></font></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre menú</b></font></td>
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Nº de raciones servidas</b></font></td>
        </tr>
        <?php 
        $consulta_datos_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu;") or die(db_error());
        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
        if ($totales_menu_escuelas[$datos_menus["id_menu"]][$centros["id_centro"]]>0) {

        $busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($datos_menus["id_menu"], (string)(date("Y-m-d", $fecha_time_stamp))));
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

        $datos_plato_hoy="<table width=100% border=1 cellpadding=0 cellspacing=1 style='border-collapse: collapse' bordercolor='dcdcdc'>";

        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                $id_plato=$datos_comida["id_plato"];
                $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY nombre_alimento;', 's', array($id_plato));
                $ingredientes="";

                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $ingredientes.=urldecode($datos_ingredientes["nombre_alimento"]).", ";
                                }
                
                if ($ingredientes!="") {
                        $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                        }

                $gluten=$datos_comida["gluten"];
                $leche=$datos_comida["leche"];
                $huevos=$datos_comida["huevos"];
                $crustaceos=$datos_comida["crustaceos"];
                $moluscos=$datos_comida["moluscos"];
                $pescados=$datos_comida["pescados"];
                $cacahuete=$datos_comida["cacahuete"];
                $frutos_secos=$datos_comida["frutos_secos"];
                $mostaza_sesamo=$datos_comida["mostaza_sesamo"];
                $sulfitos=$datos_comida["sulfitos"];
                $soja=$datos_comida["soja"];
                $apio=$datos_comida["apio"];
                        
                $alergenos="";
                if ($gluten==1) { $alergenos.="Cereales con gluten, "; }
                if ($leche==1) { $alergenos.="Leche y derivados, "; }
                if ($huevos==1) { $alergenos.="Huevos y derivados, "; }
                if ($crustaceos==1) { $alergenos.="Crustaceos, "; }
                if ($moluscos==1) { $alergenos.="Moluscos, "; }
                if ($pescados==1) { $alergenos.="Pescados y derivados, "; }
                if ($cacahuete==1) { $alergenos.="Cacahuete, "; }
                if ($frutos_secos==1) { $alergenos.="Frutos secos, "; }
                if ($mostaza_sesamo==1) { $alergenos.="Mostaza, sésamo, "; }
                if ($sulfitos==1) { $alergenos.="Sulfitos y SO2, "; }
                if ($soja==1) { $alergenos.="Soja, "; }
                if ($apio==1) { $alergenos.="Apio, "; }
                if ($alergenos!="") {
                        $alergenos=substr($alergenos,0,strlen($alergenos)-2);
                        }
                
                $datos_plato_hoy.="<tr valign=top><td width=25%>".urldecode($datos_comida["nombre_plato"])."</td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Ingredientes:</b><br><font size=1>".$ingredientes."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Alérgenos:</b><br><font size=1>".$alergenos."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Fecha de elaboración:</b> ".date("d/m/Y",$fecha_time_stamp)."<br><b>Fecha de consumo:</b> Consumo inmediato<br>";
                $datos_plato_hoy.="<b>Conservación:</b> ".ucfirst(urldecode($datos_comida["instrucciones_conservacion"]))."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
        if (db_num_rows($busca_comida)!=0) {
$datos_comida=db_fetch_array($busca_comida);

                $id_plato=$datos_comida["id_plato"];
                $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY nombre_alimento;', 's', array($id_plato));
                $ingredientes="";

                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $ingredientes.=urldecode($datos_ingredientes["nombre_alimento"]).", ";
                                }

                if ($ingredientes!="") {
                        $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                        }

                $gluten=$datos_comida["gluten"];
                $leche=$datos_comida["leche"];
                $huevos=$datos_comida["huevos"];
                $crustaceos=$datos_comida["crustaceos"];
                $moluscos=$datos_comida["moluscos"];
                $pescados=$datos_comida["pescados"];
                $cacahuete=$datos_comida["cacahuete"];
                $frutos_secos=$datos_comida["frutos_secos"];
                $mostaza_sesamo=$datos_comida["mostaza_sesamo"];
                $sulfitos=$datos_comida["sulfitos"];
                $soja=$datos_comida["soja"];
                $apio=$datos_comida["apio"];

                $alergenos="";
                if ($gluten==1) { $alergenos.="Cereales con gluten, "; }
                if ($leche==1) { $alergenos.="Leche y derivados, "; }
                if ($huevos==1) { $alergenos.="Huevos y derivados, "; }
                if ($crustaceos==1) { $alergenos.="Crustaceos, "; }
                if ($moluscos==1) { $alergenos.="Moluscos, "; }
                if ($pescados==1) { $alergenos.="Pescados y derivados, "; }
                if ($cacahuete==1) { $alergenos.="Cacahuete, "; }
                if ($frutos_secos==1) { $alergenos.="Frutos secos, "; }
                if ($mostaza_sesamo==1) { $alergenos.="Mostaza, sésamo, "; }
                if ($sulfitos==1) { $alergenos.="Sulfitos y SO2, "; }
                if ($soja==1) { $alergenos.="Soja, "; }
                if ($apio==1) { $alergenos.="Apio, "; }
                if ($alergenos!="") {
                        $alergenos=substr($alergenos,0,strlen($alergenos)-2);
                        }

                $datos_plato_hoy.="<tr valign=top><td width=25%>".urldecode($datos_comida["nombre_plato"])."</td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Ingredientes:</b><br><font size=1>".$ingredientes."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Alérgenos:</b><br><font size=1>".$alergenos."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Fecha de elaboración:</b> ".date("d/m/Y",$fecha_time_stamp)."<br><b>Fecha de consumo:</b> Consumo inmediato<br>";
                $datos_plato_hoy.="<b>Conservación:</b> ".ucfirst(urldecode($datos_comida["instrucciones_conservacion"]))."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
        if (db_num_rows($busca_comida)!=0) {
$datos_comida=db_fetch_array($busca_comida);

                $id_plato=$datos_comida["id_plato"];
                $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY nombre_alimento;', 's', array($id_plato));
                $ingredientes="";

                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $ingredientes.=urldecode($datos_ingredientes["nombre_alimento"]).", ";
                                }

                if ($ingredientes!="") {
                        $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                        }

                $gluten=$datos_comida["gluten"];
                $leche=$datos_comida["leche"];
                $huevos=$datos_comida["huevos"];
                $crustaceos=$datos_comida["crustaceos"];
                $moluscos=$datos_comida["moluscos"];
                $pescados=$datos_comida["pescados"];
                $cacahuete=$datos_comida["cacahuete"];
                $frutos_secos=$datos_comida["frutos_secos"];
                $mostaza_sesamo=$datos_comida["mostaza_sesamo"];
                $sulfitos=$datos_comida["sulfitos"];
                $soja=$datos_comida["soja"];
                $apio=$datos_comida["apio"];

                $alergenos="";
                if ($gluten==1) { $alergenos.="Cereales con gluten, "; }
                if ($leche==1) { $alergenos.="Leche y derivados, "; }
                if ($huevos==1) { $alergenos.="Huevos y derivados, "; }
                if ($crustaceos==1) { $alergenos.="Crustaceos, "; }
                if ($moluscos==1) { $alergenos.="Moluscos, "; }
                if ($pescados==1) { $alergenos.="Pescados y derivados, "; }
                if ($cacahuete==1) { $alergenos.="Cacahuete, "; }
                if ($frutos_secos==1) { $alergenos.="Frutos secos, "; }
                if ($mostaza_sesamo==1) { $alergenos.="Mostaza, sésamo, "; }
                if ($sulfitos==1) { $alergenos.="Sulfitos y SO2, "; }
                if ($soja==1) { $alergenos.="Soja, "; }
                if ($apio==1) { $alergenos.="Apio, "; }
                if ($alergenos!="") {
                        $alergenos=substr($alergenos,0,strlen($alergenos)-2);
                        }

                $datos_plato_hoy.="<tr valign=top><td width=25%>".urldecode($datos_comida["nombre_plato"])."</td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Ingredientes:</b><br><font size=1>".$ingredientes."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Alérgenos:</b><br><font size=1>".$alergenos."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Fecha de elaboración:</b> ".date("d/m/Y",$fecha_time_stamp)."<br><b>Fecha de consumo:</b> Consumo inmediato<br>";
                $datos_plato_hoy.="<b>Conservación:</b> ".ucfirst(urldecode($datos_comida["instrucciones_conservacion"]))."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
        if (db_num_rows($busca_comida)!=0) {
$datos_comida=db_fetch_array($busca_comida);

                $id_plato=$datos_comida["id_plato"];
                $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY nombre_alimento;', 's', array($id_plato));
                $ingredientes="";

                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $ingredientes.=urldecode($datos_ingredientes["nombre_alimento"]).", ";
                                }

                if ($ingredientes!="") {
                        $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                        }

                $gluten=$datos_comida["gluten"];
                $leche=$datos_comida["leche"];
                $huevos=$datos_comida["huevos"];
                $crustaceos=$datos_comida["crustaceos"];
                $moluscos=$datos_comida["moluscos"];
                $pescados=$datos_comida["pescados"];
                $cacahuete=$datos_comida["cacahuete"];
                $frutos_secos=$datos_comida["frutos_secos"];
                $mostaza_sesamo=$datos_comida["mostaza_sesamo"];
                $sulfitos=$datos_comida["sulfitos"];
                $soja=$datos_comida["soja"];
                $apio=$datos_comida["apio"];

                $alergenos="";
                if ($gluten==1) { $alergenos.="Cereales con gluten, "; }
                if ($leche==1) { $alergenos.="Leche y derivados, "; }
                if ($huevos==1) { $alergenos.="Huevos y derivados, "; }
                if ($crustaceos==1) { $alergenos.="Crustaceos, "; }
                if ($moluscos==1) { $alergenos.="Moluscos, "; }
                if ($pescados==1) { $alergenos.="Pescados y derivados, "; }
                if ($cacahuete==1) { $alergenos.="Cacahuete, "; }
                if ($frutos_secos==1) { $alergenos.="Frutos secos, "; }
                if ($mostaza_sesamo==1) { $alergenos.="Mostaza, sésamo, "; }
                if ($sulfitos==1) { $alergenos.="Sulfitos y SO2, "; }
                if ($soja==1) { $alergenos.="Soja, "; }
                if ($apio==1) { $alergenos.="Apio, "; }
                if ($alergenos!="") {
                        $alergenos=substr($alergenos,0,strlen($alergenos)-2);
                        }

                $datos_plato_hoy.="<tr valign=top><td width=25%>".urldecode($datos_comida["nombre_plato"])."</td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Ingredientes:</b><br><font size=1>".$ingredientes."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Alérgenos:</b><br><font size=1>".$alergenos."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Fecha de elaboración:</b> ".date("d/m/Y",$fecha_time_stamp)."<br><b>Fecha de consumo:</b> Consumo inmediato<br>";
                $datos_plato_hoy.="<b>Conservación:</b> ".ucfirst(urldecode($datos_comida["instrucciones_conservacion"]))."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
        if (db_num_rows($busca_comida)!=0) {
$datos_comida=db_fetch_array($busca_comida);

                $id_plato=$datos_comida["id_plato"];
                $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY nombre_alimento;', 's', array($id_plato));
                $ingredientes="";

                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $ingredientes.=urldecode($datos_ingredientes["nombre_alimento"]).", ";
                                }

                if ($ingredientes!="") {
                        $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                        }

                $gluten=$datos_comida["gluten"];
                $leche=$datos_comida["leche"];
                $huevos=$datos_comida["huevos"];
                $crustaceos=$datos_comida["crustaceos"];
                $moluscos=$datos_comida["moluscos"];
                $pescados=$datos_comida["pescados"];
                $cacahuete=$datos_comida["cacahuete"];
                $frutos_secos=$datos_comida["frutos_secos"];
                $mostaza_sesamo=$datos_comida["mostaza_sesamo"];
                $sulfitos=$datos_comida["sulfitos"];
                $soja=$datos_comida["soja"];
                $apio=$datos_comida["apio"];

                $alergenos="";
                if ($gluten==1) { $alergenos.="Cereales con gluten, "; }
                if ($leche==1) { $alergenos.="Leche y derivados, "; }
                if ($huevos==1) { $alergenos.="Huevos y derivados, "; }
                if ($crustaceos==1) { $alergenos.="Crustaceos, "; }
                if ($moluscos==1) { $alergenos.="Moluscos, "; }
                if ($pescados==1) { $alergenos.="Pescados y derivados, "; }
                if ($cacahuete==1) { $alergenos.="Cacahuete, "; }
                if ($frutos_secos==1) { $alergenos.="Frutos secos, "; }
                if ($mostaza_sesamo==1) { $alergenos.="Mostaza, sésamo, "; }
                if ($sulfitos==1) { $alergenos.="Sulfitos y SO2, "; }
                if ($soja==1) { $alergenos.="Soja, "; }
                if ($apio==1) { $alergenos.="Apio, "; }
                if ($alergenos!="") {
                        $alergenos=substr($alergenos,0,strlen($alergenos)-2);
                        }

                $datos_plato_hoy.="<tr valign=top><td width=25%>".urldecode($datos_comida["nombre_plato"])."</td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Ingredientes:</b><br><font size=1>".$ingredientes."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Alérgenos:</b><br><font size=1>".$alergenos."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Fecha de elaboración:</b> ".date("d/m/Y",$fecha_time_stamp)."<br><b>Fecha de consumo:</b> Consumo inmediato<br>";
                $datos_plato_hoy.="<b>Conservación:</b> ".ucfirst(urldecode($datos_comida["instrucciones_conservacion"]))."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
        if (db_num_rows($busca_comida)!=0) {
$datos_comida=db_fetch_array($busca_comida);

                $id_plato=$datos_comida["id_plato"];
                $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes,menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY nombre_alimento;', 's', array($id_plato));
                $ingredientes="";

                while($datos_ingredientes=db_fetch_array($busca_ingredientes)) {
                                $ingredientes.=urldecode($datos_ingredientes["nombre_alimento"]).", ";
                                }

                if ($ingredientes!="") {
                        $ingredientes=substr($ingredientes,0,strlen($ingredientes)-2);
                        }

                $gluten=$datos_comida["gluten"];
                $leche=$datos_comida["leche"];
                $huevos=$datos_comida["huevos"];
                $crustaceos=$datos_comida["crustaceos"];
                $moluscos=$datos_comida["moluscos"];
                $pescados=$datos_comida["pescados"];
                $cacahuete=$datos_comida["cacahuete"];
                $frutos_secos=$datos_comida["frutos_secos"];
                $mostaza_sesamo=$datos_comida["mostaza_sesamo"];
                $sulfitos=$datos_comida["sulfitos"];
                $soja=$datos_comida["soja"];
                $apio=$datos_comida["apio"];

                $alergenos="";
                if ($gluten==1) { $alergenos.="Cereales con gluten, "; }
                if ($leche==1) { $alergenos.="Leche y derivados, "; }
                if ($huevos==1) { $alergenos.="Huevos y derivados, "; }
                if ($crustaceos==1) { $alergenos.="Crustaceos, "; }
                if ($moluscos==1) { $alergenos.="Moluscos, "; }
                if ($pescados==1) { $alergenos.="Pescados y derivados, "; }
                if ($cacahuete==1) { $alergenos.="Cacahuete, "; }
                if ($frutos_secos==1) { $alergenos.="Frutos secos, "; }
                if ($mostaza_sesamo==1) { $alergenos.="Mostaza, sésamo, "; }
                if ($sulfitos==1) { $alergenos.="Sulfitos y SO2, "; }
                if ($soja==1) { $alergenos.="Soja, "; }
                if ($apio==1) { $alergenos.="Apio, "; }
                if ($alergenos!="") {
                        $alergenos=substr($alergenos,0,strlen($alergenos)-2);
                        }

                $datos_plato_hoy.="<tr valign=top><td width=25%>".urldecode($datos_comida["nombre_plato"])."</td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Ingredientes:</b><br><font size=1>".$ingredientes."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Alérgenos:</b><br><font size=1>".$alergenos."</font></font></td>";
                $datos_plato_hoy.="<td width=25%><font size=2><b>Fecha de elaboración:</b> ".date("d/m/Y",$fecha_time_stamp)."<br><b>Fecha de consumo:</b> Consumo inmediato<br>";
                $datos_plato_hoy.="<b>Conservación:</b> ".ucfirst(urldecode($datos_comida["instrucciones_conservacion"]))."</font></td></tr>";
                }
        $datos_plato_hoy.="</table>";
        }

        ?>
        <tr>
        <td bgcolor="#ffffff" align="left" width=90%><font size=2 face="<?= $fuente_general; ?>"><b><?=urldecode($datos_menus["nombre_menu"])?></b><br><?=$datos_plato_hoy?></font></td>
        <td bgcolor="#ffffff" align="right" width=10%><?=number_format($totales_menu_escuelas[$datos_menus["id_menu"]][$centros["id_centro"]],0,",",".");?></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table></td>
</tr>
</table>
<br>
<table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=50% align=right>
<tr><td><b>Recibí:</b></td></tr>
<tr><td>&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br /></td></tr>
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
?>
<script>
window.print();
</script>
</body>
</html>
