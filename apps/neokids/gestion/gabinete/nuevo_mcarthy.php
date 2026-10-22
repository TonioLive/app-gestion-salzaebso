<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

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
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
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

function salir() {
        if (confirm("Vas a salir sin guardar los cambios. ¿Estás segura/o?")) {
                window.location.href='seguimiento.php?id_alumno=<?=$id_usuario_seleccionado?>';
                }
        }
        

function guarda_cambios(accion) {

        self.document.<?=$formulario_name;?>.accion_despues.value=accion;
        
        var errores=0;

        var mensajes_error="";
        
        // Comprueba fecha inicio
        la_fecha=self.document.<?=$formulario_name;?>.fecha_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_ano.value
        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha introducida no es correcta.\u000A";errores=1; }
        
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }

        }

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

                mes_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino='+nombre_formulario+'&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
        
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
$estado_correcion_brunet="disabled ";
if ($zona_corregir_pruebas==1) {
        $estado_correcion_brunet="";
        }
$id_curso_actual=$_GET["id_curso_alumno"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);
                
                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

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
                                }
                                
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_usuario["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                
                $dia_l=date("j",strtotime($fecha_l));
                if (intval(date("j"))<$dia_l) {
                        $edad_d_l=$edad_d_l-1;
                        }
                if ($edad_d_l<0) {
                        $edad_d_l=0;
                        }
                
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
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
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

if (strlen($id_prueba)==0) {

        $fecha_dia=$_GET["fecha_dia"];
        $fecha_mes=$_GET["fecha_mes"];
        $fecha_ano=$_GET["fecha_ano"];
        $fecha_realizacion_prueba=$fecha_ano."-".$fecha_mes."-".$fecha_dia;

        if (strlen($datos_usuario["fecha_nacimiento"])!=0) {
                        $nacimiento_nuevo=explode( '-', $datos_usuario["fecha_nacimiento"]);
                        $edad_nuevo=date("Y")-$nacimiento[0];
                        $cumple_nuevo=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y",strtotime($fecha_realizacion_prueba)));
                        $hoy_nuevo=strtotime(date("Y/m/d",strtotime($fecha_realizacion_prueba)));
                        if (($cumple_nuevo-$hoy_nuevo)>0) {
                                $edad_nuevo=$edad_nuevo-1;
                                }

                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l_nuevo=$datos_usuario["fecha_nacimiento"];
                $edad_a_l_nuevo=(date("Y",strtotime($fecha_realizacion_prueba))-date("Y",strtotime($fecha_l_nuevo)));

                // 1.1. Cacula edad con decimales
                $mes_d_l_nuevo=date("m",strtotime($fecha_l_nuevo));
                $mes_d_n_nuevo=date("m",strtotime($fecha_realizacion_prueba));
                $dif_meses_nuevo=$mes_d_n_nuevo-$mes_d_l_nuevo;

                $edad_d_l_nuevo=($edad_a_l_nuevo*12)+$dif_meses_nuevo;

                $dia_l_nuevo=date("j",strtotime($fecha_l_nuevo));
                if (intval($fecha_dia)<$dia_l_nuevo) {
                        $edad_d_l_nuevo=$edad_d_l_nuevo-1;
                        }
                if ($edad_d_l_nuevo<0) {
                        $edad_d_l_nuevo=0;
                        }

                }

        $intro_prueba=db_query_params('INSERT INTO pruebas_mcarthy (id_alumno,er,fecha,hora) values (?,?,?,?)', 'ssss', array((string)($id_usuario_seleccionado), (string)($edad_d_l_nuevo), (string)($fecha_realizacion_prueba), (string)(date("H:i:s"))));
        $id_prueba=db_insert_id();
        $edad_real_prueba=$edad_d_l_nuevo;
        $fecha_dia=date("j",strtotime($fecha_realizacion_prueba));
        $fecha_mes=date("n",strtotime($fecha_realizacion_prueba));
        $fecha_ano=date("Y",strtotime($fecha_realizacion_prueba));
        $hora_hora=date("H");
        $hora_min=date("i");
        $observaciones_prueba="";
        } else {
        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba));
        $datos_prueba=db_fetch_array($busca_datos_prueba);
        
        $correccion_p=$datos_prueba["correccion_p"];
        $correccion_c=$datos_prueba["correccion_c"];
        $correccion_l=$datos_prueba["correccion_l"];
        $correccion_em=$datos_prueba["correccion_em"];
        
        $observaciones_prueba=$datos_prueba["observaciones_prueba"];
        if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=urldecode($observaciones_prueba);
                }
        $edad_real_prueba=$datos_prueba["er"];
        $fecha_dia=date("j",strtotime($datos_prueba["fecha"]));
        $fecha_mes=date("n",strtotime($datos_prueba["fecha"]));
        $fecha_ano=date("Y",strtotime($datos_prueba["fecha"]));
        $hora_hora=date("H",strtotime($datos_prueba["hora"]));
        $hora_min=date("i",strtotime($datos_prueba["hora"]));
        }
      
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name?>" action="guarda_mcarthy.php" method="POST">
<input type="hidden" value="<?=$id_usuario_seleccionado?>" name="id_alumno">
<input type="hidden" value="<?=$id_prueba?>" name="id_prueba">
<input type="hidden" value="0" name="accion_despues">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Prueba Mc Carhty</b></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td rowspan=8 align="center" valign="top" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
                                <td colspan=2 align="center" bgcolor="#dedede"><font size=2 face="<?= $fuente_general; ?>"><b>Datos personales</b></font></td>
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
                                <td><select name="fecha_dia" class="defaut" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if ($c_dia==$fecha_dia) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_mes" class="defaut" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if ($c_mes==$fecha_mes) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_ano" class="defaut" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=strftime("%Y")+15; $c_ano>=strftime("%Y")-15; $c_ano=$c_ano-1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if ($c_ano==$fecha_ano) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <font size=1><a href="javascript:selecciona_fecha('fecha','<?=$formulario_name;?>');" title="Selecciona la fecha en el calendario"><img src="/imagen/calendario.gif" border=0 align="absmiddle"></a></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Hora realización prueba</b></font></td>
                                <td><font size=1><select name="hora_hora" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=23;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==$hora_hora) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select>:<select name="hora_min" size=1 style="font-size: 10px"><?php 
  for ($c_horas=0;$c_horas<=59;$c_horas++) {
  $c_horas_texto=$c_horas;
        if (strlen($c_horas_texto)==1) {
                $c_horas_texto="0".$c_horas_texto;
                }
        ?>
        <option value="<?=$c_horas_texto;?>"<?php  if ($c_horas_texto==$hora_min) { print " selected"; } ?>><?=$c_horas_texto;?></option>
        <?php 
        }
  ?></select></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>ER/Edad actual:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$edad_real_prueba?> meses/<?=$usuario_fechanac;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>Corrección conversión Brunet:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><table border=1 bordercolor='#000000' style='border-collapse: collapse; font-face: Arial; font-size: 10px; text-align: right'>
                        <tr>
                        <td align="center"><b>P</b></td>
                        <td align="center"><b>C</b></td>
                        <td align="center"><b>L</b></td>
                        <td align="center"><b>EM</b></td>
                        </tr>
                        <tr>
                        <td align="center"><input type="text" <?=$estado_correcion_brunet?>value="<?=$correccion_p?>" name="correccion_p" size=2 style="text-align= right"></td>
                        <td align="center"><input type="text" <?=$estado_correcion_brunet?>value="<?=$correccion_c?>" name="correccion_c" size=2 style="text-align= right"></td>
                        <td align="center"><input type="text" <?=$estado_correcion_brunet?>value="<?=$correccion_l?>" name="correccion_l" size=2 style="text-align= right"></td>
                        <td align="center"><input type="text" <?=$estado_correcion_brunet?>value="<?=$correccion_em?>" name="correccion_em" size=2 style="text-align= right"></td>
                        </tr>
                        </table></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>Observaciones:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="observaciones_prueba" rows=6  cols="110" style="font-size: 12px; font-family: Arial"><?=$observaciones_prueba;?></textarea></font></td>
                        </tr>
                        </table>
                        <br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=5 align="center" bgcolor="#dedede"><font size=2 face="<?= $fuente_general; ?>"><b>Ejercicios</b></font></td>
                        </tr>
                        </table>
                        <?php 

                        $busca_items_mcarhty=db_query("SELECT * FROM pruebas_mcarhty_ejercicios ORDER BY id_ejercicio");
                        $edad_actual=0;
                        $contador_array=0;
                        $marcar_edad=false;
                        $colores_filas=array("#ffffff","#efefef");

                        while($ejercicios_mcarhty=db_fetch_array($busca_items_mcarhty)) {
                        $edad_actual=$ejercicios_mcarhty["id_ejercicio"];
                        $estado_capa_d="block";
                        $estado_capa_a="none";
                        ?>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%" id="items_d_<?=$edad_actual?>" style="display: <?=$estado_capa_d?>">
                                <tr>
                                        <td colspan=7 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><a href='javascript: mostrar("<?=$edad_actual?>")'><img src="/imagen/menu/mas.gif" border=0> <b><?=$ejercicios_mcarhty["id_ejercicio"];?> <?=$ejercicios_mcarhty["nombre_ejercicio"];?></b></font></a></td>
                                </tr>
                        </table>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%" id="items_a_<?=$edad_actual?>" style="display: <?=$estado_capa_a?>">
                                <tr valign="top">
                                        <td colspan=7 align="left" bgcolor="#dedede"><font size=3 face="<?= $fuente_general; ?>"><a href='javascript: ocultar("<?=$edad_actual?>")'><img src="/imagen/menu/menos.gif" border=0> <b><?=$ejercicios_mcarhty["id_ejercicio"];?> <?=$ejercicios_mcarhty["nombre_ejercicio"];?></b></font></a></td>
                                </tr>
                                <?php 
                                $parte_actual=0;
                                $contador_pregunta=1;
                                $busca_items_mcarthy=db_query("SELECT * FROM pruebas_mcarthy_items WHERE num_ejercicio=$edad_actual ORDER BY parte,orden_ejercicio");
                                while($items_mcarthy=db_fetch_array($busca_items_mcarthy)) {

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
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Nº Ambos lados</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Nº Lado preferido</b></font></td>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Lado preferido</b></font></td>
                                                        <td width=28% align="left" bgcolor="#efefef" colspan=2><font size=2><b>Nº Otro lado</b></font></td>
                                                <?php 
                                                break;
                                                case 15:
                                                // 15. Indicar intento, Mano preferida
                                                ?>
                                                        <td width=14% align="left" bgcolor="#efefef"><font size=2><b>Nº Lado preferido</b></font></td>
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
                                        
                                        $busca_respuestas_item_actual=db_query_params('SELECT * FROM pruebas_mcarthy_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $id_item_actual));
                                        
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
                                                $item_actual_dato4="";
                                                $item_actual_respuesta="";
                                                $item_actual_verdaderofalso="";
                                                $item_actual_lateralidad="";
                                                $item_actual_observaciones="";
                                                }
                                        
                                       // Vamos a ello
                                        switch($ejercicios_mcarhty["tipo_ejercicio_p".($parte_actual)]) {
                                                case 1:
                                                // 1. Puntuación I1 I2 por item, cuenta la mejor. Observaciones
                                                ?>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 2:
                                                // 2. Puntuación por item
                                                ?>
                                                        <td width=70% align="left" bgcolor="#ffffff" colspan=5><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 3:
                                                // 3. Marcar si o no, cada uno suma 1 puntom
                                                ?>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2>Si <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="1"<?php  if ($item_actual_verdaderofalso==1) { print " checked"; } ?>></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3>No <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="0"<?php  if ($item_actual_verdaderofalso==0) { print " checked"; } ?>></font></td>
                                                <?php 
                                                break;
                                                case 4:
                                                // 4. Marcar si o no, cada uno suma 1 puntom
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff">Si <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="1"<?php  if ($item_actual_verdaderofalso==1) { print " checked"; } ?>></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff">No <input name="verdaderofalso_<?=$id_item_actual?>" type="radio" value="0"<?php  if ($item_actual_verdaderofalso==0) { print " checked"; } ?>></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="respuesta_<?=$id_item_actual?>" value="<?=$item_actual_respuesta?>"  type="text" size=40></font></td>
                                                <?php 
                                                break;
                                                case 5:
                                                // 5. Marcar nº de respuestas y puntuación
                                                ?>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 6:
                                                // 6. Puntuación en I1, I2 e I3, cuenta la mejor
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" size=2></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_3" value="<?=$item_actual_dato3?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 7:
                                                // 7. Indicar puntuación y escribir respuesta
                                                ?>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="respuesta_<?=$id_item_actual?>" value="<?=$item_actual_respuesta?>" type="text" size=40></font></td>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 7:
                                                // 7. Indicar puntuación y escribir respuesta
                                                ?>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="respuesta_<?=$id_item_actual?>" value="<?=$item_actual_respuesta?>" type="text" size=40></font></td>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 8:
                                                // 8. Botes I1 e I2, Puntuación en I1, I2, Lateralidad
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_3" value="<?=$item_actual_dato3?>" type="text" size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_4" value="<?=$item_actual_dato4?>" type="text" size=2></font></td>
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
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><?php  if ($id_item_actual==93) { ?><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        </select><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                                                        <td width=28% align="left" bgcolor="#ffffff" colspan=2><input name="dato_<?=$id_item_actual?>_3" value="<?=$item_actual_dato3?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 15:
                                                // 15. Indicar intento, Mano preferida
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                        <td width=14% align="left" bgcolor="#ffffff"><?php  if ($id_item_actual==95) { ?><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        </select><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                                                        <td width=42% align="left" bgcolor="#ffffff" colspan=3><input name="dato_<?=$id_item_actual?>_2" value="<?=$item_actual_dato2?>" type="text" size=2></font></td>
                                                <?php 
                                                break;
                                                case 16:
                                                // 16. Puntuación, Mano preferida
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
                                                        <td width=56% align="left" bgcolor="#ffffff" colspan=4><?php  if ($id_item_actual==100) { ?><select name="lateralidad_<?=$id_item_actual?>">
                                                        <option value=""></option>
                                                        <option value="D"<?php  if ($item_actual_lateralidad=="D") { print " selected"; } ?>>D</option>
                                                        <option value="I"<?php  if ($item_actual_lateralidad=="I") { print " selected"; } ?>>I</option>
                                                        </select><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                                                <?php 
                                                break;
                                                case 17:
                                                // 17. Puntuación, Mano preferida
                                                ?>
                                                        <td width=14% align="left" bgcolor="#ffffff"><input name="dato_<?=$id_item_actual?>_1" value="<?=$item_actual_dato1?>" type="text" size=2></font></td>
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
                                        $contador_pregunta++;
                                        }
                                        ?>
                        </table>
                        <?php 
                        }
                        ?>
                        </table>
                        <div id="botonera" align="center">
                        <br><input type="button" onclick="guarda_cambios(1);" value="Guardar cambios y salir" style="font-size:10px"> <input type="button" onclick="guarda_cambios(0);" value="Guardar cambios y calcular resultado" style="font-size:10px"> <input type="button" onclick="salir()" value="Salir" style="font-size:10px">
                        </div><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
</center></div>
</div>
</body>
</html>
