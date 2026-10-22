<?php
require_once __DIR__ . '/configuracion/config.php';
require_once __DIR__ . '/gestion/panel/bootstrap.php';

require __DIR__ . '/gestion/panel/views/header.php';

if ($permitido === true && $panel_has_selected_centre) {
    require __DIR__ . '/gestion/panel/views/dashboard.php';
} elseif ($permitido === true) {
    require __DIR__ . '/gestion/panel/views/select-centre.php';
} else {
    require __DIR__ . '/gestion/panel/views/login.php';
}

require __DIR__ . '/gestion/panel/views/footer.php';
