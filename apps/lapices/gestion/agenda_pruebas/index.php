<?php  require("../../configuracion/parte_superior_docente.php"); ?>
<?php 

$formulario_name="centros";
$formulario_accion="editar_centro2.php";

$id_centro=$centro_actual;

$url_retorno="/gestion/agenda/index.php";
        
if (strlen($id_centro)!=0) {
        
                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro");
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);
                        
                        $db_nombre_centro=$datos_centro["nombre_centro"];

                        }
        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        }
        if ($id_curso_actual!=0) {
                                $filtro=" AND id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }
?>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <input type="hidden" name="fecha_seleccionada" value="<?=$fecha_seleccionada;?>">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Aulas del Centro <i><?=$db_nombre_centro?></i></b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=6 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listado de aulas</b></font></td>
                        </tr>
                         <?php 
                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro$filtro ORDER BY id_curso,nombre;") or die(db_error());
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$aulas["id_curso"]);
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                }

                        // Analiza el aula
                        // Calcula ocupación actual
                        $consulta_ocupacion=db_query("SELECT * FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.aula=".$aulas["id_aula"]) or die(db_error()."-1");
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);

                        $ocupacion_max=$aulas["capacidad"];
                        $ocupacion_min=$aulas["ocupacion_minima"];
                        $color_ocupacion="#000000";
                        if ($ocupacion_actual>$ocupacion_max) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="<br>Se ha superado la capacidad del aula.";
                                }
                        if ($ocupacion_actual<$ocupacion_min) {
                                $color_ocupacion="#FF0000";
                                $mensaje_ocupacion="<br>No se alcanza la ocupación mínima.";
                                }

                        // Calcula promedio de edades del aula
                        $promedio_edades=0;
                        if ($ocupacion_actual>0) {
                                while($listado_alumnos_aula=db_fetch_array($consulta_ocupacion)) {
                                        // 1. Comprueba la edad restando el año de nacimiento al año actual
                                        $fecha_l=$listado_alumnos_aula["fecha_nacimiento"];
                                        $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                        // 1.1. Cacula edad con decimales
                                        $mes_d_l=date("m",strtotime($fecha_l));
                                        $mes_d_n=date("m");
                                        $dif_meses=$mes_d_n-$mes_d_l;
                                        
                                        $edad_d_l=($edad_a_l*12)+$dif_meses;
                                        $promedio_edades=$promedio_edades+$edad_d_l;

                                        }
                                $promedio_edades=round($promedio_edades/$ocupacion_actual,2);
                                }
                        
                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=6 align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>">Curso: <?=$nombre_curso_academico;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Mín.</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Actual</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Promedio edades</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$nombre_curso_academico;
                        }
                        ?>
                        <tr valign="top">
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["nombre"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["capacidad"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$aulas["ocupacion_minima"];?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_ocupacion?>"><?=$ocupacion_actual;?></font><font size=1><?=$mensaje_ocupacion;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$promedio_edades;?> meses</font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="dia_aula.php?id_aula=<?=$aulas["id_aula"];?>" title="Listado de Alumnos en Aula"><img src="alumnos.gif" border=0></a></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                        </table><br>
                        <input type="button" onclick="window.location.href='editar_aula.php?o=1'" value="Agregar aula" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"><br><br>
                </td>
        </tr>
</form>
</table><br><br>
<?php 
} else {
?>
<br><br>Error: <a href="/index.php">Volver a la página de inicio</a></br></br>
<?php 
}
 require("../../configuracion/parte_inferior.php"); ?>
