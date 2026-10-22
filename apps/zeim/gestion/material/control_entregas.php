<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 
// Busca listado de actividades
$consulta_actividades=db_query("SELECT *,tipos_iva_servicios.categoria AS nombre_cat FROM tarifa, tipos_iva_servicios WHERE tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND tipos_iva_servicios.categoria like 'Material%'$filtro ORDER BY tipos_iva_servicios.categoria, tarifa.ARTICULO;") or die(db_error());

$filtro_actividades="";
$filtro_actividades1="";
while($actividades=db_fetch_array($consulta_actividades)) {
        $filtro_actividades=$filtro_actividades." OR reserva_servicios.id_servicio=".$actividades["ID"];
        $filtro_actividades1=$filtro_actividades1." OR servicios_contratados.id_servicio=".$actividades["ID"];
        }
if (strlen($filtro_actividades)!=0) {
        $filtro_actividades=substr($filtro_actividades,4,(strlen($filtro_actividades)-4));
        $filtro_actividades=" AND (".$filtro_actividades.")";
        
        $filtro_actividades1=substr($filtro_actividades1,4,(strlen($filtro_actividades1)-4));
        $filtro_actividades1=" AND (".$filtro_actividades1.")";
        
        }

$id_aula=$_GET["id_aula"];
$curso_seleccionado=$_GET["curso_seleccionado"];

if ($nivel_acceso==4) {
        $centro_seleccionado=$_GET["id_centro_seleccionado"];
        if (strlen($centro_seleccionado)==0) {
                $centro_seleccionado=$centro_actual;
                }
        } else {
        $centro_seleccionado=$centro_actual;
        }

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro_curso=" AND alumnos.id_curso=$id_curso_actual";
                                } else {
                                $filtro_curso="";
                                }

if ($id_aula=="-1") {
        $filtro_aulas="";
        } else {
        if (strlen($id_aula)==0) {
                $busca_la_primera=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_seleccionado AND id_curso=$id_curso_actual ORDER BY anio_maximo DESC");
                if (db_num_rows($busca_la_primera)!=0) {
                        $datos_primera=db_fetch_array($busca_la_primera);
                        $id_aula=$datos_primera["id_aula"];
                        } else {
                        $id_aula=0;
                        }
                }
        $filtro_aulas=" AND alumnos.aula=$id_aula";
        }

$fecha_time_stamp=time();
$inicio=$fecha_time_stamp;


$mes=date("n",$fecha_time_stamp);
$inicio_mes=date("m/d/Y",strtotime($mes."/1/".date("Y",$fecha_time_stamp)));
$inicio_mes_time_stamp=strtotime($inicio_mes);
$diasemana_inicio_mes=date("w",$inicio_mes_time_stamp);
$diasemana=date("w",$fecha_time_stamp);
$dianombre=$dia_sem_largo[$diasemana];
$mesnombre=$nombre_mes[$mes];


?>
<SCRIPT LANGUAGE=javascript>
<!--

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
function servicios(codigo) {
        window.open('/facturacion/editar_servicios.php?i='+codigo,'editar','toolbar=no,resizable=yes,width=750,height=500');
        }

function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }

function reservar_plaza(id_actividad,id_alumno,fecha) {
        window.open('reserva_actividad.php?curso_seleccionado=<?=$id_curso_actual?>&inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
        }

function cobrar_plaza(id_actividad,id_alumno,fecha,cantidad_cobrada) {
        window.open('cobro_actividad.php?curso_seleccionado=<?=$id_curso_actual?>&inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha+'&cantidad_cobrada='+cantidad_cobrada,'registro','toolbar=no,resizable=yes,width=450,height=400');
        }
function facturar_plaza(id_actividad,id_alumno,fecha) {
        window.open('factura_actividad.php?curso_seleccionado=<?=$id_curso_actual?>&inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=yes,width=450,height=400');
        }
function anular_cobro(id_actividad,id_alumno,fecha) {
        if (confirm("¿Deseas eliminar el cobro de esta activiad?\n\nAl hacerlo se borrará el recibo y la factura emitida.")) {
                window.open('anular_cobro_actividad.php?curso_seleccionado=<?=$id_curso_actual?>&inicio=<?=$fecha_time_stamp?>&id_actividad='+id_actividad+'&id_alumno='+id_alumno+'&fecha='+fecha,'registro','toolbar=no,resizable=no,width=450,height=400');
                }
        }
function anular_reserva(id_reserva, mensaje) {
        if (confirm("¿Deseas eliminar la reserva de "+mensaje+"?")) {
                window.open('anular_reserva_actividad.php?curso_seleccionado=<?=$id_curso_actual?>&id_reserva='+id_reserva,'registro','toolbar=no,resizable=no,width=450,height=400');
                }
        }
function ver_recibo(id_factura,id_material) {
        wver_recibo=window.open('about:blank','registro','toolbar=no,resizable=yes,width=450,height=400');
        wver_recibo.window.location.href='/pdf/recibos_material.php?id_factura='+id_factura+'&id_material='+id_material;
        }
function ver_factura(id_factura,id_alumno,fecha) {
        wver_factura=window.open('about:blank','facura','toolbar=no,resizable=yes,width=450,height=400');
        wver_factura.window.location.href='/pdf/factura.php?id_factura='+id_factura;
        }
function ver_albaran(id_factura,id_alumno,fecha) {
        wver_factura=window.open('about:blank','facura','toolbar=no,resizable=yes,width=450,height=400');
        wver_factura.window.location.href='/pdf/factura.php?alb=1&id_factura='+id_factura;
        }
//-->
</SCRIPT>
<div align="center"><center>
<form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_entregas">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?php  echo date("d",$fecha_time_stamp); ?> de <?php  echo $mesnombre; ?> de <?php  echo date("Y",$fecha_time_stamp); ?> - Control de entregas de material</b><br><br><input type="button" onclick="window.location.href='gestion_material.php'" value="Volver al listado de material" style="font-size: 12px">
<br></small></td>
  </tr>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Selecciona el aula: </b> <select name="id_aula" size=1 style="font-size: 10px" onchange="document.seleccion_entregas.submit();">
                <option value="-1">Todas las aulas</value>
                <?php 
                        $busca_listado_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$centro_seleccionado AND id_curso=$id_curso_actual ORDER BY anio_maximo DESC");

                        while($datos_listado_aulas=db_fetch_array($busca_listado_aulas)) {
                        ?>
                        <option value="<?=$datos_listado_aulas["id_aula"];?>"<?php if ($datos_listado_aulas["id_aula"]==$id_aula) { print " selected"; } ?>><?=$datos_listado_aulas["nombre"];?></option>
                        <?php 
                        }
                        ?></select>&nbsp;&nbsp;&nbsp;<b>Selecciona el curso: </b> <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_entregas.id_aula.value='';document.seleccion_entregas.submit();"><?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?></select><?php  if ($nivel_acceso==4) { ?>&nbsp;&nbsp;&nbsp;<b>Centro: </b></font><select name="id_centro_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_entregas.id_aula.value='';document.seleccion_entregas.submit()">
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php if ($centro_seleccionado==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select><?php  } ?></font>
  </tr>
</table>
</form>
<form name="listado_alumnos" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<?php 
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_seleccionado$filtro_curso$filtro_aulas ORDER BY alumnos.aula, Apellido1, Apellido2, Nombre") or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=7><font size=1 face="<?= $fuente_general; ?>">Para ver la ficha de un alumno, haz clic sobre su nombre o sobre el botón ( <img src="editar.gif" align="absmiddle">)</font></td>
        </tr>
        <?php 
        $aula_actual=0;

        $reservas=0;
        $reservas_aula=0;

        $total_ventas=0;
        $total_ventas_aula=0;
        
        $totales_por_menu=array();

        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        if ($aula_actual!=$listado_alumnos["aula"]) {
        if ($aula_actual!=0) {
        ?>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Ventas por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$total_ventas_aula;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Reservas por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$reservas_aula;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        }
        $aula_actual=$listado_alumnos["aula"];
        // Busca datos del aula
        $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=$aula_actual");
        $datos_aula=db_fetch_array($consulta_datos_aula);
        ?>
        <tr bgcolor="#efefef">
        <td colspan=7><font size=3 face="<?= $fuente_general; ?>"><b><?=$datos_aula["nombre"];?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Uds.</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        $total_ventas_aula=0;
        $reservas_aula=0;
        }
        $consulta_datos_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($listado_alumnos["id_alumno"])) or die(db_error());
        $datos_alumno=db_fetch_array($consulta_datos_alumno);

        // Todos los centros
        if ($datos_alumno["id_centro"]==$centro_seleccionado) {

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

        $color_fondo_fila_alu="#FFFFFF";

        ?>
        <tr bgcolor="<?=$color_fondo_fila_alu?>" valign="top">
        <td><font size=2 face="<?= $fuente_general; ?>"><a href="javascript:servicios(<?=$datos_alumno["id_alumno"];?>)"><b><font color="<?=$color_fuente_linea;?>"><?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <?php 
                // Comprueba reservas, anteriores compras y pagos

                ?>
                <td><?php 
                
                $contador_de_filas=0;
                
                // Comprueba reserva
                $busca_reserva_servicios=db_query("SELECT * FROM reserva_servicios,tarifa WHERE reserva_servicios.id_servicio=tarifa.ID AND reserva_servicios.id_alumno=".$datos_alumno["id_alumno"].$filtro_actividades);

                if (db_num_rows($busca_reserva_servicios)!=0) {
                        ?><table width=100% cellpadding=0 cellspacing=0 border=0>
                        <tr><td colspan=2><font size=1><b>Reservas:</b></font></td></tr>
                        <?php 
                        }

                while ($reserva_servicios=db_fetch_array($busca_reserva_servicios)) {
                        $id_actividad=$reserva_servicios["ID"];
                        $articulo_reservado=$reserva_servicios["ARTICULO"];
                        $unidades_reservadas=$reserva_servicios["cantidad"];
                        $fecha_reserva=date("d/m/Y",strtotime($reserva_servicios["fecha"]));
                        
                        $contador_de_filas=$contador_de_filas+1;
                        if (($contador_de_filas/2)==round($contador_de_filas/2,0)) {
                                $color_fila="#efefef";
                                } else {
                                $color_fila="#ffffff";
                                }
                        
                        ?>
                        <tr bgcolor="<?=$color_fila?>">
                            <td><font size=1><?=$articulo_reservado;?>, <?=$unidades_reservadas?> Ud. (<?=$fecha_reserva?>)</font></td>
                            <td align="right"><font size=1><input type="button" value="Anular Reserva" onclick="anular_reserva(<?=$reserva_servicios["id_reserva"]?>,'<?=$unidades_reservadas?> Ud. para <?=$datos_alumno["Apellido1"]?> <?=$datos_alumno["Apellido2"]?>, <?=$datos_alumno["Nombre"]?>')" style="font-size: 10px"> <input type="button" style="font-size: 10px" value="Entrega" onclick="facturar_plaza(<?=$id_actividad?>,<?=$datos_alumno["id_alumno"]?>,<?=$fecha_time_stamp?>)"></font></td>
                        </tr>
                        <?php 
                        }
                if (db_num_rows($busca_reserva_servicios)!=0) {
                        ?>
                        </table>
                        <?php 
                        }

                $busca_servicios_contratados=db_query("SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=".$datos_alumno["id_alumno"].$filtro_actividades1." ORDER BY servicios_contratados.fecha_inicio");
                
                if (db_num_rows($busca_servicios_contratados)!=0) {
                        ?><table width=100% cellpadding=0 cellspacing=0 border=0>
                        <tr><td colspan=2><font size=1><b>Entregas:</b></font></td></tr>
                        <?php 
                        }
                        
                while($servicios_contratados=db_fetch_array($busca_servicios_contratados)) {
                
                        $id_actividad=$servicios_contratados["ID"];
                        $articulo_contratado=$servicios_contratados["ARTICULO"];
                        $unidades_contratadas=$servicios_contratados["cantidad"];
                        $total_ventas_aula=$total_ventas_aula+$unidades_contratadas;
                        $total_ventas=$total_ventas+$unidades_contratadas;
                        
                        $fecha_contratacion=date("d/m/Y",strtotime($servicios_contratados["fecha_inicio"]));
                        
                        $contador_de_filas=$contador_de_filas+1;
                        if (($contador_de_filas/2)==round($contador_de_filas/2,0)) {
                                $color_fila="#efefef";
                                } else {
                                $color_fila="#ffffff";
                                }
                        
                        // Busca ultima factura emitida de este artículo
                        $total_cobradas=0;
                        $busca_factura=db_query_params('SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.articulo=? ORDER BY facturas_emitidas.fecha_emision DESC', 'ss', array($datos_alumno["id_alumno"], $servicios_contratados["id_servicio_contratado"])) or die(db_error());
                        if (db_num_rows($busca_factura)!=0) {
                                        while($datos_factura=db_fetch_array($busca_factura)) {
                                                $total_cobradas=$total_cobradas+$datos_factura["cantidad"];
                                                }
                                        }
                                        
                        if ($total_cobradas<$unidades_contratadas) {
                        ?>
                        <tr bgcolor="<?=$color_fila?>">
                            <td><font size=1><?=$articulo_contratado;?>, <?=$unidades_contratadas-$total_cobradas?> Ud. (<?=$fecha_contratacion?>)</font></td>
                            <td align="right"><font size=1><input type="button" value="Cobrar" onclick="cobrar_plaza(<?=$servicios_contratados["id_servicio_contratado"]?>,<?=$datos_alumno["id_usuario"]?>,<?=$fecha_time_stamp?>,<?=($unidades_contratadas-$total_cobradas)?>)" style="font-size: 10px"></font></td>
                        </tr>
                                <?php 
                                }
                        if (db_num_rows($busca_factura)!=0) {
                                $busca_factura=db_query_params('SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.articulo=? ORDER BY facturas_emitidas.fecha_emision DESC', 'ss', array($datos_alumno["id_alumno"], $servicios_contratados["id_servicio_contratado"])) or die(db_error());
                                while($datos_factura=db_fetch_array($busca_factura)) {
                        ?>
                        <tr bgcolor="<?=$color_fila?>">
                            <td><font size=1><?=$articulo_contratado;?>, <?=$datos_factura["cantidad"]?> Ud. (<?=date("d/m/Y",strtotime($datos_factura["fecha_emision"]))?>)</font></td>
                            <td align="right"><input type="button" value="Ver factura" onclick="ver_factura(<?=$datos_factura["id_factura"]?>,<?=$datos_alumno["id_alumno"]?>,<?=$fecha_time_stamp?>)" style="font-size: 10px"> <input type="button" value="Ver albarán" onclick="ver_albaran(<?=$datos_factura["id_factura"]?>,<?=$datos_alumno["id_alumno"]?>,<?=$fecha_time_stamp?>)" style="font-size: 10px"> <input type="button" value="Ver recibo" onclick="ver_recibo(<?=$datos_factura["id_factura"]?>,<?=$servicios_contratados["id_servicio_contratado"]?>)" style="font-size: 10px"></font></td>
                        </tr>
                                <?php 
                                }
                                }
                }
                if (db_num_rows($busca_servicios_contratados)!=0) {
                        ?>
                        </table>
                        <?php 
                        }
                ?></td>
                <?php 

                ?>
                <td align="center"><a href="javascript:editar(<?=$datos_alumno["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
                </tr>
                <?php 
                // Todos los centros
                }
            }
        ?>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Ventas por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$total_ventas_aula;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Reservas por aula:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$reservas_aula;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Ventas totales:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$total_ventas;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2 align=right><font size=3 face="<?= $fuente_general; ?>"><b>Reservas totales:</b></font></td>
        <td align=right><font size=3 face="<?= $fuente_general; ?>"><?=$reservas;?></font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=3 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>No se encuentran alumnos.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
</form>
<br>
<input type="button" onclick="window.location.href='gestion_material.php'" value="Salir" style="font-size: 12px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

