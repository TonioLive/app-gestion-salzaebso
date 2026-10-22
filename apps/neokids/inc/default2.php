<?php  
$asistencia_comedor_activada=true;
$comedor_activo=false;
$gabinete_activo=false;

mt_srand((float)microtime()*1000000);

$db_m1=db_connect("$sql_host","$sql_usuario","$sql_pass");
db_select_db("$sql_db",$db_m1);

// Actualiza comedor
include("cocina/actualiza_comedor.php");

$dia_sel=$_GET["dia"];
if (strlen($dia_sel)==0)
{
  $fecha_time_stamp=time();
}
  else
{
  $fecha_time_stamp=$dia_sel;
}

$fecha_informaciones=$fecha_time_stamp;

$nombres_escuelas=array();
$nombres_escuelas[0]="Actividades comunes";

$busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
while($lista_centros=db_fetch_array($busca_centros)){
        $nombres_escuelas[$lista_centros["id_centro"]]="Actividades para ".$lista_centros["nombre_centro"];
        }

$busca_aulas=db_query("SELECT * FROM centro_aulas ORDER BY id_aula");
$nombres_aulas=array();
$nombres_aulas[0]="Actividades comunes";
while($lista_aulas=db_fetch_array($busca_aulas)){
        $nombres_aulas[$lista_aulas["id_aula"]]="Aula ".$lista_aulas["nombre"];
        }

$fecha_agenda=date("d/m/Y",$fecha_time_stamp);


$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $fecha_inicio_curso=$datos_curso_actual["fecha_inicio"];
                $fecha_fin_curso=$datos_curso_actual["fecha_fin"];                
                } else {
                $id_curso_actual=0;
                }

if ($permitido!=true)
{

include "inc/inicio.php";

} else {

// Comprueba si el empleado ha fichado entrada en el centro

$busca_registros_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND fecha='".date("Y-m-d")."' AND hora_entrada is not Null AND hora_salida is Null ORDER BY hora_entrada") or die(db_error());
/*
if (mysql_num_rows($busca_registros_entrada)!=0) {
        $esta_en_el_centro=true;
        $estado_botones_fichar="";
        } else {
        $esta_en_el_centro=false;
        $estado_botones_fichar=" disabled";
        }	
*/
$estado_botones_fichar="";
$esta_en_el_centro=true; // Con esta linea anulamos lo de antes...
//Aqui la página de inicio de la zona privada

?><div align="center"><center><?php 
$ancho_tabla="100%";
?><table border="1" bordercolor="#e6bb91" style="bordercollapse: collapse" cellpadding="0" cellspacing="0" width="<?= $ancho_tabla; ?>" id="AutoNumber2">
  <tr valign=top>
    <td width="100%" bgcolor="#fff8c6" colspan="3">
	    <table width=100% border=0 cellpadding=0 cellspacing=0>
	    	<tr valign="bottom">
		    	<td>
			    	<font face="Arial" color="#800000" size=2><?= date("Y",$fecha_time_stamp)." ".$nombre_mes[intval(date("m",$fecha_time_stamp))]; ?><br>
    <font size=5><?php  echo date("j",$fecha_time_stamp); ?> </font><font size=4><?php  echo ($dia_sem_largo[date("w",$fecha_time_stamp)]); ?>, <?php 
  $hora=strftime("%H");
  if (($hora>=22 || $hora<5))
  {
    print "Buenas noches, ";  }

  if ($hora>=15 && $hora<22)
  {
    print "Buenas tardes, ";  }

  if ($hora>=5 && $hora<15)
  {
    print "Buenos dias, ";  }

  print $nombre;
?></font></font><?php 
    if (date("Y-m-d",$fecha_time_stamp)!=date("Y-m-d")) { ?></td><td align="right"><font face="Arial" color="#800000" size=2><a href="<?=$SERVER["PHP_SELF"];?>?dia=<?=time()?>">Regresar a hoy (<?=date("d/m/Y")?>)</a></font><?php  }
    ?>
    			</td>
	    		<?php		    				    		
    		// Busca lecturas de temperatura
    		$busca_temperaturas=db_query("SELECT * FROM registro_temperaturas.registro_temperaturas ORDER BY fecha DESC LIMIT 0,1");
    		if (db_num_rows($busca_temperaturas)!=0) {
	    		$datos_temperaturas=db_fetch_array($busca_temperaturas);
	    		?>
    			<td align="right">
	    			<big>
	    				<b>Temp. Piscina:</b> <?=number_format($datos_temperaturas["temp_piscina"],2,",",".")?> ºC	<br />		   
	    				<b>Temp. Sala:</b> <?=number_format($datos_temperaturas["temp"],2,",",".")?> ºC	<br />		
	    				<b>Humedad Rel.:</b> <?=number_format($datos_temperaturas["humedad_rel"],2,",",".")?> %&nbsp;<br />		    				    			</big>    				 			
    			</td> 
    			<td valign="middle" align="center"><button onclick="muestra_temperaturas();" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Muestra registro de temperaturas"><i class="fa fa-thermometer-three-quarters"></i></button></td>	    			
	    		<?php 
    		}
    		
    		
		    	?>   			
    		</tr>
    	</table></td>
  </tr>
<?php 
// Alertas, educadoras, coordinadoras y administradora
if (($nivel_acceso==3) or ($nivel_acceso==4) or ($nivel_acceso==2)) {
include "alertas.php";
}

if (($nivel_acceso>=3) and ($nivel_acceso<10)) {

if (strlen($centro_actual)!=0) {
?>
<tr>
<td width="250" valign="top" bgcolor="#cfcfcf" align="left">
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>" color="#800000"><b>Opciones</b></font></td></tr>
</table>
<?php 
if (($nivel_acceso==4) or ($zona_alumnos==1)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Alumnos</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/formulario/listado.php" target="_blank" title="Respuestas consultas e-mail">Respuestas consultas e-mail</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/contactos/listado_contactos.php" title="Gestión de contactos de padres interesados en inscribir a sus hijos.">Contactos</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/contactos/listado_contactos_nuevos.php" title="Gestión de contactos nuevos recibidos desde le formulario.">Contactos formulario</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/alumnos/gestion_alumnos.php" title="Gestión de alumnos matriculados en el centro.">Alumnos</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/lista_espera/listas_espera.php" title="Gestión de alumnos en lista de espera.">Lista de espera</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/listados/gestion_listados.php" title="Gestión de listados">Gestión de listados</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/contactos/mapa.php" title="Mapa de distribución geográfica de los alumnos.">Mapa alumnos</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="javascript:muestra_resumen_edades()" title="Resumen de alumnos y bajas por edades.">Resumen por edades</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="javascript:muestra_resumen_bebes()" title="Resumen bajas de bebés al cambiar de curso.">Bajas en cambio de curso</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/contacto/envios/" title="Exportar listados de emails y teléfonos">Exportar Emails y Teléfonos</font></td></tr>
<!--
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/club/" title="Inscripciones Club Lápices">Inscripciones Club</font></td></tr>-->
<?php 
if ($nivel_acceso==4) {
?>
<!--
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/centros/informe_ocupacion_aulas.php" title="Informe de ocupación de las aulas.">Informe ocupación</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/centros/informe_ocupacion_aulas_ampliado.php" title="Informe de ocupación de las aulas.">Informe ocupación ampliado</font></td></tr>-->
<?php 
}
?>
</table>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Mensajes Padres</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/mensajes/bandeja.php" title="Bandeja de entrada padres">Bandeja de entrada</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/mensajes/bandeja.php?destino=1" title="Bandeja de entrada padres">Bandeja de entrada coordinadora</font></td></tr>
</table>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Programación</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos_pruebas/gestion_programaciones.php" title="Gestión de programaciones">Gestión de programaciones</font></td></tr>
<?php 
}

if (($nivel_acceso==4) or ($zona_facturacion==1)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Facturación Servicios</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/facturacion/gestion_facturacion.php" title="Emisión y control de facturas emitidas a los alumnos matriculados en el centro.">Facturación</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/caja/listado_caja.php" title="Gestión de ingresos y gastos.">Caja</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="javascript:muestra_recibos()" title="Listado recibos firmados hoy">Recibos firmados</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos_pruebas/ocupacion_valorada.php" title="" target="_blank">Informe ocupación valorado</font></td></tr>
</table>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Renovaciones</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/aulas/calendario_control_renovaciones.php" title="Control de asistencia con renovaciones de alumnos." target="_blank">Calendario de renovaciones</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/pdf/recibos_hoy.php" title="Imprimir todos los recibos de hoy" target="_blank">Imprimir recibos de hoy</font></td></tr>
</table>
<?php 
}
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Entregas de material</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/material/gestion_material.php" title="Gestión de las reservas del material a los alumnos matriculados en el centro">Reservas</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/material/control_entregas.php" title="Gestión, cobro y facturación de las entregas del material reservado previamente a los alumnos matriculados en el centro.">Entregas</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/material/ventas_material.php" title="Resumen de las reservas y entregas del material a los alumnos.">Resumen</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/material/ventas_material_viejosynuevos.php" title="Resumen de las reservas y entregas del material textil por sexo de los alumnos.">Resumen Niños/niñas</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/material/ventas_material_comparado.php" title="Resumen de las entregas del material textil por curso.">Ventas material por curso</font></td></tr>
</table>
<?php 
if (($nivel_acceso==4) or ($zona_aulas==1)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Grupos</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/centros/gestion_aulas.php" title="Listado de todas las aulas del centro.">Listado alumnos por niveles</font></td></tr>
<!--<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos/" target="_blank" title="Listado de todas las aulas del centro.">Horario Grupos (VIEJO)</font></td></tr>-->
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos_pruebas/" target="_blank" title="Listado de todas las aulas del centro.">Horario Grupos</font></td></tr>
<!--<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos_lapices/" target="_blank" title="Listado de todas las aulas del centro.">Horario Grupos Guarderías (VIEJO)</font></td></tr>-->
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos_lapices_nuevo/" target="_blank" title="Listado de todas las aulas del centro.">Horario Grupos Guarderías</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos_externos/" target="_blank" title="Listado de todas las aulas del centro.">Horario Grupos Externos</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/aulas/calendario_control.php" title="Gestión de asistencia de los alumnos.">Asistencia</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/grupos_pruebas/ocupacion.php" title="" target="_blank">Informe ocupación</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/centros/listado_consentimiento.php" title="" target="_blank">Consentimientos redes sociales</font></td></tr>		
<?php 

if (($nivel_acceso==4) or ($zona_mensajes==1)) {
?>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/herramientas/enviar_mensaje.php" title="Enviar mensaje a los padres.">Mensaje padres</font></td></tr>
<?php 
}
?>
</table>
<?php 
}

if ((($nivel_acceso==4) or ($zona_gabinete==1)) and ($gabinete_activo==true)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Gabinete</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/gabinete/gestion_aulas.php" title="Gestión de informes.">Pruebas</font></td></tr>
</table>
<?php 
}

if ((($nivel_acceso==4) or ($zona_comedor==1)) and ($comedor_activo==true)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/cocina/calendario_mes_comedor.php" title="Listados de los menús que se deben preparar.">Listados</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/aulas/calendario_mes_comedor.php" title="Control de asistencia de los alumnos al comedor.">Asistencia</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/menu/gestion_menu.php" title="Elaboración de menús.">Menús</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/cocina/preparacion_platos/" title="Control de productos empleados en la preparación de los platos del menú.">Preparación</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/menu/menus_mes.php" title="Imprimir menús del mes.">Imprimir menús</font></td></tr>
</table>
<?php 
}

if (($nivel_acceso==4) or ($zona_almacen==1)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Servicios y material</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/facturacion/gestion_familias.php" title="Mantenimiento de tarifas de servicios y suministros para aulas y cocina.">Servicios</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/almacen/gestion_almacen.php" title="Gestión de suministros para las aulas, recepción de pedidos de material.">Suministros</font></td></tr>
<!--
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/cocina/gestion_cocina.php" title="Gestión de suministros para la cocina, recepción de pedidos de suministros.">Cocina</font></td></tr>-->
</table>
<?php 
}

if (($nivel_acceso==4) or ($zona_incidencias==1)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Incidencias</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/incidencias/menu_incidencias.php" title="Gestión de incidencias educativas, de asistencia, accidentes, material, etc.">Incidencias</font></td></tr>
</table>
<?php 
}
if (($nivel_acceso==4) or ($zona_personal==1)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Personal</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/facturacion/gestion_educadoras.php" title="Gestión de horarios y asistencia del personal de la escuela.">Gestión de personal</font></td></tr>
</table>
<?php 
}
if (($nivel_acceso==4) or ($zona_pedidos==1)) {
?>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Pedidos centro</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/almacen/pedidos_centro.php" title="Validación de pedidos de material del centro.">Validar pedidos del centro</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/almacen/calendario_pedidos_centro.php" title="Consulta de pedidos de material del centro ya tramitados.">Consultar pedidos tramitados</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/almacen/calendario_pedidos_entregados.php" title="Listas de entrega de material.">Listas de entrega de material grupos</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/gestion/centros/gestion_almacenes.php" title="Listado de departamentos NO docentes.">Departamentos</font></td></tr>
<?php 
if (($nivel_acceso==4) or ($zona_compras==1)) {
?>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Pedidos agrupados</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/almacen/pedidos_escuelas.php" title="Validación de pedidos de material.">Validar pedidos</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="/almacen/calendario_pedidos_empresa.php" title="Consulta de pedidos de material ya tramitados.">Consultar pedidos tramitados</font></td></tr>
<?php 
}
?>
</table>

<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef"><td><font size=3 face="<?= $fuente_general; ?>"><b>Soporte técnico</b></font></td></tr>
<tr bgcolor="#ffffff"><td><font size=2 face="<?= $fuente_general; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a href="http://www.teamviewer.com/link/?url=505374&id=1177194111" title="Descarga aplicación para soporte técnico">Aplicación Soporte</font></td></tr>
</table>

<?php 
}
?>
<?php  include("calendario/calendario_mes_def.php"); ?>
<?php  include("calendario/lista_citas_padres.php"); ?>
</td>
<td valign="top" colspan=2 width="<?= $ancho_pagina_total-185; ?>">
<?php 
if (($nivel_acceso==3) or ($nivel_acceso==4)) {

	// include("agenda/alertas_mensajes.php");
	include("agenda/guardias.php");
	include("agenda/cumples_hoy.php");
	include "agenda/faltas_recuperables.php";
        ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
                <h4>Alerta de faltas reiteradas</h4>
                <button class="btn btn-warning btn-xs" onclick="muestra_faltas_reiteradas()">Ver listado de faltas reiteradas</button>
        </div>
        <?php
	// include "ausencias_alumnos.php";
	include "informaciones.php";
	include "ausencias.php";
	
}

include("agenda/actividades_hoy.php");        
?>
</td>
</tr>
<?php 
// Seleccion de centro

} else {
?>
<table bgcolor="#efefef" border=0 width=100%><tr>
<td align="center">
<font size=2><b>Selecciona el centro que deseas gestionar</b></font>
<br><br>
<?php 
$consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");

if (db_num_rows($consulta_centros)==0) {
        ?>
        No exististe ningún centro en la base de datos, pulsa el botón para intoducir el primero.<br><br>
        <input type="button" onclick="window.location.href='/gestion/centros/editar_centro.php';" value="Dar de alta un centro">
        <?php 
        } else {
        ?>
        <form name="centros" method="post" action="<?=$path_index;?>">
        <select name="id_centro_seleccionado" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <option value="">Selecciona el centro</option>
        <?php 
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select>
        </form>
        <?php 
        }
?>
<br><br>
<?php 
}
// Menu administrador
if ($nivel_acceso>=4) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>ADMINISTRACION</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                         // Menu administrador
                         $consulta_menu_admin=db_query("SELECT * FROM menu_administrador ORDER BY Orden");
                         
                         while($rsmenuadmin=db_fetch_array($consulta_menu_admin)) {
                         ?>
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><a href="<?= $rsmenuadmin["link"]?>"><b><?= $rsmenuadmin["Texto"]?></b></a></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?= $rsmenuadmin["texto_ayuda"]?></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                         </table>
                         <?php 
                ?>
                </td>
        </tr>
</table>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <font size=2 face="<?= $fuente_general; ?>"><b>Mis datos</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="middle">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Editar datos" onclick="window.location.href='<?=$url_web;?>/gestion/usuarios/datos_usuario.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Edición de tus datos personales, titulación y curriculum.</font></td>
                        </tr>
                        <tr valign="middle">
                                <td width="15%"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Contratos" onclick="window.location.href='<?=$url_web;?>/gestion/personal/ficha.php'" style="font-size:20px; width: 150; height: 48"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Consulta de tus contratos, calendario laboral, horas trabajadas y documentación de prevención de riesgos laborales.</font></td>
                        </tr>
                        </table>
                </td>
        </tr>
</table>
<?php 
} else {

if ($zona_inferior==1) {

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>ADMINISTRACION</b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <?php 
                         // Menu administrador
                         $consulta_menu_admin=db_query("SELECT * FROM menu_administrador WHERE nivel_acceso=3 ORDER BY Orden");

                         while($rsmenuadmin=db_fetch_array($consulta_menu_admin)) {
                         ?>
                        <tr valign="top">
                                <td><font size=2 face="<?= $fuente_general; ?>"><a href="<?= $rsmenuadmin["link"]?>"><b><?= $rsmenuadmin["Texto"]?></b></a></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?= $rsmenuadmin["texto_ayuda"]?></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                         </table>
                         <?php 
                ?>
                </td>
        </tr>
</table>
<?php 

}

 }

if (($nivel_acceso>=4) or ($usuario_multicentro==1)) {

if (strlen($centro_actual)!=0) {
$consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");

        ?>
        <form name="centros" method="post" action="<?=$path_index;?>">
        <font size=1><b>Cambio rápido de centro: </b></font><select name="id_centro_seleccionado" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <?php 
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select>
        </form>
<?php 
         }
 }

?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=35%><font size=2 face="<?= $fuente_general; ?>"><i>Escuela</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><i>Tiempo</i></font></td>
                </tr>
        <form name="control_asistencia">
        <?php 
        // Busca si ya está registrado
        $turno=2;

        $busca_registros_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."' ORDER BY hora_entrada") or die(db_error());

        if (db_num_rows($busca_registros_entrada)==0) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela<?=$centro_actual?></option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$id_centro_listado;?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        } else {
        $tiempo_total_jornada=0;
        while($datos_registro=db_fetch_array($busca_registros_entrada)) {
        $mostrar_nuevo=false;
        $consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_registro["id_centro_asistencia"]));
        $datos_centro_asistencia=db_fetch_array($consulta_centros);
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_centro_asistencia["nombre_centro"];?></font></td>
                <td align="center"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font></td>
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        $id_registro_comedor=$datos_registro["id_asistencia"];
        $estado_registro_comedor=$datos_registro["comedor"];
        $menu_comedor_educador=$datos_registro["menu_seleccionado_hoy"];
        $centro_asistencia_comedor=$datos_registro["id_centro_asistencia"];
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <?php 
        } else {
        $tiempo_total_jornada=$tiempo_total_jornada+(strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        $tiempo_permanencia_centro=(date("H",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]))-1).date(":i:s",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$tiempo_permanencia_centro;?></font></td>
        <?php 
        $mostrar_nuevo=true;
        }
        ?>
        </tr>
        <?php 
                }
        if ($mostrar_nuevo==true) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        }
        }
        $total_jornada=(date("H",$tiempo_total_jornada)-1).date(":i:s",$tiempo_total_jornada);
        ?>
        <tr bgcolor="#efefef">
                <td align="right" colspan=3><font size=2 face="<?= $fuente_general; ?>">Duración total de la jornada:</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$total_jornada;?></font></td>
        </tr>
        </form>
        </table>
                </td>
        </tr>
</table>
        <?php 
        if ($asistencia_comedor_activada==true) {
        ?>
        <table width=80%>
                <form name="asistencia_comedor_docente" method="post" action="/gestion/docentes/asistencia_comedor.php">
                <tr>
                <td align="center"><font size=2><b>Asistencia al comedor</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center">
                <font size=2><b>Me quedo a comer en... </b><select name="id_centro_comida" size=1 style="font-size: 10px">
                <option value="">Selecciona escuela</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php if ($centro_asistencia_comedor==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select>
<br></font>
                <input type="hidden" name="id_registro_comedor" value="<?=$id_registro_comedor?>">
                <font size=2><b>¿Te quedás a comer en la escuela? Selecciona menú</b>
                <select<?=$estado_comedor;?> name="menu_seleccionado_hoy" style="font-size: 10px" onchange="document.asistencia_comedor_docente.submit()">
                                        <option value="0"<?php  if ($menu_comedor_educador==0) { print " selected"; } ?>>No me quedo a comer</option>
                                        <?php 
                                        $consulta_datos_menus=db_query_params('SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha=? AND edad_ini_menu=17', 's', array((string)(date("Y-m-d")))) or die(db_error());
                                        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
                                        ?>
                                        <option value="<?=$datos_menus["id_menu"]?>"<?php  if ($datos_menus["id_menu"]==$menu_comedor_educador) { print " selected"; }?>><?=urldecode($datos_menus["nombre_menu"])?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select>
                                        </font></td>
                </tr>
                </form>
        </table>
        <script language="Javascript">
        function envia_formulario() {
                if (document.asistencia_comedor_docente.id_centro_comida.value!="") {
                        document.asistencia_comedor_docente.submit();
                        } else {
                        alert("Selecciona la escuela donde te quedas a comer.");
                        document.asistencia_comedor_docente.asistencia_comedor[0].click();
                        }
                }
        </script>
        <?php 
        }
        ?>
<br>
<?php 
 } else {

?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td>
<?php 
 // Docente
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr>
<td width="250" valign="top" bgcolor="#cfcfcf" align="left">
<table width="250" border=0 cellpadding=1 cellspacing=1>
<tr bgcolor="#efefef" colspan=2><td><font size=3 face="<?= $fuente_general; ?>" color="#800000"><b>Opciones</b></font></td></tr>
</table>
<table width="250" border=0 cellpadding=1 cellspacing=1>
<?php 
if ($nivel_acceso==2) {

?>
<tr bgcolor="#ffffff" align="center"><td><font size=1 face="<?= $fuente_general; ?>"><input<?=$estado_botones_fichar?> type="button" value="Control de Asistencia" onclick="window.location.href='/aulas/calendario_control.php'" style="font-size:20px; width: 248; height: 48"><br>Gestión de asistencia de los alumnos.</font></td></tr>
<tr bgcolor="#ffffff" align="center"><td><font size=1 face="<?= $fuente_general; ?>"><input type="button" value="Horario grupos" onclick="window.open('/gestion/grupos_pruebas/')" style="font-size:20px; width: 248; height: 48"><br>Horarios grupos clientes Neokids</font></td></tr>
<tr bgcolor="#ffffff" align="center"><td><font size=1 face="<?= $fuente_general; ?>"><input type="button" value="Horario Guarderías" onclick="window.open('/gestion/grupos_lapices_nuevo/')" style="font-size:20px; width: 248; height: 48"><br>Horarios grupos alumnos escuelas infantiles (Lápices)</font></td></tr>
<tr bgcolor="#ffffff" align="center"><td><font size=1 face="<?= $fuente_general; ?>"><input type="button" value="Horario Externos" onclick="window.open('/gestion/grupos_externos/')" style="font-size:20px; width: 248; height: 48"><br>Horarios grupos alumnos externos (El Cole, Peter Pan, etc.)</font></td></tr>
<?php 
}
?>
<tr bgcolor="#ffffff" align="center"><td><font size=1 face="<?= $fuente_general; ?>"><input<?=$estado_botones_fichar?> type="button" value="Incidencias" onclick="window.location.href='<?=$url_web;?>/gestion/incidencias/menu_incidencias.php'" style="font-size:20px; width: 248; height: 48"><br>Gestión de incidencias educativas, de asistencia, accidentes, material, etc.</font></td></tr>
<tr bgcolor="#ffffff" align="center"><td><font size=1 face="<?= $fuente_general; ?>"><input<?=$estado_botones_fichar?> type="button" value="Editar mis datos" onclick="window.location.href='<?=$url_web;?>/gestion/usuarios/datos_usuario.php'" style="font-size:20px; width: 248; height: 48"><br>Edición de tus datos personales, titulación y curriculum.</font></td></tr>
<tr bgcolor="#ffffff" align="center"><td><font size=1 face="<?= $fuente_general; ?>"><input<?=$estado_botones_fichar?> type="button" value="Mis Contratos" onclick="window.location.href='<?=$url_web;?>/gestion/personal/ficha.php'" style="font-size:20px; width: 248; height: 48"><br>Consulta de tus contratos, calendario laboral, horas trabajadas y documentación de prevención de riesgos laborales.</font></td></tr>
<tr bgcolor="#ffffff"><td><font size=1 face="<?= $fuente_general; ?>"><input<?=$estado_botones_fichar?> type="button" value="Gestión de programaciones" onclick="window.location.href='/gestion/grupos_pruebas/gestion_programaciones.php'" style="font-size:20px; width: 248; height: 48"><br>
Gestionas las programaciones de tus grupos</font></td></tr>
</table>
<?php  include("calendario/calendario_mes_def.php"); ?>
</td>
<td valign="top" colspan=2 width="<?= $ancho_pagina_total-185; ?>">
<?php
if ($nivel_acceso==2) {
	
	include("agenda/alertas_mensajes_educadoras.php");
	
}	
	include("agenda/guardias.php");
	include("agenda/actividades_hoy.php");
?>
</td>
</tr>
</table>

</td>
</tr>
<tr valign="top">
<td>
<?php 
if ($usuario_multicentro==1) {

// if (strlen($centro_actual)!=0) {
$consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");

        ?><div align="center">
        <form name="centros" method="post" action="<?=$path_index;?>">
        <font size=1><b>Cambio rápido de centro: </b></font><select name="id_centro_seleccionado" size=1 style="font-size: 10px" onchange="document.centros.submit()">
        <?php 
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select>
        </form></diV>
<?php 
//         }
 }

if ($nivel_acceso==11) {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=35%><font size=2 face="<?= $fuente_general; ?>"><i>Escuela</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><i>Tiempo</i></font></td>
                </tr>
        <form name="control_asistencia">
        <?php 
        // Busca si ya está registrado
        $turno=2;

        $busca_registros_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."' ORDER BY hora_entrada") or die(db_error());

        if (db_num_rows($busca_registros_entrada)==0) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        } else {
        $tiempo_total_jornada=0;
        while($datos_registro=db_fetch_array($busca_registros_entrada)) {
        $mostrar_nuevo=false;
        $consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_registro["id_centro_asistencia"]));
        $datos_centro_asistencia=db_fetch_array($consulta_centros);
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_centro_asistencia["nombre_centro"];?></font></td>
                <td align="center"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font></td>
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        $id_registro_comedor=$datos_registro["id_asistencia"];
        $estado_registro_comedor=$datos_registro["comedor"];
        $menu_comedor_educador=$datos_registro["menu_seleccionado_hoy"];
        $centro_asistencia_comedor=$datos_registro["id_centro_asistencia"];
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <?php 
        } else {
        $tiempo_total_jornada=$tiempo_total_jornada+(strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        $tiempo_permanencia_centro=(date("H",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]))-1).date(":i:s",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$tiempo_permanencia_centro;?></font></td>
        <?php 
        $mostrar_nuevo=true;
        }
        ?>
        </tr>
        <?php 
                }
        if ($mostrar_nuevo==true) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        }
        }
        $total_jornada=(date("H",$tiempo_total_jornada)-1).date(":i:s",$tiempo_total_jornada);
        ?>
        <tr bgcolor="#efefef">
                <td align="right" colspan=3><font size=2 face="<?= $fuente_general; ?>">Duración total de la jornada:</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$total_jornada;?></font></td>
        </tr>
        </form>
        </table>
                </td>
        </tr>
</table>
<?php 
} else {
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font>
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=35%><font size=2 face="<?= $fuente_general; ?>"><i>Escuela</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><i>Tiempo</i></font></td>
                </tr>
        <form name="control_asistencia">
        <?php 
        // Busca si ya está registrado
        $turno=2;

        $busca_registros_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$idUsuario." AND turno=$turno AND fecha='".date("Y-m-d")."' ORDER BY hora_entrada") or die(db_error());

        if (db_num_rows($busca_registros_entrada)==0) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela<?=$centro_actual?></option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$id_centro_listado;?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        } else {
        $tiempo_total_jornada=0;
        while($datos_registro=db_fetch_array($busca_registros_entrada)) {
        $mostrar_nuevo=false;
        $consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_registro["id_centro_asistencia"]));
        $datos_centro_asistencia=db_fetch_array($consulta_centros);
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$datos_centro_asistencia["nombre_centro"];?></font></td>
                <td align="center"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font></td>
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        $id_registro_comedor=$datos_registro["id_asistencia"];
        $estado_registro_comedor=$datos_registro["comedor"];
        $menu_comedor_educador=$datos_registro["menu_seleccionado_hoy"];
        $centro_asistencia_comedor=$datos_registro["id_centro_asistencia"];
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <?php 
        } else {
        $tiempo_total_jornada=$tiempo_total_jornada+(strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        $tiempo_permanencia_centro=(date("H",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]))-1).date(":i:s",strtotime($datos_registro["hora_salida"])-strtotime($datos_registro["hora_entrada"]));
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_salida"]));?></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$tiempo_permanencia_centro;?></font></td>
        <?php 
        $mostrar_nuevo=true;
        }
        ?>
        </tr>
        <?php 
                }
        if ($mostrar_nuevo==true) {
        ?>
        <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><select name="id_centro_asistencia" size=1 style="font-size: 10px">
                <option value="0">Seleccionar Escuela</option>
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>)"></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" disabled></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
        <?php 
        }
        }
        $total_jornada=(date("H",$tiempo_total_jornada)-1).date(":i:s",$tiempo_total_jornada);
        ?>
        <tr bgcolor="#efefef">
                <td align="right" colspan=3><font size=2 face="<?= $fuente_general; ?>">Duración total de la jornada:</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$total_jornada;?></font></td>
        </tr>
        </form>
        </table>
                </td>
        </tr>
</table>
        <?php 
        if ($asistencia_comedor_activada==true) {
        ?>
        <table width=80%>
                <form name="asistencia_comedor_docente" method="post" action="/gestion/docentes/asistencia_comedor.php">
                <input type="hidden" name="id_centro_comida" value="">
                <tr>
                <td align="center"><font size=2><b>Asistencia al comedor</b></font></td>
                </tr>
                <tr bgcolor="#FFFFFF">
                <td align="center">
                <input type="hidden" name="id_registro_comedor" value="<?=$id_registro_comedor?>">
                <font size=2><b>¿Te quedás a comer en la escuela? </b>
                <font size=2><b>¿Te quedás a comer en la escuela? Selecciona menú</b>
                <select<?=$estado_comedor;?> name="menu_seleccionado_hoy" style="font-size: 10px" onchange="document.asistencia_comedor_docente.submit()">
                                        <option value="0"<?php  if ($menu_comedor_educador==0) { print " selected"; } ?>>No me quedo a comer</option>
                                        <?php 
                                        $consulta_datos_menus=db_query_params('SELECT * FROM menu_menus,menu_calendario WHERE menu_menus.id_menu=menu_calendario.id_menu AND menu_calendario.fecha=? AND edad_ini_menu=17', 's', array((string)(date("Y-m-d")))) or die(db_error());
                                        while($datos_menus=db_fetch_array($consulta_datos_menus)) {
                                        ?>
                                        <option value="<?=$datos_menus["id_menu"]?>"<?php  if ($datos_menus["id_menu"]==$menu_comedor_educador) { print " selected"; }?>><?=urldecode($datos_menus["nombre_menu"])?></option>
                                        <?php 
                                        }
                                        ?>
                                        </select>
                </td>
                </tr>
                </form>
        </table>
        <?php 
        }
        ?>
                </td>
        </tr>
</table>
<?php 
}
?>
</td>
</tr>
</table><br>
 <?php 
 }
?>
</center></div>
<script language="Javascript">
function registro_entrada_escuela(turno) {
        // Comprueba si se ha seleccionado la escuela
        if (document.control_asistencia.id_centro_asistencia.value=="0") {
                alert("No has seleccionado la escuela...");
                } else {
                window.location.href='/gestion/docentes/registro_entrada.php?id_centro_asistencia='+document.control_asistencia.id_centro_asistencia.value+'&turno='+turno;
                }
        }
        
function registro_entrada(turno) {
        window.location.href='/gestion/docentes/registro_entrada.php?turno='+turno;
        }
function registro_salida(id_registro) {
        window.location.href='/gestion/docentes/registro_salida.php?id_registro='+id_registro;
        }
</script>
<?php 
}
db_close($db_m1);
?>
<script language="Javascript">

function muestra_recibos() {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Listado recibos firmados hoy.</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/facturacion/listado_recibos_cobrados.php');        
        }

function muestra_temperaturas() {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Registro de temperaturas</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/api/registro_temperaturas.php');        
        }

function muestra_guardias() {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Calendario de Guardias</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/gestion/guardias/ver_guardias.php?fecha=<?=$fecha_time_stamp?>');        
        }

function muestra_faltas() {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Listado de faltas recuperables pendientes de tramitar.</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/gestion/grupos_pruebas/listado_faltas_recuperacion.php');        
        }

function muestra_faltas_reiteradas() {
    $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Listado de faltas reiteradas.</h4>');
    $('#grupoModal').modal('show');

    // Mostrar el mensaje de carga
    $('#loading-message').show();
    $('#detalles').hide(); // Ocultar el iframe hasta que termine de cargar

    // Cambiar el src del iframe
    $('#detalles').attr('src', '/gestion/grupos_pruebas/listado_faltas_reiteradas.php?fecha_control=<?=$fecha_time_stamp?>&fecha_inicio_curso=<?=$fecha_inicio_curso?>&fecha_fin_curso=<?=$fecha_fin_curso?>');

    // Configurar el evento onload del iframe
    $('#detalles').on('load', function () {
        // Ocultar el mensaje de carga
        $('#loading-message').hide();
        $('#detalles').show(); // Mostrar el iframe cuando esté listo
    });
}
        
function muestra_recuperaciones() {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Programación de recuperación de faltas.</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/gestion/grupos_pruebas/listado_faltas_programacion.php');        
        }        

function muestra_resumen_edades() {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Resumen alumnos y bajas por edades</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/gestion/centros/listado_edades.php');        
        } 
        
function muestra_resumen_bebes() {
        $('#nombre_grupo').html('<h4 class="modal-title" id="grupoModalLabel">Resumen bajas de bebes al inicio del curso</h4>');
        $('#grupoModal').modal('show');
        $('#detalles').attr('src', '/gestion/centros/listado_bebes.php');        
        }         

function chequea(id_cumple) {

	var href = "/inc/cambia_cumple.php?id_cumple="+id_cumple;

	var respuesta = $.ajax ({url: href}); 
	
	respuesta.always(function() {
				
		var textoRespuesta = respuesta.responseText;
				
		if( textoRespuesta.substr(0, 2) != "ok" ) {
				alert(textoRespuesta);						
			} else {
				alert(textoRespuesta.substr(4));
			}	
			
				
		});

}

$( document ).ready(function() {

	$('[data-toggle="tooltip"]').tooltip();

    $('[id^=comfirmacion-cumple-]').change(function() {
      
		var href = "/inc/cambia_cumple.php?id_cumple="+$(this).val();

		var respuesta = $.ajax ({url: href}); 
	
		respuesta.always(function() {
				
			var textoRespuesta = respuesta.responseText;
				
			if( textoRespuesta.substr(0, 2) != "ok" ) {
					alert(textoRespuesta);						
				} else {
					// alert(textoRespuesta.substr(4));
				}	
							
			});      
      
    })

});
        
</script>
<!-- Modal -->
<style type="text/css">
        #loading-message {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                height: 100%;
        }
</style>
<div class="modal modal-wide fade" id="grupoModal" tabindex="-1" role="dialog" aria-labelledby="grupoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div id="nombre_grupo"><h4 class="modal-title" id="grupoModalLabel">Título</h4></div>
      </div>    
      <div class="modal-body">
        <!-- Contenedor para el mensaje de carga -->
        <div id="loading-message" style="text-align: center; display: none;">
                <p>Cargando, por favor espere unos segundos...</p>
                <div class="spinner-border" role="status">
                <span class="sr-only">Cargando...</span>
                </div>
        </div>
        <!-- Iframe con el contenido -->        
        <iframe src="" name="detalles" id="detalles" width=100% height=550 frameborder=0></iframe>
      </div>
    </div>
  </div>
</div>

