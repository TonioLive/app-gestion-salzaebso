<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
?>
<!doctype html>
<html lang="en">
<head>

<meta charset="WINDOWS-1258">
<title>Listado faltas recuperables</title>
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
<style>
.fondo_nivel0 {
        background: #fff;
        }
.fondo_nivel1 {
        background: #9ed09c;
        }
.fondo_nivel2 {
        background: #9cbdd0;
        }
.fondo_nivel3 {
        background: #d0af9c;
        } 
.fondo_nivel4 {
        background: #f9a63b;
        } 
.fondo_nivel5 {
        background: #ffa9f0;
        }

.modal.modal-wide .modal-dialog {
  width: 75%;
}
.modal-wide .modal-body {
  overflow-y: auto;
}
         
</style>    
</head>
<body>
<div class="container-fluid">
<?php 

$total_nuevos_alumnos=0;

$array_nombres_niveles=array("Bebes de 3 a 24 meses","De 2 a 3 años (Verde)","De 3 a 5 años (Azul)","De 5 a 10 años (Rojo)","Aquagym","Matronatación");

$colores_listas_de_espera=array("#fff","#9ed09c","#9cbdd0","#d0af9c","#f9a63b","#ffa9f0");

for ($nivel_actual=0;$nivel_actual<6;$nivel_actual++) {

$busca_alumnos=db_query("SELECT * FROM usuarios, alumnos, servicios_consumidos_faltas WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_alumno=servicios_consumidos_faltas.id_alumno AND alumnos.nivel_actual=$nivel_actual AND alumnos.fecha_baja>='".date("Y-m-d")."' AND servicios_consumidos_faltas.estado=0 AND servicios_consumidos_faltas.id_grupo=0 AND servicios_consumidos_faltas.id_subgrupo=0 ORDER BY usuarios.Nombre, usuarios.Apellido1, usuarios.Apellido2") or die(db_error());

$total_nuevos_alumnos_nivel=0;
$cuerpo_tabla_nivel="";

while($alumnos=db_fetch_array($busca_alumnos)) {

                // Fecha de incorporación
                $fecha_incorporacion="";
                if (strtotime($alumnos["fecha_matricula"])>strtotime(date("Y-m-d"))) {
                        $fecha_incorporacion="<font color='#ff0000'>".date("d/m/Y",strtotime($alumnos["fecha_matricula"]))."</font> - ";
                        $contador_ocupacion_prox=$contador_ocupacion_prox+$alumnos["num_dias_asiste"];
                        }

// Comprueba si está asignado

$busca_grupo_alumno=db_query_params('SELECT * FROM aulas_alumnos_grupos WHERE id_alumno=? AND id_curso=?', 'ss', array($alumnos["id_alumno"], $id_curso_actual));

// Comprueba servicio contratado
$nivel_alumno=$nivel_actual;

if ($nivel_alumno==0) {

        // Busca servicios contratador
        $busca_servicios_ag=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? AND tarifa.tipo_neokids=3 ORDER BY servicios_contratados.fecha_inicio', 's', array($alumnos["id_alumno"]));
        
        if (db_num_rows($busca_servicios_ag)!=0) {
                $nivel_alumno=4;
                }

        $busca_servicios_mn=db_query_params('SELECT * FROM servicios_contratados, tarifa, tipos_iva_servicios WHERE servicios_contratados.id_servicio=tarifa.ID AND tarifa.CATEGORIA=tipos_iva_servicios.id_tipo_iva AND servicios_contratados.id_alumno=? AND tarifa.tipo_neokids=4 ORDER BY servicios_contratados.fecha_inicio', 's', array($alumnos["id_alumno"]));
        
        if (db_num_rows($busca_servicios_mn)!=0) {
                $nivel_alumno=5;
                }
        
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

        $total_nuevos_alumnos_nivel++;
        $total_nuevos_alumnos++;
        
        $color_fuente_linea="";
        $coletilla_fecha="";        
        
        $cuerpo_tabla_nivel.='<tr' . $color_fuente_linea . ' >'
                . '<td>' . $alumnos["Nombre"] . ' ' . $alumnos["Apellido1"] . ' ' . $alumnos["Apellido2"] . '</td>'
                . '<td>' . date("d/m/Y",strtotime($alumnos["fecha"])) . '</td>'
                . '<td>' . $edad_alumno . '</td>'                        
                . '<td>' . date("d/m/Y",strtotime($alumnos["fecha_matricula"])) . $coletilla_fecha . '</td>'
                . '<td>' . date("d/m/Y",strtotime($alumnos["fecha_baja"])) . '</td>'                                       
                . '</tr>';

        }

if ($total_nuevos_alumnos_nivel!=0) {



?>
<table class="table table-condensed table-striped table-hover">
        <tr>
                <td colspan="5"><div class="fondo_nivel<?=$nivel_actual?>"><b><?=$array_nombres_niveles[$nivel_actual]?></b></div></td>
        </tr>
        <tr>
                <td class="col-md-4"><b>Nombre</b></td>
                <td class="col-md-2"><b>Fecha falta</b></td>
                <td class="col-md-1"><b>Edad</b></td>                        
                <td class="col-md-2"><b>Fecha Inicio</b></td>
                <td class="col-md-2"><b>Fecha Baja</b></td>                               
        </tr>
        <?=$cuerpo_tabla_nivel;?>
</table>        
<?php 

}


}        

if ($total_nuevos_alumnos==0) {
?>
<h4>No hay faltas pendientes de recuperar.</h4>
<?php 
}
?>                          
</div>

</body>
</html>