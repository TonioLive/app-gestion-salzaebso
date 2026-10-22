<?php  require("../../configuracion/parte_superior_docente.php"); ?>
<?php 
$mostrar_cerradas=$_GET["c"];
if ($mostrar_cerradas=="1") {
        $texto_titulo="Listado de incidencias notificadas ya resuletas";
        $filtro_consulta="1";
        } else {
        $texto_titulo="Listado de incidencias notificadas pendientes de resolver";
        $filtro_consulta="0";
        }
?>
<SCRIPT LANGUAGE=javascript>
<!--


//-->
</SCRIPT>
<div align="center"><center>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="100%">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center"><small><b><?=$texto_titulo;?><br><br>Incidencias notificadas por mi para la escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <form name="listado_incidencias" method="POST">
        <?php 
        $busca_incidencias=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=$filtro_consulta AND notificada_por=$idUsuario ORDER BY incidencias.tipo, incidencias.fecha_incidencia");
        if (db_num_rows($busca_incidencias)==0) {
                ?>
                <tr>
                <td align="center" colspan="3"><font size=2><b>No hay incidencias para mostrar</b></font></td>
                </tr>
                <?php 
                }
        $tipo_actual=-1;
        while($listado_incidencias=db_fetch_array($busca_incidencias)){
        
        if ($tipo_actual!=$listado_incidencias["tipo"]) {
        
        $nombre_tipo_actual=$listado_incidencias["nombre_tipo"];
        $tipo_actual=$listado_incidencias["tipo"];
        ?>
        <tr>
        <td align="left" colspan="3"><br><font size=2>Incidencias de <b><?=$nombre_tipo_actual;?></b></font></td>
        </tr>
        <tr bgcolor="#efefef">
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Fecha</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Seguimiento realizado por...</b></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
        </tr>
        <?php 
        }
        
        // Busca nombre de la persona que hace el seguimiento
        
        $busca_seguimiento=db_query("SELECT * FROM usuarios WHERE id_usuario=".$listado_incidencias["seguimiento_por"]);

        if (db_num_rows($busca_seguimiento)!=0) {
                $datos_seguimiento=db_fetch_array($busca_seguimiento);
                $nombre_seguimiento=$datos_seguimiento["Apellido1"]." ".$datos_seguimiento["Apellido2"].", ".$datos_seguimiento["Nombre"];
                } else {
                $nombre_seguimiento="Desconocido";
                }
                
        ?>
        <tr>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($listado_incidencias["fecha_incidencia"]))?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_seguimiento;?></font></td>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><a href="detalles_mi_incidencia.php?id_incidencia=<?=$listado_incidencias["id_incidencia"];?>"><font color="blue">Ver detalles</font></a></font></td>
        <?php 
        }
        ?>
        </form>
        </table>
        </td>
</tr>
</table>
<br><?php 
if ($mostrar_cerradas=="1") {
?><input type="button" onclick="window.location.href='mis_incidencias.php'" value="Mostrar incidencias no resueltas" style="font-size: 10px"><?php 
} else { ?>
<input type="button" onclick="window.location.href='mis_incidencias.php?c=1'" value="Mostrar incidencias resueltas" style="font-size: 10px"><?php  } ?> <input type="button" onclick="window.location.href='menu_incidencias.php'" value="Salir" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../../configuracion/parte_inferior.php"); ?>

