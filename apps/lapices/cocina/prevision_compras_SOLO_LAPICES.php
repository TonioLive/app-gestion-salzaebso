<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$totales_extras=array();
$array_alimentos_cantidades=array();
$array_alimentos_cantidades_menu=array();

$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");

$edad_menu=2;

$seleccionados_calendario=$_GET["cd"];
if (strlen($seleccionados_calendario)==0) {
        $seleccionados_calendario=0;
        }

$inicio=$_GET["dia"];
$es_mes=$_GET["mes"];
$anio_seleccionado=$_GET["ano"];
$id_menu=$_GET["id_menu"];

if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
}


if (strlen($es_mes)!=0) {
        $dia_semana_inicio_mes=date("N",strtotime("$anio_seleccionado/$es_mes/1"))-1;
        $dias_semana_tras_fin_mes=7-(date("N",strtotime("$anio_seleccionado/$es_mes/".date("t",strtotime("$anio_seleccionado/$es_mes/1")))));
        $duracion_periodo=date("t",strtotime("$anio_seleccionado/$es_mes/1"))+$dia_semana_inicio_mes+$dias_semana_tras_fin_mes;
        } else {
        $duracion_periodo=6;
        }

$fin_periodo=$_GET["fin"];
if (strlen($fin_periodo)==0)
{
  $fin_time_stamp=$fecha_time_stamp+($duracion_periodo*86400);
}
  else
{
  $fin_time_stamp=$fin_periodo;
}

$fecha_inicio_periodo=$fecha_time_stamp;
$fecha_fin_periodo=$fin_time_stamp;

if (strlen($es_mes)!=0) {
        $texto_encabezado=$nombre_mes[$es_mes]." de ".$anio_seleccionado;
        } else {
        if ($fin_time_stamp==$fecha_time_stamp) {
                $texto_encabezado="Día ".date("d/m/Y",$fecha_time_stamp);
                } else {
                if (strlen($_GET["fin"])!=0) {
                        $texto_encabezado="Periodo del ".date("d/m/Y",$fecha_time_stamp)." al ".date("d/m/Y",$fin_time_stamp);
                        } else {
                        $texto_encabezado="Semana del ".date("d/m/Y",$fecha_time_stamp)." al ".date("d/m/Y",$fin_time_stamp);
                        }
                }
        }

        

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<div id="botonera" style="display:block;padding:0px;border:0px solid"><br>
<input type="button" onclick="imprimir()" value="Imprimir" style="font-size: 10px"> <input type="button" onclick="window.close()" value="Salir" style="font-size: 10px"><br><br>
</div>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><b>Nº de menús por típos - <?=$texto_encabezado?></i></b></td>
  </tr>
<?php 

// Determina el minimo minimorum
$busca_minimo=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu") or die(db_error());
$minimo_minimorum=0;
if (db_num_rows($busca_minimo)!=0) {
        $menu_minimo=db_fetch_array($busca_minimo);
        $minimo_minimorum=$menu_minimo["edad_ini_menu"];
        }

$listado_errores="";
$array_errores=array();

?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <?php 
        // Totales centro
        $totales_menu=array();
        $totales_centro=array();

$array_hiddens="";

while($fecha_time_stamp<=$fin_time_stamp) {

        if (date("H:i:s",$fecha_time_stamp)=="01:00:00") {
                $fecha_time_stamp=$fecha_time_stamp-3600;
                }
                
        if (date("H:i:s",$fecha_time_stamp)=="23:00:00") {
                $fecha_time_stamp=$fecha_time_stamp+3600;
                }

        // Comprueba si el dia ha sido seleccionado
        if (($seleccionados_calendario==0) or ($_POST[date("Y-m-d",$fecha_time_stamp)]==1) or ($_GET[date("Y-m-d",$fecha_time_stamp)]==1)) {
        if ($seleccionados_calendario==1) {
                $array_hiddens=$array_hiddens.'<input type="hidden" name="'.date("Y-m-d",$fecha_time_stamp).'" value="1">';
                }
        
        $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
                // Comprueba si está inscrito en una actividad que anula comedor
                $busca_actividad=db_query("SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_alumno=".$listado_alumnos["id_usuario"]." AND (servicios_consumidos_asistencia.reserva=1 OR servicios_consumidos_asistencia.no_asiste=1)AND tarifa.anula_comedor=1 AND servicios_consumidos_asistencia.fecha='".date("Y-m-d",$fecha_time_stamp)."'") or die(db_error());

                $menu_especial_hoy=false;
                if (db_num_rows($busca_actividad)!=0) {
                        $menu_especial_hoy=true;
                        }
        
                if (($menu_especial_hoy==true) or ($listado_alumnos["comedor"]!=2)) {
                
                                        $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alumnos["centro_alumno"]);
                                        $datos_centro=db_fetch_array($busca_datos_centro);

                                        $nombre_centro_alumno=$datos_centro["nombre_centro"];
                                        $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$listado_alumnos["aula"]);
                                        $datos_aula=db_fetch_array($busca_datos_aula);

                                        $nombre_aula_alumno=$datos_aula["nombre"];
                                        
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
                
                if ($menu_especial_hoy==true) {
                        $menu_actual_en_ficha=0;
                        }
                
                $coletilla_error="";
                $tipo_seleccion_menu="";
                if ($menu_actual_en_ficha==-1) {
                                if ($datos_aula["id_menu_predefinido"]!=0) {
                                        $tipo_seleccion_menu="Menú asignado en aula";
                                        $id_menu_seleccionado=$datos_aula["id_menu_predefinido"];
                                        } else {
                                        $menu_actual_en_ficha=0;
                                        $coletilla_error=" (El aula no tiene menú asignado)";
                                        }
                                }
                if ($menu_actual_en_ficha>0) {
                        $tipo_seleccion_menu="Menú asignado en ficha".$coletilla_error;
                        $id_menu_seleccionado=$menu_actual_en_ficha;
                        } else {
                        if ($menu_actual_en_ficha==0) {
                        $tipo_seleccion_menu="Automático por edad".$coletilla_error;

                        // Comprueba si es de regimen
                        $regimen=$listado_alumnos["comedor"];
                        
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
                        $busca_menu_apropiado=db_query("SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND (menu_menus.disponible_desde<='".date("Y-m-d",$fecha_time_stamp)."' AND menu_menus.disponible_hasta>='".date("Y-m-d",$fecha_time_stamp)."') AND menu_menus.edad_ini_menu<=$edad_alumno$filtro_regimen");
                        // $busca_menu_apropiado=mysql_query("SELECT * FROM menu_menus WHERE edad_ini_menu<=$edad_alumno$filtro_regimen");
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
                                if (($id_menu_seleccionado==0) and ($array_errores[$listado_alumnos["id_alumno"]]!=1)) {
                                        if ($edad_alumno<$minimo_minimorum) {
                                                $motivo_error="La edad del niño no llega a la edad mínima (".round(($minimo_minimorum*12),0)." meses) admitida en los menús.<small><br><b>Propuestas:</b><br>1. Introducir un menú apropiado para la edad del niño.<br>2. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
                                                } else {
                                                $motivo_error="El cía ".date("d/m/Y",$fecha_time_stamp)." no se encuentra un menú apropiado para la edad del niño.<small><br><b>Propuestas:</b><br>1. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
                                                }
                                       
                                                $listado_errores=$listado_errores."<tr><td>No hay menú</td><td>".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]."</td><td>".$edad_alumno_anos." año".$plural_anios." y ".$edad_alumno_meses." mes".$plural_meses."</td><td>".date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))."</td><td>".$nombre_centro_alumno."</td><td>".$nombre_aula_alumno."</td><td>".$motivo_error."</td></tr>";
                                                $array_errores[$listado_alumnos["id_alumno"]]=1;
                                        }
                               }
                        }
                        
                        if ($id_menu_seleccionado!=0) {
                                $nombre_menu_seleccionado=db_query("SELECT * FROM menu_menus WHERE id_menu=$id_menu_seleccionado");
                                $datos_menu_seleccionado=db_fetch_array($nombre_menu_seleccionado);
                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;
                        
                                $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_seleccionado AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                        
                                if (db_num_rows($busca_comida_ese_dia)!=0) {
                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);
                                        
                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {

                                                $consulta_datos_plato=db_query("SELECT * FROM menu_platos WHERE id_plato=$codigo_plato") or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);
                                                        

                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        $motivo_error="El plato ".app_url_text_utf8($datos_plato["nombre_plato"])." servido el día ".date("d/m/Y",$fecha_time_stamp)." en el menú ".app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]).", continene un ingrediente (".app_url_text_utf8($ingredientes["nombre_alimento"]).") que todavía no se le ha introducido.";
                                                                                        $listado_errores=$listado_errores."<tr><td>Alimento NO introducido</td><td>".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]."</td><td>".$edad_alumno_anos." año".$plural_anios." y ".$edad_alumno_meses." mes".$plural_meses."</td><td>".date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))."</td><td>".$nombre_centro_alumno."</td><td>".$nombre_aula_alumno."</td><td>".$motivo_error."</td></tr>";
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;;
                                                                                        $motivo_error="El plato ".app_url_text_utf8($datos_plato["nombre_plato"])." servido el día ".date("d/m/Y",$fecha_time_stamp)." en el menú ".app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]).", continene un ingrediente (".app_url_text_utf8($ingredientes["nombre_alimento"]).") al que el niño le tiene alergia ($nombre_alergia).";
                                                                                        $listado_errores=$listado_errores."<tr><td>Alergia</td><td>".$listado_alumnos["Nombre"]." ".$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"]."</td><td>".$edad_alumno_anos." año".$plural_anios." y ".$edad_alumno_meses." mes".$plural_meses."</td><td>".date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))."</td><td>".$nombre_centro_alumno."</td><td>".$nombre_aula_alumno."</td><td>".$motivo_error."</td></tr>";
                                                                                        }
                                                                                }
                                                                }

                                                        if ($hay_comida==true) {
                                                                // Suma cantidades
                                                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                                while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                $array_alimentos_cantidades[$ingredientes["id_alimento"]]=$array_alimentos_cantidades[$ingredientes["id_alimento"]]+$ingredientes["cantidad_racion"];
                                                                $array_alimentos_cantidades_menu[$ingredientes["id_alimento"]][$id_menu_seleccionado]=$array_alimentos_cantidades_menu[$ingredientes["id_alimento"]][$id_menu_seleccionado]+$ingredientes["cantidad_racion"];
                                                                }
                                                                }
                                                        }

                                                }
                                                }
                                        
                                        }
                        
                                if ($hay_comida==true) {
                                        // Busca datos de aula y centro
                                        $totales_centro[$listado_alumnos["centro_alumno"]]++;
                                        $totales_menu[$id_menu_seleccionado][0]++;
                                        $totales_menu[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]++;
                                        }
                                }
                        }
                }

// Suma adicionales
$busca_menus_adicionales=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu");
while($datos_menu_seleccionado=db_fetch_array($busca_menus_adicionales)) {

$id_menu_actual=$datos_menu_seleccionado["id_menu"];
$cantidad_menu_actual=$_GET["extra_".$id_menu_actual];

if (is_numeric($cantidad_menu_actual)==false) {
        $cantidad_menu_actual=0;
        }

if ($cantidad_menu_actual!=0) {

                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_actual AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());

                                if (db_num_rows($busca_comida_ese_dia)!=0) {
                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {

                                                $consulta_datos_plato=db_query("SELECT * FROM menu_platos WHERE id_plato=$codigo_plato") or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);

                                                        if ($hay_comida==true) {
                                                                // Suma cantidades
                                                                $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                                while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                $array_alimentos_cantidades[$ingredientes["id_alimento"]]=$array_alimentos_cantidades[$ingredientes["id_alimento"]]+($ingredientes["cantidad_racion"]*$cantidad_menu_actual);
                                                                $array_alimentos_cantidades_menu[$ingredientes["id_alimento"]][$id_menu_actual]=$array_alimentos_cantidades_menu[$ingredientes["id_alimento"]][$id_menu_actual]+($ingredientes["cantidad_racion"]*$cantidad_menu_actual);
                                                                }
                                                                }
                                                        }

                                                }
                                                }

                                        }

                                if ($hay_comida==true) {
                                        $totales_menu[$id_menu_actual][0]=$totales_menu[$id_menu_actual][0]+$cantidad_menu_actual;
                                        $totales_extras[$id_menu_actual]=$totales_extras[$id_menu_actual]+$cantidad_menu_actual;
                                        }
                                }
}

}
// echo (date("d/m/Y",$fecha_time_stamp)."<br>");
if ((date("j",$fecha_time_stamp)==24) and (date("n",$fecha_time_stamp)==10)) {
        $fecha_time_stamp=$fecha_time_stamp+3600;
        }
$fecha_time_stamp=$fecha_time_stamp+86400;
}
$busca_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu");

        ?>
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <form name="listado_cantidades" method="GET" action="<?=$_SERVER['PHP_SELF'];?>">
        <input type="hidden" name="cd" value="<?=$seleccionados_calendario;?>">
        <input type="hidden" name="dia" value="<?=$_GET["dia"];?>">
        <input type="hidden" name="mes" value="<?=$_GET["mes"];?>">
        <input type="hidden" name="ano" value="<?=$_GET["ano"];?>">
        <input type="hidden" name="id_menu" value="<?=$_GET["id_menu"];?>">
        <input type="hidden" name="fin" value="<?=$_GET["fin"];?>">
        <?=$array_hiddens;?>
        <tr>
        <td bgcolor="#dedede">Menú</td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#EFEFEF"><?=$centros["nombre_centro"];?></td>
        <?php 
        }
        ?>
        <td bgcolor="#EFEFEF">Total</td>
        <td bgcolor="#EFEFEF" align="center">Extras<br><font size=1>Indicar en esta columna el nº de personas.<br>El resto de las columnas<br>indican el nº de menús servidos<br>(personas X días)</font></td>
        </tr>
        <?php 

while($menus=db_fetch_array($busca_menus)) {
        $id_menu_seleccionado=$menus["id_menu"];
        if ($id_menu_seleccionado==$id_menu) {
                $fondo_menu_sel="#c1d4e7";
                } else {
                $fondo_menu_sel="#ffffff";
                }
        ?>
        <tr>
        <td bgcolor="#EFEFEF"><?=app_url_text_utf8($menus["nombre_menu"]);?></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="<?=$fondo_menu_sel?>"><?=$totales_menu[$id_menu_seleccionado][$centros["id_centro"]];?></td>
        <?php 
        }
        $cantidad_menu_seleccionado=$_GET["extra_".$id_menu_seleccionado];

        if (is_numeric($cantidad_menu_seleccionado)==false) {
                $cantidad_menu_seleccionado=0;
                }
        ?>
        <td bgcolor="<?=$fondo_menu_sel?>"><?=$totales_menu[$id_menu_seleccionado][0];?></td>
        <td align="right" bgcolor="<?=$fondo_menu_sel?>" width=10%><?=$totales_extras[$id_menu_seleccionado];?> <input type="text" name="extra_<?=$id_menu_seleccionado?>" size=4 value="<?=$cantidad_menu_seleccionado;?>" style="font-size: 10px; text-align: right"></td>
        </tr>
        <?php 
        }
        ?>
        <tr>
        <td bgcolor="#EFEFEF">Totales</td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        $absoluto=0;
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td><?=$totales_centro[$centros["id_centro"]];?></td>
        <?php 
        $absoluto=$absoluto+$totales_centro[$centros["id_centro"]];
        }
        ?>
        <td><?=$absoluto?></td>
        <td align="center"><input type="button" value="Recalcular" style="font-size: 10px" onclick="document.listado_cantidades.submit();"></td>
        </tr>
        </form>
</table>
<?php 
if (strlen($listado_errores)!=0) {
?>
<br>
<table border=1 width=100% style="border-collapse: collapse" bordercolor="#000000">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=7><b>Errores detectados.</b></td>
  </tr>
<tr bgcolor="#efefef">
<td><b>Incidencia</b></td>
<td><b>Alumno</b></td>
<td><b>Edad</b></td>
<td><b>Fecha nacimiento</b></td>
<td><b>Escuela</b></td>
<td><b>Aula</b></td>
<td><b>Razón</b></td>
</tr>
<?=$listado_errores?>
</table>
<?php 
}
?>
<br>
<table border=1 width=100% style="border-collapse: collapse" bordercolor="#000000">
<form name="lista_compra" action="generar_lista.php" method="POST">
<input type="hidden" name="fecha_inicio" value="<?=$fecha_inicio_periodo;?>">
<input type="hidden" name="fecha_fin" value="<?=$fecha_fin_periodo;?>">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b>Lista de ingredientes y cantidades necesarias.</b></font></td>
    <?php 
    $busca_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu");
    while($menus=db_fetch_array($busca_menus)) {
        $id_menu_seleccionado=$menus["id_menu"]
        ?>
        <td align="center"><font size=2><b><?=app_url_text_utf8($menus["nombre_menu"]);?></b></font></td>
        <?php 
        }
    ?>
    <td align="center"><font size=2><b>Total</b></font></td>
  </tr>
<?php 
$unidad_medida[0]="g.";
$unidad_medida[1]="ml.";
$busca_alimentos=db_query("SELECT * FROM menu_alimentos ORDER BY nombre_alimento");
while($listado_alimentos=db_fetch_array($busca_alimentos)) {
if (doubleval($array_alimentos_cantidades[$listado_alimentos["id_alimento"]])>0) {
?>
<tr>
        <input type="hidden" name="id_alimento[]" value="<?=$listado_alimentos["id_alimento"];?>">
        <input type="hidden" name="cantidad_alimento[]" value="<?=number_format($array_alimentos_cantidades[$listado_alimentos["id_alimento"]],2,".","");?>">
        <td bgcolor="#efefef"><font size=2><?=app_url_text_utf8($listado_alimentos["nombre_alimento"]);?></font></td>
    <?php 
    $busca_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu");
    while($menus=db_fetch_array($busca_menus)) {
        $id_menu_seleccionado=$menus["id_menu"];
        if ($id_menu_seleccionado==$id_menu){
                $fondo_menu_sel="#c1d4e7";
                } else {
                $fondo_menu_sel="#ffffff";
                }
        ?>
        <td align="right" bgcolor="<?=$fondo_menu_sel;?>"><font size=1><?=number_format($array_alimentos_cantidades_menu[$listado_alimentos["id_alimento"]][$id_menu_seleccionado],2,",",".");?> <?=$unidad_medida[$listado_alimentos["unidad_medida"]]?></font></td>
        <?php 
        }
    ?>
        <td align="right"><font size=1><?=number_format($array_alimentos_cantidades[$listado_alimentos["id_alimento"]],2,",",".");?> <?=$unidad_medida[$listado_alimentos["unidad_medida"]]?></font></td>
</tr>
<?php 
        }
}
?>
</table>
<input type="submit" value="Generar lista de la compra basada en estas cantidades" style="font-size: 10px">
</form>
<script language="Javascript">
function imprimir() {
        document.getElementById('botonera').style.display='none';
        window.print();
        document.getElementById('botonera').style.display='block';
        }
</script>
</center></div>
</div>
</body>
</html>
