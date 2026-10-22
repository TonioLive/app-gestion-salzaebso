<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];

// Busca datos del aula
$consulta_datos_aula=db_query_params('SELECT centro_aulas.*, cursos_academicos.*, centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=?', 's', array($id_aula));
$datos_aula=db_fetch_array($consulta_datos_aula);
$nombre_aula=$datos_aula["nombre_aula"];
$ciclo_aula=$datos_aula["ciclo"];

$curso_del_aula=$datos_aula["id_curso"];

$fecha_limite_presentacion=$datos_aula["fecha_limite_presentacion"];


$fecha_de_inicio_curso=$datos_aula["fecha_inicio"];

if (intval(strtotime($fecha_de_inicio_curso))>intval(time())) {
        $curso_iniciado=false;
        } else {
        $curso_iniciado=true;
        }

?>
<SCRIPT LANGUAGE=javascript>
<!--

function cambio_aula(id) {
        if (confirm("Se va ha cambiar el alumno de aula.\n\n¿Estas seguro?")) {
                opcion_seleccionada=eval("document.listado_alumnos.cambio_aula"+id+".value");
                window.location.href='cambio_aula_ayudas.php?na='+opcion_seleccionada+'&a='+id;
                }
        }

function asigna_orden() {
        if (confirm("Esta acción solo debe realizarse una vez cerrada la admisión de alumnos.\n\nUna vez asignados los números a los alumnos, no se pueden cambiar durante todo el curso.\n\n¿Estas segura/o?")) {
                self.document.listado_alumnos.action="crear_orden.php?id_aula=<?=$id_aula;?>";
                self.document.listado_alumnos.submit();
                }
        }
function anula_orden() {
        if (confirm("Esta acción solo debe realizarse si todavía no se ha presentado la documentación de las subvenciones.\n\nSi la documentación ya ha sido presentada no puede alterarse el orden.\n\n¿Estas segura/o?")) {
                window.location.href="borrar_orden.php?id_aula=<?=$id_aula;?>"
                }
        }
function abrir_anexos(codigo) {
        nueva_ventana=window.open("/blank.html","nueva_ventana","toolbar=yes, width=800, height=600, resizable=yes");
        nueva_ventana.location.href='/pdf/<?=$curso_del_aula?>/anexos'+codigo+'.php?id_aula=<?=$id_aula?>';
        }
function abrir_bajas() {
        nueva_ventana=window.open("/blank.html","nueva_ventana","toolbar=yes, width=800, height=600, resizable=yes");
        nueva_ventana.location.href='/pdf/<?=$curso_del_aula?>/carta_anexos2.php?id_aula=<?=$id_aula?>';
        }


//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de alumnos para impresión de anexos - Aula <?=$nombre_aula;?> <i>Curso <?=$datos_aula["nombre"];?></i><br>Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 
$busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=? AND alumnos.aula_ayudas=? ORDER BY orden_en_aula,Apellido1, Apellido2, Nombre', 'ss', array($centro_actual, $id_aula));

if (db_num_rows($busca_alumnos)!=0) {

$busca_orden_en_aula=db_query_params('SELECT * FROM alumnos WHERE alumnos.aula_ayudas=? AND orden_en_aula<>0', 's', array($id_aula));
$con_num_orden=db_num_rows($busca_orden_en_aula);
$busca_en_aula=db_query_params('SELECT * FROM alumnos WHERE alumnos.aula_ayudas=?', 's', array($id_aula));
$total_alumnos_aula=db_num_rows($busca_en_aula);


?>
        <tr>
                <td align="center" colspan=13>
<?php 


$forzar=true;
// Comprueba si la fecha es correcta
if (((intval(strtotime(date("Y-m-d")))<intval(strtotime($fecha_limite_presentacion)))) or ($forzar==true)) {
if ($con_num_orden==0) {
        ?><input type='button' style='font-size: 10px' onclick='asigna_orden();' value='Asignar números de orden'><?php 
        } else {
                if ($con_num_orden!=$total_alumnos_aula) {
                ?><input type='button' style='font-size: 10px' onclick='asigna_orden();' value='Asignar números de orden que faltan'><?php 
                }
                ?><input type='button' style='font-size: 10px' onclick='anula_orden();' value='Eliminar números de orden'><?php 
                }
}
if ($ciclo_aula==2) {
?><input type='button' style='font-size: 10px' onclick='abrir_anexos(1)' value='Imprimir Anexo IV.b'><?php  } else {
?><input type='button' style='font-size: 10px' onclick='abrir_anexos(1)' value='Imprimir Anexo II.a'> <input type='button' style='font-size: 10px' onclick='abrir_anexos(2)' value='Imprimir Anexo IV'> <input type='button' style='font-size: 10px' onclick='abrir_bajas()' value='Imprimir carta bajas'><?php  } ?>
                </td>
        </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=5><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td colspan=8 align=center valign=middle><?php 
        if (($curso_del_aula==10) and ($ciclo_aula==1)) {
        ?>
        <font size=2 face="<?= $fuente_general; ?>"><a href="/pdf/anexoIVa09-10.php?id_aula=<?=$id_aula?>" target="_new">Imprimir modelo de factura informativa para el curso 2009-2010.</a></font>
        <?php 
        } else {
        ?><font size=1 face="<?= $fuente_general; ?>"><a href="/pdf/presupuesto_inscritos_aula.php?id_aula=<?=$id_aula?>&m=1" target="_new">Imprimir presupuesto de educación y subvención con modelo de la consellería para todos los alumnos.</a><br>
        <a href="/pdf/presupuesto_paso1.php?id_aula=<?=$id_aula?>&m=2" target="_new">Imprimir presupuesto de educación y subvención con modelo de la consellería para todos los alumnos (DETALLADO).</a></font><?php 
        }
        ?></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nº</b></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Nacionalidad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Telefono</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Comedor</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo educacion</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Obser.</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Inicio</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fin</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Aula</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
        
        $suma_numeros_orden=0;
        $contador_nuevos=0;
        
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

        // Comprueba si es antiguo alumno
        $busca_inscripcion_ant=db_query("SELECT * FROM alumnos WHERE id_usuario=".$listado_alumnos["id_usuario"]." AND id_curso<$curso_del_aula") or die(db_error());

        if (db_num_rows($busca_inscripcion_ant)!=0) {
                $obs="<font color='blue'>Antiguo</font>";
                } else {
                $obs="<font color='red'>Nuevo</font>";
                $contador_nuevos++;
                }
        
        if ($listado_alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
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
                $fecha_nac_alumno=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        }
        
        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }
        if ($fecha_baja<date("Y-m-d")) {
                $color_fuente_linea="#ffacac";
                } else {
                if ($listado_alumnos["fecha_matricula"]>date("Y-m-d")) {
                        $color_fuente_linea="#45A9F6";
                        } else {
                        $color_fuente_linea="#000000";
                        }
                }
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
        <tr>
        <?php 
        if ($listado_alumnos["orden_en_aula"]==0) {
                ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><input type="checkbox" value="1" name="numerar_alumno_<?=$listado_alumnos["id_alumno"]?>"></font></td>
                <?php 
                } else {
                ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["orden_en_aula"];?></font></td>
                <?php 
                }
        ?>
        <td><font size=1 face="<?= $fuente_general; ?>"><a target="edicion_alumnos" href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_nac_alumno;?> - <?=$edad_alumno?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php  if ($listado_alumnos["sexo"]=="V") { print "Varón"; } else { print " Hembra"; }?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["nacionalidad"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono1"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$tipo_de_comedor[$listado_alumnos["comedor"]]?><?php  if ($listado_alumnos["comedor"]==1) { print " (".$tipo_de_menu[$listado_alumnos["tipo_menu_comedor"]].")"; } ?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?php if ($listado_alumnos["tipo_mensualidad"]==1) { print "Media jornada"; } else { print "Jornada Completa"; } ?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$obs;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));?> <?=date("d/m/Y",strtotime($listado_alumnos["fecha_inscripcion"]));?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));?></font></td>
        <?php 
        if ($fecha_baja<date("Y-m-d")) {
        ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
        <?php 
                } else {
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><select name="cambio_aula<?=$listado_alumnos["id_alumno"];?>" onchange="cambio_aula(<?=$listado_alumnos["id_alumno"];?>);" style="font-size: 10px">
        <option value=""></option>
        <?php 
        if ($nivel_acceso==4) {
        $listado_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_centro=? AND id_aula<>? AND id_curso=?', 'sss', array($centro_actual, $id_aula, $datos_aula["id_curso"]));
        } else {
        $listado_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_centro=? AND tipo=0 AND id_aula<>? AND id_curso=?', 'sss', array($centro_actual, $id_aula, $datos_aula["id_curso"]));
        }

        while($datos_listado_aulas=db_fetch_array($listado_aulas)){
        $capacidad_alumnos_list=db_query_params('SELECT * FROM alumnos WHERE aula=?', 's', array($datos_listado_aulas["id_aula"]));
        ?>
        <option value="<?=$datos_listado_aulas["id_aula"]?>"><?=$datos_listado_aulas["nombre"]?></option>
        <?php 
        }
        ?>
        </select></font></td>
        <?php 
                }
        ?>
        <td align="center">&nbsp;</td>
        </tr>
        <?php 
        }

        ?>
        <tr bgcolor="#efefef">
        <td colspan=9><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Total alumnos nuevos: <?=$contador_nuevos?></b></font></td>
        <td align="center" colspan=4><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        </tr>
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
<br>
<input type="button" onclick="window.location.href='gestion_aulas.php?curso_seleccionado=<?=$curso_del_aula;?>'" value="Volver al listado de aulas" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
