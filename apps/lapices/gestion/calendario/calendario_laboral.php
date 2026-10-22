<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<SCRIPT language="JavaScript">
<!--hide

function ampliar_dia(dia)
{
window.open('/actividades.php?dia='+dia,'ampliar_dia','toolbar=no,resizable=yes,width=550,height=550')
}
//-->
</SCRIPT>
<div align="center">
<?php 

$nombres_escuelas=array();
$nombres_escuelas[0]="Actividades comunes";

$busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
while($lista_centros=db_fetch_array($busca_centros)){
        $nombres_escuelas[$lista_centros["id_centro"]]=$lista_centros["nombre_centro"];
        }
                
$busca_aulas=db_query("SELECT * FROM centro_aulas ORDER BY id_aula");
$nombres_aulas=array();
$nombres_aulas[0]="Actividades comunes";
while($lista_aulas=db_fetch_array($busca_aulas)){
        $nombres_aulas[$lista_aulas["id_aula"]]="Aula ".$lista_aulas["nombre"];
        }

// Busca el curso actual
$curso_seleccionado=$_GET["curso_seleccionado"];
if (strlen($curso_seleccionado)==0) {

        // $busca_curso_actual=mysql_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE (fecha_inicio<='".date("Y-m-d")."' OR (month(fecha_inicio)=".date('n')." AND year(fecha_inicio)=".date('Y').")) AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
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
?>
<font size=3><b>Calendario laboral del curso <?=$datos_curso_actual["nombre"]?></b><br></font>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
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
$dias_trabajados_mes=0;
?>
<font color="#000000" size=1 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
<table border=0 cellspacing="1" cellpadding="1" width="98%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<?php  
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
<td valign="top" align="center" width="14%">
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
                        $inserta_domingo=db_query("INSERT INTO calendario_laboral (id_curso,id_centro,dia,estado) VALUES ('$id_curso_actual','$centro_actual','".date("Y-m-d",$inicio_time_stamp)."',1);");
                        $estado_boton=" checked";
                        $fondo_celda_dia="#F17327";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                }
        ?>
        <td align="center" bgcolor="<?=$fondo_celda_dia;?>" valign="top">
        <font face="<?=$fuente_general?>" size=2 color="#000000">
        <a href="javascript:ampliar_dia(<?=$inicio_time_stamp?>)" title="Editar actividades día">
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
                print "</a>";
        ?>
        <br><input type="checkbox" value="" name=""<?=$estado_boton?> onclick="cambia_dia(<?=$inicio_time_stamp;?>,<?=$id_curso_actual;?>,<?=$centro_actual;?>)"><br><?php 
        
        // Busca actividades
        
        $rsNot_query=db_query("SELECT * FROM Agenda WHERE Fecha='".date("Y-m-d",$inicio_time_stamp)."' ORDER BY id_centro;") or die(db_error());
        
        if (db_num_rows($rsNot_query)!=0) {
        ?>
        <table border="1" width="98%" cellspacing="0">
        <?php 
        $escuela_actual=-1;
        $aula_actual=-1;
        while($rsNot=db_fetch_array($rsNot_query)) {
        if ($escuela_actual!=$rsNot["id_centro"]) {
                $escuela_actual=$rsNot["id_centro"];
                ?>
                <tr valign=top>
                        <td width="100%" bgcolor="#ffd0a6" colspan=2><font face="Arial" color="#800000"><small><small><b><?= $nombres_escuelas[$rsNot["id_centro"]]; ?></b></small></small></font></td>
                </tr>
                <?php 
                }
        if ($aula_actual!=$rsNot["id_aula"]) {
                $aula_actual=$rsNot["id_aula"];
                ?>
                <tr valign=top>
                        <td width="100%" bgcolor="#fff8c6" colspan=2><font face="Arial" color="#800000"><small><small><b><?= $nombres_aulas[$rsNot["id_aula"]]; ?></b></small></small></font></td>
                </tr>
                <?php 
                }
        ?>
        <tr valign=top>
                <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="#800000"><small><small><b><?= date("H:i",strtotime($rsNot["Hora"])); ?></b></small></small></font></td>
                <td width="83%" bgcolor="#fff8c6"><small><small><b><?= $rsNot["Titulo"]; ?></b></small></small></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        <?php 
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
?>
<tr>
<td>
</td>
</tr>
</table>
<font size=1><b>Dias trabajados durante el mes:</b> <?=$dias_trabajados_mes?><br><br></font>
<?php 
}
?>
<br><font size=1><b>Dias trabajados durante el curso:</b> <?=$dias_trabajados?><br><br></font>
</div>
<script language="Javascript">
function cambia_dia(dia,curso,centro) {
        window.open("cambia_estado_dia.php?dia="+dia+"&curso="+curso+"&centro="+centro,"cambia_dia","toolbar=no,width=150,height=100");
        }
</script>
<?php 
}
require("../../configuracion/parte_inferior.php"); ?>
