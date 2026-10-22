<?php 
require("../../configuracion/config.php");
require("../../configuracion/autentifica.php");


$id_alumno=$_POST["id_alumno"];
$trimestre=$_POST["trimestre"];
$id_aula=$_POST["id_aula"];

$trimestre_numeral=$trimestre."er";

if ($trimestre==2) {
        $trimestre_numeral=$trimestre."º";
        }

$busca_aulas=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($id_aula));

if (db_num_rows($busca_aulas)!=0){
        $datos_aula=db_fetch_array($busca_aulas);

        // Datos
        $nombre_aula=$datos_aula["nombre"];
        $id_centro_aula=$datos_aula["id_centro"];
        $id_curso_aula=$datos_aula["id_curso"];        
        
        $ciclo_aula=$datos_aula["ciclo"];
        if ($ciclo_aula==0) {
                $ciclo_aula=1;
                }
        
        // Busca datos curso
                $busca_curso=db_query_params('SELECT * FROM cursos_academicos WHERE id_curso=?', 's', array($id_curso_aula)) or die(db_error());
                
                if (db_num_rows($busca_curso)!=0){
                $datos_curso=db_fetch_array($busca_curso);

                $fecha_inicio_curso=$datos_curso["fecha_inicio"];

                $fecha_ini=$datos_curso["inicio_trim".$trimestre."_".$ciclo_aula];
                $fecha_fin=$datos_curso["fin_trim".$trimestre."_".$ciclo_aula];
                
                }
                
        $evaluacion_por_edades=$datos_aula["evaluacion_por_edades"];          
        $edad_ini_aula=$datos_aula["edad_minima_prog"];  
        $edad_fin_aula=$datos_aula["edad_maxima_prog"];
        
        $mes_inicio_trimestre=date("n",strtotime($fecha_ini));  
        $mes_fin_trimestre=date("n",strtotime($fecha_fin));
                
        }
        
// Busca envaluacion guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre;");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
        $datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    } else {
    $guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_estado (id_curso,id_aula,trimestre) VALUES (?,?,?)', 'sss', array((string)($id_curso_aula), (string)($id_aula), (string)($trimestre)));
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    }        
     
?>
<html>

<head>
<title><?php  echo $titulo_web; ?></title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<style type="text/css">
<?php  require("../../configuracion/estilos/estilo2.php"); ?>
</style>
<script language="javascript">
<!--
self.focus();

function cambiacolor_over(celda){ celda.style.backgroundColor="#ffefa6"; } 
function cambiacolor_out(celda){ celda.style.backgroundColor="#ffffff"; }

var pagina_destino = "guarda_dato_evaluacion_1.php";
var storageKey = "evaluacion_pendiente_<?php echo $id_aula . '_' . $trimestre . '_' . $id_curso_aula; ?>";
var enviandoCola = false;

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    } else {
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        } catch(e){
            alert('Tu navegador no soporta AJAX');
            return false;
        }
    }
}

function obtenerColaLocal(){
    var texto = localStorage.getItem(storageKey);
    if (!texto) return {};
    try {
        return JSON.parse(texto);
    } catch(e) {
        return {};
    }
}

function guardarColaLocal(cola){
    localStorage.setItem(storageKey, JSON.stringify(cola));
    actualizarEstadoPendientes();
}

function borrarElementoCola(clave){
    var cola = obtenerColaLocal();
    if (cola[clave]) {
        delete cola[clave];
        guardarColaLocal(cola);
    }
}

function obtenerClavePayload(payload){
    return payload.tabla + "|" + payload.nombre_id + "|" + payload.fila + "|" + payload.campo;
}

function guardarPayloadEnCola(payload){
    var cola = obtenerColaLocal();
    var clave = obtenerClavePayload(payload);
    cola[clave] = payload;
    guardarColaLocal(cola);
}

function actualizarEstadoPendientes(){
    var cola = obtenerColaLocal();
    var total = Object.keys(cola).length;
    var estado = document.getElementById("estadoGuardado");
    if (!estado) return;

    if (total === 0) {
        estado.className = "label label-success";
        estado.innerHTML = "Todos los cambios están guardados";
    } else {
        estado.className = "label label-warning";
        estado.innerHTML = total + " cambio(s) pendiente(s)";
    }
}

function actualizarEstadoConexion(texto, clase){
    var el = document.getElementById("estadoConexion");
    if (!el) return;
    el.className = "label " + clase;
    el.innerHTML = texto;
}

function construirPayloadDesdeControl(control){
    return {
        texto_largo: control.getAttribute("data-texto_largo") || "0",
        fila: control.getAttribute("data-fila") || "",
        nombre_id: control.getAttribute("data-nombre_id") || "",
        tabla: control.getAttribute("data-tabla") || "",
        campo: control.getAttribute("data-campo") || "",
        valor: control.value
    };
}

function serializarPayload(payload){
    var partes = [];
    for (var clave in payload) {
        if (payload.hasOwnProperty(clave)) {
            partes.push(encodeURIComponent(clave) + "=" + encodeURIComponent(payload[clave]));
        }
    }
    return partes.join("&");
}

function enviarPayload(payload){
    return new Promise(function(resolve, reject){
        var rpc = aj();
        if (!rpc) {
            reject("No se pudo crear la petición AJAX");
            return;
        }

        rpc.open('POST', pagina_destino, true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

        rpc.timeout = 5000; // ? 5 segundos (ajustable)

        rpc.onreadystatechange = function(){
            if (rpc.readyState === 4) {
                if (rpc.status >= 200 && rpc.status < 300) {
                    try {
                        var respuesta = JSON.parse(rpc.responseText);
                        if (respuesta.ok == 1) {
                            resolve(respuesta);
                        } else {
                            reject(respuesta.mensaje || "Error al guardar");
                        }
                    } catch(e) {
                        reject("Respuesta no válida del servidor");
                    }
                } else {
                    reject("No hay conexión con el servidor");
                }
            }
        };

        rpc.onerror = function(){
            reject("No hay conexión con el servidor");
        };

        rpc.ontimeout = function(){
            reject("Tiempo de espera agotado");
        };

        rpc.send(serializarPayload(payload));
    });
}

function comprobarConexionServidor(){
    return new Promise(function(resolve, reject){
        var rpc = aj();
        if (!rpc) {
            reject("No se pudo crear la petición AJAX");
            return;
        }

        rpc.open('POST', pagina_destino, true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        rpc.timeout = 3000; // 3 segundos

        rpc.onreadystatechange = function(){
            if (rpc.readyState === 4) {
                if (rpc.status >= 200 && rpc.status < 300) {
                    try {
                        var respuesta = JSON.parse(rpc.responseText);
                        if (respuesta.ok == 1) {
                            actualizarEstadoConexion("Con conexión", "label-success");
                            resolve(true);
                        } else {
                            actualizarEstadoConexion("Sin conexión", "label-danger");
                            reject(false);
                        }
                    } catch(e) {
                        actualizarEstadoConexion("Error de conexión", "label-danger");
                        reject(false);
                    }
                } else {
                    actualizarEstadoConexion("Sin conexión", "label-danger");
                    reject(false);
                }
            }
        };

        rpc.onerror = function(){
            actualizarEstadoConexion("Sin conexión", "label-danger");
            reject(false);
        };

        rpc.ontimeout = function(){
            actualizarEstadoConexion("Sin conexión", "label-danger");
            reject(false);
        };

        rpc.send("accion=ping");
    });
}

function guardarControl(control){
    var payload = construirPayloadDesdeControl(control);
    var clave = obtenerClavePayload(payload);

    guardarPayloadEnCola(payload);

    control.style.backgroundColor = "#fff3cd";

    enviarPayload(payload).then(function(){
        borrarElementoCola(clave);
        control.style.backgroundColor = "";
    }).catch(function(){
        control.style.backgroundColor = "#f8d7da";
    });
}

function recopilarTodosLosControlesEnCola(){
    var controles = document.querySelectorAll('[data-autosave="1"]');
    for (var i = 0; i < controles.length; i++) {
        var payload = construirPayloadDesdeControl(controles[i]);
        guardarPayloadEnCola(payload);
    }
}

function vaciarColaPendiente(){
    return new Promise(function(resolve, reject){
        if (enviandoCola) {
            reject("Ya se está enviando la cola");
            return;
        }

        enviandoCola = true;

        var cola = obtenerColaLocal();
        var claves = Object.keys(cola);
        var indice = 0;

        function siguiente(){
            if (indice >= claves.length) {
                enviandoCola = false;
                resolve(true);
                return;
            }

            var clave = claves[indice];
            var payload = cola[clave];

            enviarPayload(payload).then(function(){
                borrarElementoCola(clave);

                var control = buscarControlPorPayload(payload);
                if (control) {
                    control.style.backgroundColor = "";
                }

                indice++;
                siguiente();
            }).catch(function(error){
                enviandoCola = false;
                reject(error);
            });
        }

        siguiente();
    });
}

function buscarControlPorPayload(payload){
    var selector = '[data-tabla="' + payload.tabla + '"][data-nombre_id="' + payload.nombre_id + '"][data-fila="' + payload.fila + '"][data-campo="' + payload.campo + '"]';
    return document.querySelector(selector);
}

function guardarTodoPendiente(){
    recopilarTodosLosControlesEnCola();
    actualizarEstadoGuardadoManual("Comprobando conexión con el servidor...", "label-info");

    comprobarConexionServidor().then(function(){
        actualizarEstadoGuardadoManual("Guardando datos pendientes...", "label-info");

        vaciarColaPendiente().then(function(){
            actualizarEstadoGuardadoManual("Todos los datos se han guardado correctamente", "label-success");
            alert("Todos los datos se han guardado correctamente.");
        }).catch(function(){
            actualizarEstadoGuardadoManual("No se han podido guardar todos los datos", "label-danger");
            alert("Hay cambios que no se han podido guardar. Comprueba la conexión wifi, muévete a una zona con cobertura y vuelve a pulsar 'Guardar todo'.");
        });
    }).catch(function(){
        actualizarEstadoGuardadoManual("Sin conexión con el servidor", "label-danger");
        alert("No hay conexión con el servidor.\n\nMuévete a una zona con mejor cobertura wifi y vuelve a pulsar 'Guardar todo'.\nNo cierres esta pantalla para no perder los cambios.");
    });
}

function actualizarEstadoGuardadoManual(texto, clase){
    var estado = document.getElementById("estadoGuardado");
    if (!estado) return;
    estado.className = "label " + clase;
    estado.innerHTML = texto;
}

window.addEventListener("beforeunload", function (e) {
    var cola = obtenerColaLocal();
    if (Object.keys(cola).length > 0) {
        var mensaje = "Hay cambios pendientes de guardar. Si sales ahora, podrías perderlos.";
        e.preventDefault();
        e.returnValue = mensaje;
        return mensaje;
    }
});

window.addEventListener("online", function(){
    actualizarEstadoConexion("Con conexión", "label-success");
});

window.addEventListener("offline", function(){
    actualizarEstadoConexion("Sin conexión", "label-danger");
});

document.addEventListener("DOMContentLoaded", function(){
    actualizarEstadoPendientes();
    comprobarConexionServidor().catch(function(){});
});
//-->
</script>


</head>

<body>
                                <table class="table table-striped">
                                        <tr>
                                        <td><h3>Aula: <?=$nombre_aula?> - Evaluación del <?=$trimestre_numeral?> trimestre. Del  <?=date("d/m/Y",strtotime($fecha_ini))?> al <?=date("d/m/Y",strtotime($fecha_fin))?></h3></td>
                                        </tr>
<tr>
                                        <td><font size=2><b>Visibilidad del informe por parte de los padres: <select 
											name="visibilidad" 
											id="visibilidad"
											data-autosave="1"
											data-texto_largo="0"
											data-fila="<?=$id_estado_evaluacion_guardada?>"
											data-nombre_id="id_estado"
											data-tabla="agenda_programacion_evaluaciones_estado"
											data-campo="estado"
											onchange="guardarControl(this);">
                                        <option value="0"<?php  if ($estado_evaluacion_guardada==0) { print " selected"; } ?>>NO VISIBLE</option>
                                        <option value="1"<?php  if ($estado_evaluacion_guardada==1) { print " selected"; } ?>>VISIBLE</option>
                                        </select></b></font></td>
                                        </tr>
                                </table><br />
								<div class="container-fluid" style="margin-bottom:15px;">
									<div class="row">
										<div class="col-sm-8">
											<button type="button" class="btn btn-success" onclick="guardarTodoPendiente();">
												<i class="fa fa-save"></i> Guardar todo
											</button>
											<span id="estadoGuardado" class="label label-default" style="margin-left:10px;">Sin cambios pendientes</span>
										</div>
										<div class="col-sm-4 text-right">
											<span id="estadoConexion" class="label label-info">Comprobando conexión...</span>
										</div>
									</div>
								</div>								
                                <?php 
                                // Busca objetivos trabajados
                                $contador_areas=0;
                                $contador_total=0;
                                $areas=array("ÁREA I. CONOCIMIENTO DE SÍ MISMO Y AUTONOMIA PERSONAL","ÁREA II. CONOCIMIENTO DEL ENTORNO","ÁREA III. LENGUAJES: COMUNICACIÓN Y REPRESENTACION");
                                for ($c_areas=0;$c_areas<count($areas);$c_areas++) {

                                $array_objetivos_area[$c_areas]=array();
                                $contador_array_obj=0;
                                
                                if ($edad_ini_aula==0) {
                                                // Bebes, solo lo trabajado                                               
                                                $busca_objetivos=db_query_params('SELECT * FROM agenda_programacion LEFT JOIN agenda_programacion_objetivos ON agenda_programacion.objetivo=agenda_programacion_objetivos.id_objetivo WHERE agenda_programacion.id_aula=? AND agenda_programacion.fecha>=? AND agenda_programacion.fecha<=? ORDER BY agenda_programacion_objetivos.mes, agenda_programacion.objetivo;', 'sss', array($id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());
                                        } else {
                                                // Resto, lo programado en el trimestre
                                                // $busca_objetivos=mysql_query("SELECT * FROM agenda_programacion_objetivos WHERE edad>='".$edad_ini_aula."' AND edad<='".$edad_fin_aula."' AND mes>=".$mes_inicio_trimestre." AND mes<=".$mes_fin_trimestre." ORDER BY id_objetivo;") or die(mysql_error());
                                                                                                                                                                                                                                                                												
                                                $consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE edad>='".$edad_ini_aula."' AND edad<='".$edad_fin_aula."' AND ((mes>=".$mes_inicio_trimestre." AND mes<=".$mes_fin_trimestre.") OR (mes=0)) AND editorial=0 ORDER BY id_objetivo;";                                                                                                                                                                                                                												
                                                // Comprueba si la programación debe ser EDEBE
												$nueva_programacion_activa=true;
												if (($fecha_inicio_curso<=strtotime("2019-08-31")) and ($fecha_inicio_curso>=strtotime("2017-09-01")) and (($edad_ini_aula==1) or ($edad_ini_aula==2)) and ($nueva_programacion_activa==true)) {
													
													$editorialProgramacion=1;
													if ($fecha_inicio_curso>=strtotime("2018-09-01")) {
														$editorialProgramacion=2;
														
														// 1 AÑO
														
														
														// Trimestre 1
														if ($trimestre==1) {
															$filtro_meses_programacion="mes=1 OR mes=2 OR mes=3";	
														}
														
														// Trimestre 2
														if ($trimestre==2) {
															$filtro_meses_programacion="mes=4 OR mes=5 OR mes=6";	
														}
														
														// Trimestre 3													
														if ($trimestre==3) {
															$filtro_meses_programacion="mes=7 OR mes=8 OR mes=9";
														}
																				
														if ($edad_ini_aula==2) {
															
															// 2 AÑOS
															
															if ($trimestre==1) {
																$filtro_meses_programacion="mes=1 OR mes=2";
															}
															if ($trimestre==2) {
																$filtro_meses_programacion="mes=3 OR mes=4";
															}
															if ($trimestre==3) {
																$filtro_meses_programacion="mes=5 OR mes=6";
															}								
															
														}
														
													} else {
														
														// 1 AÑO
														
														
														// Trimestre 1
														if ($trimestre==1) {
															$filtro_meses_programacion="mes=1";	
														}
														
														// Trimestre 2
														if ($trimestre==2) {
															$filtro_meses_programacion="mes=2 OR mes=3";	
														}
														
														// Trimestre 3													
														if ($trimestre==3) {
															$filtro_meses_programacion="mes=4 OR mes=5";
														}
																				
														if ($edad_ini_aula==2) {
															
															// 2 AÑOS
															
															if ($trimestre==1) {
																$filtro_meses_programacion="mes=1 OR mes=2";
															}
															if ($trimestre==2) {
																$filtro_meses_programacion="mes=3 OR mes=4";
															}
															if ($trimestre==3) {
																$filtro_meses_programacion="mes=5 OR mes=6";
															}								
															
														}	
														
													}
											 														
													if ($editorialProgramacion==2) {
														
														if ($trimestre==3) {
															// 3er trimestre, todos los objetivos delos trimestres 2º y 3º
															$consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.edad>=$edad_ini_aula AND agenda_programacion_objetivos.edad<=$edad_fin_aula AND agenda_programacion_objetivos.editorial=$editorialProgramacion AND agenda_evaluacion_items.trimestre>1 ORDER BY agenda_programacion_objetivos.id_objetivo";
														} else {
															$consulta_busca_objetivos="SELECT agenda_programacion_objetivos.* FROM agenda_programacion_objetivos,agenda_evaluacion_items WHERE agenda_programacion_objetivos.id_evaluacion=agenda_evaluacion_items.id_evaluacion AND agenda_programacion_objetivos.edad>=$edad_ini_aula AND agenda_programacion_objetivos.edad<=$edad_fin_aula AND agenda_programacion_objetivos.editorial=$editorialProgramacion AND agenda_evaluacion_items.trimestre=".$trimestre." ORDER BY agenda_programacion_objetivos.id_objetivo";	
														}
														
														
													} else {
														$consulta_busca_objetivos="SELECT * FROM agenda_programacion_objetivos WHERE edad>=$edad_ini_aula AND edad<=$edad_fin_aula AND (".$filtro_meses_programacion." OR mes=0) AND editorial=$editorialProgramacion ORDER BY id_objetivo";
													}											
												}
																								
												$busca_objetivos=db_query($consulta_busca_objetivos) or die(db_error());                                                                                                                                                                                                                												
                                                                                                                                                                                                                                                                												
                                    }

									while($cuenta_objetivos=db_fetch_array($busca_objetivos)){
                                                                
										// Objetivo principal
										if ($edad_ini_aula==0) {
											// Bebes
											$id_objetivo1=$cuenta_objetivos["objetivo"];
										} else {
											// Resto
											$id_objetivo1=$cuenta_objetivos["id_objetivo"];
										}

										// Busca area
										$busca_datos_objetivo1=db_query_params('SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo1));
										$datos_objetivos1=db_fetch_array($busca_datos_objetivo1);
                                    
                                    	if ($datos_objetivos1["id_area"]==($c_areas+1)) {

											if (in_array($id_objetivo1, $array_objetivos_area[$c_areas])==false) {
												$array_objetivos_area[$c_areas][$contador_array_obj]=$id_objetivo1;
												$contador_array_obj++;
												$contador_total++;
											}

										}
                                                                                
										// Objetivos secundarios
										$objetivos_secundarios=$cuenta_objetivos["objetivos"];
										if (strlen($objetivos_secundarios)!=0) {
											$objetivos_secundarios=str_replace("[","",$objetivos_secundarios);
											$objetivos_secundarios=str_replace("]","",$objetivos_secundarios);
											$array_objetivos_sec=explode(",",$objetivos_secundarios);
                                                                                
											for ($contador_objetivos_sec=0;$contador_objetivos_sec<count($array_objetivos_sec);$contador_objetivos_sec++){
												// Busca area
												$busca_datos_objetivo2=db_query("SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=".$array_objetivos_sec[$contador_objetivos_sec]);
                                            	$datos_objetivos2=db_fetch_array($busca_datos_objetivo2);

                                            	if ($datos_objetivos2["id_area"]==($c_areas+1)) {
													if (in_array($array_objetivos_sec[$contador_objetivos_sec], $array_objetivos_area[$c_areas])==false) {
														$array_objetivos_area[$c_areas][$contador_array_obj]=$array_objetivos_sec[$contador_objetivos_sec];
														$contador_array_obj++;
														$contador_total++;
													}

												}
											}
										}                                                           
									}
								}
                                 								
                                if ($contador_total==0) {
                                ?>
                                <b>No se han trabajado objetivos durante el periodo seleccionado.</b></font>
                                <?php 
                                } else {
                                
                                $busca_alumnos=db_query_params('SELECT *, usuarios.id_usuario AS id_usuario_inscripcion FROM usuarios,alumnos WHERE usuarios.id_usuario=alumnos.id_usuario AND alumnos.aula=? AND fecha_matricula<=? AND fecha_baja>=? ORDER BY Apellido1, Apellido2, Nombre', 'sss', array($id_aula, (string)(date("Y-m-t", strtotime($fecha_fin))), (string)(date("Y-m-01", strtotime($fecha_ini))))) or die(db_error());

                                if (db_num_rows($busca_alumnos)!=0) {
                                
                                while($listado_alumnos=db_fetch_array($busca_alumnos)){

// Busca envaluacion alumno guardada, si no está la crea
                                                        
$busca_estado_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_estado_alumnos WHERE id_curso=$id_curso_aula AND id_alumno=".$listado_alumnos["id_alumno"]." AND trimestre=$trimestre;");
                                                        
if (db_num_rows($busca_estado_evaluacion_guardada)!=0) {
        $datos_estado_evaluacion_guardada=db_fetch_array($busca_estado_evaluacion_guardada);
    $id_estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["id_estado"];
    $estado_evaluacion_guardada=$datos_estado_evaluacion_guardada["estado"];
    $observaciones_evaluacion_guardada=$datos_estado_evaluacion_guardada["observaciones"];
    } else {
    $guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_estado_alumnos (id_curso,id_alumno,trimestre) VALUES (?,?,?)', 'sss', array((string)($id_curso_aula), (string)($listado_alumnos["id_alumno"]), (string)($trimestre)));
    $id_estado_evaluacion_guardada=db_insert_id();
    $estado_evaluacion_guardada=0;
    }
	
	$contador_items=1;

	switch ($trimestre) {
    case 1:
        $grupo_actual=$listado_alumnos["agenda_grupo"];
        break;
    case 2:
        $grupo_actual=$listado_alumnos["agenda_2_grupo"];
        break;
    case 3:
        $grupo_actual=$listado_alumnos["agenda_3_grupo"];
        break;
	}

	if ($grupo_actual==0) {
			$nombre_grupo=" Grupo: Sin asignar a un grupo";        
		} else {
         	$nombre_grupo=" Grupo: ".$grupo_actual;
		}
                                                                
                                ?>
                                <table class="table table-striped table-hover">
                                        <tr class="info">
                                        <td colspan=3>
											<h4><?=$listado_alumnos["Apellido1"]?> <?=$listado_alumnos["Apellido2"]?>, <?=$listado_alumnos["Nombre"]?>.<?=$nombre_grupo?></h4>
											<button 
											type="button" 
											onclick="marcarTodoSuperado(<?=$listado_alumnos['id_alumno']?>, '<?=addslashes($listado_alumnos['Nombre'])?> <?=addslashes($listado_alumnos['Apellido1'])?> <?=addslashes($listado_alumnos['Apellido2'])?>')">
											Marcar todos los objetivos de <?=$listado_alumnos["Nombre"]?> como superados
											</button>
										</td>
                                        </tr>
                                        <tr>
                                        <td colspan=3><h5>Observaciones</h5>
										<textarea 
											class="form-control" 
											rows="3" 
											name="observaciones<?=$id_estado_evaluacion_guardada?>" 
											id="observaciones<?=$id_estado_evaluacion_guardada?>"
											data-autosave="1"
											data-texto_largo="1"
											data-fila="<?=$id_estado_evaluacion_guardada?>"
											data-nombre_id="id_estado"
											data-tabla="agenda_programacion_evaluaciones_estado_alumnos"
											data-campo="observaciones"
											onblur="guardarControl(this);"><?=utf8_decode($observaciones_evaluacion_guardada)?></textarea>
                                        </tr>
                                        <?php 
                                        
// Limpia tabla temp
$limpia_tabla=db_query_params('DELETE FROM agenda_programacion_objetivos_temp WHERE id_alumno=?', 's', array($listado_alumnos["id_alumno"]));
                                        
for ($c_areas=0;$c_areas<count($areas);$c_areas++) {        

	$num_objetivos_trabajados=count($array_objetivos_area[$c_areas]);
                                        
	if ($edad_ini_aula==0) {
		?>
		<tr>
			<td colspan="3"><h5 class="text-primary"><?=$areas[$c_areas]?></h5></td>
		</tr>
		<?php 
	}
                                       
	$mes_actual=-1;
                                       
	for ($contador_objetivos=1;$contador_objetivos<=$num_objetivos_trabajados;$contador_objetivos++) {

		$id_objetivo=$array_objetivos_area[$c_areas][($contador_objetivos-1)];
	
		$busca_datos_objetivo=db_query_params('SELECT * FROM agenda_programacion_objetivos WHERE id_objetivo=?', 's', array($id_objetivo));
		$datos_objetivos=db_fetch_array($busca_datos_objetivo);                                                                                                                               
                                           
		// Comprueba tipo de evaluación y procede según
		$mostrar_objetivo=true;
		if ($evaluacion_por_edades==1) {
              
        	$edad_alumno_anyos=date("Y",strtotime($fecha_inicio_curso)) - date("Y",strtotime($listado_alumnos["fecha_nacimiento"]));                            
               
            if ($edad_alumno_anyos==$datos_objetivos["edad"]) {
            	$mostrar_objetivo=true;
            } else {
            	$mostrar_objetivo=false;
            }
                                                                                              
		}

		if ($mostrar_objetivo==true) {
                                           
			$porcentaje_autoevaluacion=$datos_objetivos["porcentaje_autoevaluacion"];
                                        
			$num_actividades_programadas=0;
			$num_actividades_trabajadas=0;
			$num_actividades_superadas=0;

			// Busca actividades trabajadas por este niño  
		
			if ($edad_ini_aula==0) {
		
				// Bebes, solo las de su grupo
			
				$busca_actividades=db_query("SELECT * FROM agenda_programacion WHERE (objetivo=".$id_objetivo." OR objetivos like '%[".$id_objetivo."]%') AND (id_aula=".$id_aula." AND fecha>='".$fecha_ini."' AND fecha<='".$fecha_fin."') AND edad=".($grupo_actual-1)." ORDER BY id_actividad ") or die(db_error());
		
			} else {
		
				// Resto de edades, Todas
			
				$busca_actividades=db_query_params('SELECT * FROM agenda_programacion WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_ini), (string)($fecha_fin))) or die(db_error());
			
				// Si no está relacionado con un mes, busca si programado desde el inicio del curso
			
				$busca_actividades_curso=db_query_params('SELECT * FROM agenda_programacion WHERE (objetivo=? OR objetivos like ?) AND (id_aula=? AND fecha>=? AND fecha<=?) ORDER BY id_actividad ', 'sssss', array($id_objetivo, '%[' . (string)($id_objetivo) . ']%', $id_aula, (string)($fecha_inicio_curso), (string)($fecha_fin))) or die(db_error());  
			              
			}                                                                                                                                                                          

			$objetivo_no_programado = "";
			$objetivo_no_programado_curso = false;
		
			if (db_num_rows($busca_actividades)==0) {
				$objetivo_no_programado = " <span class=\"text-danger\">No programado</span>";
			}
			if ($edad_ini_aula!=0) {
				if (db_num_rows($busca_actividades_curso)==0) {
  					$objetivo_no_programado_curso = true;
				}
			}

			while($datos_actividades=db_fetch_array($busca_actividades)) {

				// Busca evaluación
				$busca_evaluacion=db_query_params('SELECT * FROM agenda_programacion_evaluacion_alumnos WHERE id_prog=? AND id_alumno=?', 'ss', array($datos_actividades["id_prog"], $listado_alumnos["id_alumno"]));
                                           
				if (db_num_rows($busca_evaluacion)!=0) {                                       
                                                             
					$datos_evaluacion=db_fetch_array($busca_evaluacion);
                                                
                	$num_actividades_trabajadas=$num_actividades_trabajadas+$datos_evaluacion["asistencia"];
                	$num_actividades_superadas=$num_actividades_superadas+$datos_evaluacion["superada"];

				}
				$num_actividades_programadas++;
			}
		
			$porcentaje_calificacion=0;
			if ($num_actividades_programadas>0) {
				$porcentaje_calificacion=round(($num_actividades_superadas/$num_actividades_programadas)*100,2);
			}

			if ($porcentaje_calificacion>=$porcentaje_autoevaluacion) {
				$calificacion="<font color='#00cc00'>S</font>";
				$superado=1;
			} else {
				$calificacion="<font color='#cc0000'>P</font>";
 				$superado=0;
			}
		
			// Inserta en tabla temp
			// $inserta_fila=mysql_query("INSERT INTO agenda_programacion_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area, texto) VALUES ('$id_objetivo','".$datos_objetivos["id_evaluacion"]."','".$listado_alumnos["id_alumno"]."','$superado','$c_areas', '$objetivo_no_programado');");
                                                
			// Inserta en tabla temp
			if ($datos_objetivos["id_evaluacion"]!=0) {
                                                            
				// Si son bebes, solo lo programado
				$insertar=true;
				if (($edad_ini_aula==0) and ($objetivo_no_programado!="") and ($num_actividades_trabajadas==0)) {
					$insertar=false;
				}
                                                             
				// Si no son bebes, es un objetivo independiente de edad, solo si programado 
				if (($datos_objetivos["mes"]==0) and ($objetivo_no_programado_curso==true)) {
					$insertar=false;
				}
                                                             
				if ($insertar==true) {
					$inserta_fila=db_query_params('INSERT INTO agenda_programacion_objetivos_temp (id_objetivo,id_evaluacion,id_alumno,superado,id_area, texto) VALUES (?,?,?,?,?, ?);', 'ssssss', array((string)($id_objetivo), (string)($datos_objetivos["id_evaluacion"]), (string)($listado_alumnos["id_alumno"]), (string)($superado), (string)($c_areas), (string)($objetivo_no_programado))) or die(db_error()); 
				}                                                                                                                     
			}                                            
		} // Fin Mostrar Objetivo
	} // Fin Bucle objetivos
                                                
	// Busca y muestra
	$busca_evaluaciones=db_query("SELECT id_evaluacion,count(id_objetivo) AS num_objetivos,sum(superado) AS objetivos_superados, texto FROM agenda_programacion_objetivos_temp WHERE id_alumno=".$listado_alumnos["id_alumno"]." AND id_area=$c_areas GROUP BY id_evaluacion ORDER BY id_evaluacion;");
                                             
	while($datos_evaluaciones=db_fetch_array($busca_evaluaciones)) {
		
		if (($datos_evaluaciones["objetivos_superados"]/$datos_evaluaciones["num_objetivos"])>0.5) {
			$calificacion=" <font color='#00cc00'>S</font>";
			$valor_calificacion=1;
		} else {
			$calificacion=" <font color='#cc0000'>P</font>";
			$valor_calificacion=0;
		}
        $valor_calificacion_evaluada = $valor_calificacion;

		// Busca datos envaluacion
		$busca_evaluacion=db_query_params('SELECT * FROM agenda_evaluacion_items WHERE id_evaluacion=?', 's', array($datos_evaluaciones["id_evaluacion"])) or die(db_error());
		$datos_evaluacion=db_fetch_array($busca_evaluacion);
                                                        
		// Busca envaluacion guardada, si no está la crea
                                                        
		$busca_evaluacion_guardada=db_query("SELECT * FROM agenda_programacion_evaluaciones_trimestrales WHERE id_curso=$id_curso_aula AND id_aula=$id_aula AND trimestre=$trimestre AND id_alumno=".$listado_alumnos["id_alumno"]." AND id_evaluacion=".$datos_evaluaciones["id_evaluacion"].";");                           

		if (db_num_rows($busca_evaluacion_guardada)!=0) {
			$datos_evaluacion_guardada=db_fetch_array($busca_evaluacion_guardada);
			$id_evaluacion_guardada=$datos_evaluacion_guardada["id_eval"];
			$valor_calificacion=$datos_evaluacion_guardada["nota"];

			/*
			// Marca toda un aula como superada
			if (($id_aula==1272) and ($trimestre=2)) {
				$actualizaEvaluacion = mysql_query("UPDATE agenda_programacion_evaluaciones_trimestrales SET nota=1 WHERE id_eval=".$id_evaluacion_guardada.";");
			}
			*/

			// Comprueba si se ha superado desde la última vez que se revisó
			if (($valor_calificacion_evaluada==1) and ($valor_calificacion==0)) {
				$valor_calificacion=1;	
				// Actualiza BBDD
				$actualizaEvaluacion = db_query_params('UPDATE agenda_programacion_evaluaciones_trimestrales SET nota=1 WHERE id_eval=?;', 's', array($id_evaluacion_guardada));
			}

		} else {
			$guarda_evaluacion=db_query_params('INSERT INTO agenda_programacion_evaluaciones_trimestrales (id_curso,id_aula,trimestre,id_alumno,id_evaluacion,nota) VALUES (?,?,?,?,?,?)', 'ssssss', array((string)($id_curso_aula), (string)($id_aula), (string)($trimestre), (string)($listado_alumnos["id_alumno"]), (string)($datos_evaluaciones["id_evaluacion"]), (string)($valor_calificacion)));
			$id_evaluacion_guardada=db_insert_id();
		}

	?>
	<tr>
		<td><font size=2 face="<?= $fuente_general; ?>"><?=$contador_items.". ".$datos_evaluacion["descripcion"].$datos_evaluaciones['texto'];?></font></td>
		<td align="center"><font size=2 face="<?= $fuente_general; ?>"><?=$calificacion?></font></td>
  		<td align="center">
			<select 
				name="evaluacion<?=$id_evaluacion_guardada?>" 
				class="objetivo-alumno alumno-<?=$listado_alumnos["id_alumno"]?>" 
				id="evaluacion<?=$id_evaluacion_guardada?>" 
				style="font-size: 10px"
				data-autosave="1"
				data-texto_largo="0"
				data-fila="<?=$id_evaluacion_guardada?>"
				data-nombre_id="id_eval"
				data-tabla="agenda_programacion_evaluaciones_trimestrales"
				data-campo="nota"
				onchange="guardarControl(this);">				
				<option style="color: #00cc00" value="1"<?php  if ($valor_calificacion==1) { print " selected"; } ?>>S</option>
				<option style="color: #cc0000" value="0"<?php  if ($valor_calificacion==0) { print " selected"; } ?>>P</option>
				<option style="color: #0000cc" value="2"<?php  if ($valor_calificacion==2) { print " selected"; } ?>>NT</option>
			</select>
		</td>
	</tr>
	<?php 
		$contador_items++;
	}
} // Fin Bucle areas
?>
</table><br class="saltopagina" />
<?php 
                                        }
                                        }
                                }
                                ?>
                                </font>
<script>
function marcarTodoSuperado(idAlumno, nombreAlumno) {

    const mensaje = 
        "¿Marcar los objetivos del alumno " + nombreAlumno + " como SUPERADOS?\n\n" +
        "ATENCIÓN: Esta acción NO se puede deshacer.\n" +
        "Se borrarán los cambios realizados anteriormente.";

    if (!confirm(mensaje)) {
        return;
    }

    const selects = document.querySelectorAll('.alumno-' + idAlumno);

    selects.forEach(function(select) {
        select.value = "1";
        if (typeof Event === "function") {
            select.dispatchEvent(new Event('change'));
        } else {
            // Compatibilidad antigua
            if (select.fireEvent) {
                select.fireEvent('onchange');
            }
        }
    });
}
</script>
</body>
</html>
<?php 
db_close();
?>
