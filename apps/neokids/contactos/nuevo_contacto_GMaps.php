<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php"); ?>
<?php  require("../configuracion/autentifica_admin.php");

$formulario_name="contactos";
$formulario_accion="nuevo_contacto1.php";

$url_retorno="listado_contactos.php";

$id_contacto=$_GET["id_contacto"];
$estado_inscripcion="";
$info_aula_alumno="";
$sexo_alumno_numero=0;
if (strlen($id_contacto)!=0) {
        $id_contacto_mapa=$id_contacto;
        $busca_datos_contacto=db_query_params('SELECT * FROM contactos WHERE id_contacto=?', 's', array($id_contacto));
        if (db_num_rows($busca_datos_contacto)!=0) {
                $datos_contacto=db_fetch_array($busca_datos_contacto);
                $mensaje="Edición";
                $nombre_alu=$datos_contacto["Nombre"];
                $apellido1_alu=$datos_contacto["Apellido1"];                                               
                $apellido2_alu=$datos_contacto["Apellido2"];
                $persona_contacto=$datos_contacto["persona_contacto"];
                $proceso_decision=$datos_contacto["proceso_decision"];
                $comentarios=$datos_contacto["comentarios"];
                $telefono=$datos_contacto["telefono"];
                $email=$datos_contacto["email"];
                $calle=$datos_contacto["calle"];
                $poblacion=$datos_contacto["poblacion"];
                $codigo_postal=$datos_contacto["codigo_postal"];
                $coordenadas=$datos_contacto["coordenadas"];
                $sexo=$datos_contacto["sexo"];
                $como_conocio=$datos_contacto["como_conocio"];
                $motivo_cancelacion=$datos_contacto["motivo_cancelacion"];
                
                $nombre_alu=str_replace("'", "", $nombre_alu);
                $apellido1_alu=str_replace("'", "", $apellido1_alu);                                               
                $apellido2_alu=str_replace("'", "", $apellido2_alu);
                $persona_contacto=str_replace("'", "", $persona_contacto);
                $proceso_decision=str_replace("'", "", $proceso_decision);
                $comentarios=str_replace("'", "", $comentarios);
                $telefono=str_replace("'", "", $telefono);
                $email=str_replace("'", "", $email);
                $calle=str_replace("'", "", $calle);
                $poblacion=str_replace("'", "", $poblacion);
                $codigo_postal=str_replace("'", "", $codigo_postal);
                $coordenadas=str_replace("'", "", $coordenadas);
                $sexo=str_replace("'", "", $sexo);
                $como_conocio=str_replace("'", "", $como_conocio);
                $motivo_cancelacion=str_replace("'", "", $motivo_cancelacion);                
                
                $no_publicidad=$datos_contacto["no_publicidad"];
                
                $direccion_complera=$datos_contacto["calle"]." ".$datos_contacto["codigo_postal"]." ".$datos_contacto["Poblacion"];
                
                if ($datos_contacto["sexo"]=="H") {
                        $sexo_alumno_numero=1;
                        } else {
                        $sexo_alumno_numero=0;
                        }
                $id_curso_contacto=$datos_contacto["id_curso"];
                $id_centro_contacto=$datos_contacto["id_centro"];
                
                $usuario_nonato=$datos_contacto["nonato"];
                $usuario_prematuro=$datos_contacto["prematuro"];
                
                        $fecha_nacimiento_dia=date("j",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_mes=date("m",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_ano=date("Y",strtotime($datos_contacto["fecha_nacimiento"]));
                        $fecha_nacimiento_dia1=date("d",strtotime($datos_contacto["fecha_nacimiento"]));
                        
                        if (($datos_contacto["fecha_nacimiento"]=="0000-00-00") or ($datos_contacto["fecha_nacimiento"]=="")) {
                                $fecha_nacimiento_dia="";
                                $fecha_nacimiento_mes="";
                                $fecha_nacimiento_ano="";
                                $edad="";
                                } else {
                                $nacimiento=explode( '-', $datos_contacto["fecha_nacimiento"]);
                                $edad=date("Y")-$nacimiento[0];
                                $cumple=mktime(0,0,0,$nacimiento[1],$nacimiento[2],date("Y"));
                                $hoy=strtotime(date("Y/m/d"));
                                if (($cumple-$hoy)>0) {
                                        $edad=$edad-1;
                                        }
                                        if ($edad<2) {
                                                // 1. Comprueba la edad restando el año de nacimiento al año actual
                                                $fecha_l=$datos_contacto["fecha_nacimiento"];
                                                $edad_a_l=(date("Y")-date("Y",strtotime($fecha_l)));

                                                // 1.1. Cacula edad con decimales
                                                $mes_d_l=date("m",strtotime($fecha_l));
                                                $mes_d_n=date("m");
                                                $dif_meses=$mes_d_n-$mes_d_l;

                                                $edad_d_l=($edad_a_l*12)+$dif_meses;
                                                $edad=$edad_d_l." meses";
                                                } else {
                                                $edad=$edad." años";
                                                }
                                }
                        $fecha_informacion_dia=date("j",strtotime($datos_contacto["fecha_informacion"]));
                        $fecha_informacion_mes=date("m",strtotime($datos_contacto["fecha_informacion"]));
                        $fecha_informacion_ano=date("Y",strtotime($datos_contacto["fecha_informacion"]));
                        if (($datos_contacto["fecha_informacion"]=="0000-00-00") or ($datos_contacto["fecha_informacion"]=="")) {
                                $fecha_informacion_dia="";
                                $fecha_informacion_mes="";
                                $fecha_informacion_ano="";
                                }
                        $fecha_b1_dia=date("j",strtotime($datos_contacto["fecha_b1"]));
                        $fecha_b1_mes=date("m",strtotime($datos_contacto["fecha_b1"]));
                        $fecha_b1_ano=date("Y",strtotime($datos_contacto["fecha_b1"]));
                        if (($datos_contacto["fecha_b1"]=="0000-00-00") or ($datos_contacto["fecha_b1"]=="")) {
                                $fecha_b1_dia="";
                                $fecha_b1_mes="";
                                $fecha_b1_ano="";
                                }
                        $fecha_b2_dia=date("j",strtotime($datos_contacto["fecha_b2"]));
                        $fecha_b2_mes=date("m",strtotime($datos_contacto["fecha_b2"]));
                        $fecha_b2_ano=date("Y",strtotime($datos_contacto["fecha_b2"]));
                        if (($datos_contacto["fecha_b2"]=="0000-00-00") or ($datos_contacto["fecha_b2"]=="")) {
                                $fecha_b2_dia="";
                                $fecha_b2_mes="";
                                $fecha_b2_ano="";
                                }
                        $fecha_b3_dia=date("j",strtotime($datos_contacto["fecha_b3"]));
                        $fecha_b3_mes=date("m",strtotime($datos_contacto["fecha_b3"]));
                        $fecha_b3_ano=date("Y",strtotime($datos_contacto["fecha_b3"]));
                        if (($datos_contacto["fecha_b3"]=="0000-00-00") or ($datos_contacto["fecha_b3"]=="")) {
                                $fecha_b3_dia="";
                                $fecha_b3_mes="";
                                $fecha_b3_ano="";
                                }
                        $fecha_c_dia=date("j",strtotime($datos_contacto["fecha_c"]));
                        $fecha_c_mes=date("m",strtotime($datos_contacto["fecha_c"]));
                        $fecha_c_ano=date("Y",strtotime($datos_contacto["fecha_c"]));
                        if (($datos_contacto["fecha_c"]=="0000-00-00") or ($datos_contacto["fecha_c"]=="")) {
                                $fecha_c_dia="";
                                $fecha_c_mes="";
                                $fecha_c_ano="";
                                }

				// Datos ficha inscripcion 2015
				
				$tipo_alumno=$datos_contacto["tipo_alumno"];
				
				$horarios_quiere_asistir=$datos_contacto["horarios_quiere_asistir"];
				$dias_quiere_asistir=$datos_contacto["dias_quiere_asistir"];
				$antiguo_alumno_lapices=$datos_contacto["antiguo_alumno_lapices"];
				
				$dni_padre1=$datos_contacto["dni_padre1"];
				$nombre_padre2=$datos_contacto["nombre_padre2"];
				$telefono_padre2=$datos_contacto["telefono_padre2"];
				$email_padre2=$datos_contacto["email_padre2"];
				$dni_padre2=$datos_contacto["dni_padre2"];
				
				$parentesco_padre1=$datos_contacto["parentesco_padre1"];
				$parentesco_padre2=$datos_contacto["parentesco_padre2"];
				
				$telefonos_familia=$datos_contacto["telefonos_familia"];
				$email_familia=$datos_contacto["email_familia"];
				
				$contacto_emergencia1=$datos_contacto["contacto_emergencia1"];
				$telefono_contacto_emergencia1=$datos_contacto["telefono_contacto_emergencia1"];
				$contacto_emergencia2=$datos_contacto["contacto_emergencia2"];
				$telefono_contacto_emergencia2=$datos_contacto["telefono_contacto_emergencia2"];
				
				$persona_autorizada=$datos_contacto["persona_autorizada"];
				
				$dni_padre1=str_replace("'", "", $dni_padre1);
				$nombre_padre2=str_replace("'", "", $nombre_padre2);
				$telefono_padre2=str_replace("'", "", $telefono_padre2);
				$email_padre2=str_replace("'", "", $email_padre2);
				$dni_padre2=str_replace("'", "", $dni_padre2);
				
				$parentesco_padre1=str_replace("'", "", $parentesco_padre1);
				$parentesco_padre2=str_replace("'", "", $parentesco_padre2);
				
				$telefonos_familia=str_replace("'", "", $telefonos_familia);
				$email_familia=str_replace("'", "", $email_familia);
				
				$contacto_emergencia1=str_replace("'", "", $contacto_emergencia1);
				$telefono_contacto_emergencia1=str_replace("'", "", $telefono_contacto_emergencia1);
				$contacto_emergencia2=str_replace("'", "", $contacto_emergencia2);
				$telefono_contacto_emergencia2=str_replace("'", "", $telefono_contacto_emergencia2);
				
				$persona_autorizada=str_replace("'", "", $persona_autorizada);				
				
				$semanas_embarazo=$datos_contacto["semanas_embarazo"];
								
				// Fin datos nuevos

                $nuevo_alumno=$datos_contacto["nuevo_alumno"];                                                
                $id_usuario=$datos_contacto["id_usuario"];
                                                             
                if (intval($id_usuario)!=0) {
                        $busca_datos_usuario=db_query_params('SELECT * FROM usuarios WHERE id_usuario=?', 's', array($id_usuario));
                        if (db_num_rows($busca_datos_usuario)!=0) {
                                $datos_usuario=db_fetch_array($busca_datos_usuario);
                                $texto_id_usuario="($id_usuario) ".$datos_usuario["Nombre"]." ".$datos_usuario["Apellido1"]." ".$datos_usuario["Apellido2"];
                                
                                // Comprueba si el alumno está inscrito en el curso seleccionado
                                $busca_inscripcion_curso_actual=db_query_params('SELECT * FROM alumnos WHERE id_curso=? AND id_centro=? AND id_usuario=?', 'sss', array($id_curso_contacto, $id_centro_contacto, $id_usuario));
                                if (db_num_rows($busca_inscripcion_curso_actual)!=0) {
                                        $datos_inscripcion_curso_actual=db_fetch_array($busca_inscripcion_curso_actual);
                                        $estado_inscripcion=" disabled";
                                        $estado_inscripcion_espera=" disabled";
                                        $busca_datos_aula=db_query_params('SELECT * FROM centro_aulas WHERE id_aula=?', 's', array($datos_inscripcion_curso_actual["aula"]));
                                        $datos_aula=db_fetch_array($busca_datos_aula);
                                        $info_aula_alumno="<br>Alumno inscrito en el centro seleccionado en el aula: ".$datos_aula["nombre"];
                                        }
                                // Comprueba si el alumno está inscrito en lista de espera
                                $busca_inscripcion_espera_curso_actual=db_query_params('SELECT * FROM lista_espera WHERE id_curso=? AND id_centro=? AND id_usuario=?', 'sss', array($id_curso_contacto, $id_centro_contacto, $id_usuario));
                                if (db_num_rows($busca_inscripcion_espera_curso_actual)!=0) {
                                        $estado_inscripcion_espera=" disabled";
                                        }

                                } else {
                                $texto_id_usuario="($id_usuario) Usuario Desconocido";
                                }
                        } else {
                        $texto_id_usuario="";
                        }
                 $estado_boton="";
                } else {
                $id_contacto="";
                $mensaje="Nuevo";
                $estado_boton=" disabled";
                }
        } else {
                $id_contacto="";
                $id_contacto_mapa=0;
                $mensaje="Nuevo";
                $estado_boton=" disabled";
        }


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<title><?php  echo $titulo_web ; ?></title>
<?php  require("../configuracion/estilos/estilo1.php"); ?>
<?php  require("../configuracion/scripts/scripts.php"); ?>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=ES"></script>
<script type="text/javascript">
  var geocoder;
  var map;
  var errores = "";
  var id_usuario=<?=$id_contacto_mapa?>;
  var query = "<?=$direccion_complera?>";

//acá cada uno crea el objeto a su manera

var pagina_destino="http://gestion.neo-kids.es/contactos/guardar_coordenadas_contacto.php";

function aj(){
    if(window.XMLHttpRequest){
        return new XMLHttpRequest();
    }
    else{
        try{
            return new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
            alert('tu navegador no soporta ajax');
            return false;
        }
    }
}
function sendAndLoad(){
    var rpc=aj();
    if(rpc){
        //si pudo crearse el objeto, avanzamos (respetar el orden)
        rpc.open('POST',pagina_destino,true);
        rpc.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //preparamos las variables a enviar
        var aEnviar='';

        for(ii=0; ii<arguments.length;ii++){
        //usamos el objeto arguments para obtener y parsear los datos a enviar
        //encodeURI es semejante a urlencode de PHP
            aEnviar+=arguments[ii].desc+'='+encodeURI(arguments[ii].dato)+'&';
        }

        //Recién ahora, que tenemos todo lo anterior terminado, podemos enviar el request
        rpc.send(aEnviar);

    }
}

function guarda_dato(coordenadas) {

        if (id_usuario!=0) {
                sendAndLoad({desc:'id_usuario',dato:id_usuario},{desc:'coordenadas',dato:coordenadas});
                // window.open(pagina_destino+'?id_usuario='+id_usuario+'&coordenadas='+coordenadas);
                }

        document.<?=$formulario_name?>.coordenadas.value=coordenadas;

        }

</script>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto" onload="initialize();">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="<?= $ancho_pagina_total; ?>" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu.gif">
  <tr>
    <td colspan="2">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=1 height=10></td>
    </td>
  </tr>
  <tr>
    <td style="border-left: medium none #111111; border-right-style: none; border-right-width: medium; border-top-style: none; border-top-width: medium; border-bottom: medium none #111111">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif"></td>
    <td width="<?= $ancho_pagina_total-185; ?>" style="border-style: none; border-width: medium" align="right">
        <img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=155 height=1 align="left">
        <?php  require("../configuracion/inc/login_sup.php"); ?>
        <br>
    </td>
  </tr>
  <tr>
    <td width="30"><img border="0" src="<?php  echo $url_root ; ?>/imagen/nada.gif" width=30 height=600></td>
    <td width="<?= $ancho_pagina_total-40; ?>" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">

<script type="text/javascript" src="../../inc/validacion_fechas.js"></script>
<script language="Javascript">

  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom:15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    if ((id_usuario!=0)&&(document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
            codeAddress();
            }
  }

  function codeAddress() {
    var address = query;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        guarda_dato(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        guarda_dato("NO LOCALIZADO");
        // alert("No se ha encontrado la dirección en el mapa: " + status + " " + consulta);
        // errores = errores+"No se ha encontrado la dirección en el mapa: " + status + "Dirección: " + consulta + "<br>";
      }
    });
  }

  function localiza() {

        query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                codeAddress();
                } else {
                alert("Escribe antes la dirección: Cale, Número, Código postal y población");
                }

        }

function comprobar_y_enviar() {

                var errores=0;

                var mensajes_error="";
                
                if (self.document.<?=$formulario_name;?>.persona_contacto.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre de la persona de contacto.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono.value=="")&&(self.document.<?=$formulario_name;?>.email.value=="")) { mensajes_error=mensajes_error+"Hay que introducir el teléfono o el e-mail de la persona de contacto.\u000A";errores=1; }
                
                query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                        codeAddress();
                        } else {
                        // mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                        }
                
                if (errores==0) {
                         document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("Hay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
                        
        }
        
function selecciona_fecha(nombre_campo_presel) {
                // Comprueba fecha

                mes_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_mes.value;");
                dia_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_dia.value;");
                ano_presel=eval("self.document.<?=$formulario_name;?>."+nombre_campo_presel+"_ano.value;");

                la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                if (la_fecha!="--/--/----") {
                        la_fecha=mes_presel+"/"+dia_presel+"/"+ano_presel;
                        if (isDate(la_fecha)!=true) {
                                fecha_preseleccionada="";
                                } else {
                                fecha_preseleccionada=ano_presel+"/"+mes_presel+"/"+dia_presel;
                                }
                        } else {
                        fecha_preseleccionada="";
                        }

         window.open('/herramientas/calendario_mes.php?dia='+fecha_preseleccionada+'&formulario_destino=<?=$formulario_name;?>&campo_destino='+nombre_campo_presel,'calendario','toolbar=no,resizable=no,width=200,height=216');
        }
function nueva_nota() {
        window.open('nueva_nota.php?id_contacto=<?=$id_contacto;?>','editar','toolbar=no,resizable=no,width=500,height=210');
        }
function editar_nota(fila) {
        if (fila!="") {
                window.open('nueva_nota.php?id_contacto=<?=$id_contacto;?>&u='+fila,'editar','toolbar=no,resizable=no,width=500,height=210');
                } else {
                alert("Primero debes seleccionar la nota que deseas editar");
                }
        }
function borrar_nota(id_borrar,nombre_mostrar) {
        if (confirm("¿Deseas confirmar la eliminación de la nota?")) {
                document.<?=$formulario_name;?>.id_quitar_nota.value=id_borrar;
                document.<?=$formulario_name;?>.retorno.value=1;
                document.<?=$formulario_name;?>.submit();
                }
        }
function borrar_alumno() {
        if (confirm("Se va a borrar la relación entre este contacto y el alumno seleccionado.\n\nSi no se han guardado los datos, se restaurará la información que había antes de crear la relación (nombre, fecha de nacimiento y sexo del alumno)\n\n¿Continuar con la eliminación de la relación?")) {
                document.<?=$formulario_name;?>.id_usuario.value="";
                document.<?=$formulario_name;?>.texto_id_usuario.value="";
                document.<?=$formulario_name;?>.nombre.value='<?=$nombre_alu;?>';
                document.<?=$formulario_name;?>.apellido1.value='<?=$apellido1_alu;?>';
                document.<?=$formulario_name;?>.apellido2.value='<?=$apellido2_alu;?>';
                document.<?=$formulario_name;?>.fecha_nacimiento_dia.value='<?=$fecha_nacimiento_dia1;?>';
                document.<?=$formulario_name;?>.fecha_nacimiento_mes.value='<?=$fecha_nacimiento_mes;?>';
                document.<?=$formulario_name;?>.fecha_nacimiento_ano.value='<?=$fecha_nacimiento_ano;?>';
                document.<?=$formulario_name;?>.sexo[<?=$sexo_alumno_numero;?>].click();
                }
        }
function copiar_alumno() {

	if (document.<?=$formulario_name;?>.id_usuario.value!="") {
		
		if (confirm("Se van a copiar los datos de este formulario en la ficha del alumno.\n\n¿Estás seguro/a?")) {
		
			window.location.href="/contactos/copiar_datos.php?id_contacto=<?=$id_contacto;?>&id_alumno=" + document.getElementById("id_usuario").value;
		
		}
	
	} else {
	
		alert("El contacto no está relacionado con ningún alumno actual. Puedes relacionarlos pulsando el botón 'Buscar alumno'");
	
	}

}        

        
function buscar_alumno() {
        window.open('seleccionar_alumno.php','nuevo','scrollbars=yes,toolbar=no,resizable=no,width=600,height=500');
        }
        
function buscar_alumno_lapices(escuela) {
        window.open('seleccionar_alumno_'+escuela+'.php','nuevo','scrollbars=yes,toolbar=no,resizable=no,width=600,height=500');
        }
                
function inscribir_alumno() {
                var errores=0;

                var mensajes_error="";

                if ((self.document.<?=$formulario_name;?>.id_centro_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_centro_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar la escuela donde se inscribe.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.id_curso_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_curso_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar el curso en el que se inscribe.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.nombre.value=="") { mensajes_error=mensajes_error+"Hay que escribir el nombre del alumno.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.apellido1.value=="") { mensajes_error=mensajes_error+"Hay escribir el primer apellido del alumno.\u000A";errores=1; }
                
                // Comprueba fecha
                if (self.document.<?=$formulario_name;?>.nonato.checked==false) {
                        la_fecha=self.document.<?=$formulario_name;?>.fecha_nacimiento_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de nacimiento introducida no es correcta.\u000A";errores=1; }
                        }
                
                if (self.document.<?=$formulario_name;?>.persona_contacto.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre de la persona de contacto.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono.value=="")&&(self.document.<?=$formulario_name;?>.email.value=="")) { mensajes_error=mensajes_error+"Hay que introducir el teléfono o el e-mail de la persona de contacto.\u000A";errores=1; }

                query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                        codeAddress();
                        } else {
                        // mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                        }

                if (errores==0) {
                        document.<?=$formulario_name;?>.action="inscribir_contacto.php";
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("No se puede realizar la inscripción con los datos proporcionados.\n\nHay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }
        
function inscribir_espera() {
                var errores=0;

                var mensajes_error="";

                if ((self.document.<?=$formulario_name;?>.id_centro_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_centro_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar la escuela donde se inscribe.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.id_curso_contacto.value=="")||(self.document.<?=$formulario_name;?>.id_curso_contacto.value==0)) { mensajes_error=mensajes_error+"Hay que seleccionar el curso en el que se inscribe.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.nombre.value=="") { mensajes_error=mensajes_error+"Hay que escribir el nombre del alumno.\u000A";errores=1; }
                if (self.document.<?=$formulario_name;?>.apellido1.value=="") { mensajes_error=mensajes_error+"Hay escribir el primer apellido del alumno.\u000A";errores=1; }

                // Comprueba fecha
                if (self.document.<?=$formulario_name;?>.nonato.checked==false) {
                        la_fecha=self.document.<?=$formulario_name;?>.fecha_nacimiento_mes.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_dia.value+"/"+self.document.<?=$formulario_name;?>.fecha_nacimiento_ano.value
                        if (isDate(la_fecha)!=true) { mensajes_error=mensajes_error+"La fecha de nacimiento introducida no es correcta.\u000A";errores=1; }
                        }
                        
                if (self.document.<?=$formulario_name;?>.persona_contacto.value=="") { mensajes_error=mensajes_error+"Hay que introducir el nombre de la persona de contacto.\u000A";errores=1; }
                if ((self.document.<?=$formulario_name;?>.telefono.value=="")&&(self.document.<?=$formulario_name;?>.email.value=="")) { mensajes_error=mensajes_error+"Hay que introducir el teléfono o el e-mail de la persona de contacto.\u000A";errores=1; }

                query=document.<?=$formulario_name?>.calle.value+" "+document.<?=$formulario_name?>.codigo_postal.value+" "+document.<?=$formulario_name?>.poblacion.value;
                if ((document.<?=$formulario_name?>.calle.value!="")&&(document.<?=$formulario_name?>.codigo_postal.value!="")&&(document.<?=$formulario_name?>.poblacion.value!="")) {
                        codeAddress();
                        } else {
                        // mensajes_error=mensajes_error+"Hay que introducir la dirección completa: Calle, Número, Código postal y Población.\u000A";errores=1;
                        }

                if (errores==0) {
                        document.<?=$formulario_name;?>.action="inscribir_contacto_espera.php";
                        document.<?=$formulario_name;?>.submit();
                        } else {
                        alert("No se puede realizar la inscripción con los datos proporcionados.\n\nHay que revisar los siguientes errores:\u000A\u000A"+mensajes_error+"\u000A\u000A")
                        }
        }
        
</script>
<br><br>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<form name="<?=$formulario_name;?>" action="<?=$formulario_accion;?>" method="POST">
<input type="hidden" name="id_contacto" value="<?=$id_contacto;?>">
<input type="hidden" name="formulario_enviado" value="1">
<input type="hidden" name="retorno" value="0">
<input type="hidden" name="id_quitar_nota" value="">
<input type="hidden" name="coordenadas" value="<?=$coordenadas?>">
        <tr>
                <td width="100%" valign="top" bgcolor="<?= $fondo_at; ?>" colspan=3 align="center">
                <br><font size=2 face="<?= $fuente_general; ?>"><b><?=$mensaje;?> Contacto</b><br><br></font>
                        <input type="button" value="Presupuesto" onclick="window.location.href='nuevo_presupuesto.php?id_contacto=<?=$id_contacto?>'" style="font-size: 10px"<?=$estado_boton;?>> <br>
<input type="button" value="Obtener ficha de inscripción Infantil/bebes" onclick="window.location.href='/formulario/imprimir_contacto.php?id_contacto=<?=$id_contacto?>'" style="font-size: 10px"<?=$estado_boton;?>> 
<input type="button" value="Obtener ficha de inscripción Aquagym" onclick="window.location.href='/formulario/imprimir_contacto.php?id_contacto=<?=$id_contacto?>&id_plantilla=1'" style="font-size: 10px"<?=$estado_boton;?>>
<input type="button" value="Obtener ficha de inscripción Matronatación" onclick="window.location.href='/formulario/imprimir_contacto.php?id_contacto=<?=$id_contacto?>&id_plantilla=2'" style="font-size: 10px"<?=$estado_boton;?>><br><br>
                        <table bgcolor="#FFFFFF" border=1 bordercolor="#cdcdcd" style="border-collapse: collapse" cellspacing=0 cellpadding=2 width="100%">
                        <tr valign="top" bgcolor="#efefef">
                        <td colspan=2><font size=1 face="<?= $fuente_general; ?>">&nbsp;</FONT></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Escuela donde asistirá:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="id_centro_contacto" size=1 style="font-size: 10px">
                        <option value="0">Sin Escuela Seleccionada</option>
                        <?php 
                        $consulta_centros=db_query_params('SELECT * FROM centros WHERE id_centro=0 OR id_centro=? ORDER BY nombre_centro;', 's', array($centro_actual));
                        while($centros=db_fetch_array($consulta_centros)){
                        ?>
                        <option value="<?=$centros["id_centro"];?>"<?php  if ($centros["id_centro"]==$id_centro_contacto) { print " selected"; } ?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                        <?php 
                        }
                        ?>
                        </select><?=$info_aula_alumno?></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Curso en el que se inscribirá:</b></font></td>
                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><select name="id_curso_contacto" size=1 style="font-size: 10px">
                        <option value="0">Sin Curso Seleccionado</option>
                        <?php 
                        // Comprueba matrículas
                        // Estados 0=No iniciado, 1=Iniciado, 2=Futuros usos, 3=Finalizado
                        $consulta_cursos=db_query("SELECT * FROM cursos_academicos WHERE estado<3 ORDER BY fecha_inicio") or die(db_error()."-4");
                        while($cursos=db_fetch_array($consulta_cursos)){
                        ?>
                        <option value="<?=$cursos["id_curso"];?>"<?php  if ($cursos["id_curso"]==$id_curso_contacto) { print " selected"; } ?>><?=$cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select> <input type="button"<?=$estado_inscripcion;?> value="Inscribir" onclick="inscribir_alumno()" style="font-size: 10px"> <input type="button" value="Inscribir lista espera" onclick="inscribir_espera()" style="font-size: 10px"></td>
                        </tr>
						<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Tipo de contacto:</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <select name="tipo_alumno" size="1" style="font-size: 10px">
                                <option value="0"<?php  if ($tipo_alumno==0) { print " selected"; } ?>>Bebes/Infantil</option>
                                <option value="1"<?php  if ($tipo_alumno==1) { print " selected"; } ?>>Aquagym</option>
                                <option value="2"<?php  if ($tipo_alumno==2) { print " selected"; } ?>>Matronatación</option>
                                </select></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Horario deseado:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="horarios_quiere_asistir" cols="77" rows=2 style="font-family: Arial; font-size: 11px"><?=$horarios_quiere_asistir;?></textarea></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Días de la semana en que desea asistir:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="dias_quiere_asistir" cols="77" rows=2 style="font-family: Arial; font-size: 11px"><?=$dias_quiere_asistir;?></textarea></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Semanas embarazo (solo matronatación):</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select id="semanas_embarazo" name="semanas_embarazo" size="1" style="font-size: 10px">
                        <option value="0"></option>
                        <?php 
                        for($semanas=1;$semanas<=40;$semanas++) {
                        ?>
                        <option value="<?=$semanas?>"<?php  if ($semanas_embarazo==$semanas) { print " selected"; } ?>><?=$semanas?></option>
                        <?php 
                        }
                        ?>
            	</select></font></td>
                        </tr>
                        
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Nuevo alumno?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="nuevo_alumno" value="1"<?php  if ($nuevo_alumno==1) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="nuevo_alumno" value="0"<?php  if ($nuevo_alumno==0) { print " checked"; } ?>> No</font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Antiguo alumno Lápices?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="antiguo_alumno_lapices" value="1"<?php  if ($antiguo_alumno_lapices==1) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="antiguo_alumno_lapices" value="0"<?php  if ($antiguo_alumno_lapices==0) { print " checked"; } ?>> No</font><br /><br /><input type="button" onclick="buscar_alumno_lapices('lapices')" value="Buscar datos alumno Lápices" style="font-size: 10px"> <input type="button" onclick="buscar_alumno_lapices('santpau')" value="Buscar datos alumno Sant Pau" style="font-size: 10px"></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Cómo nos ha conocido?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <select name="como_conocio" size="1" style="font-size: 10px">
                                <?php 
                                $busca_tipos=db_query("SELECT * FROM contactos_tipo ORDER BY id_tipo;");
                                while($datos_tipos=db_fetch_array($busca_tipos)) {
                                ?>
                                <option value="<?=$datos_tipos["id_tipo"]?>"<?php  if ($como_conocio==$datos_tipos["id_tipo"]) { print " selected"; } ?>><?=$datos_tipos["nombre_tipo"]?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
<tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Fecha prevista información</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_informacion_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_informacion_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_informacion_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_informacion_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_informacion_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_informacion_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_informacion');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr valign="top" bgcolor="#ffefef">
                        <td colspan=2 align="center"><font size=1 face="<?= $fuente_general; ?>"><b>Rellenar el caso de que el alumno no llegue a matricularse en la escuela. Indicar el motivo.</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#ffefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Motivo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <select name="motivo_cancelacion" size="1" style="font-size: 10px">
                                <option value="0"></option>
                                <?php 
                                $busca_tipos=db_query("SELECT * FROM contactos_motivos_cancelacion ORDER BY nombre_motivo;");
                                while($datos_tipos=db_fetch_array($busca_tipos)) {
                                ?>
                                <option value="<?=$datos_tipos["id_motivo"]?>"<?php  if ($motivo_cancelacion==$datos_tipos["id_motivo"]) { print " selected"; } ?>><?=urldecode($datos_tipos["nombre_motivo"]);?></option>
                                <?php 
                                }
                                ?>
                                </select></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="center" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Si ya disponemos de sus datos (por que se trata de un alumno de otros cursos o bien porque ya lo hemos inscrito) debemos seleccionarlos para evitar duplicar alumnos en la base de datos.</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Datos alumno:</b></font></td>
                                <input type="hidden" name="id_usuario" id="id_usuario" value="<?=$id_usuario;?>">
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" readonly name="texto_id_usuario" value="<?=$texto_id_usuario?>" size="38" style="font-size: 10px"><br /> <input type="button"<?=$estado_inscripcion;?> onclick="buscar_alumno()" value="Buscar datos" style="font-size: 10px"> <input type="button"<?=$estado_inscripcion;?> onclick="borrar_alumno()" value="Borrar datos" style="font-size: 10px"> <input type="button" onclick="copiar_alumno()" value="Copiar datos de contacto al alumno" style="font-size: 10px"></font></td>
                        </tr>
                        
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre y apellidos del alumno:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre" value="<?=$nombre_alu?>" size="25" style="font-size: 10px"> <input type="text" name="apellido1" value="<?=$apellido1_alu?>" size="25" style="font-size: 10px"> <input type="text" name="apellido2" value="<?=$apellido2_alu?>" size="25" style="font-size: 10px"></font> <font size=1 color="#bcbcbc"><?=$nombre_alu." ".$apellido1_alu." ".$apellido2_alu?></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Sexo</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="sexo" value="V"<?php  if ($sexo=="V") { print " checked"; } ?>> Varón&nbsp; &nbsp;<input type="radio" name="sexo" value="H"<?php  if ($sexo=="H") { print " checked"; } ?>>Hembra </font></td>
                        </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right" rowspan=2 valign="top"><font face="<?= $fuente_general; ?>" size="1"><b>Fecha de nacimiento</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_nacimiento_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_nacimiento_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_nacimiento_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_nacimiento_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_nacimiento_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad-100; $c_ano<=strftime("%Y")+7; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_nacimiento_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_nacimiento');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>  &nbsp;&nbsp;&nbsp;<font size=1 color="#bcbcbc"><?=date("d/m/Y",strtotime($datos_contacto["fecha_nacimiento"]))?></font>
                                </td>
                                </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td><font size="1" face="<?= $fuente_general; ?>">
                                <input type="checkbox" name="nonato" value="1"<?php  if ($usuario_nonato=="1") { print " checked"; } ?>> Todavía no ha nacido<br>
                                <input type="checkbox" name="prematuro" value="1"<?php  if ($usuario_prematuro=="1") { print " checked"; } ?>> Prematuro/a (8 meses)<br>
                                <input type="checkbox" name="prematuro1" value="1"<?php  if ($usuario_prematuro1=="1") { print " checked"; } ?>> Prematuro/a (7 meses)<br>
                                </font></td>
                        </tr>
                       <?php 
                       if ($edad!="") {
                       ?>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Edad actual:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><?=$edad;?></font></td>
                        </tr>
                       <?php 
                       }
                       ?>                          
                        
                        <tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos de los contactos (padre, madre, tutores...)</b></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Persona de contacto (Padre):</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="persona_contacto" value="<?=$persona_contacto?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$persona_contacto?></font></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Relación de parentesco con el alumno:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="parentesco_padre1" value="" size=1 style="font-size: 10px">
                                                        <option value="0"></option>
                                                        <option value="1"<?php  if ($parentesco_padre1==1) { print " selected"; } ?>>Padre/Madre</option>
                                                        <option value="2"<?php  if ($parentesco_padre1==2) { print " selected"; } ?>>Abuelo/a</option>
                                                        <option value="3"<?php  if ($parentesco_padre1==3) { print " selected"; } ?>>Tio/a</option>
                                                        <option value="4"<?php  if ($parentesco_padre1==4) { print " selected"; } ?>>Autorizado/a</option>
                                                        </select></font></td>
                        </tr>                      
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono de contacto 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono" value="<?=$telefono?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$telefono?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email contacto 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email" value="<?=$email?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$email?></font></font></td>
                        </tr>   
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>DNI contacto 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="dni_padre1" value="<?=$dni_padre1?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$dni_padre1?></font></font></td>
                        </tr> 
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="nombre_padre2" value="<?=$nombre_padre2?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$nombre_padre2?></font></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Relación de parentesco con el alumno:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><select name="parentesco_padre2" value="" size=1 style="font-size: 10px">
                                                        <option value="0"></option>
                                                        <option value="1"<?php  if ($parentesco_padre2==1) { print " selected"; } ?>>Padre/Madre</option>
                                                        <option value="2"<?php  if ($parentesco_padre2==2) { print " selected"; } ?>>Abuelo/a</option>
                                                        <option value="3"<?php  if ($parentesco_padre2==3) { print " selected"; } ?>>Tio/a</option>
                                                        <option value="4"<?php  if ($parentesco_padre2==4) { print " selected"; } ?>>Autorizado/a</option>
                                                        </select></font></td>
                        </tr>                        
<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono de contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_padre2" value="<?=$telefono_padre2?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$telefono_padre2?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email_padre2" value="<?=$email_padre2?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$email_padre2?></font></font></td>
                        </tr>   
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>DNI contacto 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="dni_padre2" value="<?=$dni_padre2?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$dni_padre2?></font></font></td>
                        </tr>  
						<tr valign="top">
                                <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Datos familiares</b></font></td>
                        </tr>                                                                   
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Calle y Número:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="calle" value="<?=$calle?>" size="80" style="font-size: 10px"> <font color="#bcbcbc"><?=$calle?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Población:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="poblacion" value="<?=$poblacion?>" size="50" style="font-size: 10px"> <font color="#bcbcbc"><?=$poblacion?></font></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Código postal:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="codigo_postal" value="<?=$codigo_postal?>" size="10" style="font-size: 10px"> <font color="#bcbcbc"><?=$codigo_postal?></font></font></td>
                        </tr>
                        <tr valign="top">
                                <td colspan=2 align="center"><input type="button" value="Localizar domicilio en el mapa" onclick="localiza();"><br><div id="map_canvas" style="width:450; height:300"></div></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfonos familia:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefonos_familia" value="<?=$telefonos_familia?>" size="50" style="font-size: 10px"></font> <font color="#bcbcbc"><?=$telefonos_familia?></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Email familia:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="email_familia" value="<?=$email_familia?>" size="50" style="font-size: 10px"></font> <font color="#bcbcbc"><?=$email_familia?></font></td>
                        </tr>
                        
<tr valign="top">
                        <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Contactos emergencia</b></font></td>
                        </tr>                                                                   
                       
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre contacto emergencia 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="contacto_emergencia1" value="<?=$contacto_emergencia1?>" size="50" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono contacto emergencia 1:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_contacto_emergencia1" value="<?=$telefono_contacto_emergencia1?>" size="50" style="font-size: 10px"></font></td>
                        </tr>                          
                               
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Nombre contacto emergencia 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="contacto_emergencia2" value="<?=$contacto_emergencia2?>" size="50" style="font-size: 10px"></font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Teléfono contacto emergencia 2:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="telefono_contacto_emergencia2" value="<?=$telefono_contacto_emergencia2?>" size="50" style="font-size: 10px"></font></td>
                        </tr>                                
                          
<tr valign="top">
                        <td colspan=4 align="center" bgcolor="#efefef"><font size=2 face="<?= $fuente_general; ?>"><b>Otra información</b></font></td>
                        </tr>                          

<tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Persona encargada de recoger al niño/a:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><input type="text" name="persona_autorizada" value="<?=$persona_autorizada?>" size="50" style="font-size: 10px"></font></td>
                        </tr>  
           
                                                                       
                        <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>¿Recibe publicidad?</b></font></td>
                                <td><font size="1" face="<?= $fuente_general; ?>"> <input type="radio" name="no_publicidad" value="0"<?php  if ($no_publicidad==0) { print " checked"; } ?>> Si&nbsp; &nbsp;<input type="radio" name="no_publicidad" value="1"<?php  if ($no_publicidad==1) { print " checked"; } ?>> No</font></td>
                        </tr>
                       <tr valign="top" bgcolor="#efefef">
                                <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Comentarios:</b></font></td>
                                <td><font size=1 face="<?= $fuente_general; ?>"><textarea name="comentarios" cols="77" rows=6 style="font-family: Arial; font-size: 11px"><?=$comentarios;?></textarea></font></td>
                        </tr>
        <tr bgcolor="#efefef">
        <td align="right"><font size=1 face="<?= $fuente_general; ?>"><b>Estado Actual del proceso de decisión:</b></font></td>
        <td><select name="proceso_decision" class="defaut" style="font-size: 10px">
        <option value="b1"<?php  if ($proceso_decision=="b1") { print " selected"; } ?>>B1</option>
        <option value="b2"<?php  if ($proceso_decision=="b2") { print " selected"; } ?>>B2</option>
        <option value="b3"<?php  if ($proceso_decision=="b3") { print " selected"; } ?>>B3</option>
        <option value="c"<?php  if ($proceso_decision=="c") { print " selected"; } ?>>C</option>
        </select></td>
        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Fechas significativas</b></font></td>
                        </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Primer contacto B1</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_b1_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_b1_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b1_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_b1_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b1_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_b1_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_b1');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Cambio a B2</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_b2_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_b2_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b2_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_b2_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b2_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_b2_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <a href="javascript:selecciona_fecha('fecha_b2');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Cambio a B3</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_b3_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_b3_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b3_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_b3_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_b3_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_b3_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('fecha_b3');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <tr bgcolor="#efefef">
                                <td width=25% align="right"><font face="<?= $fuente_general; ?>" size="2"><b>Cambio a C</b>&nbsp;</font></td>
                                <td width=75%>&nbsp;<select name="fecha_c_dia" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_dia=1; $c_dia<=31; $c_dia=$c_dia+1) {
                                                                      if ($c_dia<10) {
                                                                      $dia_a="0".$c_dia;
                                                                      } else {
                                                                      $dia_a=$c_dia;
                                                                      }
                                                                      ?>
                                                                      <option value="<?= $dia_a; ?>"<?php  if (intval($dia_a)==intval($fecha_c_dia)) { print " selected"; } ?>><?= $dia_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_c_mes" style="font-size: 10px">
                                                                      <option value="--">--</option>
                                                                      <?php 
                                                                      for ($c_mes=1; $c_mes<=12; $c_mes=$c_mes+1) {
                                                                      if ($c_mes<10) {
                                                                        $mes_a="0".$c_mes;
                                                                        } else {
                                                                        $mes_a=$c_mes;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $mes_a; ?>"<?php  if (intval($mes_a)==intval($fecha_c_mes)) { print " selected"; } ?>><?= $mes_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select> <select name="fecha_c_ano" style="font-size: 10px">
                                                                      <option value="----">----</option>
                                                                      <?php 
                                                                      for ($c_ano=$ano_inicio_actividad; $c_ano<=strftime("%Y")+1; $c_ano=$c_ano+1) {
                                                                        if ($c_ano<10) {
                                                                        $ano_a="0".$c_ano;
                                                                        } else {
                                                                        $ano_a=$c_ano;
                                                                        }
                                                                      ?>
                                                                      <option value="<?= $ano_a; ?>"<?php  if (intval($ano_a)==intval($fecha_c_ano)) { print " selected"; } ?>><?= $ano_a; ?></option>
                                                                      <?php 
                                                                      }
                                                                      ?>
                                                                      </select>  <a href="javascript:selecciona_fecha('fecha_c');" title="Selecciona la fecha en el calendario"><img src="calendario.gif" border=0 align="absmiddle"></a>
                                </td>
                                </tr>
                        <?php 
                        if (strlen($id_contacto)!=0) {
                        ?>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2><br><font size=2 face="<?= $fuente_general; ?>"><b>Notas de seguimiento</b></font></td>
                        </tr>
                        <tr valign="top" bgcolor="#efefef">
                                <td width=100% align="center" colspan=2>
                                <?php 
                                // Busca documentos de este expediente
                                $busca_notas=db_query_params('SELECT * FROM contactos_seguimiento, usuarios WHERE contactos_seguimiento.id_usuario=usuarios.id_usuario AND contactos_seguimiento.id_contacto=? ORDER BY contactos_seguimiento.fecha', 's', array($id_contacto)) or die(db_error());
                                ?><br>
                                <table width="98%" cellpadding=1 cellspacing=0 border=1 style="border-collapse: collapse" bordercolor="#dcdcdc">
                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="18%" colspan=2><font size=1 face="<?= $fuente_general; ?>"><b>Fechas</b></font></td>
                                                <td align="center" width="82%" colspan=4><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        </tr>

                                        <tr bgcolor="#efefef" valign="top">
                                                <td align="center" width="9%"><font size=1 face="<?= $fuente_general; ?>"><b>Creación</b></font></td>
                                                <td align="center" width="9%"><font size=1 face="<?= $fuente_general; ?>"><b>Modificación</b></font></td>
                                                <td align="left" width="20%"><font size=1 face="<?= $fuente_general; ?>"><b>Introducida por...</b></font></td>
                                                <td align="center" width="54%"><font size=1 face="<?= $fuente_general; ?>"><b>Comentarios</b></font></td>
                                                <td align="center" width="4%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                                <td align="center" width="4%"><font size=1 face="<?= $fuente_general; ?>"><b>&nbsp;</b></font></td>
                                        </tr>
                                <?php 
                                if (db_num_rows($busca_notas)==0) {
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center" colspan=6><font size=2 face="<?= $fuente_general; ?>"><b>No se han introducido notas de seguimiento, haz clic sobre el botón <i>Agregar notas</i> para añadirlas.</b></font></td>
                                </tr>
                                <?php 
                                }

                                while($datos_notas=db_fetch_array($busca_notas)) {
                                $nombre_completo_usuario_notas=$datos_notas["Apellido1"]." ".$datos_notas["Apellido2"].", ".$datos_notas["Nombre"];
                                ?>
                                <tr bgcolor="#ffffff" valign="top">
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_notas["fecha"]));?></font></td>
                                        <td align="center"><font size=1 face="<?= $fuente_general; ?>"><?=date("d/m/Y",strtotime($datos_notas["fecha_modificacion"]));?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$nombre_completo_usuario_notas;?></font></td>
                                        <td align="left"><font size=1 face="<?= $fuente_general; ?>"><?=$datos_notas["comentarios"];?></font></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a title="Modificar la nota" href="javascript:editar_nota(<?=$datos_notas["id_nota"];?>)"><img src="editar.gif" border=0></a></td>
                                        <td align="center" width=5%><font size=1 face="<?= $fuente_general; ?>" color="#000000"><a title="Eliminar de la lista" href="javascript:borrar_nota(<?=$datos_notas["id_nota"];?>,'')"><img src="borrar.gif" border=0></a></td>
                                </tr>
                                <?php 
                                }
                                ?>
                                </table>
                                <br><input type="button" onclick="nueva_nota()" style="font-size: 10px" value="Agregar notas"><br><br>
                                </td>
                        </tr>
                        <?php 
                        }
                        ?>
                        </table>
                        <input type="button" value="Cancelar" onclick="window.location.href='<?=$url_retorno?>'" style="font-size: 10px"> <input type="button" value="Siguiente >>" style="font-size: 10px" onclick="comprobar_y_enviar();">
                </td>
        </tr>
</form>
</table><br><br>
<?php  require("../configuracion/parte_inferior.php"); ?>
