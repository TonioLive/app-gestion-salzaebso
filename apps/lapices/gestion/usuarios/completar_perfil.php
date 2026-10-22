<?php
require dirname(dirname(__DIR__)).'/configuracion/config.php';
require_once dirname(dirname(__DIR__)).'/configuracion/personal_onboarding.php';
require_once dirname(__DIR__).'/panel/workflow.php';
if(empty($permitido)){header('Location: /index.php',true,303);exit;}
if(!personal_onboarding_required((int)$idUsuario)){header('Location: /index.php',true,303);exit;}
$error='';$rows=lap_wf_rows('SELECT Nombre,telefono1,Direccion,email,telefono_contacto1 FROM usuarios WHERE id_usuario=?','i',array((int)$idUsuario));
if(!$rows){http_response_code(403);exit('No se encuentra tu ficha.');}$data=$rows[0];
if(app_request_value($_SERVER,'REQUEST_METHOD')==='POST'){
 try{lap_wf_post();foreach(array('telefono1','Direccion','email','telefono_contacto1') as $key)$data[$key]=isset($_POST[$key])&&is_string($_POST[$key])?trim($_POST[$key]):'';personal_onboarding_complete((int)$idUsuario,$data);header('Location: /index.php',true,303);exit;}
 catch(Exception $e){http_response_code(422);$error=$e->getMessage();}
}
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Completa tus datos · Lápices</title><link rel="stylesheet" href="/gestion/panel/assets/workflow.css?v=20261016.1"></head><body style="margin:0;background:#f2f7f4"><main class="wf wf-profile"><img src="/imagen/logo_redondo.png" alt="Lápices" width="70" height="70"><h1>Bienvenida/o, <?=app_escape($data['Nombre'])?></h1><p class="wf-help">Antes de empezar, completa tus datos de contacto. Los cuatro campos son obligatorios.</p><?php lap_wf_error($error); ?><form method="post" class="wf-card"><?php lap_wf_token(); ?><div class="wf-grid">
<?php lap_wf_input('Teléfono','telefono1',$data['telefono1'],'tel',true,50);lap_wf_input('Correo electrónico','email',$data['email'],'email',true,255);lap_wf_input('Dirección completa y localidad','Direccion',$data['Direccion'],'text',true,1000);lap_wf_input('Teléfono de emergencia','telefono_contacto1',$data['telefono_contacto1'],'tel',true,50); ?>
</div><div class="wf-actions"><button class="wf-btn" type="submit">Guardar y entrar al programa</button></div></form><a class="wf-link" href="/configuracion/cerrar_sesion.php">Cerrar sesión</a></main></body></html>
