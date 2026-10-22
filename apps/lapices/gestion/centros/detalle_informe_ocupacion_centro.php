<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
$id_tipo_listado=$_GET["tipo"];
$id_centro=$_GET["id_centro"];
$id_curso=$_GET["id_curso"];
$fecha_asistencia=$_GET["fecha"];
$total_nuevos=0;
?>
<html>
<head>
<title><?php  echo $titulo_web; ?></title>
<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function imprimir() {
        document.getElementById('botonera').style.display='none';
        window.print();
        document.getElementById('botonera').style.display='block';
        }
        
function detalles_asistencia(id_alumno) {
        window.open("detalles_asistencia.php?fecha=<?=$fecha_asistencia;?>&id_alumno="+id_alumno,"ventana_detalle1","toolbar=no,width=750,height=550");
        }
        
//-->
</SCRIPT>
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
</head>
<body class="legacy-admin" bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<?php 

$nombre_tipo_de_listado[1]="Listado de Ocupación Actual";
$nombre_tipo_de_listado[2]="Listado de Asistencia (".date("d/m/Y",$fecha_asistencia).")";
$nombre_tipo_de_listado[3]="Listado de bajas hasta el día ".date("d/m/Y",$fecha_asistencia);
$nombre_tipo_de_listado[4]="Listado de próximas incorporaciones a partir del día ".date("d/m/Y",$fecha_asistencia);
$nombre_tipo_de_listado[5]="Listado Completo de Alumnos";
$nombre_tipo_de_listado[6]="Listado de Asistencia Turno de mañana(".date("d/m/Y",$fecha_asistencia).")";
$nombre_tipo_de_listado[7]="Listado de Asistencia Turno de tarde (".date("d/m/Y",$fecha_asistencia).")";
$nombre_tipo_de_listado[8]="Listado de Inscritos en Comedor (".date("d/m/Y",$fecha_asistencia).")";
$nombre_tipo_de_listado[9]="Listado de Asistencia Comedor (".date("d/m/Y",$fecha_asistencia).")";
$nombre_tipo_de_listado[10]="Listado de Comedores Extra (".date("d/m/Y",$fecha_asistencia).")";
$nombre_tipo_de_listado[11]="Listado de Próximas incorporaciones al comedor a partir del día ".date("d/m/Y",$fecha_asistencia);

// Busca datos del centro
$conculta_datos_del_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro");
$datos_del_centro=db_fetch_array($conculta_datos_del_centro);

?>
<SCRIPT LANGUAGE=javascript>
<!--

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
        
//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?=$nombre_tipo_de_listado[$id_tipo_listado]?></i><br>Escuela <i>"<?=$datos_del_centro["nombre_centro"];?>"</i></b></small></td>
  </tr>
<?php 

$consulta_datos_aula=db_query("SELECT centro_aulas.*, cursos_academicos.*, centro_aulas.nombre AS nombre_aula, cursos_academicos.nombre AS nombre_curso FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_centro=$id_centro AND cursos_academicos.id_curso=$id_curso ORDER BY centro_aulas.id_curso,centro_aulas.id_aula");

while($datos_aula=db_fetch_array($consulta_datos_aula)) {

?>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><font size=2><b><?=$datos_aula["nombre_aula"]?>, Curso: <i><?=$datos_aula["nombre_curso"];?></i></font></td>
  </tr>
<?php 
$id_aula=$datos_aula["id_aula"];
$curso_del_aula=$datos_aula["id_curso"];

$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aula=$id_aula ORDER BY Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=5><font size=1 face="<?= $fuente_general; ?>">Para editar un alumno, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)</font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha Nac./Edad</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Inicio</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fin</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Estado</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        while($listado_alumnos=db_fetch_array($busca_alumnos)){
        
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
                
        $fecha_baja=$listado_alumnos["fecha_baja"];

        if ($fecha_baja=="0000-00-00") {
                $fecha_baja=$datos_aula["fecha_fin"];
                }

        $mostrar=false;
        
        if ($id_tipo_listado!=5) {
                $color_fuente_linea="#000000";
                switch($id_tipo_listado)
                        {
                        case 1:
                        // Comprueba si matriculado
                        if (($fecha_baja>=date("Y-m-d",$fecha_asistencia)) and ($listado_alumnos["fecha_matricula"]<=date("Y-m-d",$fecha_asistencia))) {
                                $mostrar=true;
                                }
                        break;
                        case 2:
                        // Comprueba asistencia
                        $busca_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_asistencia)."'");
                        if (db_num_rows($busca_asistencia)!=0) {
                                $mostrar=true;
                                }
                        break;
                        case 3:
                        // Comprueba si baja
                        if ($fecha_baja<date("Y-m-d",$fecha_asistencia)) {
                                $mostrar=true;
                                }
                        break;
                        case 4:
                        // Comprueba si alta
                        if ($listado_alumnos["fecha_matricula"]>date("Y-m-d",$fecha_asistencia)) {
                                $mostrar=true;
                                }
                        break;
                        case 6:
                        // Comprueba asistencia mañana
                        $busca_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE turno=0 AND id_alumno=".$listado_alumnos["id_usuario"]." AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_asistencia)."'");
                        if (db_num_rows($busca_asistencia)!=0) {
                                $mostrar=true;
                                }
                        break;
                        case 7:
                        // Comprueba asistencia tarde
                        $busca_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE turno=1 AND id_alumno=".$listado_alumnos["id_usuario"]." AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_asistencia)."'");
                        if (db_num_rows($busca_asistencia)!=0) {
                                $mostrar=true;
                                }
                        break;
                        case 8:
                        // Comprueba inscripcion en comedor
                        if (($fecha_baja>=date("Y-m-d",$fecha_asistencia)) and ($listado_alumnos["fecha_matricula"]<=date("Y-m-d",$fecha_asistencia)) and ($listado_alumnos["comedor"]<>2)) {
                                $mostrar=true;
                                }
                        break;
                        case 9:
                        // Comprueba asistencia a comedor
                        $busca_asistencia=db_query("SELECT * FROM servicios_consumidos WHERE id_alumno=".$listado_alumnos["id_usuario"]." AND id_servicio=$id_aula AND fecha='".date("Y-m-d",$fecha_asistencia)."' AND tipo_menu<>2 AND hora_entrada_c<>''");
                        if (db_num_rows($busca_asistencia)!=0) {
                                $mostrar=true;
                                }
                        break;
                        case 10:
                        // Comprueba asistencia extra a comedor
                        $busca_asistencia=db_query("SELECT servicios_consumidos.id_alumno, alumnos.* FROM servicios_consumidos,alumnos WHERE servicios_consumidos.id_alumno=alumnos.id_usuario AND servicios_consumidos.id_alumno=".$listado_alumnos["id_usuario"]." AND alumnos.comedor=2 AND (((servicios_consumidos.id_servicio)=$id_aula) AND ((servicios_consumidos.fecha)='".date("Y-m-d",$fecha_asistencia)."')) AND servicios_consumidos.tipo_menu<>2 AND servicios_consumidos.hora_entrada_c<>'' GROUP BY servicios_consumidos.id_alumno") or die(db_error()."-1b");
                        if ((db_num_rows($busca_asistencia)!=0) and ($listado_alumnos["comedor"]==2)) {
                                $mostrar=true;
                                }
                        break;
                        case 11:
                        // Comprueba si alta
                        if (($listado_alumnos["fecha_matricula"]>date("Y-m-d",$fecha_asistencia)) and ($listado_alumnos["comedor"]<>2)) {
                                $mostrar=true;
                                }
                        break;
                        }
                } else {
                $mostrar=true;
                if ($fecha_baja<date("Y-m-d",$fecha_asistencia)) {
                        $color_fuente_linea="#ffacac";
                        } else {
                        if ($listado_alumnos["fecha_matricula"]>date("Y-m-d",$fecha_asistencia)) {
                                $color_fuente_linea="#45A9F6";
                                } else {
                                $color_fuente_linea="#000000";
                                }
                        }
                }


        if ($mostrar==true) {
        
        // Comprueba si es incorporacion nueva
        $id_curso_actual=$datos_aula["id_curso"];

        // Busca curso actual
        $consulta_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($consulta_curso_actual);

        // Busca curso anterior
       $consulta_matricula_cursos=db_query("SELECT * FROM cursos_academicos, alumnos WHERE cursos_academicos.id_curso=alumnos.id_curso AND alumnos.id_usuario=".$listado_alumnos["id_usuario"]." AND cursos_academicos.fecha_fin<'".date("Y-m-d",strtotime($datos_curso_actual["fecha_inicio"]))."'");
        if (db_num_rows($consulta_matricula_cursos)==0) {
                $total_nuevos++;
                $color_fuente_linea="red";
                }
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><a href="javascript:detalles_asistencia(<?=$listado_alumnos["id_alumno"];?>);"><b><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></b></a></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Poblacion"]?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_matricula"]));?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_baja"]));?></font></td>
        <?php 
        if ($fecha_baja<date("Y-m-d",$fecha_asistencia)) {
        ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>">BAJA</font></td>
        <?php 
                } else {
                 if ($listado_alumnos["fecha_matricula"]>date("Y-m-d",$fecha_asistencia)) {
                ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>">PRÓXIMA INCORPORACIÓN</font></td>
                <?php 
                 } else {
                ?>
                <td align="center"><font size=1 face="<?= $fuente_general; ?>">ACTIVO</font></td>
                <?php 
                }
                }
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editarservicios(<?=$listado_alumnos["id_alumno"];?>)" title="Editar servicios contratados"><img src="servicios.gif" border=0></a> <a href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>" target="edicion_alumno" title="Editar datos del alumno"><img src="modificar.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
        </tr>
        <?php 
        }
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><small><small><b>No hay alumnos en el aula.</td>
  </tr>
<?php 
}

}
?>
</table>
<font size=1><br>Alumnos de nueva incorporación este curso: <?=$total_nuevos;?><br><br>* ATENCIÓN: Este informe refleja el estado del centro el día <?=date("d/m/Y",$fecha_asistencia)?>.<br></font>
<div id="botonera" style="display:block;padding:0px;border:0px solid"><br>
<input type="button" onclick="imprimir();" value="Imprimir" style="font-size: 10px"> <input type="button" onclick="window.close()" value="Cerrar" style="font-size: 10px">
<br></div>
</center></div>
</div>
</body>
</html>

