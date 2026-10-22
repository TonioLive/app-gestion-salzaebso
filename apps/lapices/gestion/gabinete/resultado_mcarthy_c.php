<?php
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("funciones_resultado.php");

$formulario_name="mcarthy";

$id_prueba=$_GET["id_prueba"];

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["id_alumno"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["id_alumno"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }

?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function imprimir() {
        document.getElementById('botonera').style.display='none';
        self.print();
        document.getElementById('botonera').style.display='block';
        }
function editar(codigo) {
                window.open('/alumnos/nuevo_padre1.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
function mostrar(id_capa) {
        document.getElementById('items_a_'+id_capa).style.display='block';
        document.getElementById('items_d_'+id_capa).style.display='none';
        }

function ocultar(id_capa) {
        document.getElementById('items_a_'+id_capa).style.display='none';
        document.getElementById('items_d_'+id_capa).style.display='block';
        }
</script>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 

$id_curso_actual=$_GET["id_curso_alumno"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                $fecha_nacimiento_alumno=$datos_usuario["fecha_nacimiento"];

                // Datos del paso
                $usuario_sexo=$datos_usuario["sexo"];
                
if ($datos_usuario["nonato"]==1) {
        $usuario_fechanac="No ha nacido";
        } else {
                
                if (strlen($datos_usuario["fecha_nacimiento"])==0) {
                        $usuario_fechanac="";
                        } else {
                        $nacimiento=explode( '-', $datos_usuario["fecha_nacimiento"]);
                        $edad=date("Y")-$nacimiento[0];
                        $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                        $hoy=strtotime(date("Y/m/d"));
                        
                        if (($cumple-$hoy)>0) {
                                $edad=$edad-1;
                                $edad_en_dias=date("t")-(date("j",$cumple)-date("j",$hoy));
                                } else {
                                $edad_en_dias=(date("j",$cumple)-date("j",$hoy));
                                }
                                
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_usuario["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                
                $edad=$edad_d_l." meses";
                        $usuario_fechanac=$edad." (Fecha nacimiento: ".date("d/m/Y",strtotime($datos_usuario["fecha_nacimiento"])).")";
                        }
                }
                $usuario_direccion=$datos_usuario["Direccion"];
                $usuario_cp=$datos_usuario["cp"];
                $usuario_poblacion=$datos_usuario["Poblacion"];
                $usuario_provincia=$datos_usuario["Provincia"];
                $usuario_telefono1=$datos_usuario["telefono1"];
                $usuario_telefono2=$datos_usuario["telefono2"];
                $usuario_movil=$datos_usuario["movil"];
                $usuario_email=$datos_usuario["email"];
                $alergias=$datos_usuario["alergias"];
                $vacunaciones=$datos_usuario["vacunaciones"];
                $observaciones=$datos_usuario["observaciones"];
                $contacto1=$datos_usuario["contacto1"];
                $telefono_contacto1=$datos_usuario["telefono_contacto1"];
                $contacto2=$datos_usuario["contacto2"];
                $telefono_contacto2=$datos_usuario["telefono_contacto2"];
                $contacto_medico=$datos_usuario["contacto_medico"];
                $telefono_medico=$datos_usuario["telefono_medico"];
                $usuario_nacionalidad=$datos_usuario["nacionalidad"];
                }

        // Busca el curso actual
        if ($id_curso_actual==0) {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

        }

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }

if (strlen($id_prueba)!=0) {
        $busca_datos_prueba=db_query("SELECT * FROM pruebas_mcarthy WHERE id_prueba=$id_prueba");
        $datos_prueba=db_fetch_array($busca_datos_prueba);
        $observaciones_prueba=$datos_prueba["observaciones_prueba"];
        if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=app_url_text_utf8($observaciones_prueba);
                }
        $edad_real_prueba=$datos_prueba["er"];
        $fecha_dia=date("j",strtotime($datos_prueba["fecha"]));
        $fecha_mes=date("n",strtotime($datos_prueba["fecha"]));
        $fecha_ano=date("Y",strtotime($datos_prueba["fecha"]));
        $hora_hora=date("H",strtotime($datos_prueba["hora"]));
        $hora_min=date("i",strtotime($datos_prueba["hora"]));
        
                        $nacimiento=explode( '-', $fecha_nacimiento_alumno);
                        $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                        $hoy=strtotime($datos_prueba["fecha"]);

                        if ((date("j",$cumple)-date("j",$hoy))>0) {
                                $edad_en_dias_prueba=date("t",strtotime($datos_prueba["fecha"]))-(date("j",$cumple)-date("j",$hoy));
                                } else {
                                $edad_en_dias_prueba=date("j",$hoy)-date("j",$cumple);
                                }
                                
        $edad_real_prueba_gci=round($edad_real_prueba+($edad_en_dias_prueba/date("t",strtotime($datos_prueba["fecha"]))),1);
        
        }
      
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="mcarthy" action="" method="POST">
<input type="hidden" value="<?=$id_usuario_seleccionado?>" name="id_alumno">
<input type="hidden" value="<?=$id_prueba?>" name="id_prueba">
<input type="hidden" value="0" name="accion_despues">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Prueba Mc Carthy</b></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="50%">
                        <tr valign="top">
                                <td rowspan=7 align="center" valign="top" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos personales</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_nombre;?> <?=$usuario_apellido1;?> <?=$usuario_apellido2;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"><?php  if ($usuario_sexo=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha realización prueba</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_prueba["fecha"]))?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Hora realización prueba</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=date("H:i",strtotime($datos_prueba["hora"]))?></font></td>
                        </tr>
                        <tr>
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>ER:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$edad_real_prueba?> meses y <?=$edad_en_dias_prueba;?> días</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>Observaciones:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$observaciones_prueba;?></font></td>
                        </tr>
                        </table>
                        <br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%" id="items_d_<?=$edad_actual?>" style="display: <?=$estado_capa_d?>">
                                <tr>
                                        <td width=30% colspan=2 align="left" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                        <td colspan=5 width=70% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>Puntuaciones directas ponderadas</b></font></td>
                                </tr>
                                <tr>
                                        <td width=30% colspan=2 align="left" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                        <td width=14% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">V</font></td>
                                        <td width=14% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">PM</font></td>
                                        <td width=14% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">N</font></td>
                                        <td width=14% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Mem</font></td>
                                        <td width=14% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Mot</font></td>
                                </tr>
                        </table>
                        <?php 

                        $puntuacion_parte_act=array();
                        $multiplicador_parte_act=array();
                        
                        $total_d=0;
                        $total_i=0;
                        $total_a=0;
                        
                        $total_v=0;
                        $total_pm=0;
                        $total_n=0;
                        $total_mem=0;
                        $total_mot=0;

                        $busca_items_mcarhty=db_query("SELECT * FROM pruebas_mcarhty_ejercicios ORDER BY id_ejercicio");
                        $edad_actual=0;
                        $contador_array=0;
                        $marcar_edad=false;
                        $colores_filas=array("#ffffff","#efefef");

                        while($ejercicios_mcarhty=db_fetch_array($busca_items_mcarhty)) {
                        $edad_actual=$ejercicios_mcarhty["id_ejercicio"];
                        
                        $ejercicio_actual_p=0;
                        
                        $estado_capa_d="block";
                        $estado_capa_a="none";
                        ?>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%" id="items_a_<?=$edad_actual?>" style="display: <?=$estado_capa_a?>">
                                <tr valign="top">
                                        <td colspan=7 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><a href='javascript: ocultar("<?=$edad_actual?>")'><img src="/imagen/menu/menos.gif" border=0> <b><?=$ejercicios_mcarhty["id_ejercicio"];?> <?=$ejercicios_mcarhty["nombre_ejercicio"];?></b></font></a></td>
                                </tr>
                                <?php 
                                
                                $puntuacion_parte_act[1]=0;
                                $multiplicador_parte_act[1]=1;
                                $puntuacion_parte_act[2]=0;
                                $multiplicador_parte_act[2]=1;
                                $puntuacion_parte_act[3]=0;
                                $multiplicador_parte_act[3]=1;
                                
                                $parte_actual=0;
                                $contador_pregunta=1;
                                $busca_items_mcarthy=db_query("SELECT * FROM pruebas_mcarthy_items WHERE num_ejercicio=$edad_actual ORDER BY parte,orden_ejercicio");
                                while($items_mcarthy=db_fetch_array($busca_items_mcarthy)) {

                                $item_actual_p=0;

                                $id_item_actual=$items_mcarthy["id_item"];

                                // Cuenta partes
                                $busca_partes=db_query("SELECT * FROM pruebas_mcarthy_items WHERE num_ejercicio=$edad_actual GROUP BY parte");
                                if (db_num_rows($busca_partes)>1) {

                                        if ($parte_actual!=$items_mcarthy["parte"]) {
                                                $contador_pregunta=1;
                                                $parte_actual=$items_mcarthy["parte"];
                                                $mostrar_encabezado=true;
                                                ?>
                                                <tr valign="top">
                                                        <td colspan=7 align="left" bgcolor="#efefef"><font size=3 face="<?= $fuente_general; ?>"><b><img src="/imagen/menu/nada.gif" border=0> Parte <?=$parte_actual;?></b></font></td>
                                                </tr>
                                                <?php 
                                                }

                                         } else {
                                         if ($parte_actual==0) {
                                                 $parte_actual=$items_mcarthy["parte"];
                                                 $mostrar_encabezado=true;
                                                 }
                                         }

                                         if ($mostrar_encabezado==true) {
                                                $mostrar_encabezado=false;

                                                ?>
                                                <tr>
                                                        <td colspan=2 align="left" bgcolor="#efefef">&nbsp;</font></td>
                                                <?php 

                                        switch($ejercicios_mcarhty["tipo_ejercicio_p".($parte_actual)]) {
                                                case 1:
                                                // 1. Puntuación I1 I2 por item, cuenta la mejor. Observaciones
                                                ?>
                                                        <td width=28% align="left" bgcolor="#efefef" colspan=2><font size=2><b>Intento 1</b></font></td>
                                                        <td width=42% align="left" bgcolor="#efefef" colspan=3><font size=2><b>Intento 2</b></font></td>
                                                <?php 
                                                break;
                                                case 2:
                                                // 2. Puntuación por item
                                                ?>
                                                        <td width=70% align="left" bgcolor="#efefef" colspan=5><font size=2><b>Puntuación</b></font></td>
                                                <?php 
                                                break;
                                                case 3:
                                                // 3. Marcar si o no, cada uno suma 1 puntom
                                                ?>
                                                        <td width=28% align="left" bgcolor="#efefef" colspan=2><font size=2><b>Acierto</b></font></td>
                                                        <td width=42% align="left" bgcolor="#efefef" colspan=3><font size=2><b>Fallo</b></font></td>
                                                <?php 
                                                break;
                                                case 4:
                                                // 4. Marcar si o no, cada uno suma 1 puntom
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Acierto</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Fallo</b></font></font></td>
                                                        <td width=42% align="left" bgcolor="#efefef" colspan=3><font size=2><b>Observaciones</b></font></font></td>
                                                <?php 
                                                break;
                                                case 5:
                                                // 5. Marcar nº de respuestas y puntuación
                                                ?>
                                                        <td width=42% align="left" bgcolor="#efefef" colspan=3><font size=2><b>Respuestas</b></font></td>
                                                        <td width=28% align="left" bgcolor="#efefef" colspan=2><font size=2><b>Puntuación</b></font></td>
                                                <?php 
                                                break;
                                                case 6:
                                                // 6. Puntuación en I1, I2 e I3, cuenta la mejor
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Intento 1</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Intento 2</b></font></td>
                                                        <td width=42% align="left" bgcolor="#efefef" colspan=3><font size=2><b>Intento 3</b></font></td>
                                                <?php 
                                                break;
                                                case 7:
                                                // 7. Indicar puntuación y escribir respuesta
                                                ?>
                                                        <td width=42% align="left" bgcolor="#efefef" colspan=3><font size=2><b>Respuesta</b></font></td>
                                                        <td width=28% align="left" bgcolor="#efefef" colspan=2><font size=2><b>Puntuación</b></font></td>
                                                <?php 
                                                break;
                                                case 8:
                                                // 8. Botes I1 e I2, Puntuación en I1, I2, Lateralidad
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Nº Botes I. 1</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Puntuación 1</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Nº Botes I. 2</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Puntuación 2</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Mano preferida</b></font></td>
                                                <?php 
                                                break;
                                                case 9:
                                                // 9. Indicar intento, Mano preferida
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Int. Ambos lados</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Int. Lado preferido</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Lado preferido</b></font></td>
                                                        <td width=28% align="left" bgcolor="#efefef" colspan=2><font size=2><b>Nº Otro lado</b></font></td>
                                                <?php 
                                                break;
                                                case 15:
                                                // 15. Indicar intento, Mano preferida
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Int. Lado preferido</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Lado preferido</b></font></td>
                                                        <td width=42% align="left" bgcolor="#efefef" colspan=3><font size=2><b>Nº Otro lado</b></font></td>
                                                <?php 
                                                break;
                                                case 16:
                                                // 16. Puntuación, Ojo preferido
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Puntuación</b></font></td>
                                                        <td width=56% align="left" bgcolor="#efefef" colspan=4><font size=2><b>Ojo</b></font></td>
                                                <?php 
                                                break;
                                                case 17:
                                                // 17. Puntuación, Mano preferida
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Puntuación</b></font></td>
                                                        <td width=56% align="left" bgcolor="#efefef" colspan=4><font size=2><b>Mano preferida</b></font></td>
                                                <?php 
                                                break;
                                                case 18:
                                                // 18. Mano preferida
                                                ?>
                                                        <td width=70% align="left" bgcolor="#efefef" colspan=5><font size=2><b>&nbsp;</b></font></td>
                                                <?php 
                                                break;
                                                case 19:
                                                // 19. Observaciones
                                                ?>
                                                        <td width=70% align="left" bgcolor="#efefef" colspan=5><font size=2><b>&nbsp;</b></font></td>
                                                <?php 
                                                break;
                                                }
                                                ?>
                                                </tr>
                                                <?php 
                                                }

                                        ?>
                                                <tr valign="top">
                                                        <td width=5% align="left" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$contador_pregunta;?></font></td>
                                                        <td width=25% align="left" bgcolor="#ffffff"><font size=2 face="<?= $fuente_general; ?>"><?=$items_mcarthy["item"];?></font></td>
                                        <?php 

                                        // Busca respuestas

                                        $busca_respuestas_item_actual=db_query("SELECT * FROM pruebas_mcarthy_respuestas WHERE id_prueba=$id_prueba AND id_item=$id_item_actual");

                                        if (db_num_rows($busca_respuestas_item_actual)!=0) {
                                                $datos_respuestas_item_actual=db_fetch_array($busca_respuestas_item_actual);
                                                $item_actual_dato1=$datos_respuestas_item_actual["dato1"];
                                                $item_actual_dato2=$datos_respuestas_item_actual["dato2"];
                                                $item_actual_dato3=$datos_respuestas_item_actual["dato3"];
                                                $item_actual_dato4=$datos_respuestas_item_actual["dato4"];
                                                $item_actual_dato5=$datos_respuestas_item_actual["dato5"];
                                                $item_actual_respuesta=$datos_respuestas_item_actual["respuesta"];
                                                $item_actual_verdaderofalso=$datos_respuestas_item_actual["verdaderofalso"];
                                                $item_actual_lateralidad=$datos_respuestas_item_actual["lateralidad"];
                                                $item_actual_observaciones=$datos_respuestas_item_actual["observaciones"];
                                                } else {
                                                $item_actual_dato1="";
                                                $item_actual_dato2="";
                                                $item_actual_dato3="";
                                                $item_actual_dato4="";
                                                $item_actual_dato5="";
                                                $item_actual_respuesta="";
                                                $item_actual_verdaderofalso="";
                                                $item_actual_lateralidad="";
                                                $item_actual_observaciones="";
                                                }

                                       // Vamos a ello
                                        switch($ejercicios_mcarhty["tipo_ejercicio_p".($parte_actual)]) {
                                                case 1:
                                                // 1. Puntuación I1 I2 por item, cuenta la mejor. Observaciones
                                                
                                                $p1=$item_actual_dato1;
                                                $p2=$item_actual_dato2;
                                                
                                                if ($p1>$p2) {
                                                        $item_actual_p=$p1;
                                                        } else {
                                                        $item_actual_p=$p2;
                                                        }
                                                
                                                ?>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" readonly size=2></font></td>
                                                <?php 
                                                break;
                                                case 2:
                                                // 2. Puntuación por item
                                                
                                                $item_actual_p=$item_actual_dato1;
                                                
                                                ?>
                                                        <td width=70% align="left" bgcolor="#ffffff" colspan=5><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                <?php 
                                                break;
                                                case 3:
                                                // 3. Marcar si o no, cada uno suma 1 puntom
                                                
                                                $item_actual_p=$item_actual_verdaderofalso;
                                                
                                                ?>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2>Si <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="1"<?php  if ($item_actual_verdaderofalso==1) { print " checked"; } ?>></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3>No <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="0"<?php  if ($item_actual_verdaderofalso==0) { print " checked"; } ?>></font></td>
                                                <?php 
                                                break;
                                                case 4:
                                                // 4. Marcar si o no, cada uno suma 1 puntom
                                                
                                                $item_actual_p=$item_actual_verdaderofalso;
                                                
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff">Si <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="1"<?php  if ($item_actual_verdaderofalso==1) { print " checked"; } ?>></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff">No <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="0"<?php  if ($item_actual_verdaderofalso==0) { print " checked"; } ?>></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="respuesta_<?=$id_item_actual?>" value="<?=$item_actual_respuesta?>"  type="text" readonly size=40></font></td>
                                                <?php 
                                                break;
                                                case 5:
                                                // 5. Marcar nº de respuestas y puntuación
                                                
                                                $item_actual_p=$item_actual_dato2;
                                                
                                                ?>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" readonly size=2></font></td>
                                                <?php 
                                                break;
                                                case 6:
                                                // 6. Puntuación en I1, I2 e I3, cuenta la mejor
                                                
                                                $array_intentos=array($item_actual_dato1,$item_actual_dato2,$item_actual_dato3);
                                                
                                                sort($array_intentos);
                                                
                                                $item_actual_p=$array_intentos[2];
                                                
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" readonly size=2></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_3" value="<?=$item_actual_dato3?>" type="text" readonly size=2></font></td>
                                                <?php 
                                                break;
                                                case 7:
                                                // 7. Indicar puntuación y escribir respuesta
                                                
                                                $item_actual_p=$item_actual_dato1;
                                                
                                                ?>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="respuesta_<?=$id_item_actual?>" value="<?=$item_actual_respuesta?>" type="text" readonly size=40></font></td>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                <?php 
                                                break;
                                                case 8:
                                                // 8. Botes I1 e I2, Puntuación en I1, I2, Lateralidad
                                                
                                                $p1=$item_actual_dato2;
                                                $p2=$item_actual_dato4;

                                                if ($p1>$p2) {
                                                        $item_actual_p=$p1;
                                                        } else {
                                                        $item_actual_p=$p2;
                                                        }
                                                
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_3" value="<?=$item_actual_dato3?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_4" value="<?=$item_actual_dato4?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        <option value="A"<?php  if ($item_actual_lateralidad=="A") { print " selected"; } ?>>A</option>
                                                        </select></font></td>
                                                <?php 
                                                break;
                                                case 9:
                                                // 9. Indicar intento, Mano preferida
                                                
                                                $item_actual_p=$item_actual_dato1+$item_actual_dato2+$item_actual_dato3;
                                                
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><?php  if ($id_item_actual==93) { ?><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        </select><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_3" value="<?=$item_actual_dato3?>" type="text" readonly size=2></font></td>
                                                <?php 
                                                break;
                                                case 15:
                                                // 15. Indicar intento, Mano preferida
                                                
                                                $item_actual_p=$item_actual_dato1+$item_actual_dato2;
                                                
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><?php  if ($id_item_actual==95) { ?><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        </select><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" readonly size=2></font></td>
                                                <?php 
                                                break;
                                                case 16:
                                                // 16. Puntuación, Mano preferida
                                                
                                                $item_actual_p=$item_actual_dato1;
                                                
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=56% align="left" bgcolor="#ffffff" colspan=4><?php  if ($id_item_actual==100) { ?><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        </select><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                                                <?php 
                                                break;
                                                case 17:
                                                // 17. Puntuación, Mano preferida
                                                
                                                $item_actual_p=$item_actual_dato1;
                                                
                                                if ($item_actual_lateralidad=="D") { $lat_1213D++; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_1213D++; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_1213D++; $total_a++;  }
                                                
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" readonly size=2></font></td>
                                                        <td width=56% align="left" bgcolor="#ffffff" colspan=4><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        <option value="A"<?php  if ($item_actual_lateralidad=="A") { print " selected"; } ?>>A</option>
                                                        </select></font></td>
                                                <?php 
                                                break;
                                                case 18:
                                                // 18.  Mano preferida
                                                ?>
                                                        <td width=70% align="left" bgcolor="#ffffff" colspan=5><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        <option value="A"<?php  if ($item_actual_lateralidad=="A") { print " selected"; } ?>>A</option>
                                                        </select></font></td>
                                                <?php 
                                                break;
                                                case 19:
                                                // 19.  Observaciones
                                                ?>
                                                        <td width=70% align="left" bgcolor="#ffffff" colspan=5><textarea name="observaciones_<?=$id_item_actual?>" cols=100 rows=5 style="font-size: 11px; font-family: Arial"><?=$item_actual_observaciones?></textarea>
                                                        </font></td>
                                                <?php 
                                                break;
                                                }
                                        ?>
                                                </tr>
                                        <?php 

                                        $multiplicador_actual=$ejercicios_mcarhty["multiplicador".($parte_actual)];
                                        
                                        // $item_actual_p=$item_actual_p*$multiplicador_actual;
                                        // $ejercicio_actual_p=$ejercicio_actual_p+$item_actual_p;
                                        
                                        $puntuacion_parte_act[$parte_actual]=$puntuacion_parte_act[$parte_actual]+$item_actual_p;
                                        $multiplicador_parte_act[$parte_actual]=$multiplicador_actual;

                                        // Lateralidad
                                        // Test 10 parte 1
                                        if ($id_item_actual==91) {
                                                if ($item_actual_lateralidad=="D") { $lat_10_1D=1; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_10_1I=1; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_10_1A=1; $total_a++;  }
                                                }
                                        // Test 10 parte II Elemento 2
                                        if ($id_item_actual==93) {
                                                if ($item_actual_lateralidad=="D") { $lat_10_21D=1; $total_d++; }
                                                if ($item_actual_lateralidad=="I") { $lat_10_21I=1; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_10_21A=1; $total_a++;  }
                                                }
                                        // Test 10 parte III Elemento 1
                                        if ($id_item_actual==95) {
                                                if ($item_actual_lateralidad=="D") { $lat_10_22D=1; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_10_22I=1; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_10_22A=1; $total_a++;  }
                                                }
                                        // Test 13
                                        if ($id_item_actual==162) {
                                                if ($item_actual_lateralidad=="D") { $lat_1213D++; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_1213I++; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_1213A++; $total_a++;  }
                                                }
                                        // Test 11
                                        if ($id_item_actual==100) {
                                                if ($item_actual_lateralidad=="D") { $lat_11_4D++; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_11_4I++; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_11_4A++; $total_a++;  }
                                                }
                                        
                                        $contador_pregunta++;
                                        }
                                        
                                        $ejercicio_actual_p=ceil($puntuacion_parte_act[1]*$multiplicador_parte_act[1])+ceil($puntuacion_parte_act[2]*$multiplicador_parte_act[2])+ceil($puntuacion_parte_act[3]*$multiplicador_parte_act[3]);
                                        
                                        
                                        $item_actual_v=$ejercicios_mcarhty["v"]*$ejercicio_actual_p;
                                        $item_actual_pm=$ejercicios_mcarhty["pm"]*$ejercicio_actual_p;
                                        $item_actual_n=$ejercicios_mcarhty["n"]*$ejercicio_actual_p;
                                        $item_actual_mem=$ejercicios_mcarhty["mem"]*$ejercicio_actual_p;
                                        $item_actual_mot=$ejercicios_mcarhty["mot"]*$ejercicio_actual_p;
                                        
                                        $total_v=$total_v+$item_actual_v;
                                        $total_pm=$total_pm+$item_actual_pm;
                                        $total_n=$total_n+$item_actual_n;
                                        $total_mem=$total_mem+$item_actual_mem;
                                        $total_mot=$total_mot+$item_actual_mot;
                                        
                                        // Busca GCI
                                        $pdc=$total_v+$total_pm+$total_n;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $gci=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc ORDER BY desde_mt DESC");
                                                $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                $edad_antes=$datos_gci_antes["hasta_mt"];
                                                $gci_antes=$datos_gci_antes["gci"];
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc ORDER BY desde_mt");
                                                $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                $edad_despues=$datos_gci_antes["desde_mt"];
                                                $gci_despues=$datos_gci_despues["gci"];

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if (($edad_real_prueba_gci-$edad_antes)<1) {
                                                        $gci=$gci_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $gci=$gci_despues;
                                                                } else {
                                                                $gci=ceil(($gci_antes+$gci_despues)/2);
                                                                }
                                                        
                                                        }
                                                }

                                        // Busca V
                                        $pdc_item=$total_v;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_v WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                $item_unico=$datos_gci["id"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_v WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                $edad_antes=$datos_gci_antes["hasta_mt"];
                                                $item_antes=$datos_gci_antes["gci"];
                                                $id_item_antes=$datos_gci_despues["id"];
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_v WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                $edad_despues=$datos_gci_antes["desde_mt"];
                                                $item_despues=$datos_gci_despues["gci"];
                                                $id_item_despues=$datos_gci_despues["id"];

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_vI=$item;
                                        // Busca PM
                                        $pdc_item=$total_pm;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                $edad_antes=$datos_gci_antes["hasta_mt"];
                                                $item_antes=$datos_gci_antes["gci"];
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                $edad_despues=$datos_gci_antes["desde_mt"];
                                                $item_despues=$datos_gci_despues["gci"];

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_pmI=$item;
                                        // Busca N
                                        $pdc_item=$total_n;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                $edad_antes=$datos_gci_antes["hasta_mt"];
                                                $item_antes=$datos_gci_antes["gci"];
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                $edad_despues=$datos_gci_antes["desde_mt"];
                                                $item_despues=$datos_gci_despues["gci"];

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_nI=$item;
                                        // Busca MEM
                                        $pdc_item=$total_mem;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                $edad_antes=$datos_gci_antes["hasta_mt"];
                                                $item_antes=$datos_gci_antes["gci"];
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                $edad_despues=$datos_gci_antes["desde_mt"];
                                                $item_despues=$datos_gci_despues["gci"];

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_memI=$item;

                                        // $muestra="Edad: ".$edad_real_prueba_gci." Antes: ".$item_antes."(".$edad_antes.") Despues: ".$item_despues."(".$edad_despues.") Media:".($item_antes+$item_despues)/2;
                                        
                                        // Busca MOT
                                        $pdc_item=$total_mot;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                $edad_antes=$datos_gci_antes["hasta_mt"];
                                                $item_antes=$datos_gci_antes["gci"];
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                $edad_despues=$datos_gci_antes["desde_mt"];
                                                $item_despues=$datos_gci_despues["gci"];

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_motI=$item;
                                        
                                        ?>
                        </table>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%" id="items_d_<?=$edad_actual?>" style="display: <?=$estado_capa_d?>">
                                <tr>
                                        <td width=30% colspan=2 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><a href='javascript: mostrar("<?=$edad_actual?>")'><img src="/imagen/menu/mas.gif" border=0> <b><?=$ejercicios_mcarhty["id_ejercicio"];?> <?=$ejercicios_mcarhty["nombre_ejercicio"];?></b></font></a></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$item_actual_v?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$item_actual_pm?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$item_actual_n?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$item_actual_mem?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$item_actual_mot?></font></td>
                                </tr>
                        </table>
                        <?php 
                        }
                        ?>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%" id="items_d_<?=$edad_actual?>" style="display: <?=$estado_capa_d?>">
                                <tr>
                                        <td width=30% colspan=2 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>Puntuación directa compuesta</b></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_v?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_pm?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_n?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_mem?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_mot?></font></td>
                                </tr>
                                <tr>
                                        <td width=30% colspan=2 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>Puntuación General Cognitiva (V+PM+N)</b></font></td>
                                        <td width=42% colspan=3 align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_v+$total_pm+$total_n?></font></td>
                                        <td width=14% colspan=2 align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                </tr>
                                <tr>
                                        <td width=30% colspan=2 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>Puntuación típica indirecta</b></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_vI?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_pmI?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_nI?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_memI?></font></td>
                                        <td width=14% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_motI?></font></td>
                                </tr>
                                <tr>
                                        <td width=30% colspan=2 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>G.C.I.</b></font></td>
                                        <td width=42% colspan=3 align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$gci?></font></td>
                                        <td width=14% colspan=2 align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                </tr>
                        </table>
                        </table>
                        <br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%" id="items_d_<?=$edad_actual?>" style="display: <?=$estado_capa_d?>">
                                <tr>
                                        <td width=100% colspan=5 align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>Resumen de lateralidad</b></font></td>
                                </tr>
                                <tr>
                                        <td width=70% colspan=2 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        <td width=10% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>D</b></font></td>
                                        <td width=10% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>I</b></font></td>
                                        <td width=10% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>A</b></font></td>
                                        
                                </tr>
                                <tr>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Test 10, Parte I</font></td>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Botar la pelota</font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_1D?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_1I?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_1A?></font></td>
                                </tr>
                                <tr>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Test 10, Parte II, Elemento 2</font></td>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Atrapar la bolsa</font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_21D?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_21I?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_21A?></font></td>
                                </tr>
                                <tr>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Test 10, Parte III, Elemento 1</font></td>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Tiro al blanco</font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_22D?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_22I?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_10_22A?></font></td>
                                </tr>
                                <tr>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Test 12 y 13, Totales</font></td>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Dibuja</font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_1213D?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_1213I?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_1213A?></font></td>
                                </tr>
                                <tr>
                                        <td width=35% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>Totales</b></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_d?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_i?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$total_a?></font></td>
                                </tr>
                                <?php 
                                $mano_dominante="";
                                if (($total_d>0) and ($total_i==0)) {
                                        $mano_dominante="Dominancia establecida (derecho)";
                                        }
                                if (($total_i>0) and ($total_d==0)) {
                                        $mano_dominante="Dominancia establecida (izquierdo)";
                                        }
                                if (($total_i>0) and ($total_d>0)) {
                                        $mano_dominante="Dominancia no establecida";
                                        }
                                if (($total_i==0) and ($total_d==0)) {
                                        $mano_dominante="No puntuable";
                                        }
                                ?>
                                <tr>
                                        <td width=35% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Mano dominante</font></td>
                                        <td width=30% align="center" bgcolor="#ffffff" colspan=3><font size=3 face="<?= $fuente_general; ?>"><?=$mano_dominante?></font></td>
                                </tr>
                                <tr>
                                        <td width=70% colspan=2 align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>Ojo</b></font></td>
                                        <td width=10% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>D</b></font></td>
                                        <td width=10% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>I</b></font></td>
                                        <td width=10% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><b>A</b></font></td>
                                </tr>
                                <tr>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Test 11, Elemento 4</font></td>
                                        <td width=35% align="center" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>">Mirar a través del tubo</font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_11_4D?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_11_4I?></font></td>
                                        <td width=10% align="center" bgcolor="#ffffff"><font size=3 face="<?= $fuente_general; ?>"><?=$lat_11_4A?></font></td>
                                </tr>
                        </table>
                        
                        <div id="botonera" align="center">
                        <br><input type="button" onclick="window.location.href='seguimiento.php?id_alumno=<?=$id_alumno?>'" value="Salir" style="font-size:10px">
                        </div><br><br>
                        <?=conversion_mccarthy($id_prueba);?>
                </td>
        </tr>
</form>
</table>
<?=$muestra?>
</td>
</tr>
</table>
</center></div>
</div>
</body>
</html>
