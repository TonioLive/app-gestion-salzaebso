<?php 
require("../../configuracion/config.php");
// require("../../configuracion/autentifica_padre.php");
require("../../configuracion/autentifica.php");

$docente=true;

$dia=$_GET["dia"];
$a=$_GET["a"];

		// Guarda registro
		$inserta_resgitro=db_query("INSERT INTO agenda_registro_lecturas (dia_informe,dia, hora, id_alumno, id_padre, id_usuario, REMOTE_ADDR, HTTP_ACCEPT_LANGUAGE, HTTP_User_Agent, sesion) VALUES ('".date("Y-m-d",$dia)."', '".date("Y-m-d")."', '".date("H:i:s")."', $a, 0, $idUsuario, '$REMOTE_ADDR', '$HTTP_ACCEPT_LANGUAGE', '$HTTP_USER_AGENT', '".session_id()."');");
        

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
                
                // Busca trimestre actual
        	$grupo_alumno=$datos_alumno["agenda_grupo"];
                $datosCurso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso");
                if (db_num_rows($datosCurso)!=0) {
                	$datosCurso=db_fetch_array($datosCurso);
                	// Tri 1
                	if (date("Y-m-d",$dia)<=$datosCurso["fin_trim1_1"]) {
                                $grupo_alumno=$datos_alumno["agenda_grupo"]; 
                	}   
                	// Tri 2
                	if (date("Y-m-d",$dia)>$datosCurso["fin_trim1_1"]) {
                                $grupo_alumno=$datos_alumno["agenda_2_grupo"];               		
                	}  
                	// Tri 3
                	if (date("Y-m-d",$dia)>$datosCurso["fin_trim2_1"]) {
                                $grupo_alumno=$datos_alumno["agenda_3_grupo"];               		
                	}                    	              	             
                }                
                
                $id_centro=$datos_alumno["id_centro"];
                $nombre_de_pila_alumno=$datos_alumno["Nombre"];
                $anio_nacimiento_alumno=date("Y",strtotime($datos_alumno["fecha_nacimiento"]));
                        		        
                $consulta_datos_aula=db_query("SELECT * FROM centro_aulas,centros WHERE centro_aulas.id_centro=centros.id_centro AND id_aula=".$id_aula);

                if (db_num_rows($consulta_datos_aula)!=0)
                        {
                        $datos_aula=db_fetch_array($consulta_datos_aula);

                        // Datos Fijos
                        $aula_nombre=$datos_aula["nombre"];
                        $centro_nombre=$datos_aula["nombre_centro"];

                        $edad_aula=$datos_aula["edad_minima_prog"];
						$edad_aula_fin=$datos_aula["edad_maxima_prog"];
                        
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php  echo $titulo_web; ?></title>
<link href='https://fonts.googleapis.com/css?family=Ovo&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
<link href='/css/agenda.css' rel='stylesheet' type='text/css'>
<SCRIPT LANGUAGE=javascript>
<!--
self.focus();

function salir() {
        window.close();
        }

//-->
</SCRIPT>
</head>

<body bgcolor="<?php  echo $fondo_web; ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="auto">
<div class="page">
<?php  include("include_informe_dia.php"); ?>
</div>
</body>
</html>
