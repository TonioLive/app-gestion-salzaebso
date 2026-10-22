<?php  require("../configuracion/parte_superior_privado.php"); ?>
<?php 

$id_cliente=$_GET["id_cliente"];
$mes=$_GET["mes"];
$ejercicio=$_GET["ejercicio"];
$centro_factura=$_GET["centro_factura"];
if (strlen($ejercicio)==0) {
        $ejercicio=date("Y");
        }
$filtros="year(facturas_emitidas.fecha_emision)=".$ejercicio;
if (strlen($id_cliente)!=0) {
        $filtros=$filtros." AND facturas_emitidas.id_cliente=".$id_cliente;
        }
if (strlen($mes)!=0) {
        $filtros=$filtros." AND month(facturas_emitidas.fecha_emision)=".$mes;
        }
if (strlen($centro_factura)!=0) {
        $filtros=$filtros." AND alumnos.id_centro=".$centro_factura;
        }


$morisidades[0]="<font color=green>En plazo</font>";
$morisidades[1]="<font color=red>Leve</font>";
$morisidades[2]="<font color=red>Grave</font>";
$morisidades[3]="<font color=red>Muy Grave</font>";
$morisidades[4]="<font color=red>Expulsión</font>";

?>
<script>

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoadDato(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST','guarda_dato.php',true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);

    }
}

</script>
<script language="Javascript">
function pagos_a_cuenta(id_factura) {
        window.open("pagos_a_cuenta.php?id_factura="+id_factura,'pagos_a_cuenta','width=550,height=450');
        }
        
function comprobar_y_enviar() {

        top.window.document.albaranes.submit();
}
function eliminar(id_factura) {
        if (confirm("Se eliinará la factra seleccionada.\n\n¿Estas seguro?")) {
                top.window.location.href='eliminar_factura.php?centro_factura=<?=$centro_factura?>&id_cliente=<?=$id_cliente?>&ejercicio=<?=$ejercicio?>&mes=<?=$mes?>&id_factura='+id_factura;
                }
        }
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form action="chequear_subvenciones.php" method="POST" id="listado_subvenciones" name="listado_subvenciones">
<input type="hidden" name="id_cliente" value="<?=$id_cliente;?>">
<input type="hidden" name="mes" value="<?=$mes;?>">
<input type="hidden" name="ejercicio" value="<?=$ejercicio;?>">
<input type="hidden" name="centro_factura" value="<?=$centro_factura;?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                        <br>
                         <input type="button" value="Salir" onclick="window.location.href='seleccion_subvenciones.php';" style="font-size: 10px">
                         <br><br>
                        <table border=0 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=1 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#FFFFFF">
                           <td align="center" colspan=9><font size=2 face="<?= $fuente_general; ?>"><b>Listado de Becas y Subvenciones <?=$nombre_mes[$mes];?> <?=$ejercicio;?></b><br><small>Puedes ver, imprimir o editar las facturas haciendo clic en los vínculos que encontrarás en cada línea.<br></small></font></td>
                        </tr>
                        <?php 
                        $consulta1="SELECT facturas_emitidas.*, detalles_factura.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, detalles_factura,alumnos, usuarios WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND detalles_factura.subvencion=1 AND $filtros ORDER BY alumnos.id_centro, alumnos.aula, detalles_factura.concepto, fecha_emision DESC";

                        $consulta_albaranes_cliente = db_query($consulta1) or die (db_error());
                        
                        $numRecords = db_num_rows($consulta_albaranes_cliente);

                        if ($numRecords==0) {
                        ?>
                        <tr valign="top" bgcolor="#FFFFFF">
                          <td align="center" colspan=9><font size=2 face="<?= $fuente_general; ?>">No se han encontrado subvenciones con los criterios seleccionados...</font></td>
                        </tr>
                        <?php 
                        } else {
                        ?>
                        <tr valign="top" bgcolor="#ececec">
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Nº de Factura</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Fecha de emisión</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Alumno</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Concepto</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Centro</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Aula</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Importe</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">Cobrada</font></td>
                          <td align="center"><font size=2 face="<?= $fuente_general; ?>">&nbsp;</font></td>
                        </tr>
                        <?php 
                        }
$contador=0;
                        
$total_subvenciones=0;
$total_cobrado_subvenciones=0;
while($datos_factura = db_fetch_array($consulta_albaranes_cliente)){


// Mira si los albaranes de la factura y calcula los totales
$id_factura=$datos_factura["id_factura"];
                        
// Busca datos del centro
$busca_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_factura["centro_factura"]));
        if (db_num_rows($busca_centro)!=0) {
                $datos_centro=db_fetch_array($busca_centro);
                $nombre_centro=$datos_centro["nombre_centro"];
                $prefijo_centro=$datos_centro["prefijo_centro"];
                $razon_social=$datos_centro["razon_social"];
                $domicilio=$datos_centro["domicilio_centro"];
                $cp=$datos_centro["CP_centro"];
                $poblacion=$datos_centro["poblacion_centro"];
                $telefono=$datos_centro["telefono_centro"];
                $fax=$datos_centro["fax_centro"];
                $cif=$datos_centro["CIF_centro"];
                }
$nombre_aula="";
$busca_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_factura["aula"]));
if (db_num_rows($busca_aula)!=0) {
$datos_aula=db_fetch_array($busca_aula);
$nombre_aula=$datos_aula["nombre"];
}
        // Comprueba si es subvencion
        
                $total_linea=$datos_factura["cantidad"]*$datos_factura["precio"];
                $total_dto=$total_linea*round(($datos_factura["dto"]/100),2);
                $base_linea=$total_linea-$total_dto;
                $iva_linea=$base_linea*$datos_factura["tipo_iva"];
                $total_abs=$base_linea+$iva_linea;
        
                $total_subvenciones=$total_subvenciones+$total_abs;

                // $query2=mysql_query("UPDATE detalles_factura SET subvencion_cobrada=1 WHERE id_detalle=".$datos_factura["id_detalle"]);
                
                if ($datos_factura["subvencion_cobrada"]==1) {
                        $total_cobrado_subvenciones=$total_cobrado_subvenciones+$total_abs;
                        }
                               ?>
                                <tr valign="top" bgcolor="#FFFFFF">
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_de_factura["serie"].$datos_factura["num_factura"];?>/<?=$datos_factura["ejercicio_factura"];?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("j/n/Y",strtotime($datos_factura["fecha_emision"]));?></font></td>
                                  <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_factura["Apellido1"]." ".$datos_factura["Apellido2"].", ".$datos_factura["Nombre"];?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_factura["concepto"];?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_centro;?></font></td>
                                  <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_aula;?></font></td>
                                  <td align="right"><font size=1 face="<?= $fuente_general; ?>"><?=number_format(-1*$total_abs, 2, ',', '.');?> €</font></td>
                                  <td align="center" width=10%><font size=1 face="<?= $fuente_general; ?>"><input onclick="sendAndLoadDato({desc:'fila',dato:<?=$datos_factura["id_detalle"]?>},{desc:'campo',dato:'subvencion_cobrada'},{desc:'nombre_fila',dato:'id_detalle'},{desc:'tabla',dato:'detalles_factura'},{desc:'es_checkbox',dato:'1'});" type="checkbox" value="SI" id=PAG<?= $datos_factura["id_detalle"]; ?> name=PAG<?= $datos_factura["id_detalle"]; ?><?php  if ($datos_factura["subvencion_cobrada"]==1) { print " checked"; } ?>></font></td>
                                  <td align="center" width=12%><font size=1 face="<?= $fuente_general; ?>"><a href="/pdf/factura.php?id_factura=<?=$datos_factura["id_factura"]?>" target="_blank">Ver/Imprimir Factura</a></font></td>
                                </tr>
                                <?php 
                                $contador=$contador+1;
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
                           <td align="center" colspan=2><font size=2 face="<?= $fuente_general; ?>"><b>Resumen <?=$nombre_mes[$mes];?> <?=$ejercicio;?></b></font></td>
                        </tr>
                        <tr valign="top">
                          <td align="right" width="50%" bgcolor="#ececec"><font size=2 face="<?= $fuente_general; ?>">Nº de Subvenciones:</font></td>
                          <td align="right" width="50%" bgcolor="#FFFFFF"><font size=2 face="<?= $fuente_general; ?>"><?=$contador;?></font></td>
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
                        </table>
                        <?php 
                        }
                        ?>
                         <br>
                         <input type="button" value="Salir" onclick="window.location.href='seleccion_subvenciones.php';" style="font-size: 10px">
                         <br><br>
                </td>
        </tr>
</table><br><br>
<font size=1><i>Fecha del informe: <?=date("d/m/Y")?></i></font>
<?php  require("../configuracion/parte_inferior.php"); ?>
