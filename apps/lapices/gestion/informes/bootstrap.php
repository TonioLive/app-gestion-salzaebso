<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';
require_once __DIR__ . '/report_engine.php';

reports_require_page_access();

$reports_database = isset($panel_database) && $panel_database instanceof mysqli
    ? $panel_database
    : false;

if (!($reports_database instanceof mysqli)) {
    http_response_code(503);
    $reports_database_error = 'No se puede conectar con la base de datos en este momento.';
} else {
    $reports_database_error = '';
}

$panel_active_section = 'informes';
$panel_topbar_label = 'Administración';
$panel_extra_styles = array(
    '/gestion/informes/assets/report-builder.css?v=20260728.1',
);
