<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/downloadfile.php");
include "class.bargraph2.php";
include("funciones_resultado_array.php");

$id_prueba=$_GET["id_prueba"];

if (strlen($id_usuario_seleccionado)==0) {
        $id_usuario_seleccionado=$_GET["id_alumno"];
        if (strlen($id_usuario_seleccionado)==0) {
                $id_usuario_seleccionado=$_POST["id_alumno"];
                if (strlen($id_usuario_seleccionado)==0) {
                        $id_usuario_seleccionado=0;
                        $accion_texto="Nuevo";
                        }
                }
        }

$id_curso_actual=$_GET["id_curso_alumno"];

if (strlen($id_curso_actual)==0) {
        $id_curso_actual=0;
        }

if ($id_usuario_seleccionado!=0) {
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado)) or die(db_error()." Fila 90");

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);

                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                // Datos del paso
                $usuario_sexo=$datos_usuario["sexo"];

if ($datos_usuario["nonato"]==1) {
        $usuario_fechanac="No ha nacido";
        } else {

                if (strlen($datos_usuario["fecha_nacimiento"])==0) {
                        $usuario_fechanac="";
                        } else {
                        $nacimiento=explode( '-', $datos_usuario["fecha_nacimiento"]);
                        $edad=date("Y")-$nacimiento[0];
                        $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                        $hoy=strtotime(date("Y/m/d"));
                        if (($cumple-$hoy)>0) {
                                $edad=$edad-1;
                                }

                // 1. Comprueba la edad restando el año de nacimiento al año actual
                $fecha_l=$datos_usuario["fecha_nacimiento"];
                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                // 1.1. Cacula edad con decimales
                $mes_d_l=date("m",strtotime($fecha_l));
                $mes_d_n=date("m");
                $dif_meses=$mes_d_n-$mes_d_l;

                $edad_d_l=($edad_a_l*12)+$dif_meses;
                $edad=$edad_d_l." meses";

                        $usuario_fechanac=$edad." (Fecha nacimiento: ".date("d/m/Y",strtotime($datos_usuario["fecha_nacimiento"])).")";
                        }
                }
                
                $usuario_sexo=$datos_usuario["sexo"];
                $usuario_direccion=$datos_usuario["Direccion"];
                $usuario_cp=$datos_usuario["cp"];
                $usuario_poblacion=$datos_usuario["Poblacion"];
                $usuario_provincia=$datos_usuario["Provincia"];
                $usuario_telefono1=$datos_usuario["telefono1"];
                $usuario_telefono2=$datos_usuario["telefono2"];
                $usuario_movil=$datos_usuario["movil"];
                $usuario_email=$datos_usuario["email"];
                $alergias=$datos_usuario["alergias"];
                $vacunaciones=$datos_usuario["vacunaciones"];
                $observaciones=$datos_usuario["observaciones"];
                $contacto1=$datos_usuario["contacto1"];
                $telefono_contacto1=$datos_usuario["telefono_contacto1"];
                $contacto2=$datos_usuario["contacto2"];
                $telefono_contacto2=$datos_usuario["telefono_contacto2"];
                $contacto_medico=$datos_usuario["contacto_medico"];
                $telefono_medico=$datos_usuario["telefono_medico"];
                $usuario_nacionalidad=$datos_usuario["nacionalidad"];
                }

        // Busca el curso actual
        if ($id_curso_actual==0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d")))) or die(db_error()." Fila 158");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio") or die(db_error()." Fila 164");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual)) or die(db_error()." Fila 169");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

        // Busca inscripcion en curso seleccionado
        $busca_alumno_curso=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?', 'ss', array($id_usuario_seleccionado, $id_curso_actual)) or die(db_error()." Fila 174");
        if (db_num_rows($busca_alumno_curso)!=0) {
                $datos_curso_alumno=db_fetch_array($busca_alumno_curso);
                $id_centro_alumno=$datos_curso_alumno["id_centro"];
                } else {
                $id_centro_alumno=$centro_actual;
                }

        }

// Nombre foto
$nombre_foto="foto_usu".$id_usuario_seleccionado.".jpg";
$mensaje_foto="Foto Actual";
if (!file_exists($upload_path."//$nombre_foto")) {
      $nombre_foto="sin_foto.jpg";
      $mensaje_foto="Insertar Foto";
      }


// Busca datos prueba

        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_comparadas WHERE id_prueba=?', 's', array($id_prueba)) or die(db_error()." Fila 195");
        $datos_prueba=db_fetch_array($busca_datos_prueba);
        $observaciones_prueba=$datos_prueba["observaciones_prueba"];
        if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=urldecode($observaciones_prueba);
                }
        $edad_real_prueba=$datos_prueba["er"];
        $fecha_dia=date("j",strtotime($datos_prueba["fecha"]));
        $fecha_mes=date("n",strtotime($datos_prueba["fecha"]));
        $fecha_ano=date("Y",strtotime($datos_prueba["fecha"]));
        $prueba1=$datos_prueba["id_prueba1"];
        $prueba2=$datos_prueba["id_prueba2"];
        
        // Tipo informe
        $tipo_prueba1=substr($prueba1,0,1);
        $id_prueba1=substr($prueba1,1,strlen($prueba1)-1);
        
        $tipo_prueba2=substr($prueba2,0,1);
        $id_prueba2=substr($prueba2,1,strlen($prueba2)-1);

        // Busca cursos pruebas
        if (strlen($id_prueba1)!=0) {
                $fecha_prueba1="";
                switch($tipo_prueba1) {
                        case "B":
                                // 1. Brunet
                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet WHERE id_prueba=?', 's', array($id_prueba1)) or die(db_error()." Fila 221");
                                if (db_num_rows($busca_datos_prueba)!=0) {
                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                        $fecha_prueba1=$datos_prueba["fecha"];
                                        $edad_real_prueba1=$datos_prueba["er"];
                                        }
                                break;
                        case "A":
                                // 2. Brunet Ampliado
                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet_ampliado WHERE id_prueba=?', 's', array($id_prueba1)) or die(db_error()." Fila 229");
                                if (db_num_rows($busca_datos_prueba)!=0) {
                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                        $fecha_prueba1=$datos_prueba["fecha"];
                                        $edad_real_prueba1=$datos_prueba["er"];
                                        }
                                break;
                        case "C":
                                // 3. Mc Carhty
                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba1)) or die(db_error()." Fila 237");
                                if (db_num_rows($busca_datos_prueba)!=0) {
                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                        $fecha_prueba1=$datos_prueba["fecha"];
                                        $edad_real_prueba1=$datos_prueba["er"];
                                        }
                                break;
                        }
                if (strlen($fecha_prueba1)!=0) {
                        // Busca curso por fecha
                        $busca_curso1=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)($fecha_prueba1), (string)($fecha_prueba1))) or die(db_error()." Fila 246");

                        if (db_num_rows($busca_curso1)!=0) {
                                $datos_curso1=db_fetch_array($busca_curso1);
                                $id_curso1=$datos_curso1["id_curso"];
                                $nombre_curso1=$datos_curso1["nombre"];
                                } else {
                                $id_curso1=0;
                                $nombre_curso1="";
                                }
                        }
                }

        if (strlen($id_prueba2)!=0) {
                $fecha_prueba2="";
                switch($tipo_prueba2) {
                        case "B":
                                // 1. Brunet
                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet WHERE id_prueba=?', 's', array($id_prueba2)) or die(db_error()." Fila 264");
                                if (db_num_rows($busca_datos_prueba)!=0) {
                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                        $fecha_prueba2=$datos_prueba["fecha"];
                                        $edad_real_prueba2=$datos_prueba["er"];
                                        }
                                break;
                        case "A":
                                // 2. Brunet Ampliado
                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet_ampliado WHERE id_prueba=?', 's', array($id_prueba2)) or die(db_error()." Fila 272");
                                if (db_num_rows($busca_datos_prueba)!=0) {
                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                        $fecha_prueba2=$datos_prueba["fecha"];
                                        $edad_real_prueba2=$datos_prueba["er"];
                                        }
                                break;
                        case "C":
                                // 3. Mc Carhty
                                $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba2)) or die(db_error()." Fila 280");
                                if (db_num_rows($busca_datos_prueba)!=0) {
                                        $datos_prueba=db_fetch_array($busca_datos_prueba);
                                        $fecha_prueba2=$datos_prueba["fecha"];
                                        $edad_real_prueba2=$datos_prueba["er"];
                                        }
                                break;
                        }
                if (strlen($fecha_prueba2)!=0) {
                        // Busca curso por fecha
                        $busca_curso2=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)($fecha_prueba2), (string)($fecha_prueba2))) or die(db_error()." Fila 289");

                        if (db_num_rows($busca_curso2)!=0) {
                                $datos_curso2=db_fetch_array($busca_curso2);
                                $id_curso2=$datos_curso2["id_curso"];
                                $nombre_curso2=$datos_curso2["nombre"];
                                } else {
                                $id_curso2=0;
                                $nombre_curso2="";
                                }
                        }
                }

        $tipo_informe=$tipo_prueba1.$tipo_prueba2;

        if (($tipo_informe=="AA") or ($tipo_informe=="AB") or ($tipo_informe=="BA") or ($tipo_informe=="BB")) {
                // Brunet-Brunet
                $nombre_comparativa="Brunet-Brunet";

                if ($edad_real_prueba2>28) {
                        $variante_pruebas="2";
                        } else {
                        $variante_pruebas="1";
                        }
                
                if ($id_curso1==$id_curso2) {
                        // Mismo curso
                        $nombre_archivo=$web_path."/modelos/brunet-brunet".$variante_pruebas."A.rtf";
                        } else {
                        // Distintos cursos
                        $nombre_archivo=$web_path."/modelos/brunet-brunet".$variante_pruebas."B.rtf";
                        }
                        
                }
        if (($tipo_informe=="AC") or ($tipo_informe=="BC") or ($tipo_informe=="CA") or ($tipo_informe=="CB")) {
                // Brunet-McCarthy
                $nombre_comparativa="Brunet-McCarthy";
                $nombre_archivo=$web_path."/modelos/brunet-mccarthy.rtf";
                }
        if ($tipo_informe=="CC") {
                // McCarthy-McCarthy
                $nombre_comparativa="McCarthy-McCarthy";
                $nombre_archivo=$web_path."/modelos/mccarthy-mccarthy.rtf";
                }

        if ($id_curso1==$id_curso2) {
                $titulo_g1="Primer trimestre";
                $titulo_g2="Tercer trimestre";
                } else {
                $titulo_g1=$nombre_curso1;
                $titulo_g2=$nombre_curso2;
                }

        // Abre plantilla
        $gestor = fopen($nombre_archivo, "r");
        $cuerpo_documento = fread($gestor, filesize($nombre_archivo));
        fclose($gestor);

        if ($id_curso1==$id_curso2) {
                $cuerpo_documento=str_replace("[nombre_curso]",$nombre_curso1,$cuerpo_documento);
                } else {
                $cuerpo_documento=str_replace("[nombre_curso]",$nombre_curso1." y el curso ".$nombre_curso2,$cuerpo_documento);
                }

        $cuerpo_documento=str_replace("[nombre]",ucwords(mb_strtolower($usuario_nombre)),$cuerpo_documento);
        $cuerpo_documento=str_replace("[nombre_may]",mb_strtoupper($usuario_nombre),$cuerpo_documento);
        $cuerpo_documento=str_replace("[edad_real]",$edad_real_prueba,$cuerpo_documento);

        $cuerpo_documento=str_replace("[titulo_g1]",$titulo_g1,$cuerpo_documento);
        $cuerpo_documento=str_replace("[titulo_g2]",$titulo_g2,$cuerpo_documento);

        $cuerpo_documento=str_replace("[edad_g1]",$edad_real_prueba1." meses",$cuerpo_documento);
        $cuerpo_documento=str_replace("[edad_g2]",$edad_real_prueba2." meses",$cuerpo_documento);

        if ($usuario_sexo=="H") {
                $cuerpo_documento=str_replace("[del_sujeto]","de la niña",$cuerpo_documento);
                $cuerpo_documento=str_replace("[un_sujeto]","una niña",$cuerpo_documento);
                $cuerpo_documento=str_replace("[vuestro]","vuestra hija",$cuerpo_documento);
                $cuerpo_documento=str_replace("[solo]","sola",$cuerpo_documento);
                $cuerpo_documento=str_replace("[sujeto]","hija",$cuerpo_documento);
                $cuerpo_documento=str_replace("[oa]","a",$cuerpo_documento);
                } else {
                $cuerpo_documento=str_replace("[del_sujeto]","del niño",$cuerpo_documento);
                $cuerpo_documento=str_replace("[un_sujeto]","un niño",$cuerpo_documento);
                $cuerpo_documento=str_replace("[vuestro]","vuestro hijo",$cuerpo_documento);
                $cuerpo_documento=str_replace("[solo]","solo",$cuerpo_documento);
                $cuerpo_documento=str_replace("[sujeto]","hijo",$cuerpo_documento);
                $cuerpo_documento=str_replace("[oa]","o",$cuerpo_documento);
                }
                
        // Inserta graficas

        if ($tipo_informe=="CC") {
                $resultado1=calcular_mccarthy($id_prueba1);
                $resultado2=calcular_mccarthy($id_prueba2);
                
                $g = new BarGraph();
                $data = array("       Ver"=>$resultado1[0],"bal        "=>$resultado2[0],"Perceptivo "=>$resultado1[1],"  Manipulativa"=>$resultado2[1],"      Numé"=>$resultado1[2],"rica      "=>$resultado2[2],"   General "=>$resultado1[3]," Cognitiva  "=>$resultado2[3],"       Mem"=>$resultado1[4],"oria      "=>$resultado2[4],"     Motri"=>$resultado1[5],"cidad     "=>$resultado2[5]);
                $g->SetGraphPadding(15, 10, 10, 20);
                $g->SetGraphAreaHeight(300);
                $g->SetBarPadding(15);
                $g->SetAxisStep(5);
                $g->SetBarData($data);
                $g->SetGraphTitle("");
                $g->Setbar_uppertext(2);
                $g->Setbar_uppertextsize(3);
                $g->Setbar_width(20);
                $g->Setaxis_ocultatexto(1);
                $g->Setaxis_textsize(1.5);
                $g->Setgraph_2_colores(1);
                
                $g->DrawGraph($web_path."/graficos/comp".$id_prueba1.".png");

                } else {
                switch($tipo_prueba1) {
                        case "B":
                                // 1. Brunet
                                $resultado1=calcular_brunet($id_prueba1);
                                break;
                        case "A":
                                // 2. Brunet Ampliado
                                $resultado1=calcular_brunet_a($id_prueba1);
                                break;
                        case "C":
                                // 3. Mc Carhty
                                $resultado1=conversion_mccarthy($id_prueba1);
                                break;
                        }
                        
                switch($tipo_prueba2) {
                        case "B":
                                // 1. Brunet
                                $resultado2=calcular_brunet($id_prueba2);
                                break;
                        case "A":
                                // 2. Brunet Ampliado
                                $resultado2=calcular_brunet_a($id_prueba2);
                                break;
                        case "C":
                                // 3. Mc Carhty
                                $resultado2=conversion_mccarthy($id_prueba2);
                                break;
                        }
                        
                $g = new BarGraph();
                
                if (($tipo_informe=="AA") or ($tipo_informe=="AB") or ($tipo_informe=="BA") or ($tipo_informe=="BB")) {
                        // Brunet-Brunet
                        $data = array("   Control"=>$resultado1[0],"Postural    "=>$resultado2[0],"Coordinación "=>$resultado1[1]," Oculo-Motriz"=>$resultado2[1],"        Leng"=>$resultado1[2],"uaje       "=>$resultado2[2],"     Socia"=>$resultado1[3],"bilidad      "=>$resultado2[3]);
                        }
                if (($tipo_informe=="AC") or ($tipo_informe=="BC") or ($tipo_informe=="CA") or ($tipo_informe=="CB")) {
                        // Brunet-McCarthy
                        $data = array("   Control"=>$resultado1[0],"Postural    "=>$resultado2[0],"Coordinación "=>$resultado1[1]," Oculo-Motriz"=>$resultado2[1],"        Leng"=>$resultado1[2],"uaje       "=>$resultado2[2],"    Edad "=>$resultado1[4]," Madurativa    "=>$resultado2[4]);
                        }
                
                $g->SetGraphPadding(15, 10, 10, 20);
                $g->SetGraphAreaHeight(300);
                $g->SetBarPadding(15);
                // $g->SetAxisStep(5);
                $g->SetBarData($data);
                $g->SetGraphTitle("");
                $g->Setgraph_2_colores(1);

                $g->DrawGraph($web_path."/graficos/comp".$id_prueba1.".png");
                
                }

        $b=fopen($web_path."/graficos/comp".$id_prueba1.".png","rb");

        $imgData=getimagesize($web_path."/graficos/comp".$id_prueba1.".png");

        $newImagePre="{\\*\\shppict{\\pict \\jpegblip \\picw".$imgData[0]." \\pich".$imgData[1]." \\wbmbitspixel24 ";
        while (!feof($b)) {
                $newImage.= fgets($b);
                }
        $hex=bin2hex($newImage);
        $imgDat=$newImagePre.$hex."}}";
        
        $cuerpo_documento=str_replace("[grafico_niveles1]",$imgDat,$cuerpo_documento);

        /*
        $b1=fopen($web_path."/graficos/comp".$id_prueba2.".png","rb");

        $imgData1=getimagesize($web_path."/graficos/comp".$id_prueba2.".png");

        $newImagePre1="{\\*\\shppict{\\pict \\jpegblip \\picw".$imgData1[0]." \\pich".$imgData1[1]." \\wbmbitspixel24 ";
        while (!feof($b1)) {
                $newImage1.= fgets($b1);
                }
        $hex1=bin2hex($newImage1);
        $imgDat1=$newImagePre1.$hex1."}}";
        
        $cuerpo_documento=str_replace("[grafico_niveles2]",$imgDat1,$cuerpo_documento);
        */
        
        // Escribe documento

        // datos aula y alumnos
        $busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=? AND alumnos.id_curso=? ORDER BY Apellido1, Apellido2, Nombre', 'ss', array($id_usuario_seleccionado, $id_curso_actual)) or die(db_error()." Fila 409");
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($datos_alumnos["aula"])) or die(db_error()." Fila 411");
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aulas["id_centro"])) or die(db_error()." Fila 413");
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $carpeta_documentacion="/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno";


        $nombre_archivofecha=date("d")."-".date("m")."-".date("Y")."-".date("His");

        $nombre_archivo=$carpeta_documentacion."/".$nombre_comparativa."_".$id_prueba."_".$nombre_archivofecha.".rtf";
        $gestor_nuevo = fopen($nombre_archivo, "w+");
        fwrite($gestor_nuevo,$cuerpo_documento);
        fclose($gestor_nuevo);

        $dFile = new Download_file($carpeta_documentacion , $nombre_comparativa."_".$id_prueba."_".$nombre_archivofecha.".rtf");
?>
