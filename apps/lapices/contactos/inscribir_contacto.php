<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$id_contacto=$_POST["id_contacto"];

$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {

$nombre_alu=$_POST["nombre"];
$apellido1_alu=$_POST["apellido1"];
$apellido2_alu=$_POST["apellido2"];
$persona_contacto=$_POST["persona_contacto"];
$proceso_decision=$_POST["proceso_decision"];
$comentarios=$_POST["comentarios"];
$telefono=$_POST["telefono"];
$email=$_POST["email"];
$calle=$_POST["calle"];
$poblacion=$_POST["poblacion"];
$codigo_postal=$_POST["codigo_postal"];
$coordenadas=$_POST["coordenadas"];
$sexo=$_POST["sexo"];
$id_curso_contacto=$_POST["id_curso_contacto"];
$id_centro_contacto=$_POST["id_centro_contacto"];
$sip=$_POST["sip"];
$nuevo_alumno=$_POST["nuevo_alumno"];
$id_usuario=$_POST["id_usuario"];

$nonato=$_POST["nonato"];
$prematuro=$_POST["prematuro"];
$prematuro1=$_POST["prematuro1"];

$fecha_nacimiento_dia=$_POST["fecha_nacimiento_dia"];
$fecha_nacimiento_mes=$_POST["fecha_nacimiento_mes"];
$fecha_nacimiento_ano=$_POST["fecha_nacimiento_ano"];
if (!checkdate(intval($fecha_nacimiento_mes),intval($fecha_nacimiento_dia),intval($fecha_nacimiento_ano))) {
                                $fecha_nacimiento_dia="";
                                $fecha_nacimiento_mes="";
                                $fecha_nacimiento_ano="";
                                }
if ($fecha_nacimiento_dia!="--" and $fecha_nacimiento_mes!="--" and $fecha_nacimiento_ano!="----") {
                        $fecha_nacimiento=$fecha_nacimiento_ano."/".$fecha_nacimiento_mes."/".$fecha_nacimiento_dia;
                        if (!checkdate(intval($fecha_nacimiento_mes),intval($fecha_nacimiento_dia),intval($fecha_nacimiento_ano))) {
                                $fecha_nacimiento="";
                                }
                        } else {
                        $fecha_nacimiento="";
                        }
$fecha_b1_dia=$_POST["fecha_b1_dia"];
$fecha_b1_mes=$_POST["fecha_b1_mes"];
$fecha_b1_ano=$_POST["fecha_b1_ano"];
if (!checkdate(intval($fecha_b1_mes),intval($fecha_b1_dia),intval($fecha_b1_ano))) {
                                $fecha_b1_dia="";
                                $fecha_b1_mes="";
                                $fecha_b1_ano="";
                                }
if ($fecha_b1_dia!="--" and $fecha_b1_mes!="--" and $fecha_b1_ano!="----") {
                        $fecha_b1=$fecha_b1_ano."/".$fecha_b1_mes."/".$fecha_b1_dia;
                        if (!checkdate(intval($fecha_b1_mes),intval($fecha_b1_dia),intval($fecha_b1_ano))) {
                                $fecha_b1="";
                                }
                        } else {
                        $fecha_b1="";
                        }
$fecha_b2_dia=$_POST["fecha_b2_dia"];
$fecha_b2_mes=$_POST["fecha_b2_mes"];
$fecha_b2_ano=$_POST["fecha_b2_ano"];
if (!checkdate(intval($fecha_b2_mes),intval($fecha_b2_dia),intval($fecha_b2_ano))) {
                                $fecha_b2_dia="";
                                $fecha_b2_mes="";
                                $fecha_b2_ano="";
                                }
if ($fecha_b2_dia!="--" and $fecha_b2_mes!="--" and $fecha_b2_ano!="----") {
                        $fecha_b2=$fecha_b2_ano."/".$fecha_b2_mes."/".$fecha_b2_dia;
                        if (!checkdate(intval($fecha_b2_mes),intval($fecha_b2_dia),intval($fecha_b2_ano))) {
                                $fecha_b2="";
                                }
                        } else {
                        $fecha_b2="";
                        }
$fecha_b3_dia=$_POST["fecha_b3_dia"];
$fecha_b3_mes=$_POST["fecha_b3_mes"];
$fecha_b3_ano=$_POST["fecha_b3_ano"];
if (!checkdate(intval($fecha_b3_mes),intval($fecha_b3_dia),intval($fecha_b3_ano))) {
                                $fecha_b3_dia="";
                                $fecha_b3_mes="";
                                $fecha_b3_ano="";
                                }
if ($fecha_b3_dia!="--" and $fecha_b3_mes!="--" and $fecha_b3_ano!="----") {
                        $fecha_b3=$fecha_b3_ano."/".$fecha_b3_mes."/".$fecha_b3_dia;
                        if (!checkdate(intval($fecha_b3_mes),intval($fecha_b3_dia),intval($fecha_b3_ano))) {
                                $fecha_b3="";
                                }
                        } else {
                        $fecha_b3="";
                        }
$fecha_c_dia=$_POST["fecha_c_dia"];
$fecha_c_mes=$_POST["fecha_c_mes"];
$fecha_c_ano=$_POST["fecha_c_ano"];
if (!checkdate(intval($fecha_c_mes),intval($fecha_c_dia),intval($fecha_c_ano))) {
                                $fecha_c_dia="";
                                $fecha_c_mes="";
                                $fecha_c_ano="";
                                }
if ($fecha_c_dia!="--" and $fecha_c_mes!="--" and $fecha_c_ano!="----") {
                        $fecha_c=$fecha_c_ano."/".$fecha_c_mes."/".$fecha_c_dia;
                        if (!checkdate(intval($fecha_c_mes),intval($fecha_c_dia),intval($fecha_c_ano))) {
                                $fecha_c="";
                                }
                        } else {
                        $fecha_c="";
                        }

$id_quitar_nota=$_POST["id_quitar_nota"];
                // Borra la nota
                if (strlen($id_quitar_nota)!=0) {
                        db_query("DELETE FROM contactos_seguimiento WHERE id_nota=$id_quitar_nota");
                        }

if (strlen($id_contacto)!=0) {
        // Guarda cambios
        db_query("UPDATE contactos SET poblacion='$poblacion', Nombre='$nombre_alu', Apellido1='$apellido1_alu', Apellido2='$apellido2_alu', persona_contacto='$persona_contacto', proceso_decision='$proceso_decision', telefono='$telefono', email='$email', comentarios='$comentarios' WHERE id_contacto=$id_contacto;") or die(db_error());
        db_query("UPDATE contactos SET SIP='$sip',nonato='$nonato',prematuro='$prematuro',prematuro1='$prematuro1',id_usuario='$id_usuario', nuevo_alumno='$nuevo_alumno', id_centro=$id_centro_contacto,id_curso=$id_curso_contacto,fecha_nacimiento='$fecha_nacimiento', sexo='$sexo', codigo_postal='$codigo_postal', fecha_b1='$fecha_b1', fecha_b2='$fecha_b2', fecha_b3='$fecha_b3', fecha_c='$fecha_c' WHERE id_contacto=$id_contacto;") or die(db_error());
        } else {
        // Introduce nuevo centro
        db_query("INSERT INTO contactos (poblacion, Nombre, Apellido1, Apellido2, persona_contacto, proceso_decision, telefono, email, fecha_contacto, comentarios, coordenadas) VALUES ('$poblacion', '$nombre_alu', '$apellido1_alu', '$apellido2_alu', '$persona_contacto', '$proceso_decision', '$telefono', '$email', '".date("Y-m-d")."', '$comentarios', '$coordenadas');") or die(db_error());
        $id_contacto=db_insert_id();
        db_query("UPDATE contactos SET SIP='$sip',nonato='$nonato',prematuro='$prematuro',prematuro1='$prematuro1',id_usuario='$id_usuario', nuevo_alumno='$nuevo_alumno', id_centro=$id_centro_contacto,id_curso=$id_curso_contacto,fecha_nacimiento='$fecha_nacimiento', sexo='$sexo', codigo_postal='$codigo_postal', fecha_b1='$fecha_b1', fecha_b2='$fecha_b2', fecha_b3='$fecha_b3', fecha_c='$fecha_c' WHERE id_contacto=$id_contacto;") or die(db_error());
        }
}

$info_usuario_opcional="";
$info_inscripcion_opcional="";

if ($id_usuario==0) {
        // Comprueba si existe algún alumno con el nombre igual
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE Nombre='$nombre_alu' AND Apellido1='$apellido1_alu' AND Apellido2='$apellido2_alu'");
        if (db_num_rows($consulta_datos_usuario)!=0) {
                $datos_usuario_opcional=db_fetch_array($consulta_datos_usuario);
                $id_usuario_opcional=$datos_usuario_opcional["id_usuario"];
                $info_usuario_opcional="Existen datos de un alumno con el mismo nombre ($nombre_alu $apellido1_alu $apellido2_alu) con domicilio en ".$datos_usuario_opcional["Direccion"];
                // Busca inscripción del alumno en el curso seleccionado
                
                // Comprueba si el alumno está inscrito en el curso seleccionado
                $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos WHERE id_curso=$id_curso_contacto AND id_centro=$id_centro_contacto AND id_usuario=$id_usuario_opcional");
                if (db_num_rows($busca_inscripcion_curso_actual)!=0) {
                        $datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual);
                        $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_inscripcion_curso_actual["aula"]);
                        $datos_aula=db_fetch_array($busca_datos_aula);
                        $info_inscripcion_opcional="<br>Alumno ya inscrito en el centro seleccionado, en el aula: ".$datos_aula["nombre"];
                        }
                }
        } else {
                // Comprueba si el alumno está inscrito en el curso seleccionado
                $id_usuario_opcional=$id_usuario;
                $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos WHERE id_curso=$id_curso_contacto AND id_centro=$id_centro_contacto AND id_usuario=$id_usuario_opcional");
                if (db_num_rows($busca_inscripcion_curso_actual)!=0) {
                        $datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual);
                        $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_inscripcion_curso_actual["aula"]);
                        $datos_aula=db_fetch_array($busca_datos_aula);
                        $info_inscripcion_opcional="<br>Alumno ya inscrito en el centro seleccionado, en el aula: ".$datos_aula["nombre"];
                        }
        }
?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Inscripción de Contacto</b><br><br></font>
                <?php 
                if ($info_usuario_opcional!="") {
                ?>
                <?=$info_usuario_opcional;?><br>
                <?php 
                if ($info_inscripcion_opcional=="") {
                ?>
                <br><br>Si se trata del mismo alumno, pulsa el botón "Inscribir como el alumno encontrado"<br><br>
                Si se trata de un alumno distinto, pulsa el botón "Inscribir como nuevo alumno"<br><br>
                En caso de duda pulsa "Cancelar"<br><br>
                <input type="button" value="Cancelar" onclick="window.location.href='listado_contactos.php'" style="font-size: 10px">
                <input type="button" value="Inscribir con el alumno encontrado" style="font-size: 10px" onclick="caso1();">
                <input type="button" value="Inscribir como nuevo alumno" style="font-size: 10px" onclick="caso2();">
                <?php 
                        } else {
                ?>
                <?=$info_inscripcion_opcional;?>
                <br><br>Si se trata del mismo alumno, pulsa el botón "Relacionar contacto con alumno inscrito"<br><br>
                Si se trata de un alumno distinto, pulsa el botón "Inscribir como nuevo alumno"<br><br>
                En caso de duda pulsa "Cancelar"<br><br>
                <input type="button" value="Cancelar" onclick="window.location.href='listado_contactos.php'" style="font-size: 10px">
                <input type="button" value="Relacionar contacto con alumno inscrito" style="font-size: 10px" onclick="caso3();">
                <input type="button" value="Inscribir como nuevo alumno" style="font-size: 10px" onclick="caso2();">

                <?php 
                        }
                } else {
                if ($info_inscripcion_opcional=="") {
                ?>
                Se va a realizar la inscripción del alumno en el curso seleccionado.<br><br>
                Para continuar pulsa el botón "Siguiente >>"<br><br>
                En caso de duda pulsa el botón "Cancelar"<br><br>
                <input type="button" value="Cancelar" onclick="window.location.href='listado_contactos.php'" style="font-size: 10px">
                <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="caso2();">
                <?php 
                } else {
                ?>
                <?=$info_inscripcion_opcional;?><br><br>
                Para salir pulsa el botón "Finalizar >>"<br><br>
                <input type="button" value="Finalizar >>" onclick="window.location.href='listado_contactos.php'" style="font-size: 10px">
                <?php 
                }
                }
                ?>
                </td>
        </tr>
</form>
</table><br><br>
<script language="Javascript">
function caso1() {
        // Inscribir con el alumno encontrado
        window.location.href='inscribir.php?id_alumno=<?=$id_usuario_opcional?>&id_contacto=<?=$id_contacto?>';
        }
function caso2() {
        // Inscribir como nuevo alumno
        window.location.href='inscribir.php?id_alumno=0&id_contacto=<?=$id_contacto?>';
        }
function caso3() {
        // Relacionar contacto con alumno inscrito
        window.location.href='relacionar.php?id_alumno=<?=$id_usuario_opcional?>&id_contacto=<?=$id_contacto?>';
        }
</script>
<?php  require("../configuracion/parte_inferior.php"); ?>
