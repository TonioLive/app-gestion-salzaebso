<?php  require("../../configuracion/parte_superior_privado_ancho.php");

$id_curso=$_GET["curso_seleccionado"];

if (strlen($id_curso)==0) {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso=$datos_curso_actual["id_curso"];
                } else {
                $id_curso=0;
                }
        }

$id_centro=$centro_actual;

//Busca cursos
$consulta_datos_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$id_curso);
if (db_num_rows($consulta_datos_curso)!=0) {
                                $datos_curso=db_fetch_array($consulta_datos_curso);
                                $nombre_curso_academico=$datos_curso["nombre"];
                                }

?>
<div align="center"><center>
                        <form method="GET" action="<?=$_SERVER['PHP_SELF']?>" name="seleccion_curso">
                        <font size=2><b>Curso: <select name="curso_seleccionado" size=1 style="font-size: 10px" onchange="document.seleccion_curso.submit();">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></b></font></form>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=12><b>Listado de profesiones de padres <i>Escuela "<?=$nombre_centro_actual;?>"</i> <i>Curso <?=$nombre_curso_academico;?></i></b></td>
  </tr>
<tr bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>">Nº</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Profesión</b></font></td>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><b>Cantidad</b></font></td>
</tr>
<?php 
$busca_profesiones=db_query("SELECT usuarios.profesion, count(usuarios.profesion) AS total_profesion FROM alumnos,usuarios,padres WHERE usuarios.id_usuario=padres.id_usuario AND alumnos.id_usuario=padres.padre_de AND alumnos.id_centro=$centro_actual AND alumnos.id_curso=$id_curso GROUP BY usuarios.profesion ORDER BY profesion");

if (db_num_rows($busca_profesiones)!=0) {

$contador_padres=0;
$contador_alumnos=1;

while($listado_alumnos=db_fetch_array($busca_profesiones)){

        ?>
        <tr>
        <td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$contador_alumnos;?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><?=$listado_alumnos["profesion"]?></font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><?=$listado_alumnos["total_profesion"]?></font></td>
        </tr>
        <?php 
        $contador_padres=$contador_padres+$listado_alumnos["total_profesion"];
        $contador_alumnos++;
        }

}
?>
        <tr bgcolor="#efefef">
        <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>">Total</font></td>
        <td><font size=2 face="<?= $fuente_general; ?>"><?=$contador_padres;?></font></td>
        </tr>
</table>
<br>
<input type="button" onclick="window.location.href='gestion_listados.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>
