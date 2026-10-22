<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$inicial=$_GET["i"];
if (strlen($inicial)==0) {
        $inicial="A";
        }
$base_Datos="";
if ($_GET["base_datos"]) {
	$base_Datos=$_GET["base_datos"].".";
	}
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

function imprimir() {
        document.getElementById('botonera').style.display='none';
        self.print();
        document.getElementById('botonera').style.display='block';
        }
function editar(codigo) {
                window.open('nuevo_padre1.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
function ampliar(foto) {
        window.open(foto,'ampliacion','width=600,height=500');
        }
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center>
<?php 
if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["u"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["u"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }
        
$id_curso_actual=$_GET["id_curso_alumno"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query("SELECT * FROM ".$base_Datos."usuarios WHERE id_usuario=".$id_usuario_seleccionado);

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
        if ($edad<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_usuario["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad=$edad_d_l." meses";
                } else {
                $edad=$edad." años";
                }
                        $usuario_fechanac=date("d/m/Y",strtotime($datos_usuario["fecha_nacimiento"])).", Edad actual: ".$edad;;
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
        $busca_curso_actual=db_query("SELECT * FROM ".$base_Datos."cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM ".$base_Datos."cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM ".$base_Datos."cursos_academicos WHERE id_curso=$id_curso_actual");
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
      
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Ficha Alumno</b></font><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td rowspan=6 align="center" valign="middle" width="100"><a href="javascript:ampliar('/fotos_usuarios/<?=$nombre_foto?>')"><img src="/fotos_usuarios/<?=$nombre_foto?>" border=0 width="100"></a></td>
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos personales</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_nombre;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Apellidos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_apellido1;?> <?=$usuario_apellido2;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"><?php  if ($usuario_sexo=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=160 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nacionalidad</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"><?=$usuario_nacionalidad;?></font></td>
                        </tr>
                        <tr>
                                <td width=160 align="right"><font face="<?= $fuente_general; ?>" size="1"><b>Fecha Nacimiento</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_fechanac;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=3 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Información sobre la inscripción</b></font></td>
                        </tr>
                        <?php 
                        // Busca inscripción en el curso actual

                        /*if ($id_curso_actual!=0) {
                                $id_curso_actual=0;
                                }*/
                                
                                $busca_inscripcion_alumno=db_query("SELECT * FROM ".$base_Datos."alumnos WHERE id_usuario=$id_usuario_seleccionado AND id_curso=$id_curso_actual");
                                if (db_num_rows($busca_inscripcion_alumno)==0) {
                                        // Busca la inscripcion en el ultimo curso en el que está inscrito
                                        $busca_inscripcion_alumno=db_query("SELECT * FROM ".$base_Datos."alumnos WHERE id_usuario=$id_usuario_seleccionado ORDER BY id_curso DESC");
                                        }
                                if (db_num_rows($busca_inscripcion_alumno)!=0) {
                                
                                $datos_inscripcion=db_fetch_array($busca_inscripcion_alumno);

                                $consulta_nombre_centro=db_query("SELECT * FROM ".$base_Datos."centros WHERE id_centro=".$datos_inscripcion["id_centro"]) or die(db_error()."-2");
                                $datos_centro=db_fetch_array($consulta_nombre_centro);

                                // Busca datos del aula
                                $consulta_datos_aula=db_query("SELECT * FROM ".$base_Datos."centro_aulas WHERE id_aula=".$datos_inscripcion["aula"]);
                                $datos_aula=db_fetch_array($consulta_datos_aula);

                                // Tipos de comedor
                                $tipo_de_comedor[0]="Desconocido";
                                $tipo_de_comedor[1]="Si";
                                $tipo_de_comedor[2]="No";
                                $tipo_de_comedor[3]="Régimen";

                                // Tipos de menu
                                $tipo_de_menu[0]="Desconocido";
                                $tipo_de_menu[1]="Pequeños";
                                $tipo_de_menu[2]="Mayores";
                                $tipo_de_menu[3]="Colegial";
                                $tipo_de_menu[4]="Toma";
                                $tipo_de_menu[5]="Bebe";
                                
                                
                                if (($datos_inscripcion["fecha_inscripcion"]=="0000-00-00") or ($datos_inscripcion["fecha_inscripcion"]=="")) {
                                        $fecha_inscripcion="Desconocida";
                                        } else {
                                        $fecha_inscripcion=date("d/m/Y",strtotime($datos_inscripcion["fecha_inscripcion"]));
                                        }
                                if (($datos_inscripcion["fecha_matricula"]=="0000-00-00") or ($datos_inscripcion["fecha_matricula"]=="")) {
                                        $fecha_inicio_docencia="Desconocida";
                                        } else {
                                        $fecha_inicio_docencia=date("d/m/Y",strtotime($datos_inscripcion["fecha_matricula"]));
                                        }
                                if (($datos_inscripcion["fecha_baja"]=="0000-00-00") or ($datos_inscripcion["fecha_baja"]=="")) {
                                        $fecha_de_baja="Desconocida";
                                        } else {
                                        $fecha_de_baja=date("d/m/Y",strtotime($datos_inscripcion["fecha_baja"]));
                                        }
                                
                                ?>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela donde está inscrito</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_centro["nombre_centro"];?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_aula["nombre"];?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$tipo_de_comedor[$datos_inscripcion["comedor"]];?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de menú</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$tipo_de_menu[$datos_inscripcion["tipo_menu_comedor"]];?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha de inscripción</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_inscripcion;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha de inicio docencia</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_inicio_docencia;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha efectiva de baja</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$fecha_de_baja;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Motivo de la baja</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_inscripcion["motivo_baja"]?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de mensualidad</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?php if ($datos_inscripcion["tipo_mensualidad"]==1) { print "Media jornada"; } else { print "Jornada Completa"; } ?></font></td>
                        </tr>
                                <?php 
                                } else {
                                ?>
                                <tr valign="top">
                                        <td colspan=3 bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>El alumno no se encuentra inscrito actualmente en ningún curso.</b></font></td>
                                </tr>
                                <?php 
                                }
                        ?>
                    <tr valign="top">
                                <td colspan=3 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Información de contacto</b></font></td>
                        </tr>
                    <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Domicilio familiar</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_direccion;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Código postal</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_cp;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_poblacion;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Provincia</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?php 
                                $provincia=$usuario_provincia;
                                if (strlen($provincia)!=0) {
                                $busca_provincia=db_query("SELECT * FROM ".$base_Datos."provincias WHERE id_provincia=$provincia");
                                if (db_num_rows($busca_provincia)!=0) {
                                        $lista_provincias=db_fetch_array($busca_provincia);
                                        echo ($lista_provincias["provincia"]);
                                        }
                                }
                                ?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfonos</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_telefono1;?> <?=$usuario_telefono2;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Móvil</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_movil;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$usuario_email;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>1er. Contacto</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$contacto1;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono 1er. contacto</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$telefono_contacto1;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>2º Contacto</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$contacto2;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 width=260 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono 2º contacto</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$telefono_contacto2;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=3 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Padres y personas autorizadas</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=3 align="center" bgcolor="#efefef">
                                <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                                                <tr bgcolor="#dedede">
                                                <td align="left" width=35%><font size=1 face="<?= $fuente_general; ?>" color="#000000">Nombre</font></td>
                                                <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>" color="#000000">DNI</font></td>
                                                <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>" color="#000000">Poblacion</font></td>
                                                <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>" color="#000000">Relación de parentesco</font></td>
                                                <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000">&nbsp;</font></td>
                                                </tr>
                                        <?php 
                                        // COmprueba si ya hay relaciones introducidas
                                        $consulta_relaciones=db_query("SELECT * FROM ".$base_Datos."padres WHERE padre_de=$id_usuario_seleccionado");
                                        $numero_de_relaciones=db_num_rows($consulta_relaciones);
                                        // Busca relaciones
                                        $contador_relaciones=1;
                                        while($relacion=db_fetch_array($consulta_relaciones)) {
                                        // Busca datos padres
                                        $consulta_datos_padre=db_query("SELECT * FROM ".$base_Datos."usuarios WHERE id_usuario=".$relacion["id_usuario"]);

                                        if (db_num_rows($consulta_datos_padre)!=0) {
                                                $datos_padre=db_fetch_array($consulta_datos_padre);
                                                ?>
                                                        <tr bgcolor="#ffffff">
                                                        <td align="left" width=35%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$datos_padre["Apellido1"]?> <?=$datos_padre["Apellido2"]?>, <?=$datos_padre["Nombre"]?></font></td>
                                                        <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$datos_padre["DNI"]?></font></td>
                                                        <td align="center" width=20%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$datos_padre["Poblacion"]?></font></td>
                                                        <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?php 
                                                         if ($relacion["parentesco"]==1) { print "Padre/Madre"; }
                                                         if ($relacion["parentesco"]==2) { print "Abuelo/a"; }
                                                         if ($relacion["parentesco"]==3) { print "Tio/a"; }
                                                         if ($relacion["parentesco"]==4) { print "Autorizado/a"; }
                                                        ?></font></td>
                                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$relacion["id_usuario"];?>)"><img src="editar.gif" border=0></a></font></td>
                                                       </tr>
                                                <?php 
                                                $contador_relaciones=$contador_relaciones+1;
                                                }
                                        }
                                        ?>
                                        </table>
                                </td>
                        </tr>
                        <tr valign="top">
                                <td colspan=3 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Salud</b></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Alergias</b></font></td>
                                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$alergias;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Vacunaciones</b></font></td>
                                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$vacunaciones;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Observaciones</b></font></td>
                                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$observaciones;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nómbre del médico</b></font></td>
                                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$contacto_medico;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td width=100 align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono médico</b></font></td>
                                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$telefono_medico;?></font></td>
                        </tr>
                        </table>
                        <div id="botonera" align="center">
                        <br><input type="button" onclick="imprimir();" value="Imprimir" style="font-size:10px"> <input type="button" onclick="window.close();" value="Cerrar" style="font-size:10px">
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
