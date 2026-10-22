<?php
if (empty($permitido) || !app_can('zona_mensajes')) { header('Location: /index.php',true,303); exit; }
