<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';
require_once __DIR__ . '/material_service.php';

if ($permitido !== true) {
    header('Location: /index.php');
    exit;
}
if (!$panel_has_selected_centre) {
    header('Location: /index.php');
    exit;
}
if ((int) $nivel_acceso < 3 && empty($zona_almacen)) {
    http_response_code(403);
    exit('No tienes acceso a la gestión de material.');
}

$material_database = isset($panel_database) && $panel_database instanceof mysqli
    ? $panel_database
    : false;
if (!($material_database instanceof mysqli)) {
    http_response_code(503);
    exit('No se puede conectar con la base de datos.');
}

$panel_active_section = 'almacen';
$panel_topbar_label = 'Almacén';
$panel_extra_styles = array(
    '/gestion/material/assets/material.css?v=20260731.2',
);
$panel_extra_scripts = array(
    '/gestion/material/assets/material.js?v=20260730.1',
);
