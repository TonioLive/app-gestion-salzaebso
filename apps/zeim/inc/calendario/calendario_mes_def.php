<div align="center"><?php 
// A continuación detecta en que dia cae el primer lunes de esta mes
// Inicio=fecha del lunes
// mesini=Nombre del mes en el que empieza la semana
// mesfin=Nombre del mes en el que acaba la semana
// Si hoy es domingo pasa automaticamente a la semana siguiente

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{

  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$inicio;
} 

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];


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

//Inicio es el día en el que empieza la primera semana del mes
$inicio_time_stamp=$inicio_mes_time_stamp-($diasemana_inicio_mes*86400)+86400;

?>
<small><small><small><br>
<font color="#000000"><b>
<?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?>
</b></font>
</small></small></small>
<table border=0 cellspacing="1" cellpadding="1" width=250>
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font face="Verdana" color="#ffffff">
<small><small>
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
</small></small>
</font>
<td valign="top">
<font face="Verdana" color="#ffffff">
<small><small>
<b>
<?php    echo $dia_sem[$dia_s]; ?>
</b>
</small></small>
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
  
  if ($mes==date("n",$inicio_time_stamp))
    {
    $fondo_celda_dia="#EFEFEF";
    } else {
    $fondo_celda_dia="#AAAAAA";
    }
  if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    $fondo_celda_dia="#DD9999";
    }
    
    
// Comprueba si está marcado
if (strlen($centro_actual)!=0) {
        $busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND id_centro=? AND dia=?', 'sss', array($id_curso_actual, $centro_actual, (string)(date("Y-m-d", $inicio_time_stamp))));
        } else {
        $busca_dia_en_calendario=db_query_params('SELECT * FROM calendario_laboral WHERE id_curso=? AND dia=?', 'ss', array($id_curso_actual, (string)(date("Y-m-d", $inicio_time_stamp))));
        }
if (db_num_rows($busca_dia_en_calendario)!=0) {
        $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        $fondo_celda_dia="#F17327";
                        }
        } else {
                // Si es domingo lo marca como festivo
                if (($dias==7) or ($dias==6)) {
                        $fondo_celda_dia="#F17327";
                        }
        }
  
?>
<td align="right" bgcolor="<?=$fondo_celda_dia;?>">
<font face="Verdana" color="#000000">
<small><small>
<a href="<?=$SERVER["PHP_SELF"];?>?dia=<?= $inicio_time_stamp; ?>">
<?php 
    if ($mes==date("n",$inicio_time_stamp))
    {
      print "<b>";    } 
    if ($dias==7)
    {
      print "<font color=#800000>";    } 
    if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y",$fecha_time_stamp))
    {
    print "<font color='blue'>";    }
    print (date("j",$inicio_time_stamp));
    if ($mes==date("n",$inicio_time_stamp))
    {
    print "</b>";
    }
    print "</a>";
    $inicio_time_stamp=$inicio_time_stamp+86400;
?>
</small></small>
</font>
</td>
<?php  
  }
  if ($mes!=date("n",$inicio_time_stamp)) {
                                           break;
                                          }
?>
</tr>
<?php  
}
?>
<tr>
<td>
</td>
</tr>
</table>
<small><small><small>
<font color="#000000">
(*) Haz clic en el día para ver las actividades.<br></font>
<a href="<?=$SERVER["PHP_SELF"];?>?dia=<?=strtotime($ano_anterior."-".$mes_anterior."-1")?>"><< Mes anterior</a> | <a href="<?=$SERVER["PHP_SELF"];?>?dia=<?=time()?>">Ver hoy</a> | <a href="<?=$SERVER["PHP_SELF"];?>?dia=<?=strtotime($ano_siguiente."-".$mes_siguiente."-1")?>">Mes siguiente >></a>
<br><b>Leyenda de colores</b>
<table align=center>
<tr>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#DD9999"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Hoy</font></td>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#F17327"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Día festivo</font></td>
</tr>
<tr>
<td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="#EFEFEF"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td><font size=1>Día lectivo</font></td>
<td></td>
</tr>
</table>
</font>
</small></small></small></div>
