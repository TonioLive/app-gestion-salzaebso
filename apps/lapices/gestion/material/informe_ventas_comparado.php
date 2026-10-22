<?php require("../../configuracion/parte_superior_privado_ancho.php");


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
        
        $subcat=$_GET["subcat"];

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
                $tablas_facturas_escuelas_sp=",escuelamunicipal.alumnos";
                $relacion_facturas_escuelas="AND facturas_emitidas.id_cliente=alumnos.id_alumno ";
                $filtro_facturas_escuelas="AND alumnos.id_centro=$escuela_seleccionada ";
                } else {
                $escuela_seleccionada=0;
                $tablas_facturas_escuelas=",alumnos";
                $tablas_facturas_escuelas_sp=",escuelamunicipal.alumnos";
                $relacion_facturas_escuelas="AND facturas_emitidas.id_cliente=alumnos.id_alumno ";
                $filtro_facturas_escuelas="";
                }
        
        $filtro_desglose="";
               
                if (strlen($subcat)!=0) {
                
                        $filtro_desglose=" WHERE id_desglose=$subcat";
                
                }

?>
        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                <table>
                       <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"width="50%"><b>Desde:</b></td>
                                <td><select name="dia_i" size=1 style="font-size=10">
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
</select></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right" width="50%"><b>Hasta:</b></td>
                                <td><select name="dia_f" size=1 style="font-size=10">
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
</select></td>
</tr>
                      <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"width="50%"><b>Escuela:</b></td>
                                <td><select name="escuela_seleccionada" size=1 style="font-size: 10px">
                <option value="">Todas</option>
                <?php
                $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
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
                <b>Gestión de servicios<br></b><small><br><?=$periodo_mostrado;?><br><br><?=$nombres_escuelas_actuales[$escuela_seleccionada]?><br></small>
                        <table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <?php   
                        
                        $tipo_actual="";
                        $total_absoluto=0;
                        $total_absoluto_subvenciones=0;
                                                                                      
                        // comprueba si hay articulos
                        $base_datos_actual="gestionguarderias";
                        
                        // $consulta2a="SELECT detalles_factura.concepto, alumnos.id_centro, detalles_factura.subvencion, (sum(detalles_factura.cantidad*detalles_factura.precio)-(sum(detalles_factura.cantidad*detalles_factura.precio)*dto/100)) AS importe_total, sum(detalles_factura.cantidad) AS cantidad_total FROM $base_datos_actual.detalles_factura,$base_datos_actual.facturas_emitidas".$tablas_facturas_escuelas." WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' GROUP BY detalles_factura.concepto,alumnos.id_centro";
                        $consulta2a="SELECT detalles_factura.concepto, alumnos.id_centro, detalles_factura.subvencion, sum((detalles_factura.cantidad*detalles_factura.precio)-((detalles_factura.cantidad*detalles_factura.precio)*dto/100)) as importe_total, sum(detalles_factura.cantidad) AS cantidad_total FROM $base_datos_actual.detalles_factura,$base_datos_actual.facturas_emitidas".$tablas_facturas_escuelas." WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' GROUP BY detalles_factura.concepto,alumnos.id_centro";

                        // comprueba si hay articulos
                        $base_datos_actual="escuelamunicipal";
                        
                        // $consulta2b="SELECT detalles_factura.concepto, alumnos.id_centro, detalles_factura.subvencion, (sum(detalles_factura.cantidad*detalles_factura.precio)-(sum(detalles_factura.cantidad*detalles_factura.precio)*dto/100)) AS importe_total, sum(detalles_factura.cantidad) AS cantidad_total FROM $base_datos_actual.detalles_factura,$base_datos_actual.facturas_emitidas".$tablas_facturas_escuelas_sp." WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' GROUP BY detalles_factura.concepto,alumnos.id_centro";
                        $consulta2b="SELECT detalles_factura.concepto, alumnos.id_centro, detalles_factura.subvencion, sum((detalles_factura.cantidad*detalles_factura.precio)-((detalles_factura.cantidad*detalles_factura.precio)*dto/100)) as importe_total, sum(detalles_factura.cantidad) AS cantidad_total FROM $base_datos_actual.detalles_factura,$base_datos_actual.facturas_emitidas".$tablas_facturas_escuelas_sp." WHERE detalles_factura.id_factura=facturas_emitidas.id_factura ".$relacion_facturas_escuelas." AND fecha_emision>='$fecha_inicio_unix' ".$filtro_facturas_escuelas."AND fecha_emision<='$fecha_fin_unix' GROUP BY detalles_factura.concepto,alumnos.id_centro";
                        
                        if ($escuela_seleccionada!=0) {

                                if (($escuela_seleccionada!=10) and ($escuela_seleccionada!=23) and ($escuela_seleccionada!=24) and ($escuela_seleccionada!=25)) {
                                        $consulta2=$consulta2a;
                                } else {
                                        $consulta2=$consulta2b;
                                }

                        } else {

                                $consulta2="(".$consulta2a.") UNION (".$consulta2b.")";

                        }

                        $totalesPorEscuela=array();
                        $totalesPorArticulo=array();

                        $count=db_query($consulta2) or die(db_error());
                        $numRecords = db_num_rows($count);
                        if ($numRecords!=0) {

                                while($datos_articulo=db_fetch_array($count)) {
                                
                                    $es_subvencion=$datos_articulo["subvencion"];
                    
                                    if ((substr($datos_articulo["concepto"],0,22)=="Subvencion Conselleria") or (strtolower(substr($datos_articulo["concepto"],0,13))=="abono bono infantil")  or (strtolower(substr($datos_articulo["concepto"],0,13))=="bono infantil") or (strtolower(substr($datos_articulo["concepto"],0,17))=="subvencion centro")) {
                                            $proveedor_actual=141;
                                            }                               
                                    if ((substr($datos_articulo["concepto"],0,14)=="Cheque Escolar") or (substr($datos_articulo["concepto"],0,15)=="Beca de Comedor")) {
                                            $proveedor_actual=120;                                                        
                                            } 
                                          
                                    $articulo=$datos_articulo["concepto"];

                                    if (substr($datos_articulo["concepto"],0,18)=="Comedor suelto día") {
                                        $articulo="Comedor suelto día";

                                    }

                                    $cantidad_vendida=$datos_articulo["cantidad_total"];
                                    $total_articulo=$datos_articulo["importe_total"];

                                    $totalesPorEscuela[$datos_articulo['id_centro']][$articulo]['cantidad'] = $cantidad_vendida;
                                    $totalesPorEscuela[$datos_articulo['id_centro']][$articulo]['importe'] = $total_articulo;

                                    $totalesPorArticulo[$articulo]['cantidad'] += $cantidad_vendida;
                                    $totalesPorArticulo[$articulo]['importe'] += $total_articulo;

                                    }
                                }

                        // muestra los articulos
                        ?>
                        <tr valign="top" bgcolor="#dddddd">
                                <td><b>Servicio</b></td>
                                <td colspan="2" align="center"><b>Total</b></td>
                                <?php
                                // Desglose por escuelas
                                        $consultaEscuelas=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");                                        
                                        while($escuelas=db_fetch_array($consultaEscuelas)){ 
                                            if ($totalesPorEscuela[$escuelas['id_centro']]){
                                            ?>
                                            <td align="center" colspan=2><b><?=$escuelas['nombre_centro']?></b></td>
                                            <?php
                                            }                                            
                                        }
                                ?>                                 
                        </tr>
                        <tr valign="top" bgcolor="#dddddd">
                                <td><b>Servicio</b></td>   
                                <td align="center"><b>Unidades</b></td>
                                <td align="center"><b>Importe</b></td>
                                <?php
                                // Desglose por escuelas
                                        $consultaEscuelas=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");                                        
                                        while($escuelas=db_fetch_array($consultaEscuelas)){ 
                                            if ($totalesPorEscuela[$escuelas['id_centro']]){
                                            ?>
                                            <td align="center"><b>Unidades</b></td>
                                            <td align="center"><b>Importe</b></td>                                            <?php
                                            }                                            
                                        }
                                ?>                               
                        </tr>
                        <?php
                        foreach ($totalesPorArticulo as $articulo => $valoresArticulo) {
                            ?>
                            <tr valign="top" bgcolor="#FFFFFF">
                                    <td><?=$articulo;?></td>
                                    <td align="right"><?=number_format($valoresArticulo['cantidad'],0,",",".");?></td>
                                    <td align="right"><?=number_format($valoresArticulo['importe'],2,",",".");?> &euro;</td>
                                    <?php
                                    // Desglose por escuelas
                                        $consultaEscuelas=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");                                        
                                        while($escuelas=db_fetch_array($consultaEscuelas)){ 

                                            if ($totalesPorEscuela[$escuelas['id_centro']]){

                                            $cantidadEscuela=0;
                                            $importeEscuela=0;
                                            if (isset($totalesPorEscuela[$escuelas['id_centro']][$articulo])) {
                                                $cantidadEscuela=$totalesPorEscuela[$escuelas['id_centro']][$articulo]['cantidad'];
                                                $importeEscuela=$totalesPorEscuela[$escuelas['id_centro']][$articulo]['importe'];
                                            }
                                            ?>
                                            <td align="right"><?=number_format($cantidadEscuela,0,",",".");?></td>
                                            <td align="right"><?=number_format($importeEscuela,2,",",".");?> &euro;</td>
                                            <?php
                                            }
                                        }
                                    ?>                                   
                            </tr>
                            <?php
                        }
                        ?>
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
