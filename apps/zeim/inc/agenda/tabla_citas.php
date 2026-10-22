<?php 
// Coordinadoras y Administracion
if (($nivel_acceso==3) or ($nivel_acceso==4)) {
include("alertas_mensajes.php");
include("cumples_hoy.php");
}
// Aducadoras
if ($nivel_acceso==2) {
include("cumples_hoy.php");
}

$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d", $fecha_time_stamp)), (string)(date("Y-m-d", $fecha_time_stamp))));

if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }

$aula_asignada_usuario=0;

$busca_aula_asignada=db_query("SELECT * FROM centro_aulas WHERE docente_titular=$idUsuario AND id_curso=$id_curso_actual LIMIT 0,1");

if (db_num_rows($busca_aula_asignada)!=0) {
        $datos_aula_asignada=db_fetch_array($busca_aula_asignada);
        $aula_asignada_usuario=$datos_aula_asignada["id_aula"];
        }

$hay_avisos=false;

$filtro_usuarios="";
// Filtros nivel acceso
if (($nivel_acceso>=4) or ($usuario_multicentro==1)) {
        // Nada
        } else {
        $filtro_usuarios=$filtro_usuarios." AND (Agenda.id_centro=0 OR Agenda.id_centro=$centro_actual)";
        }
if (($nivel_acceso==2)) {
        $filtro_usuarios=$filtro_usuarios." AND (Agenda.id_aula=0 OR Agenda.id_aula=$aula_asignada_usuario)";
        }

// Consultamos citas de la agenda

$fecha_avisos=date("Y-m-d",$fecha_time_stamp);
$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND Agenda.avisar<='".$fecha_avisos."' AND Agenda.Fecha>'".$fecha_avisos."' AND ((agenda_asistentes.id_usuario=$idUsuario) OR (agenda_asistentes.nivel=$nivel_acceso) OR (Agenda.Nombre_usu='$identificador'))$filtro_usuarios GROUP BY Agenda.id ORDER BY Agenda.Fecha,Agenda.Hora");

if (db_num_rows($rsNot_query) != 0)
{
?>
<table width="100%" border="0" cellpadding="1" cellspacing="1">
<tr bgcolor="#efefef"><td><font size="3" face="<?= $fuente_general; ?>" color="#800000"><b>Avisos</b></font></td></tr>
</table>
<table border="1" width="100%" cellspacing="0" cellpadding="1">
<?php 
$fecha_actual_citas="";
while($rsNot=db_fetch_array($rsNot_query)) {

$dia_cita_timestamp=strtotime($rsNot["Fecha"]);
$color_cita="#800000";

if ($fecha_actual_citas!=$rsNot["Fecha"]) {
?>
<tr>
<td width="17%" bgcolor="#fff8c6"><font face="Arial" color="<?=$color_cita?>" size=2><b><?=date("d/m/Y",strtotime($rsNot["Fecha"]));?></b></font></td>
<td width="83%" bgcolor="#fff8c6" align="right"><a href="javascript:mostrar_dia(<?=$dia_cita_timestamp?>)"><font face="Arial" color="<?=$color_cita?>" size=1><b>Ampliar d&iacute;a</b></font></a> | <a href="<?=$SERVER["PHP_SELF"];?>?dia=<?=$dia_cita_timestamp;?>"><font face="Arial" color="<?=$color_cita?>" size=1><b>Ir a este d&iacute;a</b></font></a></td>
</tr>
<?php 
$fecha_actual_citas=$rsNot["Fecha"];
        }
?>
<tr>
<td width="17%" bgcolor="#fff8c6"><font face="Arial" color="<?=$color_cita?>" size=2><b><?=date("H:i",strtotime($rsNot["Hora"]));?></b></font></td>
<td width="83%" bgcolor="#fff8c6"><font face="Arial" color="<?=$color_cita?>" size=1><?=$rsNot["Titulo"];?>.</font></td>
</tr>
<?php 
}
?>
</table>
<?php 
}

$hay=false;

// Consultamos citas de la agenda
$rsNot_query=db_query("SELECT * FROM Agenda, agenda_asistentes WHERE Agenda.id=agenda_asistentes.id_cita AND DATE_FORMAT(Agenda.Fecha,'%d/%m/%Y')='".$fecha_agenda."' AND ((agenda_asistentes.id_usuario=$idUsuario) OR (agenda_asistentes.nivel=$nivel_acceso) OR (Agenda.Nombre_usu='$identificador'))$filtro_usuarios GROUP BY Agenda.id ORDER BY Agenda.id_centro,Agenda.Hora,Agenda.id_aula");

// $rsNot_query=mysql_query("SELECT * FROM Agenda WHERE DATE_FORMAT(Fecha,'%d/%m/%Y')='".$fecha_agenda."' ORDER BY id_centro,Hora,id_aula");

if (!$rsNot_query) {
                 echo("<p align=center>Error ejecutando la consulta: " . db_error() . "</p>");
                 exit();
                }

if (db_num_rows($rsNot_query) != 0)
{
  $hay=true;
}

?>
<table width="100%" border="0" cellpadding="1" cellspacing="1">
<tr bgcolor="#efefef"><td><font size="3" face="<?= $fuente_general; ?>" color="#800000"><b>Agenda de actividades <?=date("d/m/Y",$fecha_time_stamp)?></b></font></td></tr>
</table>
<?php 
//Encabezado
?>
<table border="1" width="100%" cellspacing="0" cellpadding="1">
<?php 
if ($hay==true)
{
  $escuela_actual=-1;
  $aula_actual=-1;
  // Citas de la agenda
  while($rsNot=db_fetch_array($rsNot_query))
  {
        if ($escuela_actual!=$rsNot["id_centro"]) {
                $escuela_actual=$rsNot["id_centro"];
                ?>
                <tr valign=top>
                        <td width="100%" bgcolor="#ffd0a6" colspan=2><font face="Arial" color="#800000"><small><b><?= $nombres_escuelas[$rsNot["id_centro"]]; ?></b></small></font></td>
                </tr>
                <?php 
                }
        if (($aula_actual!=$rsNot["id_aula"]) and ($escuela_actual!=0)) {
                $aula_actual=$rsNot["id_aula"];
                ?>
                <tr valign=top>
                        <td width="100%" bgcolor="#fff8c6" colspan=2><font face="Arial" color="#800000"><small><b><?= $nombres_aulas[$rsNot["id_aula"]]; ?></b></small></font></td>
                </tr>
                <?php 
                }
                
                
if (strtotime($rsNot["Hora"])<strtotime(date("H:i"))) {
        $color_cita="#800000";
        } else {
        $color_cita="#1476c9";
        }
                
?>
  <tr valign=top>
    <td width="17%" bgcolor="#fff8c6"><font face="Arial" color="<?=$color_cita?>" size=2><b><?= date("H:i",strtotime($rsNot["Hora"])); ?></b></font></td>
    <td width="83%" bgcolor="#fff8c6"><font face="Arial" color="<?=$color_cita?>" size=2><b><?= $rsNot["Titulo"]; ?></b></font></td>
  </tr>
  <tr valign=top>
    <td width="17%" bgcolor="#fff8c6" align="center">
    <?php 
     if ($rsNot["Nombre_usu"]==$identificador) {
                     $esmia=true;
             } else {
                     $esmia=false;
             }

     if (($nivel_acceso==4) or ($esmia==true)) {
    ?>
    <small><small>
    <a href="javascript:ampliar_dia('/agenda/modif_cita.php?oc=1&id=<?= $rsNot["id"]; ?>');">Modificar</a><br><a href="javascript:ampliar_dia('/agenda/borra_cita.php?oc=1&id=<?= $rsNot["id"]; ?>');">Eliminar</a>
    </small></small>
    <?php 
    } else {
    ?>&nbsp;<?php 
    }
?>

    </td>
    <td width="83%" bgcolor="#fff8c6"><small><small><?= $rsNot["Texto"]; if ($rsNot["Nombre_usu"]!=$identificador) {?><br><b><i>Introducida por:</b> <?=strtoupper($rsNot["Nombre_usu"])?></i><?php  } ?></small></small></td>
  </tr>
<?php 
  }
} else {
?>
  <tr valign=top>
    <td width="100%" bgcolor="#fff8c6" colspan="2" align="center"><font face="Arial" color="#800000" size="3">No hay actividades programadas para este d&iacute;a</font></td>
  </tr>
<?php 
}
?>
</table>