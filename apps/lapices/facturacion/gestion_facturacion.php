<?php  require("../configuracion/parte_superior_privado.php"); ?>
<table width="100%" border=0 cellpadding=2 cellspacing=0>
<tr valign="top">
<td width="50%">
<table width="100%" border=0 cellpadding=0 cellspacing=0>
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <font size=2 face="<?= $fuente_general; ?>"><b>Facturación</b></font>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="98%">
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Emitir facturas" onclick="window.location.href='facturar_servicios.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Vista Previa facturas" onclick="window.location.href='/facturacion_vista_previa/facturar_servicios.php'" style="font-size:16px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Consultar facturación" onclick="window.location.href='/facturacion/seleccion_facturas.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Crear oden SEPA" onclick='window.open("csb19_a_unico.php","csb19","toolbar=no,width=550,height=500");' style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Crea orden de domiciliación SEPA de un recibo único, para el Banco de Santander. </font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Horas extras" onclick="window.location.href='seleccion_aula_horas.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Resumen de horas extras consumidas por alumno y mes.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Fact. por CIF y Ejercicio" onclick="window.location.href='/facturacion/listado_facturacion_ejercicio.php'" style="font-size:14px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Consulta facturación por cliente y ejercicio.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Becas y Subvenciones" onclick="window.location.href='/facturacion/seleccion_subvenciones.php'" style="font-size:16px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Gestión pagos" onclick="window.location.href='/facturacion/listado_alumnos.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Servicio catering" onclick="window.location.href='/facturacion/menus_servidos_catering.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>                        
                        <?php 
                        if ($nivel_acceso==4) {
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Gestión de gastos" onclick="window.location.href='/gestion_gastos/costes.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">...</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Gastos SOLO Lápices" onclick="window.location.href='/gestion_gastos_solo_lapices/costes.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Gestión de gastos únicamente considerando las escuelas de Lápices, sin tener en cuenta otros centros asociados.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Justif. gastos 08-09" onclick="window.location.href='/gestion_gastos/justificacion_costes.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Justificación de gastos, 2º ciclo curso 2008-2009</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Justif. gastos 09-10" onclick="window.location.href='/gestion_gastos/justificacion_costes09-10.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Justificación de gastos, 2º ciclo curso 2009-2010</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Justif. gastos 10-11" onclick="window.location.href='/gestion_gastos/justificacion_costes10-11.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Justificación de gastos, 2º ciclo curso 2010-2011</font></td>
                        </tr>
                        <?php 
                        if (in_array($idUsuario,$usuariosPermitidos)) {
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Ingresos Aulas" onclick="window.location.href='/gestion/centros/listado_ingresos.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Ingresos por aula sin contar subvenciones, bonos, extras, etc.</font></td>
                        </tr>    
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Ingresos Uniformes" onclick="window.location.href='/gestion/centros/listado_ingresos_uniforme.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Ingresos por ventas de uniformes</font></td>
                        </tr>                                                 
                        <?php
						}
                        }
                        ?>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Informe ventas" onclick="window.location.href='/gestion/material/informe_ventas.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Resumen de servicios facturados durante un periodo seleccionado.</font></td>
                        </tr>
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Ventas por escuela" onclick="window.location.href='/gestion/material/informe_ventas_comparado.php'" style="font-size:20px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Ventas desglosadas por escuelas durante un periodo dado.</font></td>
                        </tr>                        
                        <?php 
                        if ($nivel_acceso==4) {
	                    ?>   
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Facturación por cursos" onclick="window.open('resumen_facturacion_cursos.php')" style="font-size:18px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Resumen de la facturación total de los últimos cursos, desglosado por tipo de servicios.</font></td>
                        </tr>                         
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Facturación mensual" onclick="window.open('resumen_facturacion_meses.php')" style="font-size:18px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Resumen de la facturación mensual de los últimos cursos, desglosado por tipo de servicios.</font></td>
                        </tr>     
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Gastos mensuales" onclick="window.open('/gestion_gastos/resumen_costes.php')" style="font-size:18px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Resumen de la gastos mensuales.</font></td>
                        </tr>   
                        <tr valign="top">
                                <td width=210><font size=2 face="<?= $fuente_general; ?>"><input type="button" value="Facturación comedor" onclick="window.open('resumen_facturacion_comedor.php')" style="font-size:18px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Resumen facturación comedor.</font></td>
                        </tr>        
	                     <?php   
	                        $obsoleto=true;
	                        if ($obsoleto==false) {
                        ?>
                        <form name="comparar" action="/alumnos/informe_facturacion_vert.php" target="_new" method="GET">
                        <tr valign="top">
                                <td width=210 align="center"><font size=2 face="<?= $fuente_general; ?>">
                                Curso seleccionado: <select name="id_curso_act" size=1 style="font-size: 10px"><?php 
                                
                                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

                                if (db_num_rows($busca_curso_actual)!=0) {
                                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                                        $id_curso_actual=$datos_curso_actual["id_curso"];
                                        $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];
                                        } else {
                                        $id_curso_actual=0;
                                        }
                                
                                $busca_curso=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio;");
                                
                                while ($lista_cursos=db_fetch_array($busca_curso)) {
                                ?>
                                <option value="<?=$lista_cursos["id_curso"];?>"<?php  if ($lista_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$lista_cursos["nombre"];?></option>
                                <?php 
                                }
                                
                                
                                ?></select><br>
                                Curso siguiente: <select name="id_curso_sig" size=1 style="font-size: 10px"><?php 
                                
                                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='$fecha_fin_curso_actual'") or die(db_error());

                                if (db_num_rows($busca_curso_actual)!=0) {
                                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                                        $id_curso_siguiente=$datos_curso_actual["id_curso"];
                                        } else {
                                        $id_curso_siguiente=0;
                                        }
                                
                                $busca_curso=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio;");

                                while ($lista_cursos=db_fetch_array($busca_curso)) {
                                ?>
                                <option value="<?=$lista_cursos["id_curso"];?>"<?php  if ($lista_cursos["id_curso"]==$id_curso_siguiente) { print " selected"; } ?>><?=$lista_cursos["nombre"];?></option>
                                <?php 
                                }
                                
                                ?></select><br><br>
                                <input type="button" value="Exportar informe a Excel" onclick="self.document.comparar.submit()" target="_new" style="font-size:14px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Genera un informe con la facturación por aula durante el curso seleccionado y permite hacer una previsión para el curso siguiente.</font></td>
                        </tr>
                        </form>
                        <form name="comparar2" action="/alumnos/informe_facturacion_V2.php" target="_new" method="GET">
                        <tr valign="top">
                                <td width=210 align="center"><font size=2 face="<?= $fuente_general; ?>">
                                Curso seleccionado: <select name="id_curso_act" size=1 style="font-size: 10px"><?php 

                                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio<='".date("Y-m-d")."' AND fecha_fin>='".date("Y-m-d")."'");

                                if (db_num_rows($busca_curso_actual)!=0) {
                                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                                        $id_curso_actual=$datos_curso_actual["id_curso"];
                                        $fecha_fin_curso_actual=$datos_curso_actual["fecha_fin"];
                                        } else {
                                        $id_curso_actual=0;
                                        }

                                $busca_curso=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio;");

                                while ($lista_cursos=db_fetch_array($busca_curso)) {
                                ?>
                                <option value="<?=$lista_cursos["id_curso"];?>"<?php  if ($lista_cursos["id_curso"]==$id_curso_actual) { print " selected"; } ?>><?=$lista_cursos["nombre"];?></option>
                                <?php 
                                }


                                ?></select><br>
                                Comparar con curso: <select name="id_curso_sig" size=1 style="font-size: 10px"><?php 

                                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE fecha_inicio>='$fecha_fin_curso_actual'") or die(db_error());

                                if (db_num_rows($busca_curso_actual)!=0) {
                                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                                        $id_curso_siguiente=$datos_curso_actual["id_curso"];
                                        } else {
                                        $id_curso_siguiente=0;
                                        }

                                $busca_curso=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio;");

                                while ($lista_cursos=db_fetch_array($busca_curso)) {
                                ?>
                                <option value="<?=$lista_cursos["id_curso"];?>"<?php  if ($lista_cursos["id_curso"]==$id_curso_siguiente) { print " selected"; } ?>><?=$lista_cursos["nombre"];?></option>
                                <?php 
                                }

                                ?></select><br><br>
                                <input type="button" value="Exportar informe a Excel" onclick="self.document.comparar2.submit()" target="_new" style="font-size:14px; width: 200; height: 32"></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>">Genera un informe con la facturación por edades durante el curso seleccionado y permite hacer una comparación con otro curso.</font></td>
                        </tr>
                        </form>
                        <?php 
	                        }
                        }
                        ?>
                        </table>
                </td>
        </tr>
</table>
</td>
</tr>
</table>
<?php

echo($_SERVER['REMOTE_ADDR']);

require("../configuracion/parte_inferior.php"); ?>
