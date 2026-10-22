<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 
$fecha_seleccionada=$_GET["fecha_seleccionada"];

if (strlen($fecha_seleccionada)==0) {
        $fecha_seleccionada=time();
        }

$url_retorno="/index.php";

$capacidad_total_empresa=0;
$ocupacion_minima_total_empresa=0;
$ocupacion_hoy_empresa=0;
$ocupacion_anterior_empresa=0;
$ocupacion_futura_empresa=0;
$asistencia_hoy_empresa=0;
$asistencia_hoy_empresa_man=0;
$asistencia_hoy_empresa_tar=0;
$ocupacion_maxima_empresa=0;
$actual_comedor_hoy_empresa=0;
$asistencia_comedor_hoy_empresa=0;
$extras_comedor_hoy_empresa=0;
$faturacion_total_empresa=0;
$faturacion_total_he_empresa=0;
$faturacion_total_co_empresa=0;
$faturacion_total_cos_empresa=0;
$proximas_altas_comedor_empresa=0;

$total_pagado_empresa=0;
$total_pagado_he_empresa=0;
$total_pagado_co_empresa=0;
$total_pagado_cos_empresa=0;

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
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }
?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
                        <input type="button" onclick="self.location.href='listado_facturacion_aulas_ampliado.php?fecha_seleccionada=<?=$fecha_seleccionada?>'" value="Ver detalle de facturas" style="font-size: 10px">
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=23 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Informe de ocupación de centros</b></font></td>
                        </tr>
                         <?php 
                         $busca_datos_centro=db_query("SELECT * FROM centros");
                         while($datos_centro=db_fetch_array($busca_datos_centro)) {
                         $id_centro=$datos_centro["id_centro"];
                         $db_nombre_centro=$datos_centro["nombre_centro"];
                         ?>
                         <tr>
                                <td colspan=23><font size=2 face="<?= $fuente_general; ?>"><b>Aulas del Centro <i><?=$db_nombre_centro?></i></b></font></td>
                         </tr>
                         <?php 
                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro$filtro ORDER BY id_curso,nombre;") or die(db_error());
                         $capacidad_total=0;
                         $ocupacion_minima_total=0;
                         $ocupacion_hoy_centro=0;
                         $ocupacion_anterior_centro=0;
                         $ocupacion_futura_centro=0;
                         $asistencia_hoy_centro=0;
                         $asistencia_hoy_centro_man=0;
                         $asistencia_hoy_centro_tar=0;
                         $ocupacion_maxima_centro=0;
                         $actual_comedor_hoy_centro=0;
                         $extras_comedor_hoy_centro=0;
                         $asistencia_comedor_hoy_centro=0;
                         $faturacion_total_centro=0;
                         $faturacion_total_he_centro=0;
                         $faturacion_total_co_centro=0;
                         $faturacion_total_cos_centro=0;

                         $total_pagado_centro=0;
                         $total_pagado_he_centro=0;
                         $total_pagado_co_centro=0;
                         $total_pagado_cos_centro=0;
                         $proximas_altas_comedor_centro=0;
                         
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($aulas["id_curso"]));
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                }

                        // Analiza el aula
                        // Calcula ocupación actual
                        $consulta_ocupacion=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=?', 'sss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)), (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);

                        // Calcula ocupación total
                        $consulta_ocupacion_total=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=?', 's', array($aulas["id_aula"])) or die(db_error()."-1");
                        $ocupacion_total=db_num_rows($consulta_ocupacion_total);

                        // Calcula ocupación anterior
                        $consulta_ocupacion_anterior=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? AND fecha_baja<?', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $ocupacion_anterior=db_num_rows($consulta_ocupacion_anterior);

                        // Calcula ocupación futura
                        $consulta_ocupacion_futura=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? AND fecha_matricula>?', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $ocupacion_futura=db_num_rows($consulta_ocupacion_futura);

                        // Calcula asistencia
                        $consulta_asistencia_hoy=db_query_params('SELECT servicios_consumidos.id_alumno FROM servicios_consumidos WHERE (((servicios_consumidos.id_servicio)=?) AND ((servicios_consumidos.fecha)=?)) GROUP BY servicios_consumidos.id_alumno', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $asistencia_hoy=db_num_rows($consulta_asistencia_hoy);

                        // Calcula asistencia mañana
                        $consulta_asistencia_hoy_man=db_query_params('SELECT servicios_consumidos.id_alumno FROM servicios_consumidos WHERE (((servicios_consumidos.id_servicio)=?) AND ((servicios_consumidos.fecha)=?)) AND servicios_consumidos.turno=0', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $asistencia_hoy_man=db_num_rows($consulta_asistencia_hoy_man);

                        // Calcula asistencia tarde
                        $consulta_asistencia_hoy_tar=db_query_params('SELECT servicios_consumidos.id_alumno FROM servicios_consumidos WHERE (((servicios_consumidos.id_servicio)=?) AND ((servicios_consumidos.fecha)=?)) AND servicios_consumidos.turno=1', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $asistencia_hoy_tar=db_num_rows($consulta_asistencia_hoy_tar);


                        $ocupacion_max=$aulas["capacidad"];
                        $ocupacion_min=$aulas["ocupacion_minima"];
                        $color_ocupacion="#000000";
                        if ($ocupacion_actual>$ocupacion_max) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="Se ha superado la capacidad del aula.";
                                }
                        if ($ocupacion_actual<$ocupacion_min) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="No se alcanza la ocupación mínima.";
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

                        // Calcula asistencia al comedor
                        // Calcula actual comedor
                        $consulta_actual_comedor_hoy=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? AND alumnos.comedor<>2', 'sss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)), (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $actual_comedor_hoy=db_num_rows($consulta_actual_comedor_hoy);

                        // Calcula extras comedor
                        $consulta_asistencia_comedor_hoy=db_query_params('SELECT servicios_consumidos.id_alumno, alumnos.* FROM servicios_consumidos,alumnos WHERE servicios_consumidos.id_alumno=alumnos.id_usuario AND (((alumnos.aula)=?) AND ((servicios_consumidos.fecha)=?)) AND alumnos.comedor=2 AND servicios_consumidos.tipo_menu<>2 AND servicios_consumidos.hora_entrada_c<>\'\'', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1b");
                        $extras_comedor_hoy=db_num_rows($consulta_asistencia_comedor_hoy);

                        // Calcula asistencia comedor
                        $consulta_asistencia_comedor_hoy=db_query_params('SELECT servicios_consumidos.id_alumno FROM servicios_consumidos WHERE (((servicios_consumidos.id_servicio)=?) AND ((servicios_consumidos.fecha)=?)) AND servicios_consumidos.tipo_menu<>2 AND servicios_consumidos.hora_entrada_c<>\'\' GROUP BY servicios_consumidos.id_alumno', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1b");
                        $asistencia_comedor_hoy=db_num_rows($consulta_asistencia_comedor_hoy);

                        // Calcula ocupación futura comedor
                        $consulta_ocupacion_futura_comedor=db_query_params('SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=? AND fecha_matricula>? AND alumnos.comedor<>2', 'ss', array($aulas["id_aula"], (string)(date("Y-m-d", $fecha_seleccionada)))) or die(db_error()."-1");
                        $proximas_altas_comedor=db_num_rows($consulta_ocupacion_futura_comedor);
                        
                        // Consulta facturación por aula
                        $faturacion_aula=0;
                        $total_pagado_aula=0;

                        // Busca servicios que se deben contar
                        $fecha_servicio_buscar=$nombre_mes[date("n",$fecha_seleccionada)]." de ".date("Y",$fecha_seleccionada);

                                // Busca servicios facturados en el mes actual

                                // Servicios propios
                                //  AND detalles_factura.cod_facturacion NOT like 'co_%' AND detalles_factura.cod_facturacion NOT like 'he_%' 
                                // $consulta_servicios_aula="SELECT * FROM alumnos, facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND alumnos.id_alumno=facturas_emitidas.id_cliente AND detalles_factura.concepto like '%$fecha_servicio_buscar%' AND detalles_factura.concepto NOT like '%Comedor%' AND alumnos.aula=".$aulas["id_aula"]. ";";
                                $consulta_servicios_aula="SELECT * FROM alumnos, facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND alumnos.id_alumno=facturas_emitidas.id_cliente AND detalles_factura.concepto like '%$fecha_servicio_buscar%' AND alumnos.aula=".$aulas["id_aula"]. " ORDER BY detalles_factura.concepto;";
                                $consulta_factura = db_query($consulta_servicios_aula) or die (db_error());

                                while($datos_factura=db_fetch_array($consulta_factura)) {
                                        if ((strlen($datos_factura["cod_facturacion"])==0) or (substr($datos_factura["cod_facturacion"],0,3)=="au_")) {
                                        if ($datos_factura["subvencion"]==1) {
                                                $multiplicador=0;
                                                } else {
                                                $multiplicador=1;
                                                }
                                        $faturacion_aula=$faturacion_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);

                                        if ($multiplicador==1) {
                                                // Comprueba si está pagado
                                                // Comprueba pagos a cuenta
                                                $total_a_cuenta=0;

                                                $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($datos_factura["id_factura"]));
                                                while ($pagos=db_fetch_array($busca_pagos)) {
                                                        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                                                        }
                                                if ($total_a_cuenta>=($datos_factura["cantidad"]*$datos_factura["precio"])) {
                                                        $total_pagado_aula=$total_pagado_aula+($datos_factura["cantidad"]*$datos_factura["precio"]);
                                                        }
                                                } else {
                                                // Comprueba si se ha cobrado la subvención
                                                if ($datos_factura["subvencion_cobrada"]==1) {
                                                        $total_pagado_aula=$total_pagado_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                                        }
                                                }
                                        }
                                }

                        // Facturacion horas extra
                        // Horas extra sueltas
                        $consulta_servicios_aula="SELECT * FROM alumnos, facturas_emitidas, detalles_factura WHERE alumnos.id_alumno=facturas_emitidas.id_cliente AND facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.cod_facturacion='he_".date("n_Y",$fecha_seleccionada)."' AND alumnos.aula=".$aulas["id_aula"].";";
                        $faturacion_he_aula=0;
                        $total_pagado_he_aula=0;

                        $consulta_factura = db_query($consulta_servicios_aula) or die (db_error());
                        while($datos_factura=db_fetch_array($consulta_factura)) {
                                if ($datos_factura["subvencion"]==1) {
                                        $multiplicador=0;
                                        } else {
                                        $multiplicador=1;
                                        }
                                $faturacion_he_aula=$faturacion_he_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);

                                if ($multiplicador==1) {
                                                // Comprueba si está pagado
                                                // Comprueba pagos a cuenta
                                                $total_a_cuenta=0;

                                                $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($datos_factura["id_factura"]));
                                                while ($pagos=db_fetch_array($busca_pagos)) {
                                                        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                                                        }
                                                if ($total_a_cuenta>=($datos_factura["cantidad"]*$datos_factura["precio"])) {
                                                        $total_pagado_he_aula=$total_pagado_he_aula+($datos_factura["cantidad"]*$datos_factura["precio"]);
                                                        }
                                                } else {
                                                // Comprueba si se ha cobrado la subvención
                                                if ($datos_factura["subvencion_cobrada"]==1) {
                                                        $total_pagado_he_aula=$total_pagado_he_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                                        }
                                                }
                                }

                        // Facturacion comedor
                        $faturacion_co_aula=0;
                        $total_pagado_co_aula=0;

                        $consulta_servicios_aula="SELECT * FROM alumnos, facturas_emitidas, detalles_factura WHERE alumnos.id_alumno=facturas_emitidas.id_cliente AND facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.cod_facturacion='co_".date("n_Y",$fecha_seleccionada)."' AND alumnos.aula=".$aulas["id_aula"].";";

                        $consulta_factura = db_query($consulta_servicios_aula) or die (db_error());
                        while($datos_factura=db_fetch_array($consulta_factura)) {
                                if ($datos_factura["subvencion"]==1) {
                                        $multiplicador=0;
                                        } else {
                                        $multiplicador=1;
                                        }
                                $faturacion_co_aula=$faturacion_co_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                if ($multiplicador==1) {
                                                // Comprueba si está pagado
                                                // Comprueba pagos a cuenta
                                                $total_a_cuenta=0;

                                                $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($datos_factura["id_factura"]));
                                                while ($pagos=db_fetch_array($busca_pagos)) {
                                                        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                                                        }
                                                if ($total_a_cuenta>=($datos_factura["cantidad"]*$datos_factura["precio"])) {
                                                        $total_pagado_co_aula=$total_pagado_co_aula+($datos_factura["cantidad"]*$datos_factura["precio"]);
                                                        }
                                                } else {
                                                // Comprueba si se ha cobrado la subvención
                                                if ($datos_factura["subvencion_cobrada"]==1) {
                                                        $total_pagado_co_aula=$total_pagado_co_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                                        }
                                                }
                                }

                        $faturacion_cos_aula=0;
                        $total_pagado_cos_aula=0;

                        $consulta_servicios_aula="SELECT * FROM alumnos, facturas_emitidas, detalles_factura WHERE alumnos.id_alumno=facturas_emitidas.id_cliente AND facturas_emitidas.id_factura=detalles_factura.id_factura AND detalles_factura.concepto like 'Comedor suelto%' AND detalles_factura.concepto like '%".date("/m/Y",$fecha_seleccionada).",%' AND alumnos.aula=".$aulas["id_aula"].";";

                        $consulta_factura = db_query($consulta_servicios_aula) or die (db_error());
                        while($datos_factura=db_fetch_array($consulta_factura)) {
                                if ($datos_factura["subvencion"]==1) {
                                        $multiplicador=0;
                                        } else {
                                        $multiplicador=1;
                                        }
                                $faturacion_cos_aula=$faturacion_cos_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                if ($multiplicador==1) {
                                                // Comprueba si está pagado
                                                // Comprueba pagos a cuenta
                                                $total_a_cuenta=0;

                                                $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($datos_factura["id_factura"]));
                                                while ($pagos=db_fetch_array($busca_pagos)) {
                                                        $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                                                        }

                                                if ($total_a_cuenta>=($datos_factura["cantidad"]*$datos_factura["precio"])) {
                                                        $total_pagado_cos_aula=$total_pagado_cos_aula+($datos_factura["cantidad"]*$datos_factura["precio"]);
                                                        }
                                                } else {
                                                // Comprueba si se ha cobrado la subvención
                                                if ($datos_factura["subvencion_cobrada"]==1) {
                                                        $total_pagado_cos_aula=$total_pagado_cos_aula+(($datos_factura["cantidad"]*$datos_factura["precio"])*$multiplicador);
                                                        }
                                                }
                                }

                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=23 align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>">Curso: <?=$nombre_curso_academico;?></font></td>
                        </tr>
                        <tr valign="middle">
                        <td colspan="7" bgcolor="#efefef">&nbsp;</td>
                        <td align="center" bgcolor="#efefef" colspan="3"><a href="javascript:selecciona_fecha();" title="Selecciona la fecha en el calendario"><font size=1 face="<?= $fuente_general; ?>"><font size=1><img src="/imagen/calendario.gif" border=0 align="right"><b>Ocupación<br><?=date("d/m/Y",$fecha_seleccionada);?></b></font></a></td>
                        <td align="center" bgcolor="#efefef" colspan="5"><font size=1 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
                        <td align="center" bgcolor="#efefef" colspan="4" width=25%><font size=1 face="<?= $fuente_general; ?>"><b>Facturación (<?=$nombre_mes[date("n",$fecha_seleccionada)]." de ".date("Y",$fecha_seleccionada)?>)**</b><br>NOTA: Ingresos generados durante el mes indicado (educación, comedor, horas extra), no confundir con las facturas emitidas durante el mismo més.</font></td>
                        <td align="center" bgcolor="#eeeeee" colspan="4" width=25%><font size=1 face="<?= $fuente_general; ?>"><b>Cobrado (<?=$nombre_mes[date("n",$fecha_seleccionada)]." de ".date("Y",$fecha_seleccionada)?>)</font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Mín.</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Actual</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Asistencia</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Asis. Mañana</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Asis. Tarde</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Bajas</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Próximas Altas</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Inscritos</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Actual</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Asistencia</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Extras</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Fijos</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Prox. Altas</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Fijos</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Horas Extra Sueltas</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Comedor Fijo</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Comedor Extras</b></font></td>
                                <td align="center" bgcolor="#eeeeee"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Fijos</b></font></td>
                                <td align="center" bgcolor="#eeeeee"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Horas Extra Sueltas</b></font></td>
                                <td align="center" bgcolor="#eeeeee"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Comedor Fijo</b></font></td>
                                <td align="center" bgcolor="#eeeeee"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><b>Comedor Extras</b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$nombre_curso_academico;
                        }
                        $capacidad_total=$capacidad_total+$aulas["capacidad"];
                        $ocupacion_minima_total=$ocupacion_minima_total+$aulas["ocupacion_minima"];
                        $ocupacion_hoy_centro=$ocupacion_hoy_centro+$ocupacion_actual;
                        $asistencia_hoy_centro=$asistencia_hoy_centro+$asistencia_hoy;
                        $asistencia_hoy_centro_man=$asistencia_hoy_centro_man+$asistencia_hoy_man;
                        $asistencia_hoy_centro_tar=$asistencia_hoy_centro_tar+$asistencia_hoy_tar;
                        $ocupacion_maxima_centro=$ocupacion_maxima_centro+$ocupacion_total;

                        $actual_comedor_hoy_centro=$actual_comedor_hoy_centro+$actual_comedor_hoy;
                        $asistencia_comedor_hoy_centro=$asistencia_comedor_hoy_centro+$asistencia_comedor_hoy;
                        $extras_comedor_hoy_centro=$extras_comedor_hoy_centro+$extras_comedor_hoy;

                        $faturacion_total_centro=$faturacion_total_centro+$faturacion_aula;
                        $faturacion_total_he_centro=$faturacion_total_he_centro+$faturacion_he_aula;
                        $faturacion_total_co_centro=$faturacion_total_co_centro+$faturacion_co_aula;
                        $faturacion_total_cos_centro=$faturacion_total_cos_centro+$faturacion_cos_aula;

                        $total_pagado_centro=$total_pagado_centro+$total_pagado_aula;
                        $total_pagado_he_centro=$total_pagado_he_centro+$total_pagado_he_aula;
                        $total_pagado_co_centro=$total_pagado_co_centro+$total_pagado_co_aula;
                        $total_pagado_cos_centro=$total_pagado_cos_centro+$total_pagado_cos_aula;

                        $capacidad_total_empresa=$capacidad_total_empresa+$aulas["capacidad"];
                        $ocupacion_minima_total_empresa=$ocupacion_minima_total_empresa+$aulas["ocupacion_minima"];
                        $ocupacion_hoy_empresa=$ocupacion_hoy_empresa+$ocupacion_actual;
                        $asistencia_hoy_empresa=$asistencia_hoy_empresa+$asistencia_hoy;
                        $asistencia_hoy_empresa_man=$asistencia_hoy_empresa_man+$asistencia_hoy_man;
                        $asistencia_hoy_empresa_tar=$asistencia_hoy_empresa_tar+$asistencia_hoy_tar;
                        $ocupacion_maxima_empresa=$ocupacion_maxima_empresa+$ocupacion_total;

                        $ocupacion_anterior_centro=$ocupacion_anterior_centro+$ocupacion_anterior;
                        $ocupacion_futura_centro=$ocupacion_futura_centro+$ocupacion_futura;
                        $ocupacion_anterior_empresa=$ocupacion_anterior_empresa+$ocupacion_anterior;
                        $ocupacion_futura_empresa=$ocupacion_futura_empresa+$ocupacion_futura;

                        $actual_comedor_hoy_empresa=$actual_comedor_hoy_empresa+$actual_comedor_hoy;
                        $asistencia_comedor_hoy_empresa=$asistencia_comedor_hoy_empresa+$asistencia_comedor_hoy;
                        $extras_comedor_hoy_empresa=$extras_comedor_hoy_empresa+$extras_comedor_hoy;

                        $faturacion_total_empresa=$faturacion_total_empresa+$faturacion_aula;
                        $faturacion_total_he_empresa=$faturacion_total_he_empresa+$faturacion_he_aula;
                        $faturacion_total_co_empresa=$faturacion_total_co_empresa+$faturacion_co_aula;
                        $faturacion_total_cos_empresa=$faturacion_total_cos_empresa+$faturacion_cos_aula;

                        $total_pagado_empresa=$total_pagado_empresa+$total_pagado_aula;
                        $total_pagado_he_empresa=$total_pagado_he_empresa+$total_pagado_he_aula;
                        $total_pagado_co_empresa=$total_pagado_co_empresa+$total_pagado_co_aula;
                        $total_pagado_cos_empresa=$total_pagado_cos_empresa+$total_pagado_cos_aula;
                        
                        $proximas_altas_comedor_centro=$proximas_altas_comedor_centro+$proximas_altas_comedor;
                        $proximas_altas_comedor_empresa=$proximas_altas_comedor_empresa+$proximas_altas_comedor;
                        
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["nombre"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["capacidad"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["ocupacion_minima"];?></font></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion(1,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_actual;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion(2,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion(6,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_man;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion(7,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_tar;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion(3,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$ocupacion_anterior;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion(4,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$ocupacion_futura;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion(5,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_total;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion(8,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$actual_comedor_hoy;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion(9,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$asistencia_comedor_hoy;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion(10,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$extras_comedor_hoy;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion(9,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$actual_comedor_hoy-$extras_comedor_hoy;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion(11,<?=$aulas["id_aula"]?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$proximas_altas_comedor;?></font></a></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_aula, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_he_aula, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_co_aula, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_cos_aula, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_aula, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_he_aula, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_co_aula, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_cos_aula, 2, ',', '.');?> €</font></td>
                           </tr>
                         <?php 
                         }
                         ?>
                         <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><b>Total centro</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$capacidad_total;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$ocupacion_minima_total;?></font></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_centro(1,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_hoy_centro;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_centro(2,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_centro;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_centro(6,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_centro_man;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_centro(7,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_centro_tar;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_centro(3,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$ocupacion_anterior_centro;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_centro(4,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$ocupacion_futura_centro;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_centro(5,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_maxima_centro;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_centro(8,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$actual_comedor_hoy_centro;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_centro(9,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$asistencia_comedor_hoy_centro;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_centro(10,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$extras_comedor_hoy_centro;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_centro(9,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$actual_comedor_hoy_centro-$extras_comedor_hoy_centro;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_centro(11,<?=$id_centro?>);'><font size=1 face="<?= $fuente_general; ?>"><?=$proximas_altas_comedor_centro;?></font></a></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_centro, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_he_centro, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_co_centro, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_cos_centro, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_centro, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_he_centro, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_co_centro, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_cos_centro, 2, ',', '.');?> €</font></td>
                        </tr>
                        <?php 
                        }
                        ?>
                        <tr>
                        <td colspan=21>&nbsp;</td>
                        </tr>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><b>Total todos los centros</b></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$capacidad_total_empresa;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$ocupacion_minima_total_empresa;?></font></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_empresa(1);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_hoy_empresa;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_empresa(2);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_empresa;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_empresa(6);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_empresa_man;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_empresa(7);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$asistencia_hoy_empresa_tar;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_empresa(3);'><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$ocupacion_anterior_empresa;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_empresa(4);'><font size=1 face="<?= $fuente_general; ?>" color="#000000"><?=$ocupacion_futura_empresa;?></font></a></td>
                                <td align="center"><a href='javascript:mostrar_ocupacion_empresa(5);'><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_maxima_empresa;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_empresa(8);'><font size=1 face="<?= $fuente_general; ?>"><?=$actual_comedor_hoy_empresa;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_empresa(9);'><font size=1 face="<?= $fuente_general; ?>"><?=$asistencia_comedor_hoy_empresa;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_empresa(10);'><font size=1 face="<?= $fuente_general; ?>"><?=$extras_comedor_hoy_empresa;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_empresa(9);'><font size=1 face="<?= $fuente_general; ?>"><?=$actual_comedor_hoy_empresa-$extras_comedor_hoy_empresa;?></font></a></td>
                                <td align="center" bgcolor="#ffffff"><a href='javascript:mostrar_ocupacion_empresa(11);'><font size=1 face="<?= $fuente_general; ?>"><?=$proximas_altas_comedor_empresa;?></font></a></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_empresa, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_he_empresa, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_co_empresa, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($faturacion_total_cos_empresa, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_empresa, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_he_empresa, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_co_empresa, 2, ',', '.');?> €</font></td>
                                <td align="right" bgcolor="#fefefe"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_pagado_cos_empresa, 2, ',', '.');?> €</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</form>
</table><br><br>
<script language="Javascript">

function mostrar_ocupacion(tipo_listado,id_aula) {
        window.open("detalle_informe_ocupacion.php?fecha=<?=$fecha_seleccionada;?>&tipo="+tipo_listado+"&id_aula="+id_aula,"ventana_detalle","toolbar=no,width=750,height=550");
        }
function mostrar_ocupacion_centro(tipo_listado,id_centro) {
        window.open("detalle_informe_ocupacion_centro.php?id_curso=<?=$id_curso_actual?>&fecha=<?=$fecha_seleccionada;?>&tipo="+tipo_listado+"&id_centro="+id_centro,"ventana_detalle","toolbar=no,width=750,height=550");
        }
function mostrar_ocupacion_empresa(tipo_listado) {
        window.open("detalle_informe_ocupacion_empresa.php?id_curso=<?=$id_curso_actual?>&fecha=<?=$fecha_seleccionada;?>&tipo="+tipo_listado,"ventana_detalle","toolbar=no,width=750,height=550");
        }
function selecciona_fecha(nombre_campo_presel,nombre_formulario) {
                // Comprueba fecha

        mes_presel=<?=date("m",$fecha_seleccionada);?>;
        dia_presel=<?=date("d",$fecha_seleccionada);?>;
        ano_presel=<?=date("Y",$fecha_seleccionada);?>;
        fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;

        window.open('/herramientas/calendario_mes1.php?dia='+fecha_preseleccionada+'&pagina_destino=/gestion/centros/informe_ocupacion_aulas_ampliado.php','calendario','toolbar=no,resizable=no,width=200,height=216');
        }
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>
