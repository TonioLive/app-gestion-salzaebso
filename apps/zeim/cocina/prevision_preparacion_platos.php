<?php

require("../configuracion/parte_superior_sin_login_ancho.php");

include("actualiza_comedor.php");

$solo_escuela=$_GET["solo_escuela"];
if (strlen($solo_escuela)==0) {
        $solo_escuela=0;
        }

$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

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

?>
<div align="center" style="page-break-after: always;"><center>
<form name="formulario" method="GET" action="<?=$_SERVER["PHP_SELF"];?>">
<input type="hidden" name="solo_escuela" value="<?=$solo_escuela?>">
<input type="hidden" name="dia" value="<?=$inicio?>">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br><br><big>Asistencia Comedor - Listado Provisional</big></i></b><br><br></small></td>
  </tr>
<?php 

// Lista de centros con el tipo de cocina seleccionado
if ($_SERVER["HTTP_HOST"]=='gestion.escuelainfantillacreu.com') {
    $tipo_lista=2;
} else {
    $tipo_lista=1;
    if (($centro_actual==25) or ($_SERVER["HTTP_HOST"]=='gestion.escuelainfantillacreu.com')) {
        $tipo_lista=2;
    }
    if (($centro_actual==26) or ($_SERVER["HTTP_HOST"]=='gestion.escuelainfantilalbalat.com')) {
        $tipo_lista=3;
    }    
}

$buscaCentros=db_query("SELECT * FROM gestionguarderias.centros WHERE lista_compra=$tipo_lista ORDER BY nombre_centro") or die(db_error());

if (db_num_rows($buscaCentros)!=0) {

        $arrayCentrosTipo=array();

        while($datosCentro=db_fetch_array($buscaCentros)) {
                $arrayCentrosTipo[]=$datosCentro["id_centro"];
        }

        $listasStringCentros=implode(",",$arrayCentrosTipo);

        $filtroSqlCentros=" AND alumnos.id_centro IN ($listasStringCentros)";
        $filtroSqlCentrosEducadoras=" AND asistencia_docentes.id_centro_asistencia IN ($listasStringCentros)";
        $filtroSqlCentrosExternos=" AND albaranes_entrega_comida.id_centro IN ($listasStringCentros)";
        $filtroSqlCentrosLista=" AND centros.id_centro IN ($listasStringCentros)";
} else {
        $filtroSqlCentros="";
        $filtroSqlCentrosEducadoras="";
        $filtroSqlCentrosExternos="";
        $filtroSqlCentrosLista="";
}

if ($solo_escuela==1) {
        $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM gestionguarderias.usuarios, gestionguarderias.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'".$filtroSqlCentros." AND alumnos.id_centro=$centro_actual ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());
        $busca_alumnos1=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'".$filtroSqlCentros." AND alumnos.id_centro=$centro_actual ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());
        } else {
        $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM gestionguarderias.usuarios, gestionguarderias.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'".$filtroSqlCentros." ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());
        $busca_alumnos1=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM escuelamunicipal.usuarios, escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."'".$filtroSqlCentros." ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());
        }        

if ((db_num_rows($busca_alumnos)!=0) or (db_num_rows($busca_alumnos1)!=0)) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <?php 
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
        
        $tiene_comedor_hoy=$listado_alumnos["comedor"];
          
                // Comprueba si asiste hoy al comedor
        $id_inscripcion=$listado_alumnos["id_alumno"];

                $base_datos_actual="gestionguarderias";

        $busca_inscripcion_comedor=db_query("SELECT * FROM $base_datos_actual.servicios_contratados, $base_datos_actual.tarifa WHERE $base_datos_actual.servicios_contratados.id_servicio=$base_datos_actual.tarifa.ID AND $base_datos_actual.servicios_contratados.id_alumno=$id_inscripcion AND $base_datos_actual.tarifa.subcat=3 AND $base_datos_actual.servicios_contratados.fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND $base_datos_actual.servicios_contratados.fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());
        if (db_num_rows($busca_inscripcion_comedor)==0) {
                        $tiene_comedor_hoy=2;
                        } else {
                        $tiene_comedor_hoy=1;
                        }
  
        if (($tiene_comedor_hoy>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

        // Comprueba si está inscrito en una actividad que anula comedor
        $busca_actividad=db_query_params('SELECT * FROM gestionguarderias.servicios_consumidos_asistencia,gestionguarderias.tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=? AND (servicios_consumidos_asistencia.reserva=1 OR servicios_consumidos_asistencia.no_asiste=1)AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha=?', 'ss', array($listado_alumnos["id_usuario"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

        $menu_especial_hoy=false;
        if (db_num_rows($busca_actividad)!=0) {
                $menu_especial_hoy=true;
                }

        if (($menu_especial_hoy==true) or ($tiene_comedor_hoy!=2)) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM gestionguarderias.centros,gestionguarderias.centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=?', 's', array($listado_alumnos["aula"]));
        $datos_centro=db_fetch_array($busca_datos_centro);

        $nombre_centro_alumno=$datos_centro["nombre_centro"];
        $nombre_aula_alumno=$datos_centro["nombre"];

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

                // Busca menu apropiado para la edad
                        // Edad actual del niño en años con decimales
                        $edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                        if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                $edad_alumno=$edad_alumno-1;
                                }
                        $edad_alumno_anos=$edad_alumno;
                        // Decimales
                        $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                        if ($mes_edad_alumno<0) {
                                $edad_alumno_meses=(12+$mes_edad_alumno);
                                $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                } else {
                                $edad_alumno_meses=$mes_edad_alumno;
                                $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                }
                                        if ($edad_alumno_anos!=1) {
                                                $plural_anios="s";
                                                } else {
                                                $plural_anios="";
                                                }
                                        if ($edad_alumno_meses>1) {
                                                $plural_meses="es";
                                                } else {
                                                $plural_meses="";
                                                }

                // Comprueba si tiene un tipo de menu seleccionado
                $menu_actual_en_ficha=$listado_alumnos["menu_actual"];
                if ($menu_actual_en_ficha==-1) {
                                if ($datos_aula["id_menu_predefinido"]!=0) {
                                        $id_menu_seleccionado=$datos_aula["id_menu_predefinido"];
                                        } else {
                                        $menu_actual_en_ficha=0;
                                        }
                                }
                                
                if ($menu_especial_hoy==true) {
                        $menu_actual_en_ficha=0;
                        }
                                
                if ($menu_actual_en_ficha>0) {
                        $id_menu_seleccionado=$menu_actual_en_ficha;
                        } else {
                        if ($menu_actual_en_ficha==0) {

                        // Comprueba si es de regimen
                        $regimen=$tiene_comedor_hoy;
                        
                        if ($menu_especial_hoy==true) {
                                        $filtro_regimen=" AND menu_menus.regimen=2";
                                        } else {
                                        if ($regimen==3) {
                                                $filtro_regimen=" AND menu_menus.regimen=1";
                                                } else {
                                                $filtro_regimen=" AND menu_menus.regimen=0";
                                                }
                                        }

                        // Calcula el menú que le toca por edades
                        $busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus, gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
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
                                }

                        }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query_params('SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=?', 's', array($id_menu_seleccionado)) or die(db_error());
        if (db_num_rows($busca_datos_menu)!=0) {
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);

                // Comprueba los alimentos del menú y comprueba las alergias del niño

                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?;', 'ss', array($id_menu_seleccionado, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
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
                                                $consulta_datos_plato=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes, gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

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
                                                        } // Procesar plato
                                                } // Fin bucle platos

                                        } else {
                                        $motivo_no_hay="Día no programado para el menú asignado: ".urldecode($datos_menu_seleccionado["nombre_menu"]);
                                        }

                } else {
                        $nombre_menu="Menú no asignado";
                        $motivo_no_hay="No se ha encontrado menú adecuado para este niño programado para este día.";
                }

        $totales_menu[$id_menu_seleccionado]=$totales_menu[$id_menu_seleccionado]+1;
        $totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]=$totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]+1;
        $totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]=$totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]+1;

        if (($motivo_no_hay!="") or ($mostrar_listado==1)) {
        if ($hay_incidencias==false) {
        ?>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=5><font size=3 face="<?= $fuente_general; ?>"><b>Incidencias de Alergias y Alimentos no introducidos - Listado Provisional</b></font></td>
        </tr>
        <tr bgcolor="#efefef" valign="top">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Menú</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela/Aula</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
        </tr>
        <?php 
        }
        ?>
        <tr valign="top">
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        $total_menus++;
        echo $nombre_menu;
        ?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_aula_alumno." - ".$nombre_centro_alumno;?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$motivo_no_hay;?></font></td>
        </tr>
        <?php 
        $hay_incidencias=true;
        }

        $usuario_anterior=$listado_alumnos["id_usuario"];
                }
                }
        }
        // Fin Lapices
        // Inicio sant pau
        $incluir_santpau_cocina=true;
        if ($incluir_santpau_cocina==true) {
                        
                $usuario_anterior=0;

                while($listado_alumnos=db_fetch_array($busca_alumnos1)){

                        $no_merienda=$listado_alumnos["no_merienda"];
                
                        // Comprueba si asiste hoy al comedor
                        $id_inscripcion=$listado_alumnos["id_alumno"];

                        $base_datos_actual="escuelamunicipal";

                $busca_inscripcion_comedor=db_query("SELECT * FROM $base_datos_actual.servicios_contratados, $base_datos_actual.tarifa WHERE $base_datos_actual.servicios_contratados.id_servicio=$base_datos_actual.tarifa.ID AND $base_datos_actual.servicios_contratados.id_alumno=$id_inscripcion AND $base_datos_actual.tarifa.subcat=3 AND $base_datos_actual.servicios_contratados.fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND $base_datos_actual.servicios_contratados.fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());
                if (db_num_rows($busca_inscripcion_comedor)==0) {
                                $tiene_comedor_hoy=2;
                                } else {
                                $tiene_comedor_hoy=1;
                                }

                if (($tiene_comedor_hoy>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

                // Comprueba si está inscrito en una actividad que anula comedor
                $busca_actividad=db_query_params('SELECT * FROM escuelamunicipal.servicios_consumidos_asistencia,escuelamunicipal.tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=? AND (servicios_consumidos_asistencia.reserva=1 OR servicios_consumidos_asistencia.no_asiste=1)AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha=?', 'ss', array($listado_alumnos["id_usuario"], (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());

                $menu_especial_hoy=false;
                if (db_num_rows($busca_actividad)!=0) {
                        $menu_especial_hoy=true;
                        }

                if (($menu_especial_hoy==true) or ($tiene_comedor_hoy!=2)) {

                // Consulta centro
                $busca_datos_centro=db_query_params('SELECT * FROM escuelamunicipal.centros,escuelamunicipal.centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=?', 's', array($listado_alumnos["aula"]));
                $datos_centro=db_fetch_array($busca_datos_centro);

                $nombre_centro_alumno=$datos_centro["nombre_centro"];
                $nombre_aula_alumno=$datos_centro["nombre"];

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

                        // Busca menu apropiado para la edad
                                // Edad actual del niño en años con decimales
                                $edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
                                if (date("m",$fecha_time_stamp)<date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) {
                                        $edad_alumno=$edad_alumno-1;
                                        }
                                $edad_alumno_anos=$edad_alumno;
                                // Decimales
                                $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($listado_alumnos["fecha_nacimiento"]));

                                if ($mes_edad_alumno<0) {
                                        $edad_alumno_meses=(12+$mes_edad_alumno);
                                        $edad_alumno=$edad_alumno+round(((12+$mes_edad_alumno)/12),3);
                                        } else {
                                        $edad_alumno_meses=$mes_edad_alumno;
                                        $edad_alumno=$edad_alumno+round(($mes_edad_alumno/12),3);
                                        }
                                                if ($edad_alumno_anos!=1) {
                                                        $plural_anios="s";
                                                        } else {
                                                        $plural_anios="";
                                                        }
                                                if ($edad_alumno_meses>1) {
                                                        $plural_meses="es";
                                                        } else {
                                                        $plural_meses="";
                                                        }

                        // Comprueba si tiene un tipo de menu seleccionado
                        $menu_actual_en_ficha=$listado_alumnos["menu_actual"];
                        if ($menu_actual_en_ficha==-1) {
                                        if ($datos_aula["id_menu_predefinido"]!=0) {
                                                $id_menu_seleccionado=$datos_aula["id_menu_predefinido"];
                                                } else {
                                                $menu_actual_en_ficha=0;
                                                }
                                        }

                        if ($menu_especial_hoy==true) {
                                $menu_actual_en_ficha=0;
                                }

                        if ($menu_actual_en_ficha>0) {
                                $id_menu_seleccionado=$menu_actual_en_ficha;
                                } else {
                                if ($menu_actual_en_ficha==0) {

                                // Comprueba si es de regimen
                                $regimen=$tiene_comedor_hoy;

                                if ($menu_especial_hoy==true) {
                                                $filtro_regimen=" AND menu_menus.regimen=2";
                                                } else {
                                                if ($regimen==3) {
                                                        $filtro_regimen=" AND menu_menus.regimen=1";
                                                        } else {
                                                        $filtro_regimen=" AND menu_menus.regimen=0";
                                                        }
                                                }

                                // Calcula el menú que le toca por edades					
                                $busca_menu_apropiado=db_query("SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
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
                                        }							
                                }

                // Consulta el nombre del menú
                $busca_datos_menu=db_query_params('SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=?', 's', array($id_menu_seleccionado));
                if (db_num_rows($busca_datos_menu)!=0) {
                        $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                        $nombre_menu=urldecode($datos_menu_seleccionado["nombre_menu"]);

                        // Comprueba los alimentos del menú y comprueba las alergias del niño

                                        // Comprueba si ese día hay comida programada
                                        $hay_comida=false;

                                        $busca_comida_ese_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?;', 'ss', array($id_menu_seleccionado, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
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
                                                        $consulta_datos_plato=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                        if (db_num_rows($consulta_datos_plato)!=0) {
                                                                $datos_plato=db_fetch_array($consulta_datos_plato);


                                                                // Comprueba platos
                                                                $busca_ingredientes=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes, gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

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
                                                                } // Procesar plato
                                                        } // Fin bucle platos

                                                } else {
                                                $motivo_no_hay="Día no programado para el menú asignado: ".urldecode($datos_menu_seleccionado["nombre_menu"]);
                                                }

                        } else {
                        $nombre_menu="Menú no asignado";
                                        $motivo_no_hay="No se ha encontrado menú adecuado para este niño programado para este día.";
                        }

                $totales_menu[$id_menu_seleccionado]=$totales_menu[$id_menu_seleccionado]+1;
                $totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]=$totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]+1;
                $totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]=$totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]+1;

                if (($motivo_no_hay!="") or ($mostrar_listado==1)) {
                if ($hay_incidencias==false) {
                ?>
                <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
                <tr>
                <td bgcolor="#efefef" align="left" colspan=5><font size=3 face="<?= $fuente_general; ?>"><b>Incidencias de Alergias y Alimentos no introducidos - Listado Provisional</b></font></td>
                </tr>
                <tr bgcolor="#efefef" valign="top">
                <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Menú</b></font></td>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela/Aula</b></font></td>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                </tr>
                <?php 
                }
                ?>
                <tr valign="top">
                <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></font></td>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
                $total_menus++;
                echo $nombre_menu;
                ?></font></td>
                <td><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_aula_alumno." - ".$nombre_centro_alumno;?></font></td>
                <td><font size=1 face="<?= $fuente_general; ?>"><?=$motivo_no_hay;?></font></td>
                </tr>
                <?php 
                $hay_incidencias=true;
                }

                $usuario_anterior=$listado_alumnos["id_usuario"];
                        }
                        }
                }
        }
        // Fin sant pau
        if ($hay_incidencias==true) {
        ?>
        </table>
        <?php 
        }

        // Cuenta los menús de educador
        $menus_edu=0;

        $busca_menu_educadora=db_query("SELECT * FROM gestionguarderias.asistencia_docentes, gestionguarderias.usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1".$filtroSqlCentrosEducadoras." ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy");

        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM gestionguarderias.centros WHERE id_centro=?', 's', array($menu_educadora["id_centro_asistencia"]));
        if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $nombre_centro_educador=$datos_centro["nombre_centro"];
                } else {
                $nombre_centro_educador="No indicado";
                }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]);
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
                                $busca_comida_ese_dia=db_query("SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]." AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) { // Inici bucle platos

                                                $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                                if ($codigo_plato!=0) {
                                                        $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                        $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;
                                                        $totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]=$totales_platos_escuelas[$codigo_plato][$menu_educadora["id_centro_asistencia"]]+1;
                                                        }

                        $busca_ingredientes_plato=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=? ORDER BY menu_alimentos.id_alimento;', 's', array($codigo_plato));

                        while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+$ingredientes["cantidad_racion"];

                                }

                                                } // Fin Bucle platos
                                        } else {
                                        $observaciones_edu="Día no programado para el menú seleccionado";
                                        }

        $menus_edu++;
        }
        // Fin lapices
        // Inicio sant pau
        $busca_menu_educadora=db_query("SELECT * FROM escuelamunicipal.asistencia_docentes, escuelamunicipal.usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1".$filtroSqlCentrosEducadoras." ORDER BY asistencia_docentes.id_centro_asistencia,asistencia_docentes.menu_seleccionado_hoy");

        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {

        // Consulta centro
        $busca_datos_centro=db_query_params('SELECT * FROM gestionguarderias.centros WHERE id_centro=?', 's', array($menu_educadora["id_centro_asistencia"]));
        if (db_num_rows($busca_datos_centro)!=0) {
                $datos_centro=db_fetch_array($busca_datos_centro);
                $nombre_centro_educador=$datos_centro["nombre_centro"];
                } else {
                $nombre_centro_educador="No indicado";
                }

        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM gestionguarderias.menu_menus WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]);
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
                                $busca_comida_ese_dia=db_query("SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]." AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) { // Inicio bucle platos

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

                                                } // Fin bucle platos
                                        } else {
                                        $observaciones_edu="Día no programado para el menú seleccionado";
                                        }

        $menus_edu++;
        }
        // Fin sant pau
        // Inicio Escuelas Externas
        
        $buscaEscuelasExternas = db_query("SELECT * FROM gestionguarderias.centros WHERE menu_externo=1 and lista_compra=$tipo_lista");

        while($centroExterno=db_fetch_array($buscaEscuelasExternas)) {

                if ($centroExterno['omitir_merienda']==0) {
                        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");
                } else {
                        $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1");
                }


                // $consultaUltimoPedido = "SELECT * FROM menu_pedidos_externos WHERE fecha<='".date("Y-m-d",$fecha_time_stamp)."' AND id_centro=".$centroExterno['id_centro']." AND cantidad<>0 AND confirmado=1 ORDER BY fecha DESC,id_menu LIMIT 0,1;";
                // Busca pedido de la última semana

                $buscaUltimoPedido = db_query_params('SELECT * FROM gestionguarderias.menu_pedidos_externos WHERE fecha<=? AND fecha>=? AND id_centro=? AND cantidad<>0 AND confirmado=1 ORDER BY fecha DESC,id_menu LIMIT 0,1;', 'sss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp - 86400 * 30)), $centroExterno['id_centro']));
                
                if (db_num_rows($buscaUltimoPedido)!=0) {
                
                        $ultimoPedido=db_fetch_array($buscaUltimoPedido);
                
                        $buscaMenusExternos = db_query_params('SELECT * FROM gestionguarderias.menu_pedidos_externos WHERE fecha=? AND id_centro=? AND cantidad<>0 AND confirmado=1 ORDER BY id_menu;', 'ss', array((string)($ultimoPedido["fecha"]), $centroExterno['id_centro']));    	
                                
                        while($menusExternos=db_fetch_array($buscaMenusExternos)) {

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
                                        $busca_comida_ese_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?;', 'ss', array($id_menu_seleccionado, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                                        if (db_num_rows($busca_comida_ese_dia)!=0) {

                                                $hay_comida=true;
                                                $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                                for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                                
                                                        if ($codigo_plato!=0) {
                                                                $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+$numero_menus_extras;
                                                                $totales_platos_escuelas[$codigo_plato][$menusExternos["id_centro"]]=$totales_platos_escuelas[$codigo_plato][$menusExternos["id_centro"]]+$numero_menus_extras;
                                                        }

                                                        $busca_ingredientes_plato=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=? ORDER BY menu_alimentos.id_alimento;', 's', array($codigo_plato));

                                                        while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                                                                $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
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
        
                }

        }
        // Fin escuelas externas
        // Buscamos observaciones escuelas externas
        $buscaObservaciones=db_query("SELECT * FROM gestionguarderias.albaranes_entrega_comida LEFT JOIN centros ON gestionguarderias.albaranes_entrega_comida.id_centro=gestionguarderias.centros.id_centro WHERE fecha='".date("Y-m-d",$fecha_time_stamp)."'".$filtroSqlCentrosExternos.";");

        $listaObservaciones='';

        while($datosObservaciones=db_fetch_array($buscaObservaciones)) {

                if (strlen($datosObservaciones['observaciones_pedido'])!=0) {
                        $listaObservaciones.="<tr><td>".$datosObservaciones['nombre_centro']."</td><td>".$datosObservaciones['observaciones_pedido']."</td></tr>";        
                }

        }

        if ($listaObservaciones!='') {
                ?>
                <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
                        <tr>
                                <td bgcolor="#efefef" align="left" colspan=2><font color="#ff0000" size=3 face="<?= $fuente_general; ?>"><b>Observaciones pedidos externos</b></font></td>
                        </tr>
                        <tr>
                                <td bgcolor="#efefef" align="left"><font size=3 face="<?= $fuente_general; ?>"><b>Escuela</b></font></td>
                                <td bgcolor="#efefef" align="left"><font size=3 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                        </tr>    
                <?php
                        echo($listaObservaciones);
                ?>
                </table>
                <?php
        }

        // FIN OBSERVACIONES
        ?>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=7><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de menús servidos - Listado Definitivo</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre menú</b></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE (centro_propio=1 or menu_externo=1) and (no_mostrar_listados_menu=0)".$filtroSqlCentrosLista." ORDER BY menu_externo,nombre_centro;");
        $numero_de_centros=db_num_rows($busca_centros);
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$centros["nombre_centro"];?></b></font></td>
        <?php 
        }
        ?>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Extras</b></font></td>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad Total</b></font></td>
        </tr>
        <?php 
        $consulta_datos_menus=db_query_params('SELECT * FROM gestionguarderias.menu_menus,gestionguarderias.menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha=? ORDER BY menu_menus.edad_ini_menu;', 's', array((string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
        // if (($totales_menu[$datos_menus["id_menu"]]>0) or ($datos_menus["id_menu"]==15)) {

        ?>
        <tr>
                <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"><?=urldecode($datos_menus["nombre_menu"])?></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE (centro_propio=1 or menu_externo=1) and (no_mostrar_listados_menu=0)".$filtroSqlCentrosLista." ORDER BY menu_externo,nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#ffffff" align="right"><?=number_format($totales_menu_escuelas[$datos_menus["id_menu"]][$centros["id_centro"]],0,",",".");?></td>
        <?php 
        }

        $numero_menus_extras=$_GET["ext".$datos_menus["id_menu"]];
        if (strlen($numero_menus_extras)==0) {
                $numero_menus_extras=0;
                }
         if (is_numeric($numero_menus_extras)==false) {
                $numero_menus_extras=0;
                }

        if ($numero_menus_extras!=0) {
               // Agrega ingredientes de extras

        // Consulta el nombre del menú
        $id_menu_seleccionado=$datos_menus["id_menu"];
        $nombre_menu=urldecode($datos_menus["nombre_menu"]);

         $totales_menu[$id_menu_seleccionado]=$totales_menu[$id_menu_seleccionado]+$numero_menus_extras;
         $totales_menu_escuelas[$id_menu_seleccionado][9999]=$totales_menu_escuelas[$id_menu_seleccionado][9999]+$numero_menus_extras;


                // Comprueba los alimentos del menú y comprueba las alergias del niño
                $array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;
                                $observaciones_edu="";
                                $busca_comida_ese_dia=db_query_params('SELECT * FROM gestionguarderias.menu_calendario WHERE id_menu=? AND fecha=?;', 'ss', array($id_menu_seleccionado, (string)(date("Y-m-d", $fecha_time_stamp)))) or die(db_error());
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) { // Inicio bucle platos

                                                $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                                if ($codigo_plato!=0) {
                                                        $totales_platos[$codigo_plato][1]=urldecode($datos_plato["nombre_plato"])." ".$edades;
                                                        $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+$numero_menus_extras;
                                                        $totales_platos_escuelas[$codigo_plato][9999]=$totales_platos_escuelas[$codigo_plato][9999]+$numero_menus_extras;
                                                        }

                        $busca_ingredientes_plato=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes,gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND id_plato=? ORDER BY menu_alimentos.id_alimento;', 's', array($codigo_plato));

                        while($ingredientes=db_fetch_array($busca_ingredientes_plato)) {

                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][1]=urldecode($ingredientes["nombre_alimento"]);
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][2]=$ingredientes["unidad_medida"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][3]=$ingredientes["peso_unidad"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][4]=$ingredientes["cantidad_racion"];
                                        $totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]=$totales_ingredientes[$codigo_plato][$ingredientes["id_alimento"]][0]+($ingredientes["cantidad_racion"]*$numero_menus_extras);

                                }

                                                } // Fin bucle platos
                                        // } else {
                                        // $observaciones_edu="Día no programado para el menú seleccionado";
                                        // }

                }  // Fin ingredientes menú
        }

        ?>
                <td bgcolor="#ffffff" align="right"><input type="text" size="8" style="text-align: right" name="ext<?=$datos_menus["id_menu"];?>" id="ext<?=$datos_menus["id_menu"];?>" value="<?=$numero_menus_extras?>" onblur="document.formulario.submit();"></td>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$totales_menu[$datos_menus["id_menu"]];?></font></td>
        </tr>
        <?php 

        }
        ?>
        </table></td>
</tr>
</table>
</center></div>
        <?php 
        $consulta_datos_plato=db_query("SELECT * FROM gestionguarderias.menu_platos GROUP BY nombre_plato ORDER BY nombre_plato") or die(db_error());

        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $plato_actual=urldecode($datos_plato["nombre_plato"]);
        $total_absoluto=0;
        $contador_platos=0;
        $num_hechas_total=0;
        $nombre_recipiente_plato=urldecode($datos_plato["nombre_recipiente"]);

        // Busca variantes del plato
        $busca_variantes=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE nombre_plato=? ORDER BY edad_ini_plato DESC', 's', array((string)($datos_plato["nombre_plato"]))) or die(db_error());

        while($datos_variantes=db_fetch_array($busca_variantes)) {

        $codigo_plato=$datos_variantes["id_plato"];

        if (intval($totales_platos[$codigo_plato][0])>0) {

        if ($contador_platos==0) {
        // Encabezado
        ?>
        <div style="page-break-after: always;">
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left"><a href="preparacion_platos/lista_platos.php?id_plato=<?=$codigo_plato;?>&dia=<?=$inicio;?>" target="_new"><font size=3 face="<?= $fuente_general; ?>"><b>Preparación de Platos para el día <?=date("d/m/Y",$fecha_time_stamp)?> - Listado Definitivo</b></font></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$plato_actual?></b></font></td>
        </tr>
        </table>
        <?php 
        // Encabezado ingredientes
        ?>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
                <td bgcolor="#ffffff" align="left" rowspan=3><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <?php 
        // Muestra ingredientes necesarios
        $busca_ingredientes=db_query_params('SELECT * FROM gestionguarderias.menu_ingredientes, gestionguarderias.menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=? ORDER BY menu_alimentos.id_alimento', 's', array($codigo_plato)) or die(db_error());

        $numero_ingredientes=db_num_rows($busca_ingredientes);
        $contador_ingredientes=0;
        if (isset($array_ingredientes_ordenados)) {
                unset($array_ingredientes_ordenados);
                }
        $array_ingredientes_ordenados=array();
        if (isset($total_absoluto_escuelas)) {
                unset($total_absoluto_escuelas);
                }
        $total_absoluto_escuelas=array();
        $total_absoluto=0;
        ?>
        <td bgcolor="#efefef" colspan="<?=$numero_ingredientes*2;?>" align="center"><font size=2><b>Ingredientes</b></font></td>
        </tr>
        <?php 

        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                $array_ingredientes_ordenados[$contador_ingredientes][0]=$ingredientes["id_alimento"];
                $array_ingredientes_ordenados[$contador_ingredientes][5]=$ingredientes["preparar_separado"];
                $array_ingredientes_ordenados[$contador_ingredientes][6]=urldecode($ingredientes["nombre_alimento"]);
                ?>
                <td bgcolor="#efefef" align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b><?=urldecode($ingredientes["nombre_alimento"]);?></b></font></td>
                <?php 
                $contador_ingredientes++;
                }
                ?>
                <tr>
                <?php 
                for($c1=0;$c1<$numero_ingredientes;$c1++) {
                ?>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total plato</b></font></td>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Por niño</b></font></td>
                <?php 
                }
                ?>
        </tr>
        <?php 
        }
        $contador_platos++;


$edad_ini_actual=$datos_variantes["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_variantes["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades=" De ".$edad_ini_actual." a ".$edad_fin_actual;

        // $nombre_mostrado=substr($totales_platos[$codigo_plato][1],(strlen($plato_actual)+1),(strlen($totales_platos[$codigo_plato][1])-strlen($plato_actual)-1));
        $nombre_mostrado=$edades;
        ?>
        <tr>
                <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_mostrado;?></b></font></td>
        <?php 
        // Bucle alimentos

        // Muestra ingredientes necesarios

        for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                $id_alimento=$array_ingredientes_ordenados[$c_ingredientes][0];
                $nombre_ingrediente=$totales_ingredientes[$codigo_plato][$id_alimento][1];
                $unidad_de_medida=$totales_ingredientes[$codigo_plato][$id_alimento][2];
                $peso_unidad=$totales_ingredientes[$codigo_plato][$id_alimento][3];
                $cantidad_raciones=$totales_ingredientes[$codigo_plato][$id_alimento][0];
                $tam_racion=$totales_ingredientes[$codigo_plato][$id_alimento][4];

                if ($unidad_de_medida==0) {
                                        $coletilla_unidad="gr.";
                                        } else {
                                        $coletilla_unidad="ml.";
                                        }
                if ($peso_unidad>0) {
                        $unidades_prod=$cantidad_raciones/$peso_unidad;
                        if (round($unidades_prod,2)>round($unidades_prod,0)) {
                                $unidades_prod=round($unidades_prod,0)+1;
                                } else {
                                $unidades_prod=round($unidades_prod,0);
                                }
                        $unidades_racion=$tam_racion/$peso_unidad;
                        if (round($unidades_racion,2)>round($unidades_racion,0)) {
                                $unidades_racion=round($unidades_racion,0)+1;
                                } else {
                                $unidades_racion=round($unidades_racion,0);
                                }
                        } else {
                        $unidades_prod=$cantidad_raciones;
                        $unidades_racion=$tam_racion;
                        }
                if ($peso_unidad==1) {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($cantidad_raciones,2,",",".");?> <?=$coletilla_unidad;?></font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($tam_racion,2,",",".");?> <?=$coletilla_unidad;?></font></td>
                <?php 
                } else {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"><a title="<?=number_format($cantidad_raciones,2,",",".");?> <?=$coletilla_unidad;?>" href="#"><?=number_format($unidades_prod,2,",",".");?> Uds.</a></font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"><a title="<?=number_format($tam_racion,2,",",".");?> <?=$coletilla_unidad;?>" href="#"><?=number_format($unidades_racion,2,",",".");?> Uds.</a></font></td>
                <?php 
                }
                $array_ingredientes_ordenados[$c_ingredientes][1]=$array_ingredientes_ordenados[$c_ingredientes][1]+$cantidad_raciones;
                $array_ingredientes_ordenados[$c_ingredientes][2]=$array_ingredientes_ordenados[$c_ingredientes][2]+$unidades_prod;
                $array_ingredientes_ordenados[$c_ingredientes][3]=$peso_unidad;
                $array_ingredientes_ordenados[$c_ingredientes][4]=$coletilla_unidad;
                }
                $total_absoluto=$total_absoluto+$totales_platos[$codigo_plato][0];
                $num_hechas_total=$num_hechas_total+($totales_platos[$codigo_plato][0]/$datos_variantes["raciones"]);

                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE (centro_propio=1 or menu_externo=1) and (no_mostrar_listados_menu=0)".$filtroSqlCentrosLista." ORDER BY nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                $total_absoluto_escuelas[$centros["id_centro"]]=$total_absoluto_escuelas[$centros["id_centro"]]+$totales_platos_escuelas[$codigo_plato][$centros["id_centro"]];
                }

                ?>
        </tr>
        <?php 
                        }
                }
        if ($contador_platos>0) {
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Total todas las edades</b></font></td>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][1],2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>

                <?php 
                } else {
                ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][2],2,",",".");?> Uds.</font></td>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <?php 
                }
                }
                ?>
                </tr>
                </table>
                <?php 
                if ($nombre_recipiente_plato!="") {
                ?>
                <br>
                <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
                <tr><td bgcolor="#efefef" align="left" colspan=<?=($contador_ingredientes+1)?>><font size=3 face="<?= $fuente_general; ?>"><b>Instrucciones preparación</b></font></td></tr>
                <tr>
                <td><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <?php 
                $hay_separados=false;
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][5]==0) {
                ?>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b><?=$array_ingredientes_ordenados[$c_ingredientes][6];?></b></font></td>
                <?php 
                } else {
                $hay_separados=true;
                }
                        }
                ?>
                </tr>
                <?php 
                $numero_completas=round($num_hechas_total,0);
                if ($numero_completas>$num_hechas_total) {
                        $numero_completas=$numero_completas-1;
                        }
                $numero_incompletas=$num_hechas_total-$numero_completas;
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$numero_completas?> <?=$nombre_recipiente_plato?> completas cada una con los siguientes ingredientes: </b></font></td>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][5]==0) {
                if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total);
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($ingredientes_por_olla,2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>

                        <?php 
                        } else {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total);
                        $cantidad_raciones=$ingredientes_por_olla;
                        $peso_unidad=$array_ingredientes_ordenados[$c_ingredientes][3];
                        if ($peso_unidad>0) {
                                $unidades_prod=$cantidad_raciones/$peso_unidad;
                                if (round($unidades_prod,2)>round($unidades_prod,0)) {
                                        $unidades_prod=round($unidades_prod,0)+1;
                                        } else {
                                        $unidades_prod=round($unidades_prod,0);
                                        }
                                $unidades_racion=$tam_racion/$peso_unidad;
                                if (round($unidades_racion,2)>round($unidades_racion,0)) {
                                        $unidades_racion=round($unidades_racion,0)+1;
                                        } else {
                                        $unidades_racion=round($unidades_racion,0);
                                        }
                                } else {
                                $unidades_prod=$cantidad_raciones;
                                $unidades_racion=$tam_racion;
                                }
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($unidades_prod,2,",",".");?> Uds.</font></td>
                        <?php 
                        }
                        }
                }
                ?>
                </tr>
                <?php 
                if ($numero_incompletas>0) {
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>1 <?=$nombre_recipiente_plato?> con los siguientes ingredientes: </b></font></td>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                if ($array_ingredientes_ordenados[$c_ingredientes][5]==0) {
                if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total)*$numero_incompletas;
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($ingredientes_por_olla,2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>
                        <?php 
                        } else {
                        $ingredientes_por_olla=($array_ingredientes_ordenados[$c_ingredientes][1]/$num_hechas_total)*$numero_incompletas;
                        $cantidad_raciones=$ingredientes_por_olla;
                        $peso_unidad=$array_ingredientes_ordenados[$c_ingredientes][3];
                        if ($peso_unidad>0) {
                                $unidades_prod=$cantidad_raciones/$peso_unidad;
                                if (round($unidades_prod,2)>round($unidades_prod,0)) {
                                        $unidades_prod=round($unidades_prod,0)+1;
                                        } else {
                                        $unidades_prod=round($unidades_prod,0);
                                        }
                                $unidades_racion=$tam_racion/$peso_unidad;
                                if (round($unidades_racion,2)>round($unidades_racion,0)) {
                                        $unidades_racion=round($unidades_racion,0)+1;
                                        } else {
                                        $unidades_racion=round($unidades_racion,0);
                                        }
                                } else {
                                $unidades_prod=$cantidad_raciones;
                                $unidades_racion=$tam_racion;
                                }
                        ?>
                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($unidades_prod,2,",",".");?> Uds.</font></td>
                        <?php 
                        }
                        }
                }
                ?>
                </tr>
                <?php 
                }
                ?>
                </table>
                <br>
                <?php 
                if ($hay_separados==true) {
                ?>
                <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
                <tr><td bgcolor="#efefef" align="left" colspan=2><font size=3 face="<?= $fuente_general; ?>"><b>Preparar separado</b></font></td></tr>
                <?php 
                for($c_ingredientes=0;$c_ingredientes<$contador_ingredientes;$c_ingredientes++) {
                        if ($array_ingredientes_ordenados[$c_ingredientes][5]==1) {
                        ?>
                        <tr>
                                <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$array_ingredientes_ordenados[$c_ingredientes][6];?></b></font></td>
                        <?php 
                        if ($array_ingredientes_ordenados[$c_ingredientes][3]==1) {
                                        ?>
                                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][1],2,",",".");?> <?=$array_ingredientes_ordenados[$c_ingredientes][4];?></font></td>
                                        <?php 
                                        } else {
                                        ?>
                                        <td bgcolor="#ffffff" align="right"><font size=1 face="<?= $fuente_general; ?>"> <?=number_format($array_ingredientes_ordenados[$c_ingredientes][2],2,",",".");?> Uds.</font></td>
                                        <?php 
                                        }
                                ?>
                                </tr>
                                <?php 
                                }
                        }
                        ?>
                        </table>
                        <?php 
                        }
                }
                ?>
        <br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=75%>
        <tr><td bgcolor="#efefef" align="left" colspan="<?=$numero_de_centros+4;?>"><font size=3 face="<?= $fuente_general; ?>"><b>Nº de raciones servidas</b></font></td></tr>
        <tr><td><font size=1>&nbsp;</font></td>
        <?php 
        // Instrucciones de preparacion
        if (isset($total_absoluto_escuelas)) {
                unset($total_absoluto_escuelas);
                }
        $total_absoluto_escuelas=array();
        $total_absoluto=0;

                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE (centro_propio=1 or menu_externo=1) and (no_mostrar_listados_menu=0)".$filtroSqlCentrosLista." ORDER BY menu_externo,nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                ?>
                <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$centros["nombre_centro"];?></b></font></td>
                <?php 
                }
                ?>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Extras</b></font></td>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                <td bgcolor="#efefef" align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Instrucciones preparación de raciones</b></font></td>
        </tr>
        <?php 
        // Busca variantes del plato
        $busca_variantes=db_query_params('SELECT * FROM gestionguarderias.menu_platos WHERE nombre_plato=? ORDER BY edad_ini_plato DESC', 's', array((string)($datos_plato["nombre_plato"]))) or die(db_error());

        while($datos_variantes=db_fetch_array($busca_variantes)) {

        $codigo_plato=$datos_variantes["id_plato"];

        if (intval($totales_platos[$codigo_plato][0])>0) {

        $contador_platos++;

$edad_ini_actual=$datos_variantes["edad_ini_plato"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_variantes["edad_fin_plato"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades=" De ".$edad_ini_actual." a ".$edad_fin_actual;

        // $nombre_mostrado=substr($totales_platos[$codigo_plato][1],(strlen($plato_actual)+1),(strlen($totales_platos[$codigo_plato][1])-strlen($plato_actual)-1));
        $nombre_mostrado=$edades;

        ?>
        <tr>
                <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_mostrado;?></b></font></td>
                <?php 
                $total_absoluto=$total_absoluto+$totales_platos[$codigo_plato][0];

                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE (centro_propio=1 or menu_externo=1) and (no_mostrar_listados_menu=0)".$filtroSqlCentrosLista." ORDER BY menu_externo,nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                $total_absoluto_escuelas[$centros["id_centro"]]=$total_absoluto_escuelas[$centros["id_centro"]]+$totales_platos_escuelas[$codigo_plato][$centros["id_centro"]];
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($totales_platos_escuelas[$codigo_plato][$centros["id_centro"]]);?></font></td>
                <?php 
                }
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($totales_platos_escuelas[$codigo_plato][9999]);?></font></td>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=intval($totales_platos[$codigo_plato][0]);?></font></td>
                <td bgcolor="#ffffff" align="left"><font size=1 face="<?= $fuente_general; ?>"> <?=urldecode($datos_variantes["instrucciones_servir"]);?></font></td>
        </tr>
                <?php 
                }
        }
        if ($contador_platos>0) {
                ?>
                <tr>
                 <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Total todas las edades</b></font></td>
                <?php 
                // Platos por escuela
                $busca_centros=db_query("SELECT * FROM gestionguarderias.centros WHERE (centro_propio=1 or menu_externo=1) and (no_mostrar_listados_menu=0)".$filtroSqlCentrosLista." ORDER BY menu_externo,nombre_centro;");
                while($centros=db_fetch_array($busca_centros)) {
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_absoluto_escuelas[$centros["id_centro"]]);?></font></td>
                <?php 
                }
                ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_absoluto_escuelas[9999]);?></font></td>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"><?=intval($total_absoluto);?></font></td>
                <td bgcolor="#ffffff" align="left"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                </tr>
                </table><br>
                </div>
                <?php 
                }
                }
        }

} else {
?>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en el comedor.<br><br><br></td>
  </tr>
</table>
<?php 

}
?>
        </div>
    </td>
  </tr>
</table>
</form>
</div>
</body>
</html>
