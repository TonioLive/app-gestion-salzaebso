<?php

if ($permitido != true) {
    header('Location: ' . $url_web . '/configuracion/acceso_denegado.htm');
    exit;
}

if ($nivel_acceso < 3 || $nivel_acceso >= 10) {
    header('Location: ' . $url_web . '/configuracion/acceso_denegado.htm');
    exit;
}

require_once __DIR__ . '/requiere_centro.php';
