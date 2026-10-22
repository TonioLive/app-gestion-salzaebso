<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica_padre.php");

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

        ?>
                <table width=690 class="tabla_datos">
        <tr>
                <td class="tabla_celda_encabezado">Curso</td>
                <td class="tabla_celda_encabezado">Escuela</td>
                <td class="tabla_celda_encabezado">Aula</td>
                <td class="tabla_celda_encabezado">Nombre</td>
                <td>&nbsp;</td>
        </tr>
        <?php 
        $contador_hijos=0;
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
                
                
                // Busca ultima factura emitida
                
                
                ?>
        <tr>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$nombre_curso_mostrado;?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_escuela["nombre_centro"];?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_aula["nombre"];?></font></td>
                <td class="tabla_celda_datos" align="center"><font size=2><?=$datos_hijos["Nombre"]." ".$datos_hijos["Apellido1"]." ".$datos_hijos["Apellido2"]?></font></td>
                <td class="tabla_celda_datos" align="center" width=125><input type="button" value="Ver facturas" class="tabla_celda_encabezado" onclick="window.location.href='ver_facturas.php?id_curso=<?=$datos_hijos["id_curso"]?>&id_hijo=<?=$datos_hijos["id_alumno"]?>'"></td>
        </tr>
                <?php 
                $contador_hijos++;
                }
        ?>
        <tr>
        <td align="center" colspan="5"><a href="menu.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','/webcam/imagenes/salir_facturas_act.gif',1)"><img src="/webcam/imagenes/salir_facturas_des.gif" alt="Ver menú" name="Image3" border=0 id="Image3"></a></td>
        </tr>
        </table>
        <?php 
        }
    
    ?>

    <?php 
    }
    ?></div>
<?php  require("../configuracion/parte_inferior_padres.php"); ?>
