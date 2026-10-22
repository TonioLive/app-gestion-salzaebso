<?php  require("../configuracion/parte_superior_privado_ancho.php"); ?>
<div align="center">
<?php 

$id_menu=$_GET["id_menu"];

$consulta_menu = db_query_params('SELECT * FROM menu_menus WHERE id_menu=?', 's', array($id_menu)) or die (db_error());
$datos_menu = db_fetch_array($consulta_menu);


$edad_ini_actual=$datos_menu["edad_ini_menu"];
if ($edad_ini_actual<2) {
        $edad_ini_actual=round(($edad_ini_actual*12),0)." meses";
        } else {
        $edad_ini_actual=$edad_ini_actual." años";
        }
$edad_fin_actual=$datos_menu["edad_fin_menu"];
if ($edad_fin_actual<2) {
        $edad_fin_actual=round(($edad_fin_actual*12),0)." meses";
        } else {
        if ($edad_fin_actual==100) {
                                $edad_fin_actual=">18";
                                }
        $edad_fin_actual=$edad_fin_actual." años";
        }
$edades="De ".$edad_ini_actual." a ".$edad_fin_actual;

$nombre_menu=$datos_menu["nombre_menu"];
if (strlen($nombre_menu)!=0) {
        $nombre_menu=urldecode($nombre_menu);
        }
$id_centro_menu=$datos_menu["id_centro"];

// Busca el curso actual
$curso_seleccionado=$_GET["curso_seleccionado"];
if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)==0) {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));
                }

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }
        
if ($id_curso_actual!=0) {

$inicio=$datos_curso_actual["fecha_inicio"];


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
?><input type="button" value="Salir" onclick="window.location.href='gestion_menu.php'" style="font-size: 10px"><br><br>
<font size=3><b>Calendario del <?=$nombre_menu;?> para el curso <?=$datos_curso_actual["nombre"]?></b><br><?=$edades?><br></font>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <input type="hidden" name="id_menu" value="<?=$id_menu;?>">
                        <font size=2><b>Cambiar al curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
                        <font size=2>ATENCION: Los días no lectivos son los que aparecen marcados en le calendario.</font><br><br>
<?php 
$anio_actual=date("Y",$fecha_time_stamp);
$dias_trabajados=0;
for ($mes_s=1; $mes_s<=12; $mes_s=$mes_s+1) {
$mesnombre=$nombre_mes[$mes];
$inicio_mes_actual=strtotime($anio_actual."-".$mes."-1");
$dias_trabajados_mes=0;
?>
<font color="#000000" size=1 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b> <input type="button" onclick="window.location.href='calendario_menu_ampliado.php?id_menu=<?=$id_menu?>&mes_seleccionado=<?=$inicio_time_stamp+1296000;?>&fecha=<?=date("d/m/Y",$inicio_time_stamp+1296000)?>'" value="Ampliar mes" style="font-size: 10px"></font>
<table border=0 cellspacing="1" cellpadding="1" width="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<td align="center">&nbsp;</td>
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center">
<font face="<?=$fuente_general?>" color="#ffffff" size=1>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
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
<td width=15% align="center"><input type="button" style="font-size: 10px" value=" + " onclick="window.open('/cocina/prevision_compras.php?id_menu=<?=$id_menu?>&dia=<?=$inicio_time_stamp;?>','listado','toolbar=no,resizable=yes,scrollbars=yes')"> <input type="button" style="font-size: 10px" value="Copiar semana..." onclick="window.open('calendario_copiar_semana.php?id_menu=<?=$id_menu?>&inicio_semana=<?=date("Y/n/j",$inicio_time_stamp);?>&dia=<?=date("Y/n/j",$inicio_time_stamp);?>','semana','toolbar=no,resizable=yes,scrollbars=yes,width=330,height=216')"> <input type="button" style="font-size: 10px" value="Copiar mes..." onclick="window.open('calendario_copiar_mes.php?id_menu=<?=$id_menu?>&inicio_mes=<?=date("Y/n/j",$inicio_time_stamp);?>&dia=<?=date("Y/n/j",$inicio_mes_actual);?>','semana','toolbar=no,resizable=yes,scrollbars=yes,width=330,height=216')"></td>
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
                        $fondo_celda_dia="#F17327";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                } else {
                // Si es domingo lo marca como festivo
                if (($dias==7) or ($dias==6)) {
                        $inserta_domingo=db_query_params('INSERT INTO calendario_laboral (id_curso,id_centro,dia,estado) VALUES (?,?,?,1);', 'sss', array((string)($id_curso_actual), (string)($centro_actual), (string)(date("Y-m-d", $inicio_time_stamp))));
                        $estado_boton=" checked";
                        $fondo_celda_dia="#F17327";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                }
                
        // Comprueba si tiene ya los platos asignados
        
        $busca_platos_dia=db_query_params('SELECT * FROM menu_calendario WHERE id_menu=? AND fecha=?', 'ss', array($id_menu, (string)(date("Y-m-d", $inicio_time_stamp))));
        if (db_num_rows($busca_platos_dia)!=0) {
                if ($fondo_celda_dia=="#F17327") {
                        $fondo_celda_dia="#E7FF30";
                        } else {
                        $fondo_celda_dia="#5DDD6C";
                        }
                } else {
                // Otra cosa
                }
        ?>
        <td align="center" bgcolor="<?=$fondo_celda_dia;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=1>
        <?php 
        print "<a href='javascript:cambia_dia($inicio_time_stamp,$id_curso_actual,$id_menu)' title='Seleccionar día'>";
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
                print "</a>";
        ?>
        <br><input type="button" style="font-size: 10px" value="Editar día" name=""<?=$estado_boton?> onclick="cambia_dia(<?=$inicio_time_stamp;?>,<?=$id_curso_actual;?>,<?=$id_menu;?>)">
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
?>
<tr>
<td>
</td>
</tr>
</table>
<font size=1><b>Dias lectivos durante el mes:</b> <?=$dias_trabajados_mes?><br><br></font>
<?php 
}
?>
<br><font size=1><b>Dias lectivos durante el curso:</b> <?=$dias_trabajados?><br><br></font>
</div>
<script language="Javascript">
function cambia_dia(dia,curso,id_menu) {
        window.open("editar_menu_dia.php?dia="+dia+"&curso="+curso+"&id_menu="+id_menu,"cambia_dia","toolbar=no,width=700,height=500");
        }
</script>
<?php 
}
require("../configuracion/parte_inferior.php"); ?>
