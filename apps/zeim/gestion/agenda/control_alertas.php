<?php  include("../../configuracion/config.php");

$estado_nota[0]="<font color='#ff0000'>Pendiente</font>";
$estado_nota[1]="<font color='#00ff00'>Realizada</font>";

$tipos_notas_inci=array();

$busca_notas_inci=db_query("SELECT * FROM agenda_alumnos_tipos_notas");
while($notas_inci=db_fetch_array($busca_notas_inci)) {
        $tipos_notas_inci[$notas_inci["id_tipo"]]=$notas_inci["nombre_tipo"];
        }

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

$id_aula=$_GET["id_aula"];

$mostrar_alertas_mensajes=false;

if ($mostrar_alertas_mensajes==true) {
// Busca mensajes pendientes en bandeja de entrada

if (($nivel_acceso==3) or ($nivel_acceso==4)) {
        // Coordinadora
        $filtro_coordinadora="";
        } else {
        // Educadora
        $filtro_coordinadora=" AND agenda_notas_padres.destino<>1";
        }


$busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND aula=? ORDER BY Apellido1, Apellido2, Nombre', 'ss', array($centro_actual, $id_aula));

$consulta_mensajes="";

while($alumnos=db_fetch_array($busca_alumnos)) {
        $consulta_mensajes.=" OR agenda_notas_padres.id_alumno=".$alumnos["id_usuario"];
        }
$hay_mensajes_no_leidos=false;
if ($consulta_mensajes!="") {

        $consulta_mensajes="(".substr($consulta_mensajes,4,strlen($consulta_mensajes)-4).")";
        $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres WHERE $consulta_mensajes AND tipo_nota<>2 AND estado=0 AND (agenda_notas_padres.id_centro_alumno=$centro_actual OR agenda_notas_padres.id_centro_alumno=0)".$filtro_coordinadora." ORDER BY fecha DESC");
        // echo("a.".mysql_num_rows($busca_no_leidos)."<br>"); 
    if (db_num_rows($busca_no_leidos)!=0) {
                $hay_mensajes_no_leidos=true;

                // while($datos_no_leidos=mysql_fetch_array($busca_no_leidos)) {
                //        echo($datos_no_leidos["id_nota_padre"]."<br>");
                //        }
            } else {
            // Busca notas no leidas
            $busca_no_leidos=db_query("SELECT * FROM agenda_notas_padres,agenda_notas_padres_respuestas WHERE agenda_notas_padres.id_nota_padre=agenda_notas_padres_respuestas.id_nota AND $consulta_mensajes AND agenda_notas_padres_respuestas.leida=0 AND agenda_notas_padres_respuestas.direccion=0 AND (agenda_notas_padres.id_centro_alumno=$centro_actual OR agenda_notas_padres.id_centro_alumno=0)".$filtro_coordinadora." ORDER BY fecha DESC") or die(db_error());
            // echo("b.".mysql_num_rows($busca_no_leidos)."<br>");
            if (db_num_rows($busca_no_leidos)!=0) {
                        $hay_mensajes_no_leidos=true;

                        // while($datos_no_leidos=mysql_fetch_array($busca_no_leidos)) {
                        // echo($datos_no_leidos["id_nota_padre"]."<br>");
                        // }
                        }
            }
        }
}
?>
<table width=600 border=0 cellpadding=4 cellspacing=1 align="right">
<tr bgcolor="#dedede"><td align="center" colspan=3><font color="red" size=3 face="Arial"><b>ALERTAS<?php  if ($hay_mensajes_no_leidos==true) { echo("<br>¡Hay mensajes nuevos no leidos! <a href='dia_aula.php?".$_SERVER["QUERY_STRING"]."'><br><small>Clic aquí para actualizar listado</small></a>"); } ?></b></font></td>
<td align=right><font size=2 face="Arial"><b><?=date("H:i:s")?></b></font></td>
</tr>
<?php 

$busca_alertas=db_query_params('SELECT * FROM agenda_alumnos_seguimiento WHERE realizada=0 AND id_aula=? AND dia=? AND avisar>0 ORDER BY hora;', 'ss', array($id_aula, (string)(date("Y-m-d")))) or die(db_error());

while($alertas=db_fetch_array($busca_alertas)) {

        $hora_actual=strtotime(date("H:i:s"));
        $hora_aviso=strtotime($alertas["hora"]);
        
        // echo($hora_actual." - ".$hora_aviso." = ".($hora_actual-$hora_aviso)."<br>");
        
        if ($alertas["avisar"]>1) {        
                $hora_aviso=$hora_aviso-($alertas["avisar"]*60);
                }
        
        if ($hora_actual>=$hora_aviso) {
        
                // Datos alumno
                if ($alertas["id_alumno"]!=0) {
                $busca_datos_alumno=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=?', 's', array($alertas["id_alumno"]));

                $datos_alumno=db_fetch_array($busca_datos_alumno);

                $nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"].". ";
                } else {
                $nombre_completo_alumno="";
                }
                ?>
                <tr bgcolor="#efefef">
                <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=date("H:i",strtotime($alertas["hora"]));?></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=utf8_encode($tipos_notas_inci[$alertas["tipo_nota"]]);?></font></td>
                <td><font size=2 face="Arial"><b><?=utf8_encode($nombre_completo_alumno)?></b><?=utf8_encode($alertas["nota"]);?></font></td>
                <td align="center" width=5%><font size=2 face="<?= $fuente_general; ?>" color="#000000"><a href='javascript:cambiar_estado(<?=$alertas["id"];?>,<?=$alertas["realizada"];?>)'><?=$estado_nota[$alertas["realizada"]]?></a></td>
                </tr>
                <?php 
                }


        }

?>
</table>
