<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 
$formulario_name="presupuesto";
$formulario_accion="nuevo_presupuesto1.php";

$url_retorno="listado_contactos.php";

$id_contacto=$_GET["id_contacto"];
$estado_inscripcion="";
$info_aula_alumno="";
$sexo_alumno_numero=0;
if (strlen($id_contacto)!=0) {

        $busca_datos_contacto=db_query_params('SELECT * FROM contactos WHERE id_contacto=?', 's', array($id_contacto));
        if (db_num_rows($busca_datos_contacto)!=0) {
                $datos_contacto=db_fetch_array($busca_datos_contacto);
                $mensaje="Emisión presupuesto";
                $nombre_alu=$datos_contacto["Nombre"];
                $apellido1_alu=$datos_contacto["Apellido1"];
                $apellido2_alu=$datos_contacto["Apellido2"];
                $persona_contacto=$datos_contacto["persona_contacto"];
                $proceso_decision=$datos_contacto["proceso_decision"];
                $comentarios=$datos_contacto["comentarios"];
                $telefono=$datos_contacto["telefono"];
                $email=$datos_contacto["email"];
                $poblacion=$datos_contacto["poblacion"];
                $codigo_postal=$datos_contacto["codigo_postal"];
                $sexo=$datos_contacto["sexo"];
                if ($datos_contacto["sexo"]=="H") {
                        $sexo_alumno_numero=1;
                        } else {
                        $sexo_alumno_numero=0;
                        }
                $id_curso_contacto=$datos_contacto["id_curso"];
                $id_centro_contacto=$datos_contacto["id_centro"];
                
                        $fecha_nacimiento_dia=date("j",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_mes=date("m",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_ano=date("Y",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_dia1=date("d",strtotime($datos_contacto["fecha_nacimiento"]));
                        
                        if (($datos_contacto["fecha_nacimiento"]=="0000-00-00") or ($datos_contacto["fecha_nacimiento"]=="")) {
                                $fecha_nacimiento_dia="";
                                $fecha_nacimiento_mes="";
                                $fecha_nacimiento_ano="";
                                $edad="";
                                } else {
                                $nacimiento=explode( '-', $datos_contacto["fecha_nacimiento"]);
                                $edad=date("Y")-$nacimiento[0];
                                $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                                $hoy=strtotime(date("Y/m/d"));
                                if (($cumple-$hoy)>0) {
                                        $edad=$edad-1;
                                        }
                                        if ($edad<2) {
                                                // 1. Comprueba la edad restando el año de nacimiento al año actual
                                                $fecha_l=$datos_contacto["fecha_nacimiento"];
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
                                }
                        $fecha_b1_dia=date("j",strtotime($datos_contacto["fecha_b1"]));
                        $fecha_b1_mes=date("m",strtotime($datos_contacto["fecha_b1"]));
                        $fecha_b1_ano=date("Y",strtotime($datos_contacto["fecha_b1"]));
                        if (($datos_contacto["fecha_b1"]=="0000-00-00") or ($datos_contacto["fecha_b1"]=="")) {
                                $fecha_b1_dia="";
                                $fecha_b1_mes="";
                                $fecha_b1_ano="";
                                }
                        $fecha_b2_dia=date("j",strtotime($datos_contacto["fecha_b2"]));
                        $fecha_b2_mes=date("m",strtotime($datos_contacto["fecha_b2"]));
                        $fecha_b2_ano=date("Y",strtotime($datos_contacto["fecha_b2"]));
                        if (($datos_contacto["fecha_b2"]=="0000-00-00") or ($datos_contacto["fecha_b2"]=="")) {
                                $fecha_b2_dia="";
                                $fecha_b2_mes="";
                                $fecha_b2_ano="";
                                }
                        $fecha_b3_dia=date("j",strtotime($datos_contacto["fecha_b3"]));
                        $fecha_b3_mes=date("m",strtotime($datos_contacto["fecha_b3"]));
                        $fecha_b3_ano=date("Y",strtotime($datos_contacto["fecha_b3"]));
                        if (($datos_contacto["fecha_b3"]=="0000-00-00") or ($datos_contacto["fecha_b3"]=="")) {
                                $fecha_b3_dia="";
                                $fecha_b3_mes="";
                                $fecha_b3_ano="";
                                }
                        $fecha_c_dia=date("j",strtotime($datos_contacto["fecha_c"]));
                        $fecha_c_mes=date("m",strtotime($datos_contacto["fecha_c"]));
                        $fecha_c_ano=date("Y",strtotime($datos_contacto["fecha_c"]));
                        if (($datos_contacto["fecha_c"]=="0000-00-00") or ($datos_contacto["fecha_c"]=="")) {
                                $fecha_c_dia="";
                                $fecha_c_mes="";
                                $fecha_c_ano="";
                                }

                $nuevo_alumno=$datos_contacto["nuevo_alumno"];
                $id_usuario=$datos_contacto["id_usuario"];
                
                if (intval($id_usuario)!=0) {
                        $busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario));
                        if (db_num_rows($busca_datos_usuario)!=0) {
                                $datos_usuario=db_fetch_array($busca_datos_usuario);
                                $texto_id_usuario="($id_usuario) ".$datos_usuario["Nombre"]." ".$datos_usuario["Apellido1"]." ".$datos_usuario["Apellido2"];
                                
                                // Comprueba si el alumno está inscrito en el curso seleccionado
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos WHERE id_curso=? AND id_centro=? AND id_usuario=?', 'sss', array($id_curso_contacto, $id_centro_contacto, $id_usuario));
                                if (db_num_rows($busca_inscripcion_curso_actual)!=0) {
                                        $datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual);
                                        $estado_inscripcion=" disabled";
                                        $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_inscripcion_curso_actual["aula"]));
                                        $datos_aula=db_fetch_array($busca_datos_aula);
                                        $info_aula_alumno="<br>Alumno inscrito en el centro seleccionado en el aula: ".$datos_aula["nombre"];
                                        }
                                } else {
                                $texto_id_usuario="($id_usuario) Usuario Desconocido";
                                }
                        } else {
                        $texto_id_usuario="";
                        }

                } else {
                $id_contacto="";
                $mensaje="Nuevo";
                }

?>
<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

function comprobar_y_enviar(accion) {

                var errores=0;

                var mensajes_error="";

                document.<?=$formulario_name;?>.retorno.value=accion;

                if (accion!=0) {
                        // document.<?=$formulario_name;?>.target="_blank";
                        } else {
                        // document.<?=$formulario_name;?>.target="_self";
                        }

                if (errores==0) {
						document.<?=$formulario_name;?>.submit();
                        if (accion!=0) {
                        // self.location.href='nuevo_contacto.php?id_contacto=<?=$id_contacto?>';
                        }
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }

        }

</script>
<input type="button" value="Cancelar" onclick="window.location.href='nuevo_contacto.php?id_contacto=<?=$id_contacto?>'" style="font-size: 10px"> <input type="button" value="Guardar y salir >>" style="font-size: 10px" onclick="comprobar_y_enviar(0);"> <input type="button" value="Imprimir Resumido >>" style="font-size: 10px" onclick="comprobar_y_enviar(2);"> <input type="button" value="Imprimir completo (Con totales) >>" style="font-size: 10px" onclick="comprobar_y_enviar(1);">
<br><br>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_contacto" value="<?=$id_contacto;?>">
<input type="hidden" name="formulario_enviado" value="1">
<input type="hidden" name="retorno" value="0">
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> Contacto</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela donde asistirá:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?php 
                        $consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=? ORDER BY nombre_centro;', 's', array($id_centro_contacto));
                        if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        } else {
                        $nombre_centro="";
                        }
                        ?>
                        <?=$nombre_centro?> <?=$info_aula_alumno?></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Curso en el que se inscribirá:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>">
                        <?php 
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_contacto)) or die(db_error()."-4");
                        if (db_num_rows($consulta_cursos)!=0) {
                                $cursos=db_fetch_array($consulta_cursos);
                                $nombre_curso=$cursos["nombre"];
                                } else {
                                $nombre_curso="";
                                }
                        echo $nombre_curso;
                        ?></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Información de contacto</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Persona de contacto:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$persona_contacto?></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Población:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$codigo_postal?> <?=$poblacion?></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre y apellidos del alumno:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_alu?> <?=$apellido1_alu?> <?=$apellido2_alu?></font></td>
                        </tr>
                        <?php 
                        if (strlen($id_contacto)!=0) {
                        
                        $busca_presupuesto=db_query_params('SELECT * FROM alumnos_presupuestos WHERE id_contacto=?', 's', array($id_contacto)) or die(db_error());
                        if (db_num_rows($busca_presupuesto)!=0) {
                                $datos_presupuesto=db_fetch_array($busca_presupuesto);
                                $id_presupuesto=$datos_presupuesto["id_presupuesto"];
                                $observaciones=$datos_presupuesto["observaciones"];
                                if (strlen($observaciones)!=0) {
                                        $observaciones=urldecode($observaciones);
                                        }
                                } else {
                                $id_presupuesto=0;
                                }
                        
                        ?>
                       <tr valign="top">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Comentarios:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="observaciones" rows=4  cols="80" style="font-size: 11px; font-family: Arial"><?= $observaciones; ?></textarea></font></td>
                       </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Servicios Contratados</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_notas=db_query_params('SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=? OR tarifa.id_curso=0) ORDER BY tarifa.CATEGORIA,tarifa.ARTICULO', 's', array($id_curso_contacto)) or die(db_error());
                                ?><br>
                                <table width="98%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="52%"><font size=1 face="<?= $fuente_general; ?>"><b>Servicio</b></font></td>
                                                <td align="center" width="16%"><font size=1 face="<?= $fuente_general; ?>"><b>Precio</b></font></td>
                                                <td align="center" width="16%"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
                                                <td align="center" width="16%"><font size=1 face="<?= $fuente_general; ?>"><b>Total</b></font></td>
                                        </tr>
                                <?php 
                                if (db_num_rows($busca_notas)==0) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center" colspan=6><font size=2 face="<?= $fuente_general; ?>"><b>No hay servicios que puedan presupuestarse para el curso seleccionado</font></td>
                                </tr>
                                <?php 
                                }
                                $familia_actual="";
                                $contador=0;
                                $total_total=0;
                                $array_codigos="";
                                while($datos_notas=db_fetch_array($busca_notas)) {
                                if ($familia_actual!=$datos_notas["categoria"]) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="left" colspan=4><font size=2 face="<?= $fuente_general; ?>"><b><?=$datos_notas["categoria"];?></b></font></td>
                                </tr>
                                <?php 
                                $familia_actual=$datos_notas["categoria"];
                                }
                                $id_servicio=$datos_notas["ID"];
                                $busca_linea_presupuesto=db_query_params('SELECT * FROM alumnos_presupuestos_detalles WHERE id_presupuesto=? AND id_servicio=?', 'ss', array($id_presupuesto, $id_servicio)) or die(db_error());
                                if (db_num_rows($busca_linea_presupuesto)!=0) {
                                                $datos_linea_presupuesto=db_fetch_array($busca_linea_presupuesto);
                                                $id_detalle=$datos_linea_presupuesto["id_detalle"];
                                                $cantidad_actual=$datos_linea_presupuesto["cantidad_servicio"];
                                                } else {
                                                $cantidad_actual=0;
                                                }
                                $total_actual=round(($cantidad_actual*$datos_notas["PRECIO_VENTA"]),2);
                                $total_total=$total_total+$total_actual;
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <input type="hidden" name="precio_<?=$datos_notas["ID"];?>" value="<?=$datos_notas["PRECIO_VENTA"];?>">
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_notas["ARTICULO"];?></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($datos_notas["PRECIO_VENTA"],2,".",",");?> €</font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="cantidad_<?=$datos_notas["ID"];?>" onblur="actualiza_totales()" size=4 style="font-size: 10px; text-align: right" value="<?=$cantidad_actual;?>"></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="total_<?=$datos_notas["ID"];?>" readonly size=4 style="font-size: 10px; text-align: right" value="<?=$total_actual;?>"> €</font></td>
                                </tr>
                                <?php 
                                $array_codigos=$array_codigos.",".$datos_notas["ID"];
                                $contador++;
                                }
                                if (strlen($array_codigos)!=0) {
                                        $array_codigos=substr($array_codigos,1,(strlen($array_codigos)-1));
                                        }

                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="right" colspan=3><font size=1 face="<?= $fuente_general; ?>"><b>Total presupuesto</b></font></td>
                                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="total_presupuesto" size=8 style="font-size: 10px; text-align: right" value="<?=$total_total;?>"> €</font></td>
                                </tr>
                                </table>

                                </td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                        <input type="button" value="Cancelar" onclick="window.location.href='nuevo_contacto.php?id_contacto=<?=$id_contacto?>'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar(0);"> <input type="button" value="Imprimir Resumido >>" style="font-size: 10px" onclick="comprobar_y_enviar(2);"> <input type="button" value="Imprimir completo (Con totales) >>" style="font-size: 10px" onclick="comprobar_y_enviar(1);">
                </td>
        </tr>
</table>
</form><br><br>
<script language="Javascript">

function actualiza_totales() {

var array_codigos=Array(<?=$array_codigos;?>);

total_presupuesto=0;
for (contador=0;contador<<?=$contador?>;contador++) {
        cantidad1=eval("document.presupuesto.cantidad_"+array_codigos[contador]+".value");

        if (isNaN(cantidad1)) {
                alert("La cantidad debe ser un número.");
                eval("document.presupuesto.cantidad_"+array_codigos[contador]+".value=0");
                } else {
                eval("document.presupuesto.total_"+array_codigos[contador]+".value=Math.round(document.presupuesto.cantidad_"+array_codigos[contador]+".value*document.presupuesto.precio_"+array_codigos[contador]+".value*100)/100")
                eval("total_presupuesto=Number(total_presupuesto)+Number(document.presupuesto.total_"+array_codigos[contador]+".value)");
                }
        }
        
document.presupuesto.total_presupuesto.value=total_presupuesto;

}

</script>
<?php 
        } else {
?>
No se puede emitir el presupuesto
<?php 

        }
require("../configuracion/parte_inferior.php"); ?>
