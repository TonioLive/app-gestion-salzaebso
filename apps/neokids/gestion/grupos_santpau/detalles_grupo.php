<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");

$id_grupo=$_GET["id_grupo"];
$id_subgrupo=$_GET["id_subgrupo"];
$fecha_limite=$_GET["fecha_limite"];

if (strlen($id_subgrupo)!=0) {

        $busca_datos_subgrupo=db_query_params('SELECT * FROM aulas_grupos,aulas_grupos_subgrupos WHERE aulas_grupos.id_grupo=aulas_grupos_subgrupos.id_grupo AND aulas_grupos_subgrupos.id_grupo=? AND aulas_grupos_subgrupos.id_subgrupo=?;', 'ss', array($id_grupo, $id_subgrupo));

        $datos_grupo=db_fetch_array($busca_datos_subgrupo);

        $id_curso_actual=$datos_grupo["id_curso"];
        $c_dia=$datos_grupo["dia"];
        
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        $nombre_curso_actual=$datos_curso_actual["nombre"];
        $permitir_tiempo_gracia=$datos_curso_actual["permitir_tiempo_gracia"];
        
}

$tiempo_gracia=0;
if ($permitir_tiempo_gracia==0) {
        $tiempo_gracia=(15*86400);
        }
$fecha_actual=strtotime($fecha_limite)+$tiempo_gracia;

?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Detalles Grupo</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">

<script>

function quitar_alumno(id_alumno) {

        $('#boton_alumno' + id_alumno).attr('disabled',true);
        
        // top.location.href = "desasigna_alumno.php?origen=<?=$_SERVER['HTTP_REFERER'];?>&fecha=<?=$fecha?>&id_alumno=" + id_alumno + "&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>";

        url = "desasigna_alumno_ajax.php?origen=<?=$_SERVER['HTTP_REFERER'];?>&fecha=<?=$fecha?>&id_alumno=" + id_alumno + "&id_grupo=<?=$id_grupo?>&id_subgrupo=<?=$id_subgrupo?>";

        $.ajax({
                url: url
                });

}

function salir() {

        top.location.href='<?=$_SERVER['HTTP_REFERER'];?>';

}

</script> 
</head>
<body>
<div class="container-fluid">
<?php 
// Busca alumnos grupo
if (strlen($id_subgrupo)!=0) {
?>
<table class="table table-condensed table-striped">
        <tr>
                <td><b>Nombre</b></td>
                <td><b>Edad</b></td>                        
                <td><b>Fecha Inicio</b></td>
                <td><b>Fecha Baja</b></td>
                <td><b>Teléfono</b></td>
                <td><b>Email</b></td>        
                <td>&nbsp;</td>                        
        </tr>
<?php 
        // Busca alumnos subgrupo
        
        $busca_alumnos_grupo=db_query_params('SELECT * FROM escuelamunicipal.usuarios,aulas_alumnos_santpau_grupos,escuelamunicipal.alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND aulas_alumnos_santpau_grupos.id_alumno=alumnos.id_alumno AND aulas_alumnos_santpau_grupos.id_subgrupo=? AND aulas_alumnos_santpau_grupos.id_grupo=? AND alumnos.fecha_baja>=?', 'sss', array($id_subgrupo, $id_grupo, (string)($fecha_limite))) or die(db_error());
        
        while($alumnos=db_fetch_array($busca_alumnos_grupo)) {
        
// Fecha de incorporación
                $fecha_incorporacion=date("d/m/Y",strtotime($alumnos["fecha_matricula"]));
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                        $fecha_incorporacion="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</span>";
                        }
                $fecha_baja=date("d/m/Y",strtotime($alumnos["fecha_baja"]));
                if (strtotime($alumnos["fecha_baja"])<strtotime(date("Y-m-d"))) {
                        $fecha_baja="<span class='text-danger'>".date("d/m/Y",strtotime($alumnos["fecha_baja"]))."</span>";
                        }
                if ($alumnos["nonato"]==1) {
                $edad_alumno="";
                $fecha_nac_alumno="No ha nacido";
        } else {
        $edad_alumno=date("Y")-date("Y",strtotime($alumnos["fecha_nacimiento"]));       
        
        if (time()<=strtotime( date("Y")."-".date("m-d",strtotime($alumnos["fecha_nacimiento"])))) {
                $edad_alumno=$edad_alumno-1;
                }
                
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                                if ($dif_meses==0) {
                                        $edad_d_l="";
                                        } else {
                                        $unidad_meses="meses";
                                        if ($dif_meses==1) {
                                                $unidad_meses="mes";
                                                }
                                        if ($dif_meses<0) {
                                                $dif_meses=12+$dif_meses;
                                                $edad_a_l=$edad_a_l-1;
                                        }
                                        $edad_d_l=" y ".$dif_meses." ".$unidad_meses;
                                        }                                
                $edad_alumno=$edad_a_l." años".$edad_d_l;
                }
                $fecha_nac_alumno=date("d/m/Y",strtotime($alumnos["fecha_nacimiento"]));
        } 
        
        $color_fuente_linea="";
    if ($alumnos["fecha_baja"]<date("Y-m-d",$fecha_actual)) {
                $color_fuente_linea=" class='text-danger'";
                }
                                                         
        ?>
        <tr<?=$color_fuente_linea;?> >
                <td><?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?></td>
                <td><?=$edad_alumno;?></td>                        
                <td><?=$fecha_incorporacion;?></td>
                <td><?=$fecha_baja;?></td>
                <td><?=$alumnos["telefono1"]." ".$alumnos["telefono2"]." ".$alumnos["movil"];?></td>
                <td><?=$alumnos["email"];?></td>        
                <td><button class="btn btn-danger btn-xs" id="boton_alumno<?=$alumnos["id_alumno"]?>" onclick="if (confirm('¿Deseas quitar a <?=$alumnos["Nombre"]?> <?=$alumnos["Apellido1"]?> <?=$alumnos["Apellido2"];?> del grupo?')) { quitar_alumno(<?=$alumnos["id_alumno"]?>); }" title="Quitar alumno del grupo"> <i class="fa fa-user-times"></i></button></td>                                
        </tr>
        <?php 
        }
?>
</table>        
<?php 
}
?>
</div>
</body>
</html>
