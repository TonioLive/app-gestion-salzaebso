<?php  require("../configuracion/config.php"); ?>
<?php  require("../configuracion/autentifica.php");


$textos_title[0]="Mostrar";
$textos_title[2]="Ocultar";

// Opciones

$alus=$_GET["alus"];
if (strlen($alus)==0) {
        $alus=1;
        }
$cont=$_GET["cont"];
if (strlen($cont)==0) {
        $cont=1;
        }
$escu=$_GET["escu"];
if (strlen($escu)==0) {
        $escu=1;
        }
$muni=$_GET["muni"];
if (strlen($muni)==0) {
        $muni=1;
        }
$lapi=$_GET["lapi"];
if (strlen($lapi)==0) {
        $lapi=1;
        }        
$list=$_GET["list"];
if (strlen($list)==0) {
        $list=1;
        }        

// busca alumnos de un curso

$centro_seleccionado_listado=$_GET["cen"];
if (strlen($centro_seleccionado_listado)==0) {
        $centro_seleccionado_listado=$centro_actual;
        }

$curso_seleccionado_listado=$_GET["cur"];
if (strlen($curso_seleccionado_listado)==0) {
        $busca_curso_actual=db_query_params('SELECT * FROM cursos_academicos WHERE fecha_inicio<=? AND fecha_fin>=?', 'ss', array((string)(date("Y-m-d")), (string)(date("Y-m-d"))));

        if (db_num_rows($busca_curso_actual)!=0) {
                $datos_curso_actual=db_fetch_array($busca_curso_actual);
                $curso_seleccionado_listado=$datos_curso_actual["id_curso"];
                } else {
                $curso_seleccionado_listado=0;
                }
        }



$latitudes='';
$longitudes='';
$nombres='';

if ($alus==1) {

$busca_alumnos_inscritos=db_query("SELECT alumnos.*,usuarios.*,alumnos.id_centro AS id_centro_ins FROM alumnos,usuarios WHERE alumnos.id_usuario=usuarios.id_usuario AND alumnos.id_curso=$curso_seleccionado_listado AND alumnos.id_centro=$centro_seleccionado_listado AND usuarios.coordenadas is Not Null ORDER BY alumnos.id_centro,alumnos.aula,usuarios.Apellido1,usuarios.Apellido2,usuarios.Nombre");

while($alumnos_inscritos=db_fetch_array($busca_alumnos_inscritos)) {

                // Escribe fila
                if ((strlen($alumnos_inscritos["coordenadas"])!=0) and ($alumnos_inscritos["coordenadas"]!="NO LOCALIZADO")) {
                        $coordenadas_actuales=str_replace("(","",$alumnos_inscritos["coordenadas"]);
                        $coordenadas_actuales=str_replace(")","",$coordenadas_actuales);
                        $array_coordenadas=explode(",",$coordenadas_actuales);
                        $latitud_actual=$array_coordenadas[0];
                        $longitud_actual=$array_coordenadas[1];
                        $latitudes=$latitudes.$latitud_actual.',';
                        $longitudes=$longitudes.$longitud_actual.',';
                        $nombres=$nombres.'"'.$alumnos_inscritos["Nombre"].' '.$alumnos_inscritos["Apellido1"].' '.$alumnos_inscritos["Apellido2"].'",';
                        }

}

if (strlen($latitudes)!=0) {
        $latitudes=substr($latitudes,0,strlen($latitudes)-1);
        $longitudes=substr($longitudes,0,strlen($longitudes)-1);
        $nombres=substr($nombres,0,strlen($nombres)-1);
        }
}

// Contactos

if ($cont==1) {

$latitudes_con='';
$longitudes_con='';
$nombres_con='';

$busca_contactos=db_query("SELECT * FROM contactos WHERE id_curso=$curso_seleccionado_listado AND id_centro=$centro_seleccionado_listado");
while($alumnos_inscritos=db_fetch_array($busca_contactos)) {
        if ((strlen($alumnos_inscritos["coordenadas"])!=0) and ($alumnos_inscritos["coordenadas"]!="NO LOCALIZADO")) {
                $coordenadas_actuales=str_replace("(","",$alumnos_inscritos["coordenadas"]);
                $coordenadas_actuales=str_replace(")","",$coordenadas_actuales);
                $array_coordenadas=explode(",",$coordenadas_actuales);
                $latitud_actual=$array_coordenadas[0];
                $longitud_actual=$array_coordenadas[1];
                $latitudes_con=$latitudes_con.$latitud_actual.',';
                $longitudes_con=$longitudes_con.$longitud_actual.',';
                $nombres_con=$nombres_con.'"'.$alumnos_inscritos["Nombre"].' '.$alumnos_inscritos["Apellido1"].' '.$alumnos_inscritos["Apellido2"].'",';
                }
        }
        
if (strlen($latitudes_con)!=0) {
        $latitudes_con=substr($latitudes_con,0,strlen($latitudes_con)-1);
        $longitudes_con=substr($longitudes_con,0,strlen($longitudes_con)-1);
        $nombres_con=substr($nombres_con,0,strlen($nombres_con)-1);
        }
}

// Lista de espera
if ($list==1) {

$latitudes_lis='';
$longitudes_lis='';
$nombres_lis='';

$busca_lista_espera=db_query("SELECT * FROM lista_espera WHERE id_centro=$centro_seleccionado_listado AND id_curso=$curso_seleccionado_listado AND NOT exists (SELECT id_alumno FROM alumnos WHERE alumnos.id_usuario=lista_espera.id_usuario AND alumnos.id_curso=lista_espera.id_curso AND alumnos.id_curso=$curso_seleccionado_listado)") or die(db_error());



while($alumnos_inscritos=db_fetch_array($busca_lista_espera)) {
        // Escribe fila
        if ((strlen($alumnos_inscritos["coordenadas"])!=0) and ($alumnos_inscritos["coordenadas"]!="NO LOCALIZADO")) {
                $coordenadas_actuales=str_replace("(","",$alumnos_inscritos["coordenadas"]);
                $coordenadas_actuales=str_replace(")","",$coordenadas_actuales);
                $array_coordenadas=explode(",",$coordenadas_actuales);
                $latitud_actual=$array_coordenadas[0];
                $longitud_actual=$array_coordenadas[1];
                $latitudes_lis=$latitudes_lis.$latitud_actual.',';
                $longitudes_lis=$longitudes_lis.$longitud_actual.',';
                $nombres_lis=$nombres_lis.'"'.$alumnos_inscritos["Nombre"].' '.$alumnos_inscritos["Apellido1"].' '.$alumnos_inscritos["Apellido2"].'",';
                }
        }
        
if (strlen($latitudes_lis)!=0) {
        $latitudes_lis=substr($latitudes_lis,0,strlen($latitudes_lis)-1);
        $longitudes_lis=substr($longitudes_lis,0,strlen($longitudes_lis)-1);
        $nombres_lis=substr($nombres_lis,0,strlen($nombres_lis)-1);
        }
} 


// Nuestras

if ($lapi==1) {

$latitudes_p='';
$longitudes_p='';
$nombres_p='';

$busca_escuelas_competencia=db_query("SELECT * FROM escuelas_competencia WHERE propia=1");
while($competencia=db_fetch_array($busca_escuelas_competencia)) {
                        $latitudes_p=$latitudes_p.str_replace(",",".",$competencia["latitud"]).',';
                        $longitudes_p=$longitudes_p.str_replace(",",".",$competencia["longitud"]).',';
                        $nombres_p=$nombres_p.'"'.$competencia["nombre_escuela"].'",';
        }
if (strlen($latitudes_p)!=0) {
        $latitudes_p=substr($latitudes_p,0,strlen($latitudes_p)-1);
        $longitudes_p=substr($longitudes_p,0,strlen($longitudes_p)-1);
        $nombres_p=substr($nombres_p,0,strlen($nombres_p)-1);
        }
}

// Competencia

if ($escu==1) {

$latitudes_c='';
$longitudes_c='';
$nombres_c='';

$busca_escuelas_competencia=db_query("SELECT * FROM escuelas_competencia WHERE municipal=0 AND propia=0");
while($competencia=db_fetch_array($busca_escuelas_competencia)) {
                        $latitudes_c=$latitudes_c.str_replace(",",".",$competencia["latitud"]).',';
                        $longitudes_c=$longitudes_c.str_replace(",",".",$competencia["longitud"]).',';
                        $nombres_c=$nombres_c.'"'.$competencia["nombre_escuela"].'",';
        }
if (strlen($latitudes_c)!=0) {
        $latitudes_c=substr($latitudes_c,0,strlen($latitudes_c)-1);
        $longitudes_c=substr($longitudes_c,0,strlen($longitudes_c)-1);
        $nombres_c=substr($nombres_c,0,strlen($nombres_c)-1);
        }
}

// Escuelas municipales

if ($muni==1) {

$latitudes_m='';
$longitudes_m='';
$nombres_m='';

$busca_escuelas_competencia=db_query("SELECT * FROM escuelas_competencia WHERE municipal=1 AND propia=0");
while($competencia=db_fetch_array($busca_escuelas_competencia)) {
                        $latitudes_m=$latitudes_m.str_replace(",",".",$competencia["latitud"]).',';
                        $longitudes_m=$longitudes_m.str_replace(",",".",$competencia["longitud"]).',';
                        $nombres_m=$nombres_m.'"'.$competencia["nombre_escuela"].'",';
        }
if (strlen($latitudes_m)!=0) {
        $latitudes_m=substr($latitudes_m,0,strlen($latitudes_m)-1);
        $longitudes_m=substr($longitudes_m,0,strlen($longitudes_m)-1);
        $nombres_m=substr($nombres_m,0,strlen($nombres_m)-1);
        }
}

$formulario_accion="mapa.php";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title><?php  echo $titulo_web ; ?></title>
<meta name="Description" content="<?php  echo $meta_description ; ?>">
<meta name="Keywords" content="<?php  echo $meta_keywords ; ?>">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=ES"></script>
<script type="text/javascript">
  var geocoder;
  var map;
  var errores = "";
  var latitudes = [<?=$latitudes;?>];
  var longitudes = [<?=$longitudes;?>];
  var nombres = [<?=$nombres;?>];
  var latitudes_c = [<?=$latitudes_c;?>];
  var longitudes_c = [<?=$longitudes_c;?>];
  var nombres_c = [<?=$nombres_c;?>];
  var latitudes_m = [<?=$latitudes_m;?>];
  var longitudes_m = [<?=$longitudes_m;?>];
  var nombres_m = [<?=$nombres_m;?>];
  var latitudes_p = [<?=$latitudes_p;?>];
  var longitudes_p = [<?=$longitudes_p;?>];
  var nombres_p = [<?=$nombres_p;?>];
  var latitudes_con = [<?=$latitudes_con;?>];
  var longitudes_con = [<?=$longitudes_con;?>];
  var nombres_con = [<?=$nombres_con;?>];

  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom:15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    muestra_direccion();
    muestra_escuelas();
  }

  function Localiza(latitud,longitud,texto) {

        var point = new google.maps.LatLng(latitud, longitud);

        map.setCenter(point);
        var alumnosMarker = new google.maps.Marker({
            map: map,
            position: point,
            title: texto
        });

        }

  function Localiza_c(latitud,longitud,texto) {

            var image = 'images/beachflag.png';
            var myLatLng = new google.maps.LatLng(latitud, longitud);
            
            map.setCenter(myLatLng);
            var escuelasMarker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: image,
                title: texto
            });
        }

  function Localiza_m(latitud,longitud,texto) {

            var image = 'images/beachflag1.png';
            var myLatLng = new google.maps.LatLng(latitud, longitud);
            
            map.setCenter(myLatLng);
            var municipalesMarker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: image,
                title: texto
            });
        }

  function Localiza_p(latitud,longitud,texto) {

            var image = 'images/beachflag2.png';
            var myLatLng = new google.maps.LatLng(latitud, longitud);
            
            map.setCenter(myLatLng);
            var lapicesMarker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: image,
                title: texto
            });
        }

  function Localiza_con(latitud,longitud,texto) {

            var image = 'images/contacto.png';
            var myLatLng = new google.maps.LatLng(latitud, longitud);
            
            map.setCenter(myLatLng);
            var contactosMarker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: image,
                title: texto
            });
        }
        
  function Localiza_lis(latitud,longitud,texto) {

            var image = 'images/listaespera.png';
            var myLatLng = new google.maps.LatLng(latitud, longitud);
            
            map.setCenter(myLatLng);
            var contactosMarker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: image,
                title: texto
            });
        }        

  function codeAddress(consulta) {
    var address = consulta;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        // alert("No se ha encontrado la dirección en el mapa: " + status + " " + consulta);
        // errores = errores+"No se ha encontrado la dirección en el mapa: " + status + "Dirección: " + consulta + "<br>";
      }
    });
  }

  function muestra_direccion() {
      <?php 
      if ($alus==1) {
      ?>
      for (conta=0;conta<latitudes.length;conta++) {
                Localiza(latitudes[conta],longitudes[conta],nombres[conta]);
                }
      <?php 
      }
      if ($cont==1) {
      ?>
      for (conta=0;conta<latitudes_con.length;conta++) {
                Localiza_con(latitudes_con[conta],longitudes_con[conta],nombres_con[conta]);
                }
      <?php 
      }
      if ($list==1) {
      ?>
      for (conta=0;conta<latitudes_lis.length;conta++) {
                Localiza_lis(latitudes_lis[conta],longitudes_lis[conta],nombres_lis[conta]);
                }
      <?php 
      }      
      ?>
      }
  function muestra_escuelas() {
      <?php 
      if ($escu==1) {
      ?>
      for (conta=0;conta<latitudes_c.length;conta++) {
                Localiza_c(latitudes_c[conta],longitudes_c[conta],nombres_c[conta]);
                }
      <?php 
      }
      if ($muni==1) {
      ?>
      for (conta=0;conta<latitudes_m.length;conta++) {
                Localiza_m(latitudes_m[conta],longitudes_m[conta],nombres_m[conta]);
                }
      <?php 
      }
      if ($lapi==1) {
      ?>
      for (conta=0;conta<latitudes_p.length;conta++) {
                Localiza_p(latitudes_p[conta],longitudes_p[conta],nombres_p[conta]);
                }
      <?php 
      }
      ?>
      }

  function cambiavalor(valor) {
  
        eval("var valor_actual=document.listados."+valor+".value;");
        
        valor_actual=(Number(valor_actual)*-1);
        
        eval("document.listados."+valor+".value=valor_actual;");
        
        document.listados.submit();

        }
</script>
<?php  require("../configuracion/estilos/estilo2.php"); ?>
</head>
<body topmargin="2" leftmargin="0" marginwidth="0" marginheight="2" scroll="auto" onload="initialize();">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-width: 0" bordercolor="#111111" width="100%" id="AutoNumber1" background="<?php  echo $url_root ; ?>/imagen/fondo_menu_ancho.gif">
  <tr>
    <td width="100%" style="border-style: none; border-width: medium" valign="top" align="center">
        <div align="center">
<div align="center"><center>
<br>
<table border="1" style="border-collapse: collapse" bordercolor="#000000" width="750">
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=5><small><b>Mapa de distribución de los alumnos</b></small></td>
  </tr>
 <form method="GET" action="<?=$formulario_accion;?>" name="listados">
 <input type="hidden" name="alus" value="<?=$alus?>">
 <input type="hidden" name="cont" value="<?=$cont?>">
 <input type="hidden" name="escu" value="<?=$escu?>">
 <input type="hidden" name="muni" value="<?=$muni?>">
 <input type="hidden" name="lapi" value="<?=$lapi?>">
                        <tr><td align="right">
                        <font size=2><b>Curso: </b></font></td><td><select name="cur" size=1 style="font-size: 10px">
                        <?php 
                        $busca_listado_cursos=db_query("SELECT * FROM cursos_academicos ORDER BY fecha_inicio");

                        while($datos_listado_cursos=db_fetch_array($busca_listado_cursos)) {
                        ?>
                        <option value="<?=$datos_listado_cursos["id_curso"];?>"<?php if ($datos_listado_cursos["id_curso"]==$curso_seleccionado_listado) { print " selected"; } ?>><?=$datos_listado_cursos["nombre"];?></option>
                        <?php 
                        }
                        ?>
                        </select></td>
                        <td rowspan=2 bgcolor="#f2efe9">
                        <font size=2>
                        &nbsp;&nbsp;&nbsp;<a href="javascript: cambiavalor('alus')" title="<?=$textos_title[($alus+1)]?>"><img src="images/marca1.gif" border=0> Alumno</a><br>
                        &nbsp;&nbsp;&nbsp;<a href="javascript: cambiavalor('cont')" title="<?=$textos_title[($cont+1)]?>"><img src="images/marca5.gif" border=0> Nuevo contacto</a><br></font></td>
                        <td rowspan=2 bgcolor="#f2efe9">
                        <font size=2>
                        &nbsp;&nbsp;&nbsp;<a href="javascript: cambiavalor('escu')" title="<?=$textos_title[($escu+1)]?>"><img src="images/marca2.gif" border=0> Escuela Infantil</a><br>
                        &nbsp;&nbsp;&nbsp;<a href="javascript: cambiavalor('muni')" title="<?=$textos_title[($muni+1)]?>"><img src="images/marca3.gif" border=0> Escuela Infantil Municipal</a><br>
                        &nbsp;&nbsp;&nbsp;<a href="javascript: cambiavalor('lapi')" title="<?=$textos_title[($lapi+1)]?>"><img src="images/marca4.gif" border=0> Lápices</a><br></font></td>
                        <td rowspan=2 bgcolor="#f2efe9" align="center"><font size=2><a href="nueva_escuela.php">Añadir escuela infantil</a><br><br>
                        <a href="listado_escuelas.php">Listado de escuelas</a></font></td>
                        </tr>
                <tr>
                <td align="right"><font size=2 face="<?= $fuente_general; ?>"><b>Centro:</b></font></td>
                <td><select name="cen" size=1 style="font-size: 10px">
                <?php 
                $consulta_centros=db_query("SELECT * FROM centros ORDER BY nombre_centro;");
                while($centros=db_fetch_array($consulta_centros)){
                ?>
                <option value="<?=$centros["id_centro"];?>"<?php if ($centros["id_centro"]==$centro_seleccionado_listado) { print " selected"; } ?>><?=$centros["nombre_centro"];?> (<?=$centros["poblacion_centro"];?>)</option>
                <?php 
                }
                ?>
                </select></font></td>
                </tr>
  <tr bgcolor="<?php  echo $fondo_at; ?>">
    <td align="center" colspan=2><small>&nbsp;</small></td>
    <td align="center" colspan=2><small><b>Para activar o descactivar los distintos tipos de marcas, haz clic con el ratón sobre ellas en la leyenda</b></small></td>
    <td align="center"><small>&nbsp;</small></td>
  </tr>
                        </table><br>
                        <input type="submit" value="Mostar en el mapa" style="font-size: 10px"> <input type="button" value="Salir" onclick="window.location.href='/index.php'" style="font-size: 10px"><br><br>
                        </form>
<div id="map_canvas" style="width:1240; height:700"></div>
<div id="capa_errores" style="visible: block"></div>
</center></div>
</div>
<?php  require("../configuracion/parte_inferior.php"); ?>
