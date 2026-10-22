<?php 
require("../configuracion/config.php");
require("../configuracion/autentifica.php");

$coletilla=" nuevos";



// escribe la parte superior del archivo
$fecha_informe=date("d")."-".date("m")."-".date("Y");
$nombre_archivo = "informes/Informe_alumnos_".$c2.$fecha_informe.".xls";
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
fwrite($gestor,"<td width=77><i>Domicilio</i></td>");
fwrite($gestor,"<td width=102><i>CP</i></td>");
fwrite($gestor,"<td width=121><i>Población</i></td>");
fwrite($gestor,"<td width=105><i>Provincia</i></td>");
fwrite($gestor,"<td width=121><i>Nombre contacto 1</i></td>");
fwrite($gestor,"<td width=121><i>Nombre contacto 2</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 1</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 2</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 3</i></td>");
fwrite($gestor,"<td width=121><i>Nombre familiares 4</i></td>");
fwrite($gestor,"<td width=120><i>Fecha inicio curso 2006-2007</i></td>");
fwrite($gestor,"<td width=120><i>Fecha baja curso 2006-2007</i></td>");
fwrite($gestor,"<td width=120><i>Escuela</i></td>");
fwrite($gestor,"<td width=120><i>Aula</i></td>");
fwrite($gestor,"</tr>");

// Filas

$busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=4 ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");
$nombre_padre=array();

while($alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos)) {

        // Comprueba si es nuevo
        $busca_inscripcion_curso_anterior=db_query("SELECT * FROM alumnos WHERE id_curso<>4 AND fecha_matricula<'2006-06-01' AND id_usuario=".$alumnos_inscritos["id_usuario"]);

        if (db_num_rows($busca_inscripcion_curso_anterior)==0) {

                // Busca provincia
                $id_provincia_sel=$alumnos_inscritos["Provincia"];
                if (strlen($id_provincia_sel)==0) {
                        $id_provincia_sel=0;
                        }
                $busca_provincia=db_query("SELECT * FROM provincias WHERE id_provincia=".$id_provincia_sel);
                if (db_num_rows($busca_provincia)!=0) {
                        $datos_provincia=db_fetch_array($busca_provincia);
                        $nombre_provincia=$datos_provincia["provincia"];
                        } else {
                        $nombre_provincia="";
                        }

                // Busca nombre del padre
                $nombre_padre[0]="";
                $nombre_padre[1]="";
                $nombre_padre[2]="";
                $nombre_padre[3]="";

                $consulta_relaciones=db_query("SELECT * FROM padres WHERE padre_de=".$alumnos_inscritos["id_usuario"]." ORDER BY parentesco") or die(db_error());

                $contador_padres=0;
                while($relaciones=db_fetch_array($consulta_relaciones)) {
                        $consulta_datos_padre=db_query("SELECT * FROM usuarios WHERE id_usuario=".$relaciones["id_usuario"]);

                        $nombre_padre_act="";
                        if (db_num_rows($consulta_datos_padre)!=0) {
                                        $datos_padre=db_fetch_array($consulta_datos_padre);
                                        $nombre_padre_act=$datos_padre["Nombre"]." ".$datos_padre["Apellido1"]." ".$datos_padre["Apellido2"];
                                        }
                        $nombre_padre[$contador_padres]=$nombre_padre_act;
                        $contador_padres++;
                        }

                $consulta_nombre_centro=db_query("SELECT * FROM centros WHERE id_centro=".$alumnos_inscritos["id_centro_ins"]) or die(db_error()."-2");
                $datos_centro=db_fetch_array($consulta_nombre_centro);

                // Busca datos del aula
                $consulta_datos_aula=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$alumnos_inscritos["aula"]);
                $datos_aula=db_fetch_array($consulta_datos_aula);

                // Escribe fila
                 fwrite($gestor,"<tr height=17 style='height:12.75pt'>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["id_alumno"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Nombre"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Apellido1"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Apellido2"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Direccion"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["cp"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["Poblacion"]."</td>");
                 fwrite($gestor,"<td>".$nombre_provincia."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["contacto1"]."</td>");
                 fwrite($gestor,"<td>".$alumnos_inscritos["contacto2"]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[0]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[1]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[2]."</td>");
                 fwrite($gestor,"<td>".$nombre_padre[3]."</td>");
                 fwrite($gestor,"<td>".date("d/m/Y",strtotime($alumnos_inscritos["fecha_matricula"]))."</td>");
                 fwrite($gestor,"<td>".date("d/m/Y",strtotime($alumnos_inscritos["fecha_baja"]))."</td>");
                 fwrite($gestor,"<td>".$datos_centro["nombre_centro"]."</td>");
                 fwrite($gestor,"<td>".$datos_aula["nombre"]."</td>");
                 fwrite($gestor,"</tr>");

         }

}
// Parte inferior del archivo

fwrite($gestor,"</table>");
fwrite($gestor,"</body>");
fwrite($gestor,"</html>");

header("Location: ".$nombre_archivo);

 odbc_close($odbc);
?>
