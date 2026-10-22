<?php  require("../configuracion/parte_superior_privado_alumnos.php");

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
        
$formulario_enviado=$_POST["formulario_enviado"];
if ($formulario_enviado=="1") {
        $num_errores=0;

        include "procesado/paso4.php";

        if ($num_errores==0) {
                             // Registra la edición de los datos
                             db_query("UPDATE usuarios SET fecha_edicion='".strftime("%Y/%m/%d")."' WHERE id_usuario=$id_usuario_seleccionado") or die(db_error()."-1");
                             $formulario_enviado=0;
                             }
        }

mt_srand((double)microtime()*1000000);
if ($resultado=="") {
        header("Location: "."gestion_alumnos.php?r=".(mt_rand(0,10000000)/10000000));
        ?>
        <script language="Javascript">
        window.location.href='gestion_alumnos.php';
        </script>
        <?php 
        }
?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%" align="center">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><br><b>Inscripción del Alumno Finalizada</b></font><br>
                        <br>
                        <font size=2 face="<?= $fuente_general; ?>"><?=$resultado;?>
                        <?php 
                        // Analiza el aula
                        $consulta_datos_alumno=db_query("SELECT * FROM usuarios WHERE id_usuario=$id_usuario_seleccionado");
                        $datos_alumno=db_fetch_array($consulta_datos_alumno);

        if ($datos_alumno["nonato"]!=1) {

                $edad_alumno=date("Y")-date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
                if ((date("m")<=date("m",strtotime($datos_alumno["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($datos_alumno["fecha_nacimiento"]))))) {
                        $edad_alumno=$edad_alumno-1;
                        }
                if ($edad_alumno<2) {
                        // 1. Comprueba la edad restando el año de nacimiento al año actual
                        $fecha_l=$datos_alumno["fecha_nacimiento"];
                        $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                        // 1.1. Cacula edad con decimales
                        $mes_d_l=date("m",strtotime($fecha_l));
                        $mes_d_n=date("m");
                        $dif_meses=$mes_d_n-$mes_d_l;

                        $edad_d_l=($edad_a_l*12)+$dif_meses;
                        $edad_alumno=$edad_d_l." meses";
                        } else {
                        $edad_alumno=$edad_alumno." años";
                        }

               } else {
               $edad_alumno=0;
               }
               
                if ($id_aula_donde_se_inscribe!=0) {
                        
                        // Calcula ocupación actual
                        $consulta_ocupacion=db_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=".$id_aula_donde_se_inscribe) or die(db_error()."-1");
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);
                        
                        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$id_aula_donde_se_inscribe) or die(db_error()."-2");
                        $datos_aula=db_fetch_array($consulta_datos_aula);
                        $ocupacion_max=$datos_aula["capacidad"];
                        $ocupacion_min=$datos_aula["ocupacion_minima"];
                        $color_ocupacion="#000000";
                        if ($ocupacion_actual>$ocupacion_max) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="<br>Se ha superado la capacidad del aula.";
                                }
                        if ($ocupacion_actual<$ocupacion_min) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="<br>No se alcanza la ocupación mínima.";
                                }
                                
                        // Calcula promedio de edades del aula
                        $promedio_edades=0;
                        if ($ocupacion_actual>0) {
                                while($listado_alumnos_aula=db_fetch_array($consulta_ocupacion)) {
                                        // 1. Comprueba la edad restando el año de nacimiento al año actual
                                        $fecha_l=$listado_alumnos_aula["fecha_nacimiento"];
                                        $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                        // 1.1. Cacula edad con decimales
                                        $mes_d_l=date("m",strtotime($fecha_l));
                                        $mes_d_n=date("m");
                                        $dif_meses=$mes_d_n-$mes_d_l;

                                        $edad_d_l=($edad_a_l*12)+$dif_meses;
                                        $promedio_edades=$promedio_edades+$edad_d_l;

                                        }
                                $promedio_edades=round($promedio_edades/$ocupacion_actual,2);
                                }
                        ?>
                        <br><br>El aula queda de la siguiente forma:<br><br>
                        <table cellpadding=2 cellspacing=1 border=0>
                                <tr>
                                        <td align="right" bgcolor="#efefef"><font size=2><b>Ocupación total:</b></font></td>
                                        <td align="right" bgcolor="#ffffff"><font color="<?=$color_ocupacion?>" size=2><?=$ocupacion_actual?> alumnos</font><font size=1><i><?=$mensaje_ocupacion;?></i></font><br>
                                </tr>
                                <tr>
                                        <td align="right" bgcolor="#efefef"><font size=2><b>Capacidad:</b></font></td>
                                        <td align="right" bgcolor="#ffffff"><font size=2><?=$ocupacion_max;?>  alumnos</font></td>
                                </tr>
                                <tr>
                                        <td align="right" bgcolor="#efefef"><font size=2><b>Ocupación mínima:</b></font></td>
                                        <td align="right" bgcolor="#ffffff"><font size=2><?=$ocupacion_min;?>  alumnos</font></td>
                                </tr>
                                <tr>
                                        <td align="right" bgcolor="#efefef"><font size=2><b>Edad del nuevo alumno inscrito:</b></font></td>
                                        <td align="right" bgcolor="#ffffff"><font size=2><?=$edad_alumno;?></font></td>
                                </tr>
                                <tr>
                                        <td align="right" bgcolor="#efefef"><font size=2><b>Promedio de edades en el aula:</b></font></td>
                                        <td align="right" bgcolor="#ffffff"><font size=2><?=$promedio_edades;?> meses</font></td>
                                </tr>
                        </table>
                        <?php 
                        }
                        ?>
                        <br>
                        <br><input type="button" onclick="window.location.href='/alumnos/gestion_alumnos.php';" value="Seguir inscibiendo alumnos"> <input type="button" onclick="window.location.href='/gestion/centros/gestion_aulas.php'" value="Ver distribución de aulas"><br><br>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php  require("../configuracion/parte_inferior.php"); ?>
