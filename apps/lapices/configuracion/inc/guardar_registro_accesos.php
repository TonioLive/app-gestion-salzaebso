<?php 

$fecha_copia_seg=date("YmdHis");

$consulta1="REPAIR TABLE estadisticas1;";
db_query($consulta1) or die(db_error());
$consulta2="CREATE TABLE registro_accesos.estadisticas1_$fecha_copia_seg (
Contador int( 11 ) NOT NULL AUTO_INCREMENT ,
Sesion char( 100 ) default NULL ,
Fecha date default NULL ,
Hora time default NULL ,
REMOTE_ADDR char( 50 ) default NULL ,
HTTP_ACCEPT_LANGUAGE char( 50 ) default NULL ,
HTTP_User_Agent char( 50 ) default NULL ,
Pagina_visitada char( 255 ) default NULL ,
usuario varchar( 100 ) default NULL ,
info_adicional varchar( 255 ) default NULL ,
PRIMARY KEY ( Contador )
);";
db_query($consulta2) or die(db_error());

$consulta3="INSERT INTO registro_accesos.estadisticas1_$fecha_copia_seg SELECT * FROM gestionguarderias.estadisticas1 ;";
db_query($consulta3) or die(db_error());
$consulta4="TRUNCATE TABLE estadisticas1;";
db_query($consulta4) or die(db_error());
$consulta5="OPTIMIZE TABLE estadisticas1;";
db_query($consulta5) or die(db_error());

$consulta1="REPAIR TABLE sesion;";
db_query($consulta1) or die(db_error());
$consulta2="CREATE TABLE registro_accesos.sesion_$fecha_copia_seg (
Contador int( 11 ) NOT NULL AUTO_INCREMENT ,
id_session char( 255 ) default NULL ,
Usuario char( 255 ) default NULL ,
Tipo char( 1 ) default NULL ,
Fecha date default NULL ,
Hora time default NULL ,
REMOTE_ADDR char( 255 ) default NULL ,
HTTP_ACCEPT_LANGUAGE char( 255 ) default NULL ,
HTTP_User_Agent char( 255 ) default NULL ,
Hora_cierre time default NULL ,
Cerrada tinyint( 1 ) default NULL ,
Fecha_cierre date default '0000-00-00',
nombre_usuario varchar( 100 ) default NULL ,
PRIMARY KEY ( Contador )
);";
db_query($consulta2) or die(db_error());
$consulta3="INSERT INTO registro_accesos.sesion_$fecha_copia_seg SELECT * FROM gestionguarderias.sesion ;";
db_query($consulta3) or die(db_error());
$consulta4="TRUNCATE TABLE sesion;";
db_query($consulta4) or die(db_error());
$consulta5="OPTIMIZE TABLE sesion;";
db_query($consulta5) or die(db_error());

$consulta1="REPAIR TABLE sesion_fallos;";
db_query($consulta1) or die(db_error());
$consulta2="CREATE TABLE registro_accesos.sesion_fallos_$fecha_copia_seg (
Contador int( 11 ) NOT NULL AUTO_INCREMENT ,
id_session char( 255 ) default NULL ,
Usuario char( 255 ) default NULL ,
Pass char( 255 ) default NULL ,
Fecha date default NULL ,
Hora time default NULL ,
REMOTE_ADDR char( 255 ) default NULL ,
HTTP_ACCEPT_LANGUAGE char( 255 ) default NULL ,
HTTP_User_Agent char( 255 ) default NULL ,
descripcion_fallo varchar( 255 ) default NULL ,
PRIMARY KEY ( Contador )
);";
db_query($consulta2) or die(db_error());
$consulta3="INSERT INTO registro_accesos.sesion_fallos_$fecha_copia_seg SELECT * FROM gestionguarderias.sesion_fallos ;";
db_query($consulta3) or die(db_error());
$consulta4="TRUNCATE TABLE sesion_fallos;";
db_query($consulta4) or die(db_error());
$consulta5="OPTIMIZE TABLE sesion_fallos;";
db_query($consulta5) or die(db_error());

db_query("INSERT INTO gestionguarderias.registro_copias_registro (hora,fecha) VALUES ('".date("H:i:s")."','".date("Y-m-d")."');") or die(db_error());

?>
