<?php  require("../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 

$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
if (strlen($ejercicio)==0) {
        $ejercicio=date("Y");
        }
$filtros="year(facturas_emitidas.fecha_emision)=".$ejercicio;

if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

$columnas_tabla=6;
$columnas_centro=4;

if ($nivel_acceso==4) {
        $columnas_centro=6;
        $columnas_tabla=8;
        }

?>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="left"><font size=2 face="<?= $fuente_general; ?>"><a href='listado_facturacion_ejercicio.php?ejercicio=<?=$ejercicio-1;?>'><b><< Ejercicio Anterior</b></a></font></td>
                           <td align="center" colspan=<?=$columnas_centro?>><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Cantidades Facturadas por CIF/NIF - <?=$ejercicio;?></b><br><br></font></td>
                           <td align="right"><font size=2 face="<?= $fuente_general; ?>"><a href='listado_facturacion_ejercicio.php?ejercicio=<?=$ejercicio+1;?>'><b>Ejercicio Siguiente >></b></a></font></td>
                        </tr>
                        <form name="facturas">
                        <?php 
                        $consulta1="SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura, alumnos.id_usuario AS id_usu_alu  FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND $filtros ORDER BY alumnos.banco_dni_titular, facturas_emitidas.id_cliente, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre";
                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());

                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center" colspan=<?=$columnas_tabla?>><font size=2 face="<?= $fuente_general; ?>">No se han encontrado facturas emitidas con los criterios seleccionados...</font></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#ececec">
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>">NIF/CIF</font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>">Razón Social</font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>">Alumnos</font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>">Escuela</font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>">Total Educación y Comedor <?=$ejercicio?></font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Educación y Comedor</font></td>
                          <?php 
                          if ($nivel_acceso==4) {
                          ?>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>">Total facturado ejercicio <?=$ejercicio?></font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Facturación total</font></td>
                          <?php 
                          }
                          ?>
                        </tr>
                        <?php 
                        }

                        $contador=0;
                        $contador1=0;

                        $total_facturado_dni=0;
                        $total_educacion_dni=0;

                        $dni_actual="Inicial";
                        $texto_informar="";
                        
while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){

                        if ($dni_actual!=$datos_factura["banco_dni_titular"]) {

                        if ($dni_actual!="Inicial") {

                        if ($total_facturado_dni>=3005.06) {
                                $color_letras="red";
                                } else {
                                $color_letras="black";
                                }


                        $nombre_escuela="Desconocida";
                        if ($datos_factura["centro_factura"]!=0) {
                                $busca_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
                                if (db_num_rows($busca_escuela)!=0) {
                                        $datos_escuela=db_fetch_array($busca_escuela);
                                        $nombre_escuela=$datos_escuela["nombre_centro"];
                                        }
                        }
                        if ($dni_mostrar!="Facturas sin CIF/NIF") {
                        $texto_informar="Informar a Dpto. de administración";
                        }
                        ?>
                        <tr valign="top" bgcolor="#ffffff">
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?=$dni_mostrar;?></font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php  if ($dni_actual!="") { print $nombre_mostrar; } ?></font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php  if ($dni_actual!="") { print $alumno_actual; } ?></font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php  if ($dni_actual!="") { print $nombre_escuela; } ?></font></td>
                          <td align="right"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?=number_format($total_educacion_dni, 2, ',', '.')?> €</font></td>
                          <?php 
                          if ($total_educacion_dni>=6010.12) {
                          ?>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><b><?=$texto_informar?><b></font></td>
                          <?php 
                          } else {
                          ?>
                          <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php if ($dni_actual!="") { ?><a href="/pdf/informe_consumo_cliente_educacion.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&id_cliente=<?=$id_cliente_actual?>&id_usu_alu=<?=$id_usu_alumno_actual;?>" target="_new">Informe desglosado por hijos</a><br><a href="/pdf/informe_consumo_cliente_educacion_agrupado.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&id_cliente=<?=$id_cliente_actual?>&id_usu_alu=<?=$id_usu_alumno_actual;?>" target="_new">Informe agrupado</a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                          <?php 
                          }
                          if ($nivel_acceso==4) {
                          ?>
                          <td align="right"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?=number_format($total_facturado_dni, 2, ',', '.')?> €</font></td>
                          <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php if ($dni_actual!="") { ?><a href="/pdf/informe_consumo_cliente.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&total_facturado=<?=$total_facturado_dni?>&id_cliente=<?=$id_cliente_actual?>" target="_new">Informe desglosado por hijos</a><br><a href="/pdf/informe_consumo_cliente_agrupado.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&total_facturado=<?=$total_facturado_dni?>&id_cliente=<?=$id_cliente_actual?>" target="_new">Informe agrupado</a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                          <?php 
                          }
                          ?>
                        </tr>
                        <?php 
                        }

                        $total_facturado_dni=0;
                        $total_educacion_dni=0;
                        $dni_actual=$datos_factura["banco_dni_titular"];
                        $nombre_mostrar=$datos_factura["banco_titular"];
                        $id_usu_alumno_actual=$datos_factura["id_usu_alu"];
                        $alumno_actual=$datos_factura["Nombre"]." ".$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"];
                        $id_cliente_actual=$datos_factura["id_cliente"];
                        if ($dni_actual!="") {
                                $dni_mostrar=$dni_actual;
                                } else {
                                $dni_mostrar="Facturas sin CIF/NIF";
                                }
                        }


// Mira si los albaranes de la factura y calcula los totales
$id_factura=$datos_factura["id_factura"];


// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

$total_dto=0;
$total_base=0;
$total_iva=0;
$total_recargo=0;
$total_factura=0;


while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;

                // Consulta el codigo de cuenta

                $codigo_tarifa=$datos_albaranes["articulo"];
                $intentar_localizar=false;

                $codigo_cuenta=0;

                if ($codigo_tarifa==0) {

                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="co_") {
                                $codigo_cuenta=2;
                                }

                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="he_") {
                                $codigo_cuenta=1;
                                }

                        if (substr($datos_albaranes["cod_facturacion"],0,6)=="cod_e_") {

                                $id_servicio_actual=substr($datos_albaranes["cod_facturacion"],6,(strlen($datos_albaranes["cod_facturacion"])-6));


                                $busca_servicio=db_query_params('SELECT * FROM servicios_consumidos_asistencia,tarifa WHERE servicios_consumidos_asistencia.id_servicio=tarifa.ID AND servicios_consumidos_asistencia.id_servicio_consumido=?;', 's', array($id_servicio_actual));

                                if (db_num_rows($busca_servicio)!=0) {
                                        $datos_servicio=db_fetch_array($busca_servicio);

                                        $codigo_tarifa=$datos_servicio["ID"];
                                        $codigo_cuenta=$datos_servicio["desglosar_como"];
                                        } else {
                                        $intentar_localizar=true;
                                        }

                                }

                        if ((substr($datos_albaranes["cod_facturacion"],0,4)=="cod_") and (substr($datos_albaranes["cod_facturacion"],0,6)!="cod_e_")) {

                                $codigo_cuenta=2;

                                }

                        if (substr($datos_albaranes["cod_facturacion"],0,3)=="au_") {

                                $id_servicio_actual=substr($datos_albaranes["cod_facturacion"],3,(strlen($datos_albaranes["cod_facturacion"])-3));

                                $busca_servicio=db_query_params('SELECT * FROM servicios_contratados_aula, tarifa WHERE servicios_contratados_aula.id_servicio=tarifa.ID AND servicios_contratados_aula.id_servicio_contratado=?', 's', array($id_servicio_actual));

                                if (db_num_rows($busca_servicio)!=0) {
                                        $datos_servicio=db_fetch_array($busca_servicio);

                                        $codigo_tarifa=$datos_servicio["ID"];
                                        $codigo_cuenta=$datos_servicio["desglosar_como"];
                                        } else {
                                        $intentar_localizar=true;
                                        }

                                }

                        } else {
                        // Busca en tarifa

                        $busca_servicio=db_query_params('SELECT * FROM servicios_contratados, tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_servicio_contratado=?', 's', array($codigo_tarifa)) or die(db_error());

                        if (db_num_rows($busca_servicio)!=0) {
                                $datos_servicio=db_fetch_array($busca_servicio);

                                $codigo_tarifa=$datos_servicio["ID"];
                                $codigo_cuenta=$datos_servicio["desglosar_como"];

                                } else {
                                $intentar_localizar=true;
                                }

                        }

                 if ($intentar_localizar==true) {
                        // Intentando determinar mediante el concepto...

                        $palabras0=Array("chandal","uniforme");

                        for ($c_pal=0;$c_pal<=count($palabras0);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras0[$c_pal]))) {
                                        $codigo_cuenta=0;
                                        break;
                                        }

                                }

                        $palabras1=Array("hora extra","salida","visita","gabinete","inscripción","inscripcion");

                        for ($c_pal=0;$c_pal<=count($palabras1);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras1[$c_pal]))) {
                                        $codigo_cuenta=1;
                                        break;
                                        }

                                }

                        $palabras2=Array("comedor","comida","paella");

                        for ($c_pal=0;$c_pal<=count($palabras2);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras2[$c_pal]))) {
                                        $codigo_cuenta=2;
                                        break;
                                        }

                                }

                        $palabras3=Array("educacion","educación");

                        for ($c_pal=0;$c_pal<=count($palabras3);$c_pal++) {
                                $concepto_min=strtolower($datos_albaranes["concepto"]);

                                if (is_numeric(strpos($concepto_min,$palabras3[$c_pal]))) {
                                        $codigo_cuenta=3;
                                        break;
                                        }

                                }

                        }

                        if (($codigo_cuenta==2) or ($codigo_cuenta==3)) {
                                $total_educacion_dni=$total_educacion_dni+$total_abs;
                                }

        }

$total_factura=round(($total_base+$total_iva+$total_recargo),2);

$total_facturado_dni=$total_facturado_dni+$total_factura;

                        }
                        if ($total_facturado_dni>=3005.06) {
                        $color_letras="red";
                        } else {
                        $color_letras="black";
                        }
                        $nombre_escuela="Desconocida";
                        if ($datos_factura["centro_factura"]!=0) {
                                $busca_escuela=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
                                if (db_num_rows($busca_escuela)!=0) {
                                        $datos_escuela=db_fetch_array($busca_escuela);
                                        $nombre_escuela=$datos_escuela["nombre_centro"];
                                        }
                        }

                        ?>
                        <tr valign="top" bgcolor="#ffffff">
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?=$dni_mostrar;?></font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php  if ($dni_actual!="") { print $nombre_mostrar; } ?></font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php  if ($dni_actual!="") { print $alumno_actual; } ?></font></td>
                          <td align="left"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php  if ($dni_actual!="") { print $nombre_escuela; } ?></font></td>
                          <td align="right"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?=number_format($total_educacion_dni, 2, ',', '.')?> €</font></td>
                          <?php 
                          if ($total_educacion_dni>=6010.12) {
                          ?>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><b>Informar a Dpto. de administración<b></font></td>
                          <?php 
                          } else {
                          ?>
                          <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php if ($dni_actual!="") { ?><a href="/pdf/informe_consumo_cliente_educacion.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&id_cliente=<?=$id_cliente_actual?>&id_usu_alu=<?=$id_usu_alumno_actual;?>" target="_new">Informe desglosado por hijos</a><br><a href="/pdf/informe_consumo_cliente_educacion_agrupado.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&id_cliente=<?=$id_cliente_actual?>&id_usu_alu=<?=$id_usu_alumno_actual;?>" target="_new">Informe agrupado</a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                          <?php 
                          }
                          if ($nivel_acceso==4) {
                          ?>
                          <td align="right"><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?=number_format($total_facturado_dni, 2, ',', '.')?> €</font></td>
                          <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>" color="<?=$color_letras;?>"><?php if ($dni_actual!="") { ?><a href="/pdf/informe_consumo_cliente.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&total_facturado=<?=$total_facturado_dni?>&id_cliente=<?=$id_cliente_actual?>" target="_new">Informe desglosado por hijos</a><br><a href="/pdf/informe_consumo_cliente_agrupado.php?nif=<?=$dni_actual;?>&ejercicio=<?=$ejercicio?>&total_facturado=<?=$total_facturado_dni?>&id_cliente=<?=$id_cliente_actual?>" target="_new">Informe agrupado</a><?php  } else { ?>&nbsp;<?php  } ?></font></td>
                          <?php 
                          }
                          ?>
                        </tr>
                        </form>
                         </table>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='gestion_facturacion.php';" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php  require("../configuracion/parte_inferior.php"); ?>
