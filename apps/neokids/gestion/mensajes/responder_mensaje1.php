<?php
require_once __DIR__.'/common.php';
$_POST['action']='reply';
$_POST['text']=app_value($_POST,'respuesta');
require __DIR__.'/acciones.php';
