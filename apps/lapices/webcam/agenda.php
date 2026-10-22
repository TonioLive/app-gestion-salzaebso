<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica_padre.php");

$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

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
<link rel="shortcut icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="https://escuelasinfantileslapices.es/favicon.ico" type="image/vnd.microsoft.icon">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  include("estilos.php"); ?>
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
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" class="tabla_principal" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1">
  <tr>
    <td align="center">
        <img border="0" src="/webcam/imagenes/cabecera_<?=$nombre_pagina_actual?>.png">
    </td>
  </tr>
  <tr>
    <td width="<?= $ancho_pagina_total; ?>" valign="top" align="center">
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

                                $parentesco=$datos_usuario["parentesco"];

                // Busca datos del padre.
                $busca_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario") or die(db_error()."- No se encuentran los datos del padre.");

                $datos_padre=db_fetch_array($busca_datos_padre);
                if ($datos_padre["sexo"]=="V") {
                        $sufijo_padre="o";
                        } else {
                        $sufijo_padre="a";
                        }

                                if ($parentesco!=1) {
                                        $errores="- Esta información está restringida a los padres del alumno -";
                                        } 
                }

        }
       
if ($errores!="") {
    ?>
    <div align="center">
    <br><br><big><?=$errores?></big><br><br>
    <input type="button" onclick="window.location.href='/webcam/menu.php'" value="Volver">
        </div>
    <?php 
    } else {
    ?>
    <div align="center">
    <?php 
    // Mostramos bienvenida
    print "¡Hola! ";
          $hora=strftime("%H");
          if (($hora>=22 || $hora<5))
                  {
                    print "Buenas noches, ";  }

                  if ($hora>=15 && $hora<22)
                  {
                    print "Buenas tardes, ";  }

                  if ($hora>=5 && $hora<15)
                  {
                    print "Buenos dias, ";  }

                  print "<i>".strtoupper($datos_padre["Nombre"])."</i><br><br>";

    // Busca hijos del padre
    $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos, padres WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=padres.padre_de AND padres.id_usuario=$id_usuario AND alumnos.fecha_baja>='".date("Y-m-d",$fechalimite)."' AND alumnos.fecha_matricula<='".date("Y-m-d")."' ORDER BY alumnos.id_curso DESC") or die(db_error()."- No se encuentran los datos del alumno.");

    if (db_num_rows($busca_datos_alumno)!=0) {
    
        $contador_hijos=0;
        $array_hijos=array();
        while($datos_hijos=db_fetch_array($busca_datos_alumno)){
                $busca_datos_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$datos_hijos["id_curso"]);
                if (db_num_rows($busca_datos_cursos)!=0) {
                        $datos_curso_mostrado=db_fetch_array($busca_datos_cursos);
                        $nombre_curso_mostrado=$datos_curso_mostrado["nombre"];
                        } else {
                        $nombre_curso_mostrado="";
                        }
                        
                // Datos de la escuela del niño
                $busca_datos_escuela=db_query("SELECT * FROM centros WHERE id_centro=".$datos_hijos["id_centro"]);

                $datos_escuela=db_fetch_array($busca_datos_escuela);

                // Datos del aula del niño
                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_hijos["aula"]);

                $datos_aula=db_fetch_array($busca_datos_aula);
                $array_hijos[$contador_hijos][0]=$datos_hijos["padre_de"];
                $array_hijos[$contador_hijos][1]=$datos_hijos["id_alumno"];
                $array_hijos[$contador_hijos][2]=$datos_hijos["id_centro"];
                $array_hijos[$contador_hijos][3]=$datos_hijos["aula"];
                $array_hijos[$contador_hijos][4]=$datos_hijos["Nombre"];
                $contador_hijos++;
                }
        // Muestra calendario

$inicio=$_GET["dia"];
if (strlen($inicio)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=strtotime($inicio);
}


$busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d",$fecha_time_stamp)."' AND fecha_fin>='".date("Y-m-d",$fecha_time_stamp)."'");

if (db_num_rows($busca_curso_actual)!=0) {
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $id_curso_actual=$datos_curso_actual["id_curso"];
        } else {
        $id_curso_actual=0;
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

$dias_laborales_mes=0;
$dias_trabajados_mes=0;
$no_ficha_salida=0;
?>
<font color="#000000" size=3><b>Agenda <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?></b></font><br><br>
<font color="#000000" size=2>Para ver el informe de un día toca el botón con el nombre de tu hijo/a.<br/>
Si el nombre aparece de color <font color="#2e921d">verde</font>, el informe de ese día está disponible.
Si aparece de color <font color="#7b3535">rojo</font>, no está disponible.<br />
El informe de hoy no estará disponible hasta las 17:00 hrs.
Hasta esa hora el nombre aparecerá de color <font color="#0000ff">azul</font>.</font>
<table border=0 cellspacing="1" cellpadding="1" width="100%" height="100%">
<tr>
<td>
</td>
</tr>
<tr bgcolor="#8F8F8F">
<font color="#ffffff" size=3>
<?php 
for ($dia_s=1; $dia_s<=7; $dia_s=$dia_s+1)
{
?>
</font>
<td valign="top">
<font color="#ffffff" size=3>
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
<td align="right" bgcolor="<?=$fondo_celda_dia;?>" valign="top">
<font color="#000000" size=3>
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
    $no_asistencia=0;


?>
</font><div align="center"><?php 

for ($c_hijos=0;$c_hijos<count($array_hijos);$c_hijos++) {

// Comprueba asistencia
$mostrar_informe=false;
$mensaje_informe=$array_hijos[$c_hijos][4]." no asiste a la escuela el día ".date("d/m/Y",$inicio_time_stamp)." o el informe no está disponible para esta fecha.";
$color_boton="#7b3535";
        
$busca_registro_entrada=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$array_hijos[$c_hijos][0]." AND id_servicio=".$array_hijos[$c_hijos][3]." AND fecha='".date("Y/m/d",$inicio_time_stamp)."'") or die(db_error());
if (db_num_rows($busca_registro_entrada)!=0) {
        $mostrar_informe=true;
        $color_boton="#2e921d";
        }

// Comprueba si es hoy

$busca_mensualidad=db_query("SELECT * FROM alumnos WHERE id_alumno=".$array_hijos[$c_hijos][1]);

$hora_limite=17;

if (db_num_rows($busca_mensualidad)!=0) {
        $datos_del_cliente=db_fetch_array($busca_mensualidad);

        if ($datos_del_cliente["tipo_mensualidad"]==1) {
                // Media jornada
                if ($datos_del_cliente["comedor"]!=2) {
                        // Se queda a comer
                        $hora_limite=15;
                        } else  {
                        // No se queda a comer
                        $hora_limite=12;
                        }
                }

        }
        
        
if ((date("m/d/Y",$inicio_time_stamp)==date("m/d/Y")) and (date("G")<$hora_limite)) {
        $mostrar_informe=false;
        $mensaje_informe="El informe de hoy estará disponible a partir de las ".$hora_limite.":00 hrs.";
        $color_boton="#0000ff";
        }


if ($inicio_time_stamp<strtotime("2012-12-16")) {
        $mostrar_informe=false;
        $mensaje_informe="El informe no está disponible para esta fecha.";
        $color_boton="#7b3535";
        }

if ($mostrar_informe==true) {

// comprueba si leido

$busca_lectura=db_query("SELECT * FROM agenda_registro_lecturas WHERE dia_informe='".date("Y-m-d",$inicio_time_stamp)."' AND id_padre=$id_usuario AND id_alumno=".$array_hijos[$c_hijos][0]);

if (db_num_rows($busca_lectura)!=0) {
	$color_boton="#ff9600";
	}

?><input type="button" id="boton_<?=$inicio_time_stamp?>" value="<?=$array_hijos[$c_hijos][4]?>" onclick="window.open('/webcam/agenda/informe_dia_html.php?dia=<?=$inicio_time_stamp;?>&a=<?=$array_hijos[$c_hijos][0]?>','informe','toolbar=yes,resizable=yes,scrollbars=yes,width=1024,height=640');document.getElementById('boton_<?=$inicio_time_stamp?>').style.color='#ff9600'" style="font-size: 10px; color: <?=$color_boton?>"><br /><?php 
        } else {
?><input type="button" value="<?=$array_hijos[$c_hijos][4]?>" onclick="alert('<?=$mensaje_informe;?>');" style="font-size: 10px; color: <?=$color_boton?>"><br /><?php 
        }
}
?></div>
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
<br>
<a href="<?=$_SERVER["PHP_SELF"];?>?dia=<?=$ano_anterior?>/<?=$mes_anterior?>/1"><< Mes anterior | <a href="<?=$_SERVER["PHP_SELF"];?>?dia=<?=$ano_siguiente?>/<?=$mes_siguiente?>/1">Mes siguiente >></a>
</font>
<br>
<br>
<table>
<tr>
<td colspan=2 align="center" bgcolor="#efefef"><font size=2><b>Leyenda de colores</b></font></td>
</tr>
<tr>
<td bgcolor="#2e921d"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe disponible.</font></td>
</tr>
<tr>
<td bgcolor="#ff9600"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe disponible y ya leido anteriormente. *</font></td>
</tr>
<tr>
<td bgcolor="#0000ff"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe no disponible hasta la salida de la escuela.</font></td>
</tr>
<tr>
<td bgcolor="#7b3535"><font size=1>&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font size=2>Informe no disponible, ese día no ha asistido a la escuela.</font></td>
</tr>
</table>
<font size=2><br />* NOTA: Esta opción ha sido activada el día 19/2/2014, los informes de fechas anteriores puede que aparezcan como no leídos aunque ya se haya accedido a ellos.</font><br /><br />
        <?php 
        // Fin Calendario
        }
        
        ?>
        <a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a>
    <?php 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
