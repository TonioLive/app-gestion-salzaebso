<?php 
$centro_actual=20;
?>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Control de presencia</b></font>
                
                <?php 
                  $consulta_usuarios=db_query_params('SELECT * FROM usuarios WHERE tipo_inscripcion>=3 AND (id_centro=? OR id_centro=0) ORDER BY usuarios.nivel_acceso, usuarios.Apellido1', 's', array($centro_actual)) or die(db_error());
  
  while($rsslc=db_fetch_array($consulta_usuarios))
  {
  $tiempo_total_jornada=0;                  
  $codigo=$rsslc["id_usuario"];
  
  // Comprueba si tiene contrato en vigor
  $fecha_control=date("Y-m-d");
  $busca_contratos=db_query_params('SELECT * FROM personal_contratos WHERE id_empleado=? AND ((fecha_inicio<=? AND fecha_fin>=?) OR (fecha_inicio<=? AND ((fecha_fin=\'0000-00-00\') OR (fecha_fin is Null)))OR (fecha_inicio>?)) ORDER BY fecha_inicio DESC', 'sssss', array($codigo, (string)($fecha_control), (string)($fecha_control), (string)($fecha_control), (string)($fecha_control))) or die(db_error());
  
  if (db_num_rows($busca_contratos)!=0) {
    			$nombre_ins=$rsslc["Nombre"]." ".$rsslc["Apellido1"]." ".$rsslc["Apellido2"]; 
                ?>
                
                <table width=80%>
                <tr bgcolor="#FFFFFF">
                <td align="center" width=35%><font size=3 face="<?= $fuente_general; ?>"><i><?=ucwords(strtolower($nombre_ins))?></i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora entrada</i></font></td>
                <td align="center" width=25%><font size=2 face="<?= $fuente_general; ?>"><i>Hora salida</i></font></td>
                <td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><i>Tiempo</i></font></td>
                </tr>
        <?php 
        // Busca si ya está registrado
        $turno=2;

        $busca_registros_entrada=db_query("SELECT * FROM asistencia_docentes WHERE id_usuario=".$codigo." AND turno=$turno AND fecha='".date("Y-m-d")."' ORDER BY hora_entrada") or die(db_error());

        if (db_num_rows($busca_registros_entrada)==0) {  
        ?>
		<tr bgcolor="#efefef" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fila;?>')">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>,<?=$codigo?>)"></td>
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
        <tr bgcolor="#efefef" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fila;?>')">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <td align="center"><font size=3><?=date("H:i:s",strtotime($datos_registro["hora_entrada"]));?></font></td>
        <?php 
        if ($datos_registro["hora_salida"]=="") {
        $id_registro_comedor=$datos_registro["id_asistencia"];
        $estado_registro_comedor=$datos_registro["comedor"];
        $menu_comedor_educador=$datos_registro["menu_seleccionado_hoy"];
        $centro_asistencia_comedor=$datos_registro["id_centro_asistencia"];
        ?>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar salida" onclick="registro_salida(<?=$datos_registro["id_asistencia"]?>,<?=$codigo?>)"></td>
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
        <tr bgcolor="#efefef" onmouseover="cambiacolor_over(this)" onmouseout="cambiacolor_out(this,'<?=$color_fila;?>')">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Registrar entrada" onclick="registro_entrada_escuela(<?=$turno;?>,<?=$codigo?>)"></td>
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
        </table>
        <?php 
        	}
        
        }
        ?>        
                </td>
        </tr>
</table>
<br>
<script language="Javascript">

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function registro_entrada_escuela(turno,educador) {
                window.location.href='/gestion/docentes/registro_entrada.php?retorno=1&educador='+educador+'&id_centro_asistencia=<?=$centro_actual;?>&turno='+turno;
        }
        
function registro_entrada(turno,educador) {
        window.location.href='/gestion/docentes/registro_entrada.php?retorno=1&educador='+educador+'&turno='+turno;
        }
function registro_salida(id_registro) {
        window.location.href='/gestion/docentes/registro_salida.php?retorno=1&id_registro='+id_registro;
        }

</script>