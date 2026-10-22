<?php  require("../configuracion/parte_superior_privado_ancho.php");

        // Busca el curso actual
        $curso_seleccionado=$_GET["curso_seleccionado"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $id_curso_actual=$curso_seleccionado;
        }
        
        if ($id_curso_actual!=0) {
                                $filtro=" AND alumnos.id_curso=$id_curso_actual";
                                } else {
                                $filtro="";
                                }

?>
<SCRIPT LANGUAGE=javascript>
<!--

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6" } 
function cambiacolor_out(celda,color){ celda.style.backgroundColor=color }

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
//-->
</SCRIPT>
<div align="center"><center>
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
    <td align="center"><small><b>Listado completo de alumnos de la Escuela <i>"<?=$nombre_centro_actual;?>"</i></b></small></td>
  </tr>
<?php 

$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual$filtro ORDER BY alumnos.aula, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre");

if (db_num_rows($busca_alumnos)!=0) {
?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <tr>
        <td colspan=4><font size=1 face="<?= $fuente_general; ?>">Para editar un alumno, haz clic sobre su nombre o sobre el botón <b>Editar</b> ( <img src="modificar.gif" align="absmiddle">)</font></td>
        </tr>
        <form name="listado_alumnos" method="POST">
        <?php 
$morisidades[0]="<font color=green>No tiene pagos atrasados.</font>";
$morisidades[1]="<font color=red>Leve</font>";
$morisidades[2]="<font color=red>Grave</font>";
$morisidades[3]="<font color=red>Muy Grave</font>";
$morisidades[4]="<font color=red>Expulsión</font>";

$contador=0;


$total_Sesiones=0;
$total_reservado_sesiones=0;

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

$total_absoluto=0;
$total_cobrado=0;
$total_subvenciones=0;
$total_cobrado_subvenciones=0;
$aula_actual=0;
$curso_actual=0;

$total_subvenciones_proveedor=array();
$total_subvenciones_pagadas_proveedor=array();

while($listado_alumnos=db_fetch_array($busca_alumnos)){

$listado_morosidades="";
$total_facturado_alumno=0;
$nivel_maximo_morosidad=0;
$texto_morosidad="";


                        if ($curso_actual!=$listado_alumnos["id_curso"]) {
                        // Datos del curso
                        $nombre_curso="";
                        $busca_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($listado_alumnos["id_curso"]));
                        if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $numero_color_curso=intval(substr($listado_alumnos["id_curso"],strlen($listado_alumnos["id_curso"])-1,1));
                                $nombre_curso=$datos_curso["nombre"];
                                }
                        ?>
                        <tr valign="top" bgcolor="<?=$colores[$numero_color_curso]?>">
                                <td align="left" colspan=9><font size=3 face="<?= $fuente_general; ?>"><b>Alumnos Curso: <?=$nombre_curso;?></b></font></td>
                        </tr>
                        <?php 
                        $curso_actual=$listado_alumnos["id_curso"];
                        }
                        
                        if ($aula_actual!=$listado_alumnos["aula"]) {
                        // Datos del aula
                        $nombre_aula="";
                        $busca_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($listado_alumnos["aula"]));
                        if (db_num_rows($busca_aula)!=0) {
                                $datos_aula=db_fetch_array($busca_aula);
                                $nombre_aula=$datos_aula["nombre"];
                                }
                        ?>
                        <tr valign="top" bgcolor="#ececec">
                                <td align="left" colspan=9><font size=2 face="<?= $fuente_general; ?>"><b><?=$nombre_centro_actual." - ".$nombre_aula;?></b></font></td>
                        </tr>
                        <tr bgcolor="#efefef">
                                <td><font size=1 face="<?= $fuente_general; ?>"><b>ID Alumno</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><b>Nombre</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Población</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Nº Sesiones Contratadas</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Total contratado</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Total recibos emitidos</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Total recibos pagados</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Estado</b></font></td>
                                <td align="center"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                        </tr>
                        <?php 
                        $aula_actual=$listado_alumnos["aula"];
                        }
        

// Comprueba deudas y pagos
        
$consulta1=array('SELECT facturas_emitidas.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, alumnos, usuarios WHERE facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND facturas_emitidas.id_cliente=?', 's', array($listado_alumnos["id_alumno"]));
$consulta_albaranes_cliente = db_query_params($consulta1[0], $consulta1[1], $consulta1[2]) or die (db_error());

$total_factura_alumno=0;
$total_cobrado_alumno=0;


while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){

// Mira si los albaranes de la factura y calcula los totales
$id_factura=$datos_factura["id_factura"];
// Calcula totales

// Busca albaranes
$consulta_datos_albaranes = db_query_params('SELECT * FROM detalles_factura WHERE id_factura=?', 's', array($id_factura)) or die (odbc_errormsg());

$total_dto=0;
$total_base=0;
$total_base_0=0;
$total_base_4=0;
$total_base_7=0;
$total_base_16=0;
$total_iva=0;
$total_iva_0=0;
$total_iva_4=0;
$total_iva_7=0;
$total_iva_16=0;
$total_recargo=0;

while($datos_albaranes = db_fetch_array($consulta_datos_albaranes)){

        $total_linea=$datos_albaranes["cantidad"]*$datos_albaranes["precio"];
        $total_dto=$total_linea*round(($datos_albaranes["dto"]/100),2);
        $base_linea=$total_linea-$total_dto;
        $iva_linea=$base_linea*$datos_albaranes["tipo_iva"];
        $total_abs=$base_linea+$iva_linea;

        $total_base=$total_base+$base_linea;
        $total_iva=$total_iva+$iva_linea;
        switch($datos_albaranes["tipo_iva"])
                {
                case 0:
                        $total_iva_0=$total_iva_0+$iva_linea;
                        $total_base_0=$total_base_0+$base_linea;
                break;
                case 0.04:
                        $total_iva_4=$total_iva_4+$iva_linea;
                        $total_base_4=$total_base_4+$base_linea;
                break;
                case 0.10:
                        $total_iva_7=$total_iva_7+$iva_linea;
                        $total_base_7=$total_base_7+$base_linea;
                break;
                case 0.21:
                        $total_iva_16=$total_iva_16+$iva_linea;
                        $total_base_16=$total_base_16+$base_linea;
                break;
                }
                
                                if (substr($datos_albaranes["concepto"],0,22)=="Subvencion Conselleria") {
                                                        $proveedor_actual=141;
                                                        }                               
                if ((substr($datos_albaranes["concepto"],0,14)=="Cheque Escolar") or (substr($datos_albaranes["concepto"],0,15)=="Beca de Comedor")) {
                                                        $proveedor_actual=120;                                                        
                                                        } 
         
        // Comprueba si es subvencion
        if ($datos_albaranes["subvencion"]==1) {
                $total_subvenciones=$total_subvenciones+$total_abs;
                $total_subvenciones_proveedor[$proveedor_actual]=$total_subvenciones_proveedor[$proveedor_actual]+$total_abs;
                }
        if ($datos_albaranes["subvencion_cobrada"]==1) {
                $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                $total_subvenciones_pagadas_proveedor[$proveedor_actual]=$total_subvenciones_pagadas_proveedor[$proveedor_actual]+$total_abs;
                }
        }
        $total_facturado_alumno=$total_base+$total_iva+$total_recargo;
        // Comprueba pagos a cuenta
        $total_a_cuenta=0;

        $busca_pagos=db_query_params('SELECT * FROM pagos_a_cuenta WHERE id_factura=?', 's', array($id_factura));
        while ($pagos=db_fetch_array($busca_pagos)) {
                $total_a_cuenta=$total_a_cuenta+$pagos["importe_pago"];
                }
                
                
       $total_a_cuenta=round($total_a_cuenta,2);
       $total_factura_sel=round(($total_base+$total_iva+$total_recargo),2);

        if ($total_a_cuenta<$total_factura_sel) {
                $morosidad=true;
                } else {
                $morosidad=false;
                }

        $tiempo_desde_emision=(strtotime(date("Y-m-d"))-strtotime($datos_factura["fecha_emision"]))/86400;

        $morosidad_actual=0;

                if ($datos_factura["forma_de_pago"]==0) {
                        $forma_de_pago="Domiciliación bancaria<br>Cuenta: ".$datos_factura["banco_cuenta"];
                        if (($tiempo_desde_emision<=15) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>15) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>30) {
                                $morosidad_actual=4;
                                }
                        } else {
                        $forma_de_pago="Contado";
                        if (($tiempo_desde_emision<=5) and ($tiempo_desde_emision>0)) {
                                $morosidad_actual=1;
                                }
                        if ($tiempo_desde_emision>5) {
                                $morosidad_actual=2;
                                }
                        if ($tiempo_desde_emision>10) {
                                $morosidad_actual=3;
                                }
                        if ($tiempo_desde_emision>20) {
                                $morosidad_actual=4;
                                }
                        }

        if ($morosidad==true) {
                if ($morosidad_actual>=$nivel_maximo_morosidad) {
                        $listado_morosidades=" F:".$id_factura." - C:".$total_a_cuenta." - T:".($total_base+$total_iva+$total_recargo);
                        $nivel_maximo_morosidad=$morosidad_actual;
                        }
                }

                $total_cobrado=$total_cobrado+round($total_a_cuenta,2);
                $total_absoluto=$total_absoluto+round(($total_base+$total_iva+$total_recargo),2);

                                $total_factura_alumno=$total_factura_alumno+round($total_a_cuenta,2);
                                $total_cobrado_alumno=$total_cobrado_alumno+round(($total_base+$total_iva+$total_recargo),2);;


                $contador=$contador+1;
        }
        
        
        // Fin facturas
        // $texto_morosidad=$morisidades[$nivel_maximo_morosidad];
        if ($total_factura_alumno<$total_cobrado_alumno) {
                $texto_morosidad="<font color=red>Pendiente de cobro: ".number_format(($total_factura_alumno-$total_cobrado_alumno)*-1,2,",",".")." €</font>"; 
                } else {
                $texto_morosidad=$morisidades[0];
                } 
        
        $total_sesiones_contratadas=0;
        $total_reservado_alumno=0;
                
        $id_inscripcion=$listado_alumnos["id_alumno"];        
                $busca_cuotas=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=?', 's', array($id_inscripcion)) or die(db_error());
        if (db_num_rows($busca_cuotas)==0) {
                $no_cuotas=true;
                } else {
                $no_cuotas=false;
                
                while($cuotas=db_fetch_array($busca_cuotas)) {
                        $total_Sesiones=$total_Sesiones+($cuotas["numero_bonos"]*$cuotas["cantidad"]);
                        $total_sesiones_contratadas=$total_sesiones_contratadas+($cuotas["numero_bonos"]*$cuotas["cantidad"]);
                        $total_reservado_alumno=round($total_reservado_alumno+((($cuotas["cantidad"]*$cuotas["PRECIO_VENTA"])*(1-($cuotas["DESCUENTO"]/100)))*(1+$cuotas["porcentaje_iva"])),2);
                        $total_reservado_sesiones=round($total_reservado_sesiones+((($cuotas["cantidad"]*$cuotas["PRECIO_VENTA"])*(1-($cuotas["DESCUENTO"]/100)))*(1+$cuotas["porcentaje_iva"])),2);
                        }        
                        
                }

                
        
        if ($total_cobrado_alumno==0) {
                $total_cobrado_alumno="<font color=#ff0000>$total_cobrado_alumno</font>";
                }
        if ($total_factura_alumno==0) {
                $total_factura_alumno="<font color=#ff0000>$total_factura_alumno</font>";
                }                                        
        ?>
        <tr>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$listado_alumnos["id_usuario"]?></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><a name="id<?=$listado_alumnos["id_alumno"];?>" target="datos_alumno" href="/alumnos/nuevo_alumno1.php?u=<?=$listado_alumnos["id_usuario"];?>"><b><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?></b></a></font></td>
        <td><font size=1 face="<?= $fuente_general; ?>"><?=$listado_alumnos["Poblacion"]?></font></td>
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$total_sesiones_contratadas;?></font></td>
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$total_reservado_alumno;?> €</font></td> 
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$total_cobrado_alumno;?> €</font></td> 
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=$total_factura_alumno;?> €</font></td> 
        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$texto_morosidad;?></font></td>        
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a target="facturas_alumno" href="listado_facturas_alumno.php?id_cliente=<?=$listado_alumnos["id_alumno"];?>" title="Gestionar pagos de facturas"><img src="servicios.gif" border=0></a> <a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="editar.gif" border=0></a></font></td>
        </tr>
        <?php 
        }
        ?>
        </form>
        </table>
                         <br>
                         <?php 
                         if ($nivel_acceso==4) {
                         ?>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="98%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Resumen <?=$ejercicio;?></b></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de sesiones contratadas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=ceil($total_Sesiones);?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Precio medio de la de sesión:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_reservado_sesiones/$total_Sesiones,2,",",".");?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de Facturas emitidas:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$contador;?></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total facturado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_cobrado, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_absoluto-$total_cobrado, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe total subvenciones:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_subvenciones, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones cobrado:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_cobrado_subvenciones, 2, ',', '.');?> €</font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones pendiente de cobro:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(-1*($total_subvenciones-$total_cobrado_subvenciones), 2, ',', '.');?> €</font></td>
                        </tr>
                        
<tr><td colspan="2">&nbsp;</td></tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_proveedor[120]*-1,2,",",".");?> €</font></td>
                                </tr>
                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe cobrado subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_pagadas_proveedor[120]*-1,2,",",".");?> €</font></td>
                                </tr>                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro subvenciones Ayuntamiento</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($total_subvenciones_proveedor[120]-$total_subvenciones_pagadas_proveedor[120])*-1,2,",",".");?> €</font></td>
                                </tr> 
                        
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Total subvenciones Conselleria Educación <?=$datos_familia["nombre_desglose"];?></font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_proveedor[141]*-1,2,",",".");?> €</font></td>
                                </tr>
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe cobrado subvenciones Conselleria Educación</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format($total_subvenciones_pagadas_proveedor[141]*-1,2,",",".");?> €</font></td>
                                </tr>                                
                                <tr valign="top" bgcolor="#ececec">
                                        <td align="right"><font size=2 face="<?= $fuente_general; ?>">Importe pendiente de cobro subvenciones Conselleria Educación</font></td>
                                        <td align="right" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=number_format(($total_subvenciones_proveedor[141]-$total_subvenciones_pagadas_proveedor141)*-1,2,",",".");?> €</font></td>
                                </tr> 
                        </table>
                        <?php 
                        }
                        ?>
        </td>
</tr>
<?php 
} else {
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><br><br><small><small><b>Todavia no se ha inscrito ningún alumno en esta escuela.<br><br><br></td>
  </tr>
<?php 

}
?>
</table>
<br>
<input type="button" onclick="window.location.href='gestion_facturacion.php'" value="Volver al menú facturación" style="font-size: 10px">
<br>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>

