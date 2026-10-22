<?php 
       
       $inicio_semana=$fecha_time_stamp-((date("N",strtotime($fecha_time_stamp))-1)*86400);
       $fin_semana=$inicio_semana+(5*86400);
                
        // Busca informaciones previstas hoy
        
        $rsinformaciones_query=db_query_params('SELECT * FROM contactos WHERE id_centro=? AND fecha_informacion>=? AND fecha_informacion<=? ORDER BY fecha_informacion;', 'sss', array($centro_actual, (string)(date("Y-m-d", $inicio_semana)), (string)(date("Y-m-d", $fin_semana)))) or die(db_error());

        if (db_num_rows($rsinformaciones_query)!=0) {
        ?>
        <div align="center">
        <br>
        <table border="1" bordercolor="#000000" style="border-collapse:collapse" width="98%" cellspacing="0">
        <tr>
        <td width="100%" colspan=6 bgcolor="#efefef" align="center"><font size=2><b>Informaciones previstas para esta semana.</b></font></td>
        </tr>
        <?php 
        $fecha_actual_infos="";
        while($rsinformaciones=db_fetch_array($rsinformaciones_query)) {
        
        if ($fecha_actual_infos!=$rsinformaciones["fecha_informacion"]) {
        	
        	$fecha_actual_infos=$rsinformaciones["fecha_informacion"];
        
        	?>
        	<tr>
        		<td colspan=6><b><?=$dia_sem_largo[date("N",strtotime($fecha_actual_infos))]?> <?=date("d/m/Y",strtotime($fecha_actual_infos));?></b></td>
        	</tr>
        	<tr bgcolor="#efefef">
        		<td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre Niño/a</b></font></td>
        		<td><font size=2 face="<?= $fuente_general; ?>"><b>Fecha Nacimiento</b></font></td>
        		<td><font size=2 face="<?= $fuente_general; ?>"><b>Nombre persona contacto</b></font></td>
        		<td align="center" width=15%><font size=2 face="<?= $fuente_general; ?>"><b>Teléfono</b></font></td>
        	</tr>
        	<?php 
        
        	}
            if (($rsinformaciones["fecha_nacimiento"]!="0000-00-00") and (strlen($rsinformaciones["fecha_nacimiento"])!=0)) {
        	$fecha_nacimiento_informacion=date("d/m/Y",strtotime($rsinformaciones["fecha_nacimiento"])); 
        	}else {
        	$fecha_nacimiento_informacion="-";
        	}             
        ?>
		<tr>
        	<td><font size=2 face="<?= $fuente_general; ?>"><?=mb_convert_case(security_escape($rsinformaciones["Apellido1"])." ".security_escape($rsinformaciones["Apellido2"])." ".security_escape($rsinformaciones["Nombre"]),MB_CASE_TITLE);?></font></td>
        	<td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$fecha_nacimiento_informacion;?></font></td>
        	<td><font size=2 face="<?= $fuente_general; ?>"><?=mb_convert_case($rsinformaciones["persona_contacto"],MB_CASE_TITLE);?></font></td>
        	<td><font size=2 face="<?= $fuente_general; ?>"><?=$rsinformaciones["telefono"]?></font></td>
        </tr>
        <?php 
        }
        ?>
        </table>
<br>
</div>
        <?php 
        }
?>
