<?php
require dirname(dirname(__DIR__)).'/configuracion/config.php';
require dirname(dirname(__DIR__)).'/configuracion/autentifica_admin.php';
require_once __DIR__.'/alta_model.php';
try{
 $actor=personal_actor();$id=app_request_int($_GET,'id',0);
 $rows=lap_wf_rows('SELECT id_centro,contrato_archivo,contrato_nombre FROM personal_altas WHERE id_usuario=?','i',array($id));
 if(!$rows||((int)$actor['nivel_acceso']!==4&&(int)$rows[0]['id_centro']!==(int)$centro_actual))throw new DomainException('No tienes acceso a este contrato.');
 $name=$rows[0]['contrato_archivo'];if(!preg_match('/^contrato-\d+-[a-f0-9]{24}\.pdf$/',$name)||!is_file(personal_private_dir().'/'.$name))throw new RuntimeException('No hay un contrato PDF archivado para esta alta.');
 header('Content-Type: application/pdf');header('X-Content-Type-Options: nosniff');header('Content-Disposition: attachment; filename="contrato-personal-'.$id.'.pdf"');header('Cache-Control: no-store, private');header('Content-Length: '.filesize(personal_private_dir().'/'.$name));readfile(personal_private_dir().'/'.$name);
}catch(Exception $e){http_response_code(403);echo app_escape($e->getMessage());}
