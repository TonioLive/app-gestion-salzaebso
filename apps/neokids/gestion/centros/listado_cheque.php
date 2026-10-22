<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$concepto[1]="Subvencion Conselleria Educación 0-1 años";
$concepto[2]="Subvencion Conselleria Educacion 1-2 años";
$concepto[3]="Subvencion Conselleria Educacion 2-3 años";
$concepto[4]="Subvencion Conselleria Educacion 3-4 años";
$concepto[5]="Subvencion Conselleria Educacion 4-5 años";
$concepto[6]="Subvencion Conselleria Educacion 5-6 años";

// Datos
$edad=$_GET["edad"];
$id_centro_selec=$_GET["id_centro_selec"];
$id_curso_selec=$_GET["id_curso_selec"];

// Datos curso
$busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_selec));
$datos_curso_actual=db_fetch_array($busca_curso_actual);

$anio_actual=date("Y",strtotime($datos_curso_actual["fecha_inicio"]));

if (strlen($edad)!=0) {
        $edad_anios=$anio_actual-$edad+1;
        } else {
        $edad_anios=0;
        }
$textoencabezado[0]="";
$textoencabezado[1]="FINS A 1 ANY/HASTA 1 AÑO";
$textoencabezado[2]="DE 1 A 2 ANYS/DE 1 A 2 AÑOS";
$textoencabezado[3]="DE 2 A 3 ANYS/DE 2 A 3 AÑOS";
$textoencabezado[4]="DE 3 A 4 ANYS/DE 3 A 4 AÑOS";
$textoencabezado[5]="DE 4 A 5 ANYS/DE 4 A 5 AÑOS";
$textoencabezado[6]="DE 5 A 6 ANYS/DE 5 A 6 AÑOS";

if ($edad_anios==0) {
        $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro_selec AND id_curso=$id_curso_selec AND (anio_maximo=".($anio_actual)." OR anio_maximo=".($anio_actual-1)." OR anio_maximo=".($anio_actual-2).")") or die(db_error());
        } else {
        $busca_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_centro=$id_centro_selec AND id_curso=$id_curso_selec AND anio_maximo=$edad") or die(db_error());
        }

$filtro_aulas="";

while($datos_aula=db_fetch_array($busca_datos_aula)) {
        $filtro_aulas=$filtro_aulas." alumnos.aula=".$datos_aula["id_aula"]." OR ";
        }
        
if ($filtro_aulas!="") {
        $filtro_aulas=substr($filtro_aulas,0,strlen($filtro_aulas)-4);
        $filtro_aulas="(".$filtro_aulas.")";
        }
        
$consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=?;', 's', array($id_centro_selec));
if (db_num_rows($consulta_centros)!=0) {
                        $centros=db_fetch_array($consulta_centros);
                        $nombre_centro=$centros["nombre_centro"];
                        $nombre_centro_completo=$centros["nombre_completo"];
                        $codigo_centro=$centros["codigo_centro"];
                        } else {
                        $nombre_centro="";
                        $nombre_centro_completo="";
                        $codigo_centro="";
                        }

error_reporting(7);


if ($edad_anios==0) {
        $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND alumnos.sustituye_a<>0 AND (fecha_matricula<='$anio_actual-12-31') AND $filtro_aulas ORDER BY usuarios.fecha_nacimiento DESC, usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre") or die(db_error()."-2");
        } else {
        $busca_inscripcion_curso_actual=db_query("SELECT * FROM alumnos, usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.orden_en_aula<>0 AND (alumnos.sustituye_a=0) AND $filtro_aulas ORDER BY usuarios.Apellido1, usuarios.Apellido2, usuarios.Nombre") or die(db_error()."-2");
        }

$numero_de_alumnos=db_num_rows($busca_inscripcion_curso_actual);

if ($numero_de_alumnos<45) {
        $tam_fuente=2;
        } else {
        $tam_fuente=1;
        }

?>
<html>
<body>
<table width=100% border=0 cellpadding=1 cellspacing=0 style="border-collapse: collapse" bordercolor="#000000">
<tr bgcolor="#ffffff">
<td align=left><font size=2 face="Arial"><b><?=$textoencabezado[$edad_anios]?></b></font></td>
<td align=right><font size=2 face="Arial"><b>CODI/CODIGO:<?=$codigo_centro?></b></font></td>
</tr>
</table><br>
<table width=100% border=2 cellpadding=1 cellspacing=0 style="border-collapse: collapse" bordercolor="#000000">
<tr bgcolor="#ffffff">
<td><font size=2 face="Arial"><b>NOM DE L'ALUMNE/NOMBRE DEL ALUMNO</b></font></td>
<td align=center><font size=2 face="Arial"><b>SEPT.</b></font></td>
<td align=center><font size=2 face="Arial"><b>OCT.</b></font></td>
<td align=center><font size=2 face="Arial"><b>NOV.</b></font></td>
<td align=center><font size=2 face="Arial"><b>DES./DIC.</b></font></td>
<td align=center><font size=2 face="Arial"><b>VALOR</b></font></td>
<td align=center><font size=2 face="Arial"><b>QUANTITAT/CANTIDAD</b></font></td>
<td align=center><font size=2 face="Arial"><b>VALOR TOTAL</b></font></td>
</tr>
<?php 

while($datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual)) {

                        $id_alumno=$datos_inscripcion_curso_actual["id_alumno"];
                        $id_usuario=$datos_inscripcion_curso_actual["id_usuario"];
                        $orden_en_aula=$datos_inscripcion_curso_actual["orden_en_aula"];
                        $nombre_alu=$datos_inscripcion_curso_actual["Nombre"];
                        $apellido1_alu=$datos_inscripcion_curso_actual["Apellido1"];
                        $apellido2_alu=$datos_inscripcion_curso_actual["Apellido2"];
                        $sustituye_a=$datos_inscripcion_curso_actual["sustituye_a"];
                        $id_aula=$datos_inscripcion_curso_actual["aula"];

$cantidad=0;
$importe=0;

if (strlen($edad)==0) {
        $edad_anios=$anio_actual-date("Y",strtotime($datos_inscripcion_curso_actual["fecha_nacimiento"]))+1;
        }

$busca_mes1=db_query_params('SELECT * FROM detalles_factura,facturas_emitidas WHERE detalles_factura.id_factura=facturas_emitidas.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.concepto=?', 'ss', array($id_alumno, (string)($concepto[$edad_anios]) . ' Septiembre de ' . (string)($anio_actual))) or die(db_error()."-A");

if (db_num_rows($busca_mes1)!=0) {
        $datos_mes1=db_fetch_array($busca_mes1);
        $cantidad++;
        $importe=$datos_mes1["precio"]*-1;
        $mes1="X";
        } else {
        $mes1="";
        }
$busca_mes2=db_query_params('SELECT * FROM detalles_factura,facturas_emitidas WHERE detalles_factura.id_factura=facturas_emitidas.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.concepto=?', 'ss', array($id_alumno, (string)($concepto[$edad_anios]) . ' Octubre de ' . (string)($anio_actual))) or die(db_error()."-B");

if (db_num_rows($busca_mes2)!=0) {
        $datos_mes2=db_fetch_array($busca_mes2);
        $cantidad++;
        $importe=$datos_mes2["precio"]*-1;
        $mes2="X";
        } else {
        $mes2="";
        }
$busca_mes3=db_query_params('SELECT * FROM detalles_factura,facturas_emitidas WHERE detalles_factura.id_factura=facturas_emitidas.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.concepto=?', 'ss', array($id_alumno, (string)($concepto[$edad_anios]) . ' Noviembre de ' . (string)($anio_actual))) or die(db_error()."-C");

if (db_num_rows($busca_mes3)!=0) {
        $datos_mes3=db_fetch_array($busca_mes3);
        $cantidad++;
        $importe=$datos_mes3["precio"]*-1;
        $mes3="X";
        } else {
        $mes3="";
        }
$busca_mes4=db_query_params('SELECT * FROM detalles_factura,facturas_emitidas WHERE detalles_factura.id_factura=facturas_emitidas.id_factura AND facturas_emitidas.id_cliente=? AND detalles_factura.concepto=?', 'ss', array($id_alumno, (string)($concepto[$edad_anios]) . ' Diciembre de ' . (string)($anio_actual))) or die(db_error()."-D");

if (db_num_rows($busca_mes4)!=0) {
        $datos_mes4=db_fetch_array($busca_mes4);
        $cantidad++;
        $importe=$datos_mes4["precio"]*-1;
        $mes4="X";
        } else {
        $mes4="";
        }


/*
if ($id_alumno==2479) {
        $mes1="X";
        $mes2="X";
        $mes3="X";
        $mes4="X";
        $cantidad=4;
        $importe=118;
        }
*/

if ($importe<>0) {
        
?>
<tr bgcolor="#ffffff">
<td><font size=<?=$tam_fuente?> face="Arial"><?=strtoupper($apellido1_alu." ".$apellido2_alu.", ".$nombre_alu)?></font></td>
<td align=center><font size=<?=$tam_fuente?> face="Arial"><?=$mes1?></font></td>
<td align=center><font size=<?=$tam_fuente?> face="Arial"><?=$mes2?></font></td>
<td align=center><font size=<?=$tam_fuente?> face="Arial"><?=$mes3?></font></td>
<td align=center><font size=<?=$tam_fuente?> face="Arial"><?=$mes4?></font></td>
<td align=right><font size=<?=$tam_fuente?> face="Arial"><?=number_format($importe, 2, ',', '.');?></font></td>
<td align=center><font size=<?=$tam_fuente?> face="Arial"><?=$cantidad;?></font></td>
<td align=right><font size=<?=$tam_fuente?> face="Arial"><?=number_format($importe*$cantidad, 2, ',', '.');?></font></td>
</tr>
<?php 
        }
}

?>
</body>
</html>
