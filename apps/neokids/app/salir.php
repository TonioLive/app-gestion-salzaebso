<?php
require_once dirname(__DIR__).'/configuracion/family.php';
app_check_csrf();
if(!empty($_SESSION['pool_push_id'])&&!empty($_SESSION['id_padre']))db_query_params('UPDATE piscina_push_suscripciones SET activa=0,actualizada=NOW() WHERE id=? AND padre=?','ii',array((int)$_SESSION['pool_push_id'],(int)$_SESSION['id_padre']),app_db());
unset($_SESSION['pool_push_id']);
foreach (array('permitido_padre','id_padre','nombre_padre','id_hijo','_family_verified','_family_seen') as $key) unset($_SESSION[$key]);
session_regenerate_id(true);header('Location: /app/login.php',true,303);exit;
