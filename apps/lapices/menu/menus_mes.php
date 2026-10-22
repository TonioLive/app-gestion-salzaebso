<?php
require_once __DIR__ . '/../configuracion/encoding.php';
require __DIR__ . '/../configuracion/config.php';
require __DIR__ . '/../configuracion/autentifica.php';
header('Content-Type: text/html; charset=UTF-8');

$selected_day = (string) app_request_value($_GET, 'dia', '');
$month_date = $selected_day !== '' ? strtotime($selected_day) : time();
if ($month_date === false) {
    $month_date = time();
}
$month_start = date('Y-m-01', $month_date);
$month_end = date('Y-m-t', $month_date);
$month_label = $nombre_mes[(int) date('n', $month_date)] . ' de ' . date('Y', $month_date);
$previous_month = date('Y-m-d', strtotime($month_start . ' -1 month'));
$next_month = date('Y-m-d', strtotime($month_start . ' +1 month'));
$menus = db_query(
    "SELECT DISTINCT calendar.id_menu, menus.nombre_menu FROM menu_calendario AS calendar "
    . "LEFT JOIN menu_menus AS menus ON menus.id_menu=calendar.id_menu "
    . "WHERE calendar.fecha>='" . $month_start . "' AND calendar.fecha<='" . $month_end . "' "
    . "ORDER BY calendar.id_menu"
);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Menús de comedor · <?=app_escape($month_label)?></title>
    <?php require __DIR__ . '/../configuracion/estilos/estilo1.php'; ?>
    <link rel="stylesheet" href="/menu/assets/menu-document.css?v=20261005.1">
</head>
<body class="legacy-admin">
<div class="menu-page-shell">
    <?php require __DIR__ . '/../configuracion/inc/login_sup_moderno.php'; ?>
    <main class="menu-page" aria-labelledby="menu-page-title">
        <header class="menu-document-heading">
            <strong>Calendario de comedor</strong>
            <span>Lápices · Neokids Natural Food</span>
        </header>
        <h1 id="menu-page-title">Menús de <?=app_escape($month_label)?></h1>
        <p class="menu-page-intro">Selecciona un tipo de menú para consultar el calendario e imprimirlo.</p>
        <?php if ($menus && db_num_rows($menus) > 0) { ?>
        <ul class="menu-type-list">
            <?php while ($menu = db_fetch_assoc($menus)) {
                $menu_name = app_url_text_utf8((string) $menu['nombre_menu']);
                if (!preg_match('//u', $menu_name)) {
                    $menu_name = iconv('Windows-1252', 'UTF-8', $menu_name);
                }
                if ($menu_name === '') {
                    $menu_name = 'Menú sin nombre';
                }
                $print_url = 'imprimir_menu.php?' . http_build_query(array(
                    'id_menu' => (int) $menu['id_menu'],
                    'dia' => $month_start,
                ));
            ?>
            <li>
                <strong class="menu-type-name"><?=app_escape($menu_name)?></strong>
                <a class="menu-action" href="<?=app_escape($print_url)?>" target="_blank" rel="noopener" aria-label="<?=app_escape('Ver e imprimir: ' . $menu_name)?>">Ver e imprimir</a>
            </li>
            <?php } ?>
        </ul>
        <?php } else { ?>
            <p>No hay menús programados para este mes.</p>
        <?php } ?>
        <nav class="menu-month-nav" aria-label="Cambiar mes">
            <a class="menu-action" href="?dia=<?=app_escape($previous_month)?>">← Mes anterior</a>
            <a class="menu-action" href="/index.php">Volver al inicio</a>
            <a class="menu-action" href="?dia=<?=app_escape($next_month)?>">Mes siguiente →</a>
        </nav>
    </main>
</div>
</body>
</html>
