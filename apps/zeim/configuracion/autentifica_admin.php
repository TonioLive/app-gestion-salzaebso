<?php
if (empty($permitido) || !in_array((int)$nivel_acceso,array(3,4),true)) { header('Location: /index.php',true,303); exit; }
