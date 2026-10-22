<?php 
require("../configuracion/config.php");

$id_cliente=$_POST["id_cliente"];
$mes=$_POST["mes"];
$ejercicio=$_POST["ejercicio"];
$centro_factura=$_POST["centro_factura"];

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

$db=db_connect((getenv('LAPICES_DB_HOST_01') ?: ''),(getenv('LAPICES_DB_USER_01') ?: ''),"$sql_pass");
db_select_db("$sql_db",$db);

$consulta1="SELECT facturas_emitidas.*, detalles_factura.*, alumnos.*, usuarios.*, alumnos.id_centro AS centro_factura FROM facturas_emitidas, detalles_factura,alumnos, usuarios WHERE facturas_emitidas.id_factura=detalles_factura.id_factura AND facturas_emitidas.id_cliente=alumnos.id_alumno AND alumnos.id_usuario=usuarios.id_usuario AND detalles_factura.subvencion=1 AND $filtros ORDER BY alumnos.id_centro, alumnos.aula, detalles_factura.concepto, fecha_emision DESC";
$query=db_query($consulta1);

$contador=1;

while($rsslc=db_fetch_array($query))
        {
        $matriz_de_codigos[$contador]=$rsslc["id_detalle"];
        $contador=$contador+1;
        }

for ($x=1; $x<=$contador-1; $x=$x+1)
{
  $act=$_POST["PAG".$matriz_de_codigos[$x]];
  if ($act=="SI")
  {
    $act1="1";
  }
    else
  {
    $act1="0";
  } 
  $query2=db_query("UPDATE detalles_factura SET subvencion_cobrada=".$act1." WHERE id_detalle=".$matriz_de_codigos[$x]);

} 

db_close();

mt_srand((double)microtime()*1000000);
header("Location: listado_subvenciones.php?id_cliente=$id_cliente&mes=$mes&ejercicio=$ejercicio&centro_factura=$centro_factura&r=".(mt_rand(0,10000000)/10000000));
?>

