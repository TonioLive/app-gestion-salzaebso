<?php  require("../configuracion/parte_superior_privado.php");

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

$colores[0]="#F69445";
$colores[1]="#E7F645";
$colores[2]="#8CF645";
$colores[3]="#45F6B1";
$colores[4]="#45A9F6";
$colores[5]="#7B45F6";
$colores[6]="#EA76FF";
$colores[7]="#DF88A9";
$colores[8]="#F75A5A";
$colores[9]="#F7995A";

?>
<SCRIPT LANGUAGE=javascript>
<!--

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
function eliminar_contacto(id) {
        if (confirm("Se va ha eliminar el contacto seleccionado.\n\n¿Estas seguro?")) {
                window.location.href='eliminar_contacto.php?id_contacto='+id;
                }
        }
function editarservicios(fila) {
        window.open('/facturacion/editar_servicios.php?i='+fila,'servicios','toolbar=yes,resizable=yes,width="+screen.width+",height="+screen.height+"');
        }
//-->
</SCRIPT>
<div align="center"><center>
<input type="button" onclick="window.location.href='informe_contactos.php?curso='+self.document.seleccion_curso.curso_seleccionado.value" value="Ver informe" style="font-size: 10px"> <input type="button" onclick="window.location.href='nuevo_contacto.php'" value="Nuevo contacto" style="font-size: 10px"> <input type="button" onclick="window.location.href='/index.php'" value="Salir" style="font-size: 10px">
<br><br>
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
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b>Listado de contactos en proceso de decisión</b></small></td>
  </tr>
<?php 
$busca_cotactos=db_query("SELECT * FROM contactos WHERE id_centro=0 OR id_centro=$centro_actual$filtro ORDER BY id_centro DESC,id_curso,proceso_decision, fecha_contacto, Apellido1, Apellido2, Nombre");

if (db_num_rows($busca_cotactos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=7><font size=1 face="<?= $fuente_general; ?>">Para editar un contacto, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)</font></td>
        </tr>
        <?php 
        $proceso_actual="";
        $curso_actual="";
        $centro_actual_contacto="";
        
        while($listado_alumnos=db_fetch_array($busca_cotactos)){
        
        if ($centro_actual_contacto!=$listado_alumnos["id_centro"]) {
        $centro_actual_contacto=$listado_alumnos["id_centro"];
        if ($centro_actual_contacto==0) {
                $nombre_centro_actual="Sin escuela seleccionada";
                } else {
                // Busca datos del centro
                $busca_centro=db_query("SELECT * FROM centros WHERE id_centro=".$listado_alumnos["id_centro"]);
                if (db_num_rows($busca_centro)!=0) {
                        $datos_centro=db_fetch_array($busca_centro);
                        $nombre_centro_actual=$datos_centro["nombre_centro"];
                        }
                }
        ?>
        <tr bgcolor="#efefef">
        <td align="left" colspan=7><font size=3 face="<?= $fuente_general; ?>"><b>Escuela: <?=$nombre_centro_actual?></b></font></td>
        </tr>
        <?php 
        }
        
        if ($curso_actual!=$listado_alumnos["id_curso"]) {
                        // Datos del curso
                        if ($listado_alumnos["id_curso"]==0) {
                        $nombre_curso="Sin curso definido";
                        } else {
                        $busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$listado_alumnos["id_curso"]);
                        if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $nombre_curso=$datos_curso["nombre"];
                                }
                        }
                        $numero_color_curso=intval(substr($listado_alumnos["id_curso"],strlen($listado_alumnos["id_curso"])-1,1));
                        ?>
                        <tr valign="top" bgcolor="<?=$colores[$numero_color_curso]?>">
                                <td align="left" colspan=7><font size=3 face="<?= $fuente_general; ?>"><b>Para el Curso: <?=$nombre_curso;?></b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$listado_alumnos["id_curso"];
                        }
        
        $proceso=$listado_alumnos["proceso_decision"];
        if ($proceso!=$proceso_actual) {
        $proceso_actual=$proceso;
        ?>
        <tr bgcolor="#efefef">
        <td align="left" colspan=7><font size=2 face="<?= $fuente_general; ?>"><b>Contactos en estado <?=$proceso_actual?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre alumno</b></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre persona contacto</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha contacto</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
        <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono</b></font></td>
        <td align="center" width=15%><font size=1 face="<?= $fuente_general; ?>"><b>E-mail</b></font></td>
        <td align="center" width=7%><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        }
        $color_fuente_linea="#000000";
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></font></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><font color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["persona_contacto"]?></font></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=date("d/m/Y",strtotime($listado_alumnos["fecha_contacto"]))?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["poblacion"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["telefono"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>" color="<?=$color_fuente_linea;?>"><?=$listado_alumnos["email"]?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="nuevo_contacto.php?id_contacto=<?=$listado_alumnos["id_contacto"];?>" title="Editar contacto"><img src="modificar.gif" border=0></a> <a href="javascript:eliminar_contacto(<?=$listado_alumnos["id_contacto"];?>);" title="Eliminar contacto"><img src="borrar.gif" border=0></a> </font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha introducido ningún contacto.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="window.location.href='informe_contactos.php'" value="Ver informe" style="font-size: 10px"> <input type="button" onclick="window.location.href='nuevo_contacto.php'" value="Nuevo contacto" style="font-size: 10px"> <input type="button" onclick="window.location.href='/index.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

