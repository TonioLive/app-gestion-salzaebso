<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php 
$array_listas_de_espera=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");

$edad_lista=$_GET["edad_lista"];
$curso_seleccionado=$_GET["id_curso_lista"];

// Busca el curso actual

if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=? LIMIT 0,1', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $id_curso_actual=0;
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=? LIMIT 0,1', 's', array($curso_seleccionado));
        $id_curso_actual=$curso_seleccionado;
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

$orden_lista=$_GET["o"];
if (strlen($orden_lista)==0) {
        $orden_lista="fecha_alta";
        }

if ($orden_lista=="fecha_alta") {
        $descripcion_orden="fecha de llegada";
        } else {
        $descripcion_orden="puntos";
        }


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto">
<h1>Listas de espera, curso <?=$datos_curso_actual["nombre"]?></h1>
<table border=1 cellspacing="1" cellpadding="2" width="100%" style="border-collapse: collapse" bordercolor="#000">
<tr>
<td>Lista de Espera</td>
<td align="left">Nombre</td>
<td align="left">Email</td>
<td align="left">Fecha Inscripción</td>
<td align="left">Estado</td>
</tr>
<?php 

$busca_ninios=db_query("SELECT lista_espera.*,sum(lista_espera_respuestas.valor) as puntuacion FROM lista_espera,lista_espera_respuestas WHERE lista_espera.id_usu_lista=lista_espera_respuestas.id_usu_lista AND lista_espera.id_curso=$curso_seleccionado AND lista_espera.id_centro=$centro_actual GROUP BY lista_espera_respuestas.id_usu_lista ORDER BY edad_espera,motivo_baja,$orden_lista");

$orden_lista=1;

$nombres_motivos_baja=array();
$busca_motivos_baja=db_query("SELECT * FROM lista_espera_motivos_baja");
while($motivos_baja=db_fetch_array($busca_motivos_baja)){
        $nombres_motivos_baja[$motivos_baja["id_motivo"]]=$motivos_baja["descripcion_motivo"];
        }
$nombres_motivos_baja[0]="Actualmente en lista de espera";

$motivo_actual=-1;

while($lista_ninios=db_fetch_array($busca_ninios)) {

$motivo_actual=$lista_ninios["motivo_baja"];


$busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($lista_ninios["id_usuario"]));
$datos_ninio=db_fetch_array($busca_datos_usuario);

$fecha_deseada="";
if ((strlen($lista_ninios["fecha_deseada"])!=0) and ($lista_ninios["fecha_deseada"]!="0000-00-00")) {
        $fecha_deseada=date("d/m/Y",strtotime($lista_ninios["fecha_deseada"]))."<br>";
        }

?>
<tr>
<td><?=$array_listas_de_espera[$lista_ninios["edad_espera"]];?></td>
<td align="left"><?=$datos_ninio["Nombre"]?> <?=$datos_ninio["Apellido1"]?> <?=$datos_ninio["Apellido2"]?></td>
<td align="left"><?=$datos_ninio["email"]?></td>
<td align="center"><?=date("d/m/Y",strtotime($lista_ninios["fecha_alta"]))?></td>
<td><?=$nombres_motivos_baja[$motivo_actual];?></td>
</tr>
<?php 
$orden_lista++;
}
?>
</table>
</body>
</html>
