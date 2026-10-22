<?php  require("../configuracion/parte_superior_privado.php");
$num_errores=0;

$id_cliente=$_POST["id_cliente"];
$tipo_factura=$_POST["tipo_factura"];

$num_factura=$_POST["num_factura"];
$num_factura1=$num_factura;
$ejercicio=$_POST["ejercicio"];
$dia=$_POST["dia"];
$mes=$_POST["mes"];
$ano=$_POST["ano"];
$dia_i=$_POST["dia_i"];
$mes_i=$_POST["mes_i"];
$ano_i=$_POST["ano_i"];
$dia_f=$_POST["dia_f"];
$mes_f=$_POST["mes_f"];
$ano_f=$_POST["ano_f"];

$servicios_propios1=$_POST["servicios_propios1"];
$servicios_propios2=$_POST["servicios_propios2"];
$servicios_aula1=$_POST["servicios_aula1"];
$servicios_aula2=$_POST["servicios_aula2"];
$mostrar_horas_extra=$_POST["horas_extra"];
$mostrar_comedor=$_POST["comedor"];
$mostrar_comedor_extra=$_POST["comedor_extra"];

        // Busca el curso actual en centros externos
        $curso_seleccionado=$_POST["id_curso_actual"];
                
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM $baseDatosOrigenExternos.cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM $baseDatosOrigenExternos.cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        }

$array_facturas="";

if ($dia_i=="--" || $mes_i=="--" || $ano_i=="----")
                {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="No has completado la fecha de de inicio del periodo.";
                } else {
                        if (!checkdate(intval($mes_i),intval($dia_i),intval($ano_i))) {
                                  $num_errores=$num_errores+1;
                                  $errores[$num_errores]="La fecha de inicio del periodo escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                                 }
                        }
if ($dia_f=="--" || $mes_f=="--" || $ano_f=="----")
                {
                  $num_errores=$num_errores+1;
                  $errores[$num_errores]="No has completado la fecha de de finalización del periodo.";
                } else {
                        if (!checkdate(intval($mes_f),intval($dia_f),intval($ano_f))) {
                                  $num_errores=$num_errores+1;
                                  $errores[$num_errores]="La fecha de finalización del periodo escrita no es correcta, revísela ya que posiblemente ha escrito una fecha que no existe (por ej. 30 de febrero o 31 de abril).</b>";
                                 }
                        }
if ($num_errores==0) {

if ($id_cliente==-1) {
				$nombre_cliente="Todos los alumnos de centros externos";
        } else {
                // Busca datos del cliente
                $consulta_datos_cliente = db_query_params('SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND id_alumno=?', 's', array($id_cliente)) or die (db_error());

                $datos_del_cliente=db_fetch_array($consulta_datos_cliente);
		
                $nombre_cliente=$datos_del_cliente["Apellido1"]." ".$datos_del_cliente["Apellido2"].", ".$datos_del_cliente["Nombre"];
        }
?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Facturación de servicios del alumno: <?=$nombre_cliente?></b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                                 <tr>
                                 <td align="center">
<?php 
		// Base de datos origen
		$centro_facturado =10; // Sant Pau
		
        // Bucle de clientes
        $contador_alumnos=0;
        $contador_facturas=0;
		
		$busca_listado_alumnos="SELECT alumnos.*, usuarios.*,alumnos.id_centro AS centro_inscripcion FROM $baseDatosOrigenExternos.alumnos, $baseDatosOrigenExternos.usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_centro=$centro_facturado AND alumnos.id_curso=$id_curso_actual ORDER BY alumnos.aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre";
								
        $consulta_listado_alumnos=db_query($busca_listado_alumnos) or die(db_error());

        while($listado_alumnos=db_fetch_array($consulta_listado_alumnos)) {

        $anular_comedor=false;
        $anular_horas_extra=false;

        $nombre_alumno=$listado_alumnos["Apellido1"]." ".$listado_alumnos["Apellido2"].", ".$listado_alumnos["Nombre"];
        $id_cliente=$listado_alumnos["id_alumno"];
        
                $id_aula_cliente=$listado_alumnos["aula"];
                $id_usuario_cliente=$listado_alumnos["id_usuario"];
                $fecha_alta_cliente=$listado_alumnos["fecha_matricula"];
                $fecha_baja_cliente=$listado_alumnos["fecha_baja"];
                $comedor_cliente=$listado_alumnos["comedor"];
                $tipo_menu_cliente=$listado_alumnos["tipo_menu_comedor"];
                $numero_tomas=$listado_alumnos["numero_tomas"];
                
                // Consulta datos del curso
                $busca_curso_actual=db_query("SELECT * FROM $baseDatosOrigenExternos.cursos_academicos WHERE id_curso=".$listado_alumnos["id_curso"]);

                $datos_curso_actual=db_fetch_array($busca_curso_actual);

                $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso=$datos_curso_actual["fecha_fin"];

                $busca_datos_centro=db_query("SELECT * FROM $baseDatosOrigenExternos.centros WHERE id_centro=".$listado_alumnos["centro_inscripcion"]);
                if (db_num_rows($busca_datos_centro)!=0) {

                        $datos_centro=db_fetch_array($busca_datos_centro);
                        $precio_hora_extra=$datos_centro["precio_hora_extra"];
                        $precio_menu[0]=$datos_centro["precio_menu_3"];
                        $precio_menu[1]=$datos_centro["precio_menu_1"];
                        $precio_menu[2]=$datos_centro["precio_menu_2"];
                        $precio_menu[3]=$datos_centro["precio_menu_3"];
                        $precio_menu[4]=$datos_centro["precio_menu_4"];
                        $precio_menu[5]=$datos_centro["precio_menu_5"];
                        $precio_semana[0]=$datos_centro["precio_semana_3"];
                        $precio_semana[1]=$datos_centro["precio_semana_1"];
                        $precio_semana[2]=$datos_centro["precio_semana_2"];
                        $precio_semana[3]=$datos_centro["precio_semana_3"];
                        $precio_semana[4]=$datos_centro["precio_semana_4"];
                        $precio_semana[5]=$datos_centro["precio_semana_5"];
                        $precio_quincena[0]=$datos_centro["precio_quincena_3"];
                        $precio_quincena[1]=$datos_centro["precio_quincena_1"];
                        $precio_quincena[2]=$datos_centro["precio_quincena_2"];
                        $precio_quincena[3]=$datos_centro["precio_quincena_3"];
                        $precio_quincena[4]=$datos_centro["precio_quincena_4"];
                        $precio_quincena[5]=$datos_centro["precio_quincena_5"];
                        $precio_mes[0]=$datos_centro["precio_mes_3"];
                        $precio_mes[1]=$datos_centro["precio_mes_1"];
                        $precio_mes[2]=$datos_centro["precio_mes_2"];
                        $precio_mes[3]=$datos_centro["precio_mes_3"];
                        $precio_mes[4]=$datos_centro["precio_mes_4"];
                        $precio_mes[5]=$datos_centro["precio_mes_5"];

if ($datos_del_cliente["comedor_especial"]==1) {
        $precio_mes[0]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[1]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[2]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[3]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[4]=$datos_del_cliente["comedor_especial_precio"];
        $precio_mes[5]=$datos_del_cliente["comedor_especial_precio"];
        }
                        
// Comprueba si es horario de media jornada

if ($listado_alumnos["tipo_mensualidad"]!=1) {
$hora_entrada_centro_man=$datos_centro["hora_entrada1"];
$hora_salida_centro_man=$datos_centro["hora_salida2"];
$hora_entrada_centro_tar=$datos_centro["hora_entrada1"];
$hora_salida_centro_tar=$datos_centro["hora_salida2"];
$division_horas=($datos_centro["division_horas"]/60);
$periodo_gracia=($datos_centro["periodo_gracia"]*60);
$horas_comida=round(((strtotime($hora_entrada_centro_tar)-strtotime($hora_salida_centro_man))/3600),2);
if ($horas_comida<0) {
        $horas_comida=0;
        }
} else {
$hora_entrada_centro_man=$datos_centro["hora_entrada1_mj"];
$hora_salida_centro_man=$datos_centro["hora_salida1_mj"];
$hora_entrada_centro_tar=$datos_centro["hora_entrada2_mj"];
$hora_salida_centro_tar=$datos_centro["hora_salida2_mj"];
$division_horas=($datos_centro["division_horas"]/60);
$periodo_gracia=($datos_centro["periodo_gracia"]*60);
$horas_comida=0;
}
                        }
                        
        // Comprueba si el numero de factura esta ocupado, si lo está buscamos el proximo libre
        $seguir=true;
        while($seguir==true) {
                $consulta_facturas=db_query_params('SELECT * FROM facturas_emitidas WHERE num_factura=? AND ejercicio_factura=? AND serie=\'EDU\'', 'ss', array($num_factura, (string)($ejercicio)));
                if (db_num_rows($consulta_facturas)==0) {
                                $seguir=false;
                        } else {
                                $num_factura++;
                        }
                }
        
        // Inserta la factura
        if (strlen($num_factura)!=5) {
                $n_fact_completo="000000".$num_factura;
                $n_fact_completo=substr($n_fact_completo,(strlen($n_fact_completo)-5),5);
                $num_factura=$n_fact_completo;
                }
        
        // Inserta la factura
        $inserta_factura=db_query_params('INSERT INTO facturas_emitidas (serie, num_factura, ejercicio_factura, id_cliente, fecha_emision, tipo_cliente,facturada) VALUES (\'EDU\',?, ?, ?, ?,\'1\',\'1\');', 'ssss', array((string)($num_factura), (string)($ejercicio), (string)($id_cliente), (string)($ano) . '/' . (string)($mes) . '/' . (string)($dia))) or die (db_error());
        $id_nueva_factura=db_insert_id();

        $consulta1=array('SELECT * FROM servicios_contratados_externos, tarifa, tipos_iva_servicios WHERE servicios_contratados_externos.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados_externos.id_alumno=? ORDER BY servicios_contratados_externos.fecha_inicio DESC', 's', array($id_cliente));
		
        $consulta_albaranes_cliente = db_query_params($consulta1[0], $consulta1[1], $consulta1[2]) or die (db_error());

        $numRecords = db_num_rows ($consulta_albaranes_cliente);
        $contador=0;

       if ($numRecords!=0) {

        while($datos_albaranes = db_fetch_array($consulta_albaranes_cliente)){

                        // Determina si el servicio es periodico o unico
                        if ($datos_albaranes["periodicidad"]==1) {
                                if ($servicios_propios1==1) {
                                // Mensual
                                $nombre_servicio_1=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) {
                                        $nombre_servicio_1=$nombre_servicio_1." ".$datos_albaranes["notas"];
                                        }

                                $desde_fecha=date("n/Y",strtotime($datos_albaranes["fecha_inicio"]));
                                $mes_inicio=date("n",strtotime($datos_albaranes["fecha_inicio"]));
                                $anio_inicio=date("Y",strtotime($datos_albaranes["fecha_inicio"]));
                                $mes_fin=date("n",strtotime($datos_albaranes["fecha_fin"]))+1;
                                $anio_fin=date("Y",strtotime($datos_albaranes["fecha_fin"]));
                                $c_fecha=$desde_fecha;
                                $c_mes=$mes_inicio;
                                $c_anio=$anio_inicio;
                                if ($mes_fin==13) {
                                                $mes_fin=1;
                                                $anio_fin++;
                                                }
                                $hasta_fecha=$mes_fin."/".$anio_fin;

                                while($c_fecha!=$hasta_fecha) {
                                        $nombre_servicio=$nombre_servicio_1." ".$nombre_mes[$c_mes]." de ".$c_anio;

                                        $fecha_servicio_buscar=$nombre_mes[$c_mes]." de ".$c_anio;

                                        // Mira si el servicio está facturado y mira el nº de factura
                                        $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.articulo=".$datos_albaranes["id_servicio_contratado"]." AND detalles_factura.concepto like '%$fecha_servicio_buscar%' AND facturas_emitidas.tipo_cliente=1 ORDER BY facturas_emitidas.fecha_emision DESC";
                                        $consulta_factura = db_query($consulta2) or die (db_error());

                                        $estafact = db_num_rows ($consulta_factura);
                                        if ($estafact==0) {

                                        // Comprueba fechas
                                        $fecha_albaran=strtotime($c_mes."/1/".$c_anio);
                                        $fecha_ini=strtotime($mes_i."/1/".$ano_i);
                                        $fecha_fin=strtotime($mes_f."/1/".$ano_f);
                                        if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ($datos_albaranes["TIPO_SERVICIO"]==0)) {
                                                        // if ((($datos_albaranes["TIPO_SERVICIO"]!=0) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes)))) {
                                                        if (((($datos_albaranes["TIPO_SERVICIO"]!=1) or ($datos_albaranes["TIPO_SERVICIO"]!=0)) and (intval($c_mes)==intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==0)) and (intval($c_mes)!=intval($mes))) or ((($datos_albaranes["TIPO_SERVICIO"]==1)) and (intval($c_mes)!=intval($mes)))) {

                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }

                                                                $concepto_desglose=$datos_albaranes["desglosar_como"];

                                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["cantidad"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose))) or die (db_error());
                                                                $contador=$contador+1;
                                                                $contador_alumnos++;
                                                                }
                                                }

                                        }
                                        $c_mes++;
                                        if ($c_mes==13) {
                                                $c_mes=1;
                                                $c_anio++;
                                                }
                                        $c_fecha=$c_mes."/".$c_anio;
                                }
                                }
                                } else {

                                if ($servicios_propios2==1) {

                                // Unico
                                $nombre_servicio=$datos_albaranes["ARTICULO"];
                                if (strlen($datos_albaranes["notas"])!=0) { $nombre_servicio=$nombre_servicio.". ".$datos_albaranes["notas"]; }

                                // Comprueba si esta facturado
                                $consulta2="SELECT * FROM facturas_emitidas, detalles_factura WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=$id_cliente AND detalles_factura.articulo=".$datos_albaranes["id_servicio_contratado"]." ORDER BY facturas_emitidas.fecha_emision DESC";
                                $consulta_factura = db_query($consulta2) or die (db_error());

                                $estafact = db_num_rows ($consulta_factura);
                                if ($estafact==0) {
                                // Comprueba fechas
                                $fecha_albaran=strtotime($datos_albaranes["fecha_inicio"]);
                                $fecha_ini=strtotime($mes_i."/".$dia_i."/".$ano_i);
                                $fecha_fin=strtotime($mes_f."/".$dia_f."/".$ano_f);
                                if (($fecha_albaran>=$fecha_ini) and ($fecha_albaran<=$fecha_fin)) {

                                                        if ($datos_albaranes["anula_horas_extra"]==1) {
                                                                        $anular_horas_extra=true;
                                                                        }

                                                                if ($datos_albaranes["anula_comedor"]==1) {
                                                                        $anular_comedor=true;
                                                                        }

                                                $concepto_desglose=$datos_albaranes["desglosar_como"];

                                                $inserta_detalle=db_query_params('INSERT INTO detalles_factura (id_factura, articulo, precio, cantidad, dto, tipo_iva, concepto, subvencion, concepto_desglosar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);', 'sssssssss', array((string)($id_nueva_factura), (string)($datos_albaranes["id_servicio_contratado"]), (string)($datos_albaranes["PRECIO_VENTA"]), (string)($datos_albaranes["cantidad"]), (string)($datos_albaranes["descuento"]), (string)($datos_albaranes["porcentaje_iva"]), (string)($nombre_servicio), (string)($datos_albaranes["SUBVENCION"]), (string)($concepto_desglose))) or die (db_error());
                                                $contador=$contador+1;
                                                $contador_alumnos++;
                                                }
                                        }
                                }
                                }
                        }
        }
		
		// Fin servicios propios
                              
        if ($contador==0) {
                          // Borramos la factura
                          $nueva_factura=db_query_params('DELETE FROM facturas_emitidas WHERE id_factura=?;', 's', array($id_nueva_factura)) or die (db_error());
                          // $num_errores=$num_errores+1;
                          // $errores[$num_errores]="No se ha encontrado ningún servicio para el alumno <i>$nombre_alumno</i>. No se ha creado la factura.";
                          } else {
                          $array_facturas=$array_facturas.",".$id_nueva_factura;
                          $contador_facturas++;
                          }
       $contador_series=$contador_edu+$contador;
       if ($contador_series!=0) {
         $num_factura++;
         }
}
if ($contador_facturas==0) {
                          // Borramos la factura
                          $num_errores=$num_errores+1;
                          $errores[$num_errores]="No se ha encontrado ningún servicio pendiente de facturar. No se han creado las facturas.";
                          }
}
if ($num_errores==0) {
$array_facturas=substr($array_facturas,1,strlen($array_facturas)-1);
?>
<p align="center"><br><br>
<font size=2 face="<?=$fuente_general;?>">Se han emitido <?=$contador_facturas?> facturas desde el nº:<?=$num_factura1;?>/<?=$ejercicio;?> hasta el nº <?=substr("00000".($num_factura-1),strlen("00000".($num_factura-1))-5,5);?>/<?=$ejercicio;?>.<br><br>
<font size=2 face="<?= $fuente_general; ?>"><a href="<?=$url_web?>/pdf/facturas.php?facturas=<?=$array_facturas;?>" target="_blank">Vista Previa/Imprimir facturas emitidas</a></font><br><br>
<a href="facturar_servicios.php?curso_seleccionado=<?=$id_curso_actual;?>">Facturar a otro alumno</a> | <a href="gestion_facturacion.php">Volver</a></font><br><br>
</p>
<?php 
} else {
?><br><br>
<font size=2>
<p align="center"><b><i>
Se han detectado errores al crear la factura.<br>Vuelve a intentarlo:<br>
</i></b></p>
<p align="center">
<font face="<?php  echo $fuente_general; ?>">
<table width="100%" bgcolor="#ececec">
<?php 
    for ($contador=1; $contador<=$num_errores; $contador=$contador+1)
    {
      print "<tr><td><font face=\"Verdana\" size=1 color=\"".$color_fuente."\"><b>- $errores[$contador]</b></font></td></tr>";
    }
?>
</table>
<?php 
  print "</small></small><br><br>";
  print "<input type=\"button\" value=\"<< Volver\" onclick=\"window.location.href='facturar_servicios.php'\" id=1 name=1></p></font>";
  }
?></td>
                                 </tr>
                         </table>
                </td>
        </tr>
</table><br><br>
<?php  require("../configuracion/parte_inferior.php"); ?>
