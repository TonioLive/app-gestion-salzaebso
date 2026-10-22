<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

$formulario_name="listados";
$formulario_accion="informe_alumnos_centro.php";

$url_retorno="/alumnos/gestion_alumnos.php";

?>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b>Aulas del Centro <i><?=$db_nombre_centro?></i></b><br><br></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top">
                                <td colspan=2 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Listados de alumnos</b></font></td>
                        </tr>
                        <form method="GET" action="<?=$formulario_accion;?>" name="listados" target="_new">
                        <tr><td align="right">
                        <font size=2><b>Curso: </b></font></td><td><select name="cur" size=1 style="font-size: 10px">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio DESC");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></td>
                        </tr>
                <tr>
                <td align="right"><font size=2 face="<?= $fuente_general; ?>">Centro:</font></td>
                <td><select name="cen" size=1 style="font-size: 10px">
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                </tr>
                        </table><br>
                        <input type="button" onclick="listado_completo();" value="Exportar listado completo antiguos alumnos" style="font-size: 10px"> <input type="button" onclick="listado_normal();" value="Exportar listado" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"><br><br>
                        </form>
<SCRIPT LANGUAGE=javascript>
<!--

function listado_completo() {
	        window.document.<?=$formulario_name?>.action="informe_alumnos_centro_todos.php";
                window.document.<?=$formulario_name?>.submit();
        }

function listado_normal() {
        window.document.<?=$formulario_name?>.action="<?=$formulario_accion;?>";
        window.document.<?=$formulario_name?>.submit();
}        

//-->
</SCRIPT>                       
<?php  require("../configuracion/parte_inferior.php"); ?>
