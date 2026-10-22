<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica.php");
require("./funciones_alergias.php");
header('Content-Type: text/html; charset=UTF-8');
function menu_print_text($value)
{
    $text = app_url_text_utf8((string) $value);
    if (!preg_match('//u', $text)) {
        $text = iconv('Windows-1252', 'UTF-8', $text);
    }
    return app_escape($text);
}
// The shared allergy labels still use the legacy encoding.
foreach ($nombres_alergias as $allergen_index => $allergen_name) {
    if (!preg_match('//u', $allergen_name)) {
        $nombres_alergias[$allergen_index] = iconv('Windows-1252', 'UTF-8', $allergen_name);
    }
}
foreach ($dia_sem_largo as $weekday_index => $weekday_name) {
    if (!preg_match('//u', $weekday_name)) {
        $dia_sem_largo[$weekday_index] = iconv('Windows-1252', 'UTF-8', $weekday_name);
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title>Calendario comedor</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/menu/assets/menu-document.css?v=20261005.1">
</head>
<body class="menu-print">
<div class="menu-print-toolbar"><button type="button" onclick="window.print()">Imprimir men&uacute;</button><p>Impresi&oacute;n preparada para papel A4 horizontal.</p></div>
<div class="menu-document-scroll" role="region" aria-label="Calendario del comedor" tabindex="0">
<?php 

$id_menu=(int) app_request_value($_GET, "id_menu", 0);

        //  Busca nombre menú
        if ($id_menu!=0) {
                $busca_nombre_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=$id_menu");
                if (db_num_rows($busca_nombre_menu)!=0) {
                        $datos_nombre_menu=db_fetch_array($busca_nombre_menu);
                        $nombre_menu_seleccionado=$datos_nombre_menu["nombre_menu"];
                        if (strlen($nombre_menu_seleccionado)!=0) {
                                $nombre_menu_seleccionado=app_url_text_utf8($nombre_menu_seleccionado);
                                }
                        } else {
                        $nombre_menu_seleccionado="";
                        }
                } else {
                $nombre_menu_seleccionado="";
                }

// Busca el curso actual
$curso_seleccionado=$datos_alumno["id_curso"];

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."' LIMIT 0,1");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado LIMIT 0,1");
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

$inicio=$datos_curso_actual["fecha_inicio"];

if (strtotime($inicio)<strtotime(date("Y-m-d"))) {
        $inicio=date("Y-m-d");
        }

if (strtotime($inicio)>strtotime($datos_curso_actual["fecha_fin"])) {
        $inicio=$datos_curso_actual["fecha_fin"];
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






?>
<table class="menu-document" border="0" cellpadding="0" cellspacing="0">
        <tr><td><header class="menu-document-heading"><strong>Calendario de comedor</strong><span>L&aacute;pices &middot; Neokids Natural Food</span></header></td></tr>
        <tr><td>
                <table class="menu-document-frame" border="0">
                <tr>
                <td bgcolor="#ffffff" align="center">
                <?php 
                $anio_actual=date("Y",$fecha_time_stamp);
                $dias_trabajados=0;
                $mes_s=1;
                $mesnombre=$nombre_mes[$mes];
                $dias_trabajados_mes=0;
                ?>
                <font color="#009590" size=3 face="<?=$fuente_general?>"><b><?=menu_print_text($nombre_menu_seleccionado)?><br><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
                        <table class="menu-calendar" cellspacing="0" cellpadding="0">
                        <thead><tr bgcolor="#8F8F8F">
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
</tr></thead><tbody>
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
        $busca_dia_en_calendario=db_query("SELECT * FROM calendario_laboral WHERE id_curso=$id_curso_actual AND id_centro=$centro_actual AND dia='".date("Y-m-d",$inicio_time_stamp)."'");
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

        // Comprueba si tiene ya los platos asignados

        // $busca_platos_dia=mysql_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_time_stamp)."'");

        ?>
        <td align="center" valign="top" bgcolor="<?=$fondo_celda_dia;?>" onclick="info_dia(<?=$inicio_time_stamp;?>,<?=$id_curso_actual;?>,<?=$id_menu;?>);">
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

$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_time_stamp)."'");
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
                $cena=$datos_platos_dia["cena"];

        $color_alerta="#FF0000";

        echo "<table width=100% border=0 cellpadding=0 cellspacing=1>";
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Comida 1:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida2 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Comida 2:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida3 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Comida 3:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida4 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Comida 4:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida5 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Comida 5:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$postre1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);
                
                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";                
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Postre:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$merienda1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Merienda 1:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$merienda2 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Merienda 2:</b> ".menu_print_text($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        if (strlen($cena)!=0) {
                echo "<tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'><b>Cena: </b> ".$cena."</font></td></tr>";
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
                </tbody></table>
                <div class="menu-allergens">
                <h3>Al&eacute;rgenos:</h3> 
                <?php
                foreach ($arrayKeysAlergias as $keyAlg => $numAlg) {
                        echo("<span><b>".$numAlg.".</b> ".app_escape(ucfirst($nombres_alergias[$numAlg-1]))."</span> ");
                        if (($numAlg==4) or ($numAlg==7) or ($numAlg==12)) {
                                echo("<br />");
                        }
                }
                ?>
                </div>
        </td>
        </tr>
        </table>
        </td>
        </tr>

</table>
</div>
</body>
</html>
