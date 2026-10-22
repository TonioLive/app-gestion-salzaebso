<?php  require("../../configuracion/parte_superior_sin_login_ancho_bootstrap.php"); ?>
<?php 
$id_aula=$_GET["id_aula"];

// Busca datos del aula
$consulta_datos_aula=db_query("SELECT *, centro_aulas.nombre AS nombre_aula, cursos_academicos.nombre AS nombre_curso FROM centro_aulas, cursos_academicos WHERE centro_aulas.id_curso=cursos_academicos.id_curso AND centro_aulas.id_aula=$id_aula");
$datos_aula=db_fetch_array($consulta_datos_aula);

$curso_del_aula=$datos_aula["id_curso"];

$fecha_de_inicio_curso=$datos_aula["fecha_inicio"];

if (intval(strtotime($fecha_de_inicio_curso))>intval(time())) {
        $curso_iniciado=false;
        } else {
        $curso_iniciado=true;
        }

?>
<div class="text-center">
	<h3>Alumnos Aula <?=$datos_aula["nombre_aula"];?> - Curso <?=$datos_aula["nombre_curso"];?> - Escuela "<?=$nombre_centro_actual;?>"</h3>
<?php 
$busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_centro=$centro_actual AND aula=$id_aula ORDER BY Apellido1, Apellido2, Nombre");

$columnas=4;


if (db_num_rows($busca_alumnos)!=0) {

$numero_alumnos=db_num_rows($busca_alumnos);

if ($numero_alumnos<=25) {
        $altura=0;
        }

?>
<tr bgcolor="#FFFFFF">
        <td align="center">
        <table width="100%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
        <?php 
        $columna_actual=0;
        while($listado_alumnos=db_fetch_array($busca_alumnos)){

        if ($columna_actual==0) {
                ?>
                <tr>
                <?php 
                }
        
        
        $edad_alumno=date("Y")-date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));
        if ((date("m")<=date("m",strtotime($listado_alumnos["fecha_nacimiento"]))) and ((date("d")<date("d",strtotime($listado_alumnos["fecha_nacimiento"]))))) {
                $edad_alumno=$edad_alumno-1;
                }
        if ($edad_alumno<2) {
                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$listado_alumnos["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad_alumno=$edad_d_l." meses";
                } else {
                $edad_alumno=$edad_alumno." años";
                }
                                
// Nombre foto
$nombre_foto="foto_usu".$listado_alumnos["id_usuario"].".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }
 
$sip=$listado_alumnos["SIP"];
      
// Busca autorizados 
$lista_autorizados="";     
$consulta_relaciones=db_query("SELECT * FROM padres WHERE padre_de=".$listado_alumnos["id_usuario"]) or die(db_error());
while($relacion=db_fetch_array($consulta_relaciones)) {
 	// Busca datos padres
    $consulta_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=".$relacion["id_usuario"]);
	if (db_num_rows($consulta_datos_padre)!=0) {
    	$datos_padre=db_fetch_array($consulta_datos_padre);
    	$lista_autorizados.="<br />".$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"]." DNI:".$datos_padre["DNI"];
    }
	
}
      
        ?>
        <td align="center"><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a href="javascript:editar(<?=$listado_alumnos["id_usuario"];?>)" title="Ver ficha del alumno"><img src="/fotos_usuarios/<?=$nombre_foto;?>"  width=100 border=1 bordercolor="#00000"></a><br><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?><br><?=date("d/m/Y",strtotime($listado_alumnos["fecha_nacimiento"]))?> - <?=$edad_alumno?><br />Nº SIP: <?=$sip?><?=$lista_autorizados?></font></td>
        <?php 
        $columna_actual++;
        if ($columna_actual==$columnas) {
                ?>
                </tr>
                <?php 
                $columna_actual=0;
                }
        }

        ?>
        </table>
<?php 
} else {
?>
	<h3>Todavia no se ha inscrito ningún alumno en este aula.</h3>
<?php 

}
?>
</div>
<script>

function editar(codigo) {
        window.open('/alumnos/ficha_alumno.php?u='+codigo,'editar','toolbar=no,resizable=no,width=600,height=500');
        }
        
</script>
<?php  require("../../configuracion/parte_inferior.php"); ?>

