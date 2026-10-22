<?php

if ($permitido != true) {
    header('Location: ' . $url_web . '/configuracion/acceso_denegado.htm');
    exit;
}

require_once __DIR__ . '/requiere_centro.php';
