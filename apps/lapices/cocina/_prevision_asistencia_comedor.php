<?php 
require("../configuracion/config.php");
// require("../configuracion/autentifica.php");

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

$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];

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
    <td align="center"><small><b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?><br><br>Previsión de asistencia al Comedor</i></b></small></td>
  </tr>
<?php 

$busca_alumnos=db_query("SELECT usuarios.*, alumnos.*, alumnos.id_centro AS centro_alumno, alumnos.comedor AS tipo_menu, alumnos.tipo_menu_comedor AS tipo_menu_consumido FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.comedor<>2 AND alumnos.fecha_matricula<='".date("Y-m-d",$fecha_time_stamp)."' AND alumnos.fecha_baja>='".date("Y-m-d",$fecha_time_stamp)."' ORDER BY alumnos.id_centro, comedor, alumnos.tipo_menu_comedor, fecha_nacimiento DESC, Apellido1, Apellido2, Nombre") or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <form name="listado_alumnos" method="POST">
        <?php 
        // Totales centro
        
        $menus_regimen_p=0;
        $menus_peque=0;
        $menus_regimen_m=0;
        $menus_mayores=0;
        $menus_regimen_c=0;
        $menus_cole=0;
        $menus_toma=0;
        $menus_regimen_b=0;
        $menus_bebe=0;
        $total_menus=0;
        
        // Parcial centro
        $menus_regimen_p_c=0;
        $menus_peque_c=0;
        $menus_regimen_m_c=0;
        $menus_mayores_c=0;
        $menus_regimen_c_c=0;
        $menus_cole_c=0;
        $menus_toma_c=0;
        $menus_regimen_b_c=0;
        $menus_bebe_c=0;
        $total_menus_c=0;
        
        $centro_mostrado=0;
        $nombre_centro_alumno="";
        $menus_edu=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        if ($listado_alumnos["tipo_menu"]>0) {

        if ($centro_mostrado!=$listado_alumnos["centro_alumno"]) {
                if ($centro_mostrado!=0) {
                ?>
<tr bgcolor="#FFFFFF">
        <td colspan=3 align="center">
        <br><font size=3>TOLALES CENTRO: <?=$nombre_centro_alumno;?></font><br><br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td align="center"><font size=3 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Normales</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Régimen</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left" width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Menús servidos: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c+$menus_peque_c+$menus_mayores_c+$menus_cole_c+$menus_toma_c+$menus_bebe_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_p_c+$menus_regimen_m_c+$menus_regimen_c+$menus_regimen_b_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c+$total_menus_c?></font></td>
        </tr>
        <tr>
        <td colspan="4"><font size=2 face="<?= $fuente_general; ?>"><b>Desglose por tipos:</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Tomas: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_toma_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> &nbsp;</font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_toma_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú bebé: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_bebe_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_b_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_bebe_c+$menus_regimen_b_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú pequeños: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_peque_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_p_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_peque_c+$menus_regimen_p_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú mayores: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_mayores_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_m_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_mayores_c+$menus_regimen_m_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú colegial: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_cole_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_c_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_cole_c+$menus_regimen_c_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú Adulto: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> 0</font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c?></font></td>
        </tr>
        </table><br>
        </td>
</tr>
                <?php 
                }
                // Consulta centro
                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alumnos["centro_alumno"]);
                $datos_centro=db_fetch_array($busca_datos_centro);

                $nombre_centro_alumno=$datos_centro["nombre_centro"];
                
                // Cuenta los menús de educador
                $menus_edu_c=0;

                $busca_menu_educadora=db_query("SELECT * FROM asistencia_docentes, usuarios WHERE asistencia_docentes.id_usuario=usuarios.id_usuario AND asistencia_docentes.fecha='".date("Y-m-d",$fecha_time_stamp)."' AND asistencia_docentes.comedor=1 AND asistencia_docentes.id_centro_asistencia=".$listado_alumnos["centro_alumno"]);
                while ($menu_educadora=db_fetch_array($busca_menu_educadora)) {
                $menus_edu++;
                $menus_edu_c++;
                }
                ?>
                <tr bgcolor="#efefef">
                        <td colspan=3><font size=3 face="<?= $fuente_general; ?>"><b><?=$nombre_centro_alumno;?></b></font></td>
                </tr>
                <?php 
                /*
                <tr bgcolor="#efefef">
                        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Menú</b></font></td>
                </tr>
                */
                $centro_mostrado=$listado_alumnos["centro_alumno"];
                
                // Parcial centro
                $menus_regimen_p_c=0;
                $menus_peque_c=0;
                $menus_regimen_m_c=0;
                $menus_mayores_c=0;
                $menus_regimen_c_c=0;
                $menus_cole_c=0;
                $menus_toma_c=0;
                $menus_regimen_b_c=0;
                $menus_bebe_c=0;
                $total_menus_c=0;
                }
        
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        // Cuenta total
        $total_menus_c++;
        $total_menus++;
                
        // Menu pequeños o mayores por defecto
        
        if ($listado_alumnos["tipo_menu_consumido"]>0) {
        if ($listado_alumnos["tipo_menu_consumido"]==4) {
                $menus_toma++;
                $menus_toma_c++;
                $nombre_menu="Toma";
                }
        if ($listado_alumnos["tipo_menu_consumido"]==1) {
                $nombre_menu="Pequeños";
                if ($listado_alumnos["tipo_menu"]==3) {
                        $menus_regimen_p++;
                        $menus_regimen_p_c++;
                        $nombre_menu=$nombre_menu." regimen";
                        } else {
                        $menus_peque++;
                        $menus_peque_c++;
                        }
                }
        if ($listado_alumnos["tipo_menu_consumido"]==2) {
                $nombre_menu="Mayores";
                if ($listado_alumnos["tipo_menu"]==3) {
                        $menus_regimen_m++;
                        $menus_regimen_m_c++;
                        $nombre_menu=$nombre_menu." regimen";
                        } else {
                        $menus_mayores++;
                        $menus_mayores_c++;
                        }
                }
        if ($listado_alumnos["tipo_menu_consumido"]==3) {
                $nombre_menu="Colegial";
                if ($listado_alumnos["tipo_menu"]==3) {
                        $menus_regimen_c++;
                        $menus_regimen_c_c++;
                        $nombre_menu=$nombre_menu." regimen";
                        } else {
                        $menus_cole++;
                        $menus_cole_c++;
                        }
                }
        if ($listado_alumnos["tipo_menu_consumido"]==5) {
                $nombre_menu="Bebe";
                if ($listado_alumnos["tipo_menu"]==3) {
                        $menus_regimen_b++;
                        $menus_regimen_b_c++;
                        $nombre_menu=$nombre_menu." regimen";
                        } else {
                        $menus_bebe++;
                        $menus_bebe_c++;
                        }
                }
        } else {
        if ($edad_alumno<1) {
                $menus_toma++;
                $menus_toma_c++;
                $nombre_menu="Toma";
                } else {
                if ($edad_alumno<2) {
                        $nombre_menu="Pequeños";
                        if ($listado_alumnos["tipo_menu"]==3) {
                                $menus_regimen_p++;
                                $menus_regimen_p_c++;
                                $nombre_menu=$nombre_menu." regimen";
                                } else {
                                $menus_peque++;
                                $menus_peque_c++;
                                }
                        } else {
                        if ($edad_alumno<3) {
                                $nombre_menu="Mayores";
                                if ($listado_alumnos["tipo_menu"]==3) {
                                        $menus_regimen_m++;
                                        $menus_regimen_m_c++;
                                        $nombre_menu=$nombre_menu." regimen";
                                        } else {
                                        $menus_mayores++;
                                        $menus_mayores_c++;
                                        }
                                } else {
                                $nombre_menu="Colegial";
                                if ($listado_alumnos["tipo_menu"]==3) {
                                        $menus_regimen_c++;
                                        $menus_regimen_c_c++;
                                        $nombre_menu=$nombre_menu." regimen";
                                        } else {
                                        $menus_cole++;
                                        $menus_cole_c++;
                                        }
                                }
                        }
                }
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
        /*
        <tr>
        <td><font size=2 face="<?= $fuente_general; ?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?php 
        $total_menus++;
        $total_menus_c++;
        // echo $nombre_menu;
        </font></td>
        </tr>
        */
                }
        }
        ?>
<tr bgcolor="#FFFFFF">
        <td colspan=3 align="center">
        <br><font size=3>TOLALES CENTRO: <?=$nombre_centro_alumno;?></font><br><br>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td align="center"><font size=3 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Normales</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Régimen</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left" width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Menús servidos: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c+$menus_peque_c+$menus_mayores_c+$menus_cole_c+$menus_toma_c+$menus_bebe_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_p_c+$menus_regimen_m_c+$menus_regimen_c+$menus_regimen_b_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c+$total_menus_c?></font></td>
        </tr>
        <tr>
        <td colspan="4"><font size=2 face="<?= $fuente_general; ?>"><b>Desglose por tipos:</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Tomas: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_toma_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> &nbsp;</font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_toma_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú bebé: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_bebe_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_b_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_bebe_c+$menus_regimen_b_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú pequeños: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_peque_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_p_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_peque_c+$menus_regimen_p_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú mayores: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_mayores_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_m_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_mayores_c+$menus_regimen_m_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú colegial: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_cole_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_c_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_cole_c+$menus_regimen_c_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú Adulto: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> 0</font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu_c?></font></td>
        </tr>
        </table>
<br>
        <FONT SIZE=3>TOTAL MENÚS SERVIDOS</FONT><BR><BR>
        <table cellpadding=2 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td align="center"><font size=3 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Normales</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Régimen</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left" width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Menús servidos: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu+$menus_peque+$menus_mayores+$menus_cole+$menus_toma+$menus_bebe?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_p+$menus_regimen_m+$menus_regimen_c+$menus_regimen_b?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu+$total_menus?></font></td>
        </tr>
        <tr>
        <td colspan="4"><font size=2 face="<?= $fuente_general; ?>"><b>Desglose por tipos:</b></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Tomas: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_toma?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> &nbsp;</font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_toma?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú bebé: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_bebe?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_b?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_bebe+$menus_regimen_b?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú pequeños: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_peque?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_p?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_peque+$menus_regimen_p?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú mayores: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_mayores?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_m?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_mayores+$menus_regimen_m?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú colegial: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_cole?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_regimen_c?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_cole+$menus_regimen_c?></font></td>
        </tr>
        <tr>
        <td bgcolor="#efefef" align="left"><font size=2 face="<?= $fuente_general; ?>"><b>Menú Adulto: </b></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu?></font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> 0</font></td>
        <td bgcolor="#ffffff" align="right"><font size=2 face="<?= $fuente_general; ?>"> <?=$menus_edu?></font></td>
        </tr>
        </table>
        </td>
</tr>
        </form>
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
