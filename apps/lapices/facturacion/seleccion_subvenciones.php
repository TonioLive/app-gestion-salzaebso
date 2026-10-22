<?php  require("../configuracion/parte_superior_privado.php");

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                $anio_inicio_curso=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));
                } else {
                $id_curso_actual=0;
                $anio_inicio_curso=date("Y");
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$curso_seleccionado");
        $id_curso_actual=$curso_seleccionado;
        }

?>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="albaranes" action="listado_subvenciones.php" method="GET">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Resumen de Becas Y Subvenciones</b></font>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Escuela:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="centro_factura" size=1 style="font-size: 10px">
                                <option value="">Todas</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_centro"];?>"<?php if ($centro_actual==$centros["id_centro"]) { print " selected"; }?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
        <?php 
        }
        ?>
        </select></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Mes y año:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="mes" style="font-size: 10px" size=1>
                                <option value="">Todos</option>
                                <?php 
                                // Busca número de factura actual en el ejercicio actual
                                for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1)
                                {
                                ?>
                                <option value="<?php    echo $c_mes; ?>"<?php if ($c_mes==strftime("%m")) { print " selected"; } ?>><?=$nombre_mes[($c_mes)];?></option>
                                <?php 
                                }
                                ?></select> <select name="ejercicio" style="font-size: 10px" size=1><?php 
                                // Busca número de factura actual en el ejercicio actual
                                for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y"); $c_ano=$c_ano+1)
                                {
                                ?>
                                <option value="<?php    echo $c_ano; ?>"<?php if ($c_ano==strftime("%Y")) { print " selected"; } ?>><?php    echo $c_ano; ?></option>
                                <?php 
                                }
                                ?></select></font></td>
                        </tr>
                         </table>
                         <input type="button" value="Cancelar" onclick="window.location.href='/index.php'" style="font-size: 10px"> <input type="submit" value="Ver listado de subvenciones >>" style="font-size: 10px">
                </td>
        </tr>
</form>
</table><br><br>
<font size=2 face="<?= $fuente_general; ?>"><b>Listados de Bonos</b></font>
<table border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
        <tr valign="top" bgcolor="#efefef">
        <td><font size=2 face="<?= $fuente_general; ?>"><b>Cuatrimestre</b></font></td>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <td><font size=2><b><?=$centros["nombre_centro"];?></b></font></td>
        <?php 
        }
        ?>
        </tr>
        <tr valign="top">
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>1º</b></font></td>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <td bgcolor="#FFFFFF"><font size=2>
        <a href="/gestion/centros/listado_cheque.php?edad=<?=$anio_inicio_curso?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 0-1</a><br>
        <a href="/gestion/centros/listado_cheque.php?edad=<?=$anio_inicio_curso-1?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 1-2</a><br>
        <a href="/gestion/centros/listado_cheque.php?edad=<?=$anio_inicio_curso-2?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 2-3</a><br>
        <a href="/gestion/centros/listado_cheque.php?edad=&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos Nuevos</a><br>
        </font></td>
        <?php 
        }
        ?>
        </tr>
        <tr valign="top">
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>2º</b></font></td>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <td bgcolor="#FFFFFF"><font size=2>
        <a href="/gestion/centros/listado_cheque1.php?edad=<?=$anio_inicio_curso?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 0-1</a><br>
        <a href="/gestion/centros/listado_cheque1.php?edad=<?=$anio_inicio_curso-1?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 1-2</a><br>
        <a href="/gestion/centros/listado_cheque1.php?edad=<?=$anio_inicio_curso-2?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 2-3</a><br>
        <a href="/gestion/centros/listado_cheque1.php?edad=&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos Nuevos</a><br>
        </font></td>
        <?php 
        }
        ?>
        </tr>
        <tr valign="top">
        <td bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>3º</b></font></td>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros WHERE mostrar_en_lapices=1 ORDER BY centro_propio DESC, nombre_centro;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <td bgcolor="#FFFFFF"><font size=2>
        <a href="/gestion/centros/listado_cheque2.php?edad=<?=$anio_inicio_curso?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 0-1</a><br>
        <a href="/gestion/centros/listado_cheque2.php?edad=<?=$anio_inicio_curso-1?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 1-2</a><br>
        <a href="/gestion/centros/listado_cheque2.php?edad=<?=$anio_inicio_curso-2?>&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos 2-3</a><br>
        <a href="/gestion/centros/listado_cheque2.php?edad=&id_centro_selec=<?=$centros["id_centro"];?>&id_curso_selec=<?=$id_curso_actual?>" target="_new">- Alumnos Nuevos</a><br>
        </font></td>
        <?php 
        }
        ?>
        </tr>
</table>

<?php  require("../configuracion/parte_inferior.php"); ?>
