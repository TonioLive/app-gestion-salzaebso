<?php
require dirname(__DIR__).'/programaciones-common.php';
prog_post();$activity=prog_id($_POST,'id_actividad');$id=0;$session=0;
if(app_value($_POST,'return_html')==='1'){$id=prog_id($_POST,'id_plantilla');$session=prog_id($_POST,'sesion_editada');$template=prog_template($id);if($session>(int)$template['num_sesiones'])prog_error('La sesión no pertenece a la programación.');}
if(!app_rows('SELECT id_actividad FROM agenda_programacion_actividades_piscina WHERE id_actividad=?','i',[$activity]))prog_error('La actividad no está disponible.');
$file=$_FILES['images']??null;
if(!$file||is_array($file['name'])||(int)$file['error']!==UPLOAD_ERR_OK||!is_uploaded_file($file['tmp_name']))prog_error('Selecciona una imagen JPEG o PNG.');
if((int)$file['size']>8*1024*1024)prog_error('La imagen no puede superar 8 MB.');
$info=@getimagesize($file['tmp_name']);
if(!$info||!in_array($info[2],[IMAGETYPE_JPEG,IMAGETYPE_PNG],true)||$info[0]*$info[1]>16000000)prog_error('Selecciona una imagen JPEG o PNG de hasta 16 megapíxeles.');
$source=$info[2]===IMAGETYPE_JPEG?@imagecreatefromjpeg($file['tmp_name']):@imagecreatefrompng($file['tmp_name']);
if(!$source)prog_error('No se ha podido leer la imagen.');
$ratio=min(1,1800/max($info[0],$info[1]));$width=max(1,(int)round($info[0]*$ratio));$height=max(1,(int)round($info[1]*$ratio));$image=imagecreatetruecolor($width,$height);imagefill($image,0,0,imagecolorallocate($image,255,255,255));imagecopyresampled($image,$source,0,0,0,0,$width,$height,$info[0],$info[1]);
$temp=tempnam(dirname(__DIR__,3).'/.private','activity-image-');
if(!$temp||!imagejpeg($image,$temp,85)||!rename($temp,__DIR__.'/'.$activity.'.jpg')){if($temp&&is_file($temp))unlink($temp);prog_error('No se ha podido guardar la imagen.');}
if(app_value($_POST,'return_html')==='1'){header('Location: ../editar_objetivos_plantillas.php?id_plantilla='.$id.'&sesion_editada='.$session.'&id_actividad='.$activity.'&done=saved',true,303);exit;}
header('Content-Type: application/json; charset=UTF-8');echo json_encode(['ok'=>true]);
