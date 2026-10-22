<?php 

function pasa_meses($edad_anios) {

        $resultado=ceil($edad_anios*12);

        return($resultado);
        }

function calcular_mccarthy($id_prueba) {

        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba));
        $datos_prueba=db_fetch_array($busca_datos_prueba);
                
        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($datos_prueba["id_alumno"]));
                
        $datos_usuario=db_fetch_array($consulta_datos_usuario);

        $fecha_nacimiento_alumno=$datos_usuario["fecha_nacimiento"];
                
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

                        while($ejercicios_mcarhty=db_fetch_array($busca_items_mcarhty)) {
                        $edad_actual=$ejercicios_mcarhty["id_ejercicio"];

                        $ejercicio_actual_p=0;

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
                                                }

                                         } else {
                                         if ($parte_actual==0) {
                                                 $parte_actual=$items_mcarthy["parte"];
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
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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
                                        $caso=0;
                                        if (db_num_rows($busca_gci)!=0) {
                                                $datos_gci=db_fetch_array($busca_gci);
                                                $item=$datos_gci["gci"];
                                                $caso=1;
                                                } else {
                                                // Busca el anterior
                                                $busca_gci_antes=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE hasta_mt<=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt DESC");
                                                if (db_num_rows($busca_gci_antes)!=0) {
                                                        $datos_gci_antes=db_fetch_array($busca_gci_antes);
                                                        $edad_antes=$datos_gci_antes["hasta_mt"];
                                                        $item_antes=$datos_gci_antes["gci"];
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_antes["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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

                                $mano_dominante="";
                                if (($total_d>0) and ($total_i==0)) {
                                        $mano_dominante="Dominancia establecida (derecho)";
                                        }
                                if (($total_i>0) and ($total_d==0)) {
                                        $mano_dominante="Dominancia establecida (izquierdo)";
                                        }
                                if (($total_i>0) and ($total_d>0)) {
                                        $mano_dominante="Dominancia no establecida";
                                        }
                                if (($total_i==0) and ($total_d==0)) {
                                        $mano_dominante="No puntuable";
                                        }
                                        
                                $ojo_dominante="No puntuable";
                                if ($lat_11_4D>0) {
                                        $ojo_dominante="Derecho";
                                        }
                                if ($lat_11_4I>0) {
                                        $ojo_dominante="Izquierdo";
                                        }
                                if ($lat_11_4A>0) {
                                        $ojo_dominante="Ambos";
                                        }


                        $resultado="<table border=1 bordercolor='#000000' style='border-collapse: collapse; font-face: Arial; font-size: 10px; text-align: right'>
                        <tr>
                        <td><b>Fecha:</b> ".date("d/m/Y",strtotime($datos_prueba["fecha"]))."</td>
                        <td><b>V</b></td>
                        <td><b>PM</b></td>
                        <td><b>N</b></td>
                        <td><b>Mem</b></td>
                        <td><b>Mot</b></td>
                        <td><b>PGC/GCI</b></td>
                        </tr>
                        <tr>
                        <td><b>PD</b></td>
                        <td>$total_v</td>
                        <td>$total_pm</td>
                        <td>$total_n</td>
                        <td>$total_mem</td>
                        <td>$total_mot</td>
                        <td>".($total_v+$total_pm+$total_n)."</td>
                        </tr>
                        <tr>
                        <td><b>PI</b></td>
                        <td>$total_vI</td>
                        <td>$total_pmI</td>
                        <td>$total_nI</td>
                        <td>$total_memI</td>
                        <td>$total_motI</td>
                        <td>$gci</td>
                        </tr>
                        </table>
                        <font size=1><b>Mano dominante:</b> $mano_dominante<br><b>Ojo:</b> $ojo_dominante</font>";

        return($resultado);
        }

function calcular_brunet($id_prueba) {

        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet WHERE id_prueba=?', 's', array($id_prueba));
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

        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($datos_prueba["id_alumno"]));

        $datos_usuario=db_fetch_array($consulta_datos_usuario);

        $fecha_nacimiento_alumno=$datos_usuario["fecha_nacimiento"];

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
                        $busca_respuesta_item=db_query_params('SELECT * FROM pruebas_brunet_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $items_brunet["id_item"])) or die(db_error());
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
                                $edP=$datosP["m"]."m, ".$datosP["d"]."d";
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

                                $edP=$edLP."m, ".$edLP_2."d";
                                }

                        // Busca resultado C
                        $busca_C=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='C' AND p=$puntosC");
                        if (db_num_rows($busca_C)!=0) {
                                $datosC=db_fetch_array($busca_C);
                                $edC=$datosC["m"]."m, ".$datosC["d"]."d";
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

                                $edC=$edLC."m, ".$edLC_2."d";
                                }

                        // Busca resultado L
                        $busca_L=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='L' AND p=$puntosL");
                        if (db_num_rows($busca_L)!=0) {
                                $datosL=db_fetch_array($busca_L);
                                $edL=$datosL["m"]."m, ".$datosL["d"]."d";
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
                                $edL=$edL."m, ".$edL_2."d";
                                }

                        // Busca resultado S
                        $busca_S=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='S' AND p=$puntosS");
                        if (db_num_rows($busca_S)!=0) {
                                $datosS=db_fetch_array($busca_S);
                                $edS=$datosS["m"]."m, ".$datosS["d"]."d";
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

                                $edS=$edLS."m, ".$edLS_2."d";
                                }

                        $edTotalM=floor($puntosTotal/10);
                        $edTotalD=30*(round(($puntosTotal/10),2)-$edTotalM);

                        $edTotal=$edTotalM."m, ".$edTotalD."d";

                        $resultado="<table border=1 bordercolor='#000000' style='border-collapse: collapse; font-face: Arial; font-size: 10px; text-align: right'>
                        <tr>
                        <td><b>Fecha:</b>".date("d/m/Y",strtotime($datos_prueba["fecha"]))."<br><b>ER:</b> $edad_real_prueba m</td>
                        <td><b>P</b></td>
                        <td><b>C</b></td>
                        <td><b>L</b></td>
                        <td><b>S</b></td>
                        <td><b>Total</b></td>
                        </tr>
                        <tr>
                        <td><b>Pts.</b></td>
                        <td>$puntosP</td>
                        <td>$puntosC</td>
                        <td>$puntosL</td>
                        <td>$puntosS</td>
                        <td>$puntosTotal</td>
                        </tr>
                        <tr>
                        <td><b>E.D.</b></td>
                        <td>$edP</td>
                        <td>$edC</td>
                        <td>$edL</td>
                        <td>$edS</td>
                        <td>$edTotal</td>
                        </tr>
                        <tr>
                        <td><b>C.D.</b></td>
                        <td>".($edLP-$edad_real_prueba)."</td>
                        <td>".($edLC-$edad_real_prueba)."</td>
                        <td>".($edLL-$edad_real_prueba)."</td>
                        <td>".($edLS-$edad_real_prueba)."</td>
                        <td>".($edTotalM-$edad_real_prueba)."</td>
                        </tr>
                        </table>";

        return($resultado);
        }
function calcular_brunet_a($id_prueba) {

        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_brunet_ampliado WHERE id_prueba=?', 's', array($id_prueba));
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

        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($datos_prueba["id_alumno"]));

        $datos_usuario=db_fetch_array($consulta_datos_usuario);

        $fecha_nacimiento_alumno=$datos_usuario["fecha_nacimiento"];

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
                        $busca_respuesta_item=db_query_params('SELECT * FROM pruebas_brunet_ampliado_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $items_brunet["id_item"])) or die(db_error());
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


                        $busca_items_brunet_ampliado=db_query("SELECT * FROM pruebas_brunet_items WHERE num_orden>160");

                        $puntosPA=0;
                        $puntosCA=0;
                        $puntosLA=0;
                        $puntosSA=0;

                        while($items_brunet_ampliado=db_fetch_array($busca_items_brunet_ampliado)) {

                        // Busca respuesta
                        $busca_respuesta_item=db_query_params('SELECT * FROM pruebas_brunet_ampliado_respuestas WHERE id_prueba=? AND id_item=?', 'ss', array($id_prueba, $items_brunet_ampliado["id_item"])) or die(db_error());
                        if (db_num_rows($busca_respuesta_item)!=0) {
                                $datos_respuesta_item=db_fetch_array($busca_respuesta_item);
                                $respuesta_item_actual=$datos_respuesta_item["respuesta"];
                                if ($items_brunet_ampliado["edad"]==36) {
                                        $valor_item_actual=1.5;
                                        }
                                if ($items_brunet_ampliado["edad"]==48) {
                                        $valor_item_actual=3;
                                        }
                                $escala_item_actual=$items_brunet_ampliado["escala"];

                                if ($respuesta_item_actual==1) {

                                        switch($escala_item_actual) {
                                                case 'P';
                                                        $puntosPA = $puntosPA+$valor_item_actual;
                                                        break;
                                                case 'C';
                                                        $puntosCA = $puntosCA+$valor_item_actual;
                                                        break;
                                                case 'L';
                                                        $puntosLA = $puntosLA+$valor_item_actual;
                                                        break;
                                                case 'S';
                                                        $puntosSA = $puntosSA+$valor_item_actual;
                                                        break;
                                                }
                                        }
                                }
                        }

                        $puntosTotalA=($puntosPA+$puntosCA+$puntosLA+$puntosSA)/4;

                        // Busca resultado P
                        $busca_P=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='P' AND p=$puntosP");
                        if (db_num_rows($busca_P)!=0) {
                                $datosP=db_fetch_array($busca_P);

                                $puntosA=$puntosPA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosP["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }

                                $edP=($datosP["m"]+$meses_a)."m, ".($datosP["d"]+$dias_a)."d";
                                $edLP=($datosP["m"]+$meses_a);
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
                                $edLP_1=(($edLPMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edLP=floor($edLP_1);
                                $edLP_2=ceil(($edLP_1-$edLP)*30);

                                $edP=$edLP."m, ".$edLP_2."d";
                                }

                        // Busca resultado C
                        $busca_C=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='C' AND p=$puntosC");
                        if (db_num_rows($busca_C)!=0) {
                                $datosC=db_fetch_array($busca_C);

                                $puntosA=$puntosCA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosC["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }
                                $edC=($datosC["m"]+$meses_a)."m, ".($datosC["d"]+$dias_a)."d";
                                $edLC=($datosC["m"]+$meses_a);
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
                                $edLC_1=$puntosCA+(($edLCMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edLC=floor($edLC_1);
                                $edLC_2=ceil(($edLC_1-$edLC)*30);

                                $edC=$edLC."m, ".$edLC_2."d";
                                }

                        // Busca resultado L
                        $busca_L=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='L' AND p=$puntosL");
                        if (db_num_rows($busca_L)!=0) {
                                $datosL=db_fetch_array($busca_L);

                                $puntosA=$puntosLA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosL["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }

                                $edL=($datosL["m"]+$meses_a)."m, ".($datosL["d"]+$dias_a)."d";
                                $edLL=($datosL["m"]+$meses_a);
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
                                $edL_1=$puntosLA+(($edLMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edL=floor($edL_1);
                                $edL_2=ceil(($edL_1-$edL)*30);
                                $edLL=$edL;
                                $edL=$edL."m, ".$edL_2."d";
                                }

                        // Busca resultado S
                        $busca_S=db_query("SELECT * FROM pruebas_puntuacion_brunet WHERE area='S' AND p=$puntosS");
                        if (db_num_rows($busca_S)!=0) {
                                $datosS=db_fetch_array($busca_S);

                                $puntosA=$puntosSA;
                                if ($puntosA==(floor($puntosA))) {
                                        $meses_a=$puntosA;
                                        $dias_a=0;
                                        } else {
                                        $meses_a=floor($puntosA);
                                        $dias_a=15;
                                        }
                                $dias_total=($datosS["d"]+$dias_a);
                                if ($dias_total>=30) {
                                        $meses_a=$meses_a+1;
                                        $dias_a=$dias_total-$dias_a;
                                        }

                                $edS=($datosS["m"]+$meses_a)."m, ".($datosS["d"]+$dias_a)."d";
                                $edLS=($datosS["m"]+$meses_a);
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
                                $edLS_1=$puntosSA+(($edLSMenos+($dif_Luntos*$valor_Luntos))/30);
                                $edLS=floor($edLS_1);
                                $edLS_2=ceil(($edLS_1-$edLS)*30);

                                $edS=$edLS."m, ".$edLS_2."d";
                                }

                        $meses_total=($puntosTotal/10)+$puntosTotalA;

                        $edTotalM=floor($meses_total);
                        $edTotalD=30*(round(($meses_total),2)-$edTotalM);

                        $edTotal=$edTotalM."m, ".$edTotalD."d";

                        $resultado="<table border=1 bordercolor='#000000' style='border-collapse: collapse; font-face: Arial; font-size: 10px; text-align: right'>
                        <tr>
                        <td><b>Fecha:</b>".date("d/m/Y",strtotime($datos_prueba["fecha"]))."<br><b>ER:</b> $edad_real_prueba m</td>
                        <td><b>P</b></td>
                        <td><b>C</b></td>
                        <td><b>L</b></td>
                        <td><b>S</b></td>
                        <td><b>Total</b></td>
                        </tr>
                        <tr>
                        <td><b>Pts. Brunet</b></td>
                        <td>$puntosP</td>
                        <td>$puntosC</td>
                        <td>$puntosL</td>
                        <td>$puntosS</td>
                        <td>$puntosTotal</td>
                        </tr>
                        <tr>
                        <td><b>M. Brunet A.</b></td>
                        <td>$puntosPA</td>
                        <td>$puntosCA</td>
                        <td>$puntosLA</td>
                        <td>$puntosSA</td>
                        <td>$puntosTotalA</td>
                        </tr>
                        <tr>
                        <td><b>E.D.</b></td>
                        <td>$edP</td>
                        <td>$edC</td>
                        <td>$edL</td>
                        <td>$edS</td>
                        <td>$edTotal</td>
                        </tr>
                        <tr>
                        <td><b>Dif. ED-ER</b></td>
                        <td>".($edLP-$edad_real_prueba)."</td>
                        <td>".($edLC-$edad_real_prueba)."</td>
                        <td>".($edLL-$edad_real_prueba)."</td>
                        <td>".($edLS-$edad_real_prueba)."</td>
                        <td>".($edTotalM-$edad_real_prueba)."</td>
                        </tr>
                        </table>";

        return($resultado);
        }

function conversion_mccarthy($id_prueba) {

        $resultado="";
        $explicacion="";
        $puntuacion_brunet_v=0;
        $puntuacion_brunet_pm=0;
        $puntuacion_brunet_mot=0;
        $item_brunet_v=0;
        $item_brunet_pm=0;
        $item_brunet_mot=0;

        $busca_datos_prueba=db_query_params('SELECT * FROM pruebas_mcarthy WHERE id_prueba=?', 's', array($id_prueba));
        $datos_prueba=db_fetch_array($busca_datos_prueba);

        $consulta_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($datos_prueba["id_alumno"]));

        $datos_usuario=db_fetch_array($consulta_datos_usuario);

        $fecha_nacimiento_alumno=$datos_usuario["fecha_nacimiento"];

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

                        while($ejercicios_mcarhty=db_fetch_array($busca_items_mcarhty)) {
                        $edad_actual=$ejercicios_mcarhty["id_ejercicio"];

                        $ejercicio_actual_p=0;

                                $puntuacion_parte_act[1]=0;
                                $multiplicador_parte_act[1]=1;
                                $puntuacion_parte_act[2]=0;
                                $multiplicador_parte_act[2]=1;
                                $puntuacion_parte_act[3]=0;
                                $multiplicador_parte_act[3]=1;

                                $parte_actual=0;
                                $contador_pregunta=1;
                                $busca_items_mcarthy=db_query("SELECT * FROM pruebas_mcarthy_items WHERE num_ejercicio=$edad_actual ORDER BY parte,orden_ejercicio");
                                
                                $puntuacion_7_1=0;
                                $puntuacion_7_2=0;
                                
                                while($items_mcarthy=db_fetch_array($busca_items_mcarthy)) {

                                $item_actual_p=0;

                                $id_item_actual=$items_mcarthy["id_item"];

                                // Cuenta partes
                                $busca_partes=db_query("SELECT * FROM pruebas_mcarthy_items WHERE num_ejercicio=$edad_actual GROUP BY parte");
                                if (db_num_rows($busca_partes)>1) {

                                        if ($parte_actual!=$items_mcarthy["parte"]) {
                                                $contador_pregunta=1;
                                                $parte_actual=$items_mcarthy["parte"];
                                                }

                                         } else {
                                         if ($parte_actual==0) {
                                                 $parte_actual=$items_mcarthy["parte"];
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


                                        // Busca equivalencias
                                        if ((($edad_real_prueba_gci>=60) and ($edad_actual==8)) or ($edad_actual!=8)) {

                                        $busca_eq=db_query("SELECT * FROM pruebas_mcarthy_conversion1 WHERE ejercicio=$edad_actual") or die(db_error());
                                        if (db_num_rows($busca_eq)==1) {
                                                $datos_eq=db_fetch_array($busca_eq);

                                                $puntuacion_sumada=$puntuacion_parte_act[1]+$puntuacion_parte_act[2]+$puntuacion_parte_act[3];

                                                // $edad_real_prueba_gci

                                                /*
                                                // 8,5
                                                $distancia_ok=abs($datos_eq["8_5"]-$puntuacion_sumada);
                                                $puntuacion_brunet_equivalente="8.5";

                                                // 7,5
                                                $distancia=abs($datos_eq["7_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="7.5";
                                                        }
                                                // 6,5
                                                $distancia=abs($datos_eq["6_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="6.5";
                                                        }
                                                // 5,5
                                                $distancia=abs($datos_eq["5_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="5.5";
                                                        }
                                                */
                                                // 5,5
                                                $distancia_ok=abs($datos_eq["5_5"]-$puntuacion_sumada);
                                                $puntuacion_brunet_equivalente="5.5";
                                                // 5
                                                $distancia=abs($datos_eq["5_0"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="5";
                                                        }
                                                // 4,5
                                                $distancia=abs($datos_eq["4_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="4.5";
                                                        }
                                                // 4
                                                $distancia=abs($datos_eq["4_0"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="4";
                                                        }
                                                // 3,5
                                                $distancia=abs($datos_eq["3_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="3.5";
                                                        }
                                                // 3
                                                $distancia=abs($datos_eq["3_0"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="3";
                                                        }
                                                // 2,5
                                                $distancia=abs($datos_eq["2_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="2.5";
                                                        }

                                                $resultado=$resultado.$edad_actual.". P:".$puntuacion_sumada.", ER:".$edad_real_prueba_gci.", EQ:".$puntuacion_brunet_equivalente." ($distancia_ok)<br>";
                                                // $explicacion=$explicacion.$edad_actual.". Parte: $parte_actual P:".$puntuacion_sumada.", ER:".$edad_real_prueba_gci." ".(12*$puntuacion_brunet_equivalente).", EQ:".$puntuacion_brunet_equivalente." ($distancia_ok) V: ".$ejercicios_mcarhty["v"]." PM: ".$ejercicios_mcarhty["pm"]." Mot: ".$ejercicios_mcarhty["mot"]."<br>";
                                                if ((12*$puntuacion_brunet_equivalente)>($edad_real_prueba_gci+15)) {
                                                        $explicacion=$explicacion."Item: ".$edad_actual.", Parte: 1, ER:".$edad_real_prueba_gci." meses EQ: $puntuacion_brunet_equivalente años (".(12*$puntuacion_brunet_equivalente)." meses).<br>";
                                                        }

                                        if ($ejercicios_mcarhty["v"]>0) { $item_brunet_v++; }
                                        if ($ejercicios_mcarhty["pm"]>0) { $item_brunet_pm++; }
                                        if ($ejercicios_mcarhty["mot"]>0) { $item_brunet_mot++; }
                                        
                                        $puntuacion_brunet_v=$puntuacion_brunet_v+($puntuacion_brunet_equivalente*$ejercicios_mcarhty["v"]);
                                        $puntuacion_brunet_pm=$puntuacion_brunet_pm+($puntuacion_brunet_equivalente*$ejercicios_mcarhty["pm"]);
                                        $puntuacion_brunet_mot=$puntuacion_brunet_mot+($puntuacion_brunet_equivalente*$ejercicios_mcarhty["mot"]);
                                        
                                                }
                                        if (db_num_rows($busca_eq)==2) {

                                                $parte_pp=1;
                                                $p_total_partes=0;
                                                while($datos_eq=db_fetch_array($busca_eq)) {
                                                $puntuacion_sumada=$puntuacion_parte_act[$parte_pp];

                                                // $edad_real_prueba_gci

                                                /*
                                                // 8,5
                                                $distancia_ok=abs($datos_eq["8_5"]-$puntuacion_sumada);
                                                $puntuacion_brunet_equivalente="8.5";

                                                // 7,5
                                                $distancia=abs($datos_eq["7_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="7.5";
                                                        }
                                                // 6,5
                                                $distancia=abs($datos_eq["6_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="6.5";
                                                        }
                                                // 5,5
                                                $distancia=abs($datos_eq["5_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="5.5";
                                                        }
                                                */
                                                // 5,5
                                                $distancia_ok=abs($datos_eq["5_5"]-$puntuacion_sumada);
                                                $puntuacion_brunet_equivalente="5.5";
                                                // 5
                                                $distancia=abs($datos_eq["5_0"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="5";
                                                        }
                                                // 4,5
                                                $distancia=abs($datos_eq["4_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="4.5";
                                                        }
                                                // 4
                                                $distancia=abs($datos_eq["4_0"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="4";
                                                        }
                                                // 3,5
                                                $distancia=abs($datos_eq["3_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="3.5";
                                                        }
                                                // 3
                                                $distancia=abs($datos_eq["3_0"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="3";
                                                        }
                                                // 2,5
                                                $distancia=abs($datos_eq["2_5"]-$puntuacion_sumada);
                                                if ($distancia<=$distancia_ok) {
                                                        $distancia_ok=$distancia;
                                                        $puntuacion_brunet_equivalente="2.5";
                                                        }

                                                $p_total_partes=$p_total_partes+$puntuacion_brunet_equivalente;
                                                // $explicacion=$explicacion.$edad_actual.". Parte: $parte_pp P:".$puntuacion_sumada.", ER:".$edad_real_prueba_gci." ".(12*$puntuacion_brunet_equivalente).", EQ:".$puntuacion_brunet_equivalente." ($distancia_ok) $p_total_partes V: ".$ejercicios_mcarhty["v"]." PM: ".$ejercicios_mcarhty["pm"]." Mot: ".$ejercicios_mcarhty["mot"]."<br>";
                                                if ((12*$puntuacion_brunet_equivalente)>($edad_real_prueba_gci+15)) {
                                                        $explicacion=$explicacion."Item: ".$edad_actual.", Parte: $parte_pp, ER:".$edad_real_prueba_gci." meses EQ: $puntuacion_brunet_equivalente años (".(12*$puntuacion_brunet_equivalente)." meses).<br>";
                                                        }
                                                $parte_pp++;
                                        if ($ejercicios_mcarhty["v"]>0) { $item_brunet_v++; }
                                        if ($ejercicios_mcarhty["pm"]>0) { $item_brunet_pm++; }
                                        if ($ejercicios_mcarhty["mot"]>0) { $item_brunet_mot++; }
                                                        }
                                                $puntuacion_brunet_equivalente=$p_total_partes;
                                                
                                                $puntuacion_brunet_v=$puntuacion_brunet_v+($puntuacion_brunet_equivalente*$ejercicios_mcarhty["v"]);
                                                $puntuacion_brunet_pm=$puntuacion_brunet_pm+($puntuacion_brunet_equivalente*$ejercicios_mcarhty["pm"]);
                                                $puntuacion_brunet_mot=$puntuacion_brunet_mot+($puntuacion_brunet_equivalente*$ejercicios_mcarhty["mot"]);
                                                }

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
                                                        $edad_despues=$datos_gci_despues["desde_mt"];
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
                                                        $id_item_antes=$datos_gci_antes["id"];
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
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_pm WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_despues["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_n WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_despues["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mem WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_despues["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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
                                                        }
                                                // Busca el posterior
                                                $busca_gci_despues=db_query("SELECT * FROM pruebas_mcarthy_puntuacion_mot WHERE desde_mt>=$edad_real_prueba_gci AND pdc=$pdc_item ORDER BY desde_mt");
                                                if (db_num_rows($busca_gci_despues)!=0) {
                                                        $datos_gci_despues=db_fetch_array($busca_gci_despues);
                                                        $edad_despues=$datos_gci_despues["desde_mt"];
                                                        $item_despues=$datos_gci_despues["gci"];
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

                                $mano_dominante="";
                                if (($total_d>0) and ($total_i==0)) {
                                        $mano_dominante="Dominancia establecida (derecho)";
                                        }
                                if (($total_i>0) and ($total_d==0)) {
                                        $mano_dominante="Dominancia establecida (izquierdo)";
                                        }
                                if (($total_i>0) and ($total_d>0)) {
                                        $mano_dominante="Dominancia no establecida";
                                        }
                                if (($total_i==0) and ($total_d==0)) {
                                        $mano_dominante="No puntuable";
                                        }

                                $ojo_dominante="No puntuable";
                                if ($lat_11_4D>0) {
                                        $ojo_dominante="Derecho";
                                        }
                                if ($lat_11_4I>0) {
                                        $ojo_dominante="Izquierdo";
                                        }
                                if ($lat_11_4A>0) {
                                        $ojo_dominante="Ambos";
                                        }

                        if ($item_brunet_v>0) {
                                $promedio_brunet_v=round($puntuacion_brunet_v/$item_brunet_v,1);
                                } else {
                                $promedio_brunet_v=0;
                                }
                        if ($item_brunet_pm>0) {
                                $promedio_brunet_pm=round($puntuacion_brunet_pm/$item_brunet_pm,1);
                                } else {
                                $promedio_brunet_pm=0;
                                }
                        if ($item_brunet_mot>0) {
                                $promedio_brunet_mot=round($puntuacion_brunet_mot/$item_brunet_mot,1);
                                } else {
                                $promedio_brunet_mot=0;
                                }

     $resultado="<table border=1 bordercolor='#000000' style='border-collapse: collapse; font-face: Arial; font-size: 10px; text-align: right'>
                        <tr>
                        <td><b>ER:</b> $edad_real_prueba m</td>
                        <td><b>P</b></td>
                        <td><b>C</b></td>
                        <td><b>L</b></td>
                        <td><b>EM</b></td>
                        </tr>
                        <tr>
                        <td><b>E.D. (Meses)</b></td>
                        <td>".pasa_meses($promedio_brunet_mot)."</td>
                        <td>".pasa_meses($promedio_brunet_pm)."</td>
                        <td>".pasa_meses($promedio_brunet_v)."</td>
                        <td>".ceil(($gci*$edad_real_prueba)/100)."</td>
                        </tr>
                        <tr>
                        <td><b>E.D. (Revisada)</b></td>
                        <td>".(pasa_meses($promedio_brunet_mot)+$datos_prueba["correccion_p"])."</td>
                        <td>".(pasa_meses($promedio_brunet_pm)+$datos_prueba["correccion_c"])."</td>
                        <td>".(pasa_meses($promedio_brunet_v)+$datos_prueba["correccion_l"])."</td>
                        <td>".(ceil(($gci*$edad_real_prueba)/100)+$datos_prueba["correccion_em"])."</td>
                        </tr>
                        </table>";
        if ($explicacion!="") {
                $resultado=$resultado."<br><font color='red' size=2><b>ATENCIÓN:</b><BR>$explicacion</font>";
                }


        return($resultado);
        }

?>
