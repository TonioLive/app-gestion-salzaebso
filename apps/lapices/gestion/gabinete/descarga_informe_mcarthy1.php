<?php
require_once __DIR__ . '/../../configuracion/deployment-config.php';
require_once __DIR__ . '/../../configuracion/encoding.php';
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");
require("../../configuracion/downloadfile.php");

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
        $consulta_datos_usuario=db_query("SELECT * FROM usuarios WHERE id_usuario=".$id_usuario_seleccionado);

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
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos,alumnos WHERE cursos_academicos.id_curso=alumnos.id_curso AND cursos_academicos.fecha_inicio<='".date("Y-m-d")."' AND alumnos.id_usuario=$id_usuario_seleccionado ORDER BY cursos_academicos.fecha_inicio DESC");

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query("SELECT * FROM cursos_academicos WHERE id_curso=$id_curso_actual");
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

        // Busca inscripcion en curso seleccionado
        $busca_alumno_curso=db_query("SELECT * FROM alumnos WHERE id_usuario=$id_usuario_seleccionado AND id_curso=$id_curso_actual");
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

if (strlen($id_prueba)!=0) {
        $busca_datos_prueba=db_query("SELECT * FROM pruebas_brunet WHERE id_prueba=$id_prueba");
        $datos_prueba=db_fetch_array($busca_datos_prueba);
        $observaciones_prueba=$datos_prueba["observaciones_prueba"];
        if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=app_url_text_utf8($observaciones_prueba);
                }
        $edad_real_prueba=$datos_prueba["er"];
        $fecha_dia=date("j",strtotime($datos_prueba["fecha"]));
        $fecha_mes=date("n",strtotime($datos_prueba["fecha"]));
        $fecha_ano=date("Y",strtotime($datos_prueba["fecha"]));
        $hora_hora=date("H",strtotime($datos_prueba["hora"]));
        $hora_min=date("i",strtotime($datos_prueba["hora"]));
        }
        
                        $busca_items_brunet=db_query("SELECT * FROM pruebas_brunet_items WHERE num_orden<161");

                        $puntosP=0;
                        $puntosC=0;
                        $puntosL=0;
                        $puntosS=0;
                        $puntosTotal=0;

                        $num_respuestasSI=0;
                        $num_respuestasNO=0;

                        while($items_brunet=db_fetch_array($busca_items_brunet)) {


                        // Busca respuesta
                        $busca_respuesta_item=db_query("SELECT * FROM pruebas_brunet_respuestas WHERE id_prueba=$id_prueba AND id_item=".$items_brunet["id_item"]) or die(db_error());
                        if (db_num_rows($busca_respuesta_item)!=0) {
                                $datos_respuesta_item=db_fetch_array($busca_respuesta_item);
                                $respuesta_item_actual=$datos_respuesta_item["respuesta"];
                                $valor_item_actual=$items_brunet["puntuacion"];
                                $escala_item_actual=$items_brunet["escala"];

                                if ($respuesta_item_actual==1) {

                                        $puntosTotal=$puntosTotal+$valor_item_actual;
                                        switch($escala_item_actual) {
                                                case 'P';
                                                        $puntosP = $puntosP+$valor_item_actual;
                                                        break;
                                                case 'C';
                                                        $puntosC = $puntosC+$valor_item_actual;
                                                        break;
                                                case 'L';
                                                        $puntosL = $puntosL+$valor_item_actual;
                                                        break;
                                                case 'S';
                                                        $puntosS = $puntosS+$valor_item_actual;
                                                        break;
                                                }
                                        }
                                }
                        }

                        // Busca resultado P
                        $busca_P=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='P' AND p=$puntosP");
                        if (db_num_rows($busca_P)!=0) {
                                $datosP=db_fetch_array($busca_P);
                                $edP=$datosP["m"]." meses, ".$datosP["d"]." días";
                                $edLP=$datosP["m"];
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="P";
                                $puntos_actual=$puntosP;

                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLPMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLPMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLPMas-$edLPMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edLP_1=($edLPMenos+($dif_Luntos*$valor_Luntos))/30;
                                $edLP=floor($edLP_1);
                                $edLP_2=ceil(($edLP_1-$edLP)*30);

                                $edP=$edLP." meses, ".$edLP_2." días";
                                }

                        // Busca resultado C
                        $busca_C=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='C' AND p=$puntosC");
                        if (db_num_rows($busca_C)!=0) {
                                $datosC=db_fetch_array($busca_C);
                                $edC=$datosC["m"]." meses, ".$datosC["d"]." días";
                                $edLC=$datosC["m"];
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="C";
                                $puntos_actual=$puntosC;

                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLCMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLCMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLCMas-$edLCMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edLC_1=($edLCMenos+($dif_Luntos*$valor_Luntos))/30;
                                $edLC=floor($edLC_1);
                                $edLC_2=ceil(($edLC_1-$edLC)*30);

                                $edC=$edLC." meses, ".$edLC_2." días";
                                }

                        // Busca resultado L
                        $busca_L=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='L' AND p=$puntosL");
                        if (db_num_rows($busca_L)!=0) {
                                $datosL=db_fetch_array($busca_L);
                                $edL=$datosL["m"]." meses, ".$datosL["d"]." días";
                                $edLL=$datosL["m"];
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="L";
                                $puntos_actual=$puntosL;

                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLMas-$edLMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edL_1=($edLMenos+($dif_Luntos*$valor_Luntos))/30;
                                $edL=floor($edL_1);
                                $edL_2=ceil(($edL_1-$edL)*30);
                                $edLL=$edL;
                                $edL=$edL." meses, ".$edL_2." días";
                                }

                        // Busca resultado S
                        $busca_S=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='S' AND p=$puntosS");
                        if (db_num_rows($busca_S)!=0) {
                                $datosS=db_fetch_array($busca_S);
                                $edS=$datosS["m"]." meses, ".$datosS["d"]." días";
                                $edLS=$datosS["m"];
                                } else {
                                // Busca anterior y posterior
                                $escala_actual="S";
                                $puntos_actual=$puntosS;

                                $busca_Lmenos=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p<$puntos_actual ORDER BY p DESC");
                                if (db_num_rows($busca_Lmenos)!=0) {
                                        $datos_Lmenos=db_fetch_array($busca_Lmenos);
                                        $puntos_Lmenos=$datos_Lmenos["p"];
                                        $edLSMenos=($datos_Lmenos["m"]*30)+$datos_Lmenos["d"];
                                        }

                                $busca_Lmas=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='$escala_actual' AND p>$puntos_actual ORDER BY p");
                                if (db_num_rows($busca_Lmas)!=0) {
                                        $datos_Lmas=db_fetch_array($busca_Lmas);
                                        $puntos_Lmas=$datos_Lmas["p"];
                                        $edLSMas=($datos_Lmas["m"]*30)+$datos_Lmas["d"];
                                        }

                                $dif_LuntosMasMenos=$puntos_Lmas-$puntos_Lmenos;
                                $did_EMAsMenos=$edLSMas-$edLSMenos;
                                $valor_Luntos=$did_EMAsMenos/$dif_LuntosMasMenos;
                                $dif_Luntos=$puntos_actual-$puntos_Lmenos;
                                $edLS_1=($edLSMenos+($dif_Luntos*$valor_Luntos))/30;
                                $edLS=floor($edLS_1);
                                $edLS_2=ceil(($edLS_1-$edLS)*30);

                                $edS=$edLS." meses, ".$edLS_2." días";
                                }

                        $edTotalM=floor($puntosTotal/10);
                        $edTotalD=30*(round(($puntosTotal/10),2)-$edTotalM);

                        $edTotal=$edTotalM." meses, ".$edTotalD." días";

        // Busca textos informe
        
        // P
        if ((($edLP-$edad_real_prueba)>-3) and (($edLP-$edad_real_prueba)<3)) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='P' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONTROL_POSTURAL=$datos_texto["texto"];
                }
        if (($edLP-$edad_real_prueba)<=-3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='P' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONTROL_POSTURAL=$datos_texto["texto"];
                }
        if (($edLP-$edad_real_prueba)>=3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='P' AND valor=2;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONTROL_POSTURAL=$datos_texto["texto"];
                }

        // C
        if ((($edLC-$edad_real_prueba)>-3) and (($edLC-$edad_real_prueba)<3)) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='C' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $COORDINACION_OM=$datos_texto["texto"];
                }
        if (($edLC-$edad_real_prueba)<=-3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='C' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $COORDINACION_OM=$datos_texto["texto"];
                }
        if (($edLC-$edad_real_prueba)>=3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='C' AND valor=2;");
                $datos_texto=db_fetch_array($busca_texto);
                $COORDINACION_OM=$datos_texto["texto"];
                }
        // L
        if ((($edLL-$edad_real_prueba)>-3) and (($edLL-$edad_real_prueba)<3)) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='L' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $LENGUAJE=$datos_texto["texto"];
                }
        if (($edLL-$edad_real_prueba)<=-3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='L' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $LENGUAJE=$datos_texto["texto"];
                }
        if (($edLL-$edad_real_prueba)>=3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='L' AND valor=2;");
                $datos_texto=db_fetch_array($busca_texto);
                $LENGUAJE=$datos_texto["texto"];
                }
                
        // S
        
        // Comprueba si es antiguo alumno
        $busca_inscripcion_ant=db_query("SELECT * FROM alumnos WHERE id_usuario=".$id_usuario_seleccionado." AND id_curso<$id_curso_actual") or die(db_error());

        if (db_num_rows($busca_inscripcion_ant)!=0) {
                // Antiguo
                if (($edLS-$edad_real_prueba)<=-3) {
                        $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE alumnos=1 AND tipo='S' AND valor=4;");
                        $datos_texto=db_fetch_array($busca_texto);
                        $SOCIABILIDAD=$datos_texto["texto"];
                        } else {
                        $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE alumnos=1 AND tipo='S' AND valor=3;");
                        $datos_texto=db_fetch_array($busca_texto);
                        $SOCIABILIDAD=$datos_texto["texto"];
                        }
                } else {
                // Nuevo
                if (($edLS-$edad_real_prueba)<=-3) {
                        $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE alumnos=2 AND tipo='S' AND valor=4;");
                        $datos_texto=db_fetch_array($busca_texto);
                        $SOCIABILIDAD=$datos_texto["texto"];
                        } else {
                        $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE alumnos=2 AND tipo='S' AND valor=3;");
                        $datos_texto=db_fetch_array($busca_texto);
                        $SOCIABILIDAD=$datos_texto["texto"];
                        }
                }
                
        // Global
        if ((($edTotalM-$edad_real_prueba)>-3) and (($edTotalM-$edad_real_prueba)<3)) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='G' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONCLUSIONES=$datos_texto["texto"];
                }
        if (($edTotalM-$edad_real_prueba)<=-3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='G' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONCLUSIONES=$datos_texto["texto"];
                }
        if (($edTotalM-$edad_real_prueba)>=3) {
                $busca_texto=db_query("SELECT * FROM pruebas_brunet_informe WHERE tipo='G' AND valor=2;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONCLUSIONES=$datos_texto["texto"];
                }


        // Abre plantilla
        $nombre_archivo=$web_path."/modelos/modelo_brunet$id_centro_alumno.rtf";
        $gestor = fopen($nombre_archivo, "r");
        $cuerpo_documento = fread($gestor, filesize($nombre_archivo));
        fclose($gestor);

        $cuerpo_documento=str_replace("[CONTROL_POSTURAL]",$CONTROL_POSTURAL,$cuerpo_documento);
        $cuerpo_documento=str_replace("[COORDINACION_OM]",$COORDINACION_OM,$cuerpo_documento);
        $cuerpo_documento=str_replace("[LENGUAJE]",$LENGUAJE,$cuerpo_documento);
        $cuerpo_documento=str_replace("[SOCIABILIDAD]",$SOCIABILIDAD,$cuerpo_documento);
        $cuerpo_documento=str_replace("[CONCLUSIONES]",$CONCLUSIONES,$cuerpo_documento);

        $cuerpo_documento=str_replace("[nombre]",ucwords(mb_strtolower($usuario_nombre)),$cuerpo_documento);
        $cuerpo_documento=str_replace("[nombre_may]",mb_strtoupper($usuario_nombre),$cuerpo_documento);
        $cuerpo_documento=str_replace("[edad_real]",$edad_real_prueba,$cuerpo_documento);


        if ($usuario_sexo=="H") {
                $cuerpo_documento=str_replace("del niño","de la niña",$cuerpo_documento);
                $cuerpo_documento=str_replace("un niño","una niña",$cuerpo_documento);
                $cuerpo_documento=str_replace("está integrado","está integrada",$cuerpo_documento);
                }


        // Inserta gráfica
        
        include "class.bargraph.php";

        $g = new BarGraph();
        $data = array("Control Postural"=>$edLP,"Coordinación Oculo-Motriz"=>$edLC,"Lenguaje"=>$edLL,"Sociabilidad"=>$edLS);
        $g->SetGraphPadding(15, 10, 10, 20);
        $g->SetGraphAreaHeight(300);
        $g->SetBarPadding(46);
        // $g->SetAxisStep(5);
        $g->SetBarData($data);
        $g->SetGraphTitle("");

        $g->DrawGraph($web_path."/graficos/$id_prueba.png");
        
        $b=fopen($web_path."/graficos/$id_prueba.png","rb");

        $imgData=getimagesize($web_path."/graficos/$id_prueba.png");

        $newImagePre="{\\*\\shppict{\\pict \\jpegblip \\picw".$imgData[0]." \\pich".$imgData[1]." \\wbmbitspixel24 ";
        while (!feof($b)) {
                $newImage.= fgets($b);
                }
        $hex=bin2hex($newImage);
        $imgDat=$newImagePre.$hex."}}";
        $cuerpo_documento=str_replace("[GRAFICO]",$imgDat,$cuerpo_documento);

        // Escribe documento

        // datos aula y alumnos
        $busca_alumnos=db_query("SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=$id_usuario_seleccionado AND alumnos.id_curso=$id_curso_actual ORDER BY Apellido1, Apellido2, Nombre");
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query("SELECT * FROM centro_aulas WHERE id_aula=".$datos_alumnos["aula"].";");
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query("SELECT * FROM centros WHERE id_centro=".$datos_aulas["id_centro"]);
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $carpeta_documentacion=(lapices_storage_path('gabinete') . "/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno");

        $sustituir=array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª','É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê','Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î','Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô','Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û','Ñ', 'ñ', 'Ç', 'ç');						
        $carpeta_documentacion=str_replace($sustituir,"_",$carpeta_documentacion);

        $nombre_archivo=$carpeta_documentacion."/Informe_Brunet_".$id_prueba."_$fecha_dia-$fecha_mes-$fecha_ano-".date("His").".rtf";
        $gestor_nuevo = fopen($nombre_archivo, "w+");
        fwrite($gestor_nuevo,$cuerpo_documento);
        fclose($gestor_nuevo);

        $dFile = new Download_file($carpeta_documentacion , "Informe_Brunet_".$id_prueba."_$fecha_dia-$fecha_mes-$fecha_ano-".date("His").".rtf");


?>
