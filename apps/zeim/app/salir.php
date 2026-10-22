<?php
require_once dirname(__DIR__).'/configuracion/family.php';
app_check_csrf();
foreach (array('permitido_padre','id_padre','nombre_padre','id_hijo','_family_verified','_family_seen') as $key) unset($_SESSION[$key]);
session_regenerate_id(true);header('Location: /app/login.php',true,303);exit;
