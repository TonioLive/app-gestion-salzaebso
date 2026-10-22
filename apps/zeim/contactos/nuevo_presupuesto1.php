<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$formulario_name="presupuesto";
$formulario_accion="nuevo_presupuesto1.php";

$url_retorno="listado_contactos.php";

$id_contacto=$_POST["id_contacto"];

$observaciones=$_POST["observaciones"];

if (strlen($observaciones)!=0) {
        $observaciones=urlencode($observaciones);
        }

$estado_inscripcion="";
$info_aula_alumno="";
$sexo_alumno_numero=0;

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

                // Inserta el presupuesto
                
                // Comprueba si existe
                
                $busca_presupuesto=db_query_params('SELECT * FROM alumnos_presupuestos WHERE id_contacto=?', 's', array($id_contacto));
                if (db_num_rows($busca_presupuesto)!=0) {
                $datos_presupuesto=db_fetch_array($busca_presupuesto);
                $id_presupuesto=$datos_presupuesto["id_presupuesto"];
                $actualiza_presupuesto=db_query_params('UPDATE alumnos_presupuestos SET fecha=?,observaciones=? WHERE id_presupuesto=?', 'sss', array((string)(date("Y-m-d")), (string)($observaciones), $id_presupuesto));
                } else {
                $introduce_presupuesto=db_query_params('INSERT INTO alumnos_presupuestos (id_contacto,fecha,observaciones) VALUES (?,?,?);', 'sss', array((string)($id_contacto), (string)(date("Y-m-d")), (string)($observaciones)));
                $id_presupuesto=db_insert_id();
                }

                $busca_notas=db_query_params('SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=? OR tarifa.id_curso=0) ORDER BY tarifa.CATEGORIA,tarifa.ARTICULO', 's', array($id_curso_contacto)) or die(db_error());

                                $familia_actual="";
                                $contador=0;
                                $array_codigos="";
                                while($datos_notas=db_fetch_array($busca_notas)) {

                                $id_servicio=$datos_notas["ID"];
                                $articulo=$datos_notas["ARTICULO"];
                                $precio_unitario=$datos_notas["PRECIO_VENTA"];
                                $catidad_pres=$_POST["cantidad_".$datos_notas["ID"]];

                                if (doubleval($catidad_pres)!=0) {
                                        // Inserta línea del presupuesto
                                        // Comprueba si existe

                                        $busca_linea_presupuesto=db_query_params('SELECT * FROM alumnos_presupuestos_detalles WHERE id_presupuesto=? AND id_servicio=?', 'ss', array($id_presupuesto, $id_servicio));
                                        if (db_num_rows($busca_linea_presupuesto)!=0) {
                                                $datos_linea_presupuesto=db_fetch_array($busca_linea_presupuesto);
                                                $id_detalle=$datos_linea_presupuesto["id_detalle"];
                                                $actualiza_linea_presupuesto=db_query_params('UPDATE alumnos_presupuestos_detalles SET cantidad_servicio=? WHERE id_detalle=?', 'ss', array((string)($catidad_pres), $id_detalle));
                                                } else {
                                                $introduce_linea_presupuesto=db_query_params('INSERT INTO alumnos_presupuestos_detalles (id_presupuesto,id_servicio,cantidad_servicio) VALUES (?,?,?);', 'sss', array((string)($id_presupuesto), (string)($id_servicio), (string)($catidad_pres)));
                                                $id_detalle=db_insert_id();
                                                }
                                        } else {
                                        // Si existe la borra
                                        $borra_linea=db_query_params('DELETE FROM alumnos_presupuestos_detalles WHERE id_presupuesto=? AND id_servicio=?', 'ss', array($id_presupuesto, $id_servicio)) or die(db_error());
                                        }

                                }

$retorno=$_POST["retorno"];
if ($retorno==0) {
        header("Location: nuevo_contacto.php?id_contacto=$id_contacto");
        } else {
        // header("Location: /pdf/presupuesto.php?id_presupuesto=$id_presupuesto&tipo=".(intval($retorno)-1));
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
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center> <br><br>
<a href="/pdf/presupuesto.php?id_presupuesto=<?=$id_presupuesto?>&tipo=<?=intval($retorno)-1;?>">Abrir presupuesto reducido</a><br><br><br>
<a href="javascript:window.close();">Cerrar ventana</a>
</center></div>
</body>
</html>
<?php 
// }
?>
