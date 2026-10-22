<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$formulario_name="presupuesto";
$formulario_accion="nuevo_presupuesto1.php";

$url_retorno="listado_contactos.php";

$id_aula=$_POST["id_aula"];
$observaciones=$_POST["observaciones"];

if (strlen($observaciones)!=0) {
        $observaciones=urlencode($observaciones);
        }

if (strlen($id_aula)!=0) {

                // datos aula

                $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$id_aula");
                $datos_aula=db_fetch_array($busca_datos_aula);

                $nombre_aula=$datos_aula["nombre"];
                $id_centro_aula=$datos_aula["id_centro"];
                $id_curso_aula=$datos_aula["id_curso"];

                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_aula") or die(db_error()."-4");
                        if (db_num_rows($consulta_cursos)!=0) {
                                $cursos=db_fetch_array($consulta_cursos);
                                $nombre_curso=$cursos["nombre"];
                                } else {
                                $nombre_curso="";
                                }

                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro_aula");
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);

                        $db_nombre_centro=$datos_centro["nombre_centro"];
                        } else {
                        $db_nombre_centro="";
                        }
                }

                // Inserta el presupuesto
                
                // Comprueba si existe
                
                $busca_presupuesto=db_query("SELECT * FROM alumnos_presupuestos WHERE id_aula=$id_aula");
                if (db_num_rows($busca_presupuesto)!=0) {
                $datos_presupuesto=db_fetch_array($busca_presupuesto);
                $id_presupuesto=$datos_presupuesto["id_presupuesto"];
                $actualiza_presupuesto=db_query("UPDATE alumnos_presupuestos SET fecha='".date("Y-m-d")."',observaciones='$observaciones' WHERE id_presupuesto=$id_presupuesto");
                } else {
                $introduce_presupuesto=db_query("INSERT INTO alumnos_presupuestos (id_aula,fecha,observaciones) VALUES ('$id_aula','".date("Y-m-d")."','$observaciones');");
                $id_presupuesto=db_insert_id();
                }

                $busca_notas=db_query("SELECT * FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.tipo_servicios=0 AND (tarifa.id_curso=$id_curso_aula OR tarifa.id_curso=0) ORDER BY tarifa.CATEGORIA,tarifa.ARTICULO") or die(db_error());

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

                                        $busca_linea_presupuesto=db_query("SELECT * FROM alumnos_presupuestos_detalles WHERE id_presupuesto=$id_presupuesto AND id_servicio=$id_servicio");
                                        if (db_num_rows($busca_linea_presupuesto)!=0) {
                                                $datos_linea_presupuesto=db_fetch_array($busca_linea_presupuesto);
                                                $id_detalle=$datos_linea_presupuesto["id_detalle"];
                                                $actualiza_linea_presupuesto=db_query("UPDATE alumnos_presupuestos_detalles SET cantidad_servicio='$catidad_pres' WHERE id_detalle=$id_detalle");
                                                } else {
                                                $introduce_linea_presupuesto=db_query("INSERT INTO alumnos_presupuestos_detalles (id_presupuesto,id_servicio,cantidad_servicio) VALUES ('$id_presupuesto','$id_servicio','$catidad_pres');");
                                                $id_detalle=db_insert_id();
                                                }
                                        } else {
                                        // Si existe la borra
                                        $borra_linea=db_query("DELETE FROM alumnos_presupuestos_detalles WHERE id_presupuesto=$id_presupuesto AND id_servicio=$id_servicio") or die(db_error());
                                        }

                                }

$retorno=$_POST["retorno"];
if ($retorno==0) {
        header("Location: /gestion/centros/gestion_aulas.php?curso_seleccionado=$id_curso_aula");
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

<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>

<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div align="center"><center> <br><br>
<a href="/pdf/presupuesto.php?id_presupuesto=<?=$id_presupuesto?>&tipo=<?=intval($retorno)-1;?>">Abrir presupuesto reducido</a><br><br><br>
<a href="javascript:window.close();">Cerrar ventana</a>
</center></div>
</body>
</html>
<?php 
// }
?>
