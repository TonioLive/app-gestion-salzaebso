<?php
require_once dirname(__DIR__,2).'/configuracion/config.php';
require_once dirname(__DIR__,2).'/configuracion/family-messages.php';
if(!fm_can_access()){http_response_code(403);exit('No tienes permiso para acceder a los mensajes de familias.');}
app_db()->set_charset('utf8mb4');
function fm_header($title) {
 header('Content-Type: text/html; charset=UTF-8');
 ?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?=app_escape($title)?> · Neo Kids</title><link rel="stylesheet" href="/app/assets/pool.css?v=20260924"><link rel="stylesheet" href="/gestion/mensajes/mensajes.css?v=20260930"></head><body><header class="family-header"><a class="brand" href="/"><span class="symbol">≈</span><span>Neo Kids<small>Gestión de piscina</small></span></a><a class="quiet" href="/">← Inicio</a></header><main class="fm-main"><div class="fm-heading"><div><span class="eyebrow">En contacto con las familias</span><h1><?=app_escape($title)?></h1></div><a class="quiet" href="/gestion/mensajes/bandeja.php">Bandeja de mensajes</a></div><?php
 if(!empty($_SESSION['fm_notice'])){echo '<p class="notice" role="status">'.app_escape($_SESSION['fm_notice']).'</p>';unset($_SESSION['fm_notice']);}
}
function fm_footer(){echo '</main></body></html>';}
function fm_input($name,$value){echo '<input type="hidden" name="'.app_escape($name).'" value="'.app_escape($value).'">';}
function fm_post_fields($action,$id=0){fm_input('_csrf',app_csrf_token());fm_input('action',$action);if($id)fm_input('id_mensaje',$id);}
function fm_redirect($path){header('Location: '.$path,true,303);exit;}
