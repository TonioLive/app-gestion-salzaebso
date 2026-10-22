<?php  require("../configuracion/parte_superior_docente.php"); ?>
<?php 

$formulario_name="centros";
$formulario_accion="editar_centro2.php";

$id_centro=$centro_actual;

$url_retorno="/aulas/gestion_aulas.php";
        
if (strlen($id_centro)!=0) {
        
                $busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$id_centro");
                if (db_num_rows($busca_datos_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_datos_centro);
                        
                        $db_nombre_centro=$datos_centro["nombre_centro"];

                        }


?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_centro" value="<?=$id_centro;?>">
<input type="hidden" name="formulario_enviado" value="1">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Aulas del Centro <i><?=$db_nombre_centro?></i></b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=5 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listado de aulas</b></font></td>
                        </tr>
                         <?php 
                         $curso_actual="";
                         $consulta_aulas=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro ORDER BY id_curso,nombre;") or die(db_error());
                         while($aulas=db_fetch_array($consulta_aulas)){
                         //Busca cursos
                         $consulta_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$aulas["id_curso"]);
                         if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                } else {
                                $nombre_curso_academico="No asignado";
                                }
                        // Calcula ocupación actual
                        $consulta_ocupacion=db_query("SELECT * FROM alumnos WHERE aula=".$aulas["id_aula"]);
                        $ocupacion_actual=db_num_rows($consulta_ocupacion);
                        
                        
                        if ($curso_actual!=$nombre_curso_academico) {
                        ?>
                        <tr valign="top">
                                <td colspan=5 align="left" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>">Curso: <?=$nombre_curso_academico;?></font></td>
                        </tr>
                        <tr valign="top">
                                <td align="left" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Capacidad</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Mín.</b></font></td>
                                <td align="center" bgcolor="#efefef"><font size=1 face="<?= $fuente_general; ?>"><b>Ocupación Actual</b></font></td>
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
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><?=$ocupacion_actual;?></font></td>
                                <td align="center" bgcolor="#ffffff"><font size=1 face="<?= $fuente_general; ?>"><a href="gestion_docentes.php?id_aula=<?=$aulas["id_aula"];?>" title="Gestión de docentes del Aula">D</a> <a href="listado_alumnos.php?id_aula=<?=$aulas["id_aula"];?>" title="Listado de Alumnos en Aula">L</a> <a href="editar_aula.php?o=1&id_aula=<?=$aulas["id_aula"];?>" title="Editar información del aula"><img src="editar.gif" border=0></a></font></td>
                        </tr>
                         <?php 
                         }
                         ?>
                        </table>
                        <input type="button" onclick="window.location.href='editar_aula.php?o=1'" value="Agregar aula" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px">
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
 require("../configuracion/parte_inferior.php"); ?>
