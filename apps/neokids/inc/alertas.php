<?php 
if (($nivel_acceso>=3) and (strlen($centro_actual)!=0)) {

$filtro_consulta="0";
$busca_incidencias=db_query("SELECT * FROM incidencias, incidencias_tipos WHERE incidencias.tipo=incidencias_tipos.id_tipo AND incidencias.resuelta=$filtro_consulta ORDER BY incidencias.tipo, incidencias.fecha_incidencia");
        if (db_num_rows($busca_incidencias)!=0) {
                ?>
                <tr bgcolor="#cfcfcf">
                <td align="right"><font size=3 face="<?= $fuente_general; ?>" color="#800000"><b>NOTA:</b></font></td>
                <td align="left"><a href="/gestion/incidencias/incidencias_general.php"><font size=3 color=red><b>Hay <?=db_num_rows($busca_incidencias);?> incidencias pendientes de resolver. Ver incidencias</b></font></a></td>
                </tr>
                <?php 
                }
                
        
}
?>
