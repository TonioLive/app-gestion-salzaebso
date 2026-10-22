<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica_admin.php");
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
window.moveTo(0,0);
// window.sizeTo(screen.width,screen.height);
window.focus();

function imprimir() {
        document.getElementById('botonera').style.display='none';
        window.print();
        document.getElementById('botonera').style.display='block';
        }
        
//-->
</SCRIPT>

</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<?php 
$id_alumno=$_GET["id_alumno"];
$fecha_sel=$_GET["fecha"];
$mes_sel=date("n",$fecha_sel);
$ano_sel=date("Y",$fecha_sel);


$busca_datos_alumno=db_query_params('SELECT * FROM alumnos WHERE id_alumno=?', 's', array($id_alumno));
$datos_del_alumno=db_fetch_array($busca_datos_alumno);

$id_aula=$datos_del_alumno["aula"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);


$busca_datos_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_del_alumno["id_centro"])) or die(db_error());
$datos_centro=db_fetch_array($busca_datos_centro);

$hora_entrada_centro_man=$datos_centro["hora_entrada1"];
$hora_salida_centro_man=$datos_centro["hora_salida1"];
$hora_entrada_centro_tar=$datos_centro["hora_entrada2"];
$hora_salida_centro_tar=$datos_centro["hora_salida2"];
$division_horas=($datos_centro["division_horas"]/60);
$periodo_gracia=($datos_centro["periodo_gracia"]*60);

// Tipos de comedor
$tipo_de_comedor[0]="Desconocido";
$tipo_de_comedor[1]="Si";
$tipo_de_comedor[2]="No";
$tipo_de_comedor[3]="Régimen";

// Tipos de menu
$tipo_de_menu[0]="Desconocido";
$tipo_de_menu[1]="Pequeños";
$tipo_de_menu[2]="Mayores";
$tipo_de_menu[3]="Colegial";
$tipo_de_menu[4]="Toma";
$tipo_de_menu[5]="Bebe";

?>
<SCRIPT LANGUAGE=javascript>
<!--

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.listado_alumnos.cambio_aula"+id+".value");
                window.location.href='cambio_aula.php?na='+opcion_seleccionada+'&a='+id;
                }
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width=800,height="+screen.height+"');
        }
//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Detalles de asistencia y horas extra consumidas por alumno del aula <i><?=$datos_aula["nombre"];?></i> durante el mes de <?=$nombre_mes[$mes_sel];?> de <?=$ano_sel;?><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 
$busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=? ORDER BY Apellido1, Apellido2, Nombre', 's', array($id_alumno)) or die(db_error());

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr bgcolor="#efefef">
        <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Días asistidos</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Comedores</b></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Horas extra Consumidas</b></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Horas a facturar</b></font></td>
        </tr>
        <form name="listado_alumnos" method="POST" action="chequear_horas_no.php">
        <?php 
        $horas_comida=round(((strtotime($hora_entrada_centro_tar)-strtotime($hora_salida_centro_man))/3600),2);
        if ($horas_comida<0) {
                $horas_comida=0;
                }
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        $id_aula=$listado_alumnos["aula"];
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
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
                
        // Consulta servicios asistencia durante el mes seleccionado
        $consulta_asistencia=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND month(fecha)=? AND year(fecha)=? AND no_facturar=0;', 'ssss', array($listado_alumnos["id_usuario"], $id_aula, $mes_sel, $ano_sel));
        $total_horas_extra=0;
        $total_horas_extra_fact=0;
        $dia_actual=0;
        $contador_dias=0;
        $contador_comedores=0;
        while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
        
                if ($dia_actual!=$datos_asistencia["fecha"]) {
                        $dia_actual=$datos_asistencia["fecha"];
                        $contador_dias++;
                        }
                        
                if ($datos_asistencia["turno"]==0) {
                        $hora_entrada_centro=strtotime($hora_entrada_centro_man);
                        $hora_salida_centro=strtotime($hora_salida_centro_man);
                        } else {
                        $hora_entrada_centro=strtotime($hora_entrada_centro_tar);
                        $hora_salida_centro=strtotime($hora_salida_centro_tar);
                        }
                        
                $hora_entrada11=$datos_asistencia["hora_entrada"];
                if ($hora_entrada11=="") {
                        $hora_entrada11=intval($hora_entrada_centro);
                        } else {
                        $hora_entrada11=intval(strtotime($datos_asistencia["hora_entrada"]));
                        }
                $diferencia_entrada=intval($hora_entrada_centro)-$hora_entrada11;

                if ($diferencia_entrada>$periodo_gracia) {
                        $diferencia_entrada=round($diferencia_entrada/36)/100;
                        $diferencia_entrada=round((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_entrada=0;
                        }
                        
                $hora_salida11=$datos_asistencia["hora_salida"];
                if ($hora_salida11=="") {
                        $hora_salida11=intval($hora_salida_centro);
                        } else {
                        $hora_salida11=intval(strtotime($datos_asistencia["hora_salida"]));
                        }
                        
                $diferencia_salida=$hora_salida11-intval($hora_salida_centro);

                if ($diferencia_salida>$periodo_gracia) {
                        $diferencia_salida=round($diferencia_salida/36)/100;
                        $diferencia_salida=round((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_salida=0;
                        }
                $diferencia_entrada1=$diferencia_entrada;
                $$diferencia_salida1=$diferencia_salida;
                // Si se queda a comer no se cobran horas
                        if ($datos_asistencia["turno"]==0) {
                                if ($datos_asistencia["hora_entrada_c"]!="") {
                                        $coletila="<font color=red> * Comedor</font>";
                                        $contador_comedores++;
                                        $diferencia_salida=$diferencia_salida-$horas_comida;
                                        if ($diferencia_salida<0) {
                                                $diferencia_salida=0;
                                                }
                                        }
                                } else {
                                // Comprueba si ha entrado a comer en otro turno
                                $busca_otro_turno=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND fecha=? AND turno=0;', 'sss', array($datos_asistencia["id_alumno"], $datos_asistencia["id_servicio"], (string)(date("Y-m-d", strtotime($datos_asistencia["fecha"])))));
                                if (db_num_rows($busca_otro_turno)!=0) {
                                        $datos_otro_turno=db_fetch_array($busca_otro_turno);
                                        if ($datos_otro_turno["hora_entrada_c"]!="") {
                                                // Si el niño ha venido a comer y ha entrado por la mañana, no le cobro horas extra
                                                $diferencia_entrada=0;
                                                /*
                                                $diferencia_entrada=$diferencia_entrada-$horas_comida;
                                                if ($diferencia_entrada<0) {
                                                        $diferencia_entrada=0;
                                                        }
                                                */
                                                }
                                        }
                                }

                if ($diferencia_entrada1<$diferencia_entrada) {
                        $diferencia_entrada=$diferencia_entrada1;
                        }
                if ($diferencia_salida1<$diferencia_salida) {
                        $diferencia_salida=$$diferencia_salida1;
                        }

                // Busca bonos horas extra contratados durante todo el año
                $busca_bonos1=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=1 AND periodicidad=1 AND (fecha_inicio<=?) AND (fecha_fin>=?)', 'sss', array($listado_alumnos["id_alumno"], (string)($ano_sel) . '-' . (string)($mes_sel) . '-1', (string)($ano_sel) . '-' . (string)($mes_sel) . '-1')) or die(db_error());
                $numero_bonos1=0;
                while($bonos1=db_fetch_array($busca_bonos1)) {
                        $numero_bonos1=$numero_bonos1+$bonos1["cantidad"];
                        }
                // Busca bonos horas extra contratados para este mes
                $busca_bonos2=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=1 AND periodicidad=2 AND  (month(fecha_inicio)=?) AND (year(fecha_inicio)=?)', 'sss', array($listado_alumnos["id_alumno"], $mes_sel, $ano_sel)) or die(db_error());
                $numero_bonos2=0;
                while($bonos2=db_fetch_array($busca_bonos2)) {
                        $numero_bonos2=$numero_bonos2+$bonos2["cantidad"];
                        }

                $horas_extra_hoy=($diferencia_entrada+$diferencia_salida);
                $horas_extra_hoy_fact=$horas_extra_hoy-$numero_bonos1-$numero_bonos2;

                $total_horas_extra=$total_horas_extra+$horas_extra_hoy;
                if ($horas_extra_hoy_fact>0) {
                        $total_horas_extra_fact=$total_horas_extra_fact+$horas_extra_hoy_fact;
                        }
                }
        // Busca horas extra individuales
        $busca_bonos3=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=2 AND periodicidad=2 AND  (month(fecha_inicio)=?) AND (year(fecha_inicio)=?)', 'sss', array($listado_alumnos["id_alumno"], $mes_sel, $ano_sel)) or die(db_error());
        $numero_bonos3=0;
        while($bonos3=db_fetch_array($busca_bonos3)) {
                        $numero_bonos3=$numero_bonos3+$bonos3["cantidad"];
                        }
        $total_horas_extra_fact=$total_horas_extra_fact-$numero_bonos3;
        if ($total_horas_extra_fact<0) {
                $total_horas_extra_fact=0;
                }
        // Convierte a hh:mm
        $horas_extra_mostrar=$total_horas_extra;
        $horas_extra_mostrar_fact=$total_horas_extra_fact;

        $horas_extra1=round($horas_extra_mostrar,0);
        if ($horas_extra1>$horas_extra_mostrar) {
                        $horas_extra1=$horas_extra1-1;
                        }
        $minutos_extra1="00".(($horas_extra_mostrar-$horas_extra1)*60);
        $minutos_extra1=substr($minutos_extra1,strlen($minutos_extra1)-2,2);
        $horas_extra1="00".$horas_extra1;
        $horas_extra1=substr($horas_extra1,strlen($horas_extra1)-2,2);
        $horas_extra_mostrar=$horas_extra1.":".$minutos_extra1;
                
        $horas_extra2=round($horas_extra_mostrar_fact,0);
        if ($horas_extra2>$horas_extra_mostrar_fact) {
                        $horas_extra2=$horas_extra2-1;
                        }

        $minutos_extra2="00".(($horas_extra_mostrar_fact-$horas_extra2)*60);
        $minutos_extra2=substr($minutos_extra2,strlen($minutos_extra2)-2,2);
        $horas_extra2="00".$horas_extra2;
        $horas_extra2=substr($horas_extra2,strlen($horas_extra2)-2,2);
        $horas_extra_mostrar_fact=$horas_extra2.":".$minutos_extra2;
        ?>
        <tr>
        <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$contador_dias?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$contador_comedores?></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$horas_extra_mostrar;?></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$horas_extra_mostrar_fact;?></font></td>
        </tr>
        <?php 
        // Listado de horas extras
        ?>
        <tr valign="top">
                <td colspan=2 align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>¿Comedor contratado?</b></font></td>
                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$tipo_de_comedor[$listado_alumnos["comedor"]];?></font></td>
                <td colspan=2 align="right" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de menú</b></font></td>
                <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><?=$tipo_de_menu[$listado_alumnos["tipo_menu_comedor"]];?></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="center" colspan=8><font size=1><b>Listado de asistencia y horas extra consumidas</b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Turno</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Entrada</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Salida</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Ent. Comedor</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Sal. Comedor</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Horas extra</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>No fact.</b></font></td>
        </tr>
        <input type="hidden" name="id_usuario" value="<?=$listado_alumnos["id_usuario"];?>">
        <input type="hidden" name="id_aula" value="<?=$id_aula;?>">
        <input type="hidden" name="id_alumno" value="<?=$id_alumno;?>">
        <input type="hidden" name="mes" value="<?=$mes_sel;?>">
        <input type="hidden" name="ano" value="<?=$ano_sel;?>">
        <?php 
        // Consulta servicios asistencia durante el mes seleccionado
        $consulta_asistencia=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND month(fecha)=? AND year(fecha)=?;', 'ssss', array($listado_alumnos["id_usuario"], $id_aula, $mes_sel, $ano_sel));
        while($datos_asistencia=db_fetch_array($consulta_asistencia)) {
                $horas_extra_hoy=0;
                if ($datos_asistencia["turno"]==0) {
                        $nombre_turno="Mañana";
                        $hora_entrada_centro=strtotime($hora_entrada_centro_man);
                        $hora_salida_centro=strtotime($hora_salida_centro_man);
                        } else {
                        $nombre_turno="Tarde";
                        $hora_entrada_centro=strtotime($hora_entrada_centro_tar);
                        $hora_salida_centro=strtotime($hora_salida_centro_tar);
                        }
                $diferencia_entrada=intval($hora_entrada_centro)-intval(strtotime($datos_asistencia["hora_entrada"]));

                if ($diferencia_entrada>$periodo_gracia) {
                        $diferencia_entrada=round($diferencia_entrada/36)/100;
                        $diferencia_entrada=round((100*$diferencia_entrada)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_entrada=0;
                        }

                $diferencia_salida=intval(strtotime($datos_asistencia["hora_salida"]))-intval($hora_salida_centro);

                if ($diferencia_salida>$periodo_gracia) {
                        $diferencia_salida=round($diferencia_salida/36)/100;
                        $diferencia_salida=round((100*$diferencia_salida)/($division_horas*100))*$division_horas;
                        } else {
                        $diferencia_salida=0;
                        }

                // Si se queda a comer no se cobran horas
                $coletila="";
                $hora_entrada_comedor="-";
                $hora_salida_comedor="-";
                        if ($datos_asistencia["turno"]==0) {
                                if ($datos_asistencia["hora_entrada_c"]!="") {
                                        $coletila="<font color=red> * Comedor</font>";
                                        $diferencia_salida=$diferencia_salida-$horas_comida;
                                        if ($diferencia_salida<0) {
                                                $diferencia_salida=0;
                                                }
                                        $hora_entrada_comedor=date("H:i:s",strtotime($datos_asistencia["hora_entrada_c"]));
                                        if ($datos_asistencia["hora_salida_c"]!="") {
                                                $hora_salida_comedor=date("H:i:s",strtotime($datos_asistencia["hora_salida_c"]));
                                                } else {
                                                $hora_salida_comedor="-";
                                                }
                                        }
                                } else {
                                // Comprueba si ha entrado a comer en otro turno
                                $busca_otro_turno=db_query_params('SELECT * FROM servicios_consumidos WHERE id_alumno=? AND id_servicio=? AND fecha=? AND turno=0;', 'sss', array($datos_asistencia["id_alumno"], $datos_asistencia["id_servicio"], (string)(date("Y-m-d", strtotime($datos_asistencia["fecha"])))));

                                if (db_num_rows($busca_otro_turno)!=0) {
                                        $datos_otro_turno=db_fetch_array($busca_otro_turno);
                                        if ($datos_otro_turno["hora_entrada_c"]!="") {
                                                // Si el niño ha venido a comer y ha entrado por la mañana, no le cobro horas extra
                                                $diferencia_entrada=0;
                                                /*
                                                $diferencia_entrada=$diferencia_entrada-$horas_comida;
                                                if ($diferencia_entrada<0) {
                                                        $diferencia_entrada=0;
                                                        }
                                                */
                                                $hora_entrada_comedor=date("H:i:s",strtotime($datos_otro_turno["hora_entrada_c"]));
                                                if ($datos_otro_turno["hora_salida_c"]!="") {
                                                        $hora_salida_comedor=date("H:i:s",strtotime($datos_otro_turno["hora_salida_c"]));
                                                        } else {
                                                        $hora_salida_comedor="-";
                                                        }
                                                }
                                        }
                                }

                $horas_extra_hoy=($diferencia_entrada+$diferencia_salida);
                
                // Convierte a hh:mm
                $horas_extra_mostrar_hoy=$horas_extra_hoy;

                $horas_extra3=round($horas_extra_mostrar_hoy,0);
                if ($horas_extra3>$horas_extra_mostrar_hoy) {
                        $horas_extra3=$horas_extra3-1;
                                }
                $minutos_extra3="00".(($horas_extra_mostrar_hoy-$horas_extra3)*60);
                $minutos_extra3=substr($minutos_extra3,strlen($minutos_extra3)-2,2);
                $horas_extra3="00".$horas_extra3;
                $horas_extra3=substr($horas_extra3,strlen($horas_extra3)-2,2);
                $horas_extra_mostrar_hoy=$horas_extra3.":".$minutos_extra3;

                ?>
                <tr bgcolor="#FFFFFF">
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=date("d/m/Y",strtotime($datos_asistencia["fecha"]));?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=$nombre_turno;?><?=$coletila;?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1" color="<?=$color_e;?>"><?=date("H:i:s",strtotime($datos_asistencia["hora_entrada"]));?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1" color="<?=$color_s;?>"><?=date("H:i:s",strtotime($datos_asistencia["hora_salida"]));?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=$hora_entrada_comedor;?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=$hora_salida_comedor;?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=$horas_extra_mostrar_hoy;?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><input readonly disabled type="checkbox" value="1" name="no_facturar_<?=$datos_asistencia["id_servicio_consumido"];?>"<?php  if ($datos_asistencia["no_facturar"]==1) { print " checked"; } ?>></font></td>
                </tr>
                <?php 
                }
        
        // Listado de bonos
        ?>
        <tr bgcolor="#efefef">
        <td align="center" colspan=8><font size=1><b>Listado de bonos y horas extra contratadas</b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Servicios contratado</b></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Periodicidad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha/Fecha inicio</b></font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Fecha fin</b></font></td>
        </tr>
        <?php 
        $numero_servicios=0;
        // Busca bonos horas extra contratados durante todo el año
        $busca_bonos1=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=1 AND periodicidad=1 AND (fecha_inicio<=?) AND (fecha_fin>=?)', 'sss', array($listado_alumnos["id_alumno"], (string)($ano_sel) . '-' . (string)($mes_sel) . '-1', (string)($ano_sel) . '-' . (string)($mes_sel) . '-1')) or die(db_error());
        while($bonos1=db_fetch_array($busca_bonos1)) {
                $numero_servicios++;
                $periodicidad="Anual";
                $fecha_fin=date("d/m/Y",strtotime($bonos1["fecha_fin"]));
                ?>
                <tr bgcolor="#FFFFFF">
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$bonos1["ARTICULO"];?><?php if (strlen($bonos1["notas"])!=0) { echo " - ".$bonos1["notas"]; } ?></font></td>
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$periodicidad;?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=$bonos1["cantidad"];?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=date("d/m/Y",strtotime($bonos1["fecha_inicio"]));?></font></td>
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$fecha_fin;?></font></td>
                </tr>
                <?php 
                }
        // Busca bonos horas extra contratados para este mes
        $busca_bonos2=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=1 AND periodicidad=2 AND  (month(fecha_inicio)=?) AND (year(fecha_inicio)=?)', 'sss', array($listado_alumnos["id_alumno"], $mes_sel, $ano_sel)) or die(db_error());
        while($bonos2=db_fetch_array($busca_bonos2)) {
                $numero_servicios++;
                $periodicidad="Mensual";
                $fecha_fin="-";
                ?>
                <tr bgcolor="#FFFFFF">
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$bonos2["ARTICULO"];?><?php if (strlen($bonos2["notas"])!=0) { echo " - ".$bonos2["notas"]; } ?></font></td>
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$periodicidad;?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=$bonos2["cantidad"];?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=date("d/m/Y",strtotime($bonos2["fecha_inicio"]));?></font></td>
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$fecha_fin;?></font></td>
                </tr>
                <?php 
                }
        // Busca horas extra individuales
        $busca_bonos3=db_query_params('SELECT * FROM servicios_contratados,tarifa WHERE servicios_contratados.id_servicio=tarifa.ID AND servicios_contratados.id_alumno=? AND tarifa.TIPO_SERVICIO=2 AND periodicidad=2 AND  (month(fecha_inicio)=?) AND (year(fecha_inicio)=?)', 'sss', array($listado_alumnos["id_alumno"], $mes_sel, $ano_sel)) or die(db_error());
        while($bonos3=db_fetch_array($busca_bonos3)) {
                $numero_servicios++;
                $periodicidad="Única";
                $fecha_fin="-";
                ?>
                <tr bgcolor="#FFFFFF">
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$bonos3["ARTICULO"];?><?php if (strlen($bonos3["notas"])!=0) { echo " - ".$bonos3["notas"]; } ?></font></td>
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$periodicidad;?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=$bonos3["cantidad"];?></font></td>
                        <td align="center"><font face="<?= $fuente_general; ?>" size="1"><?=date("d/m/Y",strtotime($bonos3["fecha_inicio"]));?></font></td>
                        <td align="center" colspan=2><font face="<?= $fuente_general; ?>" size="1"><?=$fecha_fin;?></font></td>
                </tr>
                <?php 
                }
        }
        ?>
        </form>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en este aula.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<div id="botonera" style="display:block;padding:0px;border:0px solid"><br>
<input type="button" onclick="imprimir();" value="Imprimir" style="font-size: 10px"> <input type="button" onclick="window.close()" value="Cerrar" style="font-size: 10px">
<br></div>
</center></div>
</div>
</center></div>
</div>
</body>
</html>

