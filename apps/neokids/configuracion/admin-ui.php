<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/autentifica.php';
neo_utf8_input();app_db()->set_charset('utf8mb4');
function neo_page_start($title,$subtitle='',$section='') {
    header('Content-Type: text/html; charset=UTF-8');
    $centre=app_text($GLOBALS['nombre_centro_actual']??'');
    ?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=app_escape($title)?> · Neo Kids</title><link rel="stylesheet" href="/css/neo-admin.css?v=20260930-2"><script src="/scripts/neo-admin.js?v=20260930-2" defer></script></head><body class="neo-page"><header class="neo-topbar"><a class="neo-brand" href="/"><span aria-hidden="true">≈</span><strong>Neo Kids<small>Gestión de piscina</small></strong></a><nav aria-label="Navegación"><a href="/">Inicio</a><a href="/gestion/piscina/grupos.php">Grupos</a><?php if(app_can('zona_mensajes')||((int)app_value($_SESSION,'nivel_acceso',0)===2&&app_can('zona_aulas'))){ ?><a href="/gestion/mensajes/bandeja.php">Familias</a><?php } ?><span class="neo-centre"><?=app_escape($centre)?></span></nav></header><main class="neo-main"><div class="neo-heading"><div><span class="neo-eyebrow"><?=app_escape($section?:'Administración')?></span><h1><?=app_escape($title)?></h1><?php if($subtitle!==''){ ?><p><?=app_escape($subtitle)?></p><?php } ?></div></div><?php
}
function neo_page_end(){echo '</main><footer class="neo-footer">Neo Kids · Área de personal</footer></body></html>';}
function neo_date($value,$time=false){if(!$value||substr((string)$value,0,10)==='0000-00-00')return '—';$stamp=strtotime($value);return $stamp===false?'—':date($time?'d/m/Y H:i':'d/m/Y',$stamp);}
function neo_url($changes=array()){$query=array_merge($_GET,$changes);foreach($query as $k=>$v)if($v===''||$v===null)unset($query[$k]);return '?'.http_build_query($query);}
function neo_pagination($total,$page,$size=50) {
    $pages=max(1,(int)ceil($total/$size));if($pages<2)return;
    echo '<nav class="neo-pagination" aria-label="Páginas">';
    if($page>1)echo '<a class="neo-button secondary" href="'.app_escape(neo_url(array('pagina'=>$page-1))).'">← Anterior</a>';
    echo '<span>Página '.$page.' de '.$pages.' · '.$total.' resultados</span>';
    if($page<$pages)echo '<a class="neo-button secondary" href="'.app_escape(neo_url(array('pagina'=>$page+1))).'">Siguiente →</a>';
    echo '</nav>';
}
function neo_courses(){return app_rows('SELECT id_curso,nombre,fecha_inicio,fecha_fin FROM cursos_academicos ORDER BY fecha_inicio DESC');}
function neo_current_course($courses) {foreach($courses as $c)if($c['fecha_inicio']<=date('Y-m-d')&&$c['fecha_fin']>=date('Y-m-d'))return (int)$c['id_curso'];return 0;}
