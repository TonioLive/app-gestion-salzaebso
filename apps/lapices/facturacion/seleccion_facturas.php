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
        }

 ?>
<script language="Javascript">
function enviar(destino) {

        if (destino==0) {
                self.document.albaranes.action="listado_facturas.php";
                }   

        if (destino==1) {
                self.document.albaranes.action="resumen_facturacion.php";
                }
                
        if (destino==2) {
                self.document.albaranes.action="listado_facturas_exportar.php";
                self.document.albaranes.target="_blank";
                }                

        self.document.albaranes.submit();

        }
</script>
<br><br>
<table width="85%" border=0 cellpadding=0 cellspacing=0>
<form name="albaranes" action="listado_facturas.php" method="GET">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Resumen de facturacíon</b></font>
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
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Aula:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="id_aula_facturas" size=1 style="font-size: 10px">
                                <option value="">Todas</option>
        <?php 
        $consulta_centros=db_query("SELECT * FROM centros,centro_aulas WHERE centros.id_centro=centro_aulas.id_centro AND centro_aulas.id_curso=$id_curso_actual ORDER BY centros.nombre_centro,centro_aulas.nombre;");
        while($centros=db_fetch_array($consulta_centros)){
        ?>
        <option value="<?=$centros["id_aula"];?>"><?=$centros["nombre_centro"];?>: <?=$centros["nombre"];?></option>
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
                                ?></select> <select name="ejercicio" style="font-size: 10px" size=1>
                                <option value="">Todos</option>
                                <?php 
                                // Busca número de factura actual en el ejercicio actual
                                for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y"); $c_ano=$c_ano+1)
                                {
                                ?>
                                <option value="<?php    echo $c_ano; ?>"<?php if ($c_ano==strftime("%Y")) { print " selected"; } ?>><?php    echo $c_ano; ?></option>
                                <?php 
                                }
                                ?></select></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#FFFFFF">
                                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Alumnos del curso:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="curso_mostrado" size=1 style="font-size: 10px" onchange="document.seleccion_fecha.submit();">
                                <option value="0">-</option>
                                <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?></select></font></td>
                        </tr>
                         </table>
                         <input type="button" value="Cancelar" onclick="window.location.href='/index.php'" style="font-size: 10px"> <input type="button" onclick="enviar(0)" value="Ver listado de facturas >>" style="font-size: 10px"> <input type="button" onclick="enviar(1)" value="Ver resumen de facturación >>" style="font-size: 10px"> <br /><br />
                         <input type="button" onclick="enviar(2)" value="Exportar listado de facturas >>" style="font-size: 10px">
                </td>
        </tr>
</form>
</table><br><br>
<?php  require("../configuracion/parte_inferior.php"); ?>
