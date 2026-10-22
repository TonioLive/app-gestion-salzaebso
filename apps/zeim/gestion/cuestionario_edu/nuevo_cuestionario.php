<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$formulario_name="historial_educativo";

$id_cuestionario=$_GET["id_cuestionario"];

$tipo_cuestionario=$_GET["tipo_cuestionario"];

$nombre_anexo="Anexo I";
if (strlen($tipo_cuestionario)!=0) {
	$nombre_anexo="Anexo II";
	}

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
                window.location.href='/gestion/gabinete/seguimiento.php?id_alumno=<?=$id_usuario_seleccionado?>';
                }
        }
        

function guarda_cambios(accion) {

        document.<?=$formulario_name;?>.accion_despues.value=accion;
        
        var errores=0;

        var mensajes_error="";
        
        // Comprueba fecha inicio
        la_fecha=document.<?=$formulario_name;?>.fecha_mes.value+"/"+document.<?=$formulario_name;?>.fecha_dia.value+"/"+document.<?=$formulario_name;?>.fecha_ano.value
        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha introducida no es correcta.\u000A";errores=1; }
        
                if (errores==0) {
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }

        }

function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

                mes_presel=eval("document."+nombre_formulario+"."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("document."+nombre_formulario+"."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("document."+nombre_formulario+"."+nombre_campo_presel+"_ano.value;");

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

if (strlen($id_cuestionario)==0) {

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

        $intro_prueba=db_query("INSERT INTO historial_educativo_cuestionarios$tipo_cuestionario (id_usuario,id_curso,fecha_historial) values ('$id_usuario_seleccionado','$id_curso_actual','$fecha_realizacion_prueba')");
        $id_cuestionario=db_insert_id();
        $fecha_dia=date("j",strtotime($fecha_realizacion_prueba));
        $fecha_mes=date("n",strtotime($fecha_realizacion_prueba));
        $fecha_ano=date("Y",strtotime($fecha_realizacion_prueba));
        } 
        
        
        $busca_datos_prueba=db_query("SELECT * FROM historial_educativo_cuestionarios$tipo_cuestionario WHERE id_cuestionario=$id_cuestionario");
        $datos_prueba=db_fetch_array($busca_datos_prueba);
        $fecha_dia=date("j",strtotime($datos_prueba["fecha_historial"]));
        $fecha_mes=date("n",strtotime($datos_prueba["fecha_historial"]));
        $fecha_ano=date("Y",strtotime($datos_prueba["fecha_historial"]));
     
      
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name?>" action="guarda_cuestionario.php" method="POST">
<input type="hidden" value="<?=$id_usuario_seleccionado?>" name="id_alumno">
<input type="hidden" value="<?=$id_cuestionario?>" name="id_cuestionario">
<input type="hidden" value="0" name="accion_despues">
<input type="hidden" value="<?=$tipo_cuestionario?>" name="tipo_cuestionario">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Historial Educativo - Cuestionario<?=$nombre_anexo?></b></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td rowspan=7 align="center" valign="top" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
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
                        <tr>
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>Fecha de nacimiento:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_fechanac;?></font></td>
                        </tr>
                        </table>
                      
                          
                        
                        <br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <?php 
                        // Busca preguntas cuestionario
                        $busca_cuestiones=db_query("SELECT * FROM historial_educativo_cuestiones$tipo_cuestionario ORDER BY orden;");
                        
                        $seccion_actual="0";
                        while($cuestiones=db_fetch_array($busca_cuestiones)) {
                        
                        	if ($cuestiones["seccion"]!=$seccion_actual) {
                        		$seccion_actual=$cuestiones["seccion"];
                        		// Busca datos sección
                        		$nombre_seccion="";
                        		$texto_secion="";
                        		
                        		$busca_seccion=db_query("SELECT * FROM historial_educativo_secciones$tipo_cuestionario WHERE num_seccion='".$seccion_actual."'");
                        		if (db_num_rows($busca_seccion)!=0) {
                        			$datos_seccion=db_fetch_array($busca_seccion);
                        			$nombre_seccion=$datos_seccion["nombre_seccion"];
                        			$texto_secion=$datos_seccion["texto_secion"];
                        			if (strlen($texto_secion)!=0) {
                        				$texto_secion="<br />".$texto_secion;
                        				}
                        			}
                        if ($seccion_actual=="1.1") {
                        		?>
                        <tr valign="top">
                                <td colspan=6 align="left" bgcolor="#dedede"><font size=2 face="<?= $fuente_general; ?>"><b>1. Datos Familiares</b></font></td>
                        </tr>
                        		<?php 
                        	}		
                        		
                        		?>
                        <tr valign="top">
                                <td colspan=6 align="left" bgcolor="#dedede"><font size=2 face="<?= $fuente_general; ?>"><b><?=$seccion_actual?>. <?=$nombre_seccion?></b><?=$texto_secion;?></font></td>
                        </tr>
                        		<?php 
                        		}
                        		
                        	// Tipos de cuestiones	
							switch($cuestiones["tipo_campo"]) {
                            	case 0:
                                ?>
								<tr valign="top">
                                	<td align="right" bgcolor="#fff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$cuestiones["texto_form"]?></b></font></td>
                                	<td align="left" bgcolor="#fff"><input type="text" name="<?=$cuestiones["campo_db"]?>" value="<?=$datos_prueba[$cuestiones["campo_db"]]?>" size="<?=round($cuestiones["longitud_campo"]/3,0)?>"></td>
                                </tr>
								<?php 
								break;
								case 1:
                                ?>
								<tr valign="top">
                                	<td align="right" bgcolor="#fff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$cuestiones["texto_form"]?></b></font></td>
                                	<td align="left" bgcolor="#fff"><font size=2 face="<?= $fuente_general; ?>"><input type="radio" name="<?=$cuestiones["campo_db"]?>" value="1"<?php  if ($datos_prueba[$cuestiones["campo_db"]]==1) { print " checked"; } ?>>SÍ <input type="radio" name="<?=$cuestiones["campo_db"]?>" value="0"<?php  if ($datos_prueba[$cuestiones["campo_db"]]==0) { print " checked"; } ?>>NO</font></td>
                                </tr>
								<?php 
								break;
								case 2:
                                ?>
								<tr valign="top">
                                	<td align="right" bgcolor="#fff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$cuestiones["texto_form"]?></b></font></td>
                                	<td align="left" bgcolor="#fff"><select size=1 name="<?=$cuestiones["campo_db"]?>">
                                	<?php 
                                	for ($c_numero=0;$c_numero<=$cuestiones["numero_opciones"];$c_numero++) {
                                		?>
                                		<option value="<?=$c_numero?>"<?php  if ($datos_prueba[$cuestiones["campo_db"]]==$c_numero) { print " selected"; } ?>><?=$c_numero?></option>
                                		<?php 
                                		}
                                	?>
                                	</select></td>
                                </tr>
								<?php 
								break;
								case 3:
                                ?>
								<tr valign="top">
                                	<td align="right" bgcolor="#fff"><font size=2 face="<?= $fuente_general; ?>"><b><?=$cuestiones["texto_form"]?></b></font></td>
                                	<td align="left" bgcolor="#fff"><textarea style="font-size: 12px; font-family: Arial" name="<?=$cuestiones["campo_db"]?>" cols=80 rows=3><?=$datos_prueba[$cuestiones["campo_db"]]?></textarea></td>
                                </tr>
								<?php 
								break;
								}
                        
                        	}
                        
                        ?>
                        </table>
                        <div id="botonera" align="center">
                        <br><input type="button" onclick="guarda_cambios(1);" value="Guardar cambios y salir" style="font-size:10px"> <input type="button" onclick="salir()" value="Salir" style="font-size:10px">
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