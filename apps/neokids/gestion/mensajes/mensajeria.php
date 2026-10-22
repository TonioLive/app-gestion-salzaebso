<?php
require_once __DIR__.'/common.php';
fm_redirect('bandeja.php?id_alumno='.(int)app_value($_GET,'id_alumno'));
