<?php 
$nombres_escuelas=array();
$nombres_escuelas[0]="Actividades comunes";

include("codigos_ausencias.php");

$busca_centros=db_query("SELECT * FROM centros ORDER BY id_centro");
while($lista_centros=db_fetch_array($busca_centros)){
        $nombres_escuelas[$lista_centros["id_centro"]]=$lista_centros["nombre_centro"];
        }
        
        if ($nivel_acceso!=4) {
                $filtro_escuela=" AND usuarios.id_centro=$centro_actual";
                } else {
                $filtro_escuela="";
                }
                
        // Busca ausencias
        $rsAusencias_query=db_query("SELECT * FROM horario_docentes_dias,usuarios WHERE usuarios.id_usuario=horario_docentes_dias.id_usuario AND fecha>='".date("Y-m-d",$fecha_time_stamp)."' AND fecha<'".date("Y-m-d",($fecha_time_stamp+(86400*31)))."' AND tipo_ausencia>0$filtro_escuela ORDER BY horario_docentes_dias.fecha,usuarios.id_centro;") or die(db_error());

        if (db_num_rows($rsAusencias_query)!=0) {
        ?>
        <div align="center">
        <br>
        <table border="1" bordercolor="#000000" style="border-collapse:collapse" width="98%" cellspacing="0">
        <tr>
        <td width="100%" colspan=4 bgcolor="#efefef" align="center"><font size=2><b>Incidencias de personal previstas entre los días <?=date("d/m/Y",$fecha_time_stamp);?> y <?=date("d/m/Y",($fecha_time_stamp+(86400*31)));?>.</b></font></td>
        </tr>
        <?php 
        $escuela_actual=-1;
        $aula_actual=-1;
        while($rsAusencias=db_fetch_array($rsAusencias_query)) {
        
        // Horas
        $texto_faltas="";
        if ((strlen($rsAusencias["hora_ini_falta"])!=0) and ($rsAusencias["hora_ini_falta"]!="00:00:00")) {
                $texto_faltas=$texto_faltas."De ".date("H:i",strtotime($rsAusencias["hora_ini_falta"]));
                }
        if ((strlen($rsAusencias["hora_fin_falta"])!=0) and ($rsAusencias["hora_fin_falta"]!="00:00:00")) {
                $texto_faltas=$texto_faltas." a ".date("H:i",strtotime($rsAusencias["hora_fin_falta"]));
                }
        if (strlen($texto_faltas)!=0) {
                $texto_faltas="<br>Ausente ".$texto_faltas." Hrs.";
                }
        
        if ($escuela_actual!=$rsAusencias["id_centro"]) {
                $escuela_actual=$rsAusencias["id_centro"];
                ?>
                <tr valign=top>
                        <td width="100%" colspan=4><font face="Arial" color="#800000"><small><small><b><?= $nombres_escuelas[$rsAusencias["id_centro"]]; ?></b></small></small></font></td>
                </tr>
                <tr valign=top>
                <td colspan=2 bgcolor="#efefef" align="center"><font size=1><b>Tipo y fecha</b></font></td>
                <td bgcolor="#efefef" align="center"><font size=2><b>Educadora/or</b></font></td>
                <td bgcolor="#efefef" align="center"><font size=2><b>Sustituta/o</b></font></td>
                </tr>
                <?php 
                }
        $totales_ausencias[$rsAusencias["tipo_ausencia"]]=$totales_ausencias[$rsAusencias["tipo_ausencia"]]+1;
        if ($rsAusencias["sustituido_por"]==0) {
                $nombre_sustituto="-";
                } else {
                $totales_ausencias_cubiertas[$rsAusencias["tipo_ausencia"]]=$totales_ausencias_cubiertas[$rsAusencias["tipo_ausencia"]]+1;
                $busca_sustituto=db_query("SELECT * FROM usuarios WHERE id_usuario=".$rsAusencias["sustituido_por"]);
                if (db_num_rows($busca_sustituto)!=0) {
                        $datos_sustituto=db_fetch_array($busca_sustituto);
                        $nombre_sustituto=mb_convert_case($datos_sustituto["Nombre"]." ".$datos_sustituto["Apellido1"]." ".$datos_sustituto["Apellido2"], MB_CASE_TITLE);
                        } else {
                        $nombre_sustituto="Desconocido";
                        }
                }
        ?>
        <tr valign=top>
                <td width="5%" align=center valign=top><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="<?=$colores_celdas[$rsAusencias["tipo_ausencia"]-1]?>"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td>
                <td width="10%"><small><small><a href="/gestion/docentes/cambiar_dia.php?id_docente=<?=$rsAusencias["id_usuario"]?>&dia=<?=strtotime($rsAusencias["fecha"])?>"><b><?= date("d/m/Y",strtotime($rsAusencias["fecha"])); ?></b></a></small></small></td>
                <td width="43%"><small><small><a href="/gestion/docentes/cambiar_dia.php?id_docente=<?=$rsAusencias["id_usuario"]?>&dia=<?=strtotime($rsAusencias["fecha"])?>"><b><?= mb_convert_case($rsAusencias["Nombre"]." ".$rsAusencias["Apellido1"]." ".$rsAusencias["Apellido2"],MB_CASE_TITLE); ?></a></b><?=$texto_faltas?></small></small></td>
                <td width="43%"><small><small><b><?= $nombre_sustituto; ?></b></small></small></td>
        </tr>
        <?php 
        }
        ?>
        </table>
<br>
<table width=98% bordercolor="#000000" border=1 style="border-collapse: collapse">
<tr>
        <td align="center" bgcolor="#efefef" colspan=3><font size=2><b>Resumen incidencias de personal entre los días <?=date("d/m/Y",$fecha_time_stamp);?> y <?=date("d/m/Y",($fecha_time_stamp+(86400*31)));?></b></font></td>
</tr>
<tr bgcolor="#efefef">
        <td><font size=2><b>Tipo</b></font></td>
        <td align="center"><font size=2><b>Total</b></font></td>
        <td align="center"><font size=2><b>Pendientes de cubrir</b></font></td>
</tr>
<?php 
for($ci=1;$ci<=count($nombres_ausencias);$ci++) {
        ?>
        <tr>
        <td size=16 align=left><table><tr><td><table border=1 style="border-collapse:collapse" cellpadding=0 cellspacing=0 bordercolor="#000000"><tr><td bgcolor="<?=$colores_celdas[$ci-1]?>"><img src="/imagen/nada.gif" width=16 height=16 border=0></td></tr></table></td><td> <font size=2><?=$nombres_ausencias[$ci-1];?></font></td></tr></table></td>
        <td align="center"><font size=2><?=$totales_ausencias[$ci];?></font></td>
        <td align="center"><font size=2><?=$totales_ausencias[$ci]-$totales_ausencias_cubiertas[$ci];?></font></td>
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
