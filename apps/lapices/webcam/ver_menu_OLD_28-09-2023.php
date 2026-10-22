<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require("../configuracion/config.php");
require("../configuracion/autentifica_padre.php");
require("../menu/funciones_alergias.php");

$arrStr = explode("/", $_SERVER['SCRIPT_NAME'] );
$arrStr = array_reverse($arrStr ); 
$nombre_pagina_actual=str_replace(".php","",$arrStr[0]);

// Comprueba si el archivo existe
if (file_exists("/var/www/webcam/imagenes/cabecera_".$nombre_pagina_actual.".png")==false) {
	$nombre_pagina_actual="menu";
	}

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
$id_padre=$id_usuario;
$id_usuario=$datos_alumno["id_usuario"];

if ($datos_alumno["sexo"]=="V") {
                        $sufijo_hijo="o";
                        } else {
                        $sufijo_hijo="a";
                        }

// Comprueba si es su padre
$busca_si_es_padre=db_query("SELECT * FROM padres WHERE id_usuario='$id_padre' AND padre_de=$id_usuario");
if (db_num_rows($busca_si_es_padre)!=0) {

$nombre_de_pila_alumno=$datos_alumno["Nombre"];
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
        
$inicio=$datos_curso_actual["fecha_inicio"];

if (strtotime($inicio)<strtotime(date("Y-m-d"))) {
        $inicio=date("Y-m-d");
        }

if (strtotime($inicio)>strtotime($datos_curso_actual["fecha_fin"])) {
        $inicio=$datos_curso_actual["fecha_fin"];
        }

$dia_sel_inicio=$_GET["dia"];
if (strlen($dia_sel_inicio)!=0) {
        $inicio=$dia_sel_inicio;
        }


if (strlen($inicio)!=0) {
        $anio_fecha=date("Y",strtotime($inicio));
        } else {
        $anio_fecha=date("Y");
        }

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
$mes_ini_curso=date("n",strtotime($datos_curso_actual["fecha_inicio"]));
$anio_ini_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));

// Empezamos
$array_campos_platos=array("comida1","comida2","comida3","comida4","comida5","postre1","merienda1","merienda2");







$alimentos_ya_marcados=array();
$alergias_ya_marcadas=array();

$platos_no_introducidos=array();
$platos_alaergias=array();
$hay_observaciones=false;
?>
<table width=690 class="tabla_datos">
<tr>
<td class="tabla_celda_datos" align="center">
<font size=3 color="#cc424f"><b>Comedor de <?=$nombre_completo_alumno?><br>Curso <?=$datos_curso_actual["nombre"]?></b><br></font>
<!--<font size=2 color="#cc424f">Puede acceder a la información nutricional haciendo clic con el ratón sobre la fecha que se desea ver.</font><br>-->
<br>
<?php 
$mesnombre=$nombre_mes[$mes];
$anio_actual=date("Y",$fecha_time_stamp);
$dias_trabajados=0;
$mes_s=1;
$dias_trabajados_mes=0;
?>
<font color="#cc424f" size=3 face="<?=$fuente_general?>"><b><?php  echo $mesnombre; ?>, <?=$anio_actual; ?></b></font><br>
<a href="<?=$_SERVER["PHP_SELF"]?>?id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_alumno;?>&dia=<?=$ano_anterior?>-<?=$mes_anterior?>-1"><< Mes anterior</a> | <a href="<?=$_SERVER["PHP_SELF"]?>?id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_alumno;?>&dia=<?=$ano_siguiente?>-<?=$mes_siguiente?>-1">Mes siguiente >></a>
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
<font face="<?=$fuente_general?>" color="#ffffff" size=2>
<b>
<?php    echo $dia_sem_largo[$dia_s]; ?>
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
                        $estado_boton=" checked";
                        $fondo_celda_dia="#F17327";
                        } else {
                        $estado_boton="";
                        $dias_trabajados_mes++;
                        $dias_trabajados++;
                        $fondo_celda_dia="#EFEFEF";
                        }
                }
                
        // Calcula menu apropiado para el niño
        
                        // Edad actual del niño en años con decimales
                        $edad_alumno=date("Y",$fecha_time_stamp)-date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
                        if (date("m",$fecha_time_stamp)<date("m",strtotime($datos_alumno["fecha_nacimiento"]))) {
                                $edad_alumno=$edad_alumno-1;
                                }
                        $edad_alumno_anos=$edad_alumno;
                        // Decimales
                        $mes_edad_alumno=date("n",$fecha_time_stamp)-date("n",strtotime($datos_alumno["fecha_nacimiento"]));

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
                $menu_actual_en_ficha=$datos_alumno["menu_actual"];
                $coletilla_error="";
                $tipo_seleccion_menu="";
                if ($menu_actual_en_ficha==-1) {
                                if ($datos_aula["id_menu_predefinido"]!=0) {
                                        $tipo_seleccion_menu="Menú asignado en aula";
                                        $id_menu_seleccionado=$datos_aula["id_menu_predefinido"];
                                        } else {
                                        $menu_actual_en_ficha=0;
                                        $coletilla_error=" (El aula no tiene menú asignado)";
                                        }
                                }
                if ($menu_actual_en_ficha>0) {
                        $tipo_seleccion_menu="Menú asignado en ficha".$coletilla_error;
                        $id_menu_seleccionado=$menu_actual_en_ficha;
                        } else {
                        if ($menu_actual_en_ficha==0) {
                        $tipo_seleccion_menu="Automático por edad".$coletilla_error;

                        // Comprueba si es de regimen
                        $regimen=$datos_alumno["comedor"];
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
                                if (($id_menu_seleccionado==0) and ($array_errores[$datos_alumno["id_alumno"]]!=1)) {
                                        if ($edad_alumno<$minimo_minimorum) {
                                                $motivo_error="La edad del niño no llega a la edad mínima (".round(($minimo_minimorum*12),0)." meses) admitida en los menús.<small><br><b>Propuestas:</b><br>1. Introducir un menú apropiado para la edad del niño.<br>2. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
                                                } else {
                                                $motivo_error="El cía ".date("d/m/Y",$fecha_time_stamp)." no se encuentra un menú apropiado para la edad del niño.<small><br><b>Propuestas:</b><br>1. Editar la inscripción del niño y asignarle manualmente alguno de los menús disponibles.</small>";
                                                }

                                                $listado_errores=$listado_errores."<tr><td><font size=1>No hay menú</font></td><td><font size=1>".$motivo_error."</font></td></tr>";
                                                $array_errores[$datos_alumno["id_alumno"]]=1;
                                        }
                               }
                        }
        $id_menu=$id_menu_seleccionado;
        
        //  Busca nombre menú
        if ($id_menu!=0) {
                $busca_nombre_menu=db_query("SELECT * FROM menu_menus WHERE id_menu=$id_menu");
                if (db_num_rows($busca_nombre_menu)!=0) {
                        $datos_nombre_menu=db_fetch_array($busca_nombre_menu);
                        $nombre_menu_seleccionado=$datos_nombre_menu["nombre_menu"];
                        if (strlen($nombre_menu_seleccionado)!=0) {
                                $nombre_menu_seleccionado=app_url_text_utf8($nombre_menu_seleccionado);
                                }
                        } else {
                        $nombre_menu_seleccionado="";
                        }
                } else {
                $nombre_menu_seleccionado="";
                }
        // echo $edad_alumno." ".$id_curso_actual." ".$curso_seleccionado." ".date("Y-m-d",$inicio_time_stamp)." ".$nombre_menu_seleccionado."<br>";
        // Desactivamos link temporalmente
        // onclick="info_dia($inicio_time_stamp,$id_curso_actual,$id_menu);"

        ?>
        <td align="center" valign="top" bgcolor="<?=$fondo_celda_dia;?>">
        <font face="<?=$fuente_general?>" color="#000000" size=2>
        <?php 
        // Desactivamos link temporalmente
        // print "<a href='javascript:info_dia($inicio_time_stamp,$id_curso_actual,$id_menu)' title='Ver deatlles del menú: ".$nombre_menu_seleccionado."'>";
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
        // Desactivamos link temporalmente
        //        print "</a>";

$busca_platos_dia=db_query("SELECT * FROM menu_calendario WHERE id_menu=$id_menu AND fecha='".date("Y-m-d",$inicio_time_stamp)."'");
if (db_num_rows($busca_platos_dia)!=0) {

                $datos_platos_dia=db_fetch_array($busca_platos_dia);

                                        for ($c_platos=0;$c_platos<count($array_campos_platos);$c_platos++) {

                                        $codigo_plato=$datos_platos_dia[$array_campos_platos[$c_platos]];
                                        if ($codigo_plato!=0) {

                                                $consulta_datos_plato=db_query("SELECT * FROM menu_platos WHERE id_plato=$codigo_plato") or die(db_error());

                                                if (db_num_rows($consulta_datos_plato)!=0) {
                                                        $datos_plato=db_fetch_array($consulta_datos_plato);


                                                        // Comprueba platos
                                                        $busca_ingredientes=db_query("SELECT * FROM menu_ingredientes, menu_alimentos WHERE menu_ingredientes.id_alimento=menu_alimentos.id_alimento AND menu_ingredientes.id_plato=$codigo_plato") or die(db_error());

                                                        while($ingredientes=db_fetch_array($busca_ingredientes)) {
                                                                        for ($c=0;$c<count($array_alimentos);$c++) {
                                                                                if (($ingredientes[$array_alimentos[$c]]==1) and ($datos_alumno[$array_alimentos[$c]]==0)) {
                                                                                        $hay_comida=false;
                                                                                        if ($alimentos_ya_marcados[$codigo_plato]!=1) {
                                                                                                $platos_no_introducidos[$c]=$platos_no_introducidos[$c].$datos_plato["nombre_plato"]." (Ingrediente: ".app_url_text_utf8($ingredientes["nombre_alimento"])."),";
                                                                                                $alimentos_ya_marcados[$codigo_plato]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }

                                                                        for ($c=0;$c<count($array_alergias);$c++) {
                                                                                if (($ingredientes[$array_alergias[$c]]==1) and ($datos_alumno[$array_alergias[$c]]==1)) {
                                                                                        $nombre_alergia=$nombres_alergias[$c];
                                                                                        $hay_comida=false;
                                                                                        if ($alergias_ya_marcadas[$codigo_plato]!=1) {
                                                                                                $platos_alaergias[$c]=$platos_alaergias[$c].$datos_plato["nombre_plato"]." (Ingrediente: ".app_url_text_utf8($ingredientes["nombre_alimento"])."),";
                                                                                                $alergias_ya_marcadas[$codigo_plato]=1;
                                                                                                }
                                                                                        $hay_observaciones=true;
                                                                                        }
                                                                                }
                                                                }

                                                        }

                                                }
                                                }
                // Edita el nuevo día
                $actualizar=false;
                $id_dia_menu=$datos_platos_dia["id_dia"];
                $comida1=$datos_platos_dia["comida1"];
                $comida2=$datos_platos_dia["comida2"];
                $comida3=$datos_platos_dia["comida3"];
                $comida4=$datos_platos_dia["comida4"];
                $comida5=$datos_platos_dia["comida5"];
                $postre1=$datos_platos_dia["postre1"];
                $merienda1=$datos_platos_dia["merienda1"];
                $merienda2=$datos_platos_dia["merienda2"];

        $color_alerta="#FF0000";

        echo "<table width=100% border=0 cellpadding=0 cellspacing=1>";
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 1:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida2 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 2:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida3 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 3:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida4 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 4:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$comida5 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Comida 5:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$postre1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Postre:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$merienda1 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Merienda 1:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        $busca_comida=db_query("SELECT * FROM menu_platos WHERE id_plato=$merienda2 ORDER BY nombre_plato");
        if (db_num_rows($busca_comida)!=0) {
                $datos_comida=db_fetch_array($busca_comida);

                // Calcula contenido calorico plato
                $contenidoCalorico = calculaCalorias($datos_comida['id_plato']);

                $listaAlergenosPlato = '';

                if (strlen($contenidoCalorico['alergenos_lista'])!=0) {
                $listaAlergenosPlato = " <a title='".$contenidoCalorico['alergenos_nombre']."'><span style='color: #ff0000;'>Al. (".$contenidoCalorico['alergenos_lista'].")</span></a>";
                }

                $color_fuente_linea="#000000";
                if (($alergias_ya_marcadas[$datos_comida['id_plato']]==1) or ($alimentos_ya_marcados[$datos_comida['id_plato']]==1)) {
                        $color_fuente_linea=$color_alerta;
                        }
                echo "<tr><td bgcolor='#efefef'><font size=1><b>Merienda 2:</b></font></td></tr><tr><td bgcolor='#ffffff'><font size=1 color='$color_fuente_linea'>".app_url_text_utf8($datos_comida["nombre_plato"]).$listaAlergenosPlato."</font></td></tr>";
                }
        echo "</table>";
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

$anio_actual_1=date("Y",$inicio_time_stamp);
$mes_actual_1=date("n",$inicio_time_stamp);
?>
</table>
<div>
<h3>Alergenos:</h3> 
<?php
foreach ($arrayKeysAlergias as $keyAlg => $numAlg) {
        echo("<img src='https://catering.escuelas-infantiles.com/img/iconos/".$keyAlg.".png' border='0' title='".$nombres_alergias[$numAlg-1]."'> ".$numAlg.". ".ucfirst($nombres_alergias[$numAlg-1])." ");
        if ($numAlg==6) {
                echo("<br />");
        }
}
?>
</div>
<?php 
if ($hay_observaciones==true) {
?><table border=1 cellpadding=2 cellspacing=0 bordercolor="#cc424f" style="border-collapse: collapse">
<tr valign="top">
        <td colspan=2 bgcolor="#efefef"><font size=2><b>Observaciones:</b></td>
</tr>
<?php 
$encabezado=false;
for ($c=0;$c<count($array_alimentos);$c++) {
        if (strlen($platos_no_introducidos[$c])!=0) {
                $platos_no_introducidos[$c]=app_url_text_utf8(substr($platos_no_introducidos[$c],0,strlen($platos_no_introducidos[$c])-1));
                if ($encabezado==false) {
                        ?>
                <tr valign="top">
                        <td colspan=2 bgcolor="#efefef"><font size=2><b>Alimentos todavía no introducidos.</b><br>
                        Debido a la presencia de algún ingrediente no introducido todavía en la alimentación de <?=ucwords(strtolower($nombre_de_pila_alumno));?>, los siguientes platos (marcados en rojo en el calendario) serán sustituidos por otro alternativo que sea adecuado para su dieta.</font></td>
                </tr>
                        <?php 
                        $encabezado=true;
                        }
        
                ?>
                <tr valign="top">
                        <td><font size=2><b><?=$nombres_alimentos[$c];?></b></font></td>
                        <td><font size=2><?=$platos_no_introducidos[$c];?></font></td>
                </tr>
                <?php 
                }
        }
        
$encabezado=false;
for ($c=0;$c<count($array_alergias);$c++) {
        if (strlen($platos_alaergias[$c])!=0) {
                $platos_alaergias[$c]=app_url_text_utf8(substr($platos_alaergias[$c],0,strlen($platos_alaergias[$c])-1));
                if ($encabezado==false) {
                        ?>
                <tr valign="top">
                        <td colspan=2 bgcolor="#efefef"><font size=2><b>Alergias</b><br>
                        Debido a la presencia de algún ingrediente al que <?=ucwords(strtolower($nombre_de_pila_alumno));?> tiene alergia, los siguientes platos (marcados en rojo en el calendario) serán sustituidos por otro alternativo que sea adecuado para su dieta.</font></td>
                </tr>
                        <?php 
                        $encabezado=true;
                        }

                ?>
                <tr valign="top">
                        <td><font size=2><b><?=$nombres_alergias[$c];?></b></font></td>
                        <td><font size=2><?=$platos_alaergias[$c];?></font></td>
                </tr>
                <?php 
                }
        }
?>
</table>
<?php 
}
?>
<table width=100%>
<tr>
<td width=50% align="center"><a href="comedor.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_act.gif',1)"><img src="/webcam/imagenes/salir_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a></td>
<td width=50% align="center"><a target="_new" href="imprimir_menu.php?id_curso=<?=$id_curso_actual;?>&id_hijo=<?=$id_alumno;?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','/webcam/imagenes/imprimir_act.gif',1)"><img src="/webcam/imagenes/imprimir_des.gif" alt="Ver menú" name="Image4" border=0 id="Image4"></a></td>
</tr>
</table>
</td>
</tr>
</table>
<script language="Javascript">
function info_dia(dia,curso,id_menu) {
        window.open("info_menu.php?dia="+dia+"&curso="+curso+"&id_menu="+id_menu,"info_menu","toolbar=no,width=600,height=500");
        }
</script>
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
