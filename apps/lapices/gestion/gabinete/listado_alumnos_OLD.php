<?php  require("../../configuracion/parte_superior_privado_ancho.php"); ?>
<?php 

// Datos para el formulario
$formulario_name = "formulario1";
$formulario_method = "POST";
$formulario_action = "";

$id_aula=$_GET["id_aula"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT centro_aulas.*, cursos_academicos.*, centro_aulas.nombre AS nombre_aula FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);
$centro_sel_actual=$datos_aula["id_centro"];

$busca_datos_centro_sel=db_query("SELECT * FROM centros WHERE id_centro=$centro_sel_actual");
$datos_centro_sel=db_fetch_array($busca_datos_centro_sel);
$nombre_centro_sel_actual=$datos_centro_sel["nombre_centro"];

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

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
/-->
</SCRIPT>
<div align="center"><center>
<input type="button" onclick="window.open('imprimir_listado.php?id_aula=<?=$id_aula;?>','imprimir_listado','toolbar=yes,scrollbars=yes,resizable=yes')" value="Listado para imprimir" style="font-size: 10px"> <input type="button" onclick="window.location.href='gestion_aulas.php?curso_seleccionado=<?=$curso_del_aula;?>'" value="Volver al listado de aulas" style="font-size: 10px"><br><br>
<form name="<?=$formulario_name;?>" method="<?=$formulario_method;?>" action="<?=$formulario_action;?>">
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de alumnos del Aula <?=$nombre_aula;?> <i>Curso <?=$datos_aula["nombre"];?></i><br>Escuela <i>"<?=$nombre_centro_sel_actual;?>"</i></b></small></td>
  </tr>
<?php 
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_sel_actual AND alumnos.aula=$id_aula ORDER BY orden_en_aula,Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_alumnos)!=0) {

$busca_orden_en_aula=db_query("SELECT * FROM alumnos WHERE alumnos.aula=$id_aula AND orden_en_aula<>0");
$con_num_orden=db_num_rows($busca_orden_en_aula);
$busca_en_aula=db_query("SELECT * FROM alumnos WHERE alumnos.aula=$id_aula");
$total_alumnos_aula=db_num_rows($busca_en_aula);


?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=14><font size=1 face="<?= $fuente_general; ?>">Para editar un alumno, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)<br>
        * Días asistidos al aula y al comedor durante el més actual (<?=$nombre_mes[date("n")];?> de <?=date("Y");?>)</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nº</b></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Brunet</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Brunet A.</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Mc Carthy</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Obser.</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Próxima prueba.</b></font></td>
        <td align="center"><input type=button onclick="imprimir_portadas();" value="Imprimir portadas"><br /><input type=button onclick="check_todas();" value="Selecconar/deseleccionar todos"></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
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
        $fecha_alta=$listado_alumnos["fecha_matricula"];
        
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
                                
        // Busca brunet
        $busca_pruebas_realizadas=db_query("SELECT * FROM pruebas_brunet WHERE id_alumno=".$listado_alumnos["id_usuario"]." ORDER BY fecha DESC");
        if (db_num_rows($busca_pruebas_realizadas)!=0) {
                $pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas);
                $fecha_brunet=strtotime($pruebas_realizadas["fecha"]);
                $fecha_brunet_texto=date("d/m/Y",strtotime($pruebas_realizadas["fecha"]));
                } else {
                $fecha_brunet_texto="";
                }
        // Busca brunet A
        $busca_pruebas_realizadas=db_query("SELECT * FROM pruebas_brunet_ampliado WHERE id_alumno=".$listado_alumnos["id_usuario"]." ORDER BY fecha DESC");
        if (db_num_rows($busca_pruebas_realizadas)!=0) {
                $pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas);
                $fecha_brunet_a=strtotime($pruebas_realizadas["fecha"]);
                $fecha_brunet_a_texto=date("d/m/Y",strtotime($pruebas_realizadas["fecha"]));
                } else {
                $fecha_brunet_a_texto="";
                }
        // Busca mcarthy
        $busca_pruebas_realizadas=db_query("SELECT * FROM pruebas_mcarthy WHERE id_alumno=".$listado_alumnos["id_usuario"]." ORDER BY fecha DESC");
        if (db_num_rows($busca_pruebas_realizadas)!=0) {
                $pruebas_realizadas=db_fetch_array($busca_pruebas_realizadas);
                $fecha_macarthy=strtotime($pruebas_realizadas["fecha"]);
                $fecha_macarthy_texto=date("d/m/Y",strtotime($pruebas_realizadas["fecha"]));
                } else {
                $fecha_macarthy_texto="";
                }

        // Comprueba trimestre actual
        if ($fecha_brunet>$fecha_brunet_a)  {
        
        
                }

        ?>
        <tr>
        <?php 
        if ($listado_alumnos["orden_en_aula"]==0) {
                ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">&nbsp;</font></td>
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
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_brunet_texto;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_brunet_a_texto;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_macarthy_texto;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$obs;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$fecha_proxima_prueba;?></font></td>
        <td align="center"><input type="checkbox" value="<?=$listado_alumnos["id_alumno"];?>" name="imprimir_inf"></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="seguimiento.php?id_alumno=<?=$listado_alumnos["id_usuario"];?>" target="_new" title="Seguimiento Psicopedagógico"><img src="pruebas.gif" border=0></a> <a href="/gestion/calendario/calendario_asistencia.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>" target="calendario_asistencia" title="Editar calendario docente del alumno"><img src="calendario.gif" border=0></a>  <a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>" title="Editar datos del alumno"><img src="modificar.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a> <a href="/pdf/portada_informe.php?id_alumno=<?=$listado_alumnos["id_alumno"];?>" target="_new" title="Imprimir portada informe Psicopedagógico"><img src="fotos.gif" border=0></a></font></td>
        </tr>
        <?php 
        }

        ?>
        <tr bgcolor="#efefef">
        <td colspan=3><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Total alumnos nuevos: <?=$contador_nuevos?></b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>">&nbsp;</font></td>
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
</form>
<br>
<input type="button" onclick="window.open('imprimir_listado.php?id_aula=<?=$id_aula;?>','imprimir_listado','toolbar=yes,scrollbars=yes,resizable=yes')" value="Listado para imprimir" style="font-size: 10px"> <input type="button" onclick="window.location.href='gestion_aulas.php?curso_seleccionado=<?=$curso_del_aula;?>'" value="Volver al listado de aulas" style="font-size: 10px">
<br>
</center></div>
</div>
<script>

<?php 
if ($total_alumnos_aula==1) {
?>
var solo_hay_uno=true;
<?php  } else { ?>
var solo_hay_uno=false;
<?php 
}
?>

function check_todas() {
        if (solo_hay_uno==false) {
                for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                        document.<?=$formulario_name?>.imprimir_inf[c_alumnos].click();
                        }
                } else {
                        document.<?=$formulario_name?>.imprimir_inf.click();
                }
        }
        
function imprimir_portadas() {
        lista_alumnos="";

        if (solo_hay_uno==false) {
        for (c_alumnos=0;c_alumnos<document.<?=$formulario_name?>.imprimir_inf.length;c_alumnos++) {
                if (document.<?=$formulario_name?>.imprimir_inf[c_alumnos].checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf[c_alumnos].value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }
                } else {
                if (document.<?=$formulario_name?>.imprimir_inf.checked) {
                        lista_alumnos=lista_alumnos+document.<?=$formulario_name?>.imprimir_inf.value;
                        lista_alumnos=lista_alumnos+",";
                        }
                }

        if (lista_alumnos!="") {                
                
                lista_alumnos=lista_alumnos.substr(0,(lista_alumnos.length-1))
                // window.location.href="/pdf/facturas.php?facturas="+lista_alumnos;
                ventana_facturas=window.open("/pdf/portada_informe.php?lista_alumnos="+lista_alumnos,"impresion_informes","toolbar=yes,scrollbars=yes,width=640,height=480")
                } else {
                alert("Debes seleccionar algún alumno para imprimir su agenda");
                }
        }

</script>


<?php  require("../../configuracion/parte_inferior.php"); ?>
