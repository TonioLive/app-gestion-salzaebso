<?php
require_once __DIR__.'/common.php';
$_POST['action']='new';
$_POST['text']=app_value($_POST,'texto_mensaje');$_POST['subject']=app_value($_POST,'asunto');
require __DIR__.'/acciones.php';
