<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica_padre.php");

$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto" onload="MM_preloadImages('/webcam/imagenes/ver_menu_act.gif')">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10><?php 
if ($_SESSION['permitido_padre']==true)
{
?>
<br><br><a title="Cerrar sesión" href="<?= $url_root; ?>/configuracion/cerrar_sesion_padres.php"><img src="<?= $url_root; ?>/imagen/menu/fin.gif" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
}
?></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
<?php 

$nombre_usuario=$_SESSION['nombre_padre'];

$version_js=$_GET["version_js"];

if (strlen($version_js)!=0) {
        $_SESSION['version_js']=$version_js;
        } else {
        $version_js=$_SESSION['version_js'];
        }
if (strlen($version_js)==0) {
        $version_js==0;
        }

$errores="";

if ($errores=="") {

        // Busca usuario
        $busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario'");

        if (db_num_rows($busca_usuario)==0) {
                $errores="El nombre de usuario introducido no existe.<br>";
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                // Mostramos la información del niño

                $id_usuario=$datos_usuario["id_usuario"];
                $id_hijo=$datos_usuario["padre_de"];

                // Busca datos del padre.
                $busca_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario") or die(db_error()."- No se encuentran los datos del padre.");

                $datos_padre=db_fetch_array($busca_datos_padre);
                if ($datos_padre["sexo"]=="V") {
                        $sufijo_padre="o";
                        } else {
                        $sufijo_padre="a";
                        }

                // Comprueba si tiene email
                $no_email=false;
                if (strlen($datos_padre["email"])==0) {
                        $no_email=true;
                        $errores="-No disponemos de tu dirección de Email.<br>";
                        } else {
                        // y si el mail es correcto
                        $email_padre=$datos_padre["email"];
                        if ((strpos($email_padre,"@") ? strpos($email_padre,"@")+1 : 0)==0 || (strpos($email_padre," ") ? strpos($email_padre," ")+1 : 0)!=0 || (strpos($email_padre,".") ? strpos($email_padre,".")+1 : 0)==0)
                                {
                                $no_email=true;
                                $errores="- El E-Mail que tenemos parece ser incorrecto, falta la arroba (@) o contiene caracteres no permitidos (espacios, acentos, etc.).<br>";
                                }
                        }
                // Comprueba DNI
                $dni_padre=$datos_padre["DNI"];
                if (strlen($dni_padre)==0)
                                {
                                $errores="- No disponemos de tu nº de DNI o NIE.";
                                }
                // Comprueba telefono
                $telefono_padre=$datos_padre["telefono1"].$datos_padre["telefono2"].$datos_padre["movil"];
                if (strlen($telefono_padre)==0)
                                {
                                $errores="- No disponemos de ningún teléfono de contacto.";
                                }
                // Comprueba domicilio
                $domicilio_padre=$datos_padre["Direccion"];
                if (strlen($domicilio_padre)==0)
                                {
                                $errores="- No disponemos de tu domicilio.";
                                }
                // Comprueba cp
                $codigo_postal_padre=$datos_padre["cp"];
                if (strlen($codigo_postal_padre)==0)
                                {
                                $errores="- No disponemos de tu código postal.";
                                }
                }

        }
        
if ($errores=="") {
?>
<div align="center">
<?php 
$id_alumno=$_GET["id_hijo"];
if (strlen($id_alumno)==0) {
        $id_alumno=$_POST["id_hijo"];
        }


$busca_datos_alumno=db_query("SELECT usuarios.*,alumnos.*,alumnos.id_centro AS centro_asistencia FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=$id_alumno LIMIT 0,1") or die(db_error());

if (db_num_rows($busca_datos_alumno)!=0) {

$datos_alumno=db_fetch_array($busca_datos_alumno);

$centro_actual=$datos_alumno["centro_asistencia"];
$id_usuario=$datos_alumno["id_usuario"];

// Comprueba si es su padre
$busca_si_es_padre=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario' AND padre_de=$id_usuario");
if (db_num_rows($busca_si_es_padre)!=0) {

$nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];
$comedor_alumno=$datos_alumno["comedor"];

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

$mes_fin_curso=date("n",strtotime($datos_curso_actual["fecha_fin"]));
$anio_fin_curso=date("Y",strtotime($datos_curso_actual["fecha_fin"]));

?>
<font size=3><b>Calendario de asistencia<br>Alumno/a <?=$nombre_completo_alumno?> durante el curso <?=$datos_curso_actual["nombre"]?></b><br></font>
<font size=2>ATENCION: Los días no lectivos son los que aparecen marcados en el calendario de color naranja.</font><br><br>
<?php 
$anio_actual=date("Y",$fecha_time_stamp);
$dias_trabajados=0;
$dias_asistidos_reales=0;
$dias_comedor=0;
$dias_comedor_reales=0;
$dias_faltados=0;
$faltas_comedor=0;
for ($mes_s=1; $mes_s<=12; $mes_s=$mes_s+1) {
$mesnombre=$nombre_mes[$mes];
$dias_trabajados_mes=0;
$dias_asistidos_reales_mes=0;
$dias_comedor_mes=0;
$dias_comedor_reales_mes=0;
$dias_faltados_mes=0;
$faltas_comedor_mes=0;
?>
<font color="#000000" size=1 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font>
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

    if ($mes==date("n",$inicio_time_stamp)) {

        $estado_boton_comedor="";
        
        // Comprueba si está marcado
        $busca_dia_en_calendario=db_query("SELECT * FROM calendario_laboral WHERE id_curso=$id_curso_actual AND id_centro=$centro_actual AND dia='".date("Y-m-d",$inicio_time_stamp)."' LIMIT 0,1");
        
        // Estado inicial
        $estado_boton="";
        $fondo_celda_dia="#EFEFEF";
        
        if (db_num_rows($busca_dia_en_calendario)==0) {
                if (($dias==7) or ($dias==6)) {
                        // Sábado o domingo
                        $inserta_domingo=db_query("INSERT INTO calendario_laboral (id_curso,id_centro,dia,estado) VALUES ('$id_curso_actual','$centro_actual','".date("Y-m-d",$inicio_time_stamp)."',1) LIMIT 0,1;");
                        $estado_boton=" disabled";
                        $fondo_celda_dia="#F17327";
                        }
                }
                        
        if (db_num_rows($busca_dia_en_calendario)!=0) {
                $datos_dia_en_calendario=db_fetch_array($busca_dia_en_calendario);
                if ($datos_dia_en_calendario["estado"]==1) {
                        // Día festivo
                        $estado_boton=" disabled";
                        $fondo_celda_dia="#F17327";
                        }
                }

        $dia_dentro_periodo=true;
        if ($estado_boton!=" disabled") {
                // Comprueba periodo de inscripcion
                if ((strtotime($datos_alumno["fecha_matricula"])>intval($inicio_time_stamp)) or (strtotime($datos_alumno["fecha_baja"])<intval($inicio_time_stamp-86400))) {
                        // Día fuera del periodo de asistencia
                        $fondo_celda_dia="#FFCE70";
                        $estado_boton=" checked";
                        $dia_dentro_periodo=false;
                        }
                }
        if ($estado_boton!=" disabled") {
                // Comprueba asistencia del niño
                $busca_asistencia=db_query("SELECT * FROM horario_alumnos_dias WHERE id_usuario=$id_alumno AND fecha='".date("Y-m-d",$inicio_time_stamp)."' LIMIT 0,1");
                if (db_num_rows($busca_asistencia)!=0) {
                        $datos_asistencia=db_fetch_array($busca_asistencia);
                        if ($datos_asistencia["no_comedor"]==0) {
                                $estado_boton_comedor=" checked";
                                $dias_comedor_mes++;
                                $dias_comedor++;
                                }
                        if ($datos_asistencia["no_asistencia"]==1) {
                                $estado_boton=" checked";
                                if ($dia_dentro_periodo==true) {
                                        // Dia NO asistido indicado en tabla
                                        $fondo_celda_dia="#F9F5BA";
                                        } else {
                                        $fondo_celda_dia="#FFCE70";
                                        }
                                } else {
                                // Dia asistido indicado en tabla
                                $estado_boton="";
                                $dias_trabajados_mes++;
                                $dias_trabajados++;
                                $fondo_celda_dia="#D1FFD3";
                                }
                        } else {
                        if ($estado_boton!=" checked") {
                                // Dia asistido por omisión
                                $estado_boton="";
                                $dias_trabajados_mes++;
                                $dias_trabajados++;
                                $fondo_celda_dia="#D1FFD3";
                                if ($comedor_alumno!=2) {
                                        $estado_boton_comedor=" checked";
                                        $dias_comedor_mes++;
                                        $dias_comedor++;
                                        }
                                }
                        }
                }

        // Comprueba asistencia real

        $busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$id_usuario." AND fecha='".date("Y-m-d",$inicio_time_stamp)."' LIMIT 0,1") or die(db_error());
        if (db_num_rows($busca_registro_entrada)!=0) {
                $estado_boton=" disabled";
                $fondo_celda_dia="#A6C6EA";

                $dias_asistidos_reales_mes++;
                $dias_asistidos_reales++;
                
                $datos_registro_entrada=db_fetch_array($busca_registro_entrada);
                
                if ($datos_registro_entrada["hora_entrada_c"]!="") {
                        $dias_comedor_reales_mes++;
                        $dias_comedor_reales++;
                        } else {
                        if ($estado_boton_comedor==" checked") {
                                $fondo_celda_dia="#f797f6";
                                $faltas_comedor_mes++;
                                $faltas_comedor++;
                                }
                        }
                
                } else {
                if (($inicio_time_stamp<strtotime(date("Y-m-d"))) and ($fondo_celda_dia=="#D1FFD3")) {
                        $fondo_celda_dia="#FFF200";
                        $dias_faltados_mes++;
                        $dias_faltados++;
                        if ($estado_boton_comedor==" checked") {
                                $faltas_comedor_mes++;
                                $faltas_comedor++;
                                }
                        }
                }
        if ($inicio_time_stamp<strtotime(date("Y-m-d"))) {
                $estado_boton_comedor=$estado_boton_comedor." disabled";
                $estado_boton=$estado_boton." disabled";
                }
                
        db_free_result($busca_registro_entrada);
        
        ?>
        <td align="center" style="background: <?=$fondo_celda_dia;?>" id="celda<?=$inicio_time_stamp;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=1>
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
        ?>
        <br>&nbsp;
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
<font size=1><b>Dias reales de asistencia durante el mes:</b> <?=$dias_asistidos_reales_mes?><br>
<font size=1><b>Dias previstos de asistencia durante el mes:</b> <?=$dias_trabajados_mes?><br>
<font size=1><b>Faltas de asistencia durante el mes:</b> <?=$dias_faltados_mes?><br>
<font size=1><b>Dias reales de comedor durante el mes:</b> <?=$dias_comedor_reales_mes?><br>
<font size=1><b>Dias previstos de comedor durante el mes:</b> <?=$dias_comedor_mes?><br>
<font size=1><b>Faltas de asistencia al comedor durante el mes:</b> <?=$faltas_comedor_mes?><br><br></font>
<?php 
$anio_actual_1=date("Y",$inicio_time_stamp);
$mes_actual_1=date("n",$inicio_time_stamp);
if (($mes_fin_curso==$mes_actual_1) and ($anio_fin_curso==$anio_actual_1)) {
        break;
        }

}
?>
<font size=1><b>Dias reales de asistencia durante el curso:</b> <?=$dias_asistidos_reales?><br>
<font size=1><b>Dias previstos de asistencia durante el curso:</b> <?=$dias_trabajados?><br>
<font size=1><b>Faltas de asistencia durante el curso:</b> <?=$dias_faltados?><br>
<font size=1><b>Dias reales de comedor durante el curso:</b> <?=$dias_comedor_reales?><br>
<font size=1><b>Dias previstos de comedor durante el curso:</b> <?=$dias_comedor?><br>
<font size=1><b>Faltas de asistencia al comedor durante el curso:</b> <?=$faltas_comedor?><br><br></font>
<?php 
}
?>
<br>
<table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000">
<tr><td>
<table>
<tr>
<td colspan=2 align="center" bgcolor="#efefef"><font size=1><b>Leyenda de colores</b></font></td>
</tr>
<tr>
<td bgcolor="#F17327"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día festivo</font></td>
</tr>
<tr>
<td bgcolor="#F9F5BA"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día lectivo al que ñino seleccionado no asistirá.</font></td>
</tr>
<tr>
<td bgcolor="#D1FFD3"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Dia léctivo al que el niño seleccionado debe asistir.</font></td>
</tr>
<tr>
<td bgcolor="#FFCE70"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día no festivo fuera del periodo de inscripción del niño.</font></td>
</tr>
<tr>
<td bgcolor="#A6C6EA"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Día asistido.</font></td>
</tr>
<tr>
<td bgcolor="#FFF200"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Falta de asistencia.</font></td>
</tr>
<tr>
<td bgcolor="#f797f6"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=1>Asiste a docencia pero no se queda a comer cuando había reservado.</font></td>
</tr>
</table></td></tr></table>
<?php 
} else {
?>
Hay pillín pillín... que estás intentando acceder a la información de un niño que no es el tuyo...
<br><br>
<input type="button" onclick="history.go(-1);" value="Volver">
<?php 
}
} else {
?>
El Alumno no Existe...
<br><br>
<input type="button" onclick="history.go(-1);" value="Volver">
<?php 
}

}
require("../configuracion/parte_inferior_padres.php"); ?>
