<?php  require("../../configuracion/parte_superior_privado.php");


// $busca_facturas=mysql_query("SELECT sum(total_factura) AS total FROM facturas_emitidas WHERE month(facturas_emitidas.fecha_emision)=11 AND year(facturas_emitidas.fecha_emision)=2008 GROUP BY ejercicio_factura") or die(mysql_error());

// $datos_fa=mysql_fetch_array($busca_facturas);

// echo $datos_fa["total"];

        // Busca el curso actual
        $anio_seleccionado=$_GET["anio_seleccionado"];

        if (strlen($anio_seleccionado)==0) {
        $anio_seleccionado=date("Y");
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
        
        $escuela_seleccionada=$_GET["escuela_seleccionada"];
        if (strlen($escuela_seleccionada)!=0) {
                $tablas_facturas_escuelas=",alumnos";
                $relacion_facturas_escuelas="AND facturas_emitidas.id_cliente=alumnos.id_alumno ";
                $filtro_facturas_escuelas="AND alumnos.id_centro=$escuela_seleccionada ";
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
                                <td align="right"width="50%"><font size=2 face="<?= $fuente_general; ?>"><b>Escuela:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="escuela_seleccionada" size=1 style="font-size: 10px">
                <option value="">Todas</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                $nombres_escuelas_actuales=array();
                $nombres_escuelas_actuales[0]="Todas las escuelas";
                while($centros=db_fetch_array($consulta_centros)){
                $nombres_escuelas_actuales[$centros["id_centro"]]=$centros["nombre_centro"];
                ?>
                <option value="<?=$centros["id_centro"];?>"<?php if ($escuela_seleccionada==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></td>
                        </tr>
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
                        <td colspan=3 align=center bgcolor="#FFFFFF"><br><br><font size=2><b>No hay tipos</b></font><br><br><br></td>
                        </tr>
                        <?php 
                        }
                        $tipo_actual="";
                        $total_absoluto=0;
                        $total_absoluto_subvenciones=0;
                        while($datos_familia = db_fetch_array($consulta_familia)){
                        
                        if ($datos_familia["id_desglose"]!=$tipo_actual) {
                                $tipo_actual=$datos_familia["id_desglose"];
                                $tipo_actual_nombre=$nombres_tipo["nombre_desglose"];
                                ?>
                                <tr>
                                <td colspan=3 align=left bgcolor="#ddddff"><font size=3><b><?=$tipo_actual_nombre?></b></font></td>
                                </tr>
                                <tr valign="top" bgcolor="#dddddd">
                                        <td><font size=2 face="<?=$fuente_general;?>"><b>Familia</b></font></td>
                                        <td colspan=2 align="center"><font size=2 face="<?=$fuente_general;?>"><b>Unidades/Total facturado</b></font></td>
                                </tr>
                                <?php 
                                $total_por_familia=0;
                                $total_subvenciones_por_familia=0;
                                }
                        
                        // comprueba si hay articulos
                        $consulta2="SELECT detalles_factura.concepto, detalles_factura.subvencion, sum(detalles_factura.cantidad*detalles_factura.precio) AS importe_total, sum(detalles_factura.cantidad) AS cantidad_total FROM detalles_factura,facturas_emitidas".$tablas_facturas_escuelas." WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas."AND detalles_factura.concepto_desglosar=".$datos_familia["id_desglose"]." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' GROUP BY detalles_factura.concepto";
                        $count=db_query($consulta2);
                        $numRecords = db_num_rows($count);
                        if ($numRecords!=0) {

                                ?>
                                <tr valign="top" bgcolor="#efefef">
                                        <td><font size=2 face="<?= $fuente_general; ?>"><?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>">&nbsp;</td>
                                </tr>
                                <?php 
                                while($datos_articulo=db_fetch_array($count)) {
                                        $cantidad_vendida=$datos_articulo["cantidad_total"];
                                        $total_articulo=$datos_articulo["importe_total"];
                                        if ($datos_articulo["subvencion"]==1) {
                                                // $total_articulo=$total_articulo*-1;
                                                $total_subvenciones_por_familia=$total_subvenciones_por_familia+round($total_articulo,2);
                                                $total_absoluto_subvenciones=$total_absoluto_subvenciones+round($total_articulo,2);
                                                }
                                        ?>
                                        <tr valign="top" bgcolor="#FFFFFF">
                                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$datos_articulo["concepto"];?></font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($cantidad_vendida,2,",",".");?></font></td>
                                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format($total_articulo,2,",",".");?> €</font></td>
                                        </tr>
                                        <?php 
                                        $total_por_familia=$total_por_familia+round($total_articulo,2);
                                        $total_absoluto=$total_absoluto+round($total_articulo,2);
                                        }

                                ?>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=2 face="<?= $fuente_general; ?>">Total cobrado a los padres <?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_por_familia,2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=2 face="<?= $fuente_general; ?>">Total subvenciones <?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_por_familia*-1,2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=2 face="<?= $fuente_general; ?>">Total <?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_por_familia+($total_subvenciones_por_familia*-1),2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#ffffff">
                                        <td colspan=3><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                                </tr>
                                <?php 
                                }
                        }
                        ?>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=2 face="<?= $fuente_general; ?>">Total cobrado a los padres, <?=$periodo_mostrado?>:</font></td>
                                        <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto,2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=2 face="<?= $fuente_general; ?>">Total subvenciones <?=$periodo_mostrado?>:</font></td>
                                        <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto_subvenciones*-1,2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#EFEFEF">
                                        <td><font size=2 face="<?= $fuente_general; ?>">Total <?=$periodo_mostrado?>:</font></td>
                                        <td align="right" colspan=2><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto+($total_absoluto_subvenciones*-1),2,",",".");?> €</font></td>
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
