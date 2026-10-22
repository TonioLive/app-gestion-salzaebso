<?php
require_once __DIR__.'/common.php';
$_POST['action']='read';
$_POST['id_mensaje']=app_value($_POST,'id_mensaje',app_value($_GET,'id_mensaje'));
require __DIR__.'/acciones.php';
