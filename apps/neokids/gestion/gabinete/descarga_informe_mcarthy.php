<?php 
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
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario_seleccionado));

        if (db_num_rows($consulta_datos_usuario)!=0)
                {
                $datos_usuario=db_fetch_array($consulta_datos_usuario);

                // Datos Fijos
                $usuario_nombre=$datos_usuario["Nombre"];
                $usuario_apellido1=$datos_usuario["Apellido1"];
                $usuario_apellido2=$datos_usuario["Apellido2"];

                $fecha_nacimiento_alumno=$datos_usuario["fecha_nacimiento"];

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
                                $edad_en_dias=date("t")-(date("j",$cumple)-date("j",$hoy));
                                } else {
                                $edad_en_dias=(date("j",$cumple)-date("j",$hoy));
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
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos,alumnos WHERE cursos_academicos.id_curso=alumnos.id_curso AND cursos_academicos.fecha_inicio<=? AND alumnos.id_usuario=? ORDER BY cursos_academicos.fecha_inicio DESC', 'ss', array((string)(date("Y-m-d")), $id_usuario_seleccionado));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                } else {
                $busca_curso_actual=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $id_curso_actual=$datos_curso_actual["id_curso"];
                }
        } else {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_actual));
        $datos_curso_actual=db_fetch_array($busca_curso_actual);
        }

        // Busca inscripcion en curso seleccionado
        $busca_alumno_curso=db_query_params('SELECT * FROM alumnos WHERE id_usuario=? AND id_curso=?', 'ss', array($id_usuario_seleccionado, $id_curso_actual));
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
        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba));
        $datos_prueba=db_fetch_array($busca_datos_prueba);
        $observaciones_prueba=$datos_prueba["observaciones_prueba"];
        if (strlen($observaciones_prueba)!=0) {
                $observaciones_prueba=urldecode($observaciones_prueba);
                }
        $edad_real_prueba=$datos_prueba["er"];
        $fecha_dia=date("j",strtotime($datos_prueba["fecha"]));
        $fecha_mes=date("n",strtotime($datos_prueba["fecha"]));
        $fecha_ano=date("Y",strtotime($datos_prueba["fecha"]));
        $hora_hora=date("H",strtotime($datos_prueba["hora"]));
        $hora_min=date("i",strtotime($datos_prueba["hora"]));

                        $nacimiento=explode( '-', $fecha_nacimiento_alumno);
                        $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                        $hoy=strtotime($datos_prueba["fecha"]);

                        if ((date("j",$cumple)-date("j",$hoy))>0) {
                                $edad_en_dias_prueba=date("t",strtotime($datos_prueba["fecha"]))-(date("j",$cumple)-date("j",$hoy));
                                } else {
                                $edad_en_dias_prueba=date("j",$hoy)-date("j",$cumple);
                                }

        $edad_real_prueba_gci=round($edad_real_prueba+($edad_en_dias_prueba/date("t",strtotime($datos_prueba["fecha"]))),1);

        }
        
                        $puntuacion_parte_act=array();
                        $multiplicador_parte_act=array();

                        $total_d=0;
                        $total_i=0;
                        $total_a=0;

                        $total_v=0;
                        $total_pm=0;
                        $total_n=0;
                        $total_mem=0;
                        $total_mot=0;

                        $busca_items_mcarhty=db_query("SELECT * FROM pruebas_mcarhty_ejercicios ORDER BY id_ejercicio");
                        $edad_actual=0;
                        $contador_array=0;
                        $marcar_edad=false;
                        $colores_filas=array("#ffffff","#efefef");

                        while($ejercicios_mcarhty=db_fetch_array($busca_items_mcarhty)) {
                        $edad_actual=$ejercicios_mcarhty["id_ejercicio"];

                        $ejercicio_actual_p=0;

                        $estado_capa_d="block";
                        $estado_capa_a="none";

                                $puntuacion_parte_act[1]=0;
                                $multiplicador_parte_act[1]=1;
                                $puntuacion_parte_act[2]=0;
                                $multiplicador_parte_act[2]=1;
                                $puntuacion_parte_act[3]=0;
                                $multiplicador_parte_act[3]=1;

                                $parte_actual=0;
                                $contador_pregunta=1;
                                $busca_items_mcarthy=db_query("SELECT * FROM pruebas_mcarthy_items WHERE num_ejercicio=$edad_actual ORDER BY parte,orden_ejercicio");
                                while($items_mcarthy=db_fetch_array($busca_items_mcarthy)) {

                                $item_actual_p=0;

                                $id_item_actual=$items_mcarthy["id_item"];

                                // Cuenta partes
                                $busca_partes=db_query("SELECT * FROM pruebas_mcarthy_items WHERE num_ejercicio=$edad_actual GROUP BY parte");
                                if (db_num_rows($busca_partes)>1) {

                                        if ($parte_actual!=$items_mcarthy["parte"]) {
                                                $contador_pregunta=1;
                                                $parte_actual=$items_mcarthy["parte"];
                                                $mostrar_encabezado=true;
                                                }

                                         } else {
                                         if ($parte_actual==0) {
                                                 $parte_actual=$items_mcarthy["parte"];
                                                 $mostrar_encabezado=true;
                                                 }
                                         }


                                        // Busca respuestas

                                        $busca_respuestas_item_actual=db_query_params('SELECT * FROM pruebas_mcarthy_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $id_item_actual));

                                        if (db_num_rows($busca_respuestas_item_actual)!=0) {
                                                $datos_respuestas_item_actual=db_fetch_array($busca_respuestas_item_actual);
                                                $item_actual_dato1=$datos_respuestas_item_actual["dato1"];
                                                $item_actual_dato2=$datos_respuestas_item_actual["dato2"];
                                                $item_actual_dato3=$datos_respuestas_item_actual["dato3"];
                                                $item_actual_dato4=$datos_respuestas_item_actual["dato4"];
                                                $item_actual_dato5=$datos_respuestas_item_actual["dato5"];
                                                $item_actual_respuesta=$datos_respuestas_item_actual["respuesta"];
                                                $item_actual_verdaderofalso=$datos_respuestas_item_actual["verdaderofalso"];
                                                $item_actual_lateralidad=$datos_respuestas_item_actual["lateralidad"];
                                                $item_actual_observaciones=$datos_respuestas_item_actual["observaciones"];
                                                } else {
                                                $item_actual_dato1="";
                                                $item_actual_dato2="";
                                                $item_actual_dato3="";
                                                $item_actual_dato4="";
                                                $item_actual_dato5="";
                                                $item_actual_respuesta="";
                                                $item_actual_verdaderofalso="";
                                                $item_actual_lateralidad="";
                                                $item_actual_observaciones="";
                                                }

                                       // Vamos a ello
                                        switch($ejercicios_mcarhty["tipo_ejercicio_p".($parte_actual)]) {
                                                case 1:
                                                // 1. Puntuación I1 I2 por item, cuenta la mejor. Observaciones

                                                $p1=$item_actual_dato1;
                                                $p2=$item_actual_dato2;

                                                if ($p1>$p2) {
                                                        $item_actual_p=$p1;
                                                        } else {
                                                        $item_actual_p=$p2;
                                                        }

                                                break;
                                                case 2:
                                                // 2. Puntuación por item

                                                $item_actual_p=$item_actual_dato1;

                                                break;
                                                case 3:
                                                // 3. Marcar si o no, cada uno suma 1 puntom

                                                $item_actual_p=$item_actual_verdaderofalso;

                                                break;
                                                case 4:
                                                // 4. Marcar si o no, cada uno suma 1 puntom

                                                $item_actual_p=$item_actual_verdaderofalso;

                                                break;
                                                case 5:
                                                // 5. Marcar nº de respuestas y puntuación

                                                $item_actual_p=$item_actual_dato2;

                                                break;
                                                case 6:
                                                // 6. Puntuación en I1, I2 e I3, cuenta la mejor

                                                $array_intentos=array($item_actual_dato1,$item_actual_dato2,$item_actual_dato3);

                                                sort($array_intentos);

                                                $item_actual_p=$array_intentos[2];

                                                break;
                                                case 7:
                                                // 7. Indicar puntuación y escribir respuesta

                                                $item_actual_p=$item_actual_dato1;

                                                break;
                                                case 7:
                                                // 7. Indicar puntuación y escribir respuesta

                                                $item_actual_p=$item_actual_dato1;

                                                break;
                                                case 8:
                                                // 8. Botes I1 e I2, Puntuación en I1, I2, Lateralidad

                                                $p1=$item_actual_dato2;
                                                $p2=$item_actual_dato4;

                                                if ($p1>$p2) {
                                                        $item_actual_p=$p1;
                                                        } else {
                                                        $item_actual_p=$p2;
                                                        }

                                                break;
                                                case 9:
                                                // 9. Indicar intento, Mano preferida

                                                $item_actual_p=$item_actual_dato1+$item_actual_dato2+$item_actual_dato3;

                                                break;
                                                case 15:
                                                // 15. Indicar intento, Mano preferida

                                                $item_actual_p=$item_actual_dato1+$item_actual_dato2;

                                                break;
                                                case 16:
                                                // 16. Puntuación, Mano preferida

                                                $item_actual_p=$item_actual_dato1;

                                                break;
                                                case 17:
                                                // 17. Puntuación, Mano preferida

                                                $item_actual_p=$item_actual_dato1;

                                                if ($item_actual_lateralidad=="D") { $lat_1213D++; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_1213D++; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_1213D++; $total_a++;  }

                                                break;
                                                case 18:
                                                // 18.  Mano preferida

                                                break;
                                                case 19:
                                                // 19.  Observaciones

                                                break;
                                                }

                                        $multiplicador_actual=$ejercicios_mcarhty["multiplicador".($parte_actual)];

                                        // $item_actual_p=$item_actual_p*$multiplicador_actual;
                                        // $ejercicio_actual_p=$ejercicio_actual_p+$item_actual_p;

                                        $puntuacion_parte_act[$parte_actual]=$puntuacion_parte_act[$parte_actual]+$item_actual_p;
                                        $multiplicador_parte_act[$parte_actual]=$multiplicador_actual;

                                        // Lateralidad
                                        // Test 10 parte 1
                                        if ($id_item_actual==91) {
                                                if ($item_actual_lateralidad=="D") { $lat_10_1D=1; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_10_1I=1; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_10_1A=1; $total_a++;  }
                                                }
                                        // Test 10 parte II Elemento 2
                                        if ($id_item_actual==93) {
                                                if ($item_actual_lateralidad=="D") { $lat_10_21D=1; $total_d++; }
                                                if ($item_actual_lateralidad=="I") { $lat_10_21I=1; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_10_21A=1; $total_a++;  }
                                                }
                                        // Test 10 parte III Elemento 1
                                        if ($id_item_actual==95) {
                                                if ($item_actual_lateralidad=="D") { $lat_10_22D=1; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_10_22I=1; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_10_22A=1; $total_a++;  }
                                                }
                                        // Test 13
                                        if ($id_item_actual==162) {
                                                if ($item_actual_lateralidad=="D") { $lat_1213D++; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_1213I++; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_1213A++; $total_a++;  }
                                                }
                                        // Test 11
                                        if ($id_item_actual==100) {
                                                if ($item_actual_lateralidad=="D") { $lat_11_4D++; $total_d++;  }
                                                if ($item_actual_lateralidad=="I") { $lat_11_4I++; $total_i++;  }
                                                if ($item_actual_lateralidad=="A") { $lat_11_4A++; $total_a++;  }
                                                }

                                        $contador_pregunta++;
                                        }

                                        $ejercicio_actual_p=ceil($puntuacion_parte_act[1]*$multiplicador_parte_act[1])+ceil($puntuacion_parte_act[2]*$multiplicador_parte_act[2])+ceil($puntuacion_parte_act[3]*$multiplicador_parte_act[3]);

                                        $item_actual_v=$ejercicios_mcarhty["v"]*$ejercicio_actual_p;
                                        $item_actual_pm=$ejercicios_mcarhty["pm"]*$ejercicio_actual_p;
                                        $item_actual_n=$ejercicios_mcarhty["n"]*$ejercicio_actual_p;
                                        $item_actual_mem=$ejercicios_mcarhty["mem"]*$ejercicio_actual_p;
                                        $item_actual_mot=$ejercicios_mcarhty["mot"]*$ejercicio_actual_p;

                                        $total_v=$total_v+$item_actual_v;
                                        $total_pm=$total_pm+$item_actual_pm;
                                        $total_n=$total_n+$item_actual_n;
                                        $total_mem=$total_mem+$item_actual_mem;
                                        $total_mot=$total_mot+$item_actual_mot;

                                        // Busca GCI
                                        $pdc=$total_v+$total_pm+$total_n;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $gci=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc ORDER BY desde_mt DESC");
                                                if (db_num_rows($busca_gci_antes)!=0) {
                                                        $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                        $edad_antes=$datos_gci_antes["hasta_mt"];
                                                        $gci_antes=$datos_gci_antes["gci"];
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $gci_despues=$datos_gci_despues["gci"];
                                                        }

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if (($edad_real_prueba_gci-$edad_antes)<1) {
                                                        $gci=$gci_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $gci=$gci_despues;
                                                                } else {
                                                                $gci=ceil(($gci_antes+$gci_despues)/2);
                                                                }

                                                        }
                                                }

                                        // Busca V
                                        $pdc_item=$total_v;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_v WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                $item_unico=$datos_gci["id"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_v WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                if (db_num_rows($busca_gci_antes)!=0) {
                                                        $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                        $edad_antes=$datos_gci_antes["hasta_mt"];
                                                        $item_antes=$datos_gci_antes["gci"];
                                                        $id_item_antes=$datos_gci_despues["id"];
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_v WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
                                                        $id_item_despues=$datos_gci_despues["id"];
                                                        }

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_vI=$item;
                                        // Busca PM
                                        $pdc_item=$total_pm;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                if (db_num_rows($busca_gci_antes)!=0) {
                                                        $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                        $edad_antes=$datos_gci_antes["hasta_mt"];
                                                        $item_antes=$datos_gci_antes["gci"];
                                                        $id_item_antes=$datos_gci_despues["id"];
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
                                                        $id_item_despues=$datos_gci_despues["id"];
                                                        }

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_pmI=$item;
                                        // Busca N
                                        $pdc_item=$total_n;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                if (db_num_rows($busca_gci_antes)!=0) {
                                                        $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                        $edad_antes=$datos_gci_antes["hasta_mt"];
                                                        $item_antes=$datos_gci_antes["gci"];
                                                        $id_item_antes=$datos_gci_despues["id"];
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
                                                        $id_item_despues=$datos_gci_despues["id"];
                                                        }

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_nI=$item;
                                        // Busca MEM
                                        $pdc_item=$total_mem;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                if (db_num_rows($busca_gci_antes)!=0) {
                                                        $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                        $edad_antes=$datos_gci_antes["hasta_mt"];
                                                        $item_antes=$datos_gci_antes["gci"];
                                                        $id_item_antes=$datos_gci_despues["id"];
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
                                                        $id_item_despues=$datos_gci_despues["id"];
                                                        }

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_memI=$item;

                                        // $muestra="Edad: ".$edad_real_prueba_gci." Antes: ".$item_antes."(".$edad_antes.") Despues: ".$item_despues."(".$edad_despues.") Media:".($item_antes+$item_despues)/2;

                                        // Busca MOT
                                        $pdc_item=$total_mot;
                                        $busca_gci=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE desde_mt<=$edad_real_prueba_gci AND hasta_mt>=$edad_real_prueba_gci AND pdc=$pdc_item");
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                if (db_num_rows($busca_gci_antes)!=0) {
                                                        $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                        $edad_antes=$datos_gci_antes["hasta_mt"];
                                                        $item_antes=$datos_gci_antes["gci"];
                                                        $id_item_antes=$datos_gci_despues["id"];
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
                                                        $id_item_despues=$datos_gci_despues["id"];
                                                        }

                                                if ((db_num_rows($busca_gci_antes)==0) and (db_num_rows($busca_gci_despues)!=0)) {
                                                        $item_antes=$item_despues;
                                                        $edad_antes=$edad_despues;
                                                        }
                                                if ((db_num_rows($busca_gci_antes)!=0) and (db_num_rows($busca_gci_despues)==0)) {
                                                        $item_despues=$item_antes;
                                                        $edad_despues=$edad_antes;
                                                        }

                                                if ($edad_real_prueba_gci-$edad_antes<1) {
                                                        $item=$item_antes;
                                                        } else {
                                                        if (($edad_real_prueba_gci-$edad_despues)<1)  {
                                                                $item=$item_despues;
                                                                } else {
                                                                $item=ceil(($item_antes+$item_despues)/2);
                                                                }

                                                        }
                                                }
                                        $total_motI=$item;
                                        }
        // Busca textos informe

        /*
        
        Puntuaciones
        30<=Muy Bajo
        30-39=Bajo
        40-45=Normal-Bajo
        46-55=Normal
        56-60=Normal-Alto
        61-69=Alto
        70>=Superior
        
        
        */

        // V
        if ($total_vI<=30) {
                $nivel_v="muy bajo";
                }
        if (($total_vI>30) and ($total_vI<=39)){
                $nivel_v="bajo";
                }
        if (($total_vI>39) and ($total_vI<=45)){
                $nivel_v="normal-bajo";
                }
        if (($total_vI>45) and ($total_vI<=55)){
                $nivel_v="normal";
                }
        if (($total_vI>55) and ($total_vI<=60)){
                $nivel_v="normal-alto";
                }
        if (($total_vI>60) and ($total_vI<=69)){
                $nivel_v="alto";
                }
        if ($total_vI>=70) {
                $nivel_v="superior";
                }
        if ($total_vI<46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='V' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $VERBAL=$datos_texto["texto"];
                }
        if ($total_vI>=46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='V' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $VERBAL=$datos_texto["texto"];
                }

        // PM
        if ($total_pmI<=30) {
                $nivel_pm="muy bajo";
                }
        if (($total_pmI>30) and ($total_pmI<=39)){
                $nivel_pm="bajo";
                }
        if (($total_pmI>39) and ($total_pmI<=45)){
                $nivel_pm="normal-bajo";
                }
        if (($total_pmI>45) and ($total_pmI<=55)){
                $nivel_pm="normal";
                }
        if (($total_pmI>55) and ($total_pmI<=60)){
                $nivel_pm="normal-alto";
                }
        if (($total_pmI>60) and ($total_pmI<=69)){
                $nivel_pm="alto";
                }
        if ($total_pmI>=70) {
                $nivel_pm="superior";
                }
        if ($total_pmI<46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='PM' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $PER_MANI=$datos_texto["texto"];
                }
        if ($total_pmI>=46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='PM' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $PER_MANI=$datos_texto["texto"];
                }

        // NUM
        if ($total_nI<=30) {
                $nivel_n="muy bajo";
                }
        if (($total_nI>30) and ($total_nI<=39)){
                $nivel_n="bajo";
                }
        if (($total_nI>39) and ($total_nI<=45)){
                $nivel_n="normal-bajo";
                }
        if (($total_nI>45) and ($total_nI<=55)){
                $nivel_n="normal";
                }
        if (($total_nI>55) and ($total_nI<=60)){
                $nivel_n="normal-alto";
                }
        if (($total_nI>60) and ($total_nI<=69)){
                $nivel_n="alto";
                }
        if ($total_nI>=70) {
                $nivel_n="superior";
                }
        if ($total_nI<46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='NUM' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $NUMERICA=$datos_texto["texto"];
                }
        if ($total_nI>=46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='NUM' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $NUMERICA=$datos_texto["texto"];
                }
                
        // MOT
        if ($total_motI<=30) {
                $nivel_mot="muy bajo";
                }
        if (($total_motI>30) and ($total_motI<=39)){
                $nivel_mot="bajo";
                }
        if (($total_motI>39) and ($total_motI<=45)){
                $nivel_mot="normal-bajo";
                }
        if (($total_motI>45) and ($total_motI<=55)){
                $nivel_mot="normal";
                }
        if (($total_motI>55) and ($total_motI<=60)){
                $nivel_mot="normal-alto";
                }
        if (($total_motI>60) and ($total_motI<=69)){
                $nivel_mot="alto";
                }
        if ($total_motI>=70) {
                $nivel_mot="superior";
                }
        if ($total_motI<46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='MOT' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $MOTRICIDAD=$datos_texto["texto"];
                }
        if ($total_motI>=46) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='MOT' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $MOTRICIDAD=$datos_texto["texto"];
                }
        // MEM
        if ($total_memI<=30) {
                $nivel_mem="muy bajo";
                }
        if (($total_memI>30) and ($total_memI<=39)){
                $nivel_mem="bajo";
                }
        if (($total_memI>39) and ($total_memI<=45)){
                $nivel_mem="normal-bajo";
                }
        if (($total_memI>45) and ($total_memI<=55)){
                $nivel_mem="normal";
                }
        if (($total_memI>55) and ($total_memI<=60)){
                $nivel_mem="normal-alto";
                }
        if (($total_memI>60) and ($total_memI<=69)){
                $nivel_mem="alto";
                }
        if ($total_memI>=70) {
                $nivel_mem="superior";
                }
        // GC
        if ($gci<=69) {
                $nivel_gci="muy bajo";
                $n_gci=ceil($gci/2.379310345);
                }
        if (($gci>69) and ($gci<=79)){
                $nivel_gci="bajo";
                $n_gci=ceil($gci/2.025641026);
                }
        if (($gci>79) and ($gci<=89)){
                $nivel_gci="normal-bajo";
                $n_gci=ceil($gci/1.977777778);
                }
        if (($gci>89) and ($gci<=109)){
                $nivel_gci="normal";
                $n_gci=ceil($gci/1.981818182);
                }
        if (($gci>109) and ($gci<=119)){
                $nivel_gci="normal-alto";
                $n_gci=ceil($gci/1.983333333);
                }
        if (($gci>119) and ($gci<=129)){
                $nivel_gci="alto";
                $n_gci=round($gci/1.869565217,0);
                }
        if ($gci>=130) {
                $nivel_gci="superior";
                $n_gci=ceil($gci/1.857142857);
                }
                
        if ($gci<90) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='GC' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $GEN_CON=$datos_texto["texto"];
                }
        if ($gci>=90) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='GC' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $GEN_CON=$datos_texto["texto"];
                }
                
        // CONCLUSIONES
        if ($gci<90) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='CON' AND valor=0;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONCLUSION=$datos_texto["texto"];
                }
        if (($gci>=90) and ($gci<120)) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='CON' AND valor=1;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONCLUSION=$datos_texto["texto"];
                }
        if ($gci>=120) {
                $busca_texto=db_query("SELECT * FROM pruebas_mcarthy_informe WHERE tipo='CON' AND valor=2;");
                $datos_texto=db_fetch_array($busca_texto);
                $CONCLUSION=$datos_texto["texto"];
                }


        // Abre plantilla
        $nombre_archivo=$web_path."/modelos/modelo_mc$id_centro_alumno.rtf";
        $gestor = fopen($nombre_archivo, "r");
        $cuerpo_documento = fread($gestor, filesize($nombre_archivo));
        fclose($gestor);

        $cuerpo_documento=str_replace("[VERBAL]",$VERBAL,$cuerpo_documento);
        $cuerpo_documento=str_replace("[PER_MANI]",$PER_MANI,$cuerpo_documento);
        $cuerpo_documento=str_replace("[NUMERICA]",$NUMERICA,$cuerpo_documento);
        $cuerpo_documento=str_replace("[MOTRICIDAD]",$MOTRICIDAD,$cuerpo_documento);
        $cuerpo_documento=str_replace("[GEN_CON]",$GEN_CON,$cuerpo_documento);
        $cuerpo_documento=str_replace("[CONCLUSION]",$CONCLUSION,$cuerpo_documento);
        
        $cuerpo_documento=str_replace("[nivel_pm]",$nivel_pm,$cuerpo_documento);

        $cuerpo_documento=str_replace("[nombre]",ucwords(mb_strtolower($usuario_nombre)),$cuerpo_documento);
        $cuerpo_documento=str_replace("[nombre_may]",mb_strtoupper($usuario_nombre),$cuerpo_documento);
        $cuerpo_documento=str_replace("[edad_real]",$edad_real_prueba." MESES Y ".$edad_en_dias_prueba." DÍAS",$cuerpo_documento);


        if ($usuario_sexo=="H") {
                $cuerpo_documento=str_replace("del niño","de la niña",$cuerpo_documento);
                $cuerpo_documento=str_replace("un niño","una niña",$cuerpo_documento);
                $cuerpo_documento=str_replace("está integrado","está integrada",$cuerpo_documento);
                }


        // Inserta gráfica

        // Borra el archivo si existe de antes
        if (file_exists($web_path."/graficos/mc$id_prueba.png")) {
                unlink($web_path."/graficos/mc$id_prueba.png");
                }

        include "class.bargraph.php";

        $g = new BarGraph();
        
        $data = array("Verbal"=>$total_vI,"Perceptivo-Manipulativa"=>$total_pmI,"Numérica"=>$total_nI,"General Cognitiva"=>$n_gci,"Memoria"=>$total_memI,"Motricidad"=>$total_motI);
        $g->SetGraphPadding(15, 10, 10, 20);
        $g->SetGraphAreaHeight(300);
        $g->SetBarPadding(26);
        $g->SetAxisStep(5);
        $g->SetBarData($data);
        $g->SetGraphTitle("");
        $g->Setbar_uppertext(2);
        $g->Setaxis_ocultatexto(0);
        $g->Setaxis_textsize(1.5);


        $g->DrawGraph($web_path."/graficos/mc$id_prueba.png");
        
        $b=fopen($web_path."/graficos/mc$id_prueba.png","rb");

        $imgData=getimagesize($web_path."/graficos/mc$id_prueba.png");

        $newImagePre="{\\*\\shppict{\\pict \\jpegblip \\picw".$imgData[0]." \\pich".$imgData[1]." \\wbmbitspixel24 ";
        while (!feof($b)) {
                $newImage.= fgets($b);
                }
        $hex=bin2hex($newImage);
        $imgDat=$newImagePre.$hex."}}";
        $cuerpo_documento=str_replace("[GRAFICO]",$imgDat,$cuerpo_documento);

        
        // Escribe documento

        // datos aula y alumnos
        $busca_alumnos=db_query_params('SELECT * FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.id_usuario=? AND alumnos.id_curso=? ORDER BY Apellido1, Apellido2, Nombre', 'ss', array($id_usuario_seleccionado, $id_curso_actual));
        $datos_alumnos=db_fetch_array($busca_alumnos);
        $busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?;', 's', array($datos_alumnos["aula"]));
        $datos_aulas=db_fetch_array($busca_aulas);
        $busca_escuelas=db_query_params('SELECT * FROM centros WHERE id_centro=?', 's', array($datos_aulas["id_centro"]));
        $datos_escuelas=db_fetch_array($busca_escuelas);

        $nombre_escuela=$datos_escuelas["nombre_centro"]."-ID".$datos_escuelas["id_centro"];
        $nombre_del_curso="Curso ".$datos_curso_actual["nombre"]."-ID".$id_curso_actual;
        $nombre_aula=$datos_aulas["nombre"]."-ID".$datos_aulas["id_aula"];
        $nombre_alumno=$datos_alumnos["Nombre"]." ".$datos_alumnos["Apellido1"]." ".$datos_alumnos["Apellido2"]."-ID".$datos_alumnos["id_alumno"];
        $carpeta_documentacion="/home/gabinete/$nombre_del_curso/$nombre_escuela/$nombre_aula/$nombre_alumno";

        $nombre_archivo=$carpeta_documentacion."/Informe_McArthy_".$id_prueba."_$fecha_dia-$fecha_mes-$fecha_ano-".date("His").".rtf";
        $gestor_nuevo = fopen($nombre_archivo, "w+");
        fwrite($gestor_nuevo,$cuerpo_documento);
        fclose($gestor_nuevo);

        $dFile = new Download_file($carpeta_documentacion , "Informe_McArthy_".$id_prueba."_$fecha_dia-$fecha_mes-$fecha_ano-".date("His").".rtf");


?>
