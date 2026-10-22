<?php 
include("../../configuracion/config.php");
?>
<html>
<head>

 <title>SELECCIÓN FECHAS</title>

 <META http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
 <META NAME='Keywords' CONTENT=''>
 <META NAME='Description' CONTENT=''>
 <META NAME='Generator' CONTENT='flash 5'>
 <META HTTP-EQUIV='Content-language' CONTENT='es'>
 <META HTTP-EQUIV='Reply-to' CONTENT=''>
 <META NAME='Author' CONTENT=''>
 <META NAME='Distribution' CONTENT='Global'>
 <META NAME='Rating' CONTENT='General'>
 <META NAME='Robots' CONTENT='All'>
 <META NAME='Revisit-After' CONTENT='1 days'>
 <META HTTP-EQUIV='Reply-to' CONTENT=''><link rel="SHORTCUT ICON" href="">
 
<style type="text/css">
        body {
                scrollbar-arrow-color:#840084;
                scrollbar-face-color:#DEE3D6;
                scrollbar-bace-color:green;
                scrollbar-highlight-color:#B5B6A5;
                scrollbar-shadow-color:black;
                scrollbar-track-color:#B5B6A5;
                font-family: Trebuchet MS, Arial;
                font-size: x-small;
                font-style: normal;
                line-height: normal;
                font-weight: normal;
                font-variant: normal;
                color: #000000;
                text-decoration: none;
}

a {  font-style: normal; font-weight: normal; color: #000000; text-decoration: none}
a:hover {  font-style: normal; font-weight: normal; text-decoration: underline}
</style>

<SCRIPT LANGUAGE=javascript>
<!--
self.focus();
//-->
</SCRIPT>

</head>

<body bgcolor="#FEFEFE" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center">
<?php 
$inicio=$_GET["dia"];
$id_aula=$_GET["id_aula"];
$inicio_semana=$_GET["inicio_semana"];

$id_aula_destino=$_GET["id_aula_destino"];
$grupo_destino=$_GET["grupo_destino"];

if (strlen($id_aula_destino)==0) {
	$id_aula_destino=$id_aula;
	}

if ($id_aula!=0) {
        $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

        if (db_num_rows($consulta_datos_aula)!=0)
                {
                $datos_aula=db_fetch_array($consulta_datos_aula);

                // Datos Fijos
                $aula_nombre=$datos_aula["nombre"];
                $id_curso_actual=$datos_aula["id_curso"];
                $centro_aula=$datos_aula["id_centro"];
                $ciclo_aula=$datos_aula["ciclo"];
                $edad_aula=$datos_aula["edad_minima_prog"];

                $edad_aula_max=$datos_aula["edad_maxima_prog"];
                }

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);

        }

if (strlen($inicio)!=0) {
        $anio_fecha=substr($inicio,0,4);
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

// Calcula año anterior y posterior
$ano_anterior_c=intval(date("Y",$fecha_time_stamp))-1;
$ano_posterior_c=intval(date("Y",$fecha_time_stamp))+1;
$ano_anterior_c1=intval(date("Y",$fecha_time_stamp))-10;
$ano_posterior_c1=intval(date("Y",$fecha_time_stamp))+10;
?>
<font color="#000000" size=1 face="<?=$fuente_general?>">
<a href="javascript: cambia_dia(<?=$inicio_semana?>,<?=$id_aula?>,'<?=$ano_anterior_c1?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>');"><<<</a> | <a href="javascript: cambia_dia(<?=$inicio_semana?>,<?=$id_aula?>,'<?=$ano_anterior_c?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>');"><<</a> | <b><?php  echo $mesnombre; ?>, <?php  echo date("Y",$fecha_time_stamp); ?></b> | <a href="javascript: cambia_dia(<?=$inicio_semana?>,<?=$id_aula?>,'<?=$ano_posterior_c?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>');">>></a> | <a href="javascript: cambia_dia(<?=$inicio_semana?>,<?=$id_aula?>,'<?=$ano_posterior_c1?>/<?=$mes?>/<?=date("j",$fecha_time_stamp)?>');">>>></a>
</font>
<form name="meses" action="copiar_semana.php" method="POST">
<input type="hidden" name="inicio_semana_origen" value="<?=strtotime($inicio_semana);?>">
<input type="hidden" name="inicio_semana_destino" value="">
<input type="hidden" name="id_aula" value="<?=$id_aula?>">
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
<td align="center" bgcolor="<?=$fondo_celda_dia;?>">
<font face="<?=$fuente_general?>" color="#000000" size=1>
<?php 
    if ($dias==1) {
        print "<a href='javascript:seleccionar($inicio_time_stamp)' title='Seleccionar semana'><font color='#d30000'>";
        }
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
    if ($dias==1) { print "</font></a>"; }

?>
</font>
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
<tr>
<td colspan=3 align="right"><font size=1>Aula destino:</font</td>
<td colspan=4 align="left"><font size=1><select name="id_aula_destino" style="font-size: 10px">
<?php 

        $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

        if (db_num_rows($consulta_datos_aula)!=0)
                {
                $datos_aula=db_fetch_array($consulta_datos_aula);

                // Datos Fijos
                $aula_nombre=$datos_aula["nombre"];
                $id_curso_aula=$datos_aula["id_curso"];
                $centro_aula=$datos_aula["id_centro"];
                }
                
if ($edad_aula==0) {
	$filtro_edad=" AND centro_aulas.edad_minima_prog=$edad_aula";
	} else {
	$filtro_edad="";	
	}
	
$consulta_aulas=db_query("SELECT *,centro_aulas.nombre AS nombreaula, cursos_academicos.nombre AS nombrecurso FROM centro_aulas,cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.tipo=0 AND centro_aulas.id_centro=$centro_aula AND (centro_aulas.id_curso=$id_curso_aula OR cursos_academicos.fecha_fin>='".date("Y-m-d")."')$filtro_edad ORDER BY centro_aulas.edad_minima;") or die(db_error());
while($aulas=db_fetch_array($consulta_aulas)){
?>
<option value="<?=$aulas["id_aula"]?>"<?php  if ($aulas["id_aula"]==$id_aula_destino) { print " selected"; } ?>><?=urldecode($aulas["nombreaula"]." Curso: ".$aulas["nombrecurso"])?></option>
<?php 
}
?></select></font</td>
</tr>
<?php 
if ($edad_aula==0) {
?>
<tr><td colspan=7 align="center"><font size=1><b>Copiar del grupo: <select name="grupo_origen" size=1 style="font-size: 10px">
<?php 
$lista_grupos="";
for ($c_grupos=0;$c_grupos<12;$c_grupos++) {	
	$g_seleccionado="";
	
	if ($grupo_destino==$c_grupos) { $g_seleccionado=" selected"; }

	$lista_grupos.="<option value='".$c_grupos."'".$g_seleccionado.">".($c_grupos+1)."</option>";
	}
echo($lista_grupos);
?>
</select> …al grupo: <select name="grupo_destino" size=1 style="font-size: 10px"><?=$lista_grupos?></select></b></font></td></tr></table>
<?php 
	} else {
?>
</table>
<input type="hidden" value="" name="grupo_destino">	
<?php 	
	}
?>;
</form>
<font size=1><a href="javascript: cambia_dia(<?=$inicio_semana?>,<?=$id_aula?>,'<?=$ano_anterior?>/<?=$mes_anterior?>/1');"><< Mes anterior</a> | <a href="javascript: cambia_dia(<?=$inicio_semana?>,<?=$id_aula?>,'<?=$ano_siguiente?>/<?=$mes_siguiente?>/1');">Mes siguiente >></a><br>
Para seleccionar el destino haz clic sobre el lunes de la semana elegida.
</font>
</font>
</div>
<script language="Javascript">
function seleccionar(inicio_semana_destino) {
        if (confirm("Se sobreescribirá el contenido de la semana seleccionada.\n\n¿Estás seguro?")) {		
                self.document.meses.inicio_semana_destino.value=inicio_semana_destino;
                self.document.meses.submit();
                }
        }
        
function cambia_dia(inicio_semana,id_aula,dia) {

	id_aula_destino=self.document.meses.id_aula_destino.value;
	grupo_destino=self.document.meses.grupo_destino.value;
	
	self.location.href="calendario_copiar_semana.php?id_aula_destino="+id_aula_destino+"&grupo_destino="+grupo_destino+"&inicio_semana=<?=$inicio_semana?>&id_aula="+id_aula+"&dia="+dia;
	
	}        
        
</script>
</body>
