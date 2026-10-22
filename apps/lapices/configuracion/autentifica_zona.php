<?php
if ($permitido !== true || (int) $zona_mensajes !== 1) {
    header('Location: ' . $url_web . '/configuracion/acceso_denegado.htm');
    exit;
}
