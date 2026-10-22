<?php  require("../../configuracion/parte_superior_privado.php");

$id_curso_facturas=$_GET["curso_mostrado"];

if ((strlen($id_curso_facturas)!=0) and ($id_curso_facturas!=0)) {
        $filtros=$filtros." AND alumnos.id_curso=".$id_curso_facturas;
        }

// $busca_facturas=mysql_query("SELECT sum(total_factura) AS total FROM facturas_emitidas WHERE month(facturas_emitidas.fecha_emision)=11 AND year(facturas_emitidas.fecha_emision)=2008 GROUP BY ejercicio_factura") or die(mysql_error());

// $datos_fa=mysql_fetch_array($busca_facturas);

// echo $datos_fa["total"];

        // Busca el curso actual
        $anio_seleccionado=$_GET["anio_seleccionado"];

        if (strlen($anio_seleccionado)==0) {
        $anio_seleccionado=date("Y");
        if (date("n")<8) {
	    	$anio_seleccionado--;
        }
        }

        $fecha_inicio_curso_actual=$anio_seleccionado."-08-15";
        $fecha_fin_curso_actual=($anio_seleccionado+1)."-08-14";

        $dia_i=$_GET["dia_i"];
        $mes_i=$_GET["mes_i"];
        $ano_i=$_GET["ano_i"];
        $dia_f=$_GET["dia_f"];
        $mes_f=$_GET["mes_f"];
        $ano_f=$_GET["ano_f"];

        $errores=false;
        if ($dia_i=="--" || $mes_i=="--" || $ano_i=="----")
                {
                $errores=true;
                } else {
                        if (!checkdate(intval($mes_i),intval($dia_i),intval($ano_i))) {
                                  $errores=true;
                                 }
                        }
        if ($dia_f=="--" || $mes_f=="--" || $ano_f=="----")
                {
                $errores=true;
                } else {
                        if (!checkdate(intval($mes_f),intval($dia_f),intval($ano_f))) {
                                 $errores=true;
                                 }
                        }
        if ($errores==false) {
                $fecha_inicio_curso_actual=$ano_i."-".$mes_i."-".$dia_i;
                $fecha_fin_curso_actual=$ano_f."-".$mes_f."-".$dia_f;

                }

        $nombre_curso_actual="";

        $fecha_inicio_unix=$fecha_inicio_curso_actual;
        $fecha_fin_unix=$fecha_fin_curso_actual;

        $periodo_mostrado="Periodo del ".date("d/m/Y",strtotime($fecha_inicio_curso_actual))." al ".date("d/m/Y",strtotime($fecha_fin_curso_actual));

        $filtro_curso=$_GET["filtro_curso"];
        $periodo=$_GET["periodo"];
		$agrupar=$_GET["agrupar"];
		$solo_natacion=$_GET["solo_natacion"];

        $escuela_seleccionada=$_GET["escuela_seleccionada"];
        if ((strlen($escuela_seleccionada)!=0) or (strlen($filtro_curso)!=0)) {
                $tablas_facturas_escuelas=",alumnos";
                $relacion_facturas_escuelas="AND facturas_emitidas.id_cliente=alumnos.id_alumno ";
                if ((strlen($escuela_seleccionada)!=0)) {
	                $filtro_facturas_escuelas="AND alumnos.id_centro=$escuela_seleccionada ";
	                }
        		if (strlen($filtro_curso)!=0) {
        			$filtro_facturas_escuelas.="AND alumnos.id_curso=$filtro_curso ";

        			if (strlen($periodo)!=0) {
	        			$filtro_facturas_escuelas.="AND detalles_factura.periodo=$periodo ";
        				}

        			}
                } else {
                $escuela_seleccionada=0;
                $tablas_facturas_escuelas="";
                $relacion_facturas_escuelas="";
                $filtro_facturas_escuelas="";
                }


?>
        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
        <input type="hidden" name="familia" value="<?=$familia?>">
                <table>
                       <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Desde:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="dia_i" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{

  if ($c_dia<10)
  {

    $dia_a="0".$c_dia;
  }
    else
  {

    $dia_a=$c_dia;
  }

?>
    <option value="<?php    echo $dia_a; ?>"<?php if ($dia_a==date("d",strtotime($fecha_inicio_unix))) { print " selected"; } ?>><?php    echo $dia_a; ?></option>
    <?php 

}

?>
        </select>/<select name="mes_i" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{

  if ($c_mes<10)
  {

    $mes_a="0".$c_mes;
  }
    else
  {

    $mes_a=$c_mes;
  }

?>
    <option value="<?php    echo $mes_a; ?>"<?php if ($mes_a==date("m",strtotime($fecha_inicio_unix))) { print " selected"; } ?>><?php    echo $mes_a; ?></option>
    <?php 

}

?>
        </select>/<select name="ano_i" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php 
for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
{

  if ($c_ano<10)
  {

    $ano_a="0".$c_ano;
  }
    else
  {

    $ano_a=$c_ano;
  }

?>
    <option value="<?php    echo $ano_a; ?>"<?php if ($c_ano==date("Y",strtotime($fecha_inicio_unix))) { print " selected"; } ?>><?php    echo $ano_a; ?></option>
    <?php 

}

?>
</select></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Hasta:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="dia_f" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1)
{

  if ($c_dia<10)
  {

    $dia_a="0".$c_dia;
  }
    else
  {

    $dia_a=$c_dia;
  }

?>
    <option value="<?php    echo $dia_a; ?>"<?php if ($dia_a==date("d",strtotime($fecha_fin_unix))) { print " selected"; } ?>><?php    echo $dia_a; ?></option>
    <?php 

}

?>
        </select>/<select name="mes_f" size=1 style="font-size=10">
    <option value="--">--</option>
    <?php 
for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
{

  if ($c_mes<10)
  {

    $mes_a="0".$c_mes;
  }
    else
  {

    $mes_a=$c_mes;
  }

?>
    <option value="<?php    echo $mes_a; ?>"<?php if ($mes_a==date("m",strtotime($fecha_fin_unix))) { print " selected"; } ?>><?php    echo $mes_a; ?></option>
    <?php 

}

?>
        </select>/<select name="ano_f" size=1 style="font-size=10">
    <option value="----">----</option>
    <?php 
for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+2; $c_ano=$c_ano+1)
{

  if ($c_ano<10)
  {

    $ano_a="0".$c_ano;
  }
    else
  {

    $ano_a=$c_ano;
  }

?>
    <option value="<?php    echo $ano_a; ?>"<?php if ($c_ano==date("Y",strtotime($fecha_fin_unix))) { print " selected"; } ?>><?php    echo $ano_a; ?></option>
    <?php 

}

?>
</select></font></td>
</tr>
	<tr valign="top" bgcolor="#FFFFFF">
		<td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Alumnos del curso:</b></font></td>
		<td><font size=1 face="<?= $fuente_general; ?>"><select name="filtro_curso" size=1 style="font-size: 10px" onchange="document.seleccion_fecha.submit();">
			<option value="">Todos</option>
       		<?php 
          	$busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio DESC");

			while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
           		?>
          		<option value="<?=$datos_listado_cursos["id_curso"];?>"<?php  if ($datos_listado_cursos["id_curso"]==$filtro_curso) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
          		<?php 
        		}
			?></select></font></td>
	</tr>
<?php
if (strlen($filtro_curso)!=0) {
?>
<tr valign="top">
	<td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Periodo:</b></b></font></td>
		<td><font size=1 face="<?= $fuente_general; ?>"><select size=1 id="periodo" name="periodo">
        <option value="">Selecciona el periodo</option>
        <?php 
        // Busca periodos
        $consulta_periodos = db_query("SELECT * FROM aulas_grupos_trimestres WHERE id_curso=".$filtro_curso." ORDER BY fecha_inicio") or die (db_error());
        while($datos_periodos = db_fetch_array($consulta_periodos)){
        ?>
        <option value="<?=$datos_periodos["id_trimestre"];?>"<?php if ($periodo==$datos_periodos["id_trimestre"]) { print " selected"; }?>><?=$datos_periodos["nombre_trimestre"];?> (<?=date("d/m/Y",strtotime($datos_periodos["fecha_inicio"]));?> a <?=date("d/m/Y",strtotime($datos_periodos["fecha_fin"]));?>)</option>
        <?php 
        }
        ?>
        </select>
    </td>
</tr>
<tr valign="top">
	<td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>¿Desglosar por cliente?</b></b></font></td>
		<td><font size=1 face="<?= $fuente_general; ?>"><select size=1 id="agrupar" name="agrupar">
        <option value="0">No</option>
        <option value="1"<?php if ($agrupar==1) { print " selected"; }?>>Si</option>
        </select>
    </td>
</tr>
<tr valign="top">
	<td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mostrar solo clases de natación?</b></b></font></td>
		<td><font size=1 face="<?= $fuente_general; ?>"><select size=1 id="solo_natacion" name="solo_natacion">
        <option value="0">No</option>
        <option value="1"<?php if ($solo_natacion==1) { print " selected"; }?>>Si</option>
        </select>
    </td>
</tr>
<?php
}
?>
</table>
<input type="submit" value="Actualizar" style="font-size: 10px">
</form>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center"><br>
                <font size=2 face="<?= $fuente_general; ?>"><b>Gestión de servicios<br></b><small>Facturación por Familias.<br><?=$periodo_mostrado;?><br><br><?=$nombres_escuelas_actuales[$escuela_seleccionada]?><br></small></font>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php 
                        $consulta_familia = db_query("SELECT * FROM tipos_deslose_facturacion ORDER BY nombre_desglose") or die (db_error());

                        if (db_num_rows($consulta_familia)==0) {
                        ?>
                        <tr>
                        <td colspan=6 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay tipos</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        }
                        $tipo_actual="";
                        $total_absoluto=0;
                        $total_absoluto_dto=0;
                        $total_absoluto_dto_inc=0;
                        $total_absoluto_iva=0;

                        while($datos_familia = db_fetch_array($consulta_familia)){

                        if ($datos_familia["id_desglose"]!=$tipo_actual) {
                                $tipo_actual=$datos_familia["id_desglose"];
                                $tipo_actual_nombre=$nombres_tipo["nombre_desglose"];
                                ?>
                                <tr>
                                <td colspan=7 align=left bgcolor="#ddddff"><font size=3><b><?=$tipo_actual_nombre?></b></font></td>
                                </tr>
                                <tr valign="top" bgcolor="#dddddd">
                                        <td><font size=2 face="<?=$fuente_general;?>"><b>Familia</b></font></td>
                                        <td colspan=6 align="center"><font size=2 face="<?=$fuente_general;?>"><b>Unidades/Total facturado</b></font></td>
                                </tr>
                                <?php 
                                $total_por_familia=0;
                                $total_por_familia_dto=0;
                                $total_por_familia_dto_inc=0;
                                $total_por_familia_iva=0;
                                }

                        // comprueba si hay articulos
                        $consulta2="SELECT detalles_factura.concepto, detalles_factura.subvencion, sum(detalles_factura.cantidad*detalles_factura.precio) AS importe_total_base, sum(detalles_factura.cantidad*detalles_factura.precio*(detalles_factura.dto/100)) AS importe_total_base_dtos, sum(detalles_factura.cantidad*detalles_factura.precio*(1-(detalles_factura.dto/100))*(1+detalles_factura.tipo_iva)) AS importe_total_iva_inc, sum(detalles_factura.cantidad) AS cantidad_total FROM detalles_factura,facturas_emitidas".$tablas_facturas_escuelas." WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas."AND detalles_factura.concepto_desglosar=".$datos_familia["id_desglose"]." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' GROUP BY detalles_factura.concepto";

						if ($_GET["agrupar"]==1) {
							$consulta2="SELECT detalles_factura.concepto, detalles_factura.subvencion, sum(detalles_factura.cantidad*detalles_factura.precio) AS importe_total_base, sum(detalles_factura.cantidad*detalles_factura.precio*(detalles_factura.dto/100)) AS importe_total_base_dtos, sum(detalles_factura.cantidad*detalles_factura.precio*(1-(detalles_factura.dto/100))*(1+detalles_factura.tipo_iva)) AS importe_total_iva_inc, sum(detalles_factura.cantidad) AS cantidad_total, facturas_emitidas.id_cliente AS id_cli FROM detalles_factura,facturas_emitidas".$tablas_facturas_escuelas." WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas."AND detalles_factura.concepto_desglosar=".$datos_familia["id_desglose"]." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' GROUP BY facturas_emitidas.id_cliente,detalles_factura.concepto";
						}

						if ($_GET["solo_natacion"]==1) {
							if ($_GET["agrupar"]==1) {
								$consulta2="SELECT detalles_factura.concepto, detalles_factura.subvencion, sum(detalles_factura.cantidad*detalles_factura.precio) AS importe_total_base, sum(detalles_factura.cantidad*detalles_factura.precio*(detalles_factura.dto/100)) AS importe_total_base_dtos, sum(detalles_factura.cantidad*detalles_factura.precio*(1-(detalles_factura.dto/100))*(1+detalles_factura.tipo_iva)) AS importe_total_iva_inc, sum(detalles_factura.cantidad) AS cantidad_total, facturas_emitidas.id_cliente AS id_cli, tarifa.CATEGORIA FROM detalles_factura,facturas_emitidas".$tablas_facturas_escuelas.", servicios_contratados LEFT JOIN tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas."AND detalles_factura.articulo=servicios_contratados.id_servicio_contratado AND detalles_factura.concepto_desglosar=".$datos_familia["id_desglose"]." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' AND tarifa.CATEGORIA=1 GROUP BY facturas_emitidas.id_cliente,detalles_factura.concepto";
							} else {
								$consulta2="SELECT detalles_factura.concepto, detalles_factura.subvencion, sum(detalles_factura.cantidad*detalles_factura.precio) AS importe_total_base, sum(detalles_factura.cantidad*detalles_factura.precio*(detalles_factura.dto/100)) AS importe_total_base_dtos, sum(detalles_factura.cantidad*detalles_factura.precio*(1-(detalles_factura.dto/100))*(1+detalles_factura.tipo_iva)) AS importe_total_iva_inc, sum(detalles_factura.cantidad) AS cantidad_total, facturas_emitidas.id_cliente AS id_cli, tarifa.CATEGORIA FROM detalles_factura,facturas_emitidas".$tablas_facturas_escuelas.", servicios_contratados LEFT JOIN tarifa ON servicios_contratados.id_servicio=tarifa.ID WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas."AND detalles_factura.articulo=servicios_contratados.id_servicio_contratado AND detalles_factura.concepto_desglosar=".$datos_familia["id_desglose"]." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' AND tarifa.CATEGORIA=1 GROUP BY detalles_factura.concepto";
							}
						}

                        $count=db_query($consulta2);
                        $numRecords = db_num_rows($count);
                        if ($numRecords!=0) {

                                ?>
                                <tr valign="top" bgcolor="#efefef">
                                        <td><font size=2 face="<?= $fuente_general; ?>"><?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td  align="center"><font size=2 face="<?=$fuente_general;?>"><b>Unidades</b></font></td>
                                        <td  align="center"><font size=2 face="<?=$fuente_general;?>"><b>Base Imp.</b></font></td>
                                        <td  align="center"><font size=2 face="<?=$fuente_general;?>"><b>Dtos. Aplicados</b></font></td>
                                        <td  align="center"><font size=2 face="<?=$fuente_general;?>"><b>Total Inc. Dtos.</b></font></td>
                                        <td  align="center"><font size=2 face="<?=$fuente_general;?>"><b>IVA</b></font></td>
                                        <td  align="center"><font size=2 face="<?=$fuente_general;?>"><b>Total IVA Inc.</b></font></td>
                                </tr>
                                <?php 
                                while($datos_articulo=db_fetch_array($count)) {

												$nombre_cliente="";
												if ($_GET["agrupar"]==1) {
													// Busca nombre cliente
													if ($datos_articulo["id_cli"]) {

														$busca_nombre_cliente=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($datos_articulo["id_cli"]));

														if (db_num_rows($busca_nombre_cliente)) {
															$datos_nombre_cliente=db_fetch_array($busca_nombre_cliente);
															$nombre_cliente=" ".$datos_nombre_cliente["Nombre"]." ".$datos_nombre_cliente["Apellido1"]." ".$datos_nombre_cliente["Apellido2"];
														}

													}
												}

                                                $es_subvencion=$datos_articulo["subvencion"];

                                                if (substr($datos_articulo["concepto"],0,22)=="Subvencion Conselleria") {
                                                        $proveedor_actual=141;
                                                        }
                                                if ((substr($datos_articulo["concepto"],0,14)=="Cheque Escolar") or (substr($datos_articulo["concepto"],0,15)=="Beca de Comedor")) {
                                                        $proveedor_actual=120;
                                                        }

                                        $cantidad_vendida=$datos_articulo["cantidad_total"];
                                        $total_articulo=$datos_articulo["importe_total_base"];
                                        $total_dtos=$datos_articulo["importe_total_base_dtos"];
                                        $total_iva_inc=$datos_articulo["importe_total_iva_inc"];
                                        ?>
                                        <tr valign="top" bgcolor="#FFFFFF">
                                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_articulo["concepto"].$nombre_cliente;?></font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad_vendida,2,",",".");?></font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_articulo,2,",",".");?> €</font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_dtos,2,",",".");?> €</font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_articulo-$total_dtos,2,",",".");?> €</font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_iva_inc-($total_articulo-$total_dtos),2,",",".");?> €</font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_iva_inc,2,",",".");?> €</font></td>
                                        </tr>
                                        <?php 
                                        $total_por_familia=$total_por_familia+round($total_articulo,2);
                                        $total_absoluto=$total_absoluto+round($total_articulo,2);

                                        $total_por_familia_dto=$total_por_familia_dto+round($total_dtos,2);
                                        $total_absoluto_dto=$total_absoluto_dto+round($total_dtos,2);

                                        $total_por_familia_dto_inc=$total_por_familia_dto_inc+round($total_articulo-$total_dtos,2);
                                        $total_absoluto_dto_inc=$total_absoluto_dto_inc+round($total_articulo-$total_dtos,2);

                                        $total_por_familia_iva=$total_por_familia_iva+round($total_iva_inc,2);
                                        $total_absoluto_iva=$total_absoluto_iva+round($total_iva_inc,2);

                                        }

                                ?>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td colspan=2><font size=2 face="<?= $fuente_general; ?>">Total <?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_por_familia,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_por_familia_dto,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_por_familia_dto_inc,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_por_familia_iva-$total_por_familia_dto_inc,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_por_familia_iva,2,",",".");?> €</font></td>
                                </tr>
                                <tr><td colspan="6">&nbsp;</td></tr>
                                <?php 
                                }
                        }
                        ?>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td colspan=2><font size=2 face="<?= $fuente_general; ?>">Total <?=$periodo_mostrado?>:</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto_dto,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto_dto_inc,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto_iva-$total_absoluto_dto_inc,2,",",".");?> €</font></td>
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto_iva,2,",",".");?> €</font></td>
                                </tr>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
db_close();
require("../../configuracion/parte_inferior.php"); ?>
