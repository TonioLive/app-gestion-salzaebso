<?php  require("../configuracion/parte_superior_privado.php"); ?>
<div align="center">
<br>
<?php 

$nombre_archivo_actual="calendario_pedidos_empresa.php";

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);
?>
<?php 
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
  $fecha_time_stamp=strtotime($inicio);
}

$fecha_time_stamp=$fecha_time_stamp;

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
<font color="#000000" size=3 face="<?=$fuente_general?>"><b><?=$nombre_aula;?><br>
<?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?>
</b></font>
<table border=0 cellspacing="1" cellpadding="1">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
<?php 
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
</font>
<td valign="top">
<font face="<?=$fuente_general?>" color="#ffffff" size=3>
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
?>
<td align="right" bgcolor="#ffffff">
<font face="<?=$fuente_general?>" color="#000000" size=3>
<?php 
    if ($dias==7)
    {
    $fondo_celda_dia="#D67F87";
    }
    if (date("m/d/Y",$inicio_time_stamp)==date("m/d/Y"))
    {
    $fondo_celda_dia="#4366E9";
    }
    
// Comprueba si hay pedidos con fecha de entrega en este día

$busca_pedido=db_query("SELECT * FROM pedidos_material_empresa WHERE fecha_pedido='".date("Y-m-d",$inicio_time_stamp)."'") or die(db_error());
$total_pedidos=db_num_rows($busca_pedido);
if (db_num_rows($busca_pedido)!=0) {
        $texto_adicional=$total_pedidos." pedido";
        if ($total_pedidos>1) {
                $texto_adicional=$texto_adicional."s";
                }
        } else {
        $texto_adicional="";
        }
?>
</font><div align="center"><input type="button" onclick="window.location.href='ver_pedidos_empresa_dia.php?dia_sel=<?= $inicio_time_stamp; ?>';" value="<?=date("j",$inicio_time_stamp)."\n".$texto_adicional;?>" style="font-size: 14px" style="height: 80; width: 90; border-style: solid; border-width: 1px; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; text-align=center; background-color: <?=$fondo_celda_dia?>; font-weight: bold; background-image: url('/imagenes/n.jpg')"></div>
</td>
<?php 
  $inicio_time_stamp=$inicio_time_stamp+86400;
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
<br><font size=2>
<input type="button" onclick="window.location.href='<?=$nombre_archivo_actual?>?dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1';" value="< Mes anterior" style="font-size: 14px" style="height: 35; width: 120; border-style: solid; border-width: 1px; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; text-align=center; background-color: #cdcdcd; font-weight: bold; background-image: url('/imagenes/n.jpg')"> <input type="button" onclick="window.location.href='/index.php';" value="SALIR" style="font-size: 14px" style="height: 35; width: 120; border-style: solid; border-width: 1px; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; text-align=center; background-color: #cdcdcd; font-weight: bold; background-image: url('/imagenes/n.jpg')"> <input type="button" onclick="window.location.href='<?=$nombre_archivo_actual?>?dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1';" value="Mes siguiente >" style="font-size: 14px" style="height: 35; width: 120; border-style: solid; border-width: 1px; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; text-align=center; background-color: #cdcdcd; font-weight: bold; background-image: url('/imagenes/n.jpg')">
</font>
<br>
<br>
<font color="#000000" size=2 face="<?=$fuente_general?>">
(*) Selecciona en el día para ver los pedidos<br>
</font>
<?php 
        db_close();
?>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>
