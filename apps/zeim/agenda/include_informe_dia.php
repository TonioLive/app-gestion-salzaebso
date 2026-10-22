<?php

function conversor_segundos($seg_ini,$formato) {

        $horas = floor($seg_ini/3600);
         $minutos = floor(($seg_ini-($horas*3600))/60);
         $segundos = $seg_ini-($horas*3600)-($minutos*60);
         $minus="00".$minutos;         
         
         if ($formato==0) {                
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m:'.'s';
                 } else {        
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m';
                 }
         
         return $resultado;

        }

$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$array_alimentos=array("alimentos_verdura","alimentos_pollo","alimentos_ternera","alimentos_pescado","alimentos_huevo","alimentos_lentejas","alimentos_lacteos");
$nombres_alimentos=array("Verdura","Carne de Ave (Pollo o Pavo)","Ternera","Pescado","Huevo","Legumbres","Lacteos");
$array_alergias=array("alergias_lacteos","alergias_huevo","alergias_proternera","alergias_frutossecos","alergias_marisco","alergias_pielmelocoton","alergias_gluten","alergias_pescado","alergias_soja","alergias_sulfitos","alergias_cerdo");
$nombres_alergias=array("Lacteos","Huevo","Proteina de la ternera","Frutos secos","Marisco","Piel de melocoton","Celiaco","Pescado","Soja","Sulfitos","Cerdo");

$alimentos_ya_marcados=array();
$alergias_ya_marcadas=array();

$platos_no_introducidos=array();
$platos_alaergias=array();
$hay_observaciones=false;
$merienda_de_comedor=false;

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_alumno.".jpg";

$no_merienda=$datos_alumno["no_merienda"];

if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="nada.gif";
      }
      


        // Asistencia
                $ficha_tarde=0;
                
                $hora_entrada_m="";
                $hora_salida_m="";
                $hora_entrada_t="";
                $hora_salida_t="";
                
        $consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$a AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY turno;");

        while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
                if ($datos_asistencia["turno"]==0) {
							
                        } else {
                        	// Tarde
	                        $ficha_tarde=1;
							
                        }
				}	
 
 
        $consulta_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=$a AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY turno;");
        
        $tiene_man=false;

        while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
                $horas_extra_hoy=0;
                if ($datos_asistencia["turno"]==0) {

                        $hora_entrada_centro=strtotime($hora_entrada_centro_man);
                        $hora_salida_centro=strtotime($hora_salida_centro_man);
                        } else {
                        $nombre_turno="Tarde";
                        $hora_entrada_centro=strtotime($hora_entrada_centro_tar);
                        $hora_salida_centro=strtotime($hora_salida_centro_tar);
                        }
                $diferencia_entrada=intval($hora_entrada_centro)-intval(strtotime($datos_asistencia["hora_entrada"]));

                if ($diferencia_entrada>$periodo_gracia) {
                        $diferencia_entrada=round($diferencia_entrada/36)/100;
                        $diferencia_entrada=round((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_entrada=0;
                        }

                $diferencia_salida=intval(strtotime($datos_asistencia["hora_salida"]))-intval($hora_salida_centro);

                if ($diferencia_salida>$periodo_gracia) {
                        $diferencia_salida=round($diferencia_salida/36)/100;
                        $diferencia_salida=round((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_salida=0;
                        }

                // Si se queda a comer no se cobran horas
                $coletila="";
                $hora_entrada_comedor="-";
                $hora_salida_comedor="-";
                        if ($datos_asistencia["turno"]==0) {
                                if ($datos_asistencia["hora_entrada_c"]!="") {
                                        $coletila="<font color=red> * Comedor</font>";
                                        $diferencia_salida=$diferencia_salida-$horas_comida;
                                        if ($diferencia_salida<0) {
                                                $diferencia_salida=0;
                                                }
                                        $hora_entrada_comedor=date("H:i:s",strtotime($datos_asistencia["hora_entrada_c"]));
                                        if ($datos_asistencia["hora_salida_c"]!="") {
                                                $hora_salida_comedor=date("H:i:s",strtotime($datos_asistencia["hora_salida_c"]));
                                                } else {
                                                $hora_salida_comedor="-";
                                                }
                                        }
                                } else {
                                // Comprueba si ha entrado a comer en otro turno
                                $busca_otro_turno=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND fecha=? AND turno=0;', 'sss', array($datos_asistencia["id_alumno"], $datos_asistencia["id_servicio"], (string)(date("Y-m-d", strtotime($datos_asistencia["fecha"])))));

                                if (db_num_rows($busca_otro_turno)!=0) {
                                        $datos_otro_turno=db_fetch_array($busca_otro_turno);
                                        if ($datos_otro_turno["hora_entrada_c"]!="") {

                                                $diferencia_entrada=0;
                                                /*
                                                $diferencia_entrada=$diferencia_entrada-$horas_comida;
                                                if ($diferencia_entrada<0) {
                                                        $diferencia_entrada=0;
                                                        }
                                                */
                                                $hora_entrada_comedor=date("H:i:s",strtotime($datos_otro_turno["hora_entrada_c"]));
                                                if ($datos_otro_turno["hora_salida_c"]!="") {
                                                        $hora_salida_comedor=date("H:i:s",strtotime($datos_otro_turno["hora_salida_c"]));
                                                        } else {
                                                        $hora_salida_comedor="-";
                                                        }
                                                }
                                        }
                                }

                $horas_extra_hoy=($diferencia_entrada+$diferencia_salida);

                // Convierte a hh:mm
                $horas_extra_mostrar_hoy=$horas_extra_hoy;

                $horas_extra3=round($horas_extra_mostrar_hoy,0);
                if ($horas_extra3>$horas_extra_mostrar_hoy) {
                        $horas_extra3=$horas_extra3-1;
                                }
                $minutos_extra3="00".(($horas_extra_mostrar_hoy-$horas_extra3)*60);
                $minutos_extra3=substr($minutos_extra3,strlen($minutos_extra3)-2,2);
                $horas_extra3="00".$horas_extra3;
                $horas_extra3=substr($horas_extra3,strlen($horas_extra3)-2,2);
                $horas_extra_mostrar_hoy=$horas_extra3.":".$minutos_extra3;

                $total_horas_extra=0;
                $total_horas_extra_fact=0;

                $total_horas_extra=$total_horas_extra+$horas_extra_hoy;
                if ($horas_extra_hoy_fact>0) {
                        $total_horas_extra_fact=$total_horas_extra_fact+$horas_extra_hoy_fact;
                        }

                        if ($datos_asistencia["hora_entrada"]!="") {
                        $mostrar_entrada=true;
                        if ($datos_asistencia["turno"]==0) {
                               $entrada_ma=true;
                               }
                        if (($datos_asistencia["turno"]==1) and ($entrada_ma==true)) {
                                $mostrar_entrada=false;
                                }
                        if ($mostrar_entrada==true) {
                                                                // Mostrar entrada
                                }
                                                if ($datos_asistencia["turno"]==1)        {                                                                                
                                                     $hora_entrada_t=date("H:i",strtotime($datos_asistencia["hora_entrada"]));
                                                     } else {
                                                     $hora_entrada_m=date("H:i",strtotime($datos_asistencia["hora_entrada"]));
                                                     }    

                        }

// ¿Viene por la tarde?
/*
if ($datos_asistencia["turno"]==0) {
	
	$hora_salida_m=date("H:i",strtotime($datos_asistencia["hora_salida"]));
	if (date("H",strtotime($hora_salida_m))>16) {
			$ficha_tarde=1;
	}

}
*/
// Busca registro de comida
$busca_registro_comida=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id_alumno=? AND dia=?', 'ss', array($id_alumno, (string)(date("Y-m-d", $dia)))) or die(db_error());

if (db_num_rows($busca_registro_comida)!=0) {

                        $datos_registro_comida=db_fetch_array($busca_registro_comida);

                                                $siesta1=$datos_registro_comida["siesta1"];
                                                $siesta2=$datos_registro_comida["siesta2"];

                                                }
// Busca asistencia a menu especial

        $busca_actividad=db_query("SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=".$a." AND (servicios_consumidos_asistencia.reserva=1 OR servicios_consumidos_asistencia.no_asiste=1)AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha='".date("Y-m-d",$dia)."'") or die(db_error());

        $menu_especial_hoy=false;
        if (db_num_rows($busca_actividad)!=0) {
                $menu_especial_hoy=true;
                }
                        if (($datos_asistencia["hora_entrada_c"]!="") or ($menu_especial_hoy==true)) {

                        $merienda_de_comedor=true;
                        
                        // Busca comidas y resultado
                        $descripcion_comidas="";
                        $numero_filas_comidas=0;
                        $numero_filas_almuerzo=0;
                        $numero_filas_comida=0;
                        $numero_filas_merienda=0;

                        $id_registro_comida=$datos_registro_comida["id"];

                        if ($id_registro_comida!=0) {

                        if ($menu_especial_hoy==true) {
                                        // Comprueba si tiene un menú asignado en la ficha y busca por edad similar al menú asignado
                        
                                        // Busca menu especial
                                        if  ($datos_alumno["menu_actual"]!=0) {
                                        
                                                $busca_menu_preseleccionado=db_query("SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=".$datos_alumno["menu_actual"]);
                                                if (db_num_rows($busca_menu_preseleccionado)!=0) {
                                                
                                                        $datos_menu_preseleccionada=db_fetch_array($busca_menu_preseleccionado);
                                                        $edad_alumno=$datos_menu_preseleccionada["edad_ini_menu"];
                                                
                                                }
                                        
                                        } else {
                                        
                                        // Busca por edad
                                        $edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
                                        if (date("m",$fecha_time_stamp)<date("m",strtotime($datos_alumno["fecha_nacimiento"]))) {
                                                $edad_alumno=$edad_alumno-1;
                                                }
                                        $edad_alumno_anos=$edad_alumno;
                                        // Decimales
                                        $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($datos_alumno["fecha_nacimiento"]));

                                        if ($mes_edad_alumno<0) {
                                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                                } else {
                                                $edad_alumno_meses=$mes_edad_alumno;
                                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                                }
                                        
                                        }
                                        
                                        $filtro_regimen=" AND menu_menus.regimen=2";

                                        $busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$dia)."' AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
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

                                        $id_menu=$id_menu_seleccionado;
                                        } else {
                                        $id_menu=$datos_asistencia["menu_seleccionado_hoy"];
                                        }

                        // Busca menu para hoy

$busca_platos_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());


if (db_num_rows($busca_platos_dia)!=0) {

                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) { // Bucle platos

                                                                                $procesar_plato=true;

                                        if ((substr($array_campos_platos[$c_platos],0,8)=="merienda") and ($no_merienda==1)) {
                                                $procesar_plato=false;
                                                }

                                        if ($procesar_plato==true) { // Inicio Procesar plato

                                        $codigo_plato=$datos_platos_dia[$array_campos_platos[$c_platos]];

                                        if ($codigo_plato!=0) {

                                                $consulta_datos_plato=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes, gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($datos_alumno[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        if ($alimentos_ya_marcados[$codigo_plato][$id_alumno]!=1) {
                                                                                                $platos_no_introducidos[$c][$id_alumno]=$platos_no_introducidos[$c][$id_alumno].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
                                                                                                $alimentos_ya_marcados[$codigo_plato][$id_alumno]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($datos_alumno[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        if ($alergias_ya_marcadas[$codigo_plato][$id_alumno]!=1) {
                                                                                                $platos_alaergias[$c][$id_alumno]=$platos_alaergias[$c][$id_alumno].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
                                                                                                $alergias_ya_marcadas[$codigo_plato][$id_alumno]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }
                                                                }

                                                        }

                                                }
                                              } // Fin procesar platos
                                           } // Fin bucle platos
                                                
if ($hay_observaciones==true) {
        $numero_filas_comidas++;
        }                                                

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

        $color_alerta="#FF0000";
        $descripcion_comidas.="<td rowspan=[filas_comida] class='tabla_comida'><b>COMIDA</b></td>";
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida1][$id_alumno]==1) or ($alimentos_ya_marcados[$comida1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<td class='tabla_comida'><b><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                $resultado_comida1=$resultados_comida[$datos_registro_comida["comida1"]];
                if ($datos_registro_comida["cant_comida1"]==3) {
                        $resultado_comida1="";
                        }
                $descripcion_comidas.="</td><td class='tabla_comida'>".$cantidades_comida[$datos_registro_comida["cant_comida1"]]."</td><td class='tabla_comida'>".$resultado_comida1."</td></tr>";
                $numero_filas_comida++;
                $numero_filas_comidas++;
                }

        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida2][$id_alumno]==1) or ($alimentos_ya_marcados[$comida2][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<tr class='tabla_comida'><td><b><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                $resultado_comida2=$resultados_comida[$datos_registro_comida["comida2"]];
                if ($datos_registro_comida["cant_comida2"]==3) {
                        $resultado_comida2="";
                        }
                $descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida2"]]."</td><td>".$resultado_comida2."</td></tr>";
                $numero_filas_comida++;
                $numero_filas_comidas++;
                }

        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida3][$id_alumno]==1) or ($alimentos_ya_marcados[$comida3][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<tr class='tabla_comida'><td><b><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                $resultado_comida3=$resultados_comida[$datos_registro_comida["comida3"]];
                if ($datos_registro_comida["cant_comida3"]==3) {
                        $resultado_comida3="";
                        }
                $descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida3"]]."</td><td>".$resultado_comida3."</td></tr>";
                $numero_filas_comida++;
                $numero_filas_comidas++;
                }

        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida4][$id_alumno]==1) or ($alimentos_ya_marcados[$comida4][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<tr class='tabla_comida'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                $resultado_comida4=$resultados_comida[$datos_registro_comida["comida4"]];
                if ($datos_registro_comida["cant_comida4"]==3) {
                        $resultado_comida4="";
                        }
                $descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida4"]]."</td><td>".$resultado_comida4."</td></tr>";
                $numero_filas_comida++;
                $numero_filas_comidas++;
                }

        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida5][$id_alumno]==1) or ($alimentos_ya_marcados[$comida5][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<tr class='tabla_comida'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                $resultado_comida5=$resultados_comida[$datos_registro_comida["comida5"]];
                if ($datos_registro_comida["cant_comida5"]==3) {
                        $resultado_comida5="";
                        }
                $descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_comida5"]]."</td><td>".$resultado_comida5."</td></tr>";
                $numero_filas_comida++;
                $numero_filas_comidas++;
                }
                
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$postre1][$id_alumno]==1) or ($alimentos_ya_marcados[$postre1][$id_alumno]==1))                                                 {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<tr class='tabla_comida'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                $resultado_postre1=$resultados_comida[$datos_registro_comida["postre1"]];
                if ($datos_registro_comida["cant_postre1"]==3) {
                        $resultado_postre1="";
                        }
                $descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_postre1"]]."</td><td>".$resultado_postre1."</td></tr>";
                $numero_filas_comida++;
                $numero_filas_comidas++;
                }
                
                
        // Merienda solo si activada
        if ($no_merienda!=1) {
               
        // merienda (solo si ficha tarde)
        if ($ficha_tarde==1) { 
                $descripcion_comidas.="<tr class='tabla_merienda'><td rowspan=[filas_merienda]><b>MERIENDA</b></td>";       
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                
                if ($datos_comida["cant_merienda1"]<4) {
                
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$merienda1][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda1][$id_alumno]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                $descripcion_comidas.="<td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
                $resultado_merienda1=$resultados_comida[$datos_registro_comida["merienda1"]];
                if ($datos_registro_comida["cant_merienda1"]==3) {
                        $resultado_merienda1="";
                        }
                $descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_merienda1"]]."</td><td>".$resultado_merienda1."</td></tr>";
                $numero_filas_merienda++;
                $numero_filas_comidas++;
				}
			}
                
        $busca_comida=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
				
				if ($datos_comida["cant_merienda2"]<4) {
				
					$color_fuente_linea="#000000";
					if (($alergias_ya_marcadas[$merienda2][$id_alumno]==1) or ($alimentos_ya_marcados[$merienda2][$id_alumno]==1)) {
							$color_fuente_linea=$color_alerta;
							}
					$descripcion_comidas.="<tr class='tabla_merienda'><td><font color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font>";
					$resultado_merienda2=$resultados_comida[$datos_registro_comida["merienda2"]];
					if ($datos_registro_comida["cant_merienda2"]==3) {
							$resultado_merienda2="";
							}
					$descripcion_comidas.="</td><td>".$cantidades_comida[$datos_registro_comida["cant_merienda2"]]."</td><td>".$resultado_merienda2."</td></tr>";
					$numero_filas_merienda++;
					$numero_filas_comidas++;
					
				}
            }
				
		   } // Fin filtro no ficha tarde
		
		} // Fin merienda
			
			$texto_observaciones_linea="<br /> - ".$datos_registro_comida["observaciones_comida"];
			
		  } 
		}

                          /*
// Otras observaciones comida
        $busca_incidencias=mysql_query("SELECT * FROM agenda_alumnos_seguimiento WHERE dia='".date("Y-m-d",$dia)."' AND ((id_alumno=".$id_alumno.") OR (id_alumno=0 AND id_aula=".$id_aula.")) AND area=0 ORDER BY hora");

                while($incidencias=mysql_fetch_array($busca_incidencias)) {
                                $texto_observaciones_linea.="<br /> - ".$incidencias["nota"];
                        }

                if (strlen($texto_observaciones_linea)!=0) {
                        $descripcion_comidas.="<tr class='tabla_pie_comida'><td colspan=5><b>Observaciones:</b>".$texto_observaciones_linea."</td></tr>";
                        }
                        */

            $hora_salida_comedor=date("H:i:s",strtotime($datos_asistencia["hora_salida_c"]));

                        }
                        // Fin comidas

                        if ($datos_asistencia["hora_salida"]!="") {
                        
                        if ($datos_asistencia["turno"]==1)        {                                                                                
                                                     $hora_salida_t=date("H:i",strtotime($datos_asistencia["hora_salida"]));
                                                     } else {
                                                     $hora_salida_m=date("H:i",strtotime($datos_asistencia["hora_salida"]));
                                                     }  
                        }
                       
                                       
                }

 // Otras observaciones comida

 if (strlen($descripcion_comidas)==0) {
                $texto_observaciones_linea="";
                $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE dia=? AND (id_alumno=?) AND observaciones_comida<>\'\'', 'ss', array((string)(date("Y-m-d", $dia)), $id_alumno));

                while($incidencias=db_fetch_array($busca_incidencias)) {
                                $texto_observaciones_linea.="<br /> - ".$incidencias["observaciones_comida"];
                        }

                if (strlen($texto_observaciones_linea)!=0) {
                        $descripcion_comidas.="<tr class='tabla_pie_comida'><td colspan=5><b>Observaciones:</b>".$texto_observaciones_linea."</td></tr>";
                        }
        }

// $texto_observaciones_linea="";
               $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND area=0 ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));

                while($incidencias=db_fetch_array($busca_incidencias)) {
                                $texto_observaciones_linea.="<br /> - ".$incidencias["nota"];
                        }

                if (strlen($texto_observaciones_linea)!=0) {
                        $descripcion_comidas.="<tr class='tabla_pie_comida'><td colspan=5><b>Observaciones:</b>".$texto_observaciones_linea."</td></tr>";
                        }


                
                        // Busca Almuerzo
                        $descripcion_almuerzo="";
                        
                        // Busca registro de comida
                        $busca_registro_almuerzo=db_query_params('SELECT * FROM agenda_alumnos_comidas WHERE id_alumno=? AND dia=?', 'ss', array($id_alumno, (string)(date("Y-m-d", $dia)))) or die(db_error());

                        if (db_num_rows($busca_registro_almuerzo)!=0) {
                                $datos_registro_almuerzo=db_fetch_array($busca_registro_almuerzo); 

                                // Almuerzo
                                if ($datos_registro_almuerzo["cant_almuerzo"]<4) {
                                        $descripcion_almuerzo.="<tr><td class='tabla_comidas' rowspan=[filas_comidas] style='max-width:100px!important;min-width:100px!important;' width='100'><img src='imagenes/PersonajeComedor.png' border=0></td><td class='tabla_almuerzo'><b>ALMUERZO</b></td><td class='tabla_almuerzo'>&nbsp;</td><td class='tabla_almuerzo'>".$cantidades_comida[$datos_registro_almuerzo["cant_almuerzo"]]."</td><td class='tabla_almuerzo'>".$resultados_comida[$datos_registro_almuerzo["almuerzo"]]."</td></tr>";
                                        $numero_filas_comidas++;
                                        }
                                        
                                // Merienda
                                if (($merienda_de_comedor==false) and ($ficha_tarde==1) and (($datos_registro_almuerzo["cant_merienda1"]<4) or ($datos_registro_almuerzo["cant_merienda2"]<4))) {
                                        if ($descripcion_almuerzo=="") {
                                                $descripcion_almuerzo.="<tr><td class='tabla_comidas' rowspan=[filas_comidas]><img src='imagenes/PersonajeComedor.png' border=0></td>";
                                                }
										if ($datos_registro_almuerzo["cant_merienda1"]<4) {
											
											$descripcion_almuerzo.="<tr><td class='tabla_almuerzo'><b>MERIENDA</b></td><td class='tabla_almuerzo'>&nbsp;</td><td class='tabla_almuerzo'>".$cantidades_comida[$datos_registro_almuerzo["cant_merienda1"]]."</td><td class='tabla_almuerzo'>".$resultados_comida[$datos_registro_almuerzo["merienda1"]]."</td></tr>";
											$numero_filas_comidas++;
										}
										
										if ($datos_registro_almuerzo["cant_merienda2"]<4) {
											
											$descripcion_almuerzo.="<tr><td class='tabla_almuerzo'><b>&nbsp;</b></td><td class='tabla_almuerzo'>&nbsp;</td><td class='tabla_almuerzo'>".$cantidades_comida[$datos_registro_almuerzo["cant_merienda2"]]."</td><td class='tabla_almuerzo'>".$resultados_comida[$datos_registro_almuerzo["merienda2"]]."</td></tr>";
											$numero_filas_comidas++;
										}
								
                                }
                                        
                                if ($descripcion_almuerzo!="") {
                                                if (strlen($descripcion_comidas)!=0) {
                                                        $descripcion_comidas="<tr>".$descripcion_comidas;
                                                        }
                                                            
                                                $descripcion_comidas=$descripcion_almuerzo.$descripcion_comidas;
                                                $numero_filas_almuerzo++;
                                  
                                        } else {
                                                $descripcion_comidas="<tr><td class='tabla_comidas' rowspan=[filas_comidas]><img src='imagenes/PersonajeComedor.png' border=0></td>".$descripcion_comidas;
                                        }
                                }

$descripcion_comidas=str_replace("[filas_comidas]", $numero_filas_comidas, $descripcion_comidas);
$descripcion_comidas=str_replace("[filas_comida]", $numero_filas_comida, $descripcion_comidas);
$descripcion_comidas=str_replace("[filas_merienda]", $numero_filas_merienda, $descripcion_comidas);

?>
<table width=100% border=0 cellpadding=0 cellspacing=0 align="center">
        <!-- encabezado -->
        <tr>
                <td colspan="2" align="center">
                        <table width=100% cellpadding=2 cellspacing=0 border=0>
                                <tr>
                                        <td><img src="/pdf/logo_factura<?=$id_centro?>.jpg" border=0 align="left" ></td>
                                        <td align="center"><span class="texto_encabezado"><br />Mi agenda - <?=date("j",$fecha_time_stamp);?> de <?=$nombre_mes[date("n",$fecha_time_stamp)];?> de <?=date("Y",$fecha_time_stamp);?></span><br><span class="subtitulo_encabezado"><?=$nombre_completo_alumno;?></span></td>
                                </tr>
                        </table>
                </td>
        </tr>
        <!-- Horario de entrada y salida -->
        <tr>
                <td colspan="2">
                        <table width=100% border=0 cellpaddin=2 cellspacing=2 width=100%>
                                <tr class="encabezado_horario">
                                        <td width=25%>Llegada por la Mañana</td>
                                        <td width=25%>Salida Mañana</td>
                                </tr>
                                <tr class="filas_horario">
                                        <td><?=$hora_entrada_m?></td>
                                        <td><?=$hora_salida_m?></td>
                                </tr>
                                <tr class="encabezado_horario">
                                        <td width=25%>Llegada por la tarde</td>
                                        <td width=25%>Salida tarde</td>
                                </tr>
                                <tr class="filas_horario">                
                                        <td><?php  if ($hora_salida_m!="") { echo($hora_entrada_t); } else { ?>&nbsp;<?php  }?></td>
                                        <td><?=$hora_salida_t?></td>
                                </tr>
                        </table>
                </td>
        </tr>
        <!-- Tablas -->
        <tr>
        <!-- Comida -->                 
                <td width=100% valign="top">
                        <table border=0 cellpadding=2 cellspacing=2 width="100%">
                                <tr>
                                <td colspan=2 width=100>&nbsp;</td>
                                </tr><?=$descripcion_comidas?><?php 

                if ($hay_observaciones==true) {
?><tr class='tabla_pie_comida'><td colspan=5>
<?php 
$encabezado=false;
$listado_no_introducidos="";
for ($conta1=0;$conta1<count($array_alimentos);$conta1++) {
        if (strlen($platos_no_introducidos[$conta1][$id_alumno])!=0) {
                $platos_no_introducidos[$conta1][$id_alumno]=urldecode(substr($platos_no_introducidos[$conta1][$id_alumno],0,strlen($platos_no_introducidos[$conta1][$id_alumno])-1));
                if ($encabezado==false) {
                        ?>


                        <?php 
                        $encabezado=true;
                        }
        
                        $listado_no_introducidos.=$nombres_alimentos[$conta1].", "; 
        
                }
        }
        
if (strlen($listado_no_introducidos)!=0) {
        $listado_no_introducidos=substr($listado_no_introducidos,0,strlen($listado_alergias)-2);
        ?><b>Alimentos no introducidos detectados:</b> <?=$listado_no_introducidos;?><?php 
        }
               
$encabezado=false;
$listado_alergias="";
for ($conta1=0;$conta1<count($array_alergias);$conta1++) {
        if (strlen($platos_alaergias[$conta1][$id_alumno])!=0) {
                $platos_alaergias[$conta1][$id_alumno]=urldecode(substr($platos_alaergias[$conta1][$id_alumno],0,strlen($platos_alaergias[$conta1][$id_alumno])-1));
                if ($encabezado==false) {
                        ?>
                                        <b>Alergias</b><br>

                        <?php 
                        $encabezado=true;
                        }
                $listado_alergias.=$nombres_alergias[$conta1].", ";        

                }
        }

if (strlen($listado_alergias)!=0) {
        $listado_alergias=substr($listado_alergias,0,strlen($listado_alergias)-2);
        ?><b>Alergias detectadas:</b> <?=$listado_alergias;?><?php 
        }
        
?>
</td></tr>
<?php 
}
                
                ?></table></td>
                </tr>
                <tr>
                <!-- Programacion -->
                <td rowspan="3" valign="top">
<?php 
$coletilla_grupos="";

if (($edad_aula==0) and (strlen($grupo_alumno)!=0)) {
        $coletilla_grupos=" AND (edad=".($grupo_alumno-1)." OR edad=0)";
        // $coletilla_grupos=" AND (edad=".($grupo_alumno-1).")";
        }

// Programacion mañana
$busca_programacion=db_query("SELECT * FROM agenda_programacion WHERE realizada=1 AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND turno=0 AND id_aula=$id_aula AND (objetivo<>0 OR actividad<>'')$coletilla_grupos ORDER BY tiempo_trabajo,orden_actividad");

// Programacion Tarde
$busca_programacion_tarde=db_query("SELECT * FROM agenda_programacion WHERE realizada=1 AND fecha='".date("Y-m-d",$fecha_time_stamp)."' AND turno=1 AND id_aula=$id_aula AND (objetivo<>0 OR actividad<>'')$coletilla_grupos ORDER BY tiempo_trabajo,orden_actividad");

// Programacion inglés (NUEVA 2018-2019)
$busca_programacion_ingles=db_query_params('SELECT * FROM agenda_programacion_ingles_calendario WHERE dia=? AND estado=1 AND id_aula=? ORDER BY dia', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), $id_aula));
$num_prog_ingles=0;
$programacion_dias_ingles="";

if (db_num_rows($busca_programacion_ingles)!=0) {

	$programacion_ingles_hoy=db_fetch_array($busca_programacion_ingles);
	
	$id_plantilla=$programacion_ingles_hoy["id_programacion"];
	
	// Busca número de sesión
	$busca_programaciones_ingles=db_query_params('SELECT * FROM agenda_programacion_ingles_calendario WHERE dia<=? AND estado=1 AND id_programacion=? AND id_aula=?', 'sss', array((string)(date("Y-m-d", $fecha_time_stamp)), $id_plantilla, $id_aula)) or die(db_error());	
	$num_sesion_hoy=db_num_rows($busca_programaciones_ingles);
	
	// Rio nervión
	if ($id_centro==8) {
		$num_sesion_hoy=($num_sesion_hoy*2)-1;	
		$filtro_sesiones="(numero_sesion=$num_sesion_hoy OR numero_sesion=$num_sesion_hoy+1)";
	} else {
		$filtro_sesiones="numero_sesion=$num_sesion_hoy";
	}
	
	// Busca activiades hoy
	$busca_actividades=db_query("SELECT * FROM agenda_programacion_plantillas_actividades LEFT JOIN agenda_programacion_ingles_actividades ON agenda_programacion_plantillas_actividades.id_actividad=agenda_programacion_ingles_actividades.id_actividad, agenda_programacion_ingles_objetivos WHERE agenda_programacion_ingles_actividades.id_objetivo=agenda_programacion_ingles_objetivos.id_objetivo AND id_plantilla=$id_plantilla AND $filtro_sesiones ORDER BY orden_actividad,id_prog");
	
	$num_prog_ingles=db_num_rows($busca_actividades);
		
	while($actividades=db_fetch_array($busca_actividades)) {
		
		$descripcion_actividad_actual=ucfirst($actividades["descripcion_actividad"]);		
		$descripcion_objetivo_trabajado="";
		if ((strlen($actividades["nombre_objetivo"])!=0) and ($actividades["id_objetivo"]!=9999)) {
				$descripcion_objetivo_trabajado=mb_convert_case($actividades["nombre_objetivo"], MB_CASE_UPPER).": ";
		}		
		
		$programacion_dia="<tr class='tabla_actividades_filas'><td>INGLÉS</td><td>".$descripcion_objetivo_trabajado.$descripcion_actividad_actual;
 
		$programacion_dias_ingles.=$programacion_dia."</td>";
			
	}	
	
}

$total_prog=db_num_rows($busca_programacion)+$num_prog_ingles+db_num_rows($busca_programacion_tarde)+1;

?>
<table border=0 cellpadding=2 cellspacing=2 width=100%>
                <tr>
                <td width=100>&nbsp;</td>
                <td class='tabla_actividades_encabezado' width=100>¿Cuándo?</td>
                <td class='tabla_actividades_encabezado'>¿Qué y cómo?</td>
                </tr>
                <tr>
                <td rowspan="<?=$total_prog?>" class="tabla_actividades" width=100 valign="top"><img src="imagenes/PersonajeActividades.png" border=0 width=100><br /></td>
<?php 
// Programacion mañana                                     
if (db_num_rows($busca_programacion)!=0) {
                       
                    while($programacion=db_fetch_array($busca_programacion)) {
                                                $area_actual_objetivo=$programacion["area"];
                                                if ($programacion["id_actividad"]!=0) {
                                        $descripcion_actividad_actual=$programacion["actividad"];
                                        } else {
                                        $descripcion_actividad_actual=utf8_decode($programacion["actividad"]);
                                        }
                
                    $programacion_dia="<tr class='tabla_actividades_filas' style='max-width:100px!important;min-width:100px!important;'><td>Mañana</td><td style='vertical-align: top;'>".$descripcion_actividad_actual;
                     
                    $objetivos_dia="";   
                                        // Busca nombre objetivo
                                        if ($programacion["objetivo"]!=0) {
                                        $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$programacion["objetivo"]." AND id_area=".$area_actual_objetivo);
                                        if (db_num_rows($busca_datos_objetivo)!=0) {
                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                        $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                        }
                                                }  
                        
                    // Muestra objetivos trabajados
                                        if (strlen($programacion["objetivos"])!=0) {
                                                $objetivos_trabajados=str_replace("[", "", $programacion["objetivos"]);
                                                $objetivos_trabajados=str_replace("]", "", $objetivos_trabajados);

                                                $array_objetivos_trabajados=explode(",", $objetivos_trabajados);

                                                        for($c_obj=0;$c_obj<count($array_objetivos_trabajados);$c_obj++) {
                                                                // Busca nombre objetivo
                                                                $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_trabajados[$c_obj]." AND id_area=".$area_actual_objetivo);
                                                                if (db_num_rows($busca_datos_objetivo)!=0) {
                                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                                        $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                                        }
                                                                }  
                                                        } 
                                                        if ($objetivos_dia!="") {
                                                                $programacion_dia.="<br /><b>Objetivos trabajados:</b><br>".$objetivos_dia;
                                                                }
                                       echo($programacion_dia."</td>");
                                }

                            }
                                       
// Programacion Tarde                                   
if (db_num_rows($busca_programacion_tarde)!=0) {
                       
                    while($programacion=db_fetch_array($busca_programacion_tarde)) {
                                                $area_actual_objetivo=$programacion["area"];
                                                if ($programacion["id_actividad"]!=0) {
                                        $descripcion_actividad_actual=$programacion["actividad"];
                                        } else {
                                        $descripcion_actividad_actual=utf8_decode($programacion["actividad"]);
                                        }

                                        $programacion_dia="<tr class='tabla_actividades_filas' style='max-width:100px!important;min-width:100px!important;'><td>Tarde</td><td style='vertical-align: top;'>".$descripcion_actividad_actual;
                                        
                                        // Busca nombre objetivo
                                        $objetivos_dia="";  
                                        if ($programacion["objetivo"]!=0) {
                                        $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$programacion["objetivo"]." AND id_area=".$area_actual_objetivo);
                                        if (db_num_rows($busca_datos_objetivo)!=0) {
                                                $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                }  
                            }    
                    // Muestra objetivos trabajados
                                        if (strlen($programacion["objetivos"])!=0) {
                                                $objetivos_trabajados=str_replace("[", "", $programacion["objetivos"]);
                                                $objetivos_trabajados=str_replace("]", "", $objetivos_trabajados);

                                                $array_objetivos_trabajados=explode(",", $objetivos_trabajados);

                                                        for($c_obj=0;$c_obj<count($array_objetivos_trabajados);$c_obj++) {
                                                                // Busca nombre objetivo
                                                                $busca_datos_objetivo=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_trabajados[$c_obj]." AND id_area=".$area_actual_objetivo);
                                                                if (db_num_rows($busca_datos_objetivo)!=0) {
                                                                        $datos_objetivo=db_fetch_array($busca_datos_objetivo);

                                                                        $objetivos_dia.="- ".$datos_objetivo["nombre_objetivo"]."<br>";
                                                                        }
                                                                }  
                                                        } 
                                                        if ($objetivos_dia!="") {
                                                                $programacion_dia.="<br /><b>Objetivos trabajados:</b><br>".$objetivos_dia;
                                                                }
                                       echo($programacion_dia."</td>");
                                }
                        }

// Programacion inglés
if ($programacion_dias_ingles!="") {
	
	echo($programacion_dias_ingles);
	
} 					
// Fin programación
                ?></table>                                
        </td>
</tr>
</table>
<!--  Fin programación -->    
<!-- Higiene y salud -->
<table border=0 cellpadding=2 cellspacing=2 width=100%>                       
                <tr>
                <td valign=middle><?php                 
                // Seguimiento
                
        $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (area=1 OR area=2) AND tipo_nota<>14  AND tipo_nota<>4 ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));
                $num_filas_seguimiento=db_num_rows($busca_incidencias);
                
                // Busca incidencias no atendidas
                $busca_incidencias_no=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (area=1 OR area=2) AND tipo_nota=1  AND avisar<>0 AND realizada=0', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));
                $num_filas_seguimiento=$num_filas_seguimiento-db_num_rows($busca_incidencias_no);
                
                if ($siesta1!=0) {
                        $num_filas_seguimiento++;
                        }
                if ($siesta2!=0) {
                        $num_filas_seguimiento++;
                        }
                ?>
                <table border=0 cellpadding=2 cellspacing=2 width=100%>
                <tr>
                <td rowspan="<?=$num_filas_seguimiento+1;?>" class="tabla_higiene" width=100><img src="imagenes/PersonajeHigieneySalud.png" border=0 width=100></td>                
                <?php 
                $cuenta_filas=0;
                while($incidencias=db_fetch_array($busca_incidencias)) {
                $mostrar_fila=true;        
                if (($incidencias["tipo_nota"]==1) and ($incidencias["avisar"]!=0) and ($incidencias["realizada"]==0)) {
                                $mostrar_fila=false;
                                }
                if ($mostrar_fila==true) { 
                        if ($cuenta_filas>0) {
                        ?>
                        <tr>
                        <?php
                        }               
        ?>
        <td class="tabla_higiene_filas"><?=$tipos_notas_inci[$incidencias["tipo_nota"]]?></td>
        <!-- <td class="tabla_higiene_filas"><?=date("H:i",strtotime($incidencias["hora"]))?></td>-->
        <td colspan=2 class="tabla_higiene_filas"><?php  if ($incidencias["tipo_nota"]!=3) { echo(date("H:i",strtotime($incidencias["hora"])).": "); } ?><?=str_replace("eposici?n","eposición",$incidencias["nota"]);?></td>
                        </tr>
        <?php 
                        $cuenta_filas++;   
                        }                
                }
				/*
				if ($cuenta_filas==0) {
						?>
						<td class="tabla_higiene_filas" colspan=3>&nbsp;</td>
						<?php
				}
                                */
        
                if ($siesta1!=0) {
                        $num_siesta="";
                        if ($siesta2!=0) {

                                }
                        ?>
                        <tr>             
                        <td class="tabla_higiene_filas"><?=$num_siesta?>Siesta</td>
                        <td class="tabla_higiene_filas" colspan=2>Ha dormido <?=conversor_segundos($siesta1*60,1);?></td>
                </tr>
                <?php 
                        $cuenta_filas++; 
                        }
                if ($siesta2!=0) {
                        $num_siesta="";
                        if ($siesta1!=0) {

                                }
                        ?>
                        <tr>             
                        <td class="tabla_higiene_filas"><?=$num_siesta?>Siesta</td>
                        <td class="tabla_higiene_filas" colspan=2>Ha dormido <?=conversor_segundos($siesta2*60,1);?></td>
                </tr>
                <?php 
                        $cuenta_filas++; 
                        }
        if ($cuenta_filas==0) {
                ?>
                <td class="tabla_higiene_filas" colspan=3>&nbsp;</td>
                <?php 
                }        
                
        $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (tipo_nota=14  OR tipo_nota=4) ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));        
        if (db_num_rows($busca_incidencias)!=0) {
        ?>
        <tr class='tabla_pie_higiene'><td colspan=4><b>Recordatorios y recomendaciones:</b>
        <?php 
        while($incidencias=db_fetch_array($busca_incidencias)) {
                        echo("<br /> - ".$incidencias["nota"]);
        }
        ?>
        </td></tr>
        <?php 
                }        
                ?></table></td>
                </tr>
        </table>
        <table border=0 cellpadding=2 cellspacing=2 width=100%>                       
                <tr>
                <!-- Notas y recordatorios -->
                <td valign=bottom><?php 
                
                // Seguimiento
                
        $busca_incidencias=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE dia=? AND ((id_alumno=?) OR (id_alumno=0 AND id_aula=?)) AND (area=3) ORDER BY hora', 'sss', array((string)(date("Y-m-d", $dia)), $id_alumno, $id_aula));
                $num_filas_seguimiento=db_num_rows($busca_incidencias);
                ?>
                <table border=0 cellpadding=2 cellspacing=2 width=100%>
                <tr>
                <td rowspan="<?=$num_filas_seguimiento?>" class="tabla_notas" width=100><img src="imagenes/PersonajeNotas.png" border=0 width=100><br /></td>
                <?php 
                $cuenta_filas=0;
                while($incidencias=db_fetch_array($busca_incidencias)) {
                if ($cuenta_filas>0) {
                        ?>
                        <tr>
                        <?php 
                        }
        ?>
        <td class="tabla_notas_filas" style="vertical-align: top;max-width:100px!important;min-width:100px!important;"><?=$tipos_notas_inci[$incidencias["tipo_nota"]]?>: </td>
        <td class="tabla_notas_filas" style="vertical-align: top;"><?=$incidencias["nota"]?></td>
                        </tr>
        <?php 
                $cuenta_filas++;            
                }
                
                if ($cuenta_filas==0) {
                ?>
                <td class="tabla_notas_filas" colspan=2>&nbsp;</td>
                <?php 
                }
                        
                ?></table></td>
                </tr>
                </table>