<?php 
require("../../configuracion/config.php");
// require("../../configuracion/autentifica_padre.php");
require("../../configuracion/autentifica.php");

function conversor_segundos($seg_ini,$formato) {

        $horas = floor($seg_ini/3600);
         $minutos = floor(($seg_ini-($horas*3600))/60);
         $segundos = $seg_ini-($horas*3600)-($minutos*60);
         $minus="00".$minutos;         
         
         if ($formato==0) {                
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m:'.'s';
                 } else {        
                 $resultado=$horas.'h:'.substr($minus,strlen($minus)-2,2).'m';
                 }
         
         return $resultado;

        }


$dia=$_GET["dia"];
$a=$_GET["a"];

$entrada_ma=false;

$tipos_notas_inci=array();

$busca_notas_inci=db_query("SELECT * FROM agenda_alumnos_tipos_notas");
while($notas_inci=db_fetch_array($busca_notas_inci)) {
        $tipos_notas_inci[$notas_inci["id_tipo"]]=$notas_inci["nombre_tipo"];
        }

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";

$cantidades_comida=array("Todo","Mitad","Poco","Nada");
$resultados_comida=array("Bien","Regular","Mal");

if (strlen($dia)==0) {
        $dia=time();
        // $dia=strtotime("2011-06-01");
        }

$fecha_time_stamp=$dia;

$nombres_areas[0]="Comida";
$nombres_areas[1]="Salud";
$nombres_areas[2]="Cambios";
$nombres_areas[3]="Anotaciones";
$nombres_areas[4]="Actividades";


$curso=$_GET["curso"];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title><?php  echo $titulo_web; ?></title>
<link href='https://fonts.googleapis.com/css?family=Ovo&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
<link href='/css/agenda_imprimir.css' rel='stylesheet' type='text/css'>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function salir() {
        window.close();
        }
window.print();
//-->
</SCRIPT>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<?php 
$contador_c=0;

$multiple=$_GET["multiple"];

if ($multiple==1) {

	// Datos
	$array_alumnos=$_GET["lista_alumnos"];

	if (is_numeric(strpos($array_alumnos,","))) {
        $alumnos=explode(",",$array_alumnos);
        $filtro_alumnos="";

        for ($contador_alumnos=0;$contador_alumnos<count($alumnos);$contador_alumnos++) {
                $filtro_alumnos=$filtro_alumnos." OR id_usuario=".$alumnos[$contador_alumnos];
                }

        $filtro_alumnos=" (".substr($filtro_alumnos,4,strlen($filtro_alumnos)-4).")";
        } else {
        $alumnos=array();
        $alumnos[0]=$array_alumnos;

        $filtro_alumnos=" (id_usuario=".$array_alumnos.")";

        }

	$consulta_sql="SELECT * FROM usuarios WHERE $filtro_alumnos";
	} else {
	$consulta_sql="SELECT * FROM usuarios WHERE id_usuario=$a";	
	}


$busca_lista_alumnos=db_query($consulta_sql);

$num_c=db_num_rows($busca_lista_alumnos);

$contador_c=0;

while($datos_lista_alumnos=db_fetch_array($busca_lista_alumnos)) {

$a=$datos_lista_alumnos["id_usuario"];

// Comprueba si el alumno es hijo de su padre

$busca_usuario=db_query("SELECT * FROM padres WHERE usuario_webcam='$nombre_usuario' AND padre_de=$a");
// if (mysql_num_rows($busca_usuario)==0) {
if (0!=0) {
        echo("No tiene permiso para consultar esta información.");
        die();
        } else {
        $datos_hijo=db_fetch_array($busca_usuario);
        // Busca información del niño
        
        // Inscripcion actual del niño
        $busca_datos_alumno=db_query("SELECT * FROM usuarios, alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=$a AND alumnos.fecha_matricula<='".date("Y-m-d",$dia)."' AND alumnos.fecha_baja>='".date("Y-m-d",$dia)."';") or die(db_error()."- No se encuentran los datos del alumno.");
        
        if (db_num_rows($busca_datos_alumno)!=0) {
                $datos_alumno=db_fetch_array($busca_datos_alumno);
        
                        $id_usuario_alumno=$datos_alumno["id_usuario"];
                        $nombre_completo_alumno=$datos_alumno["Nombre"]." ".$datos_alumno["Apellido1"]." ".$datos_alumno["Apellido2"];
                $id_alumno=$datos_alumno["id_alumno"];
                $id_aula=$datos_alumno["aula"];
                $id_curso=$datos_alumno["id_curso"];
                $id_centro=$datos_alumno["id_centro"];
                        $grupo_alumno=$datos_alumno["agenda_grupo"];
                        
                $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas,centros WHERE centro_aulas.id_centro=centros.id_centro AND id_aula=?', 's', array($id_aula));

                if (db_num_rows($consulta_datos_aula)!=0)
                        {
                        $datos_aula=db_fetch_array($consulta_datos_aula);

                        // Datos Fijos
                        $aula_nombre=$datos_aula["nombre"];
                        $centro_nombre=$datos_aula["nombre_centro"];

                                                $edad_aula=$datos_aula["edad_minima_prog"];

                        $hora_entrada_centro_man=$datos_aula["hora_entrada1"];
                        $hora_salida_centro_man=$datos_aula["hora_salida1"];
                        $hora_entrada_centro_tar=$datos_aula["hora_entrada2"];
                        $hora_salida_centro_tar=$datos_aula["hora_salida2"];
                        $division_horas=($datos_aula["division_horas"]/60);
                        $periodo_gracia=($datos_aula["periodo_gracia"]*60);
                        
                        }
        
                } else {
                echo("El alumno no está matriculado hoy");
                die();
                }
        
        }

?>
<div class="page">
<?php  include("include_informe_dia_imprimir.php"); ?>
</div>
<?php 
$contador_c++;
if ($contador_c<$num_c) {
?>
<div align="center" style="page-break-after: always;"></div>
<?php 
		}
}
?>
<SCRIPT LANGUAGE=javascript>
<!--
window.print();
//-->
</SCRIPT>
</body>
</html>
