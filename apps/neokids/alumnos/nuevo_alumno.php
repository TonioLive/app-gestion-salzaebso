<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$paso_actual=$_GET["paso_act"];
if (strlen($paso_actual)==0) {
        $paso_actual=$_POST["paso_act"];
        if (strlen($paso_actual)==0) { $paso_actual="11"; }
        }

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["u"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["u"];
                } else {
                $id_usuario_seleccionado==0;
                }
        }
        
if ($id_usuario_seleccionado!=0) {
        $consulta_datos_candidato=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_candidato)!=0)
                {
                $datos_candidato=db_fetch_array($consulta_datos_candidato);
                }
        }
        
if ($formulario_enviado=="1") {
        $num_errores=0;
        $paso_siguiente=$_POST["paso_siguiente"];

        include "procesado/paso".$paso_actual.".php";

        If ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query_params('UPDATE usuarios SET fecha_edicion=? WHERE id_usuario=?', 'ss', array((string)(strftime("%Y/%m/%d")), $id_usuario_seleccionado)) or die(db_error()."-1");
                             $paso_actual=$paso_siguiente;
                             $formulario_enviado=0;
                             }
        } else {
        // Datos Fijos
        $usuario_nombre=$datos_candidato["nombre"];
        $usuario_apellido1=$datos_candidato["apellido1"];
        $usuario_apellido2=$datos_candidato["apellido2"];

        // Datos del paso

        }

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "nuevo_alumno".$paso_siguiente."?u=".$id_usuario_seleccionado;

?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" mthod="<?=$formulario_method;?>" action="nuevo_alumno1.php">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Inscribir Alumno</b></font><br
                        <table bgcolor="#EFEFEF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr>
                                        <td align="left"></td>
                                        <td align="left"></td>
                                        <td align="left"></td>
                                        <td align="left"></td>
                                        <td align="left"></td>
                                </tr>
                        </table>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                                <tr valign="top">
                                        <td align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b><br><br>Selecciona una de las dos opciones<br><br></b></font></td>
                                </tr>
                                <tr valign="top">
                                        <td align="center" bgcolor="#efefef"><br><br><input type="button" value="Nuevo alumno" onclick="window.location.href='nuevo_alumno1.php'"> <input type="button" value="Alumno de otros años" onclick="window.open('seleccionar_alumno.php','nuevo','toolbar=no,resizable=no,width=550,height=400,scrollbars=yes');"></font><br><br></td>
                                </tr>
                         </table>
                         <br><input type="button" onclick="window.location.href='gestion_alumnos.php'" value="Cancelar"><br><br>
                </td>
        </tr>
</form>
</table>
</td>
</tr>
</table>
<?php 
 require("../configuracion/parte_inferior.php"); ?>
