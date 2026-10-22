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

$totales_ausencias=array();
$totales_ausencias_mes=array();
$colores_celdas=array("#f02f81","#c2b2eb","#83afd5","#eae266");
$nombres_ausencias=array("Ausencia no justificada","Asuntos propios","Baja médica","Vacaciones");

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

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

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
<font color="#000000" size=3 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
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
        
        // Busca ausencias
        
        $rsNot_query=db_query_params('SELECT * FROM horario_docentes_dias,usuarios WHERE usuarios.id_usuario=horario_docentes_dias.id_usuario AND fecha=? AND tipo_ausencia>0 ORDER BY usuarios.id_centro;', 's', array((string)(date("Y-m-d", $inicio_time_stamp)))) or die(db_error());
        
        if (db_num_rows($rsNot_query)!=0) {
        ?>
        <table border="1" bordercolor="#000000" style="border-collapse:collapse" width="98%" cellspacing="0">
        <?php 
        $escuela_actual=-1;
        $aula_actual=-1;
        while($rsNot=db_fetch_array($rsNot_query)) {
        if ($escuela_actual!=$rsNot["id_centro"]) {
                $escuela_actual=$rsNot["id_centro"];
                ?>
                <tr valign=top>
                        <td width="100%" colspan=2><font face="Arial" color="#800000"><small><small><b><?= $nombres_escuelas[$rsNot["id_centro"]]; ?></b></small></small></font></td>
                </tr>
                <?php 
                }
        $totales_ausencias[$escuela_actual][$rsNot["tipo_ausencia"]]=$totales_ausencias[$escuela_actual][$rsNot["tipo_ausencia"]]+1;
        $totales_ausencias_mes[$mes][$escuela_actual][$rsNot["tipo_ausencia"]]=$totales_ausencias_mes[$mes][$escuela_actual][$rsNot["tipo_ausencia"]]+1;
        ?>
        <tr valign=top>
                <td width="17%" align=center valign=middle><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="<?=$colores_celdas[$rsNot["tipo_ausencia"]-1]?>"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td>
                <td width="83%"><small><small><a href="/gestion/docentes/cambiar_dia.php?id_docente=<?=$rsNot["id_usuario"]?>&dia=<?=strtotime($rsNot["fecha"])?>"><b><?= $rsNot["Nombre"]." ".$rsNot["Apellido1"]." ".$rsNot["Apellido2"]; ?></b></a></small></small></td>
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
?>
<tr>
<td>
</td>
</tr>
</table>
<br>
<table width=100% bordercolor="#000000" border=1 style="border-collapse: collapse">
<tr>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE centro_propio=1");
        $numero_centros_propios=db_num_rows($busca_centros);
        ?>
        <td align="center" bgcolor="#efefef" colspan=<?=2+$numero_centros_propios;?>><font size=2><b>Resumen incidencias <?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font></td>
</tr>
<tr>
        <td rowspan=2><font size=2>Tipo</font></td>
        <td align="center" colspan=<?=$numero_centros_propios?>><font size=2>Nº en el mes</font></td>
</tr>
<tr>
        <?php 
        while($datos_centros=db_fetch_array($busca_centros)) {
        ?>
        <td align="center"><font size=2><?=$datos_centros["nombre_centro"]?></font></td>
        <?php 
        }
        ?>
</tr>
<?php 
for($ci=1;$ci<=count($nombres_ausencias);$ci++) {
        ?>
        <tr>
        <td size=16 align=left><table><tr><td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="<?=$colores_celdas[$ci-1]?>"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td> <font size=2><?=$nombres_ausencias[$ci-1];?></font></td></tr></table></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE centro_propio=1");
        while($datos_centros=db_fetch_array($busca_centros)) {
        
        $total_este=$totales_ausencias_mes[$mes][$datos_centros["id_centro"]][$ci];
        if (strlen($total_este)==0) {
                $total_este=0;
                }
        ?>
        <td align="center"><font size=2><?=$total_este;?></font></td>
        <?php 
        }
        ?>
        </tr>
        <?php 
        }
?>
</table>
<br>
<?php 
$mes=$mes+1;
if ($mes==13) {
        $mes=1;
        }
$anio_actual=date("Y",$inicio_time_stamp);
$inicio_time_stamp=$inicio_time_stamp-(86400*7);
?>
<font size=1><b>Dias trabajados durante el mes:</b> <?=$dias_trabajados_mes?><br><br></font>
<?php 
}
?>
<br>
<table width=100% bordercolor="#000000" border=1 style="border-collapse: collapse">
<tr>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE centro_propio=1");
        $numero_centros_propios=db_num_rows($busca_centros);
        ?>
        <td align="center" bgcolor="#efefef" colspan=<?=2+$numero_centros_propios;?>><font size=2><b>Resumen incidencias curso completo</b></font></td>
</tr>
<tr>
        <td rowspan=2><font size=2>Tipo</font></td>
        <td align="center" colspan=<?=$numero_centros_propios?>><font size=2>Nº en el curso</font></td>
</tr>
<tr>
        <?php 
        while($datos_centros=db_fetch_array($busca_centros)) {
        ?>
        <td align="center"><font size=2><?=$datos_centros["nombre_centro"]?></font></td>
        <?php 
        }
        ?>
</tr>
<?php 
for($ci=1;$ci<=count($nombres_ausencias);$ci++) {
        ?>
        <tr>
        <td size=16 align=left><table><tr><td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="<?=$colores_celdas[$ci-1]?>"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td> <font size=2><?=$nombres_ausencias[$ci-1];?></font></td></tr></table></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE centro_propio=1");
        while($datos_centros=db_fetch_array($busca_centros)) {
        
        $total_este=$totales_ausencias[$datos_centros["id_centro"]][$ci];
        if (strlen($total_este)==0) {
                $total_este=0;
                }
        ?>
        <td align="center"><font size=2><?=$total_este;?></font></td>
        <?php 
        }
        ?>
        </tr>
        <?php 
        }
?>
</table>
<br>
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
