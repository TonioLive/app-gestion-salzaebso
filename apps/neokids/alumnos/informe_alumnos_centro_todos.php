<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$centro_seleccionado_listado=$_GET["cen"];
$curso_seleccionado_listado=$_GET["cur"];

$coletilla="";


        // Busca el curso actual
        $curso_seleccionado=$_GET["cur"];
        if (strlen($curso_seleccionado)==0) {

        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $curso_seleccionado_listado=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio>=? ORDER BY fecha_inicio', 's', array((string)(date("Y-m-d"))));

                if (db_num_rows($busca_curso_actual)!=0) {
                        $datos_curso_actual=db_fetch_array($busca_curso_actual);
                        $curso_seleccionado_listado=$datos_curso_actual["id_curso"];
                        } else {
                        $curso_seleccionado_listado=0;
                        }
                }
        } else {
        $curso_seleccionado_listado=$curso_seleccionado;
        }

$busca_curso=db_query("SELECT * FROM cursos_academicos WHERE id_curso=".$curso_seleccionado_listado);
if (db_num_rows($busca_curso)!=0) {
                                $datos_curso=db_fetch_array($busca_curso);
                                $fecha_inicio_curso=$datos_curso["fecha_inicio"];
                                $fecha_fin_curso=$datos_curso["fecha_fin"];
                                }

// escribe la parte superior del archivo
$fecha_informe=date("d")."-".date("m")."-".date("Y");
$nombre_archivo = "informes/Listado_completo_alumnos_".$c2.$fecha_informe.".xls";
$Titulo_hoja="Alumnos $fecha_informe";
if (file_exists($nombre_archivo)) {
        unlink($nombre_archivo);
        }

$gestor = fopen($nombre_archivo, 'a');

fwrite($gestor, $contenido);

fwrite($gestor,"<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">");
fwrite($gestor,"<head>");
fwrite($gestor,"<!--[if gte mso 9]><xml>");
fwrite($gestor,"<x:ExcelWorkbook>");
fwrite($gestor,"<x:ExcelWorksheets>");
fwrite($gestor,"<x:ExcelWorksheet>");
fwrite($gestor,"<x:Name>".$Titulo_hoja."</x:Name>");
fwrite($gestor,"<x:WorksheetOptions>");
fwrite($gestor,"<x:Print>");
fwrite($gestor,"<x:ValidPrinterInfo/>");
fwrite($gestor,"</x:Print>");
fwrite($gestor,"</x:WorksheetOptions>");
fwrite($gestor,"</x:ExcelWorksheet>");
fwrite($gestor,"</x:ExcelWorksheets>");
fwrite($gestor,"</x:ExcelWorkbook>");
fwrite($gestor,"</xml>");
fwrite($gestor,"<![endif]--> ");
fwrite($gestor,"<style>");
fwrite($gestor,"<!--table");
fwrite($gestor,".style20        {mso-number-format:0%; mso-style-name:Porcentual; mso-style-id:5;}");
fwrite($gestor,".xl26        {mso-style-parent:style20; mso-number-format:Percent; white-space:normal;}");
fwrite($gestor,"-->");
fwrite($gestor,"</style>");
fwrite($gestor,"</head>");
fwrite($gestor,"<body>");
fwrite($gestor,"<table style='border-collapse: collapse;table-layout:fixed;width:960pt'>");
fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
fwrite($gestor,"<td width=67><i>ID Alumno</i></td>");
fwrite($gestor,"<td width=67><i>Nombre</i></td>");
fwrite($gestor,"<td width=323><i>Apellido 1</i></td>");
fwrite($gestor,"<td width=323><i>Apellido 2</i></td>");
fwrite($gestor,"<td width=120><i>Días asistidos</i></td>");
fwrite($gestor,"<td width=120><i>ültimo día asistido</i></td>");
fwrite($gestor,"<td width=150><i>Fecha Nacimiento</i></td>");
fwrite($gestor,"<td width=250><i>Domicilio</i></td>");
fwrite($gestor,"<td width=102><i>CP</i></td>");
fwrite($gestor,"<td width=121><i>Población</i></td>");
fwrite($gestor,"<td width=105><i>Provincia</i></td>");
fwrite($gestor,"<td width=105><i>Teléfonos</i></td>");
fwrite($gestor,"<td width=121><i>Nombre contacto 1</i></td>");
fwrite($gestor,"<td width=121><i>Nombre contacto 2</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiar 1</i></td>");
fwrite($gestor,"<td width=121><i>Email familiar 1</i></td>");
fwrite($gestor,"<td width=121><i>Teléfono familiar 1</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 2</i></td>");
fwrite($gestor,"<td width=121><i>Email familiar 2</i></td>");
fwrite($gestor,"<td width=121><i>Teléfono familiar 2</i></td>");
fwrite($gestor,"<td width=120><i>Fecha inicio curso actual</i></td>");
fwrite($gestor,"<td width=120><i>Fecha baja definitiva</i></td>");
fwrite($gestor,"<td width=120><i>Escuela Actual</i></td>");
fwrite($gestor,"<td width=120><i>Aula Actual</i></td>");
// Busca cursos anteriores
$busca_cursos_anteriores=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<? ORDER BY fecha_inicio;', 's', array((string)($fecha_inicio_curso)));
while($cursos_anteriores=db_fetch_array($busca_cursos_anteriores)) {
fwrite($gestor,"<td width=120><i>".$cursos_anteriores["nombre"]."</i></td>");
	}
fwrite($gestor,"</tr>");

// Filas

$busca_listado_alumnos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins, usuarios.id_usuario AS id_usu FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_centro<9 GROUP BY usuarios.id_usuario ORDER BY alumnos.id_curso DESC, usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre") or die(db_error()." SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins, usuarios.id_usuario AS id_usu FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_centro<9 GROUP BY usuarios.id_usuario ORDER BY alumnos.id_curso DESC, usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");

$nombre_padre=array();
$email_padre=array();
$telefonos_padres=array();

while($listado_alumno=db_fetch_array($busca_listado_alumnos)) {

$fecha_inicio_curso_actual="";            
$fecha_fin_curso_actual=""; 
$nombre_centro_actual="";      
$nombre_aula_actual="";

$id_usuario_alumno=$listado_alumno["id_usu"];

$busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins, usuarios.id_usuario AS id_usu FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=$curso_seleccionado_listado AND usuarios.id_usuario=$id_usuario_alumno") or die(db_error());


                // Busca provincia
                $id_provincia_sel=$listado_alumno["Provincia"];
                if (strlen($id_provincia_sel)==0) {
                        $id_provincia_sel=0;
                        }
                $busca_provincia=db_query_params('SELECT * FROM provincias WHERE id_provincia=?', 's', array($id_provincia_sel));
                if (db_num_rows($busca_provincia)!=0) {
                        $datos_provincia=db_fetch_array($busca_provincia);
                        $nombre_provincia=$datos_provincia["provincia"];
                        } else {
                        $nombre_provincia="";
                        }

                // Busca nombre del padre
                $nombre_padre[0]="";
                $email_padre[0]="";
                $nombre_padre[1]="";
                $email_padre[1]="";
                $nombre_padre[2]="";
                $email_padre[2]="";
                $nombre_padre[3]="";
                $email_padre[3]="";
                $telefonos_padres[0]="";
                $telefonos_padres[1]="";
                $telefonos_padres[2]="";
                $telefonos_padres[3]="";
                
                $consulta_relaciones=db_query_params('SELECT * FROM padres WHERE padre_de=? ORDER BY parentesco', 's', array($id_usuario_alumno)) or die(db_error());

                $contador_padres=0;

                while($relaciones=db_fetch_array($consulta_relaciones)) {
                        $consulta_datos_padre=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($relaciones["id_usuario"]));

                        $nombre_padre_act="";
                        if (db_num_rows($consulta_datos_padre)!=0) {
                                        $datos_padre=db_fetch_array($consulta_datos_padre);
                                        $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                                        $email_padre_act=$datos_padre["email"];
                                        $telefono_padre_act=$datos_padre["telefono1"]." ".$datos_padre["telefono2"];
                                        }
                        $nombre_padre[$contador_padres]=$nombre_padre_act;
                        $email_padre[$contador_padres]=$email_padre_act;
                        $telefonos_padres[$contador_padres]=$telefono_padre_act;
                        $contador_padres++;
                        }

                $telefono_contacto1=$listado_alumno["telefono_contacto1"];
                $telefono_contacto2=$listado_alumno["telefono_contacto2"];
                $telefonos=$telefono_contacto1." ".$telefono_contacto2;

$fecha_inicio_curso_actual="";

if (db_num_rows($busca_alumnos_inscritos)==0) {

	// Busca fecha de baja defnitiva
	$fecha_fin_curso_actual=date("d/m/Y",strtotime($listado_alumno["fecha_baja"]));

	} else {

$alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos);

                $consulta_nombre_centro=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($alumnos_inscritos["id_centro_ins"])) or die(db_error()."-2. SELECT * FROM centros WHERE id_centro=".$alumnos_inscritos["id_centro_ins"]);
                $datos_centro=db_fetch_array($consulta_nombre_centro);

                // Busca datos del aula
                $consulta_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($alumnos_inscritos["aula"]));
                $datos_aula=db_fetch_array($consulta_datos_aula);
                
                $busca_dias_asistidos=db_query_params('SELECT fecha FROM servicios_consumidos WHERE fecha>=? AND fecha<=? AND id_alumno=? GROUP BY fecha ORDER BY fecha DESC;', 'sss', array((string)($fecha_inicio_curso), (string)($fecha_fin_curso), $alumnos_inscritos["id_usu"])) or die(db_error());
                $num_dias_asistidos=db_num_rows($busca_dias_asistidos);
                if ($num_dias_asistidos!=0) {
                        $datos_dias_asistidos=db_fetch_array($busca_dias_asistidos);
                        $ultimo_dia_asistido=date("d/m/Y",strtotime($datos_dias_asistidos["fecha"]));
                        } else {
                        $ultimo_dia_asistido="-";
                        }
                        
           $fecha_inicio_curso_actual=date("d/m/Y",strtotime($alumnos_inscritos["fecha_matricula"]));            
           $fecha_fin_curso_actual=date("d/m/Y",strtotime($alumnos_inscritos["fecha_baja"])); 
           $nombre_centro_actual=$datos_centro["nombre_centro"];      
           $nombre_aula_actual=$datos_aula["nombre"];   
}               
                // Escribe fila
                 fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
                 fwrite($gestor,"<td>".$listado_alumno["id_alumno"]."</td>");
                 fwrite($gestor,"<td>".$listado_alumno["Nombre"]."</td>");
                 fwrite($gestor,"<td>".$listado_alumno["Apellido1"]."</td>");
                 fwrite($gestor,"<td>".$listado_alumno["Apellido2"]."</td>");
                 fwrite($gestor,"<td>".$num_dias_asistidos."</td>");
                 fwrite($gestor,"<td>".$ultimo_dia_asistido."</td>");
                 fwrite($gestor,"<td>".date("d/m/Y",strtotime($listado_alumno["fecha_nacimiento"]))."</td>");
                 fwrite($gestor,"<td>".$listado_alumno["Direccion"]."</td>");
                 fwrite($gestor,"<td>".$listado_alumno["cp"]."</td>");
                 fwrite($gestor,"<td>".$listado_alumno["Poblacion"]."</td>");
                 fwrite($gestor,"<td>".$nombre_provincia."</td>");
                 fwrite($gestor,"<td>".$telefonos."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["contacto1"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["contacto2"]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[0]."</td>");
                 fwrite($gestor,"<td>".$email_padre[0]."</td>");
                 fwrite($gestor,"<td>".$telefonos_padres[0]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[1]."</td>");
                 fwrite($gestor,"<td>".$email_padre[1]."</td>");
                 fwrite($gestor,"<td>".$telefonos_padres[1]."</td>");
                 fwrite($gestor,"<td>".$fecha_inicio_curso_actual."</td>");
                 fwrite($gestor,"<td>".$fecha_fin_curso_actual."</td>");
                 fwrite($gestor,"<td>".$nombre_centro_actual."</td>");
                 fwrite($gestor,"<td>".$nombre_aula_actual."</td>");
// Busca cursos anteriores
$busca_cursos_anteriores=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<? ORDER BY fecha_inicio;', 's', array((string)($fecha_inicio_curso)));
while($cursos_anteriores=db_fetch_array($busca_cursos_anteriores)) {
	$estado_curso_sel="";
	
	$buca_alumno_curso_act=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?', 'ss', array($id_usuario_alumno, $cursos_anteriores["id_curso"]));
	
	if (db_num_rows($buca_alumno_curso_act)!=0) {
		$estado_curso_sel="Si";
		}
	
	fwrite($gestor,"<td>".$estado_curso_sel."</td>");
	}
                 fwrite($gestor,"</tr>");

}
// Parte inferior del archivo

fwrite($gestor,"</table>");
fwrite($gestor,"</body>");
fwrite($gestor,"</html>");

header("Location: ".$nombre_archivo);

 odbc_close($odbc);
?>
