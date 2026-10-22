<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title>Calendario comedor</title>
<?php  require("../configuracion/estilos/estilo2.php"); ?>
<script type="text/javascript">
<!--
self.print();
//-->
</script>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<center>
<div align="center">
<?php 
$id_alumno=$_GET["id_hijo"];
if (strlen($id_alumno)==0) {
        $id_alumno=$_POST["id_hijo"];
        }


$busca_datos_alumno=db_query_params('SELECT usuarios.*,alumnos.*,alumnos.id_centro AS centro_asistencia FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=? LIMIT 0,1', 's', array($id_alumno)) or die(db_error());

if (db_num_rows($busca_datos_alumno)!=0) {

$datos_alumno=db_fetch_array($busca_datos_alumno);

$centro_actual=$datos_alumno["centro_asistencia"];
$id_usuario=$datos_alumno["id_usuario"];

if ($datos_alumno["sexo"]=="V") {
                        $sufijo_hijo="o";
                        } else {
                        $sufijo_hijo="a";
                        }

$nombre_de_pila_alumno=$datos_alumno["Nombre"];
$nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];
$comedor_alumno=$datos_alumno["comedor"];

// Busca el curso actual
$curso_seleccionado=$datos_alumno["id_curso"];

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }
$dia_sel_inicio=$_GET["dia"];
if (strlen($dia_sel_inicio)!=0) {
        $inicio=$dia_sel_inicio;
        }


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

$mes_fin_curso=date("n",strtotime($datos_curso_actual["fecha_fin"]));
$anio_fin_curso=date("Y",strtotime($datos_curso_actual["fecha_fin"]));
$mes_ini_curso=date("n",strtotime($datos_curso_actual["fecha_inicio"]));
$anio_ini_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));

// Empezamos
$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");






$alimentos_ya_marcados=array();
$alergias_ya_marcadas=array();

$platos_no_introducidos=array();
$platos_alaergias=array();
$hay_observaciones=false;
?>
<table width=690 border=0 cellpadding=0 cellspacing=0 align="center">
<tr><td><img src="/webcam/imagenes/encabezado_menu_2024.gif" border=0></tr></td>
<tr><td><table width=690 border=2 bordercolor="#606060">
<tr>
<td bgcolor="#ffffff" align="center">
<font size=3 color="#009590"><b>Alumn<?=$sufijo_hijo?> <?=$nombre_completo_alumno?> durante el curso <?=$datos_curso_actual["nombre"]?></b><br></font>
<font size=2 color="#009590">ATENCION: Los días no lectivos son los que aparecen marcados en el calendario de color naranja.<br><br></font>
<?php 
$anio_actual=date("Y",$fecha_time_stamp);
$dias_trabajados=0;
$mes_s=1;
$mesnombre=$nombre_mes[$mes];
$dias_trabajados_mes=0;
?>
<font color="#009590" size=3 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
<table border=0 cellspacing="1" cellpadding="1" width="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<?php 
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=2>
<b>
<?php    echo $dia_sem_largo[$dia_s]; ?>
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

        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=?', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", $inicio_time_stamp))));
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        $estado_boton=" checked";
                        $fondo_celda_dia="#feb617";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                } else {
                // Si es domingo lo marca como festivo
                if (($dias==7) or ($dias==6)) {
                        $estado_boton=" checked";
                        $fondo_celda_dia="#feb617";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                }

        // Calcula menu apropiado para el niño

                        // Edad actual del niño en años con decimales
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
                $menu_actual_en_ficha=$datos_alumno["menu_actual"];
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
                        $regimen=$datos_alumno["comedor"];
                        if ($regimen==3) {
                                $filtro_regimen=" AND regimen=1";
                                } else {
                                $filtro_regimen=" AND regimen=0";
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
                                if (($id_menu_seleccionado==0) and ($array_errores[$datos_alumno["id_alumno"]]!=1)) {
                                        if ($edad_alumno<$minimo_minimorum) {
                                                $motivo_error="La edad del niño no llega a la edad mínima (".round(($minimo_minimorum*12),0)." meses) admitida en los menús.<small><br><b>Propuestas:</b><br>1. Introducir un menú apropiado para la edad del niño.<br>2. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
                                                } else {
                                                $motivo_error="El cía ".date("d/m/Y",$fecha_time_stamp)." no se encuentra un menú apropiado para la edad del niño.<small><br><b>Propuestas:</b><br>1. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
                                                }

                                                $listado_errores=$listado_errores."<tr><td>No hay menú</td><td>".$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"]."</td><td>".$edad_alumno_anos." año".$plural_anios." y ".$edad_alumno_meses." mes".$plural_meses."</td><td>".date("d/m/Y",strtotime($datos_alumno["fecha_nacimiento"]))."</td><td>".$nombre_centro_alumno."</td><td>".$nombre_aula_alumno."</td><td>".$motivo_error."</td></tr>";
                                                $array_errores[$datos_alumno["id_alumno"]]=1;
                                        }
                               }
                        }
        $id_menu=$id_menu_seleccionado;
        //  Busca nombre menú
        if ($id_menu!=0) {
                $busca_nombre_menu=db_query_params('SELECT * FROM menu_menus WHERE id_menu=?', 's', array($id_menu));
                if (db_num_rows($busca_nombre_menu)!=0) {
                        $datos_nombre_menu=db_fetch_array($busca_nombre_menu);
                        $nombre_menu_seleccionado=$datos_nombre_menu["nombre_menu"];
                        if (strlen($nombre_menu_seleccionado)!=0) {
                                $nombre_menu_seleccionado=urldecode($nombre_menu_seleccionado);
                                }
                        } else {
                        $nombre_menu_seleccionado="";
                        }
                } else {
                $nombre_menu_seleccionado="";
                }
        // echo date("d/m/Y",$inicio_time_stamp)." ".$nombre_menu_seleccionado;
        // Comprueba si tiene ya los platos asignados

        // $busca_platos_dia=mysql_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_time_stamp)."'");

        ?>
        <td align="center" valign="top" bgcolor="<?=$fondo_celda_dia;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=2>
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

$busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $inicio_time_stamp))));
if (db_num_rows($busca_platos_dia)!=0) {
                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_platos_dia[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {

                                                $consulta_datos_plato=db_query_params('SELECT * FROM menu_platos WHERE id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query_params('SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=?', 's', array($codigo_plato)) or die(db_error());

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($datos_alumno[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        if ($alimentos_ya_marcados[$codigo_plato]!=1) {
                                                                                                $platos_no_introducidos[$c]=$platos_no_introducidos[$c].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
                                                                                                $alimentos_ya_marcados[$codigo_plato]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($datos_alumno[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        if ($alergias_ya_marcadas[$codigo_plato]!=1) {
                                                                                                $platos_alaergias[$c]=$platos_alaergias[$c].$datos_plato["nombre_plato"]." (Ingrediente: ".urldecode($ingredientes["nombre_alimento"])."),";
                                                                                                $alergias_ya_marcadas[$codigo_plato]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }
                                                                }

                                                        }

                                                }
                                                }

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

        $color_alerta="#FF0000";

        echo "<table width=100% border=0 cellpadding=0 cellspacing=1>";
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida1]==1) or ($alimentos_ya_marcados[$comida1]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 1:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida2]==1) or ($alimentos_ya_marcados[$comida2]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 2:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida3));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida3]==1) or ($alimentos_ya_marcados[$comida3]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 3:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida4));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida4]==1) or ($alimentos_ya_marcados[$comida4]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 4:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($comida5));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$comida5]==1) or ($alimentos_ya_marcados[$comida5]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 5:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($postre1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$postre1]==1) or ($alimentos_ya_marcados[$postre1]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Postre:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda1));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$merienda1]==1) or ($alimentos_ya_marcados[$merienda1]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Merienda 1:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        $busca_comida=db_query_params('SELECT * FROM menu_platos WHERE id_plato=? ORDER BY nombre_plato', 's', array($merienda2));
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$merienda2]==1) or ($alimentos_ya_marcados[$merienda2]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Merienda 2:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".urldecode($datos_comida["nombre_plato"])."</font></td></tr>";
                }
        echo "</table>";
        }
        ?>
        </font>
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

$anio_actual_1=date("Y",$inicio_time_stamp);
$mes_actual_1=date("n",$inicio_time_stamp);
?>
</table>
<?php 
if ($hay_observaciones==true) {
?><table border=1 cellpadding=2 cellspacing=0 bordercolor="#606060" style="border-collapse: collapse">
<tr valign="top">
        <td colspan=2 bgcolor="#efefef"><font size=2><b>Observaciones:</b></td>
</tr>
<?php 
$encabezado=false;
for ($c=0;$c<count($array_alimentos);$c++) {
        if (strlen($platos_no_introducidos[$c])!=0) {
                $platos_no_introducidos[$c]=urldecode(substr($platos_no_introducidos[$c],0,strlen($platos_no_introducidos[$c])-1));
                if ($encabezado==false) {
                        ?>
                <tr valign="top">
                        <td colspan=2 bgcolor="#efefef"><font size=2><b>Alimentos todavía no introducidos.</b><br>
                        Debido a la presencia de algún ingrediente no introducido todavía en la alimentación de <?=ucwords(strtolower($nombre_de_pila_alumno));?>, los siguientes platos (marcados en rojo en el calendario) serán sustituidos por otro alternativo que sea adecuado para su dieta.</font></td>
                </tr>
                        <?php 
                        $encabezado=true;
                        }

                ?>
                <tr valign="top">
                        <td><font size=2><b><?=$nombres_alimentos[$c];?></b></font></td>
                        <td><font size=2><?=$platos_no_introducidos[$c];?></font></td>
                </tr>
                <?php 
                }
        }

$encabezado=false;
for ($c=0;$c<count($array_alergias);$c++) {
        if (strlen($platos_alaergias[$c])!=0) {
                $platos_alaergias[$c]=substr($platos_alaergias[$c],0,strlen($platos_alaergias[$c])-1);
                if ($encabezado==false) {
                        ?>
                <tr valign="top">
                        <td colspan=2 bgcolor="#efefef"><font size=2><b>Alergias</b><br>
                        Debido a la presencia de algún ingrediente al que <?=ucwords(strtolower($nombre_de_pila_alumno));?> tiene alergia, los siguientes platos (marcados en rojo en el calendario) serán sustituidos por otro alternativo que sea adecuado para su dieta.</font></td>
                </tr>
                        <?php 
                        $encabezado=true;
                        }

                ?>
                <tr valign="top">
                        <td><font size=2><b><?=$nombres_alergias[$c];?></b></font></td>
                        <td><font size=2><?=$platos_alaergias[$c];?></font></td>
                </tr>
                <?php 
                }
        }
?>
</table>
<?php 
}
?></td>
</tr>
</table></td></tr>
        <tr><td><img src="/webcam/imagenes/pie_pagina_menu.gif" border=0></td></tr>
        </table>
<?php 
} else {
?>
El Alumno no Existe...
<br><br>
<input type="button" onclick="history.go(-1);" value="Volver">
<?php 
}
?>
</center>
</div>
</body>
</html>
