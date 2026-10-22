<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/parte_superior_sin_login_ancho.php"); ?>
<?php 

$mostrar_listado=$_GET["mostrar_listado"];
if (strlen($mostrar_listado)==0) {
        $mostrar_listado=0;
        }

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

?>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br><br><big>Previsión asistencia Comedor</big></i><br><br>Escuela: <?=$nombre_centro_actual;?></b><br><br></small></td>
  </tr>
<?php 
if ($solo_escuela==1) {
        $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.comedor<>2 AND alumnos.id_centro=$centro_actual ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());
        } else {
        $busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.comedor<>2 ORDER BY alumnos.id_centro, alumnos.aula") or die(db_error());
        }

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=6><font size=1 face="<?= $fuente_general; ?>">Para ver la ficha de un alumno, haz clic sobre su nombre o sobre el botón ( <img src="editar.gif" align="absmiddle">)</font></td>
        </tr>
        <tr bgcolor="#efefef" valign="top">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Menú</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela/Aula</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        $totales_menu=array();
        $totales_platos=array();
        
        $totales_menu_escuelas=array();
        $totales_platos_escuelas=array();

        $totales_menu_aulas=array();
        $totales_platos_aulas=array();
        
        $usuario_anterior=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        if (($listado_alumnos["comedor"]>0) and ($usuario_anterior!=$listado_alumnos["id_usuario"])) {

        // Consulta centro
        $busca_datos_centro=db_query("SELECT * FROM centros,centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_aula=".$listado_alumnos["aula"]);
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
                if ($menu_actual_en_ficha>0) {
                        $id_menu_seleccionado=$menu_actual_en_ficha;
                        } else {
                        if ($menu_actual_en_ficha==0) {

                        // Comprueba si es de regimen
                        $regimen=$listado_alumnos["comedor"];
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
                                }
                        }
        
        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$id_menu_seleccionado);
        if (db_num_rows($busca_datos_menu)!=0) {
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                
                // Comprueba los alimentos del menú y comprueba las alergias del niño
                
                                // Comprueba si ese día hay comida programada
                                $hay_comida=false;

                                $busca_comida_ese_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu_seleccionado AND fecha='".date("Y-m-d",$fecha_time_stamp)."';") or die(db_error());
                                $motivo_no_hay="";
                                if (db_num_rows($busca_comida_ese_dia)!=0) {

                                        $hay_comida=true;
                                        $datos_comida=db_fetch_array($busca_comida_ese_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        if (substr($array_campos_platos[$c_platos],0,8)=="merienda" && app_aula_sin_merienda($listado_alumnos["centro_alumno"],$listado_alumnos["id_curso"],$listado_alumnos["aula"],date("Y-m-d",$fecha_time_stamp))) {
                                                continue;
                                                }

                                        $codigo_plato=$datos_comida[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {
                                                $sumar_plato=true;
                                                $consulta_datos_plato=db_query("SELECT * FROM menu_platos WHERE id_plato=$codigo_plato") or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($listado_alumnos[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alimento NO introducido: ".app_url_text_utf8($ingredientes["nombre_alimento"]).", ingrediente del plato ".app_url_text_utf8($datos_plato["nombre_plato"])."<br>";
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($listado_alumnos[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        $sumar_plato=false;
                                                                                        $motivo_no_hay=$motivo_no_hay."Alergia a $nombre_alergia: ".app_url_text_utf8($ingredientes["nombre_alimento"]).", ingrediente del plato ".app_url_text_utf8($datos_plato["nombre_plato"])."<br>";
                                                                                        }
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
                                                                $totales_platos[$codigo_plato][1]=app_url_text_utf8($datos_plato["nombre_plato"])." ".$edades;
                                                                $totales_platos[$codigo_plato][0]=$totales_platos[$codigo_plato][0]+1;

                                                                $totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]=$totales_platos_escuelas[$codigo_plato][$listado_alumnos["centro_alumno"]]+1;

                                                                $totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]=$totales_platos_aulas[$codigo_plato][$listado_alumnos["aula"]]+1;

                                                                }
                                                        }

                                                }
                                                }

                                        } else {
                                        $motivo_no_hay="Día no programado para el menú asignado: ".app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                                        }
                
                } else {
                $nombre_menu="Menú no asignado";
                }
        
        $totales_menu[$id_menu_seleccionado]=$totales_menu[$id_menu_seleccionado]+1;
        $totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]=$totales_menu_escuelas[$id_menu_seleccionado][$listado_alumnos["centro_alumno"]]+1;
        $totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]=$totales_menu_aulas[$id_menu_seleccionado][$listado_alumnos["aula"]]+1;

        if (($motivo_no_hay!="") or ($mostrar_listado==1)) {
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
        <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
        </tr>
        <?php 
        }
        
        $usuario_anterior=$listado_alumnos["id_usuario"];
                }
        }
        
        // Cuenta los menús de educador
        $menus_edu=0;
        
        $busca_menu_educadora=db_query("SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1 AND asistencia_docentes.id_centro_asistencia=$centro_actual ORDER BY asistencia_docentes.menu_seleccionado_hoy");
        if (db_num_rows($busca_menu_educadora)!=0) {
        ?>
        <tr bgcolor="#efefef">
        <td colspan=4><font size=2><b>Educadoras/es</b></font></td>
        <td colspan=2><font size=2><b>Menú seleccionado</b></font></td>
        </tr>
        <?php 
        }
        while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {
        
        // Consulta el nombre del menú
        $busca_datos_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=".$menu_educadora["menu_seleccionado_hoy"]);
        if (db_num_rows($busca_datos_menu)!=0) {
                $id_menu_seleccionado=$menu_educadora["menu_seleccionado_hoy"];
                $datos_menu_seleccionado=db_fetch_array($busca_datos_menu);
                $nombre_menu=app_url_text_utf8($datos_menu_seleccionado["nombre_menu"]);
                }
                
         $totales_menu[$menu_educadora["menu_seleccionado_hoy"]]=$totales_menu[$menu_educadora["menu_seleccionado_hoy"]]+1;
        ?>
        <tr>
                <td colspan=4><font size=2><b><?=$menu_educadora["Apellido1"]?> <?=$menu_educadora["Apellido2"]?>, <?=$menu_educadora["Nombre"]?></b></font></td>
                <td colspan=2><font size=2><?=$nombre_menu;?></font></td>
        </tr>
        <?php 
        $menus_edu++;
        }
        
        ?>
        </form>
        </table><br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=7><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de menús servidos</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre menú</b></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$centros["nombre_centro"];?></b></font></td>
        <?php 
        }
        ?>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad Total</b></font></td>
        </tr>
        <?php 
        $consulta_datos_menus=db_query("SELECT * FROM menu_menus ORDER BY edad_ini_menu;") or die(db_error());
        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
        if ($totales_menu[$datos_menus["id_menu"]]>0) {

        ?>
        <tr>
                <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"><?=app_url_text_utf8($datos_menus["nombre_menu"])?></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#ffffff" align="right"><?=number_format($totales_menu_escuelas[$datos_menus["id_menu"]][$centros["id_centro"]],0,",",".");?></td>
        <?php 
        }
        ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$totales_menu[$datos_menus["id_menu"]];?></font></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table><br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc" width=100%>
        <tr>
        <td bgcolor="#efefef" align="left" colspan=8><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de platos servidos</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Nombre plato</b></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><?=$centros["nombre_centro"];?></b></font></td>
        <?php 
        }
        ?>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad total</b></font></td>
        <td bgcolor="#efefef" align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Instrucciones para servir</b></font></td>
        </tr>
        <?php 
        $consulta_datos_plato=db_query("SELECT * FROM menu_platos ORDER BY nombre_plato, edad_ini_plato DESC") or die(db_error());
        while($datos_plato=db_fetch_array($consulta_datos_plato)) {

        $codigo_plato=$datos_plato["id_plato"];
        if ($totales_platos[$codigo_plato][0]>0) {
        ?>
        <tr>
                <td bgcolor="#ffffff" align="left"><font size=2 face="<?= $fuente_general; ?>"><?=$totales_platos[$codigo_plato][1];?></font></td>
        <?php 
        $busca_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($busca_centros)) {
        ?>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=number_format($totales_platos_escuelas[$codigo_plato][$centros["id_centro"]],0,",",".");?></font></td>
        <?php 
        }
        ?>
                <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$totales_platos[$codigo_plato][0];?></font></td>
                <td bgcolor="#ffffff" align="left"><font size=1 face="<?= $fuente_general; ?>"> <?=app_url_text_utf8($datos_plato["instrucciones_servir"]);?></font></td>
        </tr>
        <?php 
                }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en el comedor.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="window.close()" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

